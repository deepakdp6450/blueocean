<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package uthr
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( "uthr-single-blog" );   ?> >
	
    <div class="uthr-blog-content-area">
        <?php if(has_post_thumbnail()): ?>
        <div class="uthr-blog-thumb">
            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('uthr_size_1076X755'); ?></a>
        </div>
        <?php endif; ?>
        <div class="uthr-blog-content <?php if( !has_post_thumbnail() ){echo esc_attr('no-thumbnail'); }?>">

            <?php
            if ( 'post' === get_post_type() ) :
                ?>
            <ul class="uthr-blog-meta-info">
                <li><?php the_category( ', ' ); ?></li>
                <li><?php echo esc_html( get_the_modified_date() ); ?></li>
            </ul>
            <?php endif; ?>
    		<?php
    		if ( is_singular() ) :
    			the_title( '<h1 class="entry-title">', '</h1>' );
    		else :
    			the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h3>' );
    		endif;

             if ( get_option('uthr_show_content',false) == true && get_the_excerpt()!='' ): ?>
            <div class="uthr-excerpt">
                <p><?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
                </p>
            </div>
            <?php endif; ?>

                <?php
                 wp_link_pages( array(
                        'before'      => '<div class="desc fix"><div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'uthr' ) . '</span>',
                        'after'       => '</div></div>',
                        'link_before' => '<span>',
                        'link_after'  => '</span>',
                        'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'uthr' ) . ' </span>%',
                        'separator'   => '<span class="screen-reader-text">, </span>',
                    ) );
                ?>

    		<?php
                $read_more_text = get_option( 'uthr_read_more_text','' );

                $show_read_more_button = get_option('uthr_show_read_more_button',false);
             if ( !is_singular() && $show_read_more_button == "1" && !empty( $read_more_text ) ): ?>
    	       <a class="uthr-read_more_btn" href="<?php the_permalink();?>"><?php echo wp_kses_post( $read_more_text);?></a>
    		<?php endif; ?>
            
        </div>
    </div>

</article><!-- #post- -->

