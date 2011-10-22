<?php
/*
Plugin Name: BP-Registration-Options
Plugin URI: http://webdevstudios.com/support/wordpress-plugins/buddypress-registration-options/
Description: BuddyPress plugin that allows for new member moderation, if moderation is switched on any new members will be blocked from interacting with any buddypress elements (except editing their own profile and uploading their avatar) and will not be listed in any directory until an admin approves or denies their account. Plugin also allows new members to join one or more predefined groups or blogs at registration.
Version: 3.0.3
Requires at least: WordPress 2.9.1 / BuddyPress 1.2
Tested up to: WordPress 3.0.1 / BuddyPress 1.2.5
Author: Brian Messenlehner of WebDevStudios.com
Author URI: http://webdevstudios.com/about/brian-messenlehner/
*/

/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function bp_registration_options_init() {
    require( dirname( __FILE__ ) . '/bp-registration-options.php' );
}
add_action( 'bp_init', 'bp_registration_options_init' );
//set $wpdb->prefix back to wp_ for MS 
$iprefix=$wpdb->prefix;
$iprefix=str_replace("_".$blog_id,"",$iprefix);
?>