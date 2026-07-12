<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package uthr
 */

get_header();
?>

<div id="primary" class="content-area section-padding">
    <div class="uthr-site-main">
        <?php
            if ( have_posts() ) :
                ?>
                <div class="container">
                    <div class="row masonry-blog">                     
                        <?php
                        /* Start the Loop */
                        while ( have_posts() ) :
                            ?>
                            <div class="col-md-4 col-sm-6 col-12">
                            <?php
                            the_post();
                                get_template_part( 'template-parts/content', get_post_format() );
                                ?>
                            </div>
                            <?php
                        endwhile;
                        ?> 
                        <?php if( paginate_links() ){  ?>
                        <div class="col-12">
                            <div class="uthr-blog-pagination">
                                <?php uthr_blog_pagination(); ?>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
                <?php
            else :
                get_template_part( 'template-parts/content', 'none' );

            endif;
        ?>
    </div><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();