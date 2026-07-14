<?php
namespace QuickSwish\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Admin Page Fields handlers class
 */
class Admin_Fields {

	/**
	 * [$settings_api]
	 * @var [void]
	 */
	private $settings_api;

	/**
	 * [$_instance]
	 * @var null
	 */
	public static $_instance = null;

	/**
	 * [instance] Initializes a singleton instance
	 * @return [Admin_Fields]
	 */
	public static function instance(){
		if( is_null( self:: $_instance ) ){
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * [__construct] Class construck
	 */
	function __construct(){
		$this->settings_api = new Settings_Api();
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	public function admin_init(){

		/**
		 * Set Settings field and section
		 */
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );

		/**
		 * initialize settings
		 */
        $this->settings_api->admin_init();
	}

	/**
	 * [get_settings_sections] option page section register
	 * @return [array] settings section
	 */
	public function get_settings_sections(){

		$sections = array(
			array(
				'id'    => 'quickswish_setting_tabs',
                'title' => esc_html__( 'Button Settings', 'quickswish' ),
			),
			array(
				'id'    => 'quickswish_modal_setting_tabs',
                'title' => esc_html__( 'Popup Settings', 'quickswish' ),
			),
			array(
				'id'	=> 'quickswish_style_tab',
				'title'	=> esc_html__( 'Style Settings', 'quickswish' ),
			)
		);
		return $sections;

	}

	/**
	 * [get_settings_fields] option page fields register
	 * @return [array] settings fields
	 */
	public function get_settings_fields(){
		$settings_fields =	array(

			'quickswish_setting_tabs' => array(

				array(
                    'name'  => 'enable_button',
                    'label'  => esc_html__( 'Enable quick view', 'quickswish' ),
                    'type'  => 'checkbox',
                    'default' => 'on',
                ),

				array(
                    'name'  => 'button_enable_on_mobile',
                    'label'  => esc_html__( 'Enable quick view on mobile', 'quickswish' ),
                    'type'  => 'checkbox',
                    'default' => 'on',
                ),

                array(
                    'name'    => 'button_position',
                    'label'   => esc_html__( 'Button position', 'quickswish' ),
                    'type'    => 'select',
                    'default' => 'before_cart_btn',
                    'options' => [
                        'before_cart_btn' => esc_html__( 'Before Add To Cart', 'quickswish' ),
                        'after_cart_btn'  => esc_html__( 'After Add To Cart', 'quickswish' ),
                        'top_thumbnail'   => esc_html__( 'Top On Image', 'quickswish' ),
                        'use_shortcode'   => esc_html__( 'Use Shortcode', 'quickswish' ),
                    ],
                    'desc' => wp_kses_post('You can use <code>[quickswish_button]</code>'),
                ),

                array(
                    'name'        => 'button_text',
                    'label'       => esc_html__( 'Button text', 'quickswish' ),
                    'desc'        => esc_html__( 'Enter your quickview button text.', 'quickswish' ),
                    'type'        => 'text',
                    'default'     => esc_html__( 'Quick view', 'quickswish' ),
                    'placeholder' => esc_html__( 'Quick view', 'quickswish' ),
                ),

                array(
                    'name'    => 'button_icon_type',
                    'label'   => esc_html__( 'Button icon type', 'quickswish' ),
                    'desc'    => esc_html__( 'Choose an icon type for the quickview button from here.', 'quickswish' ),
                    'type'    => 'select',
                    'default' => 'default',
                    'options' => [
                        'none'     => esc_html__( 'None', 'quickswish' ),
                        'default'  => esc_html__( 'Default', 'quickswish' ),
                        'custom'   => esc_html__( 'Custom', 'quickswish' ),
                    ]
                ),

                array(
                    'name'    => 'button_custom_icon',
                    'label'   => esc_html__( 'Button custom icon', 'quickswish' ),
                    'type'    => 'image_upload',
                    'options' => [
                        'button_label' => esc_html__( 'Upload', 'quickswish' ),   
                        'button_remove_label' => esc_html__( 'Remove', 'quickswish' ),
                    ],
                    'desc'    => esc_html__( 'Upload you custom icon from here.', 'quickswish' ),
                ),

			),

			'quickswish_modal_setting_tabs' => array(

				array(
                    'name'    => 'thumbnail_layout',
                    'label'   => esc_html__( 'Thumbnail layout', 'quickswish' ),
                    'desc'    => esc_html__( 'Choose a thumbnail layout from here.', 'quickswish' ),
                    'type'    => 'select',
                    'default' => 'theme',
                    'options' => [
                        'slider'	 => esc_html__( 'Slider', 'quickswish' ),
                        'singleimage' => esc_html__( 'Single Image', 'quickswish' ),
                        'theme' 	 => esc_html__( 'Theme', 'quickswish' ),
                    ]
                ),

                array(
                    'name'  => 'active_thubnail_border_color',
                    'label' => esc_html__( 'Active thumbnail border color', 'quickswish' ),
                    'desc'  => wp_kses_post( 'Set the border color of the current thumbnail slider image.', 'quickswish' ),
                    'type'  => 'color',
                    'class' => 'depend_thumbnail_layout_slider',
                ),

                array(
                    'name'  => 'slider_arrow_color',
                    'label' => esc_html__( 'Slider arrow color', 'quickswish' ),
                    'desc'  => wp_kses_post( 'Set the color of the thumbnail slider arrow button.', 'quickswish' ),
                    'type'  => 'color',
                    'class' => 'depend_thumbnail_layout_slider',
                ),

                array(
                    'name'  => 'slider_arrow_bg_color',
                    'label' => esc_html__( 'Slider arrow background', 'quickswish' ),
                    'desc'  => wp_kses_post( 'Set the background color of the thumbnail slider arrow button.', 'quickswish' ),
                    'type'  => 'color',
                    'class' => 'depend_thumbnail_layout_slider',
                ),

                array(
                    'name'  => 'slider_arrow_hover_color',
                    'label' => esc_html__( 'Slider arrow hover color', 'quickswish' ),
                    'desc'  => wp_kses_post( 'Set the hover color of the thumbnail slider arrow button.', 'quickswish' ),
                    'type'  => 'color',
                    'class' => 'depend_thumbnail_layout_slider',
                ),

                array(
                    'name'  => 'slider_arrow_hover_bg_color',
                    'label' => esc_html__( 'Slider arrow hover background', 'quickswish' ),
                    'desc'  => wp_kses_post( 'Set the hover background color of the thumbnail slider arrow button.', 'quickswish' ),
                    'type'  => 'color',
                    'class' => 'depend_thumbnail_layout_slider',
                ),

                array(
                    'name'    => 'content_section_area_title',
                    'headding'=> esc_html__( 'Content', 'quickswish' ),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'element_section_title_area',
                ),

                array(
                    'name' => 'select_content_to_show',
                    'label' => esc_html__('Select content to show', 'quickswish'),
                    'desc'    => esc_html__( 'Choose which content should be presented on the popup window.', 'quickswish' ),
                    'type' => 'multicheckshort',
                    'options' => [
                    	'title'         => esc_html__( 'Title', 'quickswish' ),
				        'rating'        => esc_html__( 'Rating', 'quickswish' ),
				        'price'         => esc_html__( 'Price', 'quickswish' ),
				        'excerpt'       => esc_html__( 'Excerpt', 'quickswish' ),
				        'add_to_cart'   => esc_html__( 'Add to cart', 'quickswish' ),
				        'meta'          => esc_html__( 'Meta', 'quickswish' ),
                    ],
                    'default' => [
                        'title'   		=> esc_html__( 'Title', 'quickswish' ),
                        'rating'    	=> esc_html__( 'Rating', 'quickswish' ),
                        'price'  		=> esc_html__( 'Price', 'quickswish' ),
                        'excerpt'   	=> esc_html__( 'Excerpt', 'quickswish' ),
                        'add_to_cart'   => esc_html__( 'Add to cart', 'quickswish' ),
                        'meta'   		=> esc_html__( 'Meta', 'quickswish' ),
                    ],
                ),

                array(
                    'name'  => 'enable_ajax_cart',
                    'label'  => esc_html__( 'Enable ajax add to cart', 'quickswish' ),
                    'type'  => 'checkbox',
                    'default' => 'on',
                    'desc'    => esc_html__( 'Enable this to activate AJAX add to cart feature in the popup window.', 'quickswish' ),
                ),

                array(
                    'name'    => 'social_share_button_area_title',
                    'headding'=> esc_html__( 'Social share button', 'quickswish' ),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'element_section_title_area',
                ),

                array(
                    'name'  => 'enable_social_share',
                    'label'  => esc_html__( 'Enable social share button', 'quickswish' ),
                    'type'  => 'checkbox',
                    'default' => 'on',
                    'desc'    => esc_html__( 'Enable social share button.', 'quickswish' ),
                ),

                array(
                    'name'    => 'social_share_display_from',
                    'label'   => esc_html__( 'Social share button display from', 'quickswish' ),
                    'desc'    => esc_html__( 'If you choose default this button comes from QuickSwish otherwise display from your theme WooCommerce hook.', 'quickswish' ),
                    'type'    => 'select',
                    'default' => 'custom',
                    'options' => [
                        'custom'=> esc_html__( 'Custom', 'quickswish' ),
                        'theme'  => esc_html__( 'Theme', 'quickswish' ),
                    ],
                    'class' => 'depend_social_share_enable'
                ),

                array(
                    'name'        => 'social_share_button_title',
                    'label'       => esc_html__( 'Social share button title', 'quickswish' ),
                    'desc'        => esc_html__( 'Enter your social share button title.', 'quickswish' ),
                    'type'        => 'text',
                    'default'     => esc_html__( 'Share:', 'quickswish' ),
                    'placeholder' => esc_html__( 'Share', 'quickswish' ),
                    'class' => 'depend_social_share_enable'
                ),

                array(
                    'name' => 'social_share_buttons',
                    'label' => esc_html__('Enable share buttons', 'quickswish'),
                    'desc'    => esc_html__( 'You can manage your social share buttons.', 'quickswish' ),
                    'type' => 'multicheckshort',
                    'options' => [
                    	'facebook'      => esc_html__( 'Facebook', 'quickswish' ),
                        'twitter'       => esc_html__( 'Twitter', 'quickswish' ),
                        'pinterest'     => esc_html__( 'Pinterest', 'quickswish' ),
                        'linkedin'      => esc_html__( 'Linkedin', 'quickswish' ),
                        'email'      	=> esc_html__( 'Email', 'quickswish' ),
                        'reddit'   		=> esc_html__( 'Reddit', 'quickswish' ),
                        'telegram'   	=> esc_html__( 'Telegram', 'quickswish' ),
                        'odnoklassniki' => esc_html__( 'Odnoklassniki', 'quickswish' ),
                        'whatsapp'   	=> esc_html__( 'WhatsApp', 'quickswish' ),
                        'vk'   			=> esc_html__( 'VK', 'quickswish' ),
                    ],
                    'default' => [
                        'facebook'   => esc_html__( 'Facebook', 'quickswish' ),
                        'twitter'    => esc_html__( 'Twitter', 'quickswish' ),
                        'pinterest'  => esc_html__( 'Pinterest', 'quickswish' ),
                        'linkedin'   => esc_html__( 'Linkedin', 'quickswish' ),
                        'telegram'   => esc_html__( 'Telegram', 'quickswish' ),
                    ],
                    'class' => 'depend_social_share_enable'
                ),

			),

			'quickswish_style_tab' => array(

				array(
                    'name'    => 'button_style',
                    'label'   => esc_html__( 'Button style', 'quickswish' ),
                    'desc'    => esc_html__( 'Choose a style for the quickview button from here.', 'quickswish' ),
                    'type'    => 'select',
                    'default' => 'default',
                    'options' => [
                        'default'	=> esc_html__( 'Default', 'quickswish' ),
                        'theme'		=> esc_html__( 'Theme', 'quickswish' ),
                        'custom'	=> esc_html__( 'Custom', 'quickswish' ),
                    ]
                ),

                array(
                    'name'    => 'popup_style',
                    'label'   => esc_html__( 'Popup window style', 'quickswish' ),
                    'desc'    => esc_html__( 'Choose a popup window style type from here.', 'quickswish' ),
                    'type'    => 'select',
                    'default' => 'default',
                    'options' => [
                        'default'	=> esc_html__( 'Default', 'quickswish' ),
                        'custom'	=> esc_html__( 'Custom', 'quickswish' ),
                    ]
                ),

                array(
                    'name'    => 'button_custom_style_area_title',
                    'headding'=> esc_html__( 'Button custom style', 'quickswish' ),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'element_section_title_area depend_button_custom_style',
                ),

                array(
                    'name'  => 'button_color',
                    'label' => esc_html__( 'Color', 'quickswish' ),
                    'desc'  => wp_kses_post( 'Set the color of the button.', 'quickswish' ),
                    'type'  => 'color',
                    'class' => 'depend_button_custom_style',
                ),

                array(
                    'name'  => 'button_hover_color',
                    'label' => esc_html__( 'Hover Color', 'quickswish' ),
                    'desc'  => wp_kses_post( 'Set the hover color of the button.', 'quickswish' ),
                    'type'  => 'color',
                    'class' => 'depend_button_custom_style',
                ),

                array(
                    'name'  => 'background_color',
                    'label' => esc_html__( 'Background Color', 'quickswish' ),
                    'desc'  => wp_kses_post( 'Set the background color of the button.', 'quickswish' ),
                    'type'  => 'color',
                    'class' => 'depend_button_custom_style',
                ),

                array(
                    'name'  => 'hover_background_color',
                    'label' => esc_html__( 'Hover Background Color', 'quickswish' ),
                    'desc'  => wp_kses_post( 'Set the hover background color of the button.', 'quickswish' ),
                    'type'  => 'color',
                    'class' => 'depend_button_custom_style',
                ),

                array(
                    'name'    => 'button_custom_padding',
                    'label'   => __( 'Padding', 'quickswish' ),
                    'type'    => 'dimensions',
                    'options' => [
                        'top'   => esc_html__( 'Top', 'quickswish' ),   
                        'right' => esc_html__( 'Right', 'quickswish' ),   
                        'bottom'=> esc_html__( 'Bottom', 'quickswish' ),   
                        'left'  => esc_html__( 'Left', 'quickswish' ),
                        'unit'  => esc_html__( 'Unit', 'quickswish' ),
                    ],
                    'class' => 'depend_button_custom_style',
                ),

                array(
                    'name'    => 'button_custom_margin',
                    'label'   => __( 'Margin', 'quickswish' ),
                    'type'    => 'dimensions',
                    'options' => [
                        'top'   => esc_html__( 'Top', 'quickswish' ),   
                        'right' => esc_html__( 'Right', 'quickswish' ),   
                        'bottom'=> esc_html__( 'Bottom', 'quickswish' ),   
                        'left'  => esc_html__( 'Left', 'quickswish' ),
                        'unit'  => esc_html__( 'Unit', 'quickswish' ),
                    ],
                    'class' => 'depend_button_custom_style',
                ),

                array(
                    'name'    => 'button_custom_border',
                    'label'   => __( 'Border width', 'quickswish' ),
                    'type'    => 'dimensions',
                    'options' => [
                        'top'   => esc_html__( 'Top', 'quickswish' ),   
                        'right' => esc_html__( 'Right', 'quickswish' ),   
                        'bottom'=> esc_html__( 'Bottom', 'quickswish' ),   
                        'left'  => esc_html__( 'Left', 'quickswish' ),
                        'unit'  => esc_html__( 'Unit', 'quickswish' ),
                    ],
                    'class' => 'depend_button_custom_style',
                ),
                array(
                    'name'  => 'button_custom_border_color',
                    'label' => esc_html__( 'Border Color', 'quickswish' ),
                    'desc'  => wp_kses_post( 'Set the button color of the button.', 'quickswish' ),
                    'type'  => 'color',
                    'class' => 'depend_button_custom_style',
                ),

                array(
                    'name'    => 'button_custom_border_radius',
                    'label'   => __( 'Border Radius', 'quickswish' ),
                    'type'    => 'dimensions',
                    'options' => [
                        'top'   => esc_html__( 'Top', 'quickswish' ),   
                        'right' => esc_html__( 'Right', 'quickswish' ),   
                        'bottom'=> esc_html__( 'Bottom', 'quickswish' ),   
                        'left'  => esc_html__( 'Left', 'quickswish' ),
                        'unit'  => esc_html__( 'Unit', 'quickswish' ),
                    ],
                    'class' => 'depend_button_custom_style',
                ),


                array(
                    'name'    => 'popup_custom_style_area_title',
                    'headding'=> esc_html__( 'Popup window custom style', 'quickswish' ),
                    'type'    => 'title',
                    'size'    => 'margin_0 regular',
                    'class' => 'element_section_title_area popup_custom_style',
                ),

                array(
                    'name'    => 'max_width',
                    'label'   => esc_html__( 'Max Width', 'quickswish' ),
                    'desc'    => esc_html__( 'You can manage popup window width from here.', 'quickswish' ),
                    'type'    => 'number',
                    'min'              => 1,
                    'max'              => 1500,
                    'step'             => 1,
                    'default'          => 1200,
                    'sanitize_callback' => 'floatval',
                    'class' => 'popup_custom_style',
                ),

			),

		);
		return $settings_fields;
	}

	/**
	 * [options_page] Render option page
	 * @return [void]
	 */
	public function options_page(){
		echo '<div class="wrap">';
            echo '<h2>'.esc_html__( 'QuickSwish Settings','quickswish' ).'</h2>';
            $this->save_message();
            $this->settings_api->show_navigation();
            $this->settings_api->show_forms();
        echo '</div>';
	}

	/**
	 * [save_message] option save message
	 * @return [void]
	 */
	public function save_message() {
        if( isset( $_GET['settings-updated'] ) ) {
            ?>
                <div class="updated notice is-dismissible"> 
                    <p><strong><?php esc_html_e('Successfully Settings Saved.', 'quickswish') ?></strong></p>
                </div>
            <?php
        }
    }


}