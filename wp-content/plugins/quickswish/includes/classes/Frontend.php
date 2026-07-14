<?php
namespace QuickSwish;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Frontend handlers class
 */
class Frontend {

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Frontend]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Class constructor
     */
    private function __construct() {
        Frontend\Shortcode::instance();
        Frontend\Button_Manager::instance();
        if( Frontend\Button_Manager::instance()->is_enable() == 'on' ){
            Frontend\Popup_Manager::instance();
        }
    }

}