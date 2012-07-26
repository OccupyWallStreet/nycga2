<?php

/**
 * bp_groupblog_add_admin_style()
 * 
 * Reworked so that our CSS and JS is only loaded on our admin page.
 * See bp_groupblog_add_admin_menu() for how this is loaded.
 */
function bp_groupblog_add_admin_style() {

	// add css
	wp_enqueue_style('bp-groupblog-admin-style', plugins_url() . '/bp-groupblog/inc/css/admin.css');
	wp_enqueue_style('jQueryUISmoothness', plugins_url() . '/bp-groupblog/inc/smoothness/jquery-ui-smoothness.css');
	
	// add javascripts
	wp_enqueue_script( 'bp-groupblog-admin-js', plugins_url() . '/bp-groupblog/inc/js/admin.js' );
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('effects.core', plugins_url() .'/bp-groupblog/inc/js/effects.core.js', array('jquery-ui-core'));
	
}

/**
 * bp_groupblog_add_js()
 */
function bp_groupblog_add_js() {
	if ( bp_is_groups_component() && bp_is_action_variable( 'group-blog' ) ) {
		if ( file_exists( get_stylesheet_directory() . '/groupblog/js/general.js' ) )
			wp_enqueue_script( 'bp-groupblog-js', get_stylesheet_directory_uri() . '/groupblog/js/general.js', array( 'jquery' ) );
		else
			wp_enqueue_script( 'bp-groupblog-js', plugins_url() . '/bp-groupblog/groupblog/js/general.js', array( 'jquery' ) );
	}
}
add_action( 'wp_enqueue_scripts', 'bp_groupblog_add_js', 1 );

/**
 * bp_groupblog_add_screen_css()
 */
function bp_groupblog_add_screen_css() {
	if ( bp_is_groups_component() ) {
		if ( file_exists( get_stylesheet_directory() . '/groupblog/css/style.css' ) )
			wp_enqueue_style( 'bp-groupblog-screen', get_stylesheet_directory_uri() . '/groupblog/css/style.css' );
		else
			wp_enqueue_style( 'bp-groupblog-screen', plugins_url() . '/bp-groupblog/groupblog/css/style.css' );
	}
}
add_action( 'wp_print_styles', 'bp_groupblog_add_screen_css' );

?>