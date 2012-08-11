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
 * Clear the cache
 *
 * @package	 Core
 * @since 	 1.3
 */
function bpe_clear_cache( $event )
{
	wp_cache_delete( 'bpe_get_total_events_count_active_none_0' );
	wp_cache_delete( 'bpe_get_total_events_count_active_user_' . bpe_get_event_user_id( $event ) );
	wp_cache_delete( 'bpe_get_total_events_count_active_group_' . bpe_get_event_group_id( $event ) );

	wp_cache_delete( 'bpe_get_total_events_count_archive_none_0' );
	wp_cache_delete( 'bpe_get_total_events_count_archive_user_' . bpe_get_event_user_id( $event ) );
	wp_cache_delete( 'bpe_get_total_events_count_archive_group_' . bpe_get_event_group_id( $event ) );
}
add_action( 'bpe_saved_new_event', 'bpe_clear_cache' );
add_action( 'bpe_delete_event_action', 'bpe_clear_cache' );

/**
 * Scheduled cache clearance
 *
 * @package	 Core
 * @since 	 1.3
 */
function bpe_clear_scheduled_cache( $user_id, $group_id )
{
	wp_cache_delete( 'bpe_get_total_events_count_active_none_0' );
	wp_cache_delete( 'bpe_get_total_events_count_active_user_' . $user_id );
	wp_cache_delete( 'bpe_get_total_events_count_active_group_' . $group_id );

	wp_cache_delete( 'bpe_get_total_events_count_archive_none_0' );
	wp_cache_delete( 'bpe_get_total_events_count_archive_user_' . $user_id );
	wp_cache_delete( 'bpe_get_total_events_count_archive_group_' . $group_id );
}
add_action( 'bpe_clear_events_archive_cache_action', 'bpe_clear_scheduled_cache' );

/**
 * Delete all user data upon user deletion
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_delete_user_data( $user_id )
{
	Buddyvents_Events::delete_for_user( $user_id );
	Buddyvents_Members::delete_for_user( $user_id );
}
add_action( 'wpmu_delete_user', 'bpe_delete_user_data', 1 );
add_action( 'delete_user', 'bpe_delete_user_data', 1 );

/**
 * Remove notification messages
 *
 * @package	 Core
 * @since 	 1.2.5
 */
function bpe_remove_screen_notifications()
{
	global $bp;

	bp_core_delete_notifications_by_type( bp_loggedin_user_id(), bpe_get_base( 'id' ), 'event_notification' );
}
add_action( 'bpe_loop_after_loop', 'bpe_remove_screen_notifications' );

/**
 * Delete all group data upon group deletion
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_delete_group_data( $group_id )
{
	Buddyvents_Events::remove_group_id( $group_id );
}
add_action( 'groups_delete_group', 'bpe_delete_group_data' );

/**
 * Delete all data associated with an event
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_delete_event_data( $event )
{
	// delete all metadata
	bpe_delete_eventmeta( bpe_get_event_id( $event ) );

	// delete all members
	bpe_delete_members_for_event( bpe_get_event_id( $event ) );

	// delete wp cron schedules
	bpe_delete_recurrent_event_schedules( bpe_get_event_id( $event ) );
}
add_action( 'bpe_delete_event_action', 'bpe_delete_event_data' );
?>