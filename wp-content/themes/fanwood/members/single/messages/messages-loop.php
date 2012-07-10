<?php

/**
 * BuddyPress - Single Member Messages Loop
 *
 * @package BuddyPress
 * @subpackage Theme
 */
 
?>

<?php do_action( 'bp_before_member_messages_loop' ) ?>

<?php if ( bp_has_message_threads() ) : ?>

	<div class="pagination bp-pagination no-ajax" id="user-pag">

		<div class="pag-count" id="messages-dir-count">
			<?php bp_messages_pagination_count() ?>
		</div>

		<div class="pagination-links" id="messages-dir-pag">
			<?php bp_messages_pagination() ?>
		</div>

	</div><!-- .pagination -->

	<?php do_action( 'bp_after_member_messages_pagination' ) ?>
	<?php do_action( 'bp_before_member_messages_threads' ) ?>
	
	<table id="message-threads" class="messages">
	
		<?php while ( bp_message_threads() ) : bp_message_thread(); ?>
		
		<tr id="m-<?php bp_message_thread_id() ?>" class="<?php bp_message_css_class(); ?><?php if ( bp_message_thread_has_unread() ) : ?> unread"<?php else: ?> read"<?php endif; ?>>
		
			<td>
			
				<ul class="item-list dir-list messages">

				<li id="m-<?php bp_message_thread_id() ?>" class="<?php bp_message_css_class(); ?><?php if ( bp_message_thread_has_unread() ) : ?> unread"<?php else: ?> read"<?php endif; ?>>
						
					<div class="item-header">
						
						<div class="item-avatar"><?php bp_message_thread_avatar() ?></div>

						<?php if ( 'sentbox' != bp_current_action() ) : ?>
							<div class="item-title">
								<span class="unread-count"><?php bp_message_thread_unread_count() ?></span> <?php _e( 'From:', 'buddypress' ); ?> <?php bp_message_thread_from() ?>
							</div><!-- .item-title -->
							<span class="activity">
								<?php bp_message_thread_last_post_date() ?>
							</span>
						<?php else: ?>
							<div class="item-title">
								<?php _e( 'To:', 'buddypress' ); ?> <?php bp_message_thread_to() ?>
							</div><!-- .item-title -->
							<span class="activity">
								<?php bp_message_thread_last_post_date() ?>
							</span>
						<?php endif; ?>

					</div><!-- .item-header -->

					<div class="item-content">
						<p><strong><a href="<?php bp_message_thread_view_link() ?>" title="<?php _e( "View Message", "buddypress" ); ?>"><?php bp_message_thread_subject() ?></a></strong><br />
						<?php bp_message_thread_excerpt() ?></p>
					</div>

					<?php do_action( 'bp_messages_inbox_list_item' ) ?>

					<div class="item-action">
						<a class="button confirm" href="<?php bp_message_thread_delete_link() ?>" title="<?php _e( "Delete Message", "buddypress" ); ?>"><?php _e( 'Delete', 'buddypress' ) ?></a> <input type="checkbox" name="message_ids[]" value="<?php bp_message_thread_id() ?>" />
						
					</div><!-- .item-action -->
					
				</li>
				
				</ul>
				
			</td>
			
		</tr>

		<?php endwhile; ?>
		
	</table>

	<div class="entry-content">
		<div class="messages-options-nav">
			<?php bp_messages_options() ?>
		</div><!-- .messages-options-nav -->
	</div><!-- .entry-content -->

	<?php do_action( 'bp_after_member_messages_threads' ) ?>

	<?php do_action( 'bp_after_member_messages_options' ) ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no messages were found.', 'buddypress' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_member_messages_loop' ) ?>