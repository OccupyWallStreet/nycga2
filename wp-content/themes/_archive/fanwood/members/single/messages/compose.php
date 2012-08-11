<?php

/**
 * BuddyPress - Single Member Compose Message
 *
 * @package BuddyPress
 * @subpackage Theme
 */
 
?>

<div class="entry-content">

	<form action="<?php bp_messages_form_action('compose') ?>" method="post" id="send_message_form" class="standard-form" role="main">

		<?php do_action( 'bp_before_messages_compose_content' ) ?>

		<div id="send-to">
			<label for="send-to-input"><?php _e("Send To (Username or Friend's Name)", 'buddypress') ?></label>
			<ul class="first acfb-holder">
				<li>
					<?php bp_message_get_recipient_tabs() ?>
					<input type="text" name="send-to-input" class="send-to-input" id="send-to-input" />
				</li>
			</ul>
		</div>

		<?php if ( is_super_admin() ) : ?>
		<p>
			<input type="checkbox" id="send-notice" name="send-notice" value="1" /> <?php _e( "This is a notice to all users.", "buddypress" ) ?>
		</p>
		<?php endif; ?>

		<p>
			<label for="subject"><?php _e( 'Subject', 'buddypress') ?></label><br />
			<input type="text" name="subject" id="subject" value="<?php bp_messages_subject_value() ?>" />
		</p>

		<p>
			<label for="content"><?php _e( 'Message', 'buddypress') ?></label><br />
			<textarea name="content" id="message_content" rows="15" cols="40"><?php bp_messages_content_value() ?></textarea>
		</p>

		<input type="hidden" name="send_to_usernames" id="send-to-usernames" value="<?php bp_message_get_recipient_usernames(); ?>" class="<?php bp_message_get_recipient_usernames() ?>" />

		<?php do_action( 'bp_after_messages_compose_content' ) ?>

		<p class="submit">
			<input type="submit" value="<?php _e( "Send Message", 'buddypress' ) ?>" name="send" id="send" />
		</p>

		<?php wp_nonce_field( 'messages_send_message' ) ?>
	</form>

	<script type="text/javascript">
		document.getElementById("send-to-input").focus();
	</script>

</div><!-- .entry-content -->

