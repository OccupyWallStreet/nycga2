<?php
/*
Plugin Name: GA Group Info Tab Plugin
Description: Adds an Info tab to the "Groups" page with a custom set of fields
Version: 0.1
Requires at least: WordPress 3.1 / BuddyPress 1.5
Author: #OWS Tech Ops
Author URI: http://www.nycga.net/groups/tech/
License: GPLv2
*/

// Run things when BP is ready for it.
function gait_loader(){
 require_once(  dirname( __FILE__ ) . '/info-tab-extension.php' );
 bp_register_group_extension( 'gait_info_tab' );
}
add_action( 'bp_include', 'gait_loader' );

require_once('functions.php');