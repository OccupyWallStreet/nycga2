<?php
// uncomment this line out if you want to disable custom headers
//define( 'BP_DTHEME_DISABLE_CUSTOM_HEADER', true );
function bp_dtheme_enqueue_styles() {
	// Bump this when changes are made to bust cache
	$version = '20110804';

	// Default CSS
	wp_enqueue_style( 'bp-default-main', get_template_directory_uri() . '/_inc/css/default.css', array(), $version );
	wp_enqueue_style( 'bp-colours', get_stylesheet_directory_uri() . '/_inc/css/colours.css', array(), $version );

	// Default CSS RTL
		if ( is_rtl() )
			wp_enqueue_style( 'bp-default-main-rtl',  get_template_directory_uri() . '/_inc/css/default-rtl.css', array( 'bp-default-main' ), $version );
	
		// Responsive layout
		if ( current_theme_supports( 'bp-default-responsive' ) ) {
			wp_enqueue_style( 'bp-default-responsive', get_template_directory_uri() . '/_inc/css/responsive.css', array( 'bp-default-main' ), $version );
	
			if ( is_rtl() )
				wp_enqueue_style( 'bp-default-responsive-rtl', get_template_directory_uri() . '/_inc/css/responsive-rtl.css', array( 'bp-default-responsive' ), $version );
		}
}
add_action( 'wp_print_styles', 'bp_dtheme_enqueue_styles' );
///////////////////////////////////////////////////////////////////////////
/* -------------------- Update Notifications Notice -------------------- */
if ( !function_exists( 'wdp_un_check' ) ) {
  add_action( 'admin_notices', 'wdp_un_check', 5 );
  add_action( 'network_admin_notices', 'wdp_un_check', 5 );
  function wdp_un_check() {
    if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'edit_users' ) )
      echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
  }
}
/* --------------------------------------------------------------------- */
?>