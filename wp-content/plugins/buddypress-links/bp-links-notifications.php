<?php

// E-mail link owner when someone comments on their link
function bp_links_post_update_notification( $content, $poster_user_id, $link_id, $activity_id ) {
	global $bp;

	$link = new BP_Links_Link( $link_id );

	// Now email the link owner with the contents of the message (if they have enabled email notifications)
	if ( 'no' != get_usermeta( $link->user_id, 'notification_links_activity_post' ) ) {

		$poster_name = bp_core_get_user_displayname( $poster_user_id );
		$message_link = bp_activity_get_permalink( $activity_id );
		$settings_link = bp_core_get_user_domain( $link->user_id ) . 'settings/notifications/';

		// Set up and send the message
		$ud = bp_core_get_core_userdata( $link->user_id );
		$to = $ud->user_email;
		$subject = '[' . get_blog_option( BP_ROOT_BLOG, 'blogname' ) . '] ' . sprintf( __( '%s commented on your link "%s"', 'buddypress-links' ), stripslashes( $poster_name ), wp_filter_kses( stripslashes( $link->name ) ) );

$message = sprintf( __(
'%s commented on your link "%s":

"%s"

To view and respond to the message, log in and visit: %s

---------------------
', 'buddypress-links' ), $poster_name, wp_filter_kses( stripslashes_deep( $link->name ) ), wp_filter_kses( stripslashes_deep($content) ), $message_link );

		$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'buddypress' ), $settings_link );

		// Send it
		wp_mail( $to, $subject, $message );
	}
}
add_action( 'bp_links_posted_update', 'bp_links_post_update_notification', 10, 4 );

// E-mail users that were mentioned in a link
function bp_links_at_message_notification( $content, $poster_user_id, $link_id, $activity_id ) {
	global $bp;

	/* Scan for @username strings in an activity update. Notify each user. */
	$pattern = '/[@]+([A-Za-z0-9-_]+)/';
	preg_match_all( $pattern, $content, $usernames );

	/* Make sure there's only one instance of each username */
	if ( !$usernames = array_unique( $usernames[1] ) )
		return false;

	$link = new BP_Links_Link( $link_id );

	foreach( (array)$usernames as $username ) {
		if ( !$receiver_user_id = bp_core_get_userid($username) )
			continue;

		if ( !bp_links_is_link_visibile( $link, $receiver_user_id ) )
			continue;

		// Now email the user with the contents of the message (if they have enabled email notifications)
		if ( 'no' != get_usermeta( $user_id, 'notification_activity_new_mention' ) ) {

			$poster_name = bp_core_get_user_displayname( $poster_user_id );
			$message_link = bp_activity_get_permalink( $activity_id );
			$settings_link = bp_core_get_user_domain( $receiver_user_id ) . 'settings/notifications/';

			// Set up and send the message
			$ud = bp_core_get_core_userdata( $receiver_user_id );
			$to = $ud->user_email;
			$subject = '[' . get_blog_option( BP_ROOT_BLOG, 'blogname' ) . '] ' . sprintf( __( '%s mentioned you in the link "%s"', 'buddypress-links' ), stripslashes( $poster_name ), wp_filter_kses( stripslashes( $link->name ) ) );

$message = sprintf( __(
'%s mentioned you in the link "%s":

"%s"

To view and respond to the message, log in and visit: %s

---------------------
', 'buddypress-links' ), $poster_name, wp_filter_kses( stripslashes_deep( $link->name ) ), wp_filter_kses( stripslashes_deep($content) ), $message_link );

			$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'buddypress' ), $settings_link );

			// Send it
			wp_mail( $to, $subject, $message );
		}
	}
}
add_action( 'bp_links_posted_update', 'bp_links_at_message_notification', 10, 4 );

?>