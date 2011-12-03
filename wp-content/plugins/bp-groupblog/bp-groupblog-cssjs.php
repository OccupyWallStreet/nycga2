<?php

/**
 * bp_groupblog_add_admin_js()
 */
function bp_groupblog_add_admin_js() {
	wp_enqueue_script( 'bp-groupblog-admin-js', WP_PLUGIN_URL . '/bp-groupblog/inc/js/admin.js' );
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('effects.core', WP_PLUGIN_URL .'/inc/js/effects.core.js', array('jquery-ui-core'));
}
add_action( 'admin_enqueue_scripts', 'bp_groupblog_add_admin_js', 1 );

function bp_groupblog_add_admin_style() {
	wp_enqueue_style('bp-groupblog-admin-style', WP_PLUGIN_URL . '/bp-groupblog/inc/css/admin.css');
	wp_enqueue_style('jQueryUISmoothness', WP_PLUGIN_URL . '/bp-groupblog/inc/smoothness/jquery-ui-smoothness.css');
}
add_action( 'admin_print_styles', 'bp_groupblog_add_admin_style' );

/**
 * bp_groupblog_add_js()
 */
function bp_groupblog_add_js() {
	if ( bp_is_groups_component() && bp_is_action_variable( 'group-blog' ) ) {
		if ( file_exists( STYLESHEETPATH . '/groupblog/js/general.js' ) )
			wp_enqueue_script( 'bp-groupblog-js', get_stylesheet_directory_uri() . '/groupblog/js/general.js' );
		else
			wp_enqueue_script( 'bp-groupblog-js', WP_PLUGIN_URL . '/bp-groupblog/groupblog/js/general.js' );
	}
}
add_action( 'wp_enqueue_scripts', 'bp_groupblog_add_js', 1 );

/**
 * bp_groupblog_add_screen_css()
 */
function bp_groupblog_add_screen_css() {

  if ( file_exists( STYLESHEETPATH . '/groupblog/css/style.css' ) )
  	wp_enqueue_style( 'bp-groupblog-screen', get_stylesheet_directory_uri() . '/groupblog/css/style.css' );
  	  else
  	wp_enqueue_style( 'bp-groupblog-screen', WP_PLUGIN_URL . '/bp-groupblog/groupblog/css/style.css' );
}
add_action( 'wp_print_styles', 'bp_groupblog_add_screen_css' );

?>