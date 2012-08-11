<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

wp_nonce_field( 'bpe_add_event_'. bpe_get_option( 'invite_slug' ) ) ?>

<?php if( bpe_event_needs_group_approval( bpe_get_displayed_event() ) ) : ?>
	<div id="message" class="updated">
		<p><?php _e( 'Invitations have been disabled until your event has been approved by a group admin.', 'events' ) ?></p>
	</div>
<?php else : ?>
	<label for="invitations"><?php _e( 'Start typing a members name', 'events' ) ?> &nbsp; <span class="ajax-loader"></span></label>
	<ul class="first acfb-holder">
		<li>
			<?php bp_message_get_recipient_tabs() ?>
			<input type="text" name="invitations" class="send-to-input" id="send-to-input" />
		</li>
	</ul>
	
	<?php do_action( 'bpe_user_invitation_step' ) ?>
	
	<input type="hidden" name="send_to_usernames" id="send-to-usernames" value="<?php bp_message_get_recipient_usernames(); ?>" class="<?php bp_message_get_recipient_usernames() ?>" />
	
	<?php if( bpe_attached_to_group( bpe_get_displayed_event() ) ) : ?>
	<label for="notify_group"><input type="checkbox" name="notify_group" id="notify_group" value="1" /> <?php _e( 'Notify all group members.', 'events' ) ?></label>
	<?php endif; ?>
	
	<script type="text/javascript">
	jQuery(document).ready(function() {
		var acfb = jQuery("ul.first").autoCompletefb({urlLookup: ajaxurl});
		jQuery('#create-event-form').submit( function() {
			var users = document.getElementById('send-to-usernames').className;
			document.getElementById('send-to-usernames').value = String(users);
		});
	});
	</script>
<?php endif; ?>