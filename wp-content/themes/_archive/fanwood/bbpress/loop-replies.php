<?php

/**
 * Replies Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

	<?php do_action( 'bbp_template_before_replies_loop' ); ?>

	<?php if ( !bbp_show_lead_topic() ) : ?>

		<?php if( is_user_logged_in() ) { ?>
			<div class="bbp-subscribe-links">
				<?php bbp_user_subscribe_link( array( 'before' => '' ) ); ?>
				<?php bbp_user_favorites_link(); ?>
			</div>
		<?php } ?>

	<?php endif; ?>
	
	<div id="comments-template">
	
		<div id="comments">

			<ol class="comment-list replies-list">

				<?php while ( bbp_replies() ) : bbp_the_reply(); ?>

					<?php bbp_get_template_part( 'bbpress/loop', 'single-reply' ); ?>

				<?php endwhile; ?>
	
			</ol><!-- .comment-list -->
			
		</div><!-- #comments -->
		
	</div><!-- #comments-template -->

	<?php do_action( 'bbp_template_after_replies_loop' ); ?>
