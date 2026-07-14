<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PayuVerifyPayments extends WcPayubiz
{
    protected $gateway_module;

    protected $payu_salt;

    protected $payu_key;

    public function __construct()
    {

        $plugin_data = get_option('woocommerce_payubiz_settings');

        if (is_array($plugin_data)) {
            $this->gateway_module = $plugin_data['gateway_module'];
            $this->payu_salt = $plugin_data['currency1_payu_salt'];
            $this->payu_key = $plugin_data['currency1_payu_key'];
        } else {
            error_log('Error: $plugin_data is not an array.');
            $this->gateway_module = '';
            $this->payu_salt = '';
            $this->payu_key = '';
        }

        // add the 5-minute interval
        add_filter('cron_schedules', array($this, 'cron_add_one_min'));

        // add a function to the specified hook
        add_action('check_payment_status_after_every_five_min', array($this, 'verify_payment'), 10, 4);
        add_action('pass_arguments_to_verify', array($this, 'passArgumentstoVerify'), 10, 3);
        add_action('clear_scheduled_task', array($this, 'clearScheduledTask'), 10, 4);

        // run a cron after order creation to check the payment status
        add_action('woocommerce_checkout_order_processed', array($this, 'schedulePaymentStatusCheck'));
        // Disabled duplicate cron scheduling hook to avoid multiple cron entries for the same order.
        // add_action('woocommerce_new_order', array($this, 'schedulePaymentStatusCheck'));
    }

    public function schedulePaymentStatusCheck($order_id)
    {
        // date_default_timezone_set('Asia/Kolkata');

        $schedule_time = time() + 360;
        $expiry_time = time() + 2700;
        $order = new WC_Order($order_id);
        // Avoid storing serialized WC_Order object in cron args to prevent excessive memory usage.
        // $order_new = serialize($order);
        $order_new = $order->get_id();
        if ($order->is_paid()) {
            return;
        }
        // $args = array($order_new, $schedule_time, $expiry_time);
        // Using lightweight order ID instead of serialized order object in cron arguments.
        $args = array($order_id, $schedule_time, $expiry_time);
        // Clear existing scheduled cron for same order to prevent duplicate cron buildup.
        wp_clear_scheduled_hook('pass_arguments_to_verify', $args);
        if (!wp_next_scheduled('pass_arguments_to_verify', $args)) {
            // Schedule recurring payment verification cron until order is completed or expired.
            wp_schedule_event($schedule_time, 'every_five_min', 'pass_arguments_to_verify', $args);
        }
    }

    function passArgumentstoVerify($order, $schedule_time, $expiry_time)
    {
        global $wpdb;
        // Removed unserialize() usage as cron now stores only order ID instead of full WC_Order object.
        // $order = unserialize($order);
        // if (is_string($order)) {
        //     $order = unserialize($order);
        // }

        if (!$order instanceof WC_Order) {
            $order = wc_get_order($order);
        }

        if (!$order) {
            error_log("Invalid order object.");
            return;
        }
        $order_status  = $order->get_status(); // Get the order status

        // Check if the order status indicates cancellation or failure
        if (in_array($order_status, ['refund-progress', 'cancelled', 'failed'])) {
            $this->handleCancellation($order, $schedule_time, $expiry_time);
            return;
        }

        // $now = time();
        // Using WordPress current_time() for timezone-safe cron comparison.
        $now = current_time('timestamp');

        // Check if the expiry time has passed
        // if ($expiry_time <= $now) {
        //     $this->handleRefundExpiredOrder($order);
        //     $this->handleCancellation($order, $schedule_time, $expiry_time);
        //     return;
        // }
        
        // Prevent refund execution if payment is already completed but order status update is delayed.
        if ($expiry_time <= $now && $order->get_status() === 'pending' && !$order->is_paid()) {
            $this->handleRefundExpiredOrder($order);
            $this->handleCancellation($order, $schedule_time, $expiry_time);
            return;
        }


        $method = $order->get_payment_method();

        // Check if the payment method is 'payubiz'
        if ($method == 'payubiz') {
            $this->processPayubizPayment($order, $schedule_time, $expiry_time);
        }
    }

    private function handleCancellation($order, $schedule_time, $expiry_time)
    {
        // Handle cancellation logic here
        $this->clearScheduledTask($order->get_id(), $schedule_time, $expiry_time);
    }

    private function handleRefundExpiredOrder($order)
    {
        $method = $order->get_payment_method();
        // if ($method == 'payubiz') { #changed
        if ($method == 'payubiz' && $order->get_status() !== 'completed' && $order->get_status() !== 'processing') {
            // Handle expired order logic here
            $order_id = $order->get_id();
            $refund_amount = $order->get_total();
            $refund_reason = 'Order not confirmed';
            $refund_id = wc_create_refund(array(
                'amount'   => $refund_amount,
                'reason'   => $refund_reason,
                'order_id' => $order_id,
                'refund_payment' => true
            ));
            if (is_wp_error($refund_id)) {
                error_log('Refund failed. Please try again' . ' ' . $refund_id->get_error_message());
                $order->update_status('cancelled', 'Pending order change to cancelled testmessage');
                error_log("order marked cancelled by scheduler  $order_id");
            } else {
                $order->update_status('wc-refund-progress', 'Refund in Progress', true);
                error_log("order marked refund process by scheduler  $order_id");
            }
        }
    }

    private function processPayubizPayment($order, $schedule_time, $expiry_time)
    {
        // Handle payubiz payment logic here
        $plugin_data = get_option('woocommerce_payubiz_settings');
        $txnid = get_post_meta($order->get_id(), 'order_txnid', true);
        if ($txnid) {
            $this->insert_cron_data($order->get_id(), $txnid, 'pending');
            $payuPaymentValidation = new PayuPaymentValidation();
            $verify_status = $payuPaymentValidation->verifyPayment(
                $order,
                $txnid,
                $plugin_data['currency1_payu_key'],
                $plugin_data['currency1_payu_salt']
            );
            $shipping_data = $this->payu_order_detail_api($txnid);
            if ($shipping_data) {
                $order->set_address($shipping_data, 'shipping');
                $order->set_address($shipping_data, 'billing');
            }
            if ($verify_status) {
                $order->payment_complete();
                error_log("order marked completed by scheduler  $order->get_id()");
                if ($order->is_paid()) {
                    error_log("Already paid. Cancelling future cron for order {$order->get_id()}");
                    $this->clearScheduledTask($order->get_id(), $schedule_time, $expiry_time);
                    return;
                }
            }
        }
    }


    function insert_cron_data($order_id, $txnid, $status)
    {
        global $wpdb;
        // date_default_timezone_set('Asia/Kolkata');
        $table = $wpdb->prefix . 'payu_cron_logs';
        $currentDateTime = new DateTime();
        $currentDateTime = $currentDateTime->format('Y-m-d H:i:s');

        $wpdb->insert($table, array(
            'transaction_id' => $txnid,
            'order_id' => $order_id,
            'order_status' => $status,
            'created_at' => $currentDateTime,
        ));
    }
    public function clearScheduledTask($order, $schedule_time, $expiry_time)
    {
        $args = array($order, $schedule_time, $expiry_time);
        // Remove scheduled cron events once order is completed, cancelled, or refunded.
        wp_clear_scheduled_hook('pass_arguments_to_verify', $args);
    }

    function cron_add_one_min($schedules)
    {
        $schedules['every_five_min'] = array(
            'interval' => 60 * 5,
            'display' => 'Five Minutes'
        );
        return $schedules;
    }

    protected function payu_order_detail_api($txnid)
    {

        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $hashString = "|" . $date . "|" . $this->payu_salt;
        error_log("payu hashString1".  $hashString);
        $hash = $this->getSha512Hash($hashString);
        error_log("payu hash".  $hash);
        $url = PAYU_ORDER_DETAIL_API . $txnid;
        if ($this->gateway_module == 'sandbox') {
            $url = PAYU_ORDER_DETAIL_API_UAT . $txnid;
        }
        $authorization = 'hmac username="' . $this->payu_key .
            '", algorithm="sha512", headers="date", signature="' . $hash . '"';

        $request_args = array(
            'method'      => 'GET', // Method must be POST for wp_remote_post
            'headers'     => array(
                'Date'          => $date,
                'Authorization' => $authorization
            ),
        );
        $response = wp_remote_post($url, $request_args);
        if (is_wp_error($response)) {
            return false;
        } else {
            // If you're expecting JSON response, you can decode it
            $decoded_response = json_decode(wp_remote_retrieve_body($response), true);
            if (
                isset($decoded_response['data']['address'][0]) &&
                isset($decoded_response['data']['address'][0]['shippingAddress'])
            ) {
                $shipping_data = $decoded_response['data']['address'][0]['shippingAddress'];
                $full_name = explode(' ', $shipping_data['name']);
                return array(
                    'country' => 'IN',
                    'state' => $shipping_data['state'],
                    'email' => $shipping_data['email'],
                    'city' => $shipping_data['city'],
                    'postcode' => $shipping_data['pincode'],
                    'phone' => $shipping_data['addressPhoneNumber'],
                    'address_1' => $shipping_data['addressLine'],
                    'first_name' => isset($full_name[0]) ? $full_name[0] : '',
                    'last_name' => isset($full_name[1]) ? $full_name[1] : ''
                );
            }
        }
        return false;
    }


    private function getSha512Hash($hashString)
    {
        $messageDigest = hash('sha512', $hashString, true);
        $hashtext = bin2hex($messageDigest);
        $hashtext = str_pad($hashtext, 128, '0', STR_PAD_LEFT); // Pad to 128 characters
        return $hashtext;
    }
}
$payu_verify_payments = new PayuVerifyPayments();
