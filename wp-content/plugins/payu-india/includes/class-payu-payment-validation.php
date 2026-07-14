<?php
class PayuPaymentValidation
{

	public $msg;
	public $currency1PayuSalt;
	public $bypassVerifyPayment;
	public $currency1PayuKey;
	public $gatewayModule;
	public $redirect_page_id;

	public function __construct()
	{

		$plugin_data = get_option('woocommerce_payubiz_settings');
		$this->currency1PayuSalt = sanitize_text_field($plugin_data['currency1_payu_salt']);
		$this->currency1PayuKey = sanitize_text_field($plugin_data['currency1_payu_key']);
		$this->redirect_page_id = sanitize_text_field($plugin_data['redirect_page_id']);
		$this->gatewayModule = sanitize_text_field($plugin_data['gateway_module']);
		if (sanitize_text_field($plugin_data['verify_payment']) != "yes") {
			$this->bypassVerifyPayment = true;
		}
	}

	public function payuPaymentValidationAndRedirect($postdata)
	{
		$order = $this->paymentValidationAndUpdation($postdata);
		$this->manageMessages();
		$redirect_url = $this->getRedirectUrl($order);
		$this->redirectTo($redirect_url);
	}


	public function paymentValidationAndUpdation($postdata, $bypass_verify_payment = false)
	{
		// Validate required fields
		if (
			empty($postdata['key']) ||
			empty($postdata['txnid']) ||
			empty($postdata['status']) ||
			empty($postdata['hash']) ||
			empty($postdata['amount']) ||
			empty($postdata['productinfo']) ||
			empty($postdata['firstname']) ||
			empty($postdata['email'])
		) {
			return false;
		}

		$this->bypassVerifyPayment = $bypass_verify_payment;

		global $woocommerce, $wpdb;

		$payu_key  = $this->currency1PayuKey;
		$payu_salt = $this->currency1PayuSalt;

		$txnid = sanitize_text_field($postdata['txnid']);
		$order_id = explode('_', $txnid);
		$order_id = (int) $order_id[0]; // Get rid of timestamp part

		payu_transaction_data_insert($postdata, $order_id);

		$order = wc_get_order($order_id);

		if (!$order) {
			return false;
		}

		return $this->payuValidatePostData(
			$postdata,
			$order,
			$payu_key,
			$payu_salt
		);
	}

	private function manageMessages()
	{
		// Only use WooCommerce notices when session is available.
		if (function_exists('wc_add_notice') && function_exists('WC') && WC()->session) {

			wc_clear_notices();

			if ($this->msg['class'] !== 'success') {
				wc_add_notice($this->msg['message'], $this->msg['class']);
			}
		} else {

			global $woocommerce;

			if ($this->msg['class'] !== 'success') {

				if (is_object($woocommerce) && method_exists($woocommerce, 'add_error')) {
					$woocommerce->add_error($this->msg['message']);

					if (method_exists($woocommerce, 'set_messages')) {
						$woocommerce->set_messages();
					}
				}
			}
		}
	}

	private function getRedirectUrl($order)
	{
		$redirect_url = ($this->redirect_page_id == '' || $this->redirect_page_id == 0) ?
			get_site_url() . '/' :
			get_permalink($this->redirect_page_id);
		if ($order && $this->msg['class'] == 'success') {
			$redirect_url = $order->get_checkout_order_received_url();
		}
		return $redirect_url;
	}

	private function redirectTo($redirect_url)
	{
		wp_safe_redirect($redirect_url);
		exit;
	}

	private function payuValidatePostData($postdata, $order, $payu_key, $payu_salt)
	{

		$udf4 = $order->get_meta('udf4');
		$txnid = sanitize_text_field($postdata['txnid']);

		if ($postdata['key'] === $payu_key) {
			$amount      		= 	number_format($postdata['amount'], 2);
			$productInfo  		= 	sanitize_text_field($postdata['productinfo']);
			$firstname    		= 	sanitize_text_field($postdata['firstname']);
			$email        		=	sanitize_email($postdata['email']);
			$phone        		=	sanitize_text_field($postdata['phone']);
			$udf5				=   sanitize_text_field($postdata['udf5']);


			$keyString = $payu_key . '|' . $txnid . '|' . $amount . '|' . $productInfo . '|' . $firstname . '|' . $email
				. '||||' . $udf4 . '|' . $udf5 . '|||||';
			$keyArray 	  		= 	explode("|", $keyString);
			$reverseKeyArray 	= 	array_reverse($keyArray);
			$reverseKeyString	=	implode("|", $reverseKeyArray);
			$order = $this->processPaymentStatus(
				$postdata,
				$order,
				$reverseKeyString,
				$payu_key,
				$payu_salt
			);

			if ($order && $order->is_paid()) {
				$this->payuUpdateShippingAddress($postdata, $order);
			}
		}
		return $order;
	}

	private function payuUpdateShippingAddress($postdata, $order)
	{
		if (isset($postdata['shipping_address']) && !empty($postdata['shipping_address'])) {
			$full_name = explode(
				' ',
				sanitize_text_field($postdata['shipping_address']['name'])
			);

			$new_address = array(
				'country'   => 'IN',
				'state'     => get_state_code_by_name(
					sanitize_text_field($postdata['shipping_address']['state'])
				),
				'city'      => sanitize_text_field($postdata['shipping_address']['city']),
				'email'     => sanitize_email($postdata['shipping_address']['email']),
				'postcode'  => sanitize_text_field($postdata['shipping_address']['pincode']),
				'phone'     => sanitize_text_field($postdata['shipping_address']['addressPhoneNumber']),
				'address_1' => sanitize_text_field($postdata['shipping_address']['addressLine']),
				'first_name' => isset($full_name[0]) ? sanitize_text_field($full_name[0]) : '',
				'last_name'  => isset($full_name[1]) ? sanitize_text_field($full_name[1]) : '',
			);
			$order->update_meta_data(
				'shipping_email',
				sanitize_email($postdata['shipping_address']['email'])
			);
			$order->set_shipping_first_name(isset($full_name[0]) ? $full_name[0] : '');
			$order->set_address($new_address, 'shipping');
			$order->set_address($new_address, 'billing');
			foreach ($order->get_items() as $item_id => $item) {
				// Calculate taxes for the item
				if ($item->get_type() === 'fee') {
					continue;
				}
				$item->calculate_taxes($new_address);
			}

			// Save the order to apply tax calculations
			$order->save();
		}
	}

	private function processPaymentStatus($postdata, $order, $reverseKeyString, $payu_key, $payu_salt)
	{

		// $status = $postdata['status'];
		// switch ($status) {
		// 	case 'success':
		// 		return $this->processSuccessPayment($postdata, $order, $reverseKeyString, $payu_key, $payu_salt);
		// 	case 'failure':
		// 		return $this->processFailurePayment($postdata, $order);
		// 	default:
		// 		return $this->processDefaultPayment($order);
		// }
		if (!$this->verifyResponseHash($postdata, $payu_salt)) {

			$this->msg['class'] = 'error';
			$this->msg['message'] = esc_html__('Invalid PayU response hash.', 'payu-india');

			return $order;
		}

		switch ($postdata['status']) {

			case 'success':
				return $this->processSuccessPayment(
					$postdata,
					$order,
					$reverseKeyString,
					$payu_key,
					$payu_salt
				);

			case 'failure':
				return $this->processFailurePayment($postdata, $order);

			default:
				return $this->processDefaultPayment($order);
		}
	}

	private function verifyResponseHash($postdata, $payu_salt)
	{

		$additional_charges = isset($postdata['additionalCharges'])
			? $postdata['additionalCharges']
			: 0;

		$amount = number_format((float) $postdata['amount'], 2, '.', '');

		$response_raw_hash =
			'|||||' .
			$postdata['udf5'] . '|' .
			$postdata['udf4'] . '|' .
			$postdata['udf3'] . '|' .
			$postdata['udf2'] . '|' .
			$postdata['udf1'] . '|' .
			$postdata['email'] . '|' .
			$postdata['firstname'] . '|' .
			$postdata['productinfo'] . '|' .
			$amount . '|' .
			$postdata['txnid'] . '|' .
			$postdata['key'];

		$salt_string =
			$payu_salt . '|' .
			$postdata['status'] . '|' .
			$response_raw_hash;

		if ($additional_charges > 0) {
			$salt_string =
				$additional_charges . '|' . $salt_string;
		}

		return hash_equals(
			strtolower(hash('sha512', $salt_string)),
			strtolower($postdata['hash'])
		);
	}
	private function processSuccessPayment($postdata, $order, $reverseKeyString, $payu_key, $payu_salt)
	{
		global $woocommerce;
		$txnid = sanitize_text_field($postdata['txnid']);
		$order_id = $order->get_id();
		$amount = (float) $postdata['amount'];
		$additionalCharges = 0;

		if (isset($postdata["additionalCharges"])) {
			$additionalCharges = (float) $postdata['additionalCharges'];
		}
		$postdata['amount'] = number_format($postdata['amount'], 2);
		$postdata['amount'] = str_replace(",", "", $postdata['amount']);
		// New code added Start for Hashkey
		// $responseRawHashString = '|||||' . $postdata['udf5'] . '|' . $postdata['udf4'] . '|' . $postdata['udf3'] . '|' . $postdata['udf2'] . '|' . $postdata['udf3'] . '|' . $postdata['email'] . '|' . $postdata['firstname'] . '|' . $postdata['productinfo'] . '|' . $postdata['amount'] . '|' . $postdata['txnid'] . '|' . $postdata['key'];

		// $saltString = $payu_salt . '|' . $postdata['status'] . '|' . $responseRawHashString;
		// New code added End for Hashkey

		// if (0 < $additionalCharges) {
		// 	$saltString = $additionalCharges . '|' . $payu_salt . '|' . $postdata['status'] . '|' . $responseRawHashString;
		// }

		// $sentHashString = strtolower(hash('sha512', $saltString));


		// $responseHashString = $postdata['hash'];

		$this->msg['class'] = 'error';
		$thankyou_msg = 'Thank you for shopping with us. However, the transaction has been declined.';
		$this->msg['message'] = esc_html($thankyou_msg);
		if ( $this->verifyPayment($order, $txnid, $payu_key, $payu_salt, $this->bypassVerifyPayment)
		) {
			$thankyou_msg = 'Thank you for shopping with us. Your account has been charged and your transaction is successful'
				. ' with the following order details:';
			$this->msg['message'] = esc_html($thankyou_msg);
			$this->msg['message'] .= '<br>' . esc_html__('Order Id:', 'payu-india') . ' ' . esc_html($order_id) . '<br/>';
			$this->msg['message'] .= esc_html__('Amount:', 'payu-india') . ' ' . esc_html($amount) . '<br />';
			$this->msg['message'] .= esc_html__('We will be shipping your order to you soon.', 'payu-india');


			if (0 < $additionalCharges) {
				$thankyou_msg = 'Additional amount charged by PayUBiz - ' . $additionalCharges;
				$this->msg['message'] .= '<br /><br />' . esc_html($thankyou_msg);
			}


			$this->msg['class'] = 'success';

			if ('processing' === $order->get_status() || 'completed' === $order->get_status()) {
				//do nothing
			} else {
				//complete the order
				$logger = wc_get_logger();
				$logger->info(
					"Order marked payment completed. Order ID: {$order_id}",
					array('source' => 'payu-india')
				);
				if (
					isset($postdata['extra_charges']['carrier_code']) &&
					!empty($postdata['extra_charges']['carrier_code'])
				) {
					$this->update_shipping_method(
						$order,
						sanitize_text_field($postdata['extra_charges']['carrier_code'])
					);
				}
				$order->update_meta_data(
					'payu_bankcode',
					sanitize_text_field($postdata['bankcode'])
				);

				$order->update_meta_data(
					'payu_mode',
					sanitize_text_field($postdata['mode'])
				);
				$order->save();
				$order->payment_complete();
				$user = get_user_by(
					'email',
					sanitize_email($postdata['email'])
				);

				if (! $user) {
					create_user_and_login_if_not_exist(
						sanitize_email($postdata['email'])
					);
					$user = get_user_by(
						'email',
						sanitize_email($postdata['email'])
					);
				}

				if ($user) {
					$user_id = $user->ID;

					$order->set_customer_id($user_id);

					update_user_meta(
						$user_id,
						'payu_phone',
						sanitize_text_field($postdata['phone'])
					);

					$order->save();
				}
				$order->add_order_note(
					sprintf(
						/* translators: %s: PayU payment reference number (mihpayid). */
						esc_html__('PayUBiz has processed the payment. Ref Number: %s', 'payu-india'),
						sanitize_text_field($postdata['mihpayid'])
					)
				);
				$order->add_order_note($this->msg['message']);
				$order->add_order_note('Paid by PayUBiz');
				$woocommerce->cart->empty_cart();
			}
		} else {
			//tampered
			$this->msg['class'] = 'error';
			$thankyou_msg = 'Thank you for shopping with us. However, the payment failed test1';
			$this->msg['message'] = esc_html($thankyou_msg);
			if ($order->is_paid()) {
				return $order;
			}
			$order->update_status('failed');
			$order->add_order_note('Failed');
			$order->add_order_note($this->msg['message']);
			$logger = wc_get_logger();
			$logger->error(
				"Order marked failed. Order ID: {$order_id}",
				array('source' => 'payu-india')
			);
		}
		return $order;
	}

	private function processFailurePayment($postdata, $order)
	{
		if ($order->is_paid()) {
			return $order;
		}
		$this->msg['class'] = 'error';
		$failure_reason = isset($postdata['field9'])
			? sanitize_text_field($postdata['field9'])
			: '';

		if (! empty($failure_reason)) {
			$thankyou_msg = sprintf(
				'Thank you for shopping with us. However, the payment failed (%s).',
				$failure_reason
			);
		} else {
			$thankyou_msg = 'Thank you for shopping with us. However, the payment failed.';
		}
		$this->msg['message'] = esc_html($thankyou_msg);
		$order->update_status('failed');
		$order->add_order_note('Failed');
		$order->add_order_note($this->msg['message']);

		return $order;
	}

	private function processDefaultPayment($order)
	{
		if ($order->is_paid()) {
			return $order;
		}
		$this->msg['class'] = 'error';
		$thankyou_msg = 'Thank you for shopping with us. However, the payment failed';
		$this->msg['message'] = esc_html($thankyou_msg);
		$order->update_status('failed');
		$order->add_order_note('Failed');
		$order->add_order_note($this->msg['message']);
		return $order;
	}

	private function reconcileOfferData($transaction_offer, $order)
	{

		if (!is_array($transaction_offer)) {

			$transaction_offer = array($transaction_offer);
			//$transaction_offer = json_decode(str_replace('\"', '"', $transaction_offer), true);
		}
		if (isset($transaction_offer['offer_data'])) {

			foreach ($transaction_offer['offer_data'] as $offer_data) {

				if ('SUCCESS' === $offer_data['status']) {
					$offer_title = $offer_data['offer_title'];
					$discount = $offer_data['discount'];
					if ('CASHBACK' !== $offer_data['offer_type']) {
						$this->wcUpdateOrderAddDiscount($order, $offer_title, $discount);
					}
					$offer_key = $offer_data['offer_key'];
					$offer_type = $offer_data['offer_type'];
					$order->update_meta_data('payu_offer_key', $offer_key);
					$order->update_meta_data('payu_offer_type', $offer_type);
				}
			}
		}
	}

	private function wcUpdateOrderAddDiscount($order, $title, $amount)
	{
		$subtotal = $order->get_subtotal();
		$optional_fee_exists = false;
		foreach ($order->get_fees() as $item_fee) {
			$fee_name = $item_fee->get_name();
			if ($fee_name == $title) {
				$optional_fee_exists = true;
			}
		}
		if (!$optional_fee_exists) {
			$item     = new WC_Order_Item_Fee();

			if (strpos($amount, '%') !== false) {
				$percentage = (float) str_replace(array('%', ' '), array('', ''), $amount);
				$percentage = $percentage > 100 ? -100 : -$percentage;
				$discount   = $percentage * $subtotal / 100;
			} else {
				$discount = (float) str_replace(' ', '', $amount);
				$discount = $discount > $subtotal ? -$subtotal : -$discount;
			}

			$item->set_name($title);
			$item->set_total_tax(0);
			$item->set_tax_class(false);
			$item->set_tax_status('none');
			$item->set_taxes(false);
			$item->set_amount($discount);
			$item->set_total($discount);


			$item->save();
			$item_id = $item->get_id();
			$order->update_meta_data('payu_discount_item_id', $item_id);
			$order->add_item($item);
			$order->calculate_totals(false);
			$order->save();
		}
	}

	// Adding Meta container admin shop_order pages
	public function verifyPayment($order, $txnid, $payu_key, $payu_salt, $bypass = false)
	{
		$verify_flag = false;
		if ($bypass) {
			$verify_flag = true;
		}

		try {
			$url = ('sandbox' === $this->gatewayModule) ?
				PAYU_POSTSERVICE_FORM_2_URL_UAT :
				PAYU_POSTSERVICE_FORM_2_URL_PRODUCTION;

			$response = $this->sendVerificationRequest($url, $payu_key, $txnid, $payu_salt);

			if (!$response || !isset($response['body'])) {
				return false;
			}

			$res = json_decode(sanitize_text_field($response['body']), true);
			if (!isset($res['status'])) {
				return false;
			}

			$transaction_details = $res['transaction_details'][$txnid] ?? null;
			if (!$transaction_details) {
				return false;
			}

			$transaction_offer = isset($transaction_details['transactionOffer']) ?
				json_decode($transaction_details['transactionOffer'], true) : null;
			$verify_flag = ('success' === strtolower($transaction_details['status']));
			if ($verify_flag && $transaction_offer) {
				$this->reconcileOfferData($transaction_offer, $order);
			}
		} catch (Exception $e) {
			$verify_flag = false;
		}
		return $verify_flag;
	}

	private function sendVerificationRequest($url, $payu_key, $txnid, $payu_salt)
	{
		$fields = [
			'key' => sanitize_key($payu_key),
			'command' => 'verify_payment',
			'var1' => $txnid,
			'hash' => ''
		];
		$hash = hash("sha512", $fields['key'] . '|' . $fields['command'] . '|' . $fields['var1'] . '|' . $payu_salt);
		$fields['hash'] = sanitize_text_field($hash);
		$args = [
			'body' => $fields,
			'timeout' => 5,
			'redirection' => 5,
			'httpversion' => '1.1',
			'blocking' => true,
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded',
				'accept' => 'application/json'
			]
		];
		$response = wp_remote_post($url, $args);
		if (is_wp_error($response)) {
			$args_log['response_data'] = $response->get_error_message();
			payu_insert_event_logs($args_log);
			return false;
		}
		$response_code = wp_remote_retrieve_response_code($response);
		$headerResult = wp_remote_retrieve_headers($response);
		$log_fields = $fields;

		unset($log_fields['hash']);
		$args_log = array(
			'request_type' => 'outgoing',
			'method' => 'post',
			'url' => $url,
			'request_headers' => $args['headers'],
			'request_data' => $log_fields,
			'status' => $response_code,
			'response_headers' => $headerResult,
			'response_data' => 'null'
		);
		if (empty($response['body'])) {

			payu_insert_event_logs($args_log);
			return false;
		} else {
			$res = json_decode(wp_remote_retrieve_body($response), true);
			if (JSON_ERROR_NONE !== json_last_error()) {
				return false;
			}
			$args_log['response_data'] = $res;
			payu_insert_event_logs($args_log);
		}
		return $response;
	}


	private function update_shipping_method($order, $new_method_id)
	{
		$calculate_tax_for = array(
			'country'  => $order->get_shipping_country(),
			'state'    => $order->get_shipping_state(), // (optional value)
			'postcode' => $order->get_shipping_postcode(), // (optional value)
			'city'     => $order->get_shipping_city(), // (optional value)
		);
		foreach ($order->get_items('shipping') as $item_id => $item) {
			$order->remove_item($item_id);
		}
		$added_shipping = array();
		foreach ($order->get_items('shipping') as $item) {
			$method_id = $item->get_method_id();
			$instance_id = $item->get_instance_id();
			$added_shipping[] = (string)$method_id . ':' . $instance_id;
		}
		if (!empty($added_shipping) && in_array($new_method_id, $added_shipping, true)) {
			return;
		}



		$item = new WC_Order_Item_Shipping();
		// Retrieve the customer shipping zone
		$zone_ids = array_keys(array('') + WC_Shipping_Zones::get_zones());

		// Loop through shipping Zones IDs
		foreach ($zone_ids as $zone_id) {
			// Get the shipping Zone object
			$shipping_zone = new WC_Shipping_Zone($zone_id);

			// Get all shipping method values for the shipping zone
			$shipping_methods = $shipping_zone->get_shipping_methods(true, 'values');
			// Loop through available shipping methods
			foreach ($shipping_methods as $shipping_method) {
				if ($shipping_method->is_enabled() && $shipping_method->get_rate_id() === $new_method_id) {

					// Set an existing shipping method for customer zone
					$item->set_method_title($shipping_method->get_title());
					$item->set_method_id($shipping_method->get_rate_id()); // set an existing Shipping method rate ID
					$item->set_total($shipping_method->cost);

					$item->calculate_taxes($calculate_tax_for);
					$item->save();
					break; // stop the loop
				}
			}
		}
		$order->add_item($item);
		// Calculate totals and save
		$order->calculate_totals(); // the save() method is included
	}
}
