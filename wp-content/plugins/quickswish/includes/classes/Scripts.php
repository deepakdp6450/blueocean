<?php
namespace QuickSwish;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Load general WP action hook
 */
class Scripts {

	/**
     * [$_instance]
     * @var [void]
     */
	private static $_instance;

	/**
	 * Registers the plugin.
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
        add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );
    }

	/**
     * All available scripts
     *
     * @return array
     */
    public function get_scripts() {

        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        $script_list = [
            'slick' => [
                'src'     => QUICKSWISH_ASSETS . '/lib/js/slick.min.js',
                'version' => filemtime( QUICKSWISH_PATH . '/assets/lib/js/slick.min.js' ),
                'deps'    => [ 'jquery' ]
            ],
            'quickswish-admin' => [
                'src'     => QUICKSWISH_ASSETS . '/js/admin.js',
                'version' => QUICKSWISH_VERSION,
                'deps'    => [ 'jquery' ]
            ],
            'quickswish-frontend' => [
                'src'     => QUICKSWISH_ASSETS . '/js/frontend.js',
                'version' => QUICKSWISH_VERSION,
                'deps'    => [ 'jquery', 'wc-add-to-cart-variation', 'wc-single-product' ]
            ],
        ];

        if ( current_theme_supports( 'wc-product-gallery-zoom' ) ) {
            array_push( $script_list['quickswish-frontend']['deps'], 'zoom' );
        }

        if ( current_theme_supports( 'wc-product-gallery-slider' ) ) {
            array_push( $script_list['quickswish-frontend']['deps'],'flexslider' );
        }

        if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
            array_push( $script_list['quickswish-frontend']['deps'],'photoswipe-ui-default' );
        }

        return $script_list;

    }

    /**
     * All available styles
     *
     * @return array
     */
    public function get_styles() {

        $style_list = [
            'slick' => [
                'src'     => QUICKSWISH_ASSETS . '/lib/css/slick.css',
                'version' => filemtime( QUICKSWISH_PATH . '/assets/lib/css/slick.css' ),
            ],
            'quickswish-admin' => [
                'src'     => QUICKSWISH_ASSETS . '/css/admin.css',
                'version' => QUICKSWISH_VERSION,
            ],
            'quickswish-frontend' => [
                'src'     => QUICKSWISH_ASSETS . '/css/frontend.css',
                'version' => QUICKSWISH_VERSION,
                'deps'    => []
            ],
        ];

        if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
            array_push( $style_list['quickswish-frontend']['deps'],'photoswipe-default-skin' );
        }

        return $style_list;

    }

    /**
     * Register scripts and styles
     *
     * @return void
     */
    public function register_assets() {
        $scripts = $this->get_scripts();
        $styles  = $this->get_styles();

        foreach ( $scripts as $handle => $script ) {
            $deps = isset( $script['deps'] ) ? $script['deps'] : false;
            wp_register_script( $handle, $script['src'], $deps, $script['version'], true );
        }

        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;
            wp_register_style( $handle, $style['src'], $deps, $style['version'] );
        }

         // Inline CSS
        wp_add_inline_style( 'quickswish-frontend', $this->inline_style() );

        // Frontend Localize data
        $option_data = array(
            'enable_ajax_cart' => quickswish_get_option( 'enable_ajax_cart','quickswish_modal_setting_tabs','on' ),
            'thumbnail_layout' => quickswish_get_option( 'thumbnail_layout','quickswish_modal_setting_tabs','theme' ),
        );
        $localize_data = array(
            'ajaxurl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'quickswish_nonce' ),
            'option_data' => $option_data,
        );

        // Admin Localize data
        $setting_page = 0;
        if( isset( $_GET['page'] ) && $_GET['page'] == 'quickswish' ){
            $setting_page = 1;
        }
        $admin_option_data = array(
            'button_position'     => quickswish_get_option( 'button_position','quickswish_setting_tabs','before_cart_btn' ),
            'button_icon_type'    => quickswish_get_option( 'button_icon_type','quickswish_setting_tabs','default' ),
            'button_style'        => quickswish_get_option( 'button_style', 'quickswish_style_tab', 'default' ),
            'popup_style'         => quickswish_get_option( 'popup_style','quickswish_style_tab','default' ),
            'enable_social_share' => quickswish_get_option( 'enable_social_share','quickswish_modal_setting_tabs','on' ),
            'thumbnail_layout'    => quickswish_get_option( 'thumbnail_layout','quickswish_modal_setting_tabs','theme' ),
        );
        $admin_localize_data = array(
            'ajaxurl'    => admin_url( 'admin-ajax.php' ),
            'is_settings'=> $setting_page,
            'option_data'=> $admin_option_data,
        );

        wp_localize_script( 'quickswish-frontend', 'QSVIEW', $localize_data );
        wp_localize_script( 'quickswish-admin', 'QSVIEW', $admin_localize_data );
        
    }

    /**
     * [inline_style]
     * @return [CSS String]
     */
    public function inline_style(){

        $inline_css = '';

        // Button Custom Style
        if( 'custom' === quickswish_get_option( 'button_style', 'quickswish_style_tab', 'default' ) ){

            $btn_padding        = quickswish_dimensions( 'button_custom_padding','quickswish_style_tab','padding' );
            $btn_margin         = quickswish_dimensions( 'button_custom_margin','quickswish_style_tab','margin' );
            $btn_border_width   = quickswish_dimensions( 'button_custom_border','quickswish_style_tab','border-width' );
            $btn_border_radius  = quickswish_dimensions( 'button_custom_border_radius','quickswish_style_tab','border-radius' );
            $btn_border_style   = !empty( $btn_border_width ) ? 'border-style:solid;' : '';

            $btn_border_color = quickswish_generate_css('button_custom_border_color','quickswish_style_tab','border-color');
            $btn_color    = quickswish_generate_css('button_color','quickswish_style_tab','color');
            $btn_bg_color = quickswish_generate_css('background_color','quickswish_style_tab','background-color');

            // Hover
            $btn_hover_color    = quickswish_generate_css('button_hover_color','quickswish_style_tab','color');
            $btn_hover_bg_color = quickswish_generate_css('hover_background_color','quickswish_style_tab','background-color');

            $inline_css .= "
                .quickswish-btn{
                    {$btn_padding}
                    {$btn_margin}
                    {$btn_color}
                    {$btn_bg_color}
                    {$btn_border_width}
                    {$btn_border_style}
                    {$btn_border_color}
                    {$btn_border_radius}
                }
                .quickswish-btn:hover{
                    {$btn_hover_color}
                    {$btn_hover_bg_color}
                }
            ";

        }

        // Popup Window style
        if( 'custom' === quickswish_get_option( 'popup_style', 'quickswish_style_tab', 'default' ) ){

            $popup_max_width = quickswish_generate_css( 'max_width','quickswish_style_tab','max-width', 'px' );

            $inline_css .= "
                .quickswish-modal-wrapper{
                    {$popup_max_width}
                }
            ";
            
        }

        // Thumbnail slider arrow style
        if( 'slider' === quickswish_get_option( 'thumbnail_layout', 'quickswish_modal_setting_tabs', 'theme' ) ){

            $arrow_color    = quickswish_generate_css( 'slider_arrow_color','quickswish_modal_setting_tabs','color' );
            $arrow_bg_color = quickswish_generate_css( 'slider_arrow_bg_color','quickswish_modal_setting_tabs','background-color' );

            $arrow_hover_color      = quickswish_generate_css( 'slider_arrow_hover_color','quickswish_modal_setting_tabs','color' );
            $arrow_hover_bg_color   = quickswish_generate_css( 'slider_arrow_hover_bg_color','quickswish_modal_setting_tabs','background-color' );

            $active_image_border_color = quickswish_generate_css( 'active_thubnail_border_color','quickswish_modal_setting_tabs','border-color' );

            $inline_css .= "
                .quickswish-content-area span.slick-arrow{
                    {$arrow_color}
                    {$arrow_bg_color}
                }
                .quickswish-content-area span.slick-arrow:hover{
                    {$arrow_hover_color}
                    {$arrow_hover_bg_color}
                }
                .slick-current.slick-active .quickswish-thumb-single img{
                    {$active_image_border_color}
                }
            ";

        }

        return $inline_css;

    }


}
