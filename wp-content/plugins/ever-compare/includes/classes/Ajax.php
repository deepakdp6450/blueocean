<?php
namespace EverCompare;

defined( 'ABSPATH' ) || exit;
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
     * Initialize the class
     */
    private function __construct() {

        // Add Ajax Callback
        add_action( 'wp_ajax_ever_compare_add_to_compare', [ $this, 'add_to_compare' ] );
        add_action( 'wp_ajax_nopriv_ever_compare_add_to_compare', [ $this, 'add_to_compare' ] );

        // Remove Ajax Callback
        add_action( 'wp_ajax_ever_compare_remove_from_compare', [ $this, 'remove_from_compare' ] );
        add_action( 'wp_ajax_nopriv_ever_compare_remove_from_compare', [ $this,'remove_from_compare' ] );

        // Get Nonce endpoint (no nonce verification needed)
        add_action( 'wp_ajax_ever_compare_get_nonce', [ $this, 'get_nonce' ] );
        add_action( 'wp_ajax_nopriv_ever_compare_get_nonce', [ $this, 'get_nonce' ] );

        // Get Table endpoint
        add_action( 'wp_ajax_ever_compare_get_table', [ $this, 'get_table' ] );
        add_action( 'wp_ajax_nopriv_ever_compare_get_table', [ $this, 'get_table' ] );

    }

    /**
     * [add_to_compare] Product add ajax callback
     */
    public function add_to_compare(){
        check_ajax_referer('ever_compare_nonce', 'nonce');
        if(!empty($_GET['id'])) {
            $id = absint( $_GET['id'] );
            \EverCompare\Frontend\Manage_Compare::instance()->add_to_compare( $id );
        }
    }

    /**
     * [remove_from_compare] Product delete ajax callback
     * @return [void]
     */
    public function remove_from_compare(){
        check_ajax_referer('ever_compare_nonce', 'nonce');
        if(!empty($_GET['id'])) {
            $id = absint( $_GET['id'] );
            \EverCompare\Frontend\Manage_Compare::instance()->remove_from_compare( $id );
        }
    }

    /**
     * [get_nonce] Return a fresh nonce for AJAX operations
     * @return [void]
     */
    public function get_nonce(){
        wp_send_json( array(
            'nonce' => wp_create_nonce( 'ever_compare_nonce' ),
        ) );
    }

    /**
     * [get_table] Return compare table HTML for given product IDs
     * @return [void]
     */
    public function get_table(){
        $ids = array();
        if ( ! empty( $_GET['ids'] ) ) {
            $raw_ids = sanitize_text_field( wp_unslash( $_GET['ids'] ) );
            $ids = array_filter( array_map( 'absint', explode( ',', $raw_ids ) ) );
            $ids = array_slice( $ids, 0, \EverCompare\Frontend\Manage_Compare::instance()->max_limit );
        }

        if ( empty( $ids ) ) {
            wp_send_json( array(
                'table' => '',
                'count' => 0,
            ) );
        }

        $cookie_name = \EverCompare\Frontend\Manage_Compare::instance()->get_cookie_name();
        $_COOKIE[ $cookie_name ] = wp_json_encode( $ids );

        ob_start();
        \EverCompare\Frontend\Manage_Compare::instance()->get_response_html();
        $table_html = ob_get_clean();

        wp_send_json( array(
            'table' => $table_html,
            'count' => count( $ids ),
            'products' => $ids,
        ) );
    }

}