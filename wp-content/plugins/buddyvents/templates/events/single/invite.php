<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if( bpe_has_events() ) :  while ( bpe_events() ) : bpe_the_event(); ?>

	<?php if( bpe_has_single_nav() ) : ?>
	
		<div class="single-nav">
			<div class="previous-event">
				<?php bpe_previous_event_link() ?>
			</div>
	
			<div class="next-event">
				<?php bpe_next_event_link() ?>
			</div>
		</div>
	
	<?php endif; ?>	
	
	<ul id="events-list" class="item-list single-event">
	
		<li id="event-<?php bpe_event_id() ?>" class="<?php bpe_event_status_class() ?>">
	
			<?php bpe_load_template( 'events/includes/single-nav' ); ?> 
			
			<div class="item">
				<div class="item-title"><?php printf( __( 'Invite your friends to %s', 'events' ), bpe_get_event_name() ) ?></div>
				
				<?php bpe_load_template( 'events/includes/event-header' ); ?> 
                
				<form action="" method="post" id="invite-event-form" class="standard-form">
				
					<?php wp_nonce_field( 'bpe_invite_members_event' ) ?>
	
					<label for="invitations"><?php _e( 'Start typing a members name', 'events' ) ?> &nbsp; <span class="ajax-loader"></span></label>
					<ul class="first acfb-holder">
						<li>
							<?php bp_message_get_recipient_tabs() ?>
							<input type="text" name="invitations" class="send-to-input" id="send-to-input" />
						</li>
					</ul>
					
					<?php do_action( 'bpe_user_invitation_step' ) ?>
	
					<input type="hidden" name="send_to_usernames" id="send-to-usernames" value="<?php bp_message_get_recipient_usernames(); ?>" class="<?php bp_message_get_recipient_usernames() ?>" />
					
                    <?php if( bpe_is_admin() && bpe_attached_to_group() ) : ?>
                    <label for="notify_group"><input type="checkbox" name="notify_group" id="notify_group" value="1" /> <?php _e( 'Notify all group members.', 'events' ) ?></label>
                    <?php endif; ?>
                    
					<div class="dir-submit">
						<input type="submit" value="<?php _e( 'Send Invitations', 'events' ) ?>" id="send-invites" name="send-invites" />
					</div>
			
				</form>              
	
				<script type="text/javascript">
					jQuery(document).ready(function() {
						var acfb = jQuery("ul.first").autoCompletefb({urlLookup: ajaxurl});
						jQuery('#invite-event-form').submit( function() {
							var users = document.getElementById('send-to-usernames').className;
							document.getElementById('send-to-usernames').value = String(users);
						});
					});
				</script>
		   </div>
			
			<div class="action">
				<span class="activity"><?php bpe_event_attendees() ?></span>
				<span class="event-admin"><?php _e( 'Creator:', 'events' ) ?><br /><?php bpe_event_user_avatar() ?></span>
			</div>
		</li>
	</ul>
	<?php endwhile;
else:
?>
	<div id="message" class="info">
		<p><?php _e( 'No events were found.', 'events' ) ?></p>
	</div>

<?php endif; ?>