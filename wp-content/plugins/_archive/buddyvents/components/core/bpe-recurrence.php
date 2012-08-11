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
 * Get the correct timestamp
 * 
 * @package Core
 * @since 	1.0
 */
function bpe_get_schedule_timestamp( $interval, $now, $substr = false )
{
	// probably not needed, but should 
	// make it a bit more precise
	$now = $now + bpe_get_gmt_offset();
	
	switch( $interval )
	{
		case 'daily':
			$timestamp = strtotime( '+1 day', $now );
			break;	

		case 'weekly':
			$timestamp = strtotime( '+1 week', $now );
			break;	

		case 'biweekly':
			$timestamp = strtotime( '+2 weeks', $now );
			break;	

		case 'monthly':
			$timestamp = strtotime( '+1 month', $now );
			break;	

		// respects the weekday
		case 'month':
			$date = getdate( $now );
			$nth_weekday = bpe_get_nth_weekday( $date );
			$timestamp = strtotime( '+ 4 weeks', strtotime( sprintf( '%s %s of %s %s', $nth_weekday, $date['weekday'], $date['month'], $date['year'] ), $now ) );
			break;
			
		case 'yearly':
			$timestamp = strtotime( '+1 year', $now );
			break;	

		default:
			if( function_exists( 'bpe_custom_recurrence_'. $interval ) )
				$timestamp = call_user_func( 'bpe_custom_recurrence_'. $interval, $interval, $now, $substr );
			break;
	}
	
	$timestamp = apply_filters( 'bpe_get_schedule_timestamp', $timestamp, $interval, $now, $substr );
	
	return ( $substr ) ? $timestamp - $now : $timestamp;
}

/**
 * Get the nth occurance of a weekday in a month
 * 
 * @package Core
 * @since 	2.0
 */
function bpe_get_nth_weekday( $date )
{
	$nth = 0;

	$strings = array(
		1 => 'First',	
		2 => 'Second',	
		3 => 'Third',	
		4 => 'Fourth',	
		5 => 'Fifth'
	);
	
	$current = (int) ltrim( $date['mday'], '0' );
	for( $i = $current; $i >= 1; $i = $i - 7 )
		$nth++;
	
	return $strings[$nth];
}

/**
 * Schedule an event
 * 
 * @package Core
 * @since 	1.0
 */
function bpe_schedule_event( $recurrent, $event_id, $date_time )
{
	$timestamp = bpe_get_schedule_timestamp( $recurrent, strtotime( $date_time ) );
	
	wp_schedule_single_event( $timestamp, 'bpe_create_redisplayed_event_action', array( $event_id ) );
}

/**
 * Unschedule an event
 * 
 * @package Core
 * @since 	1.0
 */
function bpe_unschedule_event( $recurrent, $event_id, $date_time )
{
	$then = strtotime( $date_time );
	$timestamp = bpe_get_schedule_timestamp( $recurrent, $then );
	$args = array( $event_id );

	wp_unschedule_event( $timestamp, 'bpe_create_redisplayed_event_action', $args );
}

/**
 * Create a recurrent event
 * 
 * @package Core
 * @since 	1.0
 */
function bpe_create_redisplayed_event( $event_id )
{
	global $bp;
	
	$event = new Buddyvents_Events( $event_id );
	$intervals = bpe_get_config( 'recurrence_intervals' );
	
	if( ! bpe_get_event_user_id( $event ) || ! bpe_get_event_name( $event ) || ! in_array( bpe_get_event_recurrent( $event ), array_keys( $intervals ) ) )
	{
		$next = wp_next_scheduled( 'bpe_create_redisplayed_event_action', array( $event_id ) );
		wp_unschedule_event( $next, 'bpe_create_redisplayed_event_action', array( $event_id ) );
		return false;
	}

	// don't do recurrence again
	if( bpe_get_eventmeta( $event_id, 'bpe_recurrence_done' ) == 'yes' )
		return false;
	
	// we need to check here
	$slug = sanitize_title_with_dashes( bpe_get_event_name( $event ) );
	$slug = bpe_remove_accents( $slug );
	$slug = bpe_check_unique_slug( $slug );
	
	$timestamp = bpe_get_schedule_timestamp( bpe_get_event_recurrent( $event ), true );

	$start_date = gmdate( 'Y-m-d', strtotime( bpe_get_event_start_date_raw( $event ) ) + $timestamp );
	$end_date = gmdate( 'Y-m-d', strtotime( bpe_get_event_end_date_raw( $event ) ) + $timestamp );
	
	$now = bp_core_current_time();
	
	$approved = ( bpe_get_option( 'approve_events' ) == true ) ? 0 : 1;
	
	if( $id = bpe_save_event( null, bpe_get_event_user_id( $event ), bpe_get_event_group_id( $event ), bpe_get_event_name( $event ), $slug, bpe_get_event_description_raw( $event ), $event->category, bpe_get_event_url_raw( $event ), bpe_get_event_location( $event ), bpe_get_event_venue_name( $event ), bpe_get_event_longitude( $event ), bpe_get_event_latitude( $event ), $start_date, bpe_get_event_start_time_raw( $event ), $end_date, bpe_get_event_end_time_raw( $event ), $now, bpe_get_event_public( $event ), bpe_get_event_limit_members( $event ), bpe_get_event_recurrent( $event ), $event->is_spam, $approved, bpe_get_event_rsvp( $event ), bpe_get_event_all_day( $event ), bpe_get_event_timezone_raw( $event ), bpe_get_event_group_approved( $event ) ) )
	{
		$new_event = new Buddyvents_Events( $id );
		// activity entries are attached to this hook
		if( $approved == 1 )
			do_action( 'bpe_saved_new_event', $new_event );

		// add the event creator as the first attendee
		bpe_add_member( null, $id, bpe_get_event_user_id( $new_event ), 1, $now, 'admin' );
		
		// get the invitations for the old event
		$invitees = bpe_get_eventmeta( bpe_get_event_id( $event ), 'invitations' );
		// update the invitations for the current event
		bpe_update_eventmeta( $id, 'invitations', $invitees );

		// hook to add to recurring events via other components
		do_action( 'bpe_add_to_recurrent_via_component', $event_id, $id );

		// add recurrence info
		bpe_update_eventmeta( $event_id, 'bpe_recurrence_done', 'yes' );
				
		if( ! empty( $invitees ) )
		{
			$subject = __( 'Event Invitation', 'events' );
			
			$eventlink = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'active_slug' ) .'/'. $slug .'/';
					
			$counter = 1;
	
			foreach( $invitees as $key => $uid )
			{
				$message = sprintf( __( "Hello %s,\n\nyou have been invited to attend %s:\n%s\n\nPlease make a choice:\n\nI will attend:\n%s\n\nI might attend:\n%s\n\nI will not attend:\n%s", 'events' ), bp_core_get_user_displayname( $uid ), bpe_get_event_name( $new_event ), $eventlink, $eventlink .'attending/'. $uid .'/', $eventlink .'maybe/'. $uid .'/', $eventlink .'not-attending/'. $uid .'/' );
				
				messages_new_message(  array(
					'sender_id' => bpe_get_event_user_id( $new_event ),
					'recipients' => array( $uid ),
					'subject' => $subject,
					'content' => $message
				) );
				
				$counter++;
	
				// help the server load and rest for 10 seconds every 50 messages
				if( $counter % 50 == 0 )
					sleep( 10 );
			}
		}
		
		// schedule the event again
		bpe_schedule_event( bpe_get_event_recurrent( $new_event ), $id, $now );
	}
}
add_action( 'bpe_create_redisplayed_event_action', 'bpe_create_redisplayed_event' );

/**
 * Show a button to remove recurrence if there is any
 * 
 * @package Core
 * @since 	1.0
 */
function bpe_remove_recurrence_button()
{
	$schedule = wp_get_schedule( 'bpe_create_redisplayed_event_action', array( bpe_get_event_id() ) );
	
	if( $schedule )
	{
		$slug = '';
		if( bpe_get_event_end_date_raw() .' '. bpe_get_event_end_time_raw() < bp_core_current_time() )
			$slug = bpe_get_option( 'archive_slug' );
		else
			$slug = bpe_get_option( 'active_slug' );
		?>
		<p>
			<?php _e( 'This is a recurrent event. Remove the recurrence by clicking the button below:', 'events' ) ?><br />
        	<a class="confirm button" href="<?php echo wp_nonce_url( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. $slug .'/'. bpe_get_event_slug() .'/remove-recurrence/'. bpe_get_event_id() .'/'. urlencode( bpe_get_event_date_created() ) .'/'. bpe_get_event_recurrent() .'/', 'bpe_remove_event_schedule_now' ) ?>">
				<?php _e( 'Remove event recurrence', 'events' ) ?>
            </a>
      	</p>
		<?php
	}
}

/**
 * Add the recurrent avatar
 * 
 * @package Core
 * @since 	1.7.9
 */
function bpe_add_recurrent_avatar( $old_event_id, $new_event_id )
{
	if( bpe_get_option( 'enable_logo' ) == false || empty( $old_event_id ) || empty( $new_event_id )  )
		return false;

	$base_path = bp_core_avatar_upload_path();
	$base_path = $base_path .'/event-avatars/';
	
	$old_directory = $base_path . $old_event_id .'/';
	$new_directory = $base_path . $new_event_id .'/';

	// stop if we don't have an existing avatar
	if( ! is_dir( $old_directory ) )
		return false;
	
	// create the new directory if it doesn't exist already
	if( ! is_dir( $new_directory ) )
		wp_mkdir_p( $new_directory );

	// only proceed if we have a directory
	if( is_dir( $new_directory ) )
	{
		// remove all current images in the new Directory
		foreach( glob( $new_directory .'*' ) as $new_file )
			unlink( $new_file );
				
		// now copy the images across
		foreach( glob( $old_directory .'*' ) as $old_file )
		{
			$new_file = str_replace( $old_directory, $new_directory, $old_file );
			copy( $old_file, $new_file );
		}
	}
}
add_action( 'bpe_add_to_recurrent_via_component', 'bpe_add_recurrent_avatar', 10, 2 );

/**
 * Remove any recurrent event schedules
 * 
 * @package Core
 * @since 	1.7.9
 */
function bpe_delete_recurrent_event_schedules( $event_id )
{
	$next = wp_next_scheduled( 'bpe_create_redisplayed_event_action', array( $event_id ) );
	wp_unschedule_event( $next, 'bpe_create_redisplayed_event_action', array( $event_id ) );
}

/**
 * Get all recurring events for the given time range
 * 
 * @package Core
 * @since 	2.0
 */
function bpe_get_calendar_recurrent_events( $begin = false, $end = false, $user_id = false, $group_id = false )
{
	global $wpdb;
	
	if( ! $begin || ! $end )
		return array();
		
	$begin = strtotime( $begin .' 00:00:00' );
	$end = strtotime( $end .' 23:59:59' );
		
	// get all ids from currently recurrent events
	$crons = _get_cron_array();

	$event_ids = array();
	foreach( $crons as $timestamp => $schedule )
	{
		foreach( $schedule as $action => $values )
		{	
			// get out early
			if( $action != 'bpe_create_redisplayed_event_action' )
				continue;

			foreach( $values as $value )
				$event_ids[] = $value['args'][0];
		}
	}

	// if we don't have any events, get out
	if( count( $event_ids ) <= 0 )
		return array();
	
	$event_ids = $wpdb->escape( join( ',', $event_ids ) );

	$result = bpe_get_events( array( 'ids' => $event_ids, 'future' => false, 'false' => false, 'user_id' => $user_id, 'group_id' => $group_id ) );
	
	// get all events
	$counter = 1;
	foreach( (array)$result['events'] as $event )
	{
		// get the first unpublished recurrence of an event
		$start_timestamp = bpe_get_schedule_timestamp( bpe_get_event_recurrent( $event ), strtotime( bpe_get_event_start_date_raw( $event ) .' '. bpe_get_event_start_time_raw( $event ) ) );
		$end_timestamp 	 = bpe_get_schedule_timestamp( bpe_get_event_recurrent( $event ), strtotime( bpe_get_event_end_date_raw( $event ) .' '. bpe_get_event_end_time_raw( $event ) ) );
		
		// check if start or end are within our timeframe
		while( $start_timestamp <= $end )
		{
			if( $start_timestamp >= $begin && $start_timestamp <= $end || $end_timestamp >= $begin && $end_timestamp <= $end )
			{
				$recurrent_event = new stdClass();
				$recurrent_event->id 			= 'recurrent-'. $counter;
				$recurrent_event->parent_id 	= bpe_get_event_id( $event );
				$recurrent_event->name 			= bpe_get_event_name( $event );
				$recurrent_event->start_date 	= date( 'Y-m-d', $start_timestamp );
				$recurrent_event->start_time 	= date( 'H:i:s', $start_timestamp );
				$recurrent_event->end_date 		= date( 'Y-m-d', $end_timestamp );
				$recurrent_event->end_time 		= date( 'H:i:s', $end_timestamp );
				$recurrent_event->description 	= bpe_get_event_description_raw( $event );
				
				$events[] = $recurrent_event;
			}

			$start_timestamp = bpe_get_schedule_timestamp( bpe_get_event_recurrent( $event ), $start_timestamp );
			$end_timestamp 	 = bpe_get_schedule_timestamp( bpe_get_event_recurrent( $event ), $end_timestamp   );
			
			$counter++;
		}
	}
	
	return apply_filters( 'bpe_get_calendar_recurrent_events', $events, $begin, $end, $result, $event_ids, $crons );	
}

/**
 * Get the length of a given recurrence interval
 * 
 * month, monthly and yearly intervals can vary, but for
 * our purpose it's close enough
 * 
 * @package Core
 * @since 	2.0
 */
function bpe_get_recurrence_days( $interval )
{
	$recurrence = array(
		'daily' 	=>   1,
		'weekly' 	=>   7,
		'biweekly'	=>  14,
		'month'		=>  30,
		'monthly'	=>  30,
		'yearly'	=> 365
	);
	
	return $recurrence[$interval];
}

/**
 * Output all recurrence options for use in templates
 * 
 * @package Core
 * @since 	2.0
 */
function bpe_recurrent_template_options( $interval )
{
	foreach( bpe_get_config( 'recurrence_intervals' ) as $key => $rec_interval )
		echo '<option'. ( ( $key == $interval ) ? ' selected="selected"' : '' ) .' value="'. $key .'">'. $rec_interval .'</option>';
}

/**
 * Return the time offset from GMT in seconds
 * 
 * Can be either positive or negative
 * 
 * @package Core
 * @since 	2.1
 * 
 * @return	$offset		Offset in seconds
 */
function bpe_get_gmt_offset()
{
    return (int)( get_option( 'gmt_offset' ) * 3600 );
}
?>