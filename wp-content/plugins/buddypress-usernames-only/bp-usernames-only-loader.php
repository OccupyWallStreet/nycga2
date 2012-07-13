<?php
/*
Plugin Name: BuddyPress Usernames Only
Description: Override display names across your BuddyPress site with usernames.
Author: r-a-y
Author URI: http://buddypress.org/community/members/r-a-y
Plugin URI: http://buddypress.org/community/groups/buddypress-usernames-only
Version: 0.58

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/
Donate: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Q6F2EM2BPQ2DS
*/

function bp_usernames_init() {
	require( dirname( __FILE__ ) . '/bp-usernames-only.php' );
}
add_action( 'bp_init', 'bp_usernames_init' );
?>