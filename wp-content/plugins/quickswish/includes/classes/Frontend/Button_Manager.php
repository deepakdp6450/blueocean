<?php
namespace QuickSwish\Frontend;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Manage Button class
 */
class Button_Manager {
    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Button_Manager]
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
        add_action( 'init', [ $this, 'button_manager' ] );
    }

    /**
     * [button_manager] Button Manager
     * @return [void]
     */
    public function button_manager(){

        $button_position  = quickswish_get_option( 'button_position', 'quickswish_setting_tabs', 'before_cart_btn' );        
        
        // Shop Button Position
        if( $button_position !== 'use_shortcode' && $this->is_enable() == 'on' ){
            switch ( $button_position ) {
                case 'before_cart_btn':
                    add_action( 'woocommerce_after_shop_loop_item', [ $this, 'button_print' ], 7 );
                    break;

                case 'top_thumbnail':
                    add_action( 'woocommerce_before_shop_loop_item', [ $this, 'button_print' ], 5 );
                    break;
                
                default:
                    add_action( 'woocommerce_after_shop_loop_item', [ $this, 'button_print' ], 20 );
                    break;
            }
        }


    }

    /**
     * [button_print]
     * @return [void]
     */
    public function button_print(){
        echo quickswish_do_shortcode( 'quickswish_button' );
    }

    /**
     * [button_html] Button HTML
     * @param  [type] $atts template attr
     * @return [HTML]
     */
    public function button_html( $atts ) {
        $button_attr = apply_filters( 'quickswish_button_arg', $atts );
        return quickswish_get_template( 'quickswish-'.$atts['template_name'].'.php', $button_attr, false );
    }

    /**
     * [is_enable] Check quickview enable
     * @return boolean
     */
    public function is_enable(){

        $enable_button           = quickswish_get_option( 'enable_button', 'quickswish_setting_tabs', 'on' ) === 'on';
        $button_enable_on_mobile = quickswish_get_option( 'button_enable_on_mobile', 'quickswish_setting_tabs', 'on' ) === 'on';

        if( !wp_is_mobile() && $enable_button ){
            $show_button = 'on';
        }else if( wp_is_mobile() && $button_enable_on_mobile ){
           $show_button = 'on';
        }else{
            $show_button = 'off';
        }

        return $show_button;

    }


}