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
 * Check if we're on a forums page
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_is_event_forum()
{
	$retval = false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'forum_slug' ), 1 ) && ( ! bp_action_variable( 2 ) || bp_action_variable( 2 ) == 'page' ) )
		$retval = true;
	
	return apply_filters( 'bpe_is_event_forum', $retval );
}

/**
 * Check if we're on a forums topic page
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_is_event_forum_topic()
{
	$retval = false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'forum_slug' ), 1 )&& bp_is_action_variable( bpe_get_option( 'topic_slug' ), 2 ) && bp_action_variable( 3 ) )
		$retval = true;
	
	return apply_filters( 'bpe_is_event_forum_topic', $retval );
}

/**
 * Check if we're on a forums topic edit page
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_is_event_forum_topic_edit()
{
	$retval = false;
	
	if( bpe_is_event_forum_topic() && bp_is_action_variable( 'edit', 4 ) )
		$retval = true;
	
	return apply_filters( 'bpe_is_event_forum_topic_edit', $retval );
}

/**
 * Check if we're on a forums tag page
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_is_event_forum_tag()
{
	$retval = false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'forum_slug' ), 1 )&& bp_is_action_variable( bpe_get_option( 'forum_tag_slug' ), 2 ) && bp_action_variable( 3 ) )
		$retval = true;
	
	return apply_filters( 'bpe_is_event_forum_tag', $retval );
}

/**
 * Check if we're on a forums reply page
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_is_event_forum_reply()
{
	$retval = false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'forum_slug' ), 1 )&& bp_is_action_variable( bpe_get_option( 'reply_slug' ), 2 ) && bp_action_variable( 3 ) )
		$retval = true;
	
	return apply_filters( 'bpe_is_event_forum_reply', $retval );
}

/**
 * Check if we're on a forums topic edit page
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_is_event_forum_reply_edit()
{
	$retval = false;
	
	if( bpe_is_event_forum_reply() && bp_is_action_variable( 'edit', 4 ) )
		$retval = true;
	
	return apply_filters( 'bpe_is_event_forum_reply_edit', $retval );
}

/**
 * Check to see if we're on any forum page
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_is_forum()
{
	$retval = false;
	
	if(	bpe_is_event_forum_reply_edit() ||
		bpe_is_event_forum_topic_edit() ||
		bpe_is_event_forum_topic() ||
		bpe_is_event_forum_reply() ||
		bpe_is_event_forum_tag() ||
		bpe_is_event_forum()
		)
		$retval = true;
	
	return apply_filters( 'bpe_is_forum', $retval );
}
?>