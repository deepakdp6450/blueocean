<?php
/**
 * Plugin Name: Ever Compare
 * Description: WooCommerce Product compare plugin
 * Plugin URI: https://hasthemes.com/plugins/
 * Author: HasTheme
 * Author URI: https://hasthemes.com/
 * Version: 1.3.6
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ever-compare
 * Domain Path: /languages
 * WC tested up to: 10.0.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* Plugin Main Class
*/
final class Ever_Compare{

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.3.6';

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Base]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * [__construct] Class Constructor
     */
    private function __construct(){

        $this->define_constants();
        add_action( 'plugins_loaded', [ $this, 'includes' ] );

        register_activation_hook( EVERCOMPARE_FILE, [ $this, 'activate' ] );

        add_action( 'init', [ $this, 'i18n' ] );
        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );

        // Compatible With WooCommerce Features
        add_action( 'before_woocommerce_init', function() {
            if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', EVERCOMPARE_FILE, true );
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', EVERCOMPARE_FILE, true );
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'product_block_editor', EVERCOMPARE_FILE, true );
            }
        } );
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'EVERCOMPARE_VERSION', self::version );
        define( 'EVERCOMPARE_FILE', __FILE__ );
        define( 'EVERCOMPARE_PATH', __DIR__ );
        define( 'EVERCOMPARE_DIR', plugin_dir_path( EVERCOMPARE_FILE ) );
        define( 'EVERCOMPARE_URL', plugins_url( '', EVERCOMPARE_FILE ) );
        define( 'EVERCOMPARE_ASSETS', EVERCOMPARE_URL . '/assets' );
        define( 'EVERCOMPARE_BASE', plugin_basename( EVERCOMPARE_FILE ) );
    }

    /**
     * [includes] Load file
     * @return [void]
     */
    public function includes(){
        require_once EVERCOMPARE_PATH . '/vendor/autoload.php';
        if ( ! function_exists('is_plugin_active') ){ include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }
    }

    /**
     * [i18n] Load text domain
     * @return [void]
     */
    public function i18n() {
        load_plugin_textdomain( 'ever-compare', false, dirname( plugin_basename( EVERCOMPARE_FILE ) ) . '/languages/' );
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        EverCompare\Assets::instance();

        if ( is_admin() ) {
            $this->admin_notices();
            EverCompare\Admin::instance();
        }

        if ( ! ever_compare_woocommerce_installed() ) {
            return;
        }

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            EverCompare\Ajax::instance();
        }

        EverCompare\Frontend::instance();

        // add image size
        $this->set_image_size();

        // let's filter the woocommerce image size
        add_filter( 'woocommerce_get_image_size_ever-compare-image', [ $this, 'wc_image_filter_size' ], 10, 1 );

        // let's filter the speculation rules
        add_filter( 'wp_speculation_rules_href_exclude_paths', function( $exclude_paths ) {
            $compare_page_id = ever_compare_get_option( 'compare_page', 'ever_compare_table_settings_tabs' );
            if ( $compare_page_id ) {
                $compare_page_url = get_permalink( $compare_page_id );
                $compare_path = parse_url( $compare_page_url, PHP_URL_PATH );
                if ( $compare_path ) {
                    $compare_path = trailingslashit( $compare_path );
                    $exclude_paths[] = "{$compare_path}*";
                }
            }
            return $exclude_paths;
        });
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $this->includes();
        $installer = new EverCompare\Installer();
        $installer->run();
    }

    /**
     * Admin Notices
     * @return void
     */
    public function admin_notices() {
        $notice = new EverCompare\Admin\Notices();
        $notice->notice();
    }

    /**
     * [set_image_size] Set Image Size
     */
    public function set_image_size(){

        $image_dimention = ever_compare_get_option( 'image_size', 'ever_compare_table_settings_tabs', array( 'width'=>300, 'height'=>300 ) );
        if( isset( $image_dimention ) && is_array( $image_dimention ) ){
            $hard_crop = !empty( ever_compare_get_option( 'hard_crop', 'ever_compare_table_settings_tabs' ) ) ? true : false;
            add_image_size( 'ever-compare-image', $image_dimention['width'], $image_dimention['height'], $hard_crop );
        }

    }

    /**
     * [wc_image_filter_size]
     * @return [array]
     */
    public function wc_image_filter_size(){

        $image_dimention = ever_compare_get_option( 'image_size', 'ever_compare_table_settings_tabs', array('width'=>300,'height'=>300) );
        $hard_crop = !empty( ever_compare_get_option( 'hard_crop', 'ever_compare_table_settings_tabs' ) ) ? true : false;

        if( isset( $image_dimention ) && is_array( $image_dimention ) ){
            return array(
                'width'  => isset( $image_dimention['width'] ) ? absint( $image_dimention['width'] ) : 300,
                'height' => isset( $image_dimention['height'] ) ? absint( $image_dimention['height'] ) : 300,
                'crop'   => isset( $hard_crop ) ? 1 : 0,
            );
        }

        return array(
            'width'  => 300,
            'height' => 300,
            'crop'   => 0,
        );
    }

}

/**
 * Initializes the main plugin
 *
 * @return ever_compare
 */
function ever_compare() {
    if( ! class_exists('Woolentor_Ever_Compare') ){
        return Ever_Compare::instance();
    }
}

// Start Our journey
ever_compare();
