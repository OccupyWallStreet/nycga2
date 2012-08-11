<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add the Eventbrite option to the settings page
 * 
 * Attached to the <code>bpe_event_settings_action_end</code> action hook
 * 
 * @package Eventbrite
 * @since 	1.6
 * 
 * @param 	mixed 	$user 	Either int or object
 * 
 * @uses 	bpe_get_option()
 * @uses 	bp_get_user_meta()
 */
function bpe_eventbrite_add_user_key( $user )
{
	if( is_object( $user ) )
		$user_id = $user->data->ID;
	else
		$user_id = $user;
	
	if( bpe_get_option( 'eventbrite_appkey' ) )
	{
		$user_key = bp_get_user_meta( $user_id, 'bpe_eventbrite_user_key', true );
        ?>
        <h4><?php _e( 'Eventbrite Settings', 'events' ); ?></h4>
        <p>
            <?php _e( 'Enter your user key:', 'events' ); ?><br />
            <input type="text" name="eb_user_key" id="eb_user_key" value="<?php echo $user_key ?>" />
        </p>
        <?php
	}
}
add_action( 'bpe_event_settings_action_end', 'bpe_eventbrite_add_user_key' );

/**
 * Save Eventbrite options
 * 
 * Attached to the <code>bpe_event_settings_save_extra</code> action hook
 * 
 * @package Eventbrite
 * @since 	1.6
 * 
 * @param 	int 	$user_id 	The current user
 * 
 * @uses 	bp_get_user_meta()
 */
function bpe_eventbrite_save_extra_data( $user_id )
{
	if( ! empty( $_POST['eb_user_key'] ) )
		bp_update_user_meta( $user_id, 'bpe_eventbrite_user_key', $_POST['eb_user_key'] );	
}
add_action( 'bpe_event_settings_save_extra', 'bpe_eventbrite_save_extra_data' );

/**
 * Add the eventbrite option to create page
 * 
 * Attached to the <code>bpe_add_to_create_page</code> action hook
 * 
 * @package Eventbrite
 * @since 	1.6
 * 
 * @uses 	bpe_get_option()
 * @uses 	bp_get_user_meta()
 * @uses 	bp_loggedin_user_id()
 */
function bpe_eventbrite_add_to_create()
{
	$user_key = bp_get_user_meta( bp_loggedin_user_id(), 'bpe_eventbrite_user_key', true );
	
	if( bpe_get_option( 'eventbrite_appkey' ) && ! empty( $user_key ) )
	{
		?>
		<label for="send_to_eventbrite"><input type="checkbox" name="send_to_eventbrite" value="1" /> <?php _e( 'Check to publish this event on Eventbrite.', 'events' ) ?></label>
		<?php
	}
}
add_action( 'bpe_add_to_create_page', 'bpe_eventbrite_add_to_create' );

/**
 * Send the event to eventbrite
 * 
 * Attached to the <code>bpe_event_is_publishable</code> action hook
 * 
 * @package Eventbrite
 * @since 	1.6
 * 
 * @param 	object 	$event 	Buddyvents event settings
 * 
 * @uses 	bpe_update_eventmeta()
 * @uses 	bpe_get_event_id()
 */
function bpe_eventbrite_add_event_meta( $event )
{
	if( isset( $_POST['send_to_eventbrite'] ) )
		bpe_update_eventmeta( bpe_get_event_id( $event ), 'post_to_eventbrite', 'yes' );
}
add_action( 'bpe_event_is_publishable', 'bpe_eventbrite_add_event_meta' );

/**
 * Send the event to eventbrite
 * 
 * Attached to the <code>bpe_saved_new_event</code> action hook
 * 
 * @package Eventbrite
 * @since 	1.6
 * 
 * @param 	object 	$event 	Buddyvents event settings
 * 
 * @uses 	bpe_get_eventmeta()
 * @uses 	bpe_get_event_id()
 */
function bpe_eventbrite_process_send( $event )
{
	$process = true;
	
	if( bpe_get_eventmeta( bpe_get_event_id( $event ), 'post_to_eventbrite' ) == 'yes' )
		$process = true;
	
	if( $process )
	{
		require_once( EVENT_ABSPATH .'components/eventbrite/bpe-eventbrite-api.php' );
		
		$eventbrite = new Buddyvents_API_Eventbrite( $event, 'event_new' );
		$response = $eventbrite->request();
	}	
}
add_action( 'bpe_saved_new_event', 'bpe_eventbrite_process_send' );

/**
 * Send updated event to eventbrite
 * 
 * Attached to the <code>bpe_edited_event_action</code> action hook
 * 
 * @package Eventbrite
 * @since 	1.6
 * 
 * @param 	object 	$event 	Buddyvents event settings
 * @uses 	bp_loggedin_user_id()
 * @uses 	bpe_get_event_user_id()
 * @uses 	bpe_get_eventmeta()
 * @uses 	bpe_get_event_id()
 */
function bpe_eventbrite_update_event( $event )
{
	// only the event admin can update an eventbrite event
	if( bp_loggedin_user_id() != bpe_get_event_user_id( $event ) )
		return false;
		
	if( bpe_get_eventmeta( bpe_get_event_id( $event ), 'post_to_eventbrite' ) == 'yes' )
	{
		require_once( EVENT_ABSPATH .'components/eventbrite/bpe-eventbrite-api.php' );
		
		$eventbrite = new Buddyvents_API_Eventbrite( $event, 'event_update' );
		$eventbrite->request();
	}	
}
add_action( 'bpe_edited_event_action', 'bpe_eventbrite_update_event' );
?>