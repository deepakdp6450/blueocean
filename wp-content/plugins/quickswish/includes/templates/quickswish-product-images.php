<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_image_ids();

$attachment_count = count( $attachment_ids );

$thumbnail_layout = quickswish_get_option( 'thumbnail_layout', 'quickswish_modal_setting_tabs', 'theme' );

?>
<div class="images">
	
	<div class="woocommerce-product-gallery__wrapper <?php echo ( 'slider' === $thumbnail_layout ? 'quickswish-main-image-slider' : '' ); ?>">
		<?php
			$attributes = array(
				'title' => esc_attr( get_the_title( get_post_thumbnail_id() ) )
			);

			if ( has_post_thumbnail() ) {

				echo '<figure class="woocommerce-product-gallery__image">' . get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'woocommerce_single' ), $attributes ) . '</figure>';


				if ( $attachment_count > 0 && 'slider' === $thumbnail_layout ) {
					foreach ( $attachment_ids as $attachment_id ) {
						echo '<div class="quickswish-product-image-wrap"><figure class="woocommerce-product-gallery__image">' . wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'woocommerce_single' ) ) . '</figure></div>';
					}
				}

			} else {
				echo '<figure class="woocommerce-product-gallery__image--placeholder">' . apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'quickswish' ) ), $post->ID ) . '</figure>';
			}
		?>
	</div>

	<?php if( 'slider' === $thumbnail_layout ): ?>
		<div class="quickswish-thumbnail-slider">
	        <?php
	            if( has_post_thumbnail() ){

	                echo '<div class="quickswish-thumb-single">' . get_the_post_thumbnail( $post->ID, apply_filters( 'woocommerce_gallery_thumbnail', 'woocommerce_single' ), $attributes ) . '</div>';

	                if ( $attachment_count > 0 ) {
						foreach ( $attachment_ids as $attachment_id ) {
	                        $thumbnail_src = wp_get_attachment_image_src( $attachment_id, 'woocommerce_single' );
	                        echo '<div class="quickswish-thumb-single"><img src=" '.$thumbnail_src[0].' " alt="'.get_the_title().'"></div>';
						}
					}

				}else{
					echo '<div class="woocommerce-product-gallery__image--placeholder">' . apply_filters( 'woocommerce_gallery_thumbnail', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'quickswish' ) ), $post->ID ) . '</div>';
				}

	        ?>
	    </div>
	<?php endif; ?>

</div>
