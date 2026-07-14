<?php 
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	$share_link = get_the_permalink();
	$share_title = get_the_title();

	$thumb_id = get_post_thumbnail_id();
	$thumb_url = wp_get_attachment_image_src( $thumb_id, 'thumbnail-size', true );

	$social_button_list = [
		'facebook' => [
			'title' => esc_html__( 'Facebook', 'quickswish' ),
			'url' 	=> 'https://www.facebook.com/sharer/sharer.php?u='.$share_link,
		],
		'twitter' => [
			'title' => esc_html__( 'Twitter', 'quickswish' ),
			'url' 	=> 'https://twitter.com/share?url=' . $share_link.'&amp;text='.$share_title,
		],
		'pinterest' => [
			'title' => esc_html__( 'Pinterest', 'quickswish' ),
			'url' 	=> 'https://pinterest.com/pin/create/button/?url='.$share_link.'&media='.$thumb_url[0],
		],
		'linkedin' => [
			'title' => esc_html__( 'Linkedin', 'quickswish' ),
			'url' 	=> 'https://www.linkedin.com/shareArticle?mini=true&url='.$share_link.'&amp;title='.$share_title,
		],
		'email' => [
			'title' => esc_html__( 'Email', 'quickswish' ),
			'url' 	=> 'mailto:?subject='.esc_html__('Check%20this%20', 'quickswish') . $share_link,
		],
		'reddit' => [
			'title' => esc_html__( 'Reddit', 'quickswish' ),
			'url' 	=> 'http://reddit.com/submit?url='.$share_link.'&amp;title='.$share_title,
		],
		'telegram' => [
			'title' => esc_html__( 'Telegram', 'quickswish' ),
			'url' 	=> 'https://telegram.me/share/url?url=' . $share_link,
		],
		'odnoklassniki' => [
			'title' => esc_html__( 'Odnoklassniki', 'quickswish' ),
			'url' 	=> 'https://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl=' . $share_link,
		],
		'whatsapp' => [
			'title' => esc_html__( 'WhatsApp', 'quickswish' ),
			'url' 	=> 'https://wa.me/?text=' . $share_link,
		],
		'vk' => [
			'title' => esc_html__( 'VK', 'quickswish' ),
			'url' 	=> 'https://vk.com/share.php?url=' . $share_link,
		],
	];


	$default_buttons = [
        'facebook'   => esc_html__( 'Facebook', 'quickswish' ),
        'twitter'    => esc_html__( 'Twitter', 'quickswish' ),
        'pinterest'  => esc_html__( 'Pinterest', 'quickswish' ),
        'linkedin'   => esc_html__( 'Linkedin', 'quickswish' ),
        'telegram'   => esc_html__( 'Telegram', 'quickswish' ),
    ];
	$button_list = quickswish_get_option( 'social_share_buttons','quickswish_modal_setting_tabs', $default_buttons );
	$button_text = quickswish_get_option( 'social_share_button_title','quickswish_modal_setting_tabs', 'Share:' );

?>

<div class="quickswish-social-share">
	<span class="quickswish-social-title"><?php esc_html_e( $button_text, 'quickswish' ); ?></span>
	<ul>
		<?php
			foreach ( $button_list as $buttonkey => $button ) {
				?>
				<li>
					<a rel="nofollow" href="<?php echo esc_url( $social_button_list[$buttonkey]['url'] ); ?>" <?php echo ( $buttonkey === 'email' ? '' : 'target="_blank"' ) ?>>
						<span class="quickswish-social-icon">
							<?php echo quickswish_icon_list( $buttonkey ); ?>
						</span>
					</a>
				</li>
				<?php
			}
		?>
	</ul>
</div>
