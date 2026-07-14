<?php
namespace MoveAddons\Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* Admin Dashboard Class
*/
class Admin_Dashboard{

    /**
     * Parent Menu Page Slug
     */
    const MENU_PAGE_SLUG = 'move-elementor';

    /**
     * Menu capability
     */
    const MENU_CAPABILITY = 'manage_options';

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
     * @return [Admin_Dashboard]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * [init] Assets Initializes
     * @return [void]
     */
    public function init(){
        add_action( 'admin_menu', [ $this, 'add_menu' ], 25 );
        add_action( 'wp_ajax_htmove_save_opt_data', [ $this, 'save_data' ] );

        add_filter('plugin_action_links_'.MOVE_ADDONS_BASE, [ $this, 'plugin_action_links' ] );
    }

    /**
     * [add_menu] Admin Menu
     */
    public function add_menu(){

        global $submenu;

        self::$parent_menu_hook = add_menu_page( 
            esc_html__( 'Move Addon', 'moveaddons' ), 
            esc_html__( 'Move Addon', 'moveaddons' ), 
            self::MENU_CAPABILITY, 
            self::MENU_PAGE_SLUG, 
            [ $this,'dashboard' ], 
            MOVE_ADDONS_ASSETS .'admin/images/menu_icon.png', 
            59 
        );

        if ( current_user_can( self::MENU_CAPABILITY ) ) {

            foreach ( $this->tabs_nav() as $tabkey => $tab ) {
                if( isset( $tab['link'] ) && !empty( $tab['link'] ) ){ continue; }
                $submenu[ self::MENU_PAGE_SLUG ][] = array( 
                    esc_html__( $tab['title'], 'moveaddons' ), 
                    self::MENU_CAPABILITY, 
                    'admin.php?page=' . self::MENU_PAGE_SLUG .'#'.$tabkey
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
        wp_enqueue_style( 'move-icon' );
        wp_enqueue_style( 'move-admin' );
        wp_enqueue_script( 'move-admin' );
    }

    /**
     * [save_data] Wp Ajax Callback
     * @return [JSON|Null]
     */
    public function save_data(){

        if ( ! current_user_can( self::MENU_CAPABILITY ) ) {
            return;
        }

        check_ajax_referer( 'htmove_save_opt_nonce', 'nonce' );

        $form_data = ( !empty( $_POST['data'] ) ? sanitize_text_field( $_POST['data'] ) : '' );
        
        if( !empty( $form_data ) ) {
            parse_str( $form_data, $data );
        } else {
            return;
        }

        $widgets  = Admin_Options_Fields::instance()->widgets();
        $userdataes = Admin_Options_Fields::instance()->userdata();
        $modules = Admin_Options_Fields::instance()->modules();

        $widgets_to_save    = [];
        $userdataes_to_save = [];
        $modules_to_save    = [];

        
        foreach( $widgets as $widget_key => $widget ) {
            $widgets_to_save[ $widget_key ] = boolval( $data[ $widget_key ] ? true : false );
        }

        foreach( $userdataes as $data_key => $userdata ) {
            // If Multiple Field
            $field_list = ( isset( $userdata['fields'] ) ? $userdata['fields'] : '' );
            if( is_array( $field_list ) ){
                foreach ( $field_list as $fkey => $field ) {
                    $userdataes_to_save[$data_key]['fields'][$fkey]['fvalue'] = ( !empty( $data[ $fkey ] ) ? $data[ $fkey ] : '' );
                }
            }else{
                $userdataes_to_save[$data_key]['value'] = ( !empty( $data[ $data_key ] ) ? $data[ $data_key ] : '' );
            }
        }

        foreach( $modules as $module_key => $module ) {
            $modules_to_save[ $module_key ] = boolval( $data[ $module_key ] ? true : false );
        }

        update_option( 'htmove_widget_list', $widgets_to_save );
        update_option( 'htmove_userdata_list', $userdataes_to_save );
        update_option( 'htmove_module_list', $modules_to_save );

        wp_send_json_success([
            'message' => esc_html__( 'Data Saved successfully!', 'moveaddons' )
        ]);

    }

    /**
     * [load_template] Template load
     * @param  [string] $template template suffix
     * @return [void]
     */
    private static function load_template( $template ) {
        $tmp_file = MOVE_ADDONS_PL_PATH . 'includes/templates/admin/dashboard-' . $template . '.php';
        if ( file_exists( $tmp_file ) ) {
            include_once( $tmp_file );
        }
    }

    /**
     * [dashboard] Render Dashboard
     * @return [void]
     */
    public function dashboard(){
        ?>
        <div class="wrap">
            <div id="htmove-admin-panel" class="htmove-admin-panel">
                <?php self::load_template('header'); ?>
                <div class="htmove-admin-tab-content">
                    <form class="move-dashboard" id="move-options-form">
                        <?php self::load_template('welcome'); ?>
                        <?php self::load_template('widgets'); ?>
                        <?php self::load_template('modules'); ?>
                        <?php self::load_template('prowidget'); ?>
                        <?php self::load_template('userdata'); ?>
                        <?php self::load_template('gopro'); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php 
        self::load_template('propopup');
    }

    /**
    * [plugin_action_links] add plugin action link
    * @param  [array] $links default plugin action link
    * @return [array] plugin action link
    */
    public function plugin_action_links( $links ) {

        if ( ! current_user_can( self::MENU_CAPABILITY ) ) {
            return $links;
        }

        $settings_link = '<a href="'.admin_url('admin.php?page=move-elementor#welcome').'">'.esc_html__( 'Settings', 'moveaddons' ).'</a>'; 

        array_unshift( $links, $settings_link );

        if( !is_plugin_active('move-pro/move-pro.php') ){
            $links['movego_pro'] = sprintf('<a href="'.esc_url('https://moveaddons.com').'" target="_blank" style="color: #39b54a; font-weight: bold;">' . esc_html__('Go Pro','moveaddons') . '</a>');
        }

        return $links; 
    }

    /**
     * [tabs_nav]
     * @return [array]
     */
    public function tabs_nav() {

        $tabs = [
            'welcome' => [
                'title'     => esc_html__( 'Welcome', 'moveaddons' ),
                'subtitle'  => esc_html__( 'General Information', 'moveaddons' ),
                'icon'      => 'move-home',
                'class'     => '',
            ],
            'widgets' => [
                'title' => esc_html__( 'Widgets', 'moveaddons' ),
                'subtitle'  => esc_html__( 'Enable/Disable widgets', 'moveaddons' ),
                'icon'      => 'move-magic',
                'class'     => '',
            ],
            'userdata' => [
                'title' => esc_html__( 'User Data', 'moveaddons' ),
                'subtitle'  => esc_html__( 'Facebook, Twitter, etc. data', 'moveaddons' ),
                'icon'      => 'move-server',
                'class'     => '',
            ],
            'prowidget' => [
                'title' => esc_html__( 'Pro Widgets', 'moveaddons' ),
                'subtitle'  => esc_html__( 'Available in our pro version.', 'moveaddons' ),
                'icon'      => 'move-magic',
                'class'     => 'htmove-pro-nav',
            ],
            'modules' => [
                'title' => esc_html__( 'Modules', 'moveaddons' ),
                'subtitle'  => esc_html__( 'Enable/Disable modules', 'moveaddons' ),
                'icon'      => 'move-sliders-h',
                'class'     => '',
            ],
            'gopro' => [
                'title' => esc_html__( 'Go Pro', 'moveaddons' ),
                'subtitle'  => esc_html__( 'Huge Features in our pro', 'moveaddons' ),
                'icon'      => 'move-stars',
                'class'     => 'htmove-pro-nav',
            ],
        ];

        return apply_filters( 'move_dashboard_tabs_nav', $tabs );

    }


}