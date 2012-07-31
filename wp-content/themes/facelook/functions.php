<?php
define( 'BP_DTHEME_DISABLE_CUSTOM_HEADER', true );

//
// Remove View links from activity items; most people use 'Favorites'.
add_filter('bp_activity_permalink', create_function('$a', "return null;"));

//
// Hide log-in error message from potential hackers.
add_filter('login_errors', create_function('$a', "return null;"));

//
// Hide erm, what..? Useful though!
remove_action('wp_head', 'wp_generator');

//
// Hide last known logged-on time from non-members.
function hide_activity($content) {
	if ( !is_user_logged_in() )
		$content='Log-in to contact this member!';

	return $content;
}
add_filter('bp_member_last_active', 'hide_activity', $last_activity);
add_filter('bp_last_activity', 'hide_activity', $last_activity);

// Only allow Send Private Message to friends
function is_my_own_friend( $friend_id = false) {
	global $bp;

	if ( is_site_admin() )
		return true;

	if ( !is_user_logged_in() )
		return false;

	if (!$friend_id) {
		$potential_friend_id = $bp->displayed_user->id;
	} else {
		$potential_friend_id = $friend_id;
	}

	if ( $bp->loggedin_user->id == $potential_friend_id )
		return false;

	if (friends_check_friendship_status($bp->loggedin_user->id, $potential_friend_id) == 'is_friend')
		return true;

	return false;
}

// Fix in case of empty avatar (gravatar.com fetch removed in core files).
function no_empty_avatar($avatar_link) {
	if ( strpos( $avatar_link, "src=''" ) ) {
		$avatar_link = str_replace("src=''", "src='" . BP_AVATAR_DEFAULT . "'", $avatar_link );
	}
	return $avatar_link;
}
add_filter( 'bp_core_fetch_avatar', 'no_empty_avatar' );

?>