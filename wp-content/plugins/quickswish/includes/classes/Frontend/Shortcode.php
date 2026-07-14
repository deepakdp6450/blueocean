<?php
namespace QuickSwish\Frontend;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Shortcode handler class
 */
class Shortcode {

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Shortcode]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * [__construct] Class construct
     */
    function __construct() {
        add_shortcode( 'quickswish_button', [ $this, 'button_shortcode' ] );
    }

    /**
     * [button_shortcode] Button Shortcode callable function
     * @param  [type] $atts 
     * @param  string $content
     * @return [HTML] 
     */
    public function button_shortcode( $atts, $content = '' ){
        
        if( 'slider' === quickswish_get_option( 'thumbnail_layout', 'quickswish_modal_setting_tabs', 'theme' ) ){
            wp_enqueue_style( 'slick' );
            wp_enqueue_script( 'slick' );
        }

        wp_enqueue_style( 'quickswish-frontend' );
        wp_enqueue_script( 'quickswish-frontend' );
        add_action( 'wp_footer', 'woocommerce_photoswipe' );

        global $product;
        $product_id = $product_url = '';
        if ( $product && is_a( $product, 'WC_Product' ) ) {
            $product_id  = $product->get_id();
            $product_url = $product->get_permalink();
        }

        /**
         * Get Settings data
         */
        $button_text      = quickswish_get_option( 'button_text', 'quickswish_setting_tabs', 'QuickView' );
        $button_position  = quickswish_get_option( 'button_position', 'quickswish_setting_tabs', 'before_cart_btn' );
        $button_style     = quickswish_get_option( 'button_style', 'quickswish_style_tab', 'default' );
        $icon_type        = quickswish_get_option( 'button_icon_type', 'quickswish_setting_tabs', 'default' );

        $button_icon  = $this->get_icon();
        if( !empty( $button_text ) ){
            $button_text = '<span class="quickswish-btn-text">'.wp_kses_post($button_text).'</span>';
        }

        $button_class = [
            'quickswish-btn',
            'quickswish-btn-pos-'.$button_position,
            'quickswish-btn-icon-'.$icon_type,
        ];

        if( $button_style === 'theme' ){
            $button_class[] = 'button';
        }

        // Shortcode atts
        $default_atts = array(
            'product_id'     => $product_id,
            'button_url'     => $product_url,
            'button_class'   => implode(' ', $button_class ),
            'button_text'    => $button_icon.$button_text,
            'template_name'  => 'button',
        );
        $atts = shortcode_atts( $default_atts, $atts, $content );
        return Button_Manager::instance()->button_html( $atts );

    }

    /**
     * [get_icon]
     * @param  string $type
     * @return [HTML]
     */
    public function get_icon(){

        $default_icon = quickswish_icon_list('default');
        $default_loader = '<span class="quickswish-loader">'.quickswish_icon_list('loading').'</span>';
        
        $button_text        = quickswish_get_option( 'button_text', 'quickswish_setting_tabs', 'QuickView' );
        $button_icon_type   = quickswish_get_option( 'button_icon_type', 'quickswish_setting_tabs', 'default' );

        if( $button_icon_type === 'custom' ){
            $button_icon = quickswish_get_option( 'button_custom_icon','quickswish_setting_tabs', '' );
        }elseif( $button_icon_type === 'default' ){
            return $default_icon;
        }else{
            $button_icon = '';
        }

        if( !empty( $button_icon ) ){
            $button_icon = '<span class="quickswish-btn-image"><img src="'.esc_url( $button_icon ).'" alt="'.esc_attr( $button_text ).'"></span>';
        }

        return $default_loader.$button_icon;

    }


}