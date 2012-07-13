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
 * Change the category name to something more appropriate
 * 
 * Attached to the <code>dpa_get_addedit_action_descriptions_category_name</code> filter hook
 * 
 * @package Achievements
 * @since 	1.3
 * 
 * @param 	string 	$category_name
 * @param 	string 	$category
 * @return 	string 	$category_name
 * 
 * @uses 	bpe_get_base()
 */
function bpe_set_achievements_category_name( $category_name, $category )
{
    if( $category_name == __( 'Other', 'dpa' ) && $category == bpe_get_base( 'id' ) )
        return __( 'Events', 'events' );
    
    return $category_name;
}
add_filter( 'dpa_get_addedit_action_descriptions_category_name', 'bpe_set_achievements_category_name', 10, 2 );
 
/**
 * Process new event achievements
 * 
 * @package Achievements
 * @since 	1.3
 * 
 * @uses 	dpa_handle_action()
 */
function dpa_handle_action_bpe_saved_new_event()
{
    $func_get_args = func_get_args();
    dpa_handle_action( 'bpe_saved_new_event', $func_get_args );
}

/**
 * Process deleted event achievements
 * 
 * @package Achievements
 * @since 	1.3
 * 
 * @uses 	dpa_handle_action()
 */
function dpa_handle_action_bpe_delete_event_action()
{
    $func_get_args = func_get_args();
    dpa_handle_action( 'bpe_delete_event_action', $func_get_args );
}

/**
 * Process edited event achievements
 * 
 * @package Achievements
 * @since 	1.3
 * 
 * @uses 	dpa_handle_action()
 */
function dpa_handle_action_bpe_edited_event_action()
{
    $func_get_args = func_get_args();
    dpa_handle_action( 'bpe_edited_event_action', $func_get_args );
}

/**
 * Process joined event achievements
 * 
 * @package Achievements
 * @since 	1.3
 * 
 * @uses 	dpa_handle_action()
 */
function dpa_handle_action_bpe_maybe_attend_event()
{
    $func_get_args = func_get_args();
    dpa_handle_action( 'bpe_maybe_attend_event', $func_get_args );
}

/**
 * Process not attending event achievements
 * 
 * @package Achievements
 * @since 	1.3
 * 
 * @uses 	dpa_handle_action()
 */
function dpa_handle_action_bpe_not_attending_event()
{
    $func_get_args = func_get_args();
    dpa_handle_action( 'bpe_not_attending_event', $func_get_args );
}

/**
 * Process might attend event achievements
 * 
 * @package Achievements
 * @since 	1.3
 * 
 * @uses 	dpa_handle_action()
 */
function dpa_handle_action_bpe_attend_event()
{
    $func_get_args = func_get_args();
    dpa_handle_action( 'bpe_attend_event', $func_get_args );
}
?>