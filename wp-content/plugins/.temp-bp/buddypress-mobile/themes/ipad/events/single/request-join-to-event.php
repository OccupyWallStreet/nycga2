<?php do_action( 'bp_before_event_request_membership_content' ) ?>

<?php if ( !bp_event_has_requested_membership() ) : ?>
	<p><?php printf( __( "You are requesting to become a member of the event '%s'.", "jet-event-system" ), jes_bp_get_event_name( false ) ); ?></p>

	<form action="<?php bp_event_form_action('request-join-to-event') ?>" method="post" name="request-join-to-event-form" id="request-join-to-event-form" class="standard-form">
		<label for="event-request-join-to-event-comments"><?php _e( 'Comments (optional)', 'jet-event-system' ); ?></label>
		<textarea name="event-request-join-to-event-comments" id="event-request-join-to-event-comments"></textarea>

		<?php do_action( 'bp_event_request_membership_content' ) ?>

		<p><input type="submit" name="event-request-send" id="event-request-send" value="<?php _e( 'Send Request', 'jet-event-system' ) ?> &rarr;" />

		<?php wp_nonce_field( 'events_request_join_to_event' ) ?>
	</form><!-- #request-join-to-event-form -->
<?php endif; ?>

<?php do_action( 'bp_after_event_request_membership_content' ) ?>
