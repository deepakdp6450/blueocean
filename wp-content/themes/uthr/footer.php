<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package uthr
 */
?>
        <?php  

           $show_footer_left = get_option( 'uthr_show_ft_left', true );
           $footer_logo = get_option( 'uthr_footer_logo', false);
           $show_footer_widget_area = get_option( 'uthr_show_footer_widget_area', false);
           $show_ft_left_social = get_option( 'uthr_show_ft_left_social',false );
           $show_ft_social = get_option( 'uthr_show_ft_social',true );
           $ft_left_content = get_option( 'uthr-ft-left-content','<div class="footer-contact">
                            <div class="footer-contact-list">
                                <span>Our Location</span>
                                <p>869 General Village Apt. 645, Moorebury, USA</p>
                            </div>
                            <div class="footer-contact-list">
                                <span>24/7 hotline:</span>
                                <a href="tel:(+99)0123456789">(+99) 012 347 8910</a>
                            </div>
                        </div>' );
            $show_footer_newsletter = get_option( 'uthr_show_footer_newsletter',true );
            $newsletter_title = get_option( 'uthr_newsletter_title','keep connected' );
            $newsletter_content = get_option( 'uthr_newsletter_content','Get updates by subscribing to our weekly newsletter' );
            $newsletter_shortcode = get_option( 'uthr_newsletter_shortcode','' );
           ?>

        <div class="uthr-footer">
        <?php if( ( is_active_sidebar( 'sidebar-2') || is_active_sidebar( 'sidebar-3') || is_active_sidebar( 'sidebar-4')|| is_active_sidebar( 'sidebar-5')) && $show_footer_widget_area ==true ){

         ?>
            <!-- Footer Top/Widget Area Start -->
            <div class="uthr-footer-top" <?php if(!empty(get_option( 'uthr_footer_bgimage' ))){ ?> data-bg-image="<?php echo esc_url( get_option( 'uthr_footer_bgimage' )); ?>"<?php } if(!empty(get_option( 'uthr_footer_bgcolor' ))){ ?>  data-bg-color="<?php echo esc_url( get_option( 'uthr_footer_bgcolor' )); ?>" <?php } ?> >

                <div class="container">
                    <div class="row row-cols-sm-2 row-cols-1">
                         <?php 
                        $count = 0;
                        if( is_active_sidebar( 'sidebar-2' ) ){
                            $count++;
                     } if( is_active_sidebar( 'sidebar-3' ) ){
                        $count++;
                        } if( is_active_sidebar( 'sidebar-4' ) ){ 
                            $count++;
                        } if( is_active_sidebar( 'sidebar-5' ) ){
                            $count++;
                        }
                        $colum = 12/$count;
                        for($i = 1, $j=2; $i<=$count; $i++){
                            ?>
                            <div class="col-lg-<?php echo esc_attr($colum); ?>">
                            <?php
                            $sidebar = 'sidebar-'.$j;
                            dynamic_sidebar( $sidebar );
                            $j++;
                        ?>
                            </div>
                        <?php    
                        }

                      ?>
                    </div>

                </div>
            </div>    
            <!-- Footer Top/Widget Area End -->
            <?php } ?>

            <!-- Footer Mailchimp section -->
            <?php  if( $show_footer_newsletter == true && ( !empty( $newsletter_title ) || !empty( $newsletter_content )  ) ): ?>
            <div class="uthr-newsletter-section">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="uthr-newsletter-inner d-flex justify-content-between align-items-center">
                                <div class="uthr-newsletter-text">
                                    <?php  if( !empty( $newsletter_title ) ){ ?>
                                    <h2> <?php echo esc_html( $newsletter_title,'uthr' ); ?></h2>
                                    <?php } ?>
                                     <?php  if( !empty( $newsletter_content ) ){ ?>

                                    <p><?php echo esc_html( $newsletter_content,'uthr' ); ?></p>

                                    <?php } ?>
                                </div>
                                <?php if( $newsletter_shortcode !='' ): ?>
                                <div class="newsletter_subscribe">
                                    <?php echo do_shortcode( $newsletter_shortcode ); ?>
                                </div>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
            <?php endif; ?>
            <!-- Footer Bottom Start -->
            <div class="uthr-footer-bottom" <?php if(!empty( get_option( 'uthr_footer_copyright_bg_color' ))){ ?>  data-bg-color="<?php echo esc_url( get_option( 'uthr_footer_copyright_bg_color' )); ?>" <?php } ?>>
                <div class="container">
                    <div class="row">
                        <?php if($show_footer_left == true ){ ?>
                        <div class="col-md-5 col-12">
                            <div class="footer-left-content">
                                <?php if( !empty( $footer_logo )): ?>
                                <div class="footer-logo">
                                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>l"><img src="<?php echo esc_url( $footer_logo ); ?>" alt="<?php echo esc_attr('uthr');?>"></a>
                                </div>
                            <?php  endif; ?>
                                <?php if ( $ft_left_content !='' ) { ?>

                                    <div class="uther-footer-left-content">
                                        <?php echo wp_kses_post( $ft_left_content );?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                <?php } ?>
                        <div class="<?php if( $show_footer_left == true ){ echo "col-md-7"; }else{ echo "uthr-ftr"; } ?> col-12">
                            <?php if ( has_nav_menu('copyright-menu') ): ?>
                            <div class="uthr-footer-menu">
                                <?php 
                                    wp_nav_menu( array(
                                        'theme_location' => 'copyright-menu',
                                    ));
                                ?>
                             </div> 
                            <?php endif; ?>

                            <?php if ( $show_ft_social == true ) { echo uther_social(); } ?>

                            <?php
                                if ( !empty( get_option( 'uthr_footer_copyright_text' ) )  ) { echo '<p class="uthr-copyright"> '. wp_kses_post( get_option( 'uthr_footer_copyright_text' ) ).'</p>';

                            } else { echo '<p class="uthr-copyright">'; esc_html__('Copyright', 'uthr'); ?> &copy; <?php echo date("Y").' '.get_bloginfo('name');  esc_html__('. All Rights Reserved.', 'uthr' ); echo '</p>'; }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Bottom End -->
        </div>
    </div><!-- #content -->
</div><!-- #page -->
    <!-- Search Popup -->
    <div class="search-popup">
        <button class="close-search style-two"><span class="icofont-brand-nexus"></span></button>
        <button class="close-search"><span class="icofont-arrow-up"></span></button>

        <form id="search" action="<?php echo esc_url(home_url( '/' )); ?>" method="GET">
            <div class="form-group">
                <input type="text" name="s" class="input-text" placeholder="<?php echo esc_attr_x( 'Search...', 'placeholder', 'uthr' ); ?>" required="" />

   <?php if(class_exists('WooCommerce') && (is_woocommerce() || is_product_tag() || is_cart() || is_checkout() || is_account_page() || is_view_order_page() || is_filtered() ) ){ ?>
        <input type="hidden" name="post_type" value="product" />
    <?php }else{ ?>
        
            <input type="hidden" name="post_type" value="post" />

    <?php } ?>
                <button><i class="icon icofont-search"></i></button>
            </div>
        </form>
    </div>
    <!-- End Header Search -->
<?php wp_footer(); ?>

</body>
</html>
