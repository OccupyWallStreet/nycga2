<?php

define ( 'BP_FADMIN_IS_INSTALLED', 1 );

define ( 'BP_FADMIN_VERSION', '0.1' );

if ( !defined( 'BP_FADMIN_SLUG' ) )
	define ( 'BP_FADMIN_SLUG', 'fadmin' );


if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
	load_textdomain( 'bp-fadmin', dirname( __FILE__ ) . '/bp-fadmin/languages/' . get_locale() . '.mo' );


function bp_fadmin_setup_globals() {
	global $bp, $wpdb;

	/* For internal identification */
	$bp->fadmin->id = 'fadmin';
	$bp->fadmin->active = BP_FADMIN_IS_INSTALLED;

	$bp->fadmin->format_notification_function = 'bp_fadmin_format_notifications';
	$bp->fadmin->slug = BP_FADMIN_SLUG;

	/* Register this in the active components array */
	$bp->active_components[$bp->fadmin->slug] = $bp->fadmin->id;
}
add_action( 'wp', 'bp_fadmin_setup_globals', 2 );
add_action( 'admin_menu', 'bp_fadmin_setup_globals', 2 );




function bp_fadmin_setup_nav() {
	global $bp;

	/* Add 'Frontend Admin' to the main user profile navigation */
	bp_core_new_nav_item( array(
		'name' => __( 'Frontend Admin', 'bp-fadmin' ),
		'slug' => $bp->fadmin->slug,
		'position' => 120,
		'screen_function' => 'bp_fadmin_screen_main_menu',
		'default_subnav_slug' => 'menu'
	) );


}
add_action( 'wp', 'bp_fadmin_setup_nav', 2 );
add_action( 'admin_menu', 'bp_fadmin_setup_nav', 2 );






function bp_fadmin_add_js() {
	global $bp;

	if ( $bp->current_component == $bp->fadmin->slug )
		wp_enqueue_script( 'bp-fadmin-js', plugins_url( '/bp-fadmin/js/general.js' ) );
}
add_action( 'template_redirect', 'bp_fadmin_add_js', 1 );


// returns an array of objects showing title, slug and description

function bp_fadmin_registered_extensions() {
	global $bp;
	
	$fadmin_extensions = array();
	
	$fadmin_extensions[0] = new stdClass;
	
	$fadmin_extensions[0]->name = __( 'Main Menu', 'bp-fadmin');
	$fadmin_extensions[0]->slug = 'menu';
	$fadmin_extensions[0]->description = __( 'A listing of all the registered frontend administration extensions.', 'bp-fadmin');
	
	return apply_filters( 'bp_fadmin_register_extension', $fadmin_extensions );
}


// Extensions

// Main menu is required
require ( dirname( __FILE__ ) . '/bp-fadmin-main-menu.php' );

// Default extension
require ( dirname( __FILE__ ) . '/bp-fadmin-group-members.php' );
?>