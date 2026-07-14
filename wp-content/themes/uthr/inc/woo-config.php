<?php
    if ( ! class_exists( 'WooCommerce' ) ) return;

global $product;
    // Add this theme support woocommerce
    add_theme_support( 'woocommerce' );

    /*
     * Remove Action
     */
    function uthr_wc_remove_action(){
        // shop page
        remove_action('woocommerce_before_main_content','woocommerce_breadcrumb',20);
        remove_action('woocommerce_sidebar','woocommerce_get_sidebar',10);
         remove_action('woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10);
         remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_rating',5);
         remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_price',10);
         remove_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_add_to_cart',10);

       // single product
         
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_stock', 30 );

    }
    add_action('init','uthr_wc_remove_action');


        // Shop details contAiner

        add_action( 'woocommerce_before_main_content',function(){
                echo '<div class="container">';
        }, 9 );

        add_action( 'woocommerce_after_main_content',function(){
                echo '</div>';
        }, 11 );



        // product notice banner bottom
        add_action( 'woocommerce_before_shop_loop',function(){
                echo '</div>
                <div class=" col-12 uthr-shop-topbar">';

        }, 11);


        if ( function_exists('wishsuite') ) {

            add_action( 'woocommerce_after_add_to_cart_button',function(){
                    echo '<div class="custom-wishbuton">'. do_shortcode('[wishsuite_button]').'</div>';
            }, 11);

            add_action( 'init', function () {
            remove_action( 'woocommerce_single_product_summary', array( \WishSuite\Frontend\Manage_Wishlist::instance(), 'button_print' ), 31 );
            } );

        }
        if ( function_exists('ever_compare') ) {

            add_action( 'init', function () {
            remove_action( 'woocommerce_single_product_summary', array( \EverCompare\Frontend\Manage_Compare::instance(), 'button_print' ), 31 );

            } );

        }


    /*
     * Product shere link
     */
    add_action( 'woocommerce_single_product_summary', 'uthr_woocommerce_single_product_sharing', 41 );


    function uthr_woocommerce_single_product_sharing(){
        $product_title  = get_the_title();
        $product_url    = get_permalink();
        $product_img    = wp_get_attachment_url( get_post_thumbnail_id() );

        $facebook_url   = 'https://www.facebook.com/sharer/sharer.php?u=' . $product_url;
        $twitter_url    = 'http://twitter.com/intent/tweet?status=' . rawurlencode( $product_title ) . '+' . $product_url;
        $pinterest_url  = 'http://pinterest.com/pin/create/bookmarklet/?media=' . $product_img . '&url=' . $product_url . '&is_video=false&description=' . rawurlencode( $product_title );
        $linkedin_url      = 'https://linkedin.com/share?url='. $product_url;
        ?>

          <div class="priduct_social d-flex">
              <span><?php echo esc_html__('SHARE:','uthr'); ?> </span>
              <ul>
                      <li><a class="tweet" href="<?php echo esc_url($twitter_url); ?>"><i class="icofont-twitter"></i></a></li>
                      <li><a class="share" href="<?php echo esc_url($facebook_url); ?>"><i class="icofont-facebook"></i></a></li>
                      <li><a class="google" href="<?php echo esc_url($linkedin_url); ?>"><i class="icofont-linkedin"></i></a></li>
                      <li><a class="pinterest" href="<?php echo esc_url($pinterest_url); ?>"><i class="icofont-pinterest"></i></a></li>
              </ul>
          </div>
        
        <?php
    }

        // prodcut loop limit
        function uthr_custom_number_of_posts() {
            $postsperpage = apply_filters( 'product_custom_limit', 9 );
            return $postsperpage;
        }
        add_filter( 'loop_shop_per_page', 'uthr_custom_number_of_posts' );




    /*
     * Ensure cart contents update when products are added to the cart via AJAX.
     */
    function uthr_wc_add_to_cart_fragment( $fragments ) {
        ob_start();
        ?>
           <span class="mini_cart_count"><?php echo sprintf ( wp_kses_post( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></sapn>
        <?php
        $fragments['span.mini_cart_count'] = ob_get_clean();
        return $fragments;
    }
    add_filter( 'woocommerce_add_to_cart_fragments', 'uthr_wc_add_to_cart_fragment' );



add_action( 'init', function () {
    function uthr_update_option( $section, $option_key, $new_value ){
        $options_data = get_option( $section );

        if( isset( $options_data[$option_key] ) ){
            $options_data[$option_key] = $new_value;
        }else{
            $options_data = array( $option_key => $new_value );
        }
        update_option( $section, $options_data );
    }
    
    uthr_update_option( 'ever_compare_settings_tabs', 'shop_btn_position','use_shortcode' );
    uthr_update_option( 'quickswish_setting_tabs', 'button_position','use_shortcode' );

    } );


