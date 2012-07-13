<?php
/*
Plugin Name: BuddyPress Topic Mover
Description: Allows BuddyPress group mods and admins to move a forum topic to another group they are a member of. An improved version of BuddyPress Forum Topic Mover.
Version: 2.5.1
Revision Date: May 3, 2011
License: GNU General Public License 2.0 (GPL)
Author: Deryk Wenaus
Author URI: http://bluemandala.com
*/

/* Only load the component if BuddyPress is loaded and initialized. */
function bp_topic_mover_init() {
	load_plugin_textdomain( 'topic_mover', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	require( dirname( __FILE__ ) . '/topic-mover.php' );
}

if ( defined( 'BP_VERSION' ) )
	bp_topic_mover_init();
else
	add_action( 'bp_init', 'bp_topic_mover_init' );
	


?>