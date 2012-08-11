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
 * Add an activity entry when an event schedule gets updated
 * 
 * Attached to the <code>bpe_updated_event_schedules</code> action hook
 * 
 * @package Schedules
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
function bpe_add_updated_event_schedules_activity( $event )
{
	$hide_show = ( bpe_get_event_public( $event ) == 0 ) ? true : false;
	$event_link = bpe_get_event_link( $event );
	
	$act_id =  bp_activity_get_activity_id( array(
		'user_id' 	=> bpe_get_event_user_id( $event ),
		'component' => bpe_get_base( 'slug' ),
		'type' 		=> 'updated_event_schedule',
		'item_id' 	=> bpe_get_event_id( $event )
	) );
	
	$id = ( is_numeric( $act_id ) ) ? $act_id : false;
	
	bpe_record_activity( array(	
		'id' 			=> $act_id,
		'action' 		=> apply_filters( 'bpe_activity_action_updated_schedule', sprintf( __( '%1$s has updated the schedule for the event <a href="%2$s">%3$s</a>', 'events' ), bp_core_get_userlink( bpe_get_event_user_id( $event ) ), $event_link, bpe_get_event_name( $event ) ), $event_link, $event ),
		'primary_link' 	=> $event_link,
		'type' 			=> 'updated_event_schedule',
		'item_id' 		=> bpe_get_event_id( $event ),
		'hide_sitewide' => $hide_show
	) );
}
add_action( 'bpe_updated_event_schedules', 'bpe_add_updated_event_schedules_activity' );

/**
 * Add new activity filters
 * 
 * Attached to the <code>bpe_activity_filters</code> filter.
 * 
 * @package Schedules
 * @since 	2.1.1
 * 
 * @uses 	bpe_get_option()
 */
function bpe_add_schedules_activity_filter()
{
	$filter = '';
	
	if( in_array( bpe_get_option( 'enable_schedules' ), array( 1, 2, 3 ) ) )
    	$filter .= '<option value="updated_event_schedule">'. __( 'Updated event schedule', 'events' ) .'</option>';

	return $filter;
}
add_action( 'bpe_activity_filters', 'bpe_add_schedules_activity_filter' );

/**
 * Register schedules activity actions
 * 
 * Attached to the <code>bp_register_activity_actions</code> hook
 * 
 * @package Core
 * @since 	2.1.1
 * 
 * @uses 	bp_activity_set_action()
 * @uses 	bpe_get_base()
 * @uses 	bpe_get_option()
 */
function bpe_register_schedules_activity_actions()
{
	if( bpe_get_option( 'enable_schedules' ) )
		bp_activity_set_action( bpe_get_base( 'id' ), 'updated_event_schedule', __( 'Updated event schedule', 'events' ) );
}
add_action( 'bp_register_activity_actions', 'bpe_register_schedules_activity_actions' );
?>