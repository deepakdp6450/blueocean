<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="quickswish-content-area woocommerce single-product">
	<div id="product-<?php the_ID(); ?>" <?php post_class('product'); ?> >
		<?php do_action( 'quickswish_product_image' ); ?>
		<div class="summary entry-summary quickswish-custom-scroll">
			<div class="summary-content">
				<?php do_action( 'quickswish_product_content' ); ?>
			</div>
		</div>
	</div>
</div>