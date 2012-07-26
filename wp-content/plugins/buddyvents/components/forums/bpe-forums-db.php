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
 * Get a topic_id from the slug
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_get_id( $type = 'topic', $slug = 3 )
{
	global $wpdb;
	
	$post_type = ( $type == 'topic' ) ? bbp_get_topic_post_type() : bbp_get_reply_post_type();

	$query = $wpdb->prepare( "
		SELECT ID
		FROM {$wpdb->posts}
		WHERE post_type = %s
		AND post_name = %s
	", $post_type, bp_action_variable( $slug ) );

	$cache_key = 'get-topic-reply-id-'. md5( $query );

	$id = wp_cache_get( $cache_key, 'buddyvents' );
	if( $id !==  false )
		return $id;
	
	$id = $wpdb->get_var( $query );
	
	wp_cache_set( $cache_key, $id, 'buddyvents' );
	
	return $id;
}

/**
 * Get all forum ids
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_get_event_forum_ids()
{
	global $wpdb;

	$query = $wpdb->prepare( "
		SELECT ID
		FROM {$wpdb->posts}
		WHERE post_type = %s
		AND post_parent = %d
	", bbp_get_forum_post_type(), bpe_get_option( 'main_event_forum' ) );
	
	$cache_key = 'get-forum-ids-'. md5( $query );

	$forum_ids = wp_cache_get( $cache_key, 'buddyvents' );
	if( $forum_ids !==  false )
		return $forum_ids;

	$forum_ids = (array) $wpdb->get_col( $query );
		
	$forum_ids[] = bpe_get_option( 'main_event_forum' );
	
	wp_cache_set( $cache_key, $forum_ids, 'buddyvents' );

	return $forum_ids;
}

/**
 * Get an event from a forum id
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_get_event( $forum_id )
{
	$result = bpe_get_events( array(
		'future'	=> false,
		'past'		=> false,
		'meta' 		=> $forum_id,
		'meta_key' 	=> 'forum_id'
	) );

	return ( $result['total'] > 0 ) ? $result['events'][0] : false;
}
?>