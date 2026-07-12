<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

?>

<div class="shop-page-area">
	<div class="container">
		<div class="row  flex-row-reverse">	

			 <!-- product area column start -->
				<div class=" <?php if(is_active_sidebar('sidebar-shop')){ echo esc_attr(" col-lg-9 shop-main-column-right"); }else{echo esc_attr( "col-12" ); } ?>">
							<?php




						if ( woocommerce_product_loop() ) {


					$shop_banner_title = get_option( 'uthr_shop_banner_title', esc_html__( 'ESSENTIAL <br> WEARS','uthr' ));
					$shop_banner_content = get_option( 'uthr_shop_banner_content', esc_html__( 'The collections basic items <br> essential for all girls','uthr' ));
							?>

					<div class="shop_banner d-flex align-items-center"  <?php if(!empty(get_option( 'uthr_shop_banner_image' ))){ ?> data-bg-image="<?php echo esc_url( get_option( 'uthr_shop_banner_image' )); ?>"<?php }?> >
                        <div class="shop_banner_text">
                        	<?php
                            if ( !empty( $shop_banner_title )  ) {
                                
                             echo "<h2>". wp_kses_post( $shop_banner_title)."</h2>" ;
                            }
                            if ( !empty( $shop_banner_content )  ) {
                                
                             echo "<p>". wp_kses_post( $shop_banner_content)."</p>" ;
                            }
                            ?>
                        </div>
                    </div>
					<div class="row">

						<div class="col-12">
						<?php
							/**
						 * Hook: woocommerce_before_shop_loop.
						 *
						 * @hooked woocommerce_output_all_notices - 10
						 * @hooked woocommerce_result_count - 20
						 * @hooked woocommerce_catalog_ordering - 30
						 */
						do_action( 'woocommerce_before_shop_loop' );

						?>


						   <div class="toolbar_btn_wrapper">
                            <div class="view_btn">
                                <a class="view" href="#">VIEW</a>
                            </div>
                            <div class="shop_toolbar_btn">
                                <ul class="d-flex align-items-center">
                                    <li><a href="#" class="active btn-grid-3" data-role="grid_3"><i class="fa fa-th"></i></a></li>

                                    <li><a href="#" class="btn-list" data-role="grid_list"><i class="fa fa-th-list"></i></a></li>
                                </ul>
                            </div>
                        </div>
						</div>
					</div>

					<div class="row shop_wrapper">
						<?php
						//woocommerce_product_loop_start();


						if ( wc_get_loop_prop( 'total' ) ) {
							while ( have_posts() ) {
								the_post();

								/**
								 * Hook: woocommerce_shop_loop.
								 */
								do_action( 'woocommerce_shop_loop' );

								wc_get_template_part( 'content', 'product' );
							}
						}

							//woocommerce_product_loop_end();

							?> 
							<div class="col-12 uthr-shop-pagination ">
								<?php

							/**
							 * Hook: woocommerce_after_shop_loop.
							 *
							 * @hooked woocommerce_pagination - 10
							 */
							do_action( 'woocommerce_after_shop_loop' );

							?>
							</div>
							<?php
						} else {
							/**
							 * Hook: woocommerce_no_products_found.
							 *
							 * @hooked wc_no_products_found - 10
							 */
							do_action( 'woocommerce_no_products_found' );
						}
						?>
					</div>
			</div>
			<!-- product area column End -->
		<?php if(is_active_sidebar('sidebar-shop')){ ?>

			<div class="col-lg-3 shop-main-sidebar">
				<?php
				dynamic_sidebar( 'sidebar-shop' );
				?>
			</div>
		<?php } ?>
		</div>
	</div>
</div>

<?php

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );



get_footer( 'shop' );
