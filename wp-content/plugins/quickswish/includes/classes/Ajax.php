<?php
namespace QuickSwish;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Ajax handlers class
 */
class Ajax {

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Ajax]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Class construct
     */
    private function __construct() {

        // For Quickview popup
        add_action( 'wp_ajax_quickswish_product', [ $this, 'quickview' ] );
        add_action( 'wp_ajax_nopriv_quickswish_product', [ $this, 'quickview' ] );

        // For Add to cart
        if( quickswish_get_option( 'enable_ajax_cart','quickswish_modal_setting_tabs','on' ) === 'on' ){
            add_action( 'wp_ajax_quickswish_insert_to_cart', [ $this, 'insert_to_cart' ] );
            add_action( 'wp_ajax_nopriv_quickswish_insert_to_cart', [ $this, 'insert_to_cart' ] );
        }

    }

    /**
     * [quickview] Ajax Callable function
     * @return [void]
     */
    public function quickview(){
        if( isset( $_POST['id'] ) ) {

            $id = sanitize_text_field( (int) $_POST['id'] );
            if( ! $id || ! quickswish_is_woocommerce() ) {
                return;
            }

            check_ajax_referer( 'quickswish_nonce', 'nonce' );

            global $post, $product, $woocommerce;
            $post    = get_post( $id );
            $product = wc_get_product( $id );

            if ( $product ) {
                $atts = [
                    'template_name' => 'popup-content',
                    'product'       => $product,
                ];
                echo Frontend\Popup_Manager::instance()->popup_content( $atts );
            }

        }
        wp_die();

    }

    /**
     * [insert_to_cart] Insert product
     * @return [JSON]
     */
    public function insert_to_cart(){

        check_ajax_referer( 'quickswish_nonce', 'nonce' );

        if ( ! isset( $_POST['product_id'] ) ) {
            return;
        }

        $product_id         = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
        $quantity           = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
        $variation_id       = !empty( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
        $variations         = !empty( $_POST['variations'] ) ? array_map( 'sanitize_text_field', json_decode( stripslashes( $_POST['variations'] ), true ) ) : array();
        $passed_validation  = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );
        $product_status     = get_post_status( $product_id );

        $cart_item_data = $_POST;

        if ( $passed_validation && 'publish' === $product_status ) {

            if( count( $variations ) == 0 ){
                \WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations, $cart_item_data );
            }

            do_action( 'woocommerce_ajax_added_to_cart', $product_id );
            if ( 'yes' === get_option('woocommerce_cart_redirect_after_add') ) {
                wc_add_to_cart_message( array( $product_id => $quantity ), true );
            }
            \WC_AJAX::get_refreshed_fragments();
        } else {
            $data = array(
                'error' => true,
                'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
            );
            echo wp_send_json( $data );
        }
        wp_send_json_success();

        
    }
    

}