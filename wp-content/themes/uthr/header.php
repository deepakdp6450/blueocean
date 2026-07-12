<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package uthr
 */
global $woocommerce;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

<div id="page" class="site">
        <!--== Start Preloader Content ==-->
    <div class="preloader-wrap">
      <div class="preloader">
        <span class="dot"></span>
        <div class="dots">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    </div>
    <!--== End Preloader Content ==--> 
<?php 

$userlogin_show_hide = get_option( 'uthr_userlogin_show_hide', true);
$user_search_show_hide = get_option( 'uthr_user_search_show_hide', true);
$cart_show_hide = get_option( 'uthr_cart_show_hide', true);
$wishlist_show_hide = get_option( 'uthr_wishlist_show_hide', true);
$header_top_show_hide = get_option( 'uthr_header_top_show_hide', true);
$header_top_left_info1 = get_option( 'uthr_header_top_left_info1', '0123 4567 8910');
$header_top_left_info1_number = str_replace(' ','',$header_top_left_info1 );
$header_top_left_info2 = get_option( 'uthr_header_top_left_info2', 'uthrstore@domain.com');
$header_top_middle_info = get_option( 'uthr_header_top_middle_info', 'Free shipping worldwide for orders over $99');
$header_top_right_show_hide = get_option( 'uthr_header_top_right_show_hide', true );
$header_top_right_follow_us = get_option( 'uthr_header_top_right_follow_us', 'Follow us' );

?>

<?php if( $header_top_show_hide == true ): ?>

<!-- Header top -->

<div class="uthr-header-top">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="uthr-header-top-inner d-flex justify-content-between align-items-center">
                    <?php if( !empty( $header_top_left_info1 )  || !empty( $header_top_left_info2 )){ ?>
                    <div class="uthr-header-contact-info">
                        <ul class="d-flex">
                            <?php if( !empty( $header_top_left_info1 ) ){ ?>
                            <li> 
                                <i class="icon-user icons"></i> 
                                <a href=" <?php if( is_numeric($header_top_left_info1_number) ){ ?>  tel:<?php echo esc_attr($header_top_left_info1_number ); }else{ echo "#"; } ?>">
                                <?php echo wp_kses_post( $header_top_left_info1 )?></a>
                            </li>
                            <?php } ?>
                            <?php if( !empty( $header_top_left_info2 ) ){ ?>
                            <li> 
                                <i class="icon-envelope-letter icons"></i> 
                                <a href="#"><?php echo wp_kses_post($header_top_left_info2 )?></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php } ?>

                    <?php if( !empty( $header_top_middle_info ) ){ ?>
                    <div class="uthr-free-shipping-text">
                        <p><?php echo wp_kses_post($header_top_middle_info )?></p>
                    </div>
                    <?php } ?>
                    <?php if( !empty( $header_top_right_show_hide ) ){ ?>
                    <div class="uthr-header-top-sidebar d-flex align-items-center">
                        <div class="uthr-header-social d-flex">
                            <?php if(!empty( $header_top_right_follow_us )){ ?>
                            <span><?php  echo wp_kses_post( $header_top_right_follow_us );?></span>
                            <?php } ?>
                            <?php  echo uther_social('d-flex');?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
<!-- Header top end -->
    <div class="uthr-header">
        <div class="uthr-header-inner">
            <div class="container-fluid">
                <div class="row align-items-center">

                    <!-- Logo Start -->
                    <div class="col-lg-2 col-sm-5 col-5">
                        <div class="uthr-logo">
                            <?php
                                if( has_custom_logo() ){
                                    the_custom_logo(); 
                                }else {
                                ?>
                                    <h3 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h3>
                                <?php
                                 $description = get_bloginfo( 'description', 'display' );
                                    if ( $description || is_customize_preview() ) { ?> 
                                        <p class="site-description"><?php echo esc_html( $description ); ?> </p> 
                            <?php } 
                            }
          
                            ?>
                        </div>
                    </div>
                    <!-- Logo End -->

                    <!-- Menu & Button Start -->
                    <div class="col-lg-10 col-sm-7 col-7">
                        <div class="row align-items-center">
                            <div class="col position-static d-none d-lg-block">
                                <nav class="uthr-menu <?php if( ( $userlogin_show_hide != true || has_nav_menu('login-menu') !=true ) && ( $cart_show_hide != true || class_exists('WooCommerce') !=true )  && $user_search_show_hide != true ){ echo" uthr-menu-right "; }  ?>" >
                                    <?php
                                        if ( has_nav_menu('main-menu') ) {
                                        wp_nav_menu( array(
                                        'theme_location' => 'main-menu',
                                        'container' => false,
                                        'menu_class' => 'uthr-nav',
                                        'walker' => new uthr_Walker_Nav_Menu(),
                                        'device' => 'desktop'
                                        ) );
                                        }
                                    ?>
                                </nav>
                            </div>

                        <?php if( !empty(get_option( 'uthr_pre_button_text' ))): ?>
                            <div class="col-auto d-none d-sm-block">
                                <?php echo uthr_button( 'uthr_pre_button_text','uthr_pre_button_link' ); ?>
                            </div>
                        <?php endif; ?>
                    
                   
                    <?php if( $userlogin_show_hide == true || $cart_show_hide == true ||$user_search_show_hide == true ): ?>

                         <div class="col-auto  header-right-bar-content">

                            <?php if( $user_search_show_hide == true ): ?>
                             <!-- Search Btn -->
                            <div class="uthr-login  header-right-bar">
                                <div class="search-box-btn search-box-outer">
                                    <i class="icon-magnifier icons"></i>                                 
                                </div>
                            </div> 
                            <?php endif; ?>

                            <?php if (has_nav_menu('login-menu') && $userlogin_show_hide == true ): ?>
                            <div class="uthr-login header-right-bar">
                                   <i class="icon-user icons"></i>
                                    <?php 
                                          wp_nav_menu( array(
                                            'theme_location' => 'login-menu',
                                        ));
                                ?>
                             </div> 
                            <?php endif; ?>
                            <?php if($wishlist_show_hide==true && class_exists('WishSuite_Base' ) ): ?>
                            <?php
                            $page_id = wishsuite_get_option( 'wishlist_page', 'wishsuite_table_settings_tabs' );
                            $wishlist_page_url = get_permalink( $page_id );
                            ?>
                             <!-- Search Btn -->
                            <div class="uthr-login  header-right-bar">
                                <div class="search-box-btn wishlist-box">
                                    <a href="<?php echo esc_url( $wishlist_page_url ); ?>"><i class="icon-heart icons"></i></a>
                                </div>
                            </div> 
                            <?php endif; ?>
                            <?php if( class_exists('WooCommerce' ) && $cart_show_hide == true ): ?>
                                <div class="uthr-login header-right-bar shopping_cart">
                                    <a href="#"><span class="item_count"><?php 
                                            echo '<span class="mini_cart_count">' . esc_html( $woocommerce->cart->get_cart_contents_count() ) . '</span>'; 
                                        ?></span><i class="icon-basket-loaded icons"></i></a>
                                    
                                    <div class="widget_shopping_cart_content">
                                        <?php if(class_exists('WooCommerce')): ?>
                                                <!--mini cart-->
                                            <?php wc_get_template('cart/mini-cart.php'); ?>
                                                <!--mini cart end-->
                                        <?php endif; ?>
                                    </div>
                                 </div> 
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <div class="col-auto d-lg-none">
                                <div class="uthr-mobile-menu-toggle">
                                    <button class="toggle">
                                        <i class="icon-top"></i>
                                        <i class="icon-middle"></i>
                                        <i class="icon-bottom"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Menu & Button End -->
                </div>
            </div>
        </div>
    </div>

    <div id="uthr-site-main-mobile-menu" class="uthr-site-main-mobile-menu">
        <div class="uthr-mobile-menu-header">
            <div class="uthr-mobile-menu-logo">
                    <?php
                        if( has_custom_logo() ){
                            the_custom_logo(); 
                        }else{
                            ?>
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <?php
                            bloginfo( 'name' );
                            ?>
                            </a>
                        <?php
                        }
                    ?>
            </div>
            <div class="uthr-mobile-menu-close">
                <button class="toggle">
                    <i class="icon-top"></i>
                    <i class="icon-bottom"></i>
                </button>
            </div>
        </div>
        <div class="uthr-mobile-menu-content">
            <nav class="uthr-site-mobile-menu">
                <?php
                       if ( has_nav_menu('main-menu') ) {
                    wp_nav_menu( array(
                        'theme_location' => 'main-menu',
                        'container'      => false,
                        'menu_class'     => 'uthr-nav',
                        'walker'         => new uthr_Walker_Nav_Menu(),
                        'device'         => 'mobile'
                    ) );
                }
                ?>
            </nav>
        </div>
    </div>

    <!-- Page Header Section Start -->
    <?php
    $hide_page_title_page='';
        $hide_page_title_page =  get_option('uthr_hide_page_title_page' );
        if ( $hide_page_title_page == true ){
        if( !is_front_page() && !is_page() && !is_404() ){
            get_template_part('/template-parts/page-title'); 
        }}else{
          if( !is_front_page() && !is_404() ){
            get_template_part('/template-parts/page-title'); 
        }  
        }
    ?>

    <!-- Page Header Section End -->

    <div id="content" class="site-content">
        