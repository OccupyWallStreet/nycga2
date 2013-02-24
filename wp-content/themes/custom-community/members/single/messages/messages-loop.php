<?php do_action( 'bp_before_member_messages_loop' ) ?>

<?php if ( bp_has_message_threads() ) : ?>

	<div class="pagination no-ajax" id="user-pag">

		<div class="pag-count" id="messages-dir-count">
			<?php bp_messages_pagination_count() ?>
		</div>

		<div class="pagination-links" id="messages-dir-pag">
			<?php bp_messages_pagination() ?>
		</div>

	</div><!-- .pagination -->

	<?php do_action( 'bp_after_member_messages_pagination' ) ?>
	<?php do_action( 'bp_before_member_messages_threads' ) ?>

	<table id="message-threads" class="messages-notices span8">
		<?php while ( bp_message_threads() ) : bp_message_thread(); ?>

			<tr id="m-<?php bp_message_thread_id() ?>" class="<?php bp_message_css_class(); ?><?php if ( bp_message_thread_has_unread() ) : ?> unread"<?php else: ?> read"<?php endif; ?>>
				<td width="1%" class="thread-count hidden-phone">
					<span class="unread-count"><?php bp_message_thread_unread_count() ?></span>
				</td>
				<td width="15%" class="thread-avatar hidden-phone"><?php bp_message_thread_avatar() ?></td>

				<?php if ( 'sentbox' != bp_current_action() ) : ?>
					<td width="30%" class="thread-from">
						<?php _e( 'From:', 'cc' ); ?> <?php bp_message_thread_from() ?><br />
						<span class="activity"><?php bp_message_thread_last_post_date() ?></span>
					</td>
				<?php else: ?>
					<td width="28%" class="thread-from">
						<?php _e( 'To:', 'cc' ); ?> <?php bp_message_thread_to() ?><br />
						<span class="activity"><?php bp_message_thread_last_post_date() ?></span>
					</td>
				<?php endif; ?>

				<td width="40%" class="thread-info">
					<p><a href="<?php bp_message_thread_view_link() ?>" title="<?php _e( "View Message", "cc" ); ?>"><?php bp_message_thread_subject() ?></a></p>
					<p class="thread-excerpt"><?php bp_message_thread_excerpt() ?></p>
				</td>

				<?php do_action( 'bp_messages_inbox_list_item' ) ?>

				<td width="27%" class="thread-options">
					<input type="checkbox" name="message_ids[]" value="<?php bp_message_thread_id() ?>" />
					<a class="button confirm" href="<?php bp_message_thread_delete_link() ?>" title="<?php _e( "Delete Message", "cc" ); ?>"><?php _e( 'Delete', 'cc' ) ?></a> &nbsp;
				</td>
			</tr>

		<?php endwhile; ?>
	</table><!-- #message-threads -->

	<div class="messages-options-nav">
		<?php bp_messages_options() ?>
	</div><!-- .messages-options-nav -->

	<?php do_action( 'bp_after_member_messages_threads' ) ?>

	<?php do_action( 'bp_after_member_messages_options' ) ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no messages were found.', 'cc' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_member_messages_loop' ) ?>