<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

    <div class="uthr-header-mini-cart">
                   
    	<?php if ( ! WC()->cart->is_empty() ) : ?>
        <div class="cart_gallery">

<?php
			do_action( 'woocommerce_before_mini_cart_contents' ); 

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
					$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
					$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>

            <div class="uthr-header-mini-cart-item  <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?> ">

                <div class="cart_img">
                    <?php if ( empty( $product_permalink ) ) : ?>
						<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
					<?php else : ?>
						<a href="<?php echo esc_url( $product_permalink ); ?>" class="image">
							<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
						</a>
					<?php endif; ?>
                </div>

                <div class="cart_info">
                    <a href="<?php echo esc_url( $product_permalink ); ?>"> <?php echo esc_attr( $product_name );?></a>
                    <p><span><?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?></span></p>
                </div>
                <div class="cart_remove">                
                    <?php
						echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
							'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><i class="icofont-close-line"></i></a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							__( 'Remove this item', 'uthr' ),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() )
						), $cart_item_key );
					?>
					<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                </div>
            </div>
				<?php
				}
			}
			do_action( 'woocommerce_mini_cart_contents' );
		?>
        </div>
        <div class="uthr-header-mini-cart_table">
            <div class="cart_table_border">
                <div class="cart_total">
				<?php echo ( '<span>Sub total:</span>'); ?> <span class="price"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                </div>
                <div class="cart_total  mt-10">
				<?php echo ( '<span>Total:</span>'); ?> <span class="price"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                </div>
            </div>
        </div>
        <div class="uthr-header-mini-cart_footer">
            <div class="cart_button">
                <a href="<?php echo get_permalink( wc_get_page_id( 'cart' ) ); ?>"> <i class="icofont-cart"></i><?php _e( 'View cart', 'uthr' ); ?></a>
            </div>
            <div class="cart_button">
                <a href="<?php echo get_permalink( wc_get_page_id( 'checkout' ) ); ?>"> <i class="icofont-close-line"></i> <?php _e( 'Checkout', 'uthr' ); ?></a>
            </div>
        </div>

        <?php else : ?>

         <div class="cart_gallery">
        	<p class="woocommerce-mini-cart__empty-message"><?php _e( 'No products in the cart.', 'uthr' ); ?></p>
        </div>
        <?php endif; ?>

    </div>

    <!--mini cart end-->
    <?php do_action( 'woocommerce_after_mini_cart' ); ?>