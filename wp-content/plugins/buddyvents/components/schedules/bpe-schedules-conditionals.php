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
 * Is current page an schedule page
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_event_schedule()
{
	if( bpe_get_displayed_event( 'has_schedule' ) == false )
		return false;

	if( ! bpe_are_schedules_enabled( bpe_get_displayed_event() ) )
		return false;
		
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && in_array( bp_current_action(), array( bpe_get_option( 'active_slug' ), bpe_get_option( 'archive_slug' ) ) ) && bp_action_variable( 0 ) && bp_is_action_variable( bpe_get_option( 'schedule_slug' ), 1 ) )
		return true;
	
	return false;
}

/**
 * Check if schedules are enabled
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_are_schedules_enabled( $event )
{
	if( bpe_get_option( 'enable_schedules' ) == 4 )
		return false;
	
	if( bpe_get_option( 'enable_schedules' ) == 1 )
		return true;
	
	if( bpe_get_option( 'enable_schedules' ) == 3 && bpe_is_member( $event ) )
		return true;
	
	if( bpe_get_option( 'enable_schedules' ) == 2 && is_user_logged_in() )
		return true;
		
	return false;	
}
?>