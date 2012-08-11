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
 * Redirect handler for subscriptions and favorites
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_redirect_subscriptions_favorites( $location )
{
	if( ! bpe_is_event_forum_topic() )
		return $location;

	if ( empty( $_GET['topic_id'] ) || empty( $_GET['action'] ) )
		return $location;

	$possible_actions = array(
		'bbp_favorite_add',
		'bbp_favorite_remove',
		'bbp_subscribe',
		'bbp_unsubscribe'
	);

	if( ! in_array( $_GET['action'], $possible_actions ) )
		return $location;
	
	$location = bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'forum_slug' ) .'/'. bpe_get_option( 'topic_slug' ) .'/'.bp_action_variable( 3 ) .'/';
	
	return $location;
}
add_filter( 'wp_redirect', 'bpe_forums_redirect_subscriptions_favorites' );

/**
 * Make sure certain vars are set to true
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_is_edit_topic( $true_or_false )
{
	if( bpe_is_event_forum_topic_edit() )
		return true;
	
	return $true_or_false;
}
add_filter( 'bbp_is_topic_edit', 'bpe_forums_is_edit_topic' );
add_filter( 'bbp_is_edit', 		 'bpe_forums_is_edit_topic' );

/**
 * Make sure certain vars are set to true
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_is_edit_reply( $true_or_false )
{
	if( bpe_is_event_forum_reply_edit() )
		return true;
	
	return $true_or_false;
}
add_filter( 'bbp_is_reply_edit', 'bpe_forums_is_edit_reply' );
add_filter( 'bbp_is_edit', 		 'bpe_forums_is_edit_reply' );

/**
 * Adjust the topic id
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_adjust_topic_id( $bbp_topic_id, $topic_id )
{
	if( $topic_id == 0 && bpe_is_event_forum_topic() ) :
		$bbp_topic_id = bpe_forums_get_id();
	endif;
		
	return $bbp_topic_id;
}
add_filter( 'bbp_get_topic_id', 'bpe_forums_adjust_topic_id', 10, 2 );

/**
 * We need to fix the replies pagination when on events forum pages
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_modify_paged_replies( $args )
{
	if( ! bpe_is_event_forum_topic() )
		return $args;
	
	$paged = bp_action_variable( 5 );
	
	if( ! empty( $paged ) )
		$args['paged'] = $paged;
	
	return $args;
}
add_filter( 'bbp_has_replies_query', 'bpe_forums_modify_paged_replies' );

/**
 * We need to fix the topics pagination when on events tag pages
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_modify_paged_tag_topics( $args )
{
	if( ! bpe_is_event_forum_tag() )
		return $args;
	
	$paged = bp_action_variable( 5 );
	
	if( ! empty( $paged ) )
		$args['paged'] = $paged;
	
	return $args;
}
add_filter( 'bbp_has_topics_query', 'bpe_forums_modify_paged_tag_topics' );

/**
 * Let's set the single forum to true on single forum pages
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_is_single_topic( $true_or_false )
{
	if( bpe_is_event_forum_topic() )
		return true;
	
	return $true_or_false;
}
add_filter( 'bbp_is_single_topic', 'bpe_forums_is_single_topic' );

/**
 * When a new reply or topic gets created, always redirect to the topic
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_redirect_topic_or_reply( $url )
{
	global $wp_rewrite;

	if( ! bpe_is_event_forum() && ! bpe_is_event_forum_topic() )
		return $url;
	
	// we need to do this for replies
	$url = split( '#', $url );
	
	// for replies we add the div id back in
	$div = ( ! empty( $url[1] ) ) ? '#'. $url[1] : '';

	$url = $url[0];
	
	// strip any paged args from the url - we add them back later
	$paged = '';

	$link = str_replace( bp_get_root_domain(), '', $url );
	$link = trim( $link, '/' );

	$parts = split( '/', $link );
		
	$count = count( $parts );
		
	if( $parts[$count-2] == 'page' && is_numeric( $parts[$count-1] ) ) :
		$paged = 'page/'. $parts[$count-1] .'/';
			
		unset( $parts[$count-2], $parts[$count-1] );
		
		$url = bp_get_root_domain() .'/'. join( '/', $parts );
	endif;
	
	$url = str_replace( bp_get_root_domain(), '', $url );
	$url = trim( $url, '/' );

	$segments = split( '/', $url );
	$segments = array_reverse( $segments );
	
	return bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'forum_slug' ) .'/'. bpe_get_option( 'topic_slug' ) .'/'. $segments[0] .'/'. $paged . $div;
}
add_filter( 'bbp_new_reply_redirect_to', 'bpe_forums_redirect_topic_or_reply' );
add_filter( 'bbp_new_topic_redirect_to', 'bpe_forums_redirect_topic_or_reply' );

/**
 * Change the edit URL
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_modify_topic_edit_url( $url, $topic_id )
{
	global $wp_rewrite, $bbp;

	if( ! bpe_is_event_forum_topic() )
		return $url;
	
	$search  = home_url( $wp_rewrite->root . $bbp->topic_slug . '/' );
	$replace = bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'forum_slug' ) .'/'. bpe_get_option( 'topic_slug' ) .'/';

	$url = str_replace( $search, $replace, $url );
	
	return $url;
}
add_filter( 'bbp_get_topic_edit_url', 'bpe_forums_modify_topic_edit_url', 10, 2 );

/**
 * Change the edit URL
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_modify_reply_edit_url( $url, $topic_id )
{
	global $wp_rewrite, $bbp;

	if( ! bpe_is_event_forum_topic() )
		return $url;
	
	$search = home_url( $wp_rewrite->root . $bbp->reply_slug . '/' );
	$replace = bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'forum_slug' ) .'/'. bpe_get_option( 'reply_slug' ) .'/';
	
	$url = str_replace( $search, $replace, $url );
	
	return $url;
}
add_filter( 'bbp_get_reply_edit_url', 'bpe_forums_modify_reply_edit_url', 10, 2 );

/**
 * Change a topic term link
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_modify_tag_link( $termlink, $term, $taxonomy )
{
	if( ! bpe_is_forum() || $taxonomy != bbp_get_topic_tag_tax_id() )
		return $termlink;

	$termlink = bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'forum_slug' ) .'/'. bpe_get_option( 'forum_tag_slug' ) .'/'. $term->slug .'/';
	
	return $termlink;
}
add_filter( 'term_link', 'bpe_forums_modify_tag_link', 10, 3 );

/**
 * Adjust the forums base for topics pagination
 * 
 * @package	Forums
 * @since 	2.1
 */
function bpe_forums_modify_base( $args )
{
	global $wp_rewrite;
	
	if( ! bpe_is_event_forum() && ! bpe_is_event_forum_tag() )
		return $args;
	
	if( bpe_is_event_forum() ) :
		$args['base'] = bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'forum_slug' ) .'/'. user_trailingslashit( $wp_rewrite->pagination_base . '/%#%/' );
	
	elseif( bpe_is_event_forum_tag() ) :
		$args['base'] = bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'forum_slug' ) .'/'. bpe_get_option( 'forum_tag_slug' ) .'/'. bp_action_variable( 3 ) .'/'. user_trailingslashit( $wp_rewrite->pagination_base . '/%#%/' );
		
	endif;
		
	return $args;
}
add_filter( 'bbp_topic_pagination', 'bpe_forums_modify_base' );
?>