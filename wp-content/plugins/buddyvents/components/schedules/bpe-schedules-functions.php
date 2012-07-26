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
 * Process schedule edit
 * 
 * @package	Schedules
 * @since 	1.7
 */
function bpe_process_schedule( $input, $displayed_event, $api = false, $edit = false )
{
	$schedule_fail = false;
	$fail_time = false;
	
	$schedules = ( isset( $input['schedule'] ) ) ? $input['schedule'] : array();

	if( count( $schedules ) > 0 )
	{
		foreach( (array)$schedules as $key => $schedule )
		{
			if( empty( $schedule['day'] ) || empty( $schedule['start'] ) || empty( $schedule['description'] ) )
			{
				$schedule_fail = true;
				break;
			}

			if( ! empty( $schedule['end'] ) )
			{
				if( $schedule['start'] >= $schedule['end'] )
				{
					$fail_time  = true;
					break;
				}
			}
		}
	}

	// check for all required fields
	if( $schedule_fail == true )
	{
		if( ! $api )
		{
			bpe_add_message( __( 'Please fill in all fields marked by *.', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
		}
		else
			return bpe_api_message( 'Required data is missing', 'failed' );
	}

	// check the time
	if( $fail_time == true )
	{
		if( ! $api )
		{
			bpe_add_message( __( 'End time has to be after start time.', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
		}
		else
			return bpe_api_message( 'Required data is missing', 'failed' );
	}

	// process any schedule entries
	$existing_schedules = explode( ',', $input['schedule_ids'] );
	
	$sids = array();
	foreach( (array)$schedules as $key => $schedule )
	{
		$schedule_id = ( isset( $schedule['id'] ) ) ? $schedule['id'] : null;
		
		if( ! is_null( $schedule_id ) )
			$sids[] = $schedule_id;
		
		bpe_add_schedule( $schedule_id, bpe_get_event_id( $displayed_event ), $schedule['day'], $schedule['start'], $schedule['end'], $schedule['description'] );
	}

	$delete_schedules = array_diff( (array)$existing_schedules, (array)$sids );

	if( count( $delete_schedules ) > 0 )
		bpe_delete_schedules_by_ids( $delete_schedules );
	
	if( $edit )
		do_action( 'bpe_updated_event_schedules', $displayed_event );
		
	if( ! $edit && ! $api )
		@setcookie( 'buddyvents_schedules', $_POST['schedule-counter'], time() + 86400, COOKIEPATH );

	if( ! $api && $edit )
		bpe_add_message( __( 'Schedules have been updated.', 'events' ) );
	else
		return bpe_api_message( 'Schedules have been updated', 'success' );
}

/**
 * Display the correct counter
 *
 * @package Schedules
 * @since 	1.5
 */
function bpe_schedule_counter()
{
	$cookie = ( isset( $_COOKIE['buddyvents_schedules'] ) ) ? stripslashes( $_COOKIE['buddyvents_schedules'] ) : false;

	if( $cookie )
		return (int)$cookie + 1;

	return 1;	
}

/**
 * Display schedules for editing
 *
 * @package Schedules
 * @since 	1.5
 */
function bpe_edit_schedules( $eid = false )
{
	$counter = 1;
	
	if( ! $eid )
		$eid = bpe_get_displayed_event( 'id' );
	
	$schedule_ids = array();
	
	if( bpe_has_schedules( array( 'event_id' => $eid ) ) ) :
		while ( bpe_schedules() ) : bpe_the_schedule();
		
		$end = bpe_get_schedule_end_raw();
		
		if( $end == '00:00:00' )
			$end = '';
		
		$schedule = array(
			'id' => bpe_get_schedule_id(),
			'day' => bpe_get_schedule_day_raw(),
			'start' => bpe_get_schedule_start_raw(),
			'end' => $end,
			'description' => bpe_get_schedule_description_raw()
		);
		
		$schedule_ids[] = bpe_get_schedule_id();
	
		echo bpe_event_schedule_form( $counter, $schedule );
		
		$counter++;
		endwhile;
	endif;
	
	echo '<input type="hidden" name="schedule_ids" value="'. implode( ',', $schedule_ids ) .'" />';
}

/**
 * Should the date of the schedule be displayed
 *
 * @package Schedules
 * @since 	1.5
 */
function bpe_check_day_display()
{
	global $current_schedule_date;
	
	$date = bpe_get_schedule_day();
	
	if( ! $current_schedule_date || $current_schedule_date != $date ):
		?>
		<tr class="schedule-heading<?php if( ! $current_schedule_date ) echo ' schedule-heading-first'; ?>"> 
			<th scope="row" colspan="2"><?php echo $date ?></th>
		</tr>
		<?php
	endif;
	
	$current_schedule_date = $date;             
}

/**
 * Add all schedules from the parent event
 * 
 * @package Schedules
 * @since 	2.0
 */
function bpe_add_recurrent_schedules( $old_event_id, $new_event_id )
{
	if( bpe_get_option( 'enable_schedules' ) == 4 || empty( $old_event_id ) || empty( $new_event_id ) )
		return false;
	
	$old_event = new Buddyvents_Events( $old_event_id );
	$old_schedules = bpe_get_schedules( array( 'event_id' => $old_event_id ) );
	
	foreach( $old_schedules as $schedule )
	{
		$timestamp = bpe_get_schedule_timestamp( bpe_get_event_recurrent( $old_event ), strtotime( $schedule->day ) );
		
		bpe_add_schedule( null, $new_event_id, gmdate( 'Y-m-d', $timestamp ), $schedule->start, $schedule->end, $schedule->description );
	}
}
add_action( 'bpe_add_to_recurrent_via_component', 'bpe_add_recurrent_schedules', 10, 2 );

/**
 * Modify the page title
 * 
 * @package Schedules
 * @since 	2.1.1
 */
function bpe_schedules_adjust_page_title( $title, $sep )
{
	if( ! bpe_is_event_schedule() )
		return $title;
	
	$title = stripslashes( bpe_get_displayed_event( 'name' ) ) .' '. $sep .' '. __( 'Schedule', 'events' );
	
	return apply_filters( 'bpe_schedules_adjust_page_title', $title, $sep );
}
add_filter( 'bpe_adjust_page_title', 'bpe_schedules_adjust_page_title', 10, 2 );

/**
 * Delete all data associated with an event
 *
 * @package	 Schedules
 * @since 	 2.1.1
 */
function bpe_delete_schedule_data( $event )
{
	// delete all schedules
	bpe_delete_schedules_for_event( bpe_get_event_id( $event ) );
}
add_action( 'bpe_delete_event_action', 'bpe_delete_schedule_data' );
?>