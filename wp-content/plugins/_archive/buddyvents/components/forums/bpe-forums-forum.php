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
 * Adjust the forum id
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_adjust_forum_id( $bbp_forum_id, $forum_id )
{
	if( $forum_id == 0 && bpe_is_forum() ) :
		$bbp_forum_id = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'forum_id' );
	endif;
		
	return $bbp_forum_id;
}
add_filter( 'bbp_get_forum_id', 'bpe_forums_adjust_forum_id', 10, 2 );

/**
 * Let's set the single forum to true on single forum pages
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_is_single_forum( $true_or_false )
{
	if( bpe_is_forum() )
		return true;
	
	return $true_or_false;
}
add_filter( 'bbp_is_single_forum', 'bpe_forums_is_single_forum' );

/**
 * We need to fix the topics pagination when on events forum pages
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_modify_paged_topics( $args )
{
	if( ! bpe_is_event_forum() )
		return $args;
	
	$paged = bp_action_variable( 3 );
	
	if( ! empty( $paged ) )
		$args['paged'] = $paged;
	
	return $args;
}
add_filter( 'bbp_has_topics_query', 'bpe_forums_modify_paged_topics' );

/**
 * Add the forum id back in when editing a topic
 * 
 * @TODO: Check to see if BBPress has fixed this
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_add_edit_forum_id()
{
	if( ! bpe_is_event_forum_topic_edit() )
		return false;
	
	$forum_id = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'forum_id' );;
	
	echo '<input type="hidden" id="bbp_forum_id" name="bbp_forum_id" value="'. $forum_id .'" />';
}
add_action( 'bbp_theme_after_topic_form', 'bpe_add_edit_forum_id' );
?>