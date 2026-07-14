<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function uthr_breadcrumbs() {

    
    $breadcrumbs_separator = '-';

    /**
    * Settings
    */
    $separator          = $breadcrumbs_separator;
    $breadcrums_id      = 'breadcrumbs';
    $breadcrums_class   = 'uthr-page-breadcrumb';
    $home_title         = __('Home', 'uthr');

    // If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
    $custom_taxonomy    = '';

    // Get the query & post information
    global $post,$wp_query;

    // Do not display on the homepage
    if ( !is_front_page() ) {

        // Build the breadcrums
        echo '<ul id="' . $breadcrums_id . '" class="' . $breadcrums_class . '">';

        // Home page
        echo '<li class="item-home"><a href="' . esc_url(get_home_url()) . '" title="' . $home_title . '">' . $home_title . '</a></li>';

        if ( is_archive() && !is_tax() && !is_category() && !is_tag() ) {

            echo '<li class="active item-current item-archive">' . post_type_archive_title( '', false ) . '</li>';

        } else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {

            // If post is a custom post type
            $post_type = get_post_type();

            // If it is a custom post type display name and link
            if($post_type != 'post') {

                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);

                echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-custom-post-type-' . $post_type . '" href="' . esc_url($post_type_archive) . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';

            }

            $custom_tax_name = get_queried_object()->name;
            echo '<li class="active item-current item-archive">' . esc_html( $custom_tax_name ) . '</li>';

        } else if ( is_single() ) {

            // If post is a custom post type
            $post_type = get_post_type();

            // If it is a custom post type display name and link
            if($post_type != 'post') {

                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);

                echo '<li class="item-custom-post-type-' . $post_type . '"><a class="bread-custom-post-type-' . $post_type . '" href="' . esc_url($post_type_archive) . '" title="' . esc_attr($post_type_object->labels->name) . '">' . esc_html($post_type_object->labels->name) . '</a></li>';

            }

            // Get post category info
            $category = get_the_category();

            if(!empty($category)) {

                // Get last category post is in
                $last_category = end($category);
                // Get parent any categories and create array
                $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','),',');
                $cat_parents = explode(',',$get_cat_parents);
                // Loop through parent categories and store in variable $cat_display
                $cat_display = '';
                foreach($cat_parents as $parents) {
                    $cat_display .= '<li class="item-cat">'.$parents.'</li>';
                }

            }

            // If it's a custom post type within a custom taxonomy
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);
            if(empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {

                $taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );
                $cat_id         = $taxonomy_terms[0]->term_id;
                $cat_nicename   = $taxonomy_terms[0]->slug;
                $cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name       = $taxonomy_terms[0]->name;

            }

            // Check if the post is in a category
            if(!empty($last_category)) {
                echo wp_kses_post($cat_display);
                echo '<li class="active item-current item-' . $post->ID . '">' . esc_html( get_the_title() ) . '</li>';

                // Else if post is in a custom taxonomy
            } else if(!empty($cat_id)) {

                echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '"><a class="bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . esc_url($cat_link) . '" title="' . esc_attr($cat_name) . '">' . esc_html($cat_name) . '</a></li>';
                echo '<li class="active item-current item-' . $post->ID . '">' . esc_html( get_the_title() ) . '</li>';

            } else {
                echo '<li class="active item-current item-' . $post->ID . '">' . esc_html( get_the_title() ) . '</li>';
            }

        } else if ( is_category() ) {

            // Category page
            echo '<li class="active item-current item-cat">' . esc_html( single_cat_title('', false) ) . '</li>';

        } else if ( is_page() ) {

            // Standard page
            if( $post->post_parent ){

                // If child page, get parents
                $anc = get_post_ancestors( $post->ID );

                // Get parents in the right order
                $anc = array_reverse($anc);

                // Parent page loop
                foreach ( $anc as $ancestor ) {
                    $parents = '<li class="item-parent item-parent-' . $ancestor . '"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
                }

                // Display parent pages
                echo wp_kses_post($parents);

                // Current page
                echo '<li class="active item-current item-' . $post->ID . '">' . esc_html( get_the_title() ) . '</li>';

            } else {

                // Just display current page if not parents
                echo '<li class="active item-current item-' . $post->ID . '">' . esc_html( get_the_title() ) . '</li>';

            }

        } else if ( is_tag() ) {

            // Tag page

            // Get tag information
            $term_id        = get_query_var('tag_id');
            $taxonomy       = 'post_tag';
            $args           = 'include=' . $term_id;
            $terms          = get_terms( $taxonomy, $args );
            $get_term_id    = $terms[0]->term_id;
            $get_term_slug  = $terms[0]->slug;
            $get_term_name  = $terms[0]->name;

            // Display the tag name
            echo '<li class="active item-current item-tag-' . $get_term_id . ' item-tag-' . $get_term_slug . '">' . $get_term_name . '</li>';

        } elseif ( is_day() ) {

            // Day archive

            // Year link
            echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . esc_html__('Archives', 'uthr') .'</a></li>';

            // Month link
            echo '<li class="item-month item-month-' . get_the_time('m') . '"><a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link( get_the_time('Y'), get_the_time('m') ) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . esc_html__('Archives', 'uthr') .'</a></li>';

            // Day display
            echo '<li class="active item-current item-' . get_the_time('j') . '">' . get_the_time('jS') . ' ' . get_the_time('M') . esc_html__('Archives', 'uthr') .'</li>';

        } else if ( is_month() ) {

            // Month Archive

            // Year link
            echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . esc_html__('Archives', 'uthr') .'</a></li>';

            // Month display
            echo '<li class="item-month item-month-' . get_the_time('m') . '">' . get_the_time('M') . esc_html__('Archives', 'uthr') .'</li>';

        } else if ( is_year() ) {

            // Display year archive
            echo '<li class="active item-current item-current-' . get_the_time('Y') . '">' . get_the_time('Y') . esc_html__('Archives', 'uthr') .'</li>';

        } else if ( is_author() ) {

            // Auhor archive

            // Get the author information
            global $author;
            $userdata = get_userdata( $author );

            // Display author name
            echo '<li class="active item-current item-current-' . esc_attr($userdata->user_nicename) . '">' . esc_html__('Author: ', 'uthr') . esc_html( $userdata->display_name ) . '</li>';

        } else if ( get_query_var('paged') ) {

            // Paginated archives
            echo '<li class="active item-current item-current-' . get_query_var('paged') . '">'.esc_html__('Page', 'uthr') . ' ' . get_query_var('paged') . '</li>';

        } else if ( is_search() ) {

            // Search results page
            echo '<li class="active item-current item-current-' . get_search_query() . '">' . esc_html__('Search results for: ', 'uthr') . get_search_query() . '</li>';

        } elseif ( is_404() ) {
            
            // 404 page
            echo '<li>' . esc_html__('Error 404', 'uthr') . '</li>';
        }

        echo '</ul>';

    }

}

/**
 * [uthr_premium_btn]
 * @return HTML
 */
function uthr_button( $btn_text = '', $btn_link = '' ){
    $btn_text = get_option( $btn_text );
    $btn_link = get_option( $btn_link );
    $btn = '';
    if( !empty( $btn_text ) ){
        $btn = '<a href="'.esc_url($btn_link).'" class="uthr-header-btn">'.esc_html( $btn_text ).'</a>';
    }
    return $btn;
}



// Blog post Sharing
if ( ! function_exists('uthr_sharing_icon_links') ) {

 function uthr_sharing_icon_links( ) {

  global $post;

  $html = '<ul>';



   // facebook

   $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u='. get_the_permalink();
   $html .= '<li><a href="'. esc_url( $facebook_url ) .'" target="_blank"><i class="icofont-facebook"></i></a></li>';

   // twitter

   $twitter_url = 'https://twitter.com/share?'. esc_url(get_permalink()) .'&amp;text='. get_the_title();
   $html .= '<li><a href="'. esc_url( $twitter_url ) .'" target="_blank"><i class="icofont-twitter"></i></a></li>';

   // linkedin

   $linkedin_url = 'http://www.linkedin.com/shareArticle?url='. esc_url(get_permalink()) .'&amp;title='. get_the_title();
   $html .= '<li><a href="'. esc_url( $linkedin_url ) .'" target="_blank"><i class="icofont-linkedin"></i></a></li>';

  
   // pinterest
   $pinterest_url = 'https://pinterest.com/pin/create/bookmarklet/?url='. esc_url(get_permalink()) .'&amp;description='. get_the_title() .'&amp;media='. esc_url(wp_get_attachment_url( get_post_thumbnail_id($post->ID)) );
   $html .= '<li><a href="'. esc_url( $pinterest_url ) .'" target="_blank"><i class="icofont-pinterest"></i></a></li>';

   
   // tumblr
   $tumblr_url = 'http://www.tumblr.com/share/link?url='. urlencode( esc_url(get_permalink()) ) .'&amp;name='. urlencode( get_the_title() ) .'&amp;description='. urlencode( get_the_excerpt() );
   $html .= '<li><a href="'. esc_url( $tumblr_url ) .'" target="_blank"><i class="icofont-tumblr"></i></a></li>';

   
   // reddit

   $reddit_url = 'http://reddit.com/submit?url='. esc_url(get_permalink()) .'&amp;title='. get_the_title();
   $html .= '<li><a href="'. esc_url( $reddit_url ) .'" target="_blank"><i class="icofont-reddit"></i></a></li>';

  $html .= '</ul>';

  echo wp_kses_post($html);

 }

}

    
// Social link function
function uther_social( $ul_class = 'uthr-footer-social'){

    $social_links = [];
    if(''!= get_option('uthr_company_facebook_link','#' )){
        $social_links['facebook'] = get_option('uthr_company_facebook_link','#' );
    }
     if(''!= get_option('uthr_company_instagram_link','#' )){
        $social_links['instagram'] = get_option('uthr_company_instagram_link','#' );
    }
     if(''!= get_option('uthr_company_twitter_link','#' )){
        $social_links['twitter'] = get_option('uthr_company_twitter_link','#' );
    }
     if(''!= get_option('uthr_company_youtube_link','#' )){
        $social_links['youtube'] = get_option('uthr_company_youtube_link','#' );
    }
     if(''!= get_option('uthr_company_pinterest_link','#' )){
        $social_links['pinterest'] = get_option('uthr_company_pinterest_link','#' );
    }
    ?>
        <?php if( $social_links ): ?>
        <ul class="<?php echo esc_attr( $ul_class) ?>">

            <?php if(''!= get_option('uthr_company_facebook_link','#' ) ): ?>
            <li>
                <a href="<?php echo esc_url( $social_links['facebook'] ); ?>"><i class="icon-social-facebook icons"></i></a>
            </li>
            <?php endif; ?>

            <?php if( ''!= get_option('uthr_company_instagram_link','#' ) ): ?>
            <li><a href="<?php echo esc_url( $social_links['instagram'] ); ?>"><i class="icon-social-instagram icons"></i></a></li>
            <?php endif; ?>

            <?php if(''!= get_option('uthr_company_twitter_link','#' ) ): ?>
            <li><a href="<?php echo esc_url( $social_links['twitter'] ); ?>"><i class="icon-social-twitter icons"></i></a></li>
            <?php endif; ?>

            <?php if(''!= get_option('uthr_company_youtube_link','#' ) ): ?>
            <li><a href="<?php echo esc_url( $social_links['youtube'] ); ?>"><i class="icon-social-youtube icons"></i></a></li>
            <?php endif; ?>

            <?php if(''!= get_option('uthr_company_pinterest_link','#' ) ): ?>
            <li><a href="<?php echo esc_url( $social_links['pinterest'] ); ?>"><i class="icon-social-pinterest icons"></i></a></li>
            <?php endif; ?>
        </ul>
    <?php
    else:

        echo esc_html__("Social link doesn't exist","uthr");

        endif;
    }
