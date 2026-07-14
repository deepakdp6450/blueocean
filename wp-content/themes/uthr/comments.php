<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package uthr
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) :
		?>
		<h2 class="comments-title">
			<?php
			$uthr_comment_count = get_comments_number();
			if ( '1' === $uthr_comment_count ) {
				printf(
					/* translators: 1: title. */
					esc_html__( 'One thought on &ldquo;%1$s&rdquo;', 'uthr' ),
					'<span>' . get_the_title() . '</span>'
				);
			} else {
				printf( // WPCS: XSS OK.
					/* translators: 1: comment count number, 2: title. */
					esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $uthr_comment_count, 'comments title', 'uthr' ) ),
					number_format_i18n( $uthr_comment_count ),
					'<span>' . get_the_title() . '</span>'
				);
			}
			?>
		</h2><!-- .comments-title -->

		<?php the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
			) );
			?>
		</ol><!-- .comment-list -->

		<?php
		the_comments_navigation();

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'uthr' ); ?></p>
			<?php
		endif;

	endif; // Check for have_comments().


			$fields = array(
				
				'author' =>
					'<div class="input_half left"><input id="author" name="author" type="text" placeholder=" '. esc_attr__('Your Name *', 'uthr') .' " value="' . esc_attr( $commenter['comment_author'] ) . '" required="required" /></div>',
					
				'email' =>
					'<div class="input_half right"><input id="email" name="email" class="input_half" placeholder=" '. esc_attr__( 'Your Email *', 'uthr' ) .' " type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" required="required" /></div>',
					
				'url' =>
					'<input id="url" name="url" placeholder=" '. esc_attr__( 'Your Website', 'uthr' ) .' " type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" />'
					
			);
			
			$args = array(
				
				'class_submit' => 'submit',
				'label_submit' => esc_html__( 'Submit Comment', 'uthr' ),
				'comment_field' =>
					'<textarea id="comment" name="comment" placeholder="'. esc_attr__( 'Comment *', 'uthr' ) .'"  required="required"></textarea>',
				'fields' => apply_filters( 'comment_form_default_fields', $fields ),
				'title_reply' => esc_html__('Leave a Comment','uthr'),
				
			);

	comment_form( $args );
	?>

</div><!-- #comments -->
