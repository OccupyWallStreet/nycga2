<?php
/*
Plugin Name: BuddyPress Activity Stream Bar
Version: 1.3.2
Plugin URI: http://blog.slyspyder.com/?p=410
Description: Adds a static bar at the bottom of every page of your website. Which displays the latest 20 BuddyPress activities and rotates threw them.
Author: Tosh Hatch
Author URI: http://www.SlySpyder.com
*/

/* Define a constant that will hold the current version number of the component */
define ( 'BP_ACTIVITY_BAR_VERSION', '1.3.2' );
// Begin BuddyPress aware
function bp_activity_bar_loader() {
// Begin BuddyPress aware http://codex.wordpress.org/Determining_Plugin_and_Content_Directories
function add_bp_activity_header() {
	wp_enqueue_style('bp_activity_header_css', WP_PLUGIN_URL .'/buddypress-activity-stream-bar/bp_activity_bar.css');
	wp_print_styles();
}
function add_bp_activity_footer() { ?>
  <div id="innerbpclose"><a href="javascript:bpactbarclose()">-</a></div><div id="innerbpopen"><a href="javascript:bpactbaropen()">-</a></div>
  <div id="footeractivity">
    <div id="bp_activity_bar">
    <?php if ( bp_has_activities('max=20') ) : ?>
    <ul id="rotation"><?php while ( bp_activities() ) : bp_the_activity(); ?><li id="activity-<?php bp_activity_id() ?>" class="slide">		
    <div id="innerbp_act"><a href="JavaScript:rotateback();"><img src="<?php get_bloginfo('wpurl') ?>/wp-content/plugins/buddypress-activity-stream-bar/img/left.png" /></a><a href="JavaScript:rotate();"><img src="<?php get_bloginfo('wpurl') ?>/wp-content/plugins/buddypress-activity-stream-bar/img/right.png" /></a>&nbsp;<a href="<?php bp_activity_user_link() ?>"><?php bp_activity_avatar( 'type=full&width=20&height=20' ) ?></a></div>
    <?php bp_activity_action() ?><?php do_action( 'bp_activity_entry_content' )  ?>
    </li><?php endwhile; ?></ul>
    </div>
  </div>
  <?php endif;
  echo "<script type='text/javascript' src='".get_bloginfo('wpurl')."/wp-content/plugins/buddypress-activity-stream-bar/bp_activity_bar.js'></script>";
}
add_action('wp_head', 'add_bp_activity_header');
add_action('wp_footer', 'add_bp_activity_footer');
//END BuddyPress aware
}
//END BuddyPress aware
if ( defined( 'BP_VERSION' ) )
	bp_activity_bar_loader();
else
	add_action( 'bp_init', 'bp_activity_bar_loader' );
// END BuddyPress aware
?>