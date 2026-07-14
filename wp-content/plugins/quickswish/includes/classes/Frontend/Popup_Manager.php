<?php
namespace QuickSwish\Frontend;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Manage PopUp class
 */
class Popup_Manager {

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Popup_Manager]
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

        // Render Modal HTML
        add_action( 'wp_footer', [ $this, 'render_modal_html' ] );

        // Popup content
        $this->popup_action_content();

        // Remove single page redirect action URL
        add_filter( 'woocommerce_add_to_cart_form_action', [ $this, 'remove_form_action_url' ], 10, 1 );

    }

    /**
     * [render_modal_html]
     * @return [void]
     */
    public function render_modal_html(){
        quickswish_get_template( 'quickswish-popup-modal.php', null, true );
    }

    /**
     * [popup_content] Popup content HTML
     * @param  [type] $atts template attr
     * @return [HTML]
     */
    public function popup_content( $atts ) {
        $popup_attr = apply_filters( 'quickswish_pupup_arg', $atts );
        return quickswish_get_template( 'quickswish-'.$atts['template_name'].'.php', $popup_attr, false );
    }

    /**
     * [is_fire_quickview_ajax] If our ajax action is fire
     * @return boolean
     */
    public function is_fire_quickview_ajax() {
        if( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'quickswish_product' ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * [remove_form_action_url] Remove single product page redirect. 
     * @param  [URL] $value
     * @return [URL] 
     */
    public function remove_form_action_url( $value ) {
        if ( $this->is_fire_quickview_ajax() ) {
            return '';
        }
        return $value;
    }

    /**
     * [popup_action_content] Popup Content
     * @return [void]
     */
    public function popup_action_content() {

        // Image.
        $this->image_manager();

        // Content.
        add_action('init', [$this, 'popup_content_manager']);

        // Social share button
        if( quickswish_get_option( 'enable_social_share','quickswish_modal_setting_tabs','on' ) === 'on' ){
            if(  quickswish_get_option( 'social_share_display_from','quickswish_modal_setting_tabs','custom' ) === 'custom' ){
                add_action( 'quickswish_product_content', [ $this, 'social_share' ], 50 );
            }else{
                add_action( 'quickswish_product_content', 'woocommerce_template_single_sharing', 45 );
            }
        }


    }

    /**
     * [popup_content_manager]
     * @return [void]
     */
    public function popup_content_manager(){

        $default_content = [
            'title'         => esc_html__( 'Title', 'quickswish' ),
            'rating'        => esc_html__( 'Rating', 'quickswish' ),
            'price'         => esc_html__( 'Price', 'quickswish' ),
            'excerpt'       => esc_html__( 'Excerpt', 'quickswish' ),
            'add_to_cart'   => esc_html__( 'Add to cart', 'quickswish' ),
            'meta'          => esc_html__( 'Meta', 'quickswish' ),
        ];
        $element_list = quickswish_get_option( 'select_content_to_show','quickswish_modal_setting_tabs', $default_content );
        $element_list = is_array($element_list) ? $element_list : [];
        $priority = 0;
        foreach ( $element_list as $elementkey => $element ) {
            $priority += 5;
            $this->popup_display_content( $elementkey, $priority );
        }

    }

    /**
     * [popup_display_content]
     * @param  [string] $element Element content key
     * @return [void] 
     */
    public function popup_display_content( $element, $priority ){

        switch ( $element ) {

            case 'rating':
                add_action( 'quickswish_product_content', 'woocommerce_template_single_rating', $priority );
                break;

            case 'price':
                add_action( 'quickswish_product_content', 'woocommerce_template_single_price', $priority );
                break;

            case 'excerpt':
                add_action( 'quickswish_product_content', 'woocommerce_template_single_excerpt', $priority );
                break;

            case 'add_to_cart':
                add_action( 'quickswish_product_content', 'woocommerce_template_single_add_to_cart', $priority );
                break;

            case 'meta':
                add_action( 'quickswish_product_content', 'woocommerce_template_single_meta', $priority );
                break;
            
            default:
                case 'title':
                add_action( 'quickswish_product_content', 'woocommerce_template_single_title', $priority );
                break;

        }


    }

    /**
     * [image_manager]
     * @return [void]
     */
    public function image_manager(){
        $image_layout = quickswish_get_option( 'thumbnail_layout','quickswish_modal_setting_tabs', 'theme' );

        if( $image_layout === 'theme' ){
            add_action( 'quickswish_product_image', 'woocommerce_show_product_sale_flash', 10 );
            add_action( 'quickswish_product_image', 'woocommerce_show_product_images', 20 );
        }else{
            $atts = [];
            $image_attr = apply_filters( 'quickswish_product_image_arg', $atts );
            add_action( 'quickswish_product_image', 'woocommerce_show_product_sale_flash', 10 );
            add_action( 'quickswish_product_image', function() use ( $image_attr ){
                quickswish_get_template( 'quickswish-product-images.php', $image_attr, true );
            }, 20 );
        }

    }

    /**
     * [social_media_share]
     * @return [void]
     */
    public function social_share(){
        $atts = [];
        $social_share_attr = apply_filters( 'quickswish_social_share_arg', $atts );
        quickswish_get_template( 'quickswish-social-share.php', $social_share_attr, true );
    }


}