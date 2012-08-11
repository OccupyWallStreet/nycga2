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
		<li id="event-<?php bpe_event_id() ?>" class="vevent <?php bpe_event_status_class() ?>">

			<?php bpe_load_template( 'events/includes/single-nav' ); ?>
			
			<div class="item">
				<div class="item-title"><?php printf( __( 'Add yourself to <span class="summary">%s</span>', 'events' ), bpe_get_event_name() ) ?></div>
				
				<?php bpe_load_template( 'events/includes/event-header' ); ?>                
                
				<form id="self-signup" name="self-signup" action="" method="post" class="standard-form">
                	
                    <?php wp_nonce_field( 'bpe_member_self_signup' ) ?>
                    
                    <h4><?php _e( 'Note:', 'events' ) ?></h4>
                    <p><?php printf( __( 'You can only sign yourself up to this event if you received your tickets on an email address other than the one you used to register on %s.', 'events' ), get_bloginfo( 'name' ) ) ?></p>
                    
                    <input type="hidden" id="hash" name="hash" value="<?php echo bp_action_variable( 2 ) ?>" />

                    <label for="event_email"><?php _e( 'Email', 'events' ) ?></label>
                    <input type="text" id="event_email" name="event_email" value="" />
                    
               		<input type="submit" id="event-signup" name="event-signup" value="<?php _e( 'Sign Up', 'events' ) ?>" />
                </form>
		   </div>
		</li>
	</ul>

<?php endwhile; ?>
<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No events were found.', 'events' ) ?></p>
	</div>

<?php endif; ?>