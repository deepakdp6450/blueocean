<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package uthr
 */

get_header(); ?>

<div class="content-area section-padding">
    <div class="uthr-site-main">
			<?php
				if ( have_posts() ) : ?>
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
				endif; ?>
	</div><!-- #primary -->
</div><!-- #primary -->

<?php get_footer();
