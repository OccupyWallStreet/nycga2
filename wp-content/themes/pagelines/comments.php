<?php
/**
 * COMMENTS
 *
 * The template for displaying Comments.
 *
 * @package     PageLines Framework
 * @since       1.0
 *
 * @link        http://www.pagelines.com/
 * @link        http://www.pagelines.com/tour
 *
 * @author      PageLines   http://www.pagelines.com/
 * @copyright   Copyright (c) 2012, PageLines   hello@pagelines.com
 *
 * @internal    last revised February 23, 2012
 * @version     2.1.1
 */

if(!have_comments() && !comments_open())
	return;
	
?>

<div id="comments" class="wp-comments">
	<div class="wp-comments-pad">
	<?php 
	
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		if ( post_password_required() ){
			printf('<p class="nopassword">%s</p></div></div>', __( 'This post is password protected. Enter the password to view any comments.', 'pagelines' ) );
			return;
			
		}
		
		if ( have_comments() ) : ?>
			<h3 id="comments-title"><?php
			printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), 'pagelines' ),
			number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );
			?></h3>

			<ol class="commentlist">
				<?php wp_list_comments(array('avatar_size' => 45)); ?>
			</ol>
			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
						<div class="navigation fix">
							<div class="alignleft"><?php previous_comments_link( __( "<span class='meta-nav'>&larr;</span> Older Comments", 'pagelines' ) ); ?></div>
							<div class="alignright"><?php next_comments_link( __( "Newer Comments <span class='meta-nav'>&rarr;</span>", 'pagelines' ) ); ?></div>
						</div> <!-- .navigation -->
			<?php endif; // check for comment navigation ?>

			
<?php 	else : // or, if we don't have comments:

			/* If there are no comments and comments are closed,
			 * let's leave a little note, shall we?
			 */
			if ( ! comments_open() ) :
		?>
			<p class="nocomments"><?php _e('Comments are closed.', 'pagelines');?></p>
		<?php endif; // end ! comments_open() ?>

<?php 
		endif; // end have_comments() 
	 
	comment_form(); ?>
	</div>
</div>
