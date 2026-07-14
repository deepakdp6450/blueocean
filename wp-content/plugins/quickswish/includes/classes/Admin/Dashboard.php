<?php
namespace QuickSwish\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
    const MENU_PAGE_SLUG = 'quickswish';

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

        add_action( 'admin_menu', [ $this, 'add_menu' ], 20 );

        add_filter('plugin_action_links_'.QUICKSWISH_BASE, [ $this, 'action_links' ] );

        add_action( 'admin_init', [ $this, 'admin_init' ] );

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

        $settings_link = '<a href="'.admin_url( 'admin.php?page='.self::MENU_PAGE_SLUG ).'">'.esc_html__( 'Settings', 'quickswish' ).'</a>'; 

        array_unshift( $links, $settings_link );

        return $links; 
    }

    /**
     * [add_menu] Admin Menu
     */
    public function add_menu(){

        global $submenu;

        self::$parent_menu_hook = add_menu_page( 
            esc_html__( 'QuickSwish', 'quickswish' ), 
            esc_html__( 'QuickSwish', 'quickswish' ), 
            self::MENU_CAPABILITY, 
            self::MENU_PAGE_SLUG, 
            [ $this,'dashboard' ], 
            'dashicons-search',
            59 
        );

        if ( current_user_can( self::MENU_CAPABILITY ) ) {

            foreach ( $this->sub_menu_nav() as $menukey => $menu ) {
                $submenu[ self::MENU_PAGE_SLUG ][] = array( 
                    esc_html__( $menu['title'], 'quickswish' ), 
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
        wp_enqueue_style( 'quickswish-admin' );
        wp_enqueue_script( 'quickswish-admin' );
    }

    /**
     * [dashboard] Dashboard plugin page
     * @return [HTML]
     */
    public function dashboard(){
        Admin_Fields::instance()->options_page();
    }

    /**
     * [sub_menu_nav]
     * @return [array]
     */
    public function sub_menu_nav() {

        $submenu = [
            'settings' => [
                'title'     => esc_html__( 'Settings', 'quickswish' ),
                'subtitle'  => esc_html__( 'Settings', 'quickswish' ),
                'icon'      => '',
                'class'     => '',
            ],
        ];

        return apply_filters( 'quickswish_dashboard_submenu', $submenu );

    }

    /**
     * [plugin_recommendations]
     * @return [void]
     */
    public function plugin_recommendations(){

        $get_instance = Recommended_Plugins::instance( 
            array( 
                'text_domain'       => 'quickswish', 
                'parent_menu_slug'  => self::MENU_PAGE_SLUG, 
                'menu_capability'   => self::MENU_CAPABILITY, 
                'menu_page_slug'    => 'recommendations',
                'priority'          => 25,
                'assets_url'        => QUICKSWISH_ASSETS,
                'hook_suffix'       => 'quickswish_page_recommendations'
            )
        );

        $get_instance->add_new_tab( array(

            'title' => esc_html__( 'Recommended', 'quickswish' ),
            'active' => true,
            'plugins' => array(
                array(
                    'slug'      => 'woolentor-addons',
                    'location'  => 'woolentor_addons_elementor.php',
                    'name'      => esc_html__( 'WooLentor', 'quickswish' )
                ),
                array(
                    'slug'      => 'wc-builder',
                    'location'  => 'wc-builder.php',
                    'name'      => esc_html__( 'WC Builder', 'quickswish' )
                ),
                array(
                    'slug'      => 'ever-compare',
                    'location'  => 'ever-compare.php',
                    'name'      => esc_html__( 'EverCompare', 'quickswish' )
                ),
                array(
                    'slug'      => 'wishsuite',
                    'location'  => 'wishsuite.php',
                    'name'      => esc_html__( 'WishSuite', 'quickswish' )
                ),
                array(
                    'slug'      => 'whols',
                    'location'  => 'whols.php',
                    'name'      => esc_html__( 'Whols', 'quickswish' )
                ),
                array(
                    'slug'      => 'just-tables',
                    'location'  => 'just-tables.php',
                    'name'      => esc_html__( 'JustTables', 'quickswish' )
                ),
                array(
                    'slug'      => 'wc-multi-currency',
                    'location'  => 'wcmilticurrency.php',
                    'name'      => esc_html__( 'Multi Currency', 'quickswish' )
                )
            )

        ) );

        $get_instance->add_new_tab(array(
            'title' => esc_html__( 'You May Also Like', 'quickswish' ),
            'plugins' => array(

                array(
                    'slug'      => 'woolentor-addons-pro',
                    'location'  => 'woolentor_addons_pro.php',
                    'name'      => esc_html__( 'WooLentor Pro', 'quickswish' ),
                    'link'      => 'https://hasthemes.com/plugins/woolentor-pro-woocommerce-page-builder/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'WooLentor is one of the most popular WooCommerce Elementor Addons on WordPress.org. It has been downloaded more than 672,148 times and 60,000 stores are using WooLentor plugin. Why not you?', 'quickswish' ),
                ),

                array(
                    'slug'      => 'just-tables-pro',
                    'location'  => 'just-tables-pro.php',
                    'name'      => esc_html__( 'JustTables Pro', 'quickswish' ),
                    'link'      => 'https://hasthemes.com/wp/justtables/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'JustTables is an incredible WordPress plugin that lets you showcase all your WooCommerce products in a sortable and filterable table view. It allows your customers to easily navigate through different attributes of the products and compare them on a single page. This plugin will be of great help if you are looking for an easy solution that increases the chances of landing a sale on your online store.', 'quickswish' ),
                ),

                array(
                    'slug'      => 'whols-pro',
                    'location'  => 'whols-pro.php',
                    'name'      => esc_html__( 'Whols Pro', 'quickswish' ),
                    'link'      => 'https://hasthemes.com/plugins/whols-woocommerce-wholesale-prices/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'Whols is an outstanding WordPress plugin for WooCommerce that allows store owners to set wholesale prices for the products of their online stores. This plugin enables you to show special wholesale prices to the wholesaler. Users can easily request to become a wholesale customer by filling out a simple online registration form. Once the registration is complete, the owner of the store will be able to review the request and approve the request either manually or automatically.', 'quickswish' ),
                ),

                array(
                    'slug'      => 'multicurrencypro',
                    'location'  => 'multicurrencypro.php',
                    'name'      => esc_html__( 'Multi Currency Pro for WooCommerce', 'quickswish' ),
                    'link'      => 'https://hasthemes.com/plugins/multi-currency-pro-for-woocommerce/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'Multi-Currency Pro for WooCommerce is a prominent currency switcher plugin for WooCommerce. This plugin allows your website or online store visitors to switch to their preferred currency or their country’s currency.', 'quickswish' ),
                ),

                array(
                    'slug'      => 'email-candy-pro',
                    'location'  => 'email-candy-pro.php',
                    'name'      => esc_html__( 'Email Candy Pro', 'quickswish' ),
                    'link'      => 'https://hasthemes.com/plugins/email-candy-pro/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'Email Candy is an outstanding WordPress plugin that allows you to customize the default WooCommerce email templates and give a professional look to your WooCommerce emails. If you are tired of using the boring design of WooCommerce emails and want to create customized emails, then this plugin will come in handy.', 'quickswish' ),
                ),
            )
        ));

        $get_instance->add_new_tab(array(
            'title' => esc_html__( 'Others', 'quickswish' ),
            'plugins' => array(

                array(
                    'slug'      => 'ht-mega-for-elementor',
                    'location'  => 'htmega_addons_elementor.php',
                    'name'      => esc_html__( 'HT Mega', 'quickswish' )
                ),

                array(
                    'slug'      => 'ht-slider-for-elementor',
                    'location'  => 'ht-slider-for-elementor.php',
                    'name'      => esc_html__( 'HT Slider For Elementor', 'quickswish' )
                ),

                array(
                    'slug'      => 'wp-plugin-manager',
                    'location'  => 'plugin-main.php',
                    'name'      => esc_html__( 'WP Plugin Manager', 'quickswish' )
                ),

                array(
                    'slug'      => 'ht-contactform',
                    'location'  => 'contact-form-widget-elementor.php',
                    'name'      => esc_html__( 'HT Contact Form 7', 'quickswish' )
                ),

                array(
                    'slug'      => 'ht-wpform',
                    'location'  => 'wpform-widget-elementor.php',
                    'name'      => esc_html__( 'HT WPForms', 'quickswish' )
                ),

                array(
                    'slug'      => 'hashbar-wp-notification-bar',
                    'location'  => 'init.php',
                    'name'      => esc_html__( 'HashBar', 'quickswish' )
                ),

                array(
                    'slug'      => 'ht-menu-lite',
                    'location'  => 'ht-mega-menu.php',
                    'name'      => esc_html__( 'HT Menu', 'quickswish' )
                ),

                array(
                    'slug'      => 'htmega-pro',
                    'location'  => 'htmega_pro.php',
                    'name'      => esc_html__( 'HT Mega Pro', 'quickswish' ),
                    'link'      => 'https://hasthemes.com/plugins/ht-mega-pro/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'HTMega is an absolute addon for elementor that includes 80+ elements & 360 Blocks with unlimited variations. HT Mega brings limitless possibilities. Embellish your site with the elements of HT Mega.', 'quickswish' ),
                ),

                array(
                    'slug'      => 'hashbar-pro',
                    'location'  => 'init.php',
                    'name'      => esc_html__( 'HashBar Pro', 'quickswish' ),
                    'link'      => 'https://hasthemes.com/plugins/wordpress-notification-bar-plugin/',
                    'author_link'=> 'https://hasthemes.com/',
                    'description'=> esc_html__( 'HashBar is a WordPress Notification / Alert / Offer Bar plugin which allows you to create unlimited notification bars to notify your customers. This plugin has option to show email subscription form (sometimes it increases up to 500% email subscriber), Offer text and buttons about your promotions. This plugin has the options to add unlimited background colors and images to make your notification bar more professional.', 'quickswish' ),
                ),

            )
        ));


    }

    /**
     * [admin_init]
     * @return [void]
     */
    public function admin_init(){
        $this->redirect_option_page();
        $this->admin_notices();
    }

    /**
     * [redirect_option_page] After Active the plugin then redirect to option page
     * @return [void]
     */
    public function redirect_option_page() {
        if ( get_option( 'quickswish_do_activation_redirect', FALSE ) ) {
            delete_option('quickswish_do_activation_redirect');
            if( !isset( $_GET['activate-multi'] ) ){
                wp_redirect( admin_url( "admin.php?page=".self::MENU_PAGE_SLUG ) );
            }
        }
    }

    /**
     * Admin Notices
     * @return void
     */
    public function admin_notices() {
        $notice = new Notices();
        $notice->notice();
    }


}