<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class uthr_Customizer{

    public $prefixid = 'uthr_';

    private static $_instance = null;
    public static function instance(){
        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    function __construct(){
        add_action( 'customize_register', [ $this, 'add_settings' ], 999 );
        add_action( 'customize_register', [ $this, 'add_controls' ], 999 );
    }

    public function add_settings( $wp_customize ) {
        foreach ( $this->get_setting_controls() as $setting_key => $setting ) {
            $wp_customize->add_setting( $this->prefixid . $setting_key, array(
                'type'              => 'option',
                'capability'        => 'edit_theme_options',
                'transport'         => isset( $setting['transport'] ) ? $setting['transport'] : 'postMessage',
                'default'           => isset( $setting['default'] ) ? $setting['default'] : '',
                'sanitize_callback' => isset( $setting['sanitize_callback'] ) ? array( 'uthr_Sanitize', $setting['sanitize_callback'] ) : '',
            ) );
        }
    }

    // Add Control
    public function add_controls( $wp_customize ){

        foreach ( $this->get_setting_controls() as $setting_key => $setting ) {
            
            // Add Section
            $this->section_add( $wp_customize, $setting );

            // Get control class name (none, color, upload, image)
            $control_class = isset( $setting['control_type'] ) ? ucfirst( $setting['control_type'] ) . '_' : '';
            $control_class = 'WP_Customize_' . $control_class . 'Control';

            // Control Configuration
            $control_config = array(
                'label'    => $setting['title'],
                'settings' => $this->prefixid . $setting_key,
                'priority' => isset( $setting['priority'] ) ? $setting['priority'] : 10,
            );

            // Description
            if ( ! empty( $setting['description'] ) ) {
                $control_config['description'] = $setting['description'];
            }

            // Add control to section
            if ( ! empty( $setting['section'] ) ) {
                $control_config['section'] = $this->prefixid . $setting['section'];
            }

            // Add custom field type
            if ( ! empty( $setting['type'] ) ) {
                $control_config['type'] = $setting['type'];
            }

            // Add select field options
            if ( ! empty( $setting['choices'] ) ) {
                $control_config['choices'] = $setting['choices'];
            }
            // Input attributese
            if ( ! empty( $setting['input_attrs'] ) ) {
                $control_config['input_attrs'] = $setting['input_attrs'];
            }

            // Repeater controls:
            if ( ! empty( $setting['customizer_repeater_image_control'] ) ) {
                $control_config['customizer_repeater_image_control'] = $setting['customizer_repeater_image_control'];
            }
            if ( ! empty( $setting['customizer_repeater_icon_control'] ) ) {
                $control_config['customizer_repeater_icon_control'] = $setting['customizer_repeater_icon_control'];
            }
            if ( ! empty( $setting['customizer_repeater_title_control'] ) ) {
                $control_config['customizer_repeater_title_control'] = $setting['customizer_repeater_title_control'];
            }
            if ( ! empty( $setting['customizer_repeater_link_control'] ) ) {
                $control_config['customizer_repeater_link_control'] = $setting['customizer_repeater_link_control'];
            }

            $wp_customize->add_control( new $control_class( $wp_customize, $this->prefixid . $setting_key, $control_config ) );

        }

    }

    // Section add
    public function section_add( $wp_customize, $fields ){
        // Get sections
        $sections = $this->get_sections();
        if ( ! empty( $fields['section'] ) && isset( $sections[ $fields['section'] ] ) ) {
            // Section key
            $section_key = $fields['section'];
            // Data Reference from section
            $section = $sections[ $section_key ];
            // Section config
            $section_config = array(
                'title'     => $section['title'],
                'priority'  => ( isset( $section['priority'] ) ? $section['priority'] : 10 ),
            );
            // Description
            if ( ! empty( $section['description'] ) ) {
                $section_config['description'] = $section['description'];
            }

            // Add Panel
            $this->panel_add( $wp_customize, $section );

            // Add Section to panel
            if ( ! empty( $section['panel'] ) ) {
                $section_config['panel'] = $this->prefixid . $section['panel'];
            }

            // Register section
            $wp_customize->add_section( $this->prefixid . $section_key, $section_config );
        }
    }

    // Panel Add
    public function panel_add( $wp_customize, $fieldssection ){
        // Panel List
        $panels = $this->get_panels();
        if ( ! empty( $fieldssection['panel'] ) && isset( $panels[ $fieldssection['panel'] ] ) ) {
            
            // Reference current panel key
            $panel_key = $fieldssection['panel'];
            // Data Reference from panel
            $panel = $panels[ $panel_key ];

            // Panel config
            $panel_config = array(
                'title'         => $panel['title'],
                'priority'      => ( isset( $panel['priority'] ) ? $panel['priority'] : 10 ),
            );
            // Panel description
            if ( ! empty( $panel['description'] ) ) {
                $panel_config['description'] = $panel['description'];
            }
            // Register panel
            $wp_customize->add_panel( $this->prefixid . $panel_key, $panel_config );

        }
    }

    // Panel List
    public function get_panels(){
        $panels = array(
            // Penel.
            'uthr_panel' => array(
                'title'    => esc_html__( 'Uthr Theme Options', 'uthr' ),
                'priority' => 170,
            ),
        );
        return $panels;
    }
    // Sections
    public function get_sections(){
        $sections = array(
            'theme_color_settings' => array(
                'title'    => esc_html__( 'Theme Color Settings', 'uthr' ),
                'panel' => 'uthr_panel',
                'priority'  => 10,
            ),
            'header_top_settings' => array(
                'title'    => esc_html__( 'Header Top Settings', 'uthr' ),
                'panel' => 'uthr_panel',
                'priority'  => 11,
            ),
            'header_settings' => array(
                'title'    => esc_html__( 'Header Settings', 'uthr' ),
                'panel' => 'uthr_panel',
                'priority'  => 12,
            ),
            'premium_btn_settings' => array(
                'title'    => esc_html__( 'Menu Button', 'uthr' ),
                'panel' => 'uthr_panel',
                'priority'  => 13,
            ),            
            'page_title_settings' => array(
                'title'    => esc_html__( 'Page Title Settings', 'uthr' ),
                'panel' => 'uthr_panel',
                'priority'  => 14,
            ),
            
            'blogs_settings' => array(
                'title'    => esc_html__( 'Blog layout Settings', 'uthr' ),
                'panel' => 'uthr_panel',
                'priority'  => 15,
            ),
            
            'footer_settings' => array(
                'title'    => esc_html__( 'Footer Settings', 'uthr' ),
                'panel' => 'uthr_panel',
                'priority'  => 16,
            ), 

            'social_settings' => array(
                'title'    => esc_html__( 'Social Links Settings', 'uthr' ),
                'panel' => 'uthr_panel',
                'priority'  => 17,
            ), 
            'error_settings' => array(
                'title'    => esc_html__( '404 Page Settings', 'uthr' ),
                'panel' => 'uthr_panel',
                'priority'  => 18,
            ), 
            'woo_settings' => array(
                'title'    => esc_html__( 'WooCommerce Settings', 'uthr' ),
                'panel' => 'uthr_panel',
                'priority'  => 18,
            ),

        );
        return $sections;
    }


    // Settings Controll Field
    public function get_setting_controls(){
        $controls = array();

        $controls['theme_pry_color'] = array(
            'title' => esc_html__('Theme Primary Color','uthr'),
            'section'   => 'theme_color_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>10,
        );
            $controls['theme_sery_color'] = array(
            'title' => esc_html__('Theme Secondary Color','uthr'),
            'section'   => 'theme_color_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>10,
        );
        // Header top settings
        $controls['header_top_show_hide'] = array(
            'title' => esc_html__('Show/Hide Header Top','uthr'),
            'section'   => 'header_top_settings',
            'type'   => 'checkbox',
            'default'     => true,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );            
        $controls['header_top_left_info1'] = array(
            'title' => esc_html__('Header Top Left Info ','uthr'),
            'section'   => 'header_top_settings',
            'type'   => 'text',
            'default'     => esc_html__( '0123 4567 8910','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );            
        $controls['header_top_left_info2'] = array(
            'title' => esc_html__('Header Top Left Info 2','uthr'),
            'section'   => 'header_top_settings',
            'type'   => 'text',
            'default'     => esc_html__( 'uthrstore@domain.com','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );
        $controls['header_top_middle_info'] = array(
            'title' => esc_html__('Header Top Middle Info','uthr'),
            'section'   => 'header_top_settings',
            'type'   => 'textarea',
            'default'     => esc_html__( 'Free shipping worldwide for orders over $99','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );
        $controls['header_top_right_show_hide'] = array(
            'title' => esc_html__('Show/Hide Header Top Right','uthr'),
            'section'   => 'header_top_settings',
            'type'   => 'checkbox',
            'default'     => true,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );
        $controls['header_top_right_follow_us'] = array(
            'title' => esc_html__('Follow us text','uthr'),
            'section'   => 'header_top_settings',
            'type'   => 'text',
            'default'     => esc_html__( 'Follow us','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );         
        $controls['header_top_bg_color'] = array(
            'title' => esc_html__('Header Top BG Color','uthr'),
            'section'   => 'header_top_settings',
            'type'   => 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,

        );
        // Header setting
        $controls['header_bgcolor'] = array(
            'title' => esc_html__('Header BG Color','uthr'),
            'section'   => 'header_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>10,
        );        
        $controls['header_menu_color'] = array(
            'title' => esc_html__('Header Menu Color','uthr'),
            'section'   => 'header_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>10,
        );
        $controls['header_menu_hover_color'] = array(
            'title' => esc_html__('Header Menu Hover Color','uthr'),
            'section'   => 'header_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>10,
        );


        $controls['user_search_show_hide'] = array(
            'title' => esc_html__('Show Search Button','uthr'),
            'section'   => 'header_settings',
            'type'   => 'checkbox',
            'default'     => true,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );
        $controls['cart_show_hide'] = array(
            'title' => esc_html__('Show Cart Button','uthr'),
            'section'   => 'header_settings',
            'type'   => 'checkbox',
            'default'     => true,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );
        $controls['userlogin_show_hide'] = array(
            'title' => esc_html__('Show User Info','uthr'),
            'section'   => 'header_settings',
            'type'   => 'checkbox',
            'default'     => true,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );
        $controls['wishlist_show_hide'] = array(
            'title' => esc_html__('Show Wishlist icon','uthr'),
            'section'   => 'header_settings',
            'type'   => 'checkbox',
            'default'     => true,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );
        $controls['login_color'] = array(
            'title' => esc_html__('Button Color','uthr'),
            'section'   => 'header_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>10,
        );        
        $controls['login_hover_color'] = array(
            'title' => esc_html__('Button Hover Color','uthr'),
            'section'   => 'header_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>10,
        );




        $controls['pre_button_text'] = array(
            'title' => esc_html__('Button Text','uthr'),
            'section'   => 'premium_btn_settings',
            'type'   => 'text',
            'default'     => esc_html__( '','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );

        $controls['pre_button_link'] = array(
            'title' => esc_html__('Button Link','uthr'),
            'section'   => 'premium_btn_settings',
            'type'   => 'text',
            'default'     => esc_html__( '#','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );
        $controls['button_bgcolor'] = array(
            'title' => esc_html__('Button BG Color','uthr'),
            'section'   => 'premium_btn_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>10,
        );
        $controls['button_color'] = array(
            'title' => esc_html__('Button Color','uthr'),
            'section'   => 'premium_btn_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>10,
        );
        $controls['button_bg_hover_color'] = array(
            'title' => esc_html__('Button Hover BG Color','uthr'),
            'section'   => 'premium_btn_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>10,
        );


        // page title 
        $controls['page_title_bgimage'] = array(
            'title' => esc_html__('Background Image','uthr'),
            'section'   => 'page_title_settings',
            'control_type'=> 'image',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>15,
        );
        $controls['page_title_bgcolor'] = array(
            'title' => esc_html__('Background Color','uthr'),
            'section'   => 'page_title_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>15,
        );
        $controls['page_title_color'] = array(
            'title' => esc_html__('Page Title Color','uthr'),
            'section'   => 'page_title_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>15,
        );        
        $controls['breadcrumb_color'] = array(
            'title' => esc_html__('Breadcrumb Color','uthr'),
            'section'   => 'page_title_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>15,
        );
        $controls['breadcrumb_color_hover'] = array(
            'title' => esc_html__('Breadcrumb Hover Color','uthr'),
            'section'   => 'page_title_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>15,
        );
        $controls['hide_page_title_page'] = array(
            'title' => esc_html__('To Hide Page Title Section On Page','uthr'),
            'section'   => 'page_title_settings',
            'type'   => 'checkbox',
            'default'     => false,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>15,
        );       

        // Blog setting
        $controls['show_read_more_button'] = array(
            'title' => esc_html__('Read More Button','uthr'),
            'section'   => 'blogs_settings',
            'type'   => 'checkbox',
            'default'     => false,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );
        $controls['read_more_text'] = array(
            'title' => esc_html__('Read More Button Text','uthr'),
            'section'   => 'blogs_settings',
            'type'   => 'text',
            'default'     => esc_html__( 'Read More','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );        
        $controls['show_content'] = array(
            'title' => esc_html__('Blog Content','uthr'),
            'section'   => 'blogs_settings',
            'type'   => 'checkbox',
            'default'     => false,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );

        $controls['blog_prev_text'] = array(
            'title' => esc_html__('Prev Button Text','uthr'),
            'section'   => 'blogs_settings',
            'type'   => 'text',
            'default'     => esc_html__( 'Prev','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>15,
        );
        $controls['blog_next_text'] = array(
            'title' => esc_html__('Next Button Text','uthr'),
            'section'   => 'blogs_settings',
            'type'   => 'text',
            'default'     => esc_html__( 'Next','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>15,
        );
        $controls['show_footer_newsletter'] = array(
            'title' => esc_html__('Show Footer Newslatter','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'checkbox',
            'default'     => false,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        ); 
        $controls['newsletter_title'] = array(
            'title' => esc_html__('Newslatter Title','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'text',
            'default'     => 'keep connected',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,

        );
        $controls['newsletter_content'] = array(
            'title' => esc_html__('Newslatter Content','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'textarea',
            'default'     => 'Get updates by subscribing to our weekly newsletter',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,

        ); 

        $controls['newsletter_shortcode'] = array(
            'title' => esc_html__('Newslatter Shortcode','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'text',
            'default'     => '',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,

        );        
        $controls['show_footer_widget_area'] = array(
            'title' => esc_html__('Show Footer Widgets Area','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'checkbox',
            'default'     => false,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>10,
        );
 

        // Footer Option
        $controls['footer_bgimage'] = array(
            'title' => esc_html__('Widget Area BG Image','uthr'),
            'section'   => 'footer_settings',
            'control_type'=> 'image',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>15,
        );
        $controls['footer_bgcolor'] = array(
            'title' => esc_html__('Widget Area BG Color','uthr'),
            'section'   => 'footer_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>15,
        );

        $controls['footer_widgets_title_color'] = array(
            'title' => esc_html__('Widget Title Color','uthr'),
            'section'   => 'footer_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>15,
        );
        $controls['footer_widgets_content_color'] = array(
            'title' => esc_html__('Widgets Content Color','uthr'),
            'section'   => 'footer_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>15,
        );
        $controls['footer_widgets_link_hover_color'] = array(
            'title' => esc_html__('Widgets Link Hover Color','uthr'),
            'section'   => 'footer_settings',
            'type'=> 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
            'priority'=>15,
        );        
        $controls['show_ft_left'] = array(
            'title' => esc_html__('Show Footer Left Content','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'checkbox',
            'default'     => true,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>15,
        );
        // Footer left option
        $controls['footer_logo'] = array(
            'title' => esc_html__('Footer Left Logo','uthr'),
            'section'   => 'footer_settings',
            'control_type'=> 'image',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>15,
        );
        $controls['ft_left_content'] = array(
            'title' => esc_html__('Footer Left Content','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'textarea',
            'default'     => '<div class="footer-contact">
                            <div class="footer-contact-list">
                                <span>Our Location</span>
                                <p>869 General Village Apt. 645, Moorebury, USA</p>
                            </div>
                            <div class="footer-contact-list">
                                <span>24/7 hotline:</span>
                                <a href="tel:(+99)0123456789">(+99) 012 347 8910</a>
                            </div>
                        </div>',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,

        );

        // Copyright section

        $controls['footer_copyright_text'] = array(
            'title' => esc_html__('Footer Copyright Text','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'textarea',
            'default'     => sprintf( esc_html__('&copy; %1$s, %2$s','uthr'), date('Y'), esc_html__('Uthr Theme. All Rights Reserved. Built with Hasthemes.','uthr') ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,

        );
        $controls['footer_copyright_bg_color'] = array(
            'title' => esc_html__('Copyright BG Color','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,

        );
        $controls['footer_copyright_color'] = array(
            'title' => esc_html__('Copyright Text Color','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,

        );
         $controls['show_ft_social'] = array(
            'title' => esc_html__('Show Copyright Social Icon','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'checkbox',
            'default'     => true,
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,
        );       
        $controls['footer_social_color'] = array(
            'title' => esc_html__('Social Icon Color','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,

        );
        $controls['footer_social_hover_color'] = array(
            'title' => esc_html__('Social Icon Hover Color','uthr'),
            'section'   => 'footer_settings',
            'type'   => 'color',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,

        );
        $controls['company_facebook_link'] = array(
            'title' => esc_html__('Facebook Link','uthr'),
            'section'   => 'social_settings',
            'type'   => 'text',
            'default'     => esc_html__( '#','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>30,
        );
        $controls['company_instagram_link'] = array(
            'title' => esc_html__('Instagram Link','uthr'),
            'section'   => 'social_settings',
            'type'   => 'text',
            'default'     => esc_html__( '#','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>30,
        );
        $controls['company_twitter_link'] = array(
            'title' => esc_html__('Twitter Link','uthr'),
            'section'   => 'social_settings',
            'type'   => 'text',
            'default'     => esc_html__( '#','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>30,
        );
        $controls['company_youtube_link'] = array(
            'title' => esc_html__('Youtube Link','uthr'),
            'section'   => 'social_settings',
            'type'   => 'text',
            'default'     => esc_html__( '#','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>30,
        );
        $controls['company_pinterest_link'] = array(
            'title' => esc_html__('Pinterest Link','uthr'),
            'section'   => 'social_settings',
            'type'   => 'text',
            'default'     => esc_html__( '#','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>30,
        );

        
        // 404 Page Content

        $controls['error_page_text'] = array(
            'title' => esc_html__('404 Text','uthr'),
            'section'   => 'error_settings',
            'type'   => 'text',
            'default'     => esc_html__( '404','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,
        );
        $controls['error_page_title'] = array(
            'title' => esc_html__('404 Title','uthr'),
            'section'   => 'error_settings',
            'type'   => 'text',
            'default'     => esc_html__( 'PAGE NOT FOUND','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,
        );        
        $controls['error_page_content'] = array(
            'title' => esc_html__('404 Content','uthr'),
            'section'   => 'error_settings',
            'type'   => 'textarea',
            'default'     => esc_html__('The page you are looking for does not exist or has been moved.','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,

        );
        $controls['error_page_btn'] = array(
            'title' => esc_html__('404 Button Text','uthr'),
            'section'   => 'error_settings',
            'type'   => 'text',
            'default'     => esc_html__( 'Go back to Home Page','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,
        ); 


        $controls['shop_banner_title'] = array(
            'title' => esc_html__('Shop Banner Title','uthr'),
            'section'   => 'woo_settings',
            'type'   => 'text',
            'default'     => esc_html__( 'ESSENTIAL <br> WEARS','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,
        ); 

        $controls['shop_banner_content'] = array(
            'title' => esc_html__('Shop Banner Content','uthr'),
            'section'   => 'woo_settings',
            'type'   => 'text',
            'default'     => esc_html__( 'The collections basic items <br> essential for all girls','uthr' ),
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>20,
        ); 
        $controls['shop_banner_image'] = array(
            'title' => esc_html__('Banner Image','uthr'),
            'section'   => 'woo_settings',
            'control_type'=> 'image',
            'transport'   => 'refresh',
            'sanitize_callback' => 'sanitize_input',
            'priority'=>15,
        );








        return $controls;

    }

}

uthr_Customizer::instance();