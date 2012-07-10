<?php
/*
Plugin Name: Forum Attachments for BuddyPress
Plugin URI: http://teleogistic.net/2009/10/help-me-alpha-test-buddypress-forum-attachments/
Description: Gives members the ability to upload attachments on their posts. Ported to BP by Boone Gorges
Author: boonebgorges
Author URI: http://teleogistic.net
Version: 0.2.4

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/

Donate: http://teleogistic.net/donate
*/

function bb_attachments_localize () {
	$plugin_dir = basename(dirname(__FILE__));
	$locale = get_locale();
	$mofile = WP_PLUGIN_DIR . "/forum-attachments-for-buddypress/languages/fa-4-buddypress-$locale.mo";
      
      if ( file_exists( $mofile ) )
      		load_textdomain( 'fa-4-buddypress', $mofile );
}

add_action ('plugins_loaded', 'bb_attachments_localize');


/* Only load the BuddyPress plugin functions if BuddyPress is loaded and initialized. */
function bp_forum_attachments_init() {
	require( dirname( __FILE__ ) . '/forum-attachments-for-buddypress-bp-functions.php' );
}
add_action( 'bp_init', 'bp_forum_attachments_init' );



?>