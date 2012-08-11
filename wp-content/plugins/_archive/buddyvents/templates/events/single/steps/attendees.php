<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
 
do_action( 'bpe_attendee_edit_before_all' );

if( bpe_is_manual_attendee_enabled() || bpe_are_tickets_enabled() ) :
	wp_nonce_field( 'bpe_edit_event_'. bpe_get_option( 'manage_slug' ) );
	?>
	<div class="bp-widget">
		<label for="invitations"><?php _e( 'Manually Add Attendees', 'events' ) ?> &nbsp; <span class="ajax-loader"></span></label>
		<ul class="first acfb-holder">
			<li>
				<?php bp_message_get_recipient_tabs() ?>
				<input type="text" name="invitations" class="send-to-input" id="send-to-input" />
			</li>
		</ul>
		
		<input type="hidden" name="send_to_usernames" id="send-to-usernames" value="<?php bp_message_get_recipient_usernames(); ?>" class="<?php bp_message_get_recipient_usernames() ?>" />
		<small><?php _e( 'These users will be added automatically and an email notifying them will be sent.', 'events' ) ?></small>
		
		<div class="dir-submit">
			<input type="submit" class="button" value="<?php _e( 'Add attendees', 'events' ) ?>" id="add-attendee" name="add-attendee" />
		</div>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		var acfb = jQuery("ul.first").autoCompletefb({urlLookup: ajaxurl});
		jQuery('#edit-event-form').submit( function() {
			var users = document.getElementById('send-to-usernames').className;
			document.getElementById('send-to-usernames').value = String(users);
		});
	});
	</script>
	<hr />
    
    <?php do_action( 'bpe_attendee_edit_after_manual_atttendees' ) ?>
    
<?php endif; ?>

<div class="bp-widget">
    <h4><?php _e( 'Admins', 'events' ); ?></h4>
    <?php bpe_display_event_members( bpe_get_displayed_event(), 'admins' ) ?>                            
</div>

<?php do_action( 'bpe_attendee_edit_after_admins' ) ?>

<?php if( bpe_event_has_organizers( bpe_get_displayed_event() ) ) : ?>

    <div class="bp-widget">
        <h4><?php _e( 'Organizers', 'events' ) ?></h4>
        <?php bpe_display_event_members( bpe_get_displayed_event(), 'organizers' ) ?>
    </div>
    
    <?php do_action( 'bpe_attendee_edit_after_organizers' ) ?>

<?php endif; ?>

<div class="bp-widget">
    <h4><?php _e( 'Attendees', 'events' ); ?></h4>
    <?php bpe_display_event_members( bpe_get_displayed_event(), 'attendees' ) ?>
</div>

<?php do_action( 'bpe_attendee_edit_after_attendees' ) ?>

<div class="bp-widget">
    <h4><?php _e( 'Undecided Attendees', 'events' ); ?></h4>
    <?php bpe_display_event_members( bpe_get_displayed_event(), 'maybe' ) ?>
</div>

<?php do_action( 'bpe_attendee_edit_after_undecided' ) ?>

<div class="bp-widget">
    <h4><?php _e( 'Not Attending', 'events' ); ?></h4>
    <?php bpe_display_event_members( bpe_get_displayed_event(), 'not_attending' ) ?>
</div>

<?php do_action( 'bpe_attendee_edit_after_not_attending' ) ?>