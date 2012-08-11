<?php

/**
 * Replies Loop - Single Reply
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

	<li id="post-<?php bbp_reply_id(); ?>" <?php bbp_reply_class(); ?>>
	
		<div class="comment-wrap">

			<div class="comment-header bbp-reply-author">

				<?php do_action( 'bbp_theme_before_reply_author_details' ); ?>
				<?php bbp_reply_author_link( array( 'type' => 'avatar', 'size' => 36 ) ); ?>
				<?php do_action( 'bbp_theme_after_reply_author_details' ); ?>

				<div class="comment-meta">

					<span class="comment-author vcard"><cite><?php bbp_reply_author_link( array( 'type' => 'name' ) ); ?></cite></span><!-- .comment-author -->
					
					<a href="<?php bbp_reply_url(); ?>" title="<?php bbp_reply_title(); ?>"><?php printf( __( '%1$s at %2$s', 'bbpress' ), get_the_date(), esc_attr( get_the_time() ) ); ?></a>

					<?php if ( is_super_admin() ) : ?>
						<?php do_action( 'bbp_theme_before_reply_author_admin_details' ); ?>
						<?php bbp_author_ip( bbp_get_reply_id() ); ?>
						<?php do_action( 'bbp_theme_after_reply_author_admin_details' ); ?>
					<?php endif; ?>
				
				</div><!-- .comment-meta -->

			</div><!-- .comment-header -->

			<div class="comment-content comment-text bbp-reply-content">
			
				<?php do_action( 'bbp_theme_after_reply_content' ); ?>
				
				<?php bbp_reply_content(); ?>
				
					<?php do_action( 'bbp_theme_before_reply_admin_links' ); ?>
					
					<?php bbp_reply_admin_links( array( 'sep' => ' &#160; ' ) ); ?>
					
					<?php do_action( 'bbp_theme_after_reply_admin_links' ); ?>
					
				<?php do_action( 'bbp_theme_before_reply_content' ); ?>
				
			</div><!-- .bbp-reply-content -->
		
		</div><!-- .comment-wrap -->

	</li>
