<?php 
/**
 * The template for displaying Search form.
 *
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package uthr
 */

?>
<div class="blog-search">
	<form id="search" action="<?php echo esc_url(home_url( '/' )); ?>" method="GET">
		<input type="text"  name="s"  placeholder="<?php echo esc_attr_x( 'Search Here', 'placeholder', 'uthr' ); ?>" />
		<button type="submit"><i class="icofont-search"></i></button>

    <?php if(class_exists('WooCommerce') && (is_woocommerce() || is_product_tag() || is_cart() || is_checkout() || is_account_page() || is_view_order_page() || is_filtered() ) ): ?>
    	<input type="hidden" name="post_type" value="product" />
    <?php endif; ?>
    
	</form>
</div>