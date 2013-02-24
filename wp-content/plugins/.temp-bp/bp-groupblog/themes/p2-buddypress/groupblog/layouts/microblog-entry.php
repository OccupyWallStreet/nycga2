<?php /* This template is used by blog-activity.php to show each activity */ ?>
<?php $post = get_post( bp_get_activity_secondary_item_id() ); ?>

<?php do_action( 'groupblog_before_activity_entry' ) ?>

<li class="<?php bp_activity_css_class() ?>" id="activity-<?php bp_activity_id() ?>">

	<div class="post">
	
		<div class="author-box">
			<?php bp_activity_avatar( 'type=full&width=50&height=50' ) ?>
			<p><?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
		</div>
	
		<div class="post-content">
			<?php if ( bp_get_activity_secondary_item_id() ) : ?>
				<h2 class="posttitle"><a href="<?php bp_activity_feed_item_link() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
		
				<p class="date"><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'buddypress' ) ?> <?php the_category(', ') ?> <?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>
			<?php else : ?>
				<?php bp_activity_action() ?>
			<?php endif; ?>
			
			<?php if ( bp_activity_has_content() ) : ?>	
				<div class="entry">
					<?php bp_activity_content_body() ?>
				</div>
			<?php endif; ?>
			
			<?php do_action( 'bp_activity_entry_content' ) ?>
	
			<p class="postmetadata">										
				<?php if ( is_user_logged_in() && bp_activity_can_comment() ) : ?>
					<a href="<?php bp_activity_comment_link() ?>" class="acomment-reply" id="acomment-comment-<?php bp_activity_id() ?>"><?php _e( 'Reply', 'buddypress' ) ?> (<span><?php bp_activity_comment_count() ?></span>)</a>
				<?php else : ?>	
					 <?php _e( 'Comments', 'buddypress' ) ?> (<span><?php bp_activity_comment_count() ?></span>)
				<?php endif; ?>
	
				<?php if ( is_user_logged_in() ) : ?>
					<?php if ( !bp_get_activity_is_favorite() ) : ?>
						<a href="<?php bp_activity_favorite_link() ?>" class="fav" title="<?php _e( 'Mark as Favorite', 'buddypress' ) ?>"><?php _e( 'Favorite', 'buddypress' ) ?></a>
					<?php else : ?>
						<a href="<?php bp_activity_unfavorite_link() ?>" class="unfav" title="<?php _e( 'Remove Favorite', 'buddypress' ) ?>"><?php _e( 'Remove Favorite', 'buddypress' ) ?></a>
					<?php endif; ?>
				<?php endif;?>
	
				<?php do_action( 'bp_activity_entry_meta' ) ?>

				<span class="tags"><?php the_tags( __( 'Tags: ', 'buddypress' ), ', ', '<br />'); ?></span>
			</p>
			
			<?php if ( 'activity_comment' == bp_get_activity_type() ) : ?>
				<div class="activity-inreplyto">
					<strong><?php _e( 'In reply to', 'buddypress' ) ?></strong> - <?php bp_activity_parent_content() ?> &middot;
					<a href="<?php bp_activity_thread_permalink() ?>" class="view" title="<?php _e( 'View Thread / Permalink', 'buddypress' ) ?>"><?php _e( 'View', 'buddypress' ) ?></a>
				</div>
			<?php endif; ?>
		
			<?php do_action( 'bp_before_activity_entry_comments' ) ?>

			<?php if ( bp_activity_can_comment() ) : ?>
				<div class="activity-comments">
					<?php bp_activity_comments() ?>
		
					<?php if ( is_user_logged_in() ) : ?>
					<form action="<?php bp_activity_comment_form_action() ?>" method="post" id="ac-form-<?php bp_activity_id() ?>" class="ac-form"<?php bp_activity_comment_form_nojs_display() ?>>
						<div class="ac-reply-avatar"><?php bp_loggedin_user_avatar( 'width=25&height=25' ) ?></div>
						<div class="ac-reply-content">
							<div class="ac-textarea">
								<textarea id="ac-input-<?php bp_activity_id() ?>" class="ac-input" name="ac_input_<?php bp_activity_id() ?>"></textarea>
							</div>
							<input type="submit" name="ac_form_submit" value="<?php _e( 'Post', 'buddypress' ) ?> &rarr;" /> &nbsp; <?php _e( 'or press esc to cancel.', 'buddypress' ) ?>
							<input type="hidden" name="comment_form_id" value="<?php bp_activity_id() ?>" />
						</div>
						<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ) ?>
					</form>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		
			<?php do_action( 'bp_after_activity_entry_comments' ) ?>
					
		</div>
				
	</div>			

</li>

<?php do_action( 'groupblog_after_activity_entry' ) ?>

