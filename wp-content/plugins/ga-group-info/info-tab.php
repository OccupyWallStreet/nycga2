<?php
/*
Plugin Name: GA Group Info Tab Plugin
Description: Adds an Info tab to the "Groups" page with a custom set of fields
Version: 0.1
Author: LMCv3
Author URI: http://www.louiemccoy.com
License: GPLv2
*/

// Run things when BP is ready for it.
function gait_loader(){
 // do stuff
}
add_action( 'bp_include', 'gait_loader' );

require_once('functions.php');