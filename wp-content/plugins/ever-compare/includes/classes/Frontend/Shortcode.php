<?php
namespace EverCompare\Frontend;

defined( 'ABSPATH' ) || exit;
/**
 * Shortcode handler class
 */
class Shortcode {

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
     * Initializes the class
     */
    function __construct() {
        add_shortcode( 'evercompare_button', [ $this, 'button_shortcode' ] );
        add_shortcode( 'evercompare_table', [ $this, 'table_shortcode' ] );
        add_shortcode( 'evercompare_counter', [ $this, 'counter_shortcode' ] );
    }

    /**
     * [button_shortcode] Compare Button Shortcode callable function
     * @param  [type] $atts 
     * @param  string $content
     * @return [HTML] 
     */
    public function button_shortcode( $atts, $content = '' ){

        wp_enqueue_style( 'evercompare-frontend' );
        wp_enqueue_script( 'evercompare-frontend' );

        global $product;
        $product_id = '';
        $product_title = '';
        if ( $product && is_a( $product, 'WC_Product' ) ) {
            $product_id = $product->get_id();
            $product_title = $product->get_name();
        } else if (get_post_type(get_the_ID()) === 'product') {
            $product_id = get_the_ID();
            $product_title = get_the_title($product_id);
        }

        // Fetch option data
        $remove_on_click    = ever_compare_get_option( 'remove_on_click','ever_compare_settings_tabs', 'off' );
        $button_text        = ever_compare_get_option( 'button_text','ever_compare_settings_tabs', 'Compare' );
        $button_added_text  = ever_compare_get_option( 'added_button_text','ever_compare_settings_tabs', 'Added' );
        $button_remove_text  = ever_compare_get_option( 'remove_button_text','ever_compare_settings_tabs', 'Remove' );
        $button_remove_text =  empty($button_remove_text) ? __('Remove', 'ever-compare') : $button_remove_text;

        $shop_page_btn_position     = ever_compare_get_option( 'shop_btn_position', 'ever_compare_settings_tabs', 'after_cart_btn' );
        $product_page_btn_position  = ever_compare_get_option( 'product_btn_position', 'ever_compare_settings_tabs', 'after_cart_btn' );
        $button_style               = ever_compare_get_option( 'button_style', 'ever_compare_style_tabs', 'theme' );

        $button_class = array(
            'htcompare-btn',
            'htcompare-btn-style-'.$button_style,
            'htcompare-shop-'.$shop_page_btn_position,
            'htcompare-product-'.$product_page_btn_position,
        );

        if( $button_style === 'theme' ){
            $button_class[] = 'button';
        }

        $button_icon        = $this->get_icon();
        $added_button_icon  = $this->get_icon('added_');
        
        if( !empty( $button_text ) ){
            $button_text = '<span class="evercompare-btn-text">'.$button_text.'</span>';
        }

        if($remove_on_click === 'off'){
            if( !empty( $button_added_text ) ){
                $button_added_text = '<span class="evercompare-btn-text">'.$button_added_text.'</span>';
            }
        } else {
            $button_added_text = '<span class="evercompare-btn-text">'.$button_remove_text.'</span>';
        }

        // Shortcode atts
        $default_atts = array(
            'product_id'        => $product_id,
            'product_title'     => $product_title,
            'button_url'        => Manage_Compare::instance()->get_compare_page_url(),
            'button_class'      => implode(' ', $button_class ),
            'button_text'       => $button_icon.$button_text,
            'button_added_text' => $added_button_icon.$button_added_text,
            'template_name'     => 'add',
        );

        $atts = shortcode_atts( $default_atts, $atts, $content );
        return Manage_Compare::instance()->button_html( $atts );

    }

    /**
     * [table_shortcode] Compare table shortcode callable function
     * @param  [type] $atts
     * @param  string $content
     * @return [HTML] 
     */
    public function table_shortcode( $atts, $content = '' ){

        wp_enqueue_style( 'evercompare-frontend' );
        wp_enqueue_script( 'evercompare-frontend' );

        // Shareable link via query param — render server-side (not cached due to query param)
        if ( ! empty( $_GET['evcompare'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            /* Fetch From option data */
            $empty_compare_text = ever_compare_get_option('empty_table_text','ever_compare_table_settings_tabs');
            $return_shop_button = ever_compare_get_option('shop_button_text','ever_compare_table_settings_tabs','Return to shop');

            /* Product and Field */
            $products   = Manage_Compare::instance()->get_compared_products_data();
            $fields     = Manage_Compare::instance()->get_compare_fields();

            $custom_heading = !empty( ever_compare_get_option( 'table_heading', 'ever_compare_table_settings_tabs' ) ) ? ever_compare_get_option( 'table_heading', 'ever_compare_table_settings_tabs' ) : array();

            $default_atts = array(
                'evercompare'  => Manage_Compare::instance(),
                'products'     => $products,
                'fields'       => $fields,
                'return_shop_button'=> $return_shop_button,
                'heading_txt'       => $custom_heading,
                'empty_compare_text'=> $empty_compare_text,
            );

            $atts = shortcode_atts( $default_atts, $atts, $content );
            return Manage_Compare::instance()->table_html( $atts );
        }

        // Cookie-based: render placeholder that JS will populate via AJAX
        return '<div class="htcompare-table" data-ajax-load="true"></div>';

    }

    /**
     * Evercompare Counter Shortcode
     *
     * @param [array] $atts
     * @param string $content
     * @return void
     */
    public function counter_shortcode( $atts, $content = '' ){

        wp_enqueue_style( 'evercompare-frontend' );
        wp_enqueue_script( 'evercompare-frontend' );

        $default_atts = array(
            'count'      => 0,
            'page_url'      => Manage_Compare::instance()->get_compare_page_url(),
        );

        $atts = shortcode_atts( $default_atts, $atts, $content );
        return Manage_Compare::instance()->count_html( $atts );

    }

    /**
     * [get_icon]
     * @param  string $type
     * @return [HTML]
     */
    public function get_icon( $type = '' ){

        $default_icon   = ever_compare_icon_list('default');
        $default_loader = '<span class="ever-compare-loader">'.ever_compare_icon_list('loading').'</span>';
        
        $button_text = ( $type === 'added' ) ? ever_compare_get_option( 'added_button_text','ever_compare_settings_tabs', 'Added' ) : ever_compare_get_option( 'button_text','ever_compare_settings_tabs', 'Compare' );

        $button_icon_type   = ever_compare_get_option( $type.'button_icon_type', 'ever_compare_settings_tabs', 'default' );

        if( $button_icon_type === 'custom' ){
            $button_icon = ever_compare_get_option( $type.'button_custom_icon','ever_compare_settings_tabs', '' );
        }elseif( $button_icon_type === 'default' ){
            return $default_icon;
        }else{
            $button_icon = '';
        }

        if( !empty( $button_icon ) ){
            $button_icon = '<span class="ever-compare-btn-image"><img src="'.esc_url( $button_icon ).'" alt="'.esc_attr( $button_text ).'"></span>';
        }

        return $default_loader.$button_icon;
        
    }


}
