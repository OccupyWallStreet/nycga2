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
 * Is current page a create page
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_events_create()
{
	global $bpe;
	
	if( ! is_user_logged_in() )
		return false;
		
	if( in_array( 'create', (array)bpe_get_option( 'deactivated_tabs' ) ) )
		return false;
		
	if( bpe_get_option( 'restrict_creation' ) === true && empty( $bpe->user_groups ) )
		return false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'create_slug' ) ) )
		return true;
	
	return false;
}

/**
 * Is current page a map page
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_events_map()
{
	if( in_array( 'map', (array)bpe_get_option( 'deactivated_tabs' ) ) )
		return false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'map_slug' ) ) )
		return true;
	
	return false;
}

/**
 * Is current page a calendar page
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_events_calendar()
{
	if( in_array( 'calendar', (array)bpe_get_option( 'deactivated_tabs' ) ) )
		return false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'calendar_slug' ) ) )
		return true;
	
	return false;
}

/**
 * Is current page an archive page
 *
 * @package	 Core
 * @since 	 1.2.4
 */
function bpe_is_events_archive()
{
	if( in_array( 'archive', (array)bpe_get_option( 'deactivated_tabs' ) ) )
		return false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'archive_slug' ) ) )
		return true;
	
	return false;
}

/**
 * Is current page an active page
 *
 * @package	 Core
 * @since 	 1.4
 */
function bpe_is_events_active()
{
	if( in_array( 'active', (array)bpe_get_option( 'deactivated_tabs' ) ) )
		return false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'active_slug' ) ) )
		return true;
	
	return false;
}

/**
 * Is current page a directory
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_events_directory()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && ! in_array( bp_action_variable( 0 ), bpe_get_config( 'forbidden_slugs' ) ) )
		return true;
	
	return false;
}

/**
 * Is current page the directory page
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_events_directory_loop()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && ! bp_action_variable( 0 ) && ! bp_displayed_user_id() )
		return true;
	
	return false;
}

/**
 * Is current page a single event page
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_single_event()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && in_array( bp_current_action(), array( bpe_get_option( 'active_slug' ), bpe_get_option( 'archive_slug' ) ) ) && bp_action_variable( 0 ) && ! in_array( bp_action_variable( 0 ), bpe_get_config( 'forbidden_slugs' ) ) )
		return true;

	return false;
}

/**
 * Is current page a single event page
 *
 * @package	 Core
 * @since 	 1.2
 */
function bpe_is_home_event()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && in_array( bp_current_action(), array( bpe_get_option( 'active_slug' ), bpe_get_option( 'archive_slug' ) ) ) && bp_action_variable( 0 ) && ! bp_action_variable( 1 ) )
		return true;
	
	return false;
}

/**
 * Is current page a direction page
 *
 * @package	 Core
 * @since 	 1.4
 */
function bpe_is_event_directions()
{
	if( bpe_is_closed_event( bpe_get_displayed_event() ) )
		return false;

	if( bpe_is_event_cancelled( bpe_get_displayed_event() ) )
		return false;

	if( ! bpe_has_event_location( bpe_get_displayed_event() ) )
		return false;

	if( ! bpe_are_directions_enabled( bpe_get_displayed_event() ) )
		return false;

	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'active_slug' ) ) && bp_action_variable( 0 ) && bp_is_action_variable( bpe_get_option( 'directions_slug' ), 1 ) )
		return true;
	
	return false;
}

/**
 * Is current page an edit page
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_edit_event()
{
	if( ! is_user_logged_in() )
		return false;
	
	if( bpe_is_event_cancelled( bpe_get_displayed_event() ) )
		return false;
	
	if( ! bpe_is_admin( bpe_get_displayed_event() ) )
		return false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && in_array( bp_current_action(), array( bpe_get_option( 'active_slug' ), bpe_get_option( 'archive_slug' ) ) ) && bp_action_variable( 0 ) && bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) )
		return true;
	
	return false;
}

/**
 * Is current page an invite page
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_invite_event()
{
	if( ! bpe_is_rsvp_enabled( bpe_get_displayed_event() ) )
		return false;

	if( ! bpe_is_member( bpe_get_displayed_event() ) )
		return false;
	
	if( bpe_is_event_cancelled( bpe_get_displayed_event() ) )
		return false;
		
	if( bpe_is_closed_event( bpe_get_displayed_event() ) )
		return false;
		
	if( bpe_is_private_event( bpe_get_displayed_event() ) && ! bpe_is_admin( bpe_get_displayed_event() ) )
		return false;
		
	if( bpe_attached_to_group( bpe_get_displayed_event() ) && ! bpe_is_admin( bpe_get_displayed_event() ) )
	{
		if( bpe_event_get_group_status( bpe_get_displayed_event() ) == 'hidden' || bpe_event_get_group_status( bpe_get_displayed_event() ) == 'private' )
			return false;
	}
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'active_slug' ) ) && bp_action_variable( 0 ) && bp_is_action_variable( bpe_get_option( 'invite_slug' ), 1 ) )
		return true;
	
	return false;
}

/**
 * Is current page an invite page
 *
 * @package	 Core
 * @since 	 1.4
 */
function bpe_is_invite_event_page()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'active_slug' ) ) && bp_action_variable( 0 ) && bp_is_action_variable( bpe_get_option( 'invite_slug' ), 1 ) )
		return true;
	
	return false;
}

/**
 * Is current page an attendee page
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_event_attendees()
{
	if( ! bpe_get_option( 'enable_attendees' ) )
		return false;

	if( ! is_user_logged_in() )
		return false;
		
	if( ! bpe_is_rsvp_enabled( bpe_get_displayed_event() ) )
		return false;

	if( ! bpe_is_member( bpe_get_displayed_event() ) )
		return false;

	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && in_array( bp_current_action(), array( bpe_get_option( 'active_slug' ), bpe_get_option( 'archive_slug' ) ) ) && bp_action_variable( 0 ) && bp_is_action_variable( bpe_get_option( 'attendee_slug' ), 1 ) )
		return true;
	
	return false;
}

/**
 * Is current page a success page
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_is_sale_success()
{
	if( bpe_get_option( 'enable_tickets' ) === false )
		return false;

	if( ! is_user_logged_in() )
		return false;
		
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'checkout_slug' ) ) && bp_is_action_variable( bpe_get_option( 'success_slug' ), 0 ) )
		return true;
	
	return false;
}

/**
 * Is current page a cancel page
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_is_sale_cancel()
{
	if( bpe_get_option( 'enable_tickets' ) === false )
		return false;

	if( ! is_user_logged_in() )
		return false;
		
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'checkout_slug' ) ) && bp_is_action_variable( bpe_get_option( 'cancel_slug' ), 0 ) )
		return true;
	
	return false;
}

/**
 * Is current page a signup page
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_is_event_signup()
{
	if( bpe_get_option( 'enable_tickets' ) === false )
		return false;
	
	if( ! bp_action_variable( 2 ) )
		return false;

	if( ! is_user_logged_in() )
		return false;

	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'active_slug' ) ) && bp_is_action_variable( bpe_get_option( 'signup_slug' ), 1 ) )
		return true;
	
	return false;
}

/**
 * Is current page a category page
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_event_category()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'category_slug' ) ) && bp_action_variable( 0 ) )
		return true;
	
	return false;
}

/**
 * Is current page a timezone page
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_is_event_timezone()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'timezone_slug' ) ) && bp_action_variable( 0 ) )
		return true;
	
	return false;
}

/**
 * Is current page a venue page
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_is_event_venue()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'venue_slug' ) ) && bp_action_variable( 0 ) )
		return true;
	
	return false;
}

/**
 * Is current page a search page
 *
 * @package	 Core
 * @since 	 1.5
 */
function bpe_is_event_search()
{
	if( in_array( 'search', (array)bpe_get_option( 'deactivated_tabs' ) ) )
		return false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'search_slug' ) ) && ! bp_action_variable( 0 ) )
		return true;
	
	return false;
}

/**
 * Is current page a search page
 *
 * @package	 Core
 * @since 	 1.5
 */
function bpe_is_event_search_results()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'search_slug' ) ) && bp_is_action_variable( bpe_get_option( 'results_slug' ), 0 ) && isset( $_REQUEST['s'] ) )
		return true;
	
	return false;
}

/**
 * Is current page a month archive page
 *
 * @package	 Core
 * @since 	 1.4
 */
function bpe_is_event_month_archive()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'month_slug' ) ) && bp_action_variable( 0 ) )
		return true;
	
	return false;
}

/**
 * Is current page a day archive page
 *
 * @package	 Core
 * @since 	 1.4
 */
function bpe_is_event_day_archive()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'day_slug' ) ) && bp_action_variable( 0 ) )
		return true;
	
	return false;
}

/**
 * Is the event closed
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_closed_event( $e = false, $admin = false )
{
	global $event_template;

	$event = ( isset( $event_template->event ) ) ? $event_template->event : $e;
	
	if( ! $event )
		return false;

	if( $admin )
	{
		if( is_super_admin() )
			return false;	
	}
	
	if( bpe_get_event_end_date_raw( $event ) .' '. bpe_get_event_end_time_raw( $event ) <= bp_core_current_time() )
		return true;
		
	return false;
}

/**
 * Is the event closed due to max attendees
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_reached_max( $e = false )
{
	global $event_template;
	
	$event = ( isset( $event_template->event ) ) ? $event_template->event : $e;

	if( ! $event )
		return false;
	
	if( bpe_get_event_limit_members( $event ) <= 0 )
		return false;
		
	if( $event->attendees >= bpe_get_event_limit_members( $event ) )
		return true;	
		
	return false;
}

/**
 * Is the event closed
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_private_event( $e = false )
{
	if( bpe_get_event_public( $e ) == 0 )
		return true;
		
	return false;
}

/**
 * Is the current user an event member
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_member( $e = false )
{
	global $event_template;

	$event = ( isset( $event_template->event ) ) ? $event_template->event : $e;
	
	if( ! $event )
		return false;
	
	if( bp_loggedin_user_id() == bpe_get_event_user_id( $event ) )
		return true;
	
	if( in_array( bp_loggedin_user_id(), (array)$event->attendee_ids ) )
		return true;

	if( in_array( bp_loggedin_user_id(), (array)$event->maybe_attendee_ids ) )
		return true;

	if( in_array( bp_loggedin_user_id(), (array)$event->admin_ids ) )
		return true;

	if( in_array( bp_loggedin_user_id(), (array)$event->organizer_ids ) )
		return true;
	
	return false;
}

/**
 * Is the current user an event admin
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_admin( $e = false, $check_super_admin = true )
{
	global $event_template;
	
	if( $check_super_admin )
	{
		if( is_super_admin() )
			return true;
	}
	
	$event = ( isset( $event_template->event ) ) ? $event_template->event : $e;
	
	if( ! $event )
		return false;
	
	if( in_array( bp_loggedin_user_id() , (array)$event->admin_ids ) )
		return true;
	
	return false;
}

/**
 * Check if an event is coupled to a group
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_attached_to_group( $e = false )
{
	global $event_template;
	
	$event = ( isset( $event_template->event ) ) ? $event_template->event : $e;
	
	if( ! $event )
		return false;
	
	if( ! bp_is_active( 'groups' ) )
		return false;
	
	// no need to show on group pages
	if( bp_get_current_group_id() )
		return false;

	if( bpe_get_event_group_id( $event ) )
		return true;
	
	return false;
}

/**
 * Check if we're on a members event map page
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_is_member_map()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'map_slug' ) ) )
		return true;
		
	return false;
}

/**
 * Check if we're on a members event invoices page
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_is_member_invoices()
{
	if( in_array( 'invoices', (array)bpe_get_option( 'deactivated_tabs' ) ) )
		return false;

	if( bpe_get_option( 'enable_tickets' ) == false )
		return false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'invoice_slug' ) ) )
		return true;
		
	return false;
}

/**
 * Is current page a success page
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_is_member_sale_success()
{
	if( bpe_get_option( 'enable_tickets' ) === false || bpe_get_option( 'enable_invoices' ) === false )
		return false;

	if( ! is_user_logged_in() )
		return false;
		
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'invoice_slug' ) ) && bp_is_action_variable( bpe_get_option( 'checkout_slug' ), 0 ) && bp_is_action_variable( bpe_get_option( 'success_slug' ), 1 ) )
		return true;
	
	return false;
}

/**
 * Is current page a cancel page
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_is_member_sale_cancel()
{
	if( bpe_get_option( 'enable_tickets' ) === false || bpe_get_option( 'enable_invoices' ) === false )
		return false;

	if( ! is_user_logged_in() )
		return false;
		
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'invoice_slug' ) ) && bp_is_action_variable( bpe_get_option( 'checkout_slug' ), 0 ) && bp_is_action_variable( bpe_get_option( 'cancel_slug' ), 1 ) )
		return true;
	
	return false;
}

/**
 * Check if we're on a members event archive page
 *
 * @package	 Core
 * @since 	 1.2.4
 */
function bpe_is_member_archive()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'archive_slug' ) ) )
		return true;
		
	return false;
}

/**
 * Check if we're on a members event month page
 *
 * @package	 Core
 * @since 	 1.6
 */
function bpe_is_member_month()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'month_slug' ) ) && bp_displayed_user_id() )
		return true;
		
	return false;
}

/**
 * Check if we're on a members event category page
 *
 * @package	 Core
 * @since 	 1.6
 */
function bpe_is_member_category()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'category_slug' ) ) && bp_displayed_user_id() )
		return true;
		
	return false;
}

/**
 * Check if we're on a members event timezone page
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_is_member_timezone()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'timezone_slug' ) ) && bp_displayed_user_id() )
		return true;
		
	return false;
}

/**
 * Check if we're on a members event venue page
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_is_member_venue()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'venue_slug' ) ) && bp_displayed_user_id() )
		return true;
		
	return false;
}

/**
 * Check if we're on a members event day page
 *
 * @package	 Core
 * @since 	 1.6
 */
function bpe_is_member_day()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'day_slug' ) ) && bp_displayed_user_id() )
		return true;
		
	return false;
}

/**
 * Check if we're on a members event calendar page
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_is_member_calendar()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'calendar_slug' ) ) )
		return true;
		
	return false;
}

/**
 * Check if we're on a members event page
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_is_member_active()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'active_slug' ) ) )
		return true;
		
	return false;
}

/**
 * Check if we're on a members event page
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_is_member_attending()
{
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'attending_slug' ) ) )
		return true;
		
	return false;
}

/**
 * Check if an event is restricted
 *
 * @package	 Core
 * @since 	 1.2
 */
function bpe_is_restricted()
{
	global $bpe;
	
	if( ! is_user_logged_in() )
		return true;

	if( bpe_get_option( 'restrict_creation' ) === true && ! $bpe->user_groups )
		return true;
		
	return false;
}

/**
 * Check if RSVP is enabled
 *
 * @package	 Core
 * @since 	 1.6
 */
function bpe_is_rsvp_enabled( $e = false )
{
	if( ! bpe_get_option( 'enable_attendees' ) )
		return false;
	
	if( bpe_get_event_rsvp( $e ) == 1 )
		return true;
		
	return false;
}

/**
 * Check if the group address is enabled
 *
 * @package	 Core
 * @since 	 1.2
 */
function bpe_is_address_enabled( $e = false )
{
	global $event_template;

	if( ! bp_is_active( 'groups' ) )
		return false;
	
	$event = ( isset( $event_template->event ) ) ? $event_template->event : $e;

	$address = bpe_event_get_group_address( $event );
	
	if( $address && bpe_get_option( 'enable_address' ) === true )
		return true;
		
	return false;	
}

/**
 * Check if directions are enabled
 *
 * @package	 Core
 * @since 	 1.4
 */
function bpe_are_directions_enabled( $event )
{
	if( bpe_get_option( 'enable_directions' ) == 4 )
		return false;
	
	if( bpe_get_option( 'enable_directions' ) == 1 )
		return true;
	
	if( bpe_get_option( 'enable_directions' ) == 3 && bpe_is_member( $event ) )
		return true;
	
	if( bpe_get_option( 'enable_directions' ) == 2 && is_user_logged_in() )
		return true;
		
	return false;	
}

/**
 * Check if iCal download is enabled
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_is_ical_enabled( $event )
{
	if( bpe_is_closed_event( $event ) )
		return false;
	
	if( bpe_get_option( 'enable_ical' ) == 4 )
		return false;
	
	if( bpe_get_option( 'enable_ical' ) == 1 )
		return true;
	
	if( bpe_get_option( 'enable_ical' ) == 3 && bpe_is_member( $event ) )
		return true;
	
	if( bpe_get_option( 'enable_ical' ) == 2 && is_user_logged_in() )
		return true;
		
	return false;	
}

/**
 * Check if the current user has a valid location
 *
 * @package	 Core
 * @since 	 1.2
 */
function bpe_has_user_location()
{
	if( mapo_get_id_by_user( bp_loggedin_user_id() ) )
		return true;
	
	return false;	
}

/**
 * Check if the current event has a valid location
 *
 * @package	 Core
 * @since 	 1.4
 */
function bpe_has_event_location( $e = false )
{
	if( bpe_get_event_latitude( $e ) != 0.00000000000000 && bpe_get_event_longitude( $e ) != 0.00000000000000 )
		return true;
	
	return false;	
}

/**
 * Check if the current event has a timezone attached
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_has_event_timezone( $e = false )
{
	if( ! bpe_get_option( 'geonames_username' ) )
		return false;
	
	if( bpe_get_event_timezone_raw( $e ) )
		return true;
	
	return false;	
}

/**
* Has a single view a nav list
 *
 * @package	 Core
 * @since 	 1.7
*/
function bpe_has_single_nav()
{
	if( bpe_get_previous_event_link() && bpe_get_next_event_link() )
		return true;
		
	return false;
}

/**
 * Check if we have a URL
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_has_url( $e = false )
{
	if( bpe_get_event_url_raw( $e ) )
		return true;
	
	return false;	
}

/**
 * Check for all day event
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_is_all_day_event( $e = false )
{
	if( bpe_get_event_all_day( $e ) == 1 )
		return true;
	
	return false;	
}

/**
 * Has an event organizers
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_event_has_organizers( $e = false )
{
	global $event_template;
	
	$event = ( isset( $event_template->event ) ) ? $event_template->event : $e;
	
	if( ! $event )
		return false;
	
	if( count( $event->organizer_ids ) > 0 )
		return true;
	
	return false;	
}

/**
 * Are logos enabled
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_are_logos_enabled()
{
	if( bpe_get_option( 'enable_logo' ) === true )
		return true;
		
	return false;
}

/**
 * Do we use fullcalendar.js
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_use_fullcalendar()
{
	if( bpe_get_option( 'use_fullcalendar' ) === true )
		return true;
		
	return false;
}

/**
 * Are manual attendees enabled
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_is_manual_attendee_enabled()
{
	if( bpe_get_option( 'enable_manual_attendees' ) === true )
		return true;
		
	return false;	
}

/**
 * Are tickets enabled
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_are_tickets_enabled()
{
	if( bpe_get_option( 'enable_tickets' ) === true )
		return true;
		
	return false;	
}

/**
 * Check to see if the loggedin user has a location
 *
 * @package	 Core
 * @since 	 2.0.6
 */
function bpe_loggedin_user_has_location()
{
	global $bp;
	
	if( ! is_user_logged_in() )
		return false;
	
	if( $bp->loggedin_user->has_location == true )
		return true;
	
	return false;
}

/**
 * Check if an event needs group approval
 *
 * @package	 Core
 * @since 	 2.0.7
 */
function bpe_event_needs_group_approval( $event )
{
	if( bpe_attached_to_group( $event ) )
		if( ! groups_is_user_admin( bp_loggedin_user_id(), bpe_get_event_group_id( $event ) ) )
			return true;
	
	return false;
}

/**
 * Check if an event is cancelled
 *
 * @package	 Core
 * @since 	 2.1
 */
function bpe_is_event_cancelled( $event )
{
	if( bpe_get_eventmeta( $event->id, 'status' ) == 'cancelled' )
		return true;
	
	return false;
}

/**
 * Check if forums are enabled
 *
 * @package	 Core
 * @since 	 2.1
 */
function bpe_are_forums_enabled()
{
	if( bpe_get_option( 'enable_forums' ) === true && function_exists( 'bbp_get_version' ) )
		if( version_compare( bbp_get_version(), Buddyvents::BBPRESS_VERSION, '>=' ) == true )
			return true;
		
	return false;
}
?>