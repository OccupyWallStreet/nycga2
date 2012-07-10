<?php
/*
Plugin Name: SM Sticky Clicky Star
Plugin URI: http://sethmatics.com/extend/plugins/sm-sticky-clicky-star/
Description: Turn sticky (featured) posts on and off with 1 easy click! Control permissions with "User Role Editor".
Author: Seth Carstens & Jeremy Smeltzer
Version: 1.0.3
Author URI: http://sethamtics.com/
*/

define('sm_sticky_clicky_star_DIR', WP_PLUGIN_DIR.'/sm-sticky-clicky-star/');

// get the "author" role object
$role = get_role( 'administrator' );
$role->add_cap( 'edit_post_sticky' );

//only load the sticky clicky star plugin functions if the user has the capability to "sticky" posts.
add_action('init', 'sm_sticky_clicky_star_init');
function sm_sticky_clicky_star_init() {
	if(current_user_can('edit_others_posts') || current_user_can('edit_post_sticky') ) {
		require_once(sm_sticky_clicky_star_DIR.'sm-sticky-clicky-star-functions.php');
	}
}