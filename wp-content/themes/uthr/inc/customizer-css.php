<?php

if(!function_exists('uthr_custom_css')){

    function uthr_custom_css(){

      if ( !empty( get_option('uthr_theme_pry_color') )){
        $theme_pry_color = get_option('uthr_theme_pry_color');
      }

      if ( !empty( get_option('uthr_theme_sery_color') )){
        $theme_sery_color = get_option('uthr_theme_sery_color');
      }

      // header top bg
    if ( !empty( get_option('uthr_header_top_bg_color') )){
        $header_top_bg_color = get_option('uthr_header_top_bg_color','#6a7964');
      }
      // header color
      if ( !empty( get_option('uthr_header_bgcolor') )){
        $header_bgcolor = get_option('uthr_header_bgcolor');
      }
      if ( !empty( get_option('uthr_header_menu_color') )){
        $header_menu_color = get_option('uthr_header_menu_color');
      }
      if ( !empty( get_option('uthr_header_menu_hover_color') )){
        $header_menu_hover_color = get_option('uthr_header_menu_hover_color');
      }

      // Button Style
      if ( !empty( get_option('uthr_button_bgcolor') )){
        $button_bgcolor = get_option('uthr_button_bgcolor');
      }
      if ( !empty( get_option('uthr_button_color') )){
        $button_color = get_option('uthr_button_color');
      }
      if ( !empty( get_option('uthr_button_bg_hover_color') )){
        $button_bg_hover_color = get_option('uthr_button_bg_hover_color');
      }

      if ( !empty( get_option('uthr_login_color') )){
        $login_color = get_option('uthr_login_color');
      }
      if ( !empty( get_option('uthr_login_hover_color') )){
        $login_hover_color = get_option('uthr_login_hover_color');
      }

      // Page title color
      if ( !empty( get_option('uthr_page_title_color') )){
        $page_title_color = get_option('uthr_page_title_color');
      }
      if ( !empty( get_option('uthr_breadcrumb_color') )){
        $breadcrumb_color = get_option('uthr_breadcrumb_color');
      }
      if ( !empty( get_option('uthr_breadcrumb_color_hover') )){
        $breadcrumb_color_hover = get_option('uthr_breadcrumb_color_hover');
      }

      // Footer css
      if ( !empty( get_option('uthr_footer_widgets_title_color') )){
        $footer_widgets_title_color = get_option('uthr_footer_widgets_title_color');
      }
      if ( !empty( get_option('uthr_footer_widgets_content_color') )){
        $footer_widgets_content_color = get_option('uthr_footer_widgets_content_color');
      }
      if ( !empty( get_option('uthr_footer_widgets_link_hover_color') )){
        $footer_widgets_link_hover_color = get_option('uthr_footer_widgets_link_hover_color');
      }
      if ( !empty( get_option('uthr_footer_copyright_color') )){
        $footer_copyright_color = get_option('uthr_footer_copyright_color');
      }

      // Footer social
      if ( !empty( get_option('uthr_footer_social_color') )){
        $footer_social_color = get_option('uthr_footer_social_color');
      }
      if ( !empty( get_option('uthr_footer_social_hover_color') )){
        $footer_social_hover_color = get_option('uthr_footer_social_hover_color');
      }


       ?>     

         <!-- Custom Stylesheet -->

          <style type="text/css">

            /*Theme Primary color*/
            <?php if(!empty($theme_pry_color) ){ ?> 
              /*color*/
              a:hover, a:focus,.blog-search form button:hover,.uthr-category-list>a:hover,.uthr-category-list a:hover,.type-post.tag-sticky-2 .uthr-blog-content:before, .type-post.sticky .uthr-blog-content:before,.uthr-sidebar-widget ul li:hover >a,.widget_calendar tbody td#today,.widget-area .uthr-sidebar-widget select:focus,.widget-area .uthr-sidebar-widget select:active,
               .widget-area .uthr-sidebar-widget select:hover,.comment-reply-title small,.uthr-menu > ul > li:hover > a,.uthr-menu >ul > li >.sub-menu li:hover > a,.uthr-login a:hover,.uthr-page-breadcrumb li a:hover,.uthr-footer-widget ul li:hover > a,.uthr-footer-widget-list ul li:hover > a, .uthr-footer-widget.widget_nav_menu ul li:hover > a,.blog-search form button:hover,.uthr-excerpt ul.wp-block-archives.wp-block-archives-list li:hover > a , .uthr-excerpt ul.wp-block-categories.wp-block-categories-list li:hover > a,.uthr-menu>ul>li.current-menu-item > a,.theme-color,.newsletter-form .form-btn:hover::after,.social-sharing-post ul li a:hover,.next-prev a:hover,.tagcloud>a:hover,.wp-block-tag-cloud a:hover,.product-content h4 a:hover {
                color:<?php echo esc_attr( $theme_pry_color );?>;    
              }
              /*backgound color*/
              .post-password-form input[type="submit"],.uthr-footer-widget div.wpforms-container-full .wpforms-form button[type=submit],.uthr-header-btn,a.wp-block-button__link:hover,.uthr-excerpt .is-style-outline .wp-block-button__link:not(.has-text-color):hover,.wp-block-search .wp-block-search__button,.pnf-inner a.btn,.search-popup .close-search,a.uthr-read_more_btn, .comments-area input[type="submit"],.preloader-wrap .preloader .dot,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,.product_action ul li a:hover, .woocommerce a.button.htcompare-btn.htcompare-btn-style-theme.htcompare-shop-use_shortcode.htcompare-product-after_cart_btn.button:hover,.wishsuite-product-add_to_cart a:hover,.cart_button a:hover{   
                background-color:<?php echo esc_attr( $theme_pry_color );?>!important;    
              }
              
            /*border color*/
              .type-post.tag-sticky-2 .uthr-blog-content-area, .type-post.sticky .uthr-blog-content-area,.tagcloud>a:hover,.post-password-form input[type="submit"],.uthr-footer-widget div.wpforms-container-full .wpforms-form button[type=submit],.wp-block-search .wp-block-search__button,.pnf-inner a.btn,.cart_button a:hover {   
                border-color:<?php echo esc_attr( $theme_pry_color );?>;    
              }
              /*menu dot color*/

              .uthr-menu >ul > li >.sub-menu li:hover > a::before{
                text-shadow: 8px 0 <?php echo esc_attr( $theme_pry_color );?>, -8px 0 <?php echo esc_attr( $theme_pry_color );?>;
              }


          <?php } ?>

            /*Theme Secondary color*/
            <?php if(!empty($theme_sery_color) ){ ?> 
              /*color*/
              /*backgound color*/
              .comment-form input[type="submit"]:hover,.comment-form input[type="submit"]:focus,.post-password-form input[type="submit"]:hover,.uthr-footer-widget div.wpforms-container-full .wpforms-form button[type=submit]:hover,.uthr-header-btn:hover,.wp-block-search .wp-block-search__button:hover,.pnf-inner a.btn:hover,.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover,.woocommerce div.product form.cart .button::before,.wishsuite-product-add_to_cart a {   
                background-color:<?php echo esc_attr( $theme_sery_color );?>!important;    
              }
              
            /*border color*/
              .comment-form input[type="submit"]:hover,.comment-form input[type="submit"]:focus,.post-password-form input[type="submit"]:hover,.uthr-footer-widget div.wpforms-container-full .wpforms-form button[type=submit]:hover,.wp-block-search .wp-block-search__button:hover,.pnf-inner a.btn:hover {
                border-color:<?php echo esc_attr( $theme_sery_color );?>;    
              }
          <?php } ?>
          /*Header top bg*/
          <?php if(!empty($header_top_bg_color) ){ ?> 
              .uthr-header-top{   
                background-color:<?php echo esc_attr( $header_top_bg_color );?>;    
              }
         <?php } ?>
          /*Header Color*/
            <?php if(!empty($header_bgcolor) ){ ?> 
              .uthr-header .uthr-header-inner{   
                background-color:<?php echo esc_attr( $header_bgcolor );?>;    
              }
          <?php } if(!empty( $header_menu_color) ){ ?> 
              .uthr-menu > ul > li > a{
                color:<?php echo esc_attr( $header_menu_color );?>;    
              }
          <?php } if(!empty( $header_menu_hover_color) ){ ?> 
              .uthr-menu > ul > li:hover > a,.uthr-menu >ul > li >.sub-menu li:hover > a,.uthr-menu>ul>li.current-menu-item > a{
                color:<?php echo esc_attr( $header_menu_hover_color );?>;  
              }
              .uthr-menu > ul > li > a .text::before{
                background-color:<?php echo esc_attr( $header_menu_hover_color );?>;  
              }
              /*menu dot color*/
              .uthr-menu >ul > li >.sub-menu li:hover > a::before{
                text-shadow: 8px 0 <?php echo esc_attr( $header_menu_hover_color );?>, -8px 0 <?php echo esc_attr( $header_menu_hover_color );?>;
              }

           <?php } ?>


           /*Button style*/
          <?php if(!empty( $button_bgcolor )){ ?> 
              .uthr-header-btn{
                background-color:<?php echo esc_attr( $button_bgcolor );?>;  
              }
           <?php } if(!empty( $button_color )){ ?> 
              .uthr-header-btn{
                color:<?php echo esc_attr( $button_color );?>;  
              } 
             <?php } if(!empty( $button_bg_hover_color )){ ?> 
              .uthr-header-btn:hover{
                background-color:<?php echo esc_attr( $button_bg_hover_color );?>;  
              } 
             <?php } if(!empty( $login_color )){ ?> 
              .uthr-login a,.uthr-login>i,.search-box-btn{
                color:<?php echo esc_attr( $login_color );?>;  
              } 
              <?php } if(!empty( $login_hover_color )){ ?> 
              .uthr-login a:hover,.search-box-btn:hover{
                color:<?php echo esc_attr( $login_hover_color );?>;  
              } 
              <?php } ?>

              /*Page title color*/
        <?php if(!empty( $page_title_color )){ ?> 
              .uthr-page-header-content .title{
                color:<?php echo esc_attr( $page_title_color );?>;  
              } 
              <?php }if(!empty( $breadcrumb_color )){ ?> 
              .uthr-page-breadcrumb li {
                color:<?php echo esc_attr( $breadcrumb_color );?>;  
              } 
              <?php }if(!empty( $breadcrumb_color_hover )){ ?> 
              .uthr-page-breadcrumb li a:hover {
                color:<?php echo esc_attr( $breadcrumb_color_hover );?>;  
              } 
              <?php } ?>

            /*Footer css*/
            <?php if(!empty( $footer_widgets_title_color )){ ?> 
              .uthr-footer-widget-title{   
                color:<?php echo esc_attr( $footer_widgets_title_color );?>;    
              }
          <?php }  if(!empty( $footer_widgets_content_color )){ ?> 
              .uthr-footer-widget ul li,.uthr-footer-widget ul li a,.uthr-footer-widget p,.uthr-footer-widget b, .uthr-footer-widget strong,.uthr-footer-top,.uthr-footer-top .widget_calendar thead th,.uthr-footer-top .widget_calendar caption,.uthr-footer-widget-list ul li a, .uthr-footer-widget.widget_nav_menu ul li a{
                color:<?php echo esc_attr( $footer_widgets_content_color );?>;    
                }
        <?php } if(!empty( $footer_widgets_link_hover_color )){ ?> 

             .uthr-footer-widget ul li:hover > a,.uthr-footer-widget-list ul li:hover > a, .uthr-footer-widget.widget_nav_menu ul li:hover > a,.blog-search form button:hover{
                color:<?php echo esc_attr( $footer_widgets_link_hover_color );?>;    
                }
                .uthr-footer-widget .tagcloud>a:hover{
                  border-color: <?php echo esc_attr( $footer_widgets_link_hover_color );?>;
                  background-color: <?php echo esc_attr( $footer_widgets_link_hover_color );?>;
                }
        <?php } if(!empty( $footer_copyright_color )){ ?> 
             .uthr-copyright{
                color:<?php echo esc_attr( $footer_copyright_color );?>;    
                }
        <?php } if(!empty( $footer_social_color )){ ?> 
             ul.uthr-footer-social li a{
                color:<?php echo esc_attr( $footer_social_color );?>;    
                }
        <?php } if(!empty( $footer_social_hover_color )){ ?> 
             ul.uthr-footer-social li a:hover{
                color:<?php echo esc_attr( $footer_social_hover_color );?>;    
                }
        <?php } ?>

          </style>

      <?php

      }
  }


add_action( 'wp_head', 'uthr_custom_css'  ) ;