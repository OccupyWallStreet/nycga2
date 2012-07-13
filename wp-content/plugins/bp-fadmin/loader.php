<?php
/*
Plugin Name: BuddyPress Frontend Admin
Plugin URI: http://namoo.co.uk
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=A9NEGJEZR23H4
Description: Component to bring backend admin options to the frontend.
Version: 0.3
Revision Date: July 30, 2010
Requires at least: WP 3.0.1, BuddyPress 1.2.5
Tested up to: WP 3.0.2, BuddyPress 1.2.6
License: AGPL http://www.fsf.org/licensing/licenses/agpl-3.0.html
Author: David Cartwright
Author URI: http://namoo.co.uk
Site Wide Only: true
*/

/* Only load the component if BuddyPress is loaded and initialized. */
function bp_fadmin_init() {
	require( dirname( __FILE__ ) . '/includes/bp-fadmin-core.php' );
}
add_action( 'bp_init', 'bp_fadmin_init' );

?>