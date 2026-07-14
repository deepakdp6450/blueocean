<?php
if (!defined('ABSPATH')) {
      exit;
}
if(!function_exists('payuAdminFields')){
      function payuAdminFields(){
            $site_url = get_site_url();
            $payu_payment_success_webhook_url = $site_url . '/wp-json/payu/v1/get-payment-success-update';
            $payu_payment_failed_webhook_url = $site_url . '/wp-json/payu/v1/get-payment-failed-update';
            $payu_payment_refund_webhook_url = $site_url . '/wp-json/payu/v1/refund-status-update';
            $form_fields = array(
                  'enabled' => array(
                        'title' => __('Enable/Disable', 'payu-india'),
                        'type' => 'checkbox',
                        'label' => __('Enable PayU Plugin', 'payu-india'),
                        'default' => 'no'
                  ),
                  'checkout_express' => array(
                        'title' => __('Select Checkout Experience', 'payu-india'),
                        'type' => 'select',
                        'options' => array(
                              'redirect' => esc_html__('PayU Redirect', 'payu-india'),
                              'bolt' => esc_html__('Bolt', 'payu-india'),
                              'checkout_express' => esc_html__('CommercePro', 'payu-india') 
                        ),
                        'default' => 'redirect'
                  ),
                  'dynamic_charges_flag' => array(
                        'title' => __('Fetch Shipping Charges from Store', 'payu-india'),
                        'type' => 'checkbox',
                        'description' => __('Make sure to add shipping charges.','payu-india'),
                        'label' => __('Fetch Shipping Charges from Store', 'payu-india'),
                        'default' => 'false'
                  ),
                  
                  'description' => array(
                        'title' => __('Description:', 'payu-india'),
                        'type' => 'textarea',
                        'description' => __(
                              'This controls the description which the user sees during checkout.',
                              'payu-india'),
                        'default' => __(
                              'Pay securely by UPI, Cards, Net Banking or Wallets through PayU.', 
                              'payu-india')
                  ),
                  'gateway_module' => array(
                        'title' => __('Gateway Mode', 'payu-india'),
                        'type' => 'select',
                        'options' => array("0" => "Select", "sandbox" => "Sandbox", "production" => "Production"),
                        'description' => __('Mode of gateway subscription.', 'payu-india')
                  ),
                  'disable_checkout' => array(
                        'title' => __('Disable Checkout Page', 'payu-india'),
                        'type' => 'checkbox',
                        'label' => __('Disable Checkout Page', 'payu-india'),
                        'default' => 'no'
                  ),
                  'enable_refund' => array(
                        'title' => __('Allow users to cancel orders & initiate refunds from email and payment confirmation page', 'payu-india'),
                        'type' => 'checkbox',
                        'label' => __('Allow users to cancel orders & initiate refunds from email and payment confirmation page', 'payu-india'),
                        'default' => 'no'
                  ),
                  'enable_webhook' => array(
                        'title' => __('Webhoook URLs', 'payu-india'),
                        'type' => 'hidden',
                        'description' => sprintf(
                        /* translators: 1: Refund webhook URL, 2: Success webhook URL, 3: Failed webhook URL. */
                        __(
                              'Please add the following URLs to the PayU dashboard webhook settings:<br><span style="font-weight:700;">Refund URL:</span> %1$s<br><span style="font-weight:700;">Success URL:</span> %2$s<br><span style="font-weight:700;">Failed URL:</span> %3$s',
                              'payu-india'
                        ),
                        esc_url($payu_payment_refund_webhook_url),
                        esc_url($payu_payment_success_webhook_url),
                        esc_url($payu_payment_failed_webhook_url)
                        ),
                  ),
                  'payu_account' => array(
                        'title' => __('PayU Account', 'payu-india'),
                        'type' => 'hidden',
                        'description' => __('A PayU account is required to configure the the key & salt. <a target="_blank" href="https://onboarding.payu.in/app/account/signup?partner_name=WooCommerce&partner_source=Affiliate+Links&partner_uuid=11eb-3a29-70592552-8c2b-0a696b110fde&source=Partner">Sign up</a> for a PayU merchant account or <a target="_blank" href="https://onboarding.payu.in/app/account/login?partner_name=WooCommerce&partner_source=Affiliate+Links&partner_uuid=11eb-3a29-70592552-8c2b-0a696b110fde&source=Partner">login</a> to your existing account.','payu-india'),
                  ),
                  'currency1_payu_key' => array(
                        'title' => __('PayU Key', 'payu-india'),
                        'type' => 'text',
                        'description' =>  __('The key can be found in the "Payment Gateway" tab within the "Key Salt Details" section  in the PayU dashboard.', 'payu-india')
                  ),
                  'currency1_payu_salt' => array(
                        'title' => __('PayU Salt', 'payu-india'),
                        'type' => 'text',
                        'description' =>  __('The key can be found in the "Payment Gateway" tab within the "Key Salt Details" section  in the PayU dashboard.', 'payu-india')
                  ),
                  'verify_payment' => array(
                        'title' => __('Verify Payment', 'payu-india'),
                        'type' => 'select',
                        'options' => array("0" => esc_html__('Select', 'payu-india'), "yes" => esc_html__('Yes', 'payu-india'), "no" => esc_html__('No', 'payu-india')),
                        'description' => __('Verify Payment at server.', 'payu-india')
                  ),
                  'redirect_page_id' => array(
                        'title' => __('Return Page','payu-india'),
                        'type' => 'select',
                        'options' => payu_get_pages('Select Page'),
                        'description' => "Post payment redirect URL for which payment is not successful."
                  ),
                  //Added Settings by SM For Buy Now
                  'enable_buy_now' => array(
                        'title' => __('Enable/Disable Buy Now [ This will work only for CommercePro Checkout. ]', 'payu-india'),
                        'type' => 'checkbox',
                        'label' => __('Enable Buy Now', 'payu-india'),
                        'default' => 'no',
                        'class' => 'payu-checkoutexpress-buy-now-settings'
                  ),
                  // 'enable_buy_now_on_product_page' => array(
                  //       'title' => __('Enable/Disable Buy Now On Product Page', 'payu-india'),
                  //       'type' => 'checkbox',
                  //       'label' => __('Enable Buy Now On Product Page', 'payu-india'),
                  //       'default' => 'no',
                  //       'class' => 'payu-buy-now-settings'
                  // ),
                  // 'enable_buy_now_on_shop_page' => array(
                  //       'title' => __('Enable/Disable Buy Now On Shop Page', 'payu-india'),
                  //       'type' => 'checkbox',
                  //       'label' => __('Enable Buy Now On Shop Page', 'payu-india'),
                  //       'default' => 'no'
                  // ),
                  'button_background_color' => array(
                        'title' => __('Button Background Color', 'payu-india'),
                        'type' => 'text',
                        'description' => __('Set the background color for Buy Now with PayU buttons.', 'payu-india'),
                        'default' => '#007BFF',
                        'class' => 'payu-buy-now-settings'
                  ),
                  'button_text_color' => array(
                        'title' => __('Button Text Color', 'payu-india'),
                        'type' => 'text',
                        'description' => __('Set the text color for Buy Now with PayU PayU buttons.', 'payu-india'),
                        'default' => '#FFFFFF',
                        'class' => 'payu-buy-now-settings'
                  ),
                  'button_border_radius' => array(
                        'title' => __('Button Border Radius', 'payu-india'),
                        'type' => 'number',
                        'description' => __('Set the border radius for Buy Now with PayU button (e.g., 5 for rounded corners).', 'payu-india'),
                        'default' => '6',
                        'class' => 'payu-buy-now-settings'
                  ),
                  'button_hover_color' => array(
                        'title' => __('Button Hover Color', 'payu-india'),
                        'type' => 'text',
                        'description' => __('Set the background color for Buy Now with PayU button on hover.', 'payu-india'),
                        'default' => '#0056b3',
                        'class' => 'payu-buy-now-settings'
                  ),
                  //Added Settings by SM For Affordability Widget
                  'enable_affordability_widget' => array(
                        'title' => __('Enable/Disable Affordability', 'payu-india'),
                        'type' => 'checkbox',
                        'label' => __('Enable Affordability Widget', 'payu-india'),
                        'default' => 'no'
                  ),
                  'enable_affordability_widget_on_product_page' => array(
                        'title' => __('Enable/Disable Affordability Widget On Product Page', 'payu-india'),
                        'type' => 'checkbox',
                        'label' => __('Enable Affordability Widget On Product Page', 'payu-india'),
                        'default' => 'no',
                        'class' => 'payu-affordability-settings'
                  ),
                  'enable_affordability_widget_on_cart_page' => array(
                        'title' => __('Enable/Disable Affordability Widget On Cart Page', 'payu-india'),
                        'type' => 'checkbox',
                        'label' => __('Enable Affordability Widget On Cart Page', 'payu-india'),
                        'default' => 'no',
                        'class' => 'payu-affordability-settings'
                  ),
                  'enable_affordability_widget_on_checkout_page' => array(
                        'title' => __('Enable/Disable Affordability Widget On Checkout Page [ This option is for Bolt & PayU Redirect Checkout. ]', 'payu-india'),
                        'type' => 'checkbox',
                        'label' => __('Enable Affordability Widget On Checkout Page', 'payu-india'),
                        'default' => 'no',
                        'class' => 'payu-affordability-settings'
                  ),
                  'lightColor' => array(
                        'title' => __('LightColor', 'payu-india'),
                        'type' => 'text',
                        'description' =>  __('You can set the lightColor color of Affordability Widget.', 'payu-india'),
                        'default' => '#FFFCF3',
                        'class' => 'payu-affordability-settings'
                  ),
                  'darkColor' => array(
                        'title' => __('DarkColor', 'payu-india'),
                        'type' => 'text',
                        'description' =>  __('You can set the darkColor color of Affordability Widget.', 'payu-india'),
                        'default' => '#FFC915',
                        'class' => 'payu-affordability-settings'
                  ),
                  'backgroundColor' => array(
                        'title' => __('BackgroundColor', 'payu-india'),
                        'type' => 'text',
                        'description' =>  __('You can set the backgroundColor color of Affordability Widget.', 'payu-india'),
                        'default' => '#FFFFFF',
                        'class' => 'payu-affordability-settings'
                  ),
            );
            return apply_filters(
                  'wc_payu_settings',
                  $form_fields
            );
            
      }
      
}
