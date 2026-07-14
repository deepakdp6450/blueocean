<?php
namespace QuickSwish;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Load general WP action hook
 */
class Actions {

	/**
     * [$_instance]
     * @var [void]
     */
	private static $_instance;

	/**
     * [instance] Initializes a singleton instance
     * @return [Actions]
     */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
	}

	/**
     * [__construct] Class constructor.
     */
	public function __construct() {
        register_activation_hook( QUICKSWISH_BASE, [ $this, 'activate' ] );
		add_action( 'init', [ $this, 'i18n' ] );
	}

    /**
     * [i18n] Load text domain
     * @return [void]
     */
    public function i18n() {
        load_plugin_textdomain( 'quickswish', false, dirname( plugin_basename( QUICKSWISH_FILE ) ) . '/languages/' );
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $installer = new Installer();
        $installer->run();
    }


}
