<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package uthr
 */

get_header();
?>

    <div id="primary" class="content-area section-padding single_blog_page">

        <div class="container-fluid">
            <div class="row ">               
                <div class="col-12">
                    <div class="single-blog-area">
                        <?php
                            while ( have_posts() ) :
                                the_post();

                                get_template_part( 'template-parts/content-single', get_post_format() );

                            endwhile; // End of the loop.
                        ?>
                    </div>
                </div>


            </div>
        </div>
    </div><!-- #primary -->
<?php
 // If comments are open or we have at least one comment, load up the comment template.
if ( ( comments_open() || get_comments_number() ) && ! post_password_required() ) :

    ?>
    <div class="uthr-comment-area">
        <div class="container">
            <?php
            comments_template();
            ?>
        </div>
    </div>
<?php
endif;
?>

<?php
get_footer();