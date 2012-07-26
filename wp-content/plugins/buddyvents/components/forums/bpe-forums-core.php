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
 * Include all needed files
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_load_files()
{
	$files = array(
		'bpe-forums-topic',
		'bpe-forums-forum',
		'bpe-forums-extension',
		'bpe-forums-db',
		'bpe-forums-conditionals',
		'bpe-forums-widget'
			);
	
	foreach( $files as $file )
		require( EVENT_ABSPATH .'components/forums/'. $file .'.php' );
}
add_action( 'init', 'bpe_forums_load_files', 0 );

/**
 * Reset some hooks the post/get handlers are attached to.We have
 * to do this because BP bypasses <code>template_redirect</code>
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_reset_post_handlers()
{
	if( ! bpe_is_forum() )
		return false;

	// remove the default actions, just to be safe
	remove_action( 'template_redirect', 'bbp_new_topic_handler'		   );
	remove_action( 'template_redirect', 'bbp_new_reply_handler'  	   );
	remove_action( 'template_redirect', 'bbp_edit_topic_handler',	 1 );
	remove_action( 'template_redirect', 'bbp_edit_reply_handler',	 1 );
	remove_action( 'template_redirect', 'bbp_subscriptions_handler', 1 );
	remove_action( 'template_redirect', 'bbp_favorites_handler',     1 );
	remove_action( 'template_redirect', 'bbp_toggle_reply_handler',  1 );
	remove_action( 'template_redirect', 'bbp_toggle_topic_handler',  1 );
	remove_action( 'template_redirect', 'bbp_merge_topic_handler', 	 1 );
	remove_action( 'template_redirect', 'bbp_split_topic_handler', 	 1 );
	
	// and attach them when we can use them
	add_action( 'wp', 'bbp_new_topic_handler',     2 );
	add_action( 'wp', 'bbp_edit_topic_handler',    2 );
	add_action( 'wp', 'bbp_new_reply_handler',     2 );
	add_action( 'wp', 'bbp_edit_reply_handler',    2 );	
	add_action( 'wp', 'bbp_subscriptions_handler', 2 );
	add_action( 'wp', 'bbp_favorites_handler', 	   2 );
	add_action( 'wp', 'bbp_toggle_reply_handler',  2 );
	add_action( 'wp', 'bbp_toggle_topic_handler',  2 );
	add_action( 'wp', 'bbp_merge_topic_handler',   2 );
	add_action( 'wp', 'bbp_split_topic_handler',   2 );

	// handle messages
	remove_action( 'bbp_template_notices', 'bbp_template_notices' );
	add_action( 'template_notices', 'bbp_template_notices' );
}
add_action( 'init', 'bpe_forums_reset_post_handlers' );

/**
 * Modify the permalink for event forums, event topics and event replies
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_modify_permalinks( $link, $post )
{
	if( ! in_array( $post->post_type, array( bbp_get_forum_post_type(), bbp_get_topic_post_type(), bbp_get_reply_post_type() ) ) )
		return $link;
	
	if( in_array( $post->post_type, array( bbp_get_topic_post_type(), bbp_get_reply_post_type() ) ) )
		$forum_ids = bpe_forums_get_event_forum_ids();

	switch( $post->post_type )
	{
		case bbp_get_forum_post_type():
			if( bbp_get_forum_parent( $post->ID ) != bpe_get_option( 'main_event_forum' ) )
				return $link;
			
			if( ! $event = bpe_forums_get_event( $post->ID ) )
				return $link;
			
			$link = bpe_get_event_link( $event ) . bpe_get_option( 'forum_slug' ) .'/';
			break;

		case bbp_get_topic_post_type():
			$forum_id = bbp_get_topic_forum_id( $post->ID );
			
			if( ! in_array( $forum_id, $forum_ids ) )
				return $link;
			
			if( ! $event = bpe_forums_get_event( $forum_id ) )
				return $link;

			$link = bpe_get_event_link( $event ) . bpe_get_option( 'forum_slug' ) .'/'. bpe_get_option( 'topic_slug' ) .'/'. $post->post_name;
			break;
			
		case bbp_get_reply_post_type():
			$forum_id = bbp_get_reply_forum_id( $post->ID );
			
			if( ! in_array( $forum_id, $forum_ids ) )
				return $link;

			if( ! $event = bpe_forums_get_event( $forum_id ) )
				return $link;

			$link = bpe_get_event_link( $event ) . bpe_get_option( 'forum_slug' ) .'/'. bpe_get_option( 'reply_slug' ) .'/'. $post->post_name;
			break;
	}
	
	return $link;
}
add_filter( 'post_type_link', 'bpe_forums_modify_permalinks', 10, 2 );
?>