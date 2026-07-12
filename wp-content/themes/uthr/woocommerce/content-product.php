<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

if (is_single()){
	$pro_column = 3;
}else{

	if(is_active_sidebar('sidebar-shop')){
	$pro_column = 4;
	}else{
		$pro_column = 3;
	}
}

?>
<div <?php wc_product_class( "col-lg-{$pro_column} col-sm-6 col-12 ", $product ); ?>>

    <div class="single-product-box">
        <div class="product-thumb">
            <a href="<?php the_permalink();?>">
                <?php
                    /**
                     * Hook: woocommerce_before_shop_loop_item_title.
                     *
                     * @hooked woocommerce_show_product_loop_sale_flash - 10
                     * @hooked woocommerce_template_loop_product_thumbnail - 10
                     */
                    do_action( 'woocommerce_before_shop_loop_item_title' );
                ?>
                </a>
                <div class="product_action">
                    <ul>
                        <?php                        
                        if ( function_exists( 'WishSuite' ) ) { ?>
                        <li class="wishlist">
                            <?php echo do_shortcode('[wishsuite_button]');?>
                        </li>
                       <?php } ?>
                       
                        <?php
                        if ( function_exists('QuickSwish') ) { ?>
                        <li class="quick_view"><?php echo do_shortcode('[quickswish_button]');?></li>
                       <?php } ?>

                        <?php
                        if ( function_exists('ever_compare') ) { ?>
                       <li class="compare"><?php echo do_shortcode('[evercompare_button]');?></li>
                       <?php } ?>

                        
                    </ul>
                </div>
        </div>
        <div class="product-content">
        	<div class="pro-rating">
        		<?php woocommerce_template_loop_rating(); ?>
        	</div>
            <h4><a href="<?php the_permalink( ) ?>"><?php the_title(); ?></a></h4>
            <div class="price-box"> 
                <?php woocommerce_template_loop_price(); ?>
            </div>
            <div class="pro-button"> 
                <?php woocommerce_template_loop_add_to_cart(); ?>
            </div>
        </div>

            <div class="product_list_content">
                <h4 class="product_name"><a href="<?php the_permalink( ) ?>"><?php the_title(); ?></a></h4>
                <div class="price_box"> 
                    <?php woocommerce_template_loop_price(); ?>
                </div>
                <div class="product_desc">
                    <p><?php woocommerce_template_single_excerpt(); ?></p>
                </div>
                <div class="add-to-cart">
                    <?php woocommerce_template_loop_add_to_cart(); ?>
                        
                </div>
            </div>
    </div>


	<?php

	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</div>
