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
 * Display secondary activity avatars
 * 
 * @package Core
 * @since 	1.7
 * @todo 	fix the many db calls
 * 
 * @uses 	bpe_get_base()
 * @uses 	bp_core_fetch_avatar()
 * @param 	string 	$action
 * @param 	object 	$activity
 * @global 	object 	$activities_template
 * @return 	string 	$action
 */
function bpe_activity_secondary_avatars( $action, $activity )
{
	global $activities_template;
	
	if( $activity->component == bpe_get_base( 'slug' ) )
	{
		if( $secondary_avatar = bp_core_fetch_avatar( array( 'item_id' => $activities_template->activity->item_id, 'object' => 'event', 'avatar_dir' => 'event-avatars', 'type' => 'thumb', 'alt' => __( 'Event logo', 'events' ), 'class' => 'avatar', 'width' => 20, 'height' => 20, 'no_grav' => true ) ) )
		{
			if( ! bpe_get_option( 'enable_logo' ) )
				$secondary_avatar = '';

			$reverse_content = strrev( $action );
			$position        = strpos( $reverse_content, 'a<' );
			$action          = substr_replace( $action, $secondary_avatar, -$position - 2, 0 );
		}
	}
	
	return $action;
}
add_filter( 'bp_get_activity_action_pre_meta', 'bpe_activity_secondary_avatars', 10, 2 );
 
/**
 * Add activity comments for single view events
 * 
 * Attached to the <code>bpe_end_single_event_action</code> action hook
 * on single event home views
 * 
 * @package Core
 * @since 	1.1
 * 
 * @param 	int 	$event_id
 * @param 	int 	$user_id
 * @uses 	bpe_load_template()
 */
function bpe_add_event_activity( $event_id, $user_id )
{
	bpe_load_template( 'events/single/activity' );
}
add_action( 'bpe_end_single_event_action', 'bpe_add_event_activity', 10, 2 );

/**
 * Record an event activity item
 * 
 * Wrapper function for <code>bp_activity_add</code> that adds predefined 
 * default values
 * 
 * @package Core
 * @since 	1.0
 * 
 * @param 	array 	$args
 * @uses 	bp_loggedin_user_id()
 * @uses 	bpe_get_base()
 * @uses 	bp_core_current_time()
 * @uses 	wp_parse_args()
 * @uses 	bp_activity_add()
 * @return 	boolean
 */
function bpe_record_activity( $args = '' )
{
	if( ! function_exists( 'bp_activity_add' ) )
		return false;
		
	$defaults = array(
		'id' 				=> false,
		'user_id' 			=> bp_loggedin_user_id(),
		'action' 			=> '',
		'content'			=> '',
		'primary_link' 		=> '',
		'component' 		=> bpe_get_base( 'slug' ),
		'type' 				=> false,
		'item_id' 			=> false,
		'secondary_item_id' => false,
		'recorded_time' 	=> bp_core_current_time(),
		'hide_sitewide' 	=> false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	return bp_activity_add( array( 'id' => $id, 'user_id' => $user_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $component, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'recorded_time' => $recorded_time, 'hide_sitewide' => $hide_sitewide ) );
}

/**
 * Add an activity entry for a new event
 * 
 * Attached to the <code>bpe_saved_new_event</code> action hook
 * 
 * @package Core
 * @since 	1.0
 * 
 * @param 	object 	$event 		Buddyvents event settings
 * @uses 	bpe_get_event_public()
 * @uses 	bpe_get_event_link()
 * @uses 	bpe_record_activity()
 * @uses 	bpe_get_event_user_id()
 * @uses 	apply_filters()
 * @uses 	bp_create_excerpt()
 * @uses 	bpe_get_event_description_excerpt_raw()
 */
function bpe_add_new_event_activity( $event )
{
	$hide_show = ( bpe_get_event_public( $event ) == 0 ) ? true : false;
	$event_link = bpe_get_event_link( $event );
	
	bpe_record_activity( array(	
		'user_id' 		=> bpe_get_event_user_id( $event ),
		'action' 		=> apply_filters( 'bpe_activity_action_new_event', sprintf( __( '%1$s published the event <a href="%2$s">%3$s</a> starting on %4$s at %5$s', 'events' ), bp_core_get_userlink( bpe_get_event_user_id( $event ) ), $event_link, bpe_get_event_name( $event ), bpe_get_event_start_date( $event ), bpe_get_event_start_time( $event ) ), $event_link, $event ),
		'content' 		=> bp_create_excerpt( bpe_get_event_description_excerpt_raw( $event ) ),
		'primary_link' 	=> $event_link,
		'type' 			=> 'new_event',
		'item_id' 		=> bpe_get_event_id( $event ),
		'hide_sitewide' => $hide_show
	) );
}
add_action( 'bpe_saved_new_event', 'bpe_add_new_event_activity' );

/**
 * Add an activity entry when a user joins an event
 * 
 * Attached to the <code>bpe_attend_event</code> action hook
 * 
 * @package Core
 * @since 	1.0
 * 
 * @param 	object 	$event 		Buddyvents event settings
 * @param 	int 	$user_id 	The user to attach this activity to
 * @uses bp_loggedin_user_id()
 * @uses bpe_get_event_public()
 * @uses bpe_get_event_link()
 * @uses bpe_record_activity()
 * @uses apply_filters()
 * @uses bpe_get_event_id()
 */
function bpe_add_join_event_activity( $event, $user_id = false )
{
	if( !$user_id )
		$user_id = bp_loggedin_user_id();
	
	$hide_show = ( bpe_get_event_public( $event ) == 0 ) ? true : false;
	$event_link = bpe_get_event_link( $event );
	
	bpe_record_activity( array(	
		'user_id' 		=> $user_id,
		'action' 		=> apply_filters( 'bpe_activity_action_attend_event', sprintf( __( '%1$s is attending the event <a href="%2$s">%3$s</a>', 'events' ), bp_core_get_userlink( $user_id ), $event_link, bpe_get_event_name( $event ) ), $event_link, $event ),
		'primary_link' 	=> $event_link,
		'type' 			=> 'attend_event',
		'item_id' 		=> bpe_get_event_id( $event ),
		'hide_sitewide' => $hide_show
	) );
}
add_action( 'bpe_attend_event', 'bpe_add_join_event_activity', 10, 2 );

/**
 * Add an activity entry when a user might join an event
 * 
 * Attached to the <code>bpe_maybe_attend_event</code> action hook
 * 
 * @package Core
 * @since 	1.0
 * 
 * @param 	object 	$event 		Buddyvents event settings
 * @param 	int 	$user_id 	The user to attach this activity to
 * @uses 	bp_loggedin_user_id()
 * @uses 	bpe_get_event_public()
 * @uses 	bpe_get_event_link()
 * @uses 	bpe_record_activity()
 * @uses 	apply_filters()
 * @uses 	bpe_get_event_id() 
 */
function bpe_add_maybe_join_event_activity( $event, $user_id = false )
{
	if( !$user_id )
		$user_id = bp_loggedin_user_id();
	
	$hide_show = ( bpe_get_event_public( $event ) == 0 ) ? true : false;
	$event_link = bpe_get_event_link( $event );
	
	bpe_record_activity( array(	
		'user_id' 		=> $user_id,
		'action' 		=> apply_filters( 'bpe_activity_action_maybe_attend_event', sprintf( __( '%1$s might attend the event <a href="%2$s">%3$s</a>', 'events' ), bp_core_get_userlink( $user_id ), $event_link, bpe_get_event_name( $event ) ), $event_link, $event ),
		'primary_link' 	=> $event_link,
		'type' 			=> 'maybe_attend_event',
		'item_id' 		=> bpe_get_event_id( $event ),
		'hide_sitewide' => $hide_show
	) );
}
add_action( 'bpe_maybe_attend_event', 'bpe_add_maybe_join_event_activity', 10, 2 );

/**
 * Add an activity entry when a user comments on an event
 * 
 * @package Core
 * @since 	2.1
 * 
 * @param 	mixed 	$event 			Buddyvents event settings
 * @param 	string 	$comment_text	The comment content
 * @param 	int 	$user_id 		The user to attach this activity to
 * @uses 	bpe_get_event_public()
 * @uses 	bpe_get_events()
 * @uses 	bp_loggedin_user_id()
 * @uses 	bpe_record_activity()
 * @uses 	apply_filters()
 * @uses 	do_action()
 */
function bpe_add_event_comment( $event, $comment_text, $user_id = false )
{
	$hide_show = ( bpe_get_event_public( $event ) == 0 ) ? true : false;
	
	if( ! is_object( $event ) ) :
		$result = bpe_get_events( array( 'ids' => $event, 'future' => false, 'past' => false ) );
		$event  = $result['events'][0];
	endif;
	
	if( ! $user_id )
		$user_id = bp_loggedin_user_id();

	bpe_record_activity( array(
		'action' 		=> apply_filters( 'bpe_add_event_comment', sprintf( __( '%1$s posted a new comment on <a href="%2$s">%3$s</a>:', 'events' ), bp_core_get_userlink( $user_id ), bpe_get_event_link( $event ), bpe_get_event_name( $event ) ), $event, $comment_text, $user_id ),
		'hide_sitewide' => $hide_show, 
		'type' 			=> 'event_comment', 
		'content' 		=> $comment_text, 
		'item_id' 		=> $event->id
	) );
	
	do_action( 'bpe_send_comment', $event );
}

/**
 * Add an activity entry when a user removes himself from an event
 * 
 * Attached to the <code>bpe_not_attending_event</code> action hook
 * 
 * @package Core
 * @since 	1.0
 * 
 * @param 	object 	$event 		Buddyvents event settings
 * @param 	int 	$user_id 	The user to attach this activity to
 * @uses 	bp_loggedin_user_id()
 * @uses 	bpe_get_event_public()
 * @uses 	bpe_get_event_link()
 * @uses 	bpe_record_activity()
 * @uses 	apply_filters()
 * @uses 	bpe_get_event_id()
 */
function bpe_add_remove_event_activity( $event, $user_id = false )
{
	if( ! $user_id )
		$user_id = bp_loggedin_user_id();
	
	$hide_show = ( bpe_get_event_public( $event ) == 0 ) ? true : false;
	$event_link = bpe_get_event_link( $event );
	
	bpe_record_activity( array(	
		'user_id' 		=> $user_id,
		'action' 		=> apply_filters( 'bpe_activity_action_remove_event', sprintf( __( '%1$s is no longer attending the event <a href="%2$s">%3$s</a>', 'events' ), bp_core_get_userlink( $user_id ), $event_link, bpe_get_event_name( $event ) ), $event_link, $event ),
		'primary_link' 	=> $event_link,
		'type' 			=> 'remove_from_event',
		'item_id' 		=> bpe_get_event_id( $event ),
		'hide_sitewide' => $hide_show
	) );
}
add_action( 'bpe_not_attending_event', 'bpe_add_remove_event_activity', 10, 2 );

/**
 * Add an activity entry when an event has been edited
 * 
 * Attached to the <code>bpe_edited_event_action</code> action hook
 * 
 * @package Core
 * @since 	1.0
 * 
 * @param 	object $event Buddyvents event settings
 * @uses 	bp_activity_get_activity_id()
 * @uses 	bpe_get_event_user_id()
 * @uses 	bpe_get_base()
 * @uses 	bpe_get_event_public()
 * @uses 	bpe_get_event_link()
 * @uses 	bpe_record_activity()
 * @uses 	apply_filters()
 * @uses 	bpe_get_event_id()
 */
function bpe_add_edited_event_activity( $event )
{
	$hide_show = ( bpe_get_event_public( $event ) == 0 ) ? true : false;
	$event_link = bpe_get_event_link( $event );
	
	$act_id =  bp_activity_get_activity_id( array(
		'user_id' 	=> bpe_get_event_user_id( $event ),
		'component' => bpe_get_base( 'slug' ),
		'type' 		=> 'edited_event',
		'item_id' 	=> bpe_get_event_id( $event )
	) );
	
	$id = ( is_numeric( $act_id ) ) ? $act_id : false;
	
	bpe_record_activity( array(	
		'id' 			=> $act_id,
		'action' 		=> apply_filters( 'bpe_activity_action_remove_event', sprintf( __( '%1$s has edited the event <a href="%2$s">%3$s</a>', 'events' ), bp_core_get_userlink(  bpe_get_event_user_id( $event )  ), $event_link, bpe_get_event_name( $event ) ), $event_link, $event ),
		'primary_link' 	=> $event_link,
		'type' 			=> 'edited_event',
		'item_id' 		=> bpe_get_event_id( $event ),
		'hide_sitewide' => $hide_show
	) );
}
add_action( 'bpe_edited_event_action', 'bpe_add_edited_event_activity' );

/**
 * Add an activity entry when an event has a new logo
 * 
 * Attached to the <code>bpe_new_event_logo</code> action hook
 * 
 * @package Core
 * @since 	1.7
 * 
 * @param 	object $event Buddyvents event settings
 * @uses 	bp_activity_get_activity_id()
 * @uses 	bpe_get_event_user_id()
 * @uses 	bpe_get_base()
 * @uses 	bpe_get_event_public()
 * @uses 	bpe_get_event_link()
 * @uses 	bpe_record_activity()
 * @uses 	apply_filters()
 * @uses 	bpe_get_event_id()
 */
function bpe_add_new_event_logo_activity( $event )
{
	$hide_show = ( bpe_get_event_public( $event ) == 0 ) ? true : false;
	$event_link = bpe_get_event_link( $event );
	
	$act_id =  bp_activity_get_activity_id( array(
		'user_id' 	=> bpe_get_event_user_id( $event ),
		'component' => bpe_get_base( 'slug' ),
		'type' 		=> 'new_event_logo',
		'item_id' 	=> bpe_get_event_id( $event )
	) );
	
	$id = ( is_numeric( $act_id ) ) ? $act_id : false;
	
	bpe_record_activity( array(	
		'id' 			=> $act_id,
		'action' 		=> apply_filters( 'bpe_activity_action_new_logo', sprintf( __( '%1$s has uploaded a new event logo for <a href="%2$s">%3$s</a>', 'events' ), bp_core_get_userlink( bpe_get_event_user_id( $event ) ), $event_link, bpe_get_event_name( $event ) ), $event_link, $event ),
		'primary_link' 	=> $event_link,
		'type' 			=> 'new_event_logo',
		'item_id' 		=> bpe_get_event_id( $event ),
		'hide_sitewide' => $hide_show
	) );
}
add_action( 'bpe_new_event_logo', 'bpe_add_new_event_logo_activity' );

/**
 * Add an activity entry when an event gets cancelled
 * 
 * Attached to the <code></code> action hook
 * 
 * @package Core
 * @since 	2.1
 * 
 * @param 	object $event Buddyvents event settings
 * @uses 	bp_activity_get_activity_id()
 * @uses 	bpe_get_event_user_id()
 * @uses 	bpe_get_base()
 * @uses 	bpe_get_event_public()
 * @uses 	bpe_get_event_link()
 * @uses 	bpe_record_activity()
 * @uses 	apply_filters()
 * @uses 	bpe_get_event_id()
 *  */
function bpe_add_cancelled_event_activity( $event )
{
	$hide_show = ( bpe_get_event_public( $event ) == 0 ) ? true : false;
	$event_link = bpe_get_event_link( $event );
	
	$act_id =  bp_activity_get_activity_id( array(
		'user_id' 	=> bpe_get_event_user_id( $event ),
		'component' => bpe_get_base( 'slug' ),
		'type' 		=> 'cancelled_event',
		'item_id' 	=> bpe_get_event_id( $event )
	) );
	
	$id = ( is_numeric( $act_id ) ) ? $act_id : false;
	
	bpe_record_activity( array(	
		'id' 			=> $act_id,
		'action' 		=> apply_filters( 'bpe_activity_action_cancelled_event', sprintf( __( '%1$s has cancelled the event <a href="%2$s">%3$s</a>', 'events' ), bp_core_get_userlink( bpe_get_event_user_id( $event ) ), $event_link, bpe_get_event_name( $event ) ), $event_link, $event ),
		'primary_link' 	=> $event_link,
		'type' 			=> 'cancelled_event',
		'item_id' 		=> bpe_get_event_id( $event ),
		'hide_sitewide' => $hide_show
	) );
}
add_action( 'bpe_cancel_event_action', 'bpe_add_cancelled_event_activity' );


/**
 * Delete all activity for an event if event has been deleted
 * 
 * Attached to the <code>bpe_delete_event_action</code> action hook
 * 
 * @package Core
 * @since 	1.0
 * 
 * @uses 	bp_activity_delete()
 * @uses 	bpe_get_base()
 * @uses 	bpe_get_event_id()
 * @param 	object $event Buddyvents event settings
 */
function bpe_delete_activity( $event )
{
	bp_activity_delete( array( 
		'component' => bpe_get_base( 'slug' ),
		'item_id' 	=> bpe_get_event_id( $event )
	) );
}
add_action( 'bpe_delete_event_action', 'bpe_delete_activity' );

/**
 * Add new events to activity filters
 * 
 * Attached to the <code>bp_activity_filter_options</code> and <code>bp_member_activity_filter_options</code>
 * action hooks. The result can be filtered using the bpe_activity_filters filter
 * 
 * @package Core
 * @since 	1.0
 * 
 * @uses 	bpe_get_option()
 * @uses 	apply_filters()
 */
function bpe_add_activity_filter()
{
    $filter  = '<option value="new_event">'. __( 'New event created', 'events' ) .'</option>';
    $filter .= '<option value="edited_event">'. __( 'Event updated', 'events' ) .'</option>';
    $filter .= '<option value="cancelled_event">'. __( 'Event cancelled', 'events' ) .'</option>';
	if( bpe_get_option( 'enable_attendees' ) )
	{
		$filter .= '<option value="attend_event">'. __( 'New event attendee', 'events' ) .'</option>';
		$filter .= '<option value="maybe_attend_event">'. __( 'Potential event attendee', 'events' ) .'</option>';
		$filter .= '<option value="remove_from_event">'. __( 'Removed event attendee', 'events' ) .'</option>';
	}
	if( bpe_get_option( 'enable_logo' ) )
    	$filter .= '<option value="new_event_logo">'. __( 'New event logo', 'events' ) .'</option>';

	echo apply_filters( 'bpe_activity_filters', $filter );
}
add_action( 'bp_activity_filter_options', 		 'bpe_add_activity_filter' );
add_action( 'bp_member_activity_filter_options', 'bpe_add_activity_filter' );

/**
 * Register event activity actions
 * 
 * Attached to the <code>bp_register_activity_actions</code> hook
 * 
 * @package Core
 * @since 	2.0
 * 
 * @uses 	bp_activity_set_action()
 * @uses 	bpe_get_base()
 * @uses 	bpe_get_option()
 */
function bpe_register_activity_actions()
{
	bp_activity_set_action( bpe_get_base( 'id' ), 'new_event', __( 'New event created', 'events' ) );
	bp_activity_set_action( bpe_get_base( 'id' ), 'edited_event', __( 'Event updated', 'events' ) );
	bp_activity_set_action( bpe_get_base( 'id' ), 'cancelled_event', __( 'Event cancelled', 'events' ) );

	if( bpe_get_option( 'enable_attendees' ) )
	{
		bp_activity_set_action( bpe_get_base( 'id' ), 'attend_event', __( 'New event attendee', 'events' ) );
		bp_activity_set_action( bpe_get_base( 'id' ), 'maybe_attend_event', __( 'Potential event attendee', 'events' ) );
		bp_activity_set_action( bpe_get_base( 'id' ), 'remove_from_event', __( 'Removed event attendee', 'events' ) );
	}

	if( bpe_get_option( 'enable_logo' ) )
		bp_activity_set_action( bpe_get_base( 'id' ), 'new_event_logo', __( 'New event logo', 'events' ) );

	if( bpe_get_option( 'enable_schedules' ) )
		bp_activity_set_action( bpe_get_base( 'id' ), 'updated_event_schedule', __( 'Updated event schedule', 'events' ) );

	if( bpe_get_option( 'enable_documents' ) )
		bp_activity_set_action( bpe_get_base( 'id' ), 'updated_event_document', __( 'Updated event document', 'events' ) );

	do_action( 'bpe_register_activity_actions' );
}
add_action( 'bp_register_activity_actions', 'bpe_register_activity_actions' );
?>