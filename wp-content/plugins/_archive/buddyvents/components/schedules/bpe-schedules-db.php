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
 * Add a schedule
 *
 * @package	 Schedules
 * @since 	 1.5
 */
function bpe_add_schedule( $id = null, $event_id, $day, $start, $end, $description )
{
	$schedule = new Buddyvents_Schedules( $id );
	
	$schedule->event_id 	= $event_id;
	$schedule->day 			= $day;
	$schedule->start 		= $start;
	$schedule->end 			= $end;
	$schedule->description 	= $description;

	if( $new_id = $schedule->save() )
		return $new_id;
		
	return false;
}

/**
 * Loop function: get all schedules
 *
 * @package	 Schedules
 * @since 	 1.5
 */
function bpe_get_schedules( $args = '' )
{
	global $bp;
	
	$defaults = array(
		'event_id' => false,
		'day' => false,
		'start' => false,
		'end' => false,
		'per_page' => 50,
		'page' => 1,
		'search_terms' => false
	);	

	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	$schedules = Buddyvents_Schedules::get( (int)$event_id, $day, $start, $end, (int)$page, (int)$per_page, $search_terms );

	return apply_filters( 'bpe_get_schedules', $schedules, &$params );
}

/**
 * Check if an event has a schedule
 *
 * @package	 Schedules
 * @since 	 1.5
 */
function bpe_has_event_schedule( $id )
{
	return Buddyvents_Schedules::has_event_schedule( $id );
}


/**
 * Get the number of schedules for an event
 *
 * @package	 Schedules
 * @since 	 1.5
 */
function bpe_schedule_amount( $id )
{
	return Buddyvents_Schedules::schedule_amount( $id );
}

/**
 * Delete all schedules for an event
 *
 * @package	 Schedules
 * @since 	 1.5
 */
function bpe_delete_schedules_for_event( $id )
{
	return Buddyvents_Schedules::delete_schedules_for_event( $id );
}

/**
 * Delete schedules by id
 *
 * @package	 Schedules
 * @since 	 1.5
 */
function bpe_delete_schedules_by_ids( $ids )
{
	return Buddyvents_Schedules::delete_schedules_by_ids( $ids );
}
?>