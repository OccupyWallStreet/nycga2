<?php
if( !defined( "BP_SHOW_DISPLAYNAME_ON_PROFILE" ) )
	define( 'BP_SHOW_DISPLAYNAME_ON_PROFILE', true );

	if( BP_SHOW_DISPLAYNAME_ON_PROFILE != true )
		add_filter( 'bp_displayed_user_fullname' , 'ray_bp_displayed_user_fullname' );

/* CORE OVERRIDES ------------------ */

function ray_bp_core_get_userlink( $link, $user_id ) {
	$displayed_user = bp_core_get_core_userdata( $user_id );

	preg_match_all("/>([^\"]*)</", $link, $user_match);

	return str_replace( $user_match[1], $displayed_user->user_login, $link );
}
add_filter( 'bp_core_get_userlink', 'ray_bp_core_get_userlink', 1, 2 );

// used in member profile header
function ray_bp_displayed_user_fullname() {
	global $bp;
	
	return $bp->displayed_user->userdata->user_login;
}

// used in private messages (sent between blah and x)
function ray_bp_get_loggedin_user_fullname() {
	global $bp;
	
	return $bp->loggedin_user->userdata->user_login;
}
add_filter( 'bp_get_loggedin_user_fullname', 'ray_bp_get_loggedin_user_fullname' );

// used for group invite friends list
function ray_bp_get_member_name( $name ) {
	global $members_template;
	
	return $members_template->member->user_login;
}
add_filter( 'bp_get_member_name' , 'ray_bp_get_member_name' );

// used in a lot of places
// - email notifications
// - messages subnav (From: x)
// - private messages (sent between x and blah)
function ray_bp_core_get_user_displayname($name, $user_id) {
	$displayed_user = bp_core_get_core_userdata( $user_id );
	
	return $displayed_user->user_login;
}
add_filter( 'bp_core_get_user_displayname', 'ray_bp_core_get_user_displayname', 1, 2 );

// used in "unique identifier" block and BP followers members listing
function ray_bp_get_user_firstname( $name ) {
	global $bp, $members_template;

	// check to see if we're on a follow page
	if( strpos($bp->current_component, $bp->follow->id) !== false ) {
		// now we do a crazy workaround...
		if( $members_template->member->user_login != $bp->loggedin_user->userdata->user_login )
			return $members_template->member->user_login;
	}

	// members directory
	elseif ( bp_is_directory() ) {
		return $members_template->member->user_login;
	}

	// profile header follow button
	if ( $bp->displayed_user->id )
		return $bp->displayed_user->userdata->user_login;

	return $name;
}
add_filter( 'bp_get_user_firstname' , 'ray_bp_get_user_firstname' );

// used in <title> tag
function ray_bp_page_title( $title ) {
	global $bp;

	if ( bp_is_member() ) {
		$title = str_replace( $bp->displayed_user->fullname, $bp->displayed_user->userdata->user_login, $title );
	}

	return $title;
}
add_filter( 'bp_page_title', 'ray_bp_page_title' );


/* GROUP OVERRIDES ------------------ */

// used in group member listing
function ray_bp_get_group_member_link() {
	global $members_template;
	
	return '<a href="' . bp_core_get_user_domain( $members_template->member->user_id, $members_template->member->user_nicename, $members_template->member->user_login ) . '">' . $members_template->member->user_login . '</a>';
}
add_filter( 'bp_get_group_member_link', 'ray_bp_get_group_member_link' );


/* ACTIVITY OVERRIDES ------------------ */

// used in activity comments
function ray_bp_acomment_name( $name, $comment ) {
	return $comment->user_login;
}
add_filter( 'bp_acomment_name' , 'ray_bp_acomment_name', 1, 2 );

// used in parent activity update
// not the best method... especially if the user has changed their display name multiple times
function ray_bp_get_activity_action( $action, $activity ) {
	$displayed_user = bp_core_get_core_userdata( $activity->user_id );

	return str_replace( '>' . $displayed_user->display_name . '<', '>' . $displayed_user->user_login . '<', $action );
}
add_filter( 'bp_get_activity_action', 'ray_bp_get_activity_action', 1, 2 );

// RSS feed title
function ray_bp_get_activity_feed_item_title( $title ) {
	global $activities_template;
	
	$displayed_user = bp_core_get_core_userdata( $activities_template->activity->user_id );
		
	return preg_replace( '/' . $displayed_user->display_name . '/', $displayed_user->user_login, $title, 1 );
}
add_filter( 'bp_get_activity_feed_item_title', 'ray_bp_get_activity_feed_item_title' );

// RSS feed description
function ray_bp_get_activity_feed_item_description( $content ) {
	global $activities_template;
	
	if ( empty( $activities_template->activity->action ) )
		return $content;
	else {
		$displayed_user = bp_core_get_core_userdata( $activities_template->activity->user_id );

		return str_replace( $displayed_user->display_name, $displayed_user->user_login, $activities_template->activity->action ) . ' ' . $activities_template->activity->content;
	}
}
add_filter( 'bp_get_activity_feed_item_description', 'ray_bp_get_activity_feed_item_description' );


/* FORUM OVERRIDES ------------------ */

// used in forum topics
function ray_bp_get_the_topic_post_poster_name( $name ) {
	global $topic_template;
	
	return str_replace($topic_template->post->poster_name, $topic_template->post->poster_login, $name);
}
add_filter( 'bp_get_the_topic_post_poster_name' , 'ray_bp_get_the_topic_post_poster_name' );

// used in forum directory loop
function ray_bp_get_the_topic_last_poster_name( $name ) {
	global $forum_template;
	
	return str_replace($forum_template->topic->topic_last_poster_displayname, $forum_template->topic->topic_last_poster_login, $name);
}
add_filter( 'bp_get_the_topic_last_poster_name', 'ray_bp_get_the_topic_last_poster_name' );


/* MESSAGE OVERRIDES ------------------ */

// used in message loop
function ray_bp_get_the_thread_message_sender_name() {
	global $thread_template;
	
	$displayed_user = bp_core_get_core_userdata( $thread_template->message->sender_id );
	return $displayed_user->user_login;
}
add_filter( 'bp_get_the_thread_message_sender_name', 'ray_bp_get_the_thread_message_sender_name' );

// override display name for ajax message reply
// hopefully there aren't any side-effects with doing this
function ray_bp_message_reply_ajax_sent_name() {
	global $bp;
	
	$bp->loggedin_user->fullname = $bp->loggedin_user->userdata->user_login;
}
add_action( 'bp_before_message_meta', 'ray_bp_message_reply_ajax_sent_name' );


/* BLOG OVERRIDES ------------------ */

// used in comment author link
function ray_get_comment_author( $author ) {
	global $comment;

	if( $comment->user_id > 0 ) {
		$displayed_user = bp_core_get_core_userdata( $comment->user_id );
		return $displayed_user->user_login;
	}
	else
		return $author;	
}
add_filter( 'get_comment_author', 'ray_get_comment_author' );

// used in "Leave a reply" block for comments in BP default theme
// hopefully there aren't any side-effects with doing this
function ray_user_identity() {
	global $user_identity, $user_login;

	$user_identity = $user_login;
}
add_action( 'wp', 'ray_user_identity' );

?>