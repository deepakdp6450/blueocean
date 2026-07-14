<?php

namespace MoveAddons\Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* Assest Manager Class
*/
class QuickView{

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [QuickView]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * [init] QuickView Initializes
     * @return [void]
     */
    public function init() {
        add_action( 'move_footer_content', [ $this, 'quick_view_html' ], 10 );

        add_action( 'wp_ajax_move_quickview', [ $this, 'quickview_ajax' ] );
        add_action( 'wp_ajax_nopriv_move_quickview', [ $this, 'quickview_ajax' ] );
    }

    public function quick_view_html(){
        echo '<div class="woocommerce htmove-quick-view-modal" id="htmovequick-viewmodal" style="visibility: hidden;opacity: 0;display:none;"><div class="htmove-modal-dialog product"><div class="htmove-modal-content"><button type="button" class="htmove-modal-close">&times;</button><div class="htmove-modal-body"></div></div></div></div>';
    }

    public function quickview_ajax() {

        check_ajax_referer( 'htmove_quickview_nonce', 'nonce' );

        if ( isset( $_POST['id'] ) && (int) $_POST['id'] ) {
            global $post, $product, $woocommerce;
            $id      = ( int ) $_POST['id'];
            $post    = get_post( $id );
            $product = wc_get_product($id);
            if ( $product && is_a( $product, 'WC_Product' ) ) { 
                if( $product->get_catalog_visibility() === 'hidden'){
                    wp_die( -1, 403 );
                }
                include_once ( apply_filters( 'move_quickview_tmp', MOVE_ADDONS_PL_PATH.'includes/templates/quickview.php' ) );
            }
        }
        wp_die();

    }


}
