<?php
/**
 * Plugin Name: QuickSwish
 * Description: WooCommerce Product quick view WordPress plugin
 * Plugin URI: https://hasthemes.com/plugins/
 * Author: HasTheme
 * Author URI: https://hasthemes.com/
 * Version: 1.1.2
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: quickswish
 * Domain Path: /languages
 * WC tested up to: 9.8.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin Main Class
 */
final class QuickSwish{

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.1.2';

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [QuickSwish]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) && !( self::$_instance instanceof QuickSwish ) ) {
            self::$_instance = new self();
            self::$_instance->define_constants();
            self::$_instance->includes();
            self::$_instance->dependency_class_instance();
        }
        
        // Compatible With WooCommerce Custom Order Tables
        add_action( 'before_woocommerce_init', function() {
            if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', QUICKSWISH_FILE, true );
            }
        } );
        
        return self::$_instance;
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        $this->define( 'QUICKSWISH_VERSION', self::version );
        $this->define( 'QUICKSWISH_FILE', __FILE__ );
        $this->define( 'QUICKSWISH_PATH', __DIR__ );
        $this->define( 'QUICKSWISH_URL', plugins_url( '', QUICKSWISH_FILE ) );
        $this->define( 'QUICKSWISH_DIR', plugin_dir_path( QUICKSWISH_FILE ) );
        $this->define( 'QUICKSWISH_ASSETS', QUICKSWISH_URL . '/assets' );
        $this->define( 'QUICKSWISH_BASE', plugin_basename( QUICKSWISH_FILE ) );
    }

    /**
     * Define constant if not already set
     *
     * @param  string $name
     * @param  string|bool $value
     * @return type
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * [includes] Load file
     * @return [void]
     */
    public function includes(){
        include( QUICKSWISH_PATH . '/vendor/autoload.php' );
        if ( ! function_exists('is_plugin_active') ){ include( ABSPATH . 'wp-admin/includes/plugin.php' ); }
    }

   /**
     * Load actions
     *
     * @return void
     */
    private function dependency_class_instance() {
        \QuickSwish\Actions::instance();
        \QuickSwish\Scripts::instance();
        \QuickSwish\Frontend::instance();
        if ( is_admin() ) {
            \QuickSwish\Admin::instance();
        }
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            \QuickSwish\Ajax::instance();
        }
    }

}

/**
 * Initializes the main plugin
 *
 * @return HTQuickview
 */
function QuickSwish() {
    return QuickSwish::instance();
}

/**
 * Get the plugin running. Load on plugins_loaded action to avoid issue on multisite.
 */
QuickSwish();
