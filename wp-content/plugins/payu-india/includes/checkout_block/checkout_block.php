<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class WC_Payu_Blocks extends AbstractPaymentMethodType 
{
    protected $name = 'payu';

    public function initialize()
    {
       // $this->settings = get_option('woocommerce_payubiz_settings');
        $this->settings = get_option('woocommerce_payubiz_settings', []);
    }

    public function get_payment_method_script_handles()
    {
        wp_register_script(
            'payu-blocks-integration',
            plugin_dir_url(__FILE__) . 'checkout_block.js',
            [
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
                'wp-i18n',
            ],
            null,
            true
        );

        
        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations('payu-blocks-integration', 'payu-india', plugin_dir_path(__FILE__) . 'languages');
        }

        return ['payu-blocks-integration'];
    }

    public function get_payment_method_data()
    {
        return [
            'title' => $this->settings['title'] ?? esc_html__('Pay by PayUBiz', 'payu-india'),
            'description' => $this->settings['description'] ?? esc_html__('Pay securely using PayUBiz.', 'payu-india'),
            'image'       => plugins_url('images/payubizlogo.png', dirname(__FILE__)),
        ]; 
    }
}
