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
 * Screen function for a users events
 * 
 * @package	Core
 * @since 	1.1
 */
function bpe_screen_events_active()
{
	if( bp_displayed_user_id() )
		bp_core_load_template( apply_filters( 'bpe_template_directory_user_home', 'events/member/home' ) );
}

/**
 * Invoices screen function
 * 
 * @package	Core
 * @since 	2.0
 */
function bpe_screen_events_invoices()
{
	if( bp_displayed_user_id() )
		bp_core_load_template( apply_filters( 'bpe_template_directory_user_home', 'events/member/home' ) );
}

/**
 * Screen function for month, day and category archives
 * 
 * @package	Core
 * @since 	1.6
 */
function bpe_screen_events_custom_pages()
{
	if( bpe_is_member_category() || bpe_is_member_month() || bpe_is_member_day() || bpe_is_member_timezone() || bpe_is_member_venue() )
		bp_core_load_template( apply_filters( 'bpe_template_directory_user_home', 'events/member/home' ) );
}
add_action( 'bp_actions', 'bpe_screen_events_custom_pages' );

/**
 * Screen function for events a user attends
 * 
 * @package	Core
 * @since 	1.1
 */
function bpe_screen_events_attending()
{
	if( bp_displayed_user_id() )
		bp_core_load_template( apply_filters( 'bpe_template_directory_user_home', 'events/member/home' ) );
}

/**
 * Screen function for a users calendar
 * 
 * @package	Core
 * @since 	1.1
 */
function bpe_screen_events_calendar()
{
	if( bp_displayed_user_id() )
		bp_core_load_template( apply_filters( 'bpe_template_directory_user_home', 'events/member/home' ) );
}

/**
 * Screen function for a users map
 * 
 * @package	Core
 * @since 	1.1
 */
function bpe_screen_events_map()
{
	if( bp_displayed_user_id() )
		bp_core_load_template( apply_filters( 'bpe_template_directory_user_home', 'events/member/home' ) );
}

/**
 * Screen function for a users archive
 * 
 * @package	Core
 * @since 	1.2.4
 */
function bpe_screen_events_archive()
{
	if( bp_displayed_user_id() )
		bp_core_load_template( apply_filters( 'bpe_template_directory_user_home', 'events/member/home' ) );
}
?>