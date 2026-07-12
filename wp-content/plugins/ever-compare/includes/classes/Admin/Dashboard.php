<?php
namespace EverCompare\Admin;

defined( 'ABSPATH' ) || exit;
/**
 * Dashboard handlers class
 */
class Dashboard {

    /**
     * Menu capability
     */
    const MENU_CAPABILITY = 'manage_options';

    /**
     * Parent Menu Page Slug
     */
    const MENU_PAGE_SLUG = 'evercompare';

    /**
     * [$parent_menu_hook] Parent Menu Hook
     * @var string
     */
    static $parent_menu_hook = '';

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Admin]
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

        Admin_Fields::instance();

        if( isset( get_option('woolentor_others_tabs')['compare'] ) && get_option('woolentor_others_tabs')['compare'] == 'on' ){
            add_action( 'admin_menu', [ $this, 'add_menu_if_woolentor' ], 226 );
        }else{
            add_action( 'admin_menu', [ $this, 'add_menu' ], 20 );
        }

        add_filter('plugin_action_links_'.EVERCOMPARE_BASE, [ $this, 'action_links' ] );

         // Add a post display state for special EverCompare page.
        add_filter( 'display_post_states', [ $this, 'add_display_post_states' ], 10, 2 );

        // Redirect Option page
        $this->redirect_option_page();

        // Recommended plugin
        add_action('init', [ $this, 'plugin_recommendations' ]);

    }

    /**
    * [action_links] add plugin action link
    * @param  [array] $links default plugin action link
    * @return [array] plugin action link
    */
    public function action_links( $links ) {

        if ( ! current_user_can( self::MENU_CAPABILITY ) ) {
            return $links;
        }

        $settings_link = '<a href="'.admin_url( 'admin.php?page='.self::MENU_PAGE_SLUG ).'">'.esc_html__( 'Settings', 'ever-compare' ).'</a>'; 

        array_unshift( $links, $settings_link );

        return $links; 
    }

    /**
     * [add_menu_if_woolentor] Admin Menu If WooLentor active
     */
    public function add_menu_if_woolentor(){

        self::$parent_menu_hook = add_submenu_page(
            'woolentor_page',
            esc_html__( 'Compare', 'ever-compare' ),
            esc_html__( 'Compare', 'ever-compare' ),
            'manage_options',
            self::MENU_PAGE_SLUG,
            [ $this,'dashboard' ]
        );

        add_action( 'load-' . self::$parent_menu_hook, [ $this, 'init_hooks'] );

    }

    /**
     * [add_menu] Admin Menu
     */
    public function add_menu(){

        global $submenu;

        self::$parent_menu_hook = add_menu_page( 
            esc_html__( 'Ever Compare', 'ever-compare' ), 
            esc_html__( 'Ever Compare', 'ever-compare' ), 
            self::MENU_CAPABILITY, 
            self::MENU_PAGE_SLUG, 
            [ $this,'dashboard' ], 
            'dashicons-update-alt', 
            59 
        );

        if ( current_user_can( self::MENU_CAPABILITY ) ) {

            foreach ( $this->sub_menu_nav() as $menukey => $menu ) {
                $submenu[ self::MENU_PAGE_SLUG ][] = array( 
                    $menu['title'], 
                    self::MENU_CAPABILITY,
                    'admin.php?page='.self::MENU_PAGE_SLUG.'#'.$menukey,
                );
            }

        }

        add_action( 'load-' . self::$parent_menu_hook, [ $this, 'init_hooks'] );
        

    }

    /**
     * Initialize our hooks for the admin page
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
     * [enqueue_scripts] Add Scripts Base Menu Slug
     * @param  [string] $hook
     * @return [void]
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 'evercompare-admin' );
        wp_enqueue_script( 'evercompare-admin' );
    }

    /**
     * [dashboard] Dashboard plugin page
     * @return [HTML]
     */
    public function dashboard(){
        Admin_Fields::instance()->plugin_page();
    }

    /**
     * [sub_menu_nav]
     * @return [array]
     */
    public function sub_menu_nav() {

        $submenu = [
            'settings' => [
                'title'     => esc_html__( 'Settings', 'ever-compare' ),
                'subtitle'  => esc_html__( 'Settings', 'ever-compare' ),
                'icon'      => '',
                'class'     => '',
            ],
        ];

        return apply_filters( 'ever_compare_dashboard_submenu', $submenu );

    }

    /**
     * [redirect_option_page] After Active the plugin then redirect to option page
     * @return [void]
     */
    public function redirect_option_page() {
        if ( get_option( 'evercompare_do_activation_redirect', FALSE ) ) {
            delete_option('evercompare_do_activation_redirect');
            if( !isset( $_GET['activate-multi'] ) ){ //phpcs:ignore WordPress.Security.NonceVerification.Recommended
                wp_redirect( admin_url( "admin.php?page=".self::MENU_PAGE_SLUG ) );
            }
        }
    }

    /**
     * Add a post display state for special WishSuite page in the page list table.
     *
     * @param array   $post_states An array of post display states.
     * @param WP_Post $post  The current post object.
     */
    public function add_display_post_states( $post_states, $post ){
        if ( (int)ever_compare_get_option( 'compare_page', 'ever_compare_table_settings_tabs' ) === $post->ID ) {
            $post_states['evercompare_page_for_compare_table'] = __( 'EverCompare', 'ever-compare' );
        }
        return $post_states;
    }

    /**
     * [plugin_recommendations]
     * @return [void]
     */
    public function plugin_recommendations(){

        $get_instance = Recommended_Plugins::instance( 
            array( 
                'text_domain'       => 'ever-compare', 
                'parent_menu_slug'  => self::MENU_PAGE_SLUG, 
                'menu_capability'   => self::MENU_CAPABILITY, 
                'menu_page_slug'    => 'everrecommendations',
                'priority'          => 25,
                'assets_url'        => EVERCOMPARE_ASSETS,
                'hook_suffix'       => 'ever-compare_page_everrecommendations'
            )
        );

        $get_instance->add_new_tab( array(

            'title' => esc_html__( 'Recommended', 'ever-compare' ),
            'active' => true,
            'plugins' => array(

                array(
                    'slug'      => 'woolentor-addons',
                    'location'  => 'woolentor_addons_elementor.php',
                    'name'      => esc_html__( 'WooLentor', 'ever-compare' )
                ),

                array(
                    'slug'      => 'ht-mega-for-elementor',
                    'location'  => 'htmega_addons_elementor.php',
                    'name'      => esc_html__( 'HT Mega', 'ever-compare' )
                ),

                array(
                    'slug'      => 'hashbar-wp-notification-bar',
                    'location'  => 'init.php',
                    'name'      => esc_html__( 'HashBar', 'ever-compare' )
                ),

                array(
                    'slug'      => 'ht-slider-for-elementor',
                    'location'  => 'ht-slider-for-elementor.php',
                    'name'      => esc_html__( 'HT Slider For Elementor', 'ever-compare' )
                ),

                array(
                    'slug'      => 'ht-contactform',
                    'location'  => 'contact-form-widget-elementor.php',
                    'name'      => esc_html__( 'HT Contact Form 7', 'ever-compare' )
                ),

                array(
                    'slug'      => 'ht-wpform',
                    'location'  => 'wpform-widget-elementor.php',
                    'name'      => esc_html__( 'HT WPForms', 'ever-compare' )
                ),

                array(
                    'slug'      => 'ht-menu-lite',
                    'location'  => 'ht-mega-menu.php',
                    'name'      => esc_html__( 'HT Menu', 'ever-compare' )
                ),

                array(
                    'slug'      => 'insert-headers-and-footers-script',
                    'location'  => 'init.php',
                    'name'      => esc_html__( 'HT Script', 'ever-compare' )
                ),

                array(
                    'slug'      => 'wp-plugin-manager',
                    'location'  => 'plugin-main.php',
                    'name'      => esc_html__( 'WP Plugin Manager', 'ever-compare' )
                ),

                array(
                    'slug'      => 'wc-builder',
                    'location'  => 'wc-builder.php',
                    'name'      => esc_html__( 'WC Builder', 'ever-compare' )
                ),

                array(
                    'slug'      => 'whols',
                    'location'  => 'whols.php',
                    'name'      => esc_html__( 'Whols', 'ever-compare' )
                ),

                array(
                    'slug'      => 'just-tables',
                    'location'  => 'just-tables.php',
                    'name'      => esc_html__( 'JustTables', 'ever-compare' )
                ),

                array(
                    'slug'      => 'wc-multi-currency',
                    'location'  => 'wcmilticurrency.php',
                    'name'      => esc_html__( 'Multi Currency', 'ever-compare' )
                )
            )

        ) );

        $get_instance->add_new_tab(array(
            'title' => esc_html__( 'You May Also Like', 'ever-compare' ),
            'plugins' => array(

                array(
                    'slug'      => 'woolentor-addons-pro',
                    'location'  => 'woolentor_addons_pro.php',
                    'name'      => esc_html__( 'WooLentor Pro', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/woolentor-pro-woocommerce-page-builder/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'WooLentor is one of the most popular WooCommerce Elementor Addons on WordPress.org. It has been downloaded more than 672,148 times and 60,000 stores are using WooLentor plugin. Why not you?', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'htmega-pro',
                    'location'  => 'htmega_pro.php',
                    'name'      => esc_html__( 'HT Mega Pro', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/ht-mega-pro/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'HTMega is an absolute addon for elementor that includes 80+ elements & 360 Blocks with unlimited variations. HT Mega brings limitless possibilities. Embellish your site with the elements of HT Mega.', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'swatchly-pro',
                    'location'  => 'swatchly-pro.php',
                    'name'      => esc_html__( 'Product Variation Swatches', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/swatchly-product-variation-swatches-for-woocommerce-products/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'Are you getting frustrated with WooCommerce’s current way of presenting the variants for your products? Well, say goodbye to dropdowns and start showing the product variations in a whole new light with Swatchly.', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'whols-pro',
                    'location'  => 'whols-pro.php',
                    'name'      => esc_html__( 'Whols Pro', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/whols-woocommerce-wholesale-prices/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'Whols is an outstanding WordPress plugin for WooCommerce that allows store owners to set wholesale prices for the products of their online stores. This plugin enables you to show special wholesale prices to the wholesaler. Users can easily request to become a wholesale customer by filling out a simple online registration form. Once the registration is complete, the owner of the store will be able to review the request and approve the request either manually or automatically.', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'just-tables-pro',
                    'location'  => 'just-tables-pro.php',
                    'name'      => esc_html__( 'JustTables Pro', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/wp/justtables/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'JustTables is an incredible WordPress plugin that lets you showcase all your WooCommerce products in a sortable and filterable table view. It allows your customers to easily navigate through different attributes of the products and compare them on a single page. This plugin will be of great help if you are looking for an easy solution that increases the chances of landing a sale on your online store.', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'multicurrencypro',
                    'location'  => 'multicurrencypro.php',
                    'name'      => esc_html__( 'Multi Currency Pro for WooCommerce', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/multi-currency-pro-for-woocommerce/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'Multi-Currency Pro for WooCommerce is a prominent currency switcher plugin for WooCommerce. This plugin allows your website or online store visitors to switch to their preferred currency or their country’s currency.', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'cf7-extensions-pro',
                    'location'  => 'cf7-extensions-pro.php',
                    'name'      => esc_html__( 'Extensions For CF7 Pro', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/cf7-extensions/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'Contact Form7 Extensions plugin is a fantastic WordPress plugin that enriches the functionalities of Contact Form 7.This all-in-one WordPress plugin will help you turn any contact page into a well-organized, engaging tool for communicating with your website visitors by providing tons of advanced features like drag and drop file upload, repeater field, trigger error for already submitted forms, popup form response, country flags and dial codes with a telephone input field and acceptance field, etc. in addition to its basic features.', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'hashbar-pro',
                    'location'  => 'init.php',
                    'name'      => esc_html__( 'HashBar Pro', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/wordpress-notification-bar-plugin/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'HashBar is a WordPress Notification / Alert / Offer Bar plugin which allows you to create unlimited notification bars to notify your customers. This plugin has option to show email subscription form (sometimes it increases up to 500% email subscriber), Offer text and buttons about your promotions. This plugin has the options to add unlimited background colors and images to make your notification bar more professional.', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'wp-plugin-manager-pro',
                    'location'  => 'plugin-main.php',
                    'name'      => esc_html__( 'WP Plugin Manager Pro', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/wp-plugin-manager-pro/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'WP Plugin Manager Pro is a specialized WordPress Plugin that helps you to deactivate unnecessary WordPress Plugins page wise and boosts the speed of your WordPress site to improve the overall site performance.', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'ht-script-pro',
                    'location'  => 'plugin-main.php',
                    'name'      => esc_html__( 'HT Script Pro', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/insert-headers-and-footers-code-ht-script/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'Insert Headers and Footers Code allows you to insert Google Analytics, Facebook Pixel, custom CSS, custom HTML, JavaScript code to your website header and footer without modifying your theme code.This plugin has the option to add any custom code to your theme in one place, no need to edit the theme code. It will save your time and remove the hassle for the theme update.', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'ht-menu',
                    'location'  => 'ht-mega-menu.php',
                    'name'      => esc_html__( 'HT Menu Pro', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/ht-menu-pro/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'WordPress Mega Menu Builder for Elementor', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'ht-slider-addons-pro',
                    'location'  => 'ht-slider-addons-pro.php',
                    'name'      => esc_html__( 'HT Slider Pro For Elementor', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/ht-slider-pro-for-elementor/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'HT Slider Pro is a plugin to create a slider for WordPress websites easily using the Elementor page builder. 80+ prebuild slides/templates are included in this plugin. There is the option to create a post slider, WooCommerce product slider, Video slider, image slider, etc. Fullscreen, full width and box layout option are included.', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'ht-google-place-review',
                    'location'  => 'ht-google-place-review.php',
                    'name'      => esc_html__( 'Google Place Review', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/google-place-review-plugin-for-wordpress/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'If you are searching for a modern and excellent google places review WordPress plugin to showcase reviews from Google Maps and strengthen trust between you and your site visitors, look no further than HT Google Place Review', 'ever-compare' ),
                ),

                array(
                    'slug'      => 'was-this-helpful',
                    'location'  => 'was-this-helpful.php',
                    'name'      => esc_html__( 'Was This Helpful?', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/was-this-helpful/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( "Was this helpful? is a WordPress plugin that allows you to take visitors' feedback on your post/pages or any article. A visitor can share his feedback by like/dislike/yes/no", 'ever-compare' ),
                ),

                array(
                    'slug'      => 'ht-click-to-call',
                    'location'  => 'ht-click-to-call.php',
                    'name'      => esc_html__( 'HT Click To Call', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/ht-click-to-call/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( "HT – Click to Call is a lightweight WordPress plugin that allows you to add a floating click to call button on your website. It will offer your website visitors an opportunity to call your business immediately at the right moment, especially when they are interested in your products or services and seeking more information.", 'ever-compare' ),
                ),

                array(
                    'slug'      => 'docus-pro',
                    'location'  => 'docus-pro.php',
                    'name'      => esc_html__( 'Docus Pro', 'ever-compare' ),
                    'link'      => 'https://hasthemes.com/plugins/docus-pro-youtube-video-playlist/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( "Embedding a YouTube playlist into your website plays a vital role to curate your content into several categories and make your web content more engaging and popular by keeping the visitors on your website for a longer period.", 'ever-compare' ),
                ),

            )
        ));

        $get_instance->add_new_tab(array(
            'title' => esc_html__( 'Others', 'ever-compare' ),
            'plugins' => array(

                array(
                    'slug'      => 'really-simple-google-tag-manager',
                    'location'  => 'really-simple-google-tag-manager.php',
                    'name'      => esc_html__( 'Really Simple Google Tag Manager', 'ever-compare' )
                ),

                array(
                    'slug'      => 'ht-instagram',
                    'location'  => 'ht-instagram.php',
                    'name'      => esc_html__( 'HT Feed', 'ever-compare' )
                ),

                array(
                    'slug'      => 'faster-youtube-embed',
                    'location'  => 'faster-youtube-embed.php',
                    'name'      => esc_html__( 'Faster YouTube Embed', 'ever-compare' )
                ),

                array(
                    'slug'      => 'wc-sales-notification',
                    'location'  => 'wc-sales-notification.php',
                    'name'      => esc_html__( 'WC Sales Notification', 'ever-compare' )
                ),

                array(
                    'slug'      => 'preview-link-generator',
                    'location'  => 'preview-link-generator.php',
                    'name'      => esc_html__( 'Preview Link Generator', 'ever-compare' )
                ),

                array(
                    'slug'      => 'quickswish',
                    'location'  => 'quickswish.php',
                    'name'      => esc_html__( 'QuickSwish', 'ever-compare' )
                ),

                array(
                    'slug'      => 'docus',
                    'location'  => 'docus.php',
                    'name'      => esc_html__( 'Docus – YouTube Video Playlist', 'ever-compare' )
                ),

                array(
                    'slug'      => 'data-captia',
                    'location'  => 'data-captia.php',
                    'name'      => esc_html__( 'DataCaptia', 'ever-compare' )
                ),

                array(
                    'slug'      => 'coupon-zen',
                    'location'  => 'coupon-zen.php',
                    'name'      => esc_html__( 'Coupon Zen', 'ever-compare' )
                ),

                array(
                    'slug'      => 'sirve',
                    'location'  => 'sirve.php',
                    'name'      => esc_html__( 'Sirve – Simple Directory Listing', 'ever-compare' )
                ),

                array(
                    'slug'      => 'ht-social-share',
                    'location'  => 'ht-social-share.php',
                    'name'      => esc_html__( 'HT Social Share', 'ever-compare' )
                ),

            )
        ));


    }
    

}