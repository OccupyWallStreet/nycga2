<?php do_action( 'bp_before_event_send_invites_content' ) ?>

<?php if ( bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>

	<form action="<?php bp_event_send_invite_form_action() ?>" method="post" id="send-invite-form" class="standard-form">

		<div class="left-menu">

			<div id="event-invite-list">
				<ul>
					<?php bp_new_jes_event_invite_friend_list() ?>
				</ul>

				<?php wp_nonce_field( 'events_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ) ?>
			</div>

		</div><!-- .left-menu -->

		<div class="main-column">

			<div id="message" class="info">
				<p><?php _e('Select people to invite from your friends list.', 'jet-event-system'); ?></p>
			</div>

			<?php do_action( 'bp_before_event_send_invites_list' ) ?>

			<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
			<ul id="event-friend-list" class="item-list">
			<?php if ( bp_event_has_invite_jes() ) : ?>
				<?php while ( bp_jes_event_invite_jes() ) : bp_event_the_invite(); ?>
					<li id="<?php bp_jes_event_invite_item_id() ?>">

						<?php bp_jes_event_invite_user_avatar() ?>

						<h4><?php bp_jes_event_invite_user_link() ?></h4>
						<span class="activity"><?php bp_jes_event_invite_user_last_active() ?></span>

						<?php do_action( 'bp_event_send_invites_item' ) ?>

						<div class="action">
							<a class="remove" href="<?php bp_jes_event_invite_user_remove_invite_url() ?>" id="<?php bp_jes_event_invite_item_id() ?>"><?php _e( 'Remove Invite', 'jet-event-system' ) ?></a>

							<?php do_action( 'bp_event_send_invites_item_action' ) ?>
						</div>
					</li>

				<?php endwhile; ?>

			<?php endif; ?>
			</ul><!-- #friend-list -->

			<?php do_action( 'bp_after_event_send_invites_list' ) ?>

		</div><!-- .main-column -->

		<div class="clear"></div>

		<div class="submit">
			<input type="submit" name="submit" id="submit" value="<?php _e( 'Send Invites', 'jet-event-system' ) ?>" />
		</div>

		<?php wp_nonce_field( 'events_send_invites', '_wpnonce_send_invites') ?>

		<?php /* This is important, don't forget it */ ?>
		<input type="hidden" name="event_id" id="event_id" value="<?php jes_bp_event_id() ?>" />

	</form><!-- #send-invite-form -->

<?php else : ?>

	<div id="message" class="info">
		<p><?php _e( 'Once you have built up friend connections you will be able to invite others to your event. You can send invites any time in the future by selecting the "Send Invites" option when viewing your new event.', 'jet-event-system' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_event_send_invites_content' ) ?>
