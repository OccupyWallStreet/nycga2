<?php
/*
Plugin Name: BuddyPress Groups Directory Extras
Plugin URI: http://wordpress.org/extend/plugins/buddypress-groups-directory-extras/
Description: Display additional information for each group on the groups directory page/loop
Author: rich @etiviti
Author URI: http://etivite.com
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.1.1
Text Domain: bp-groups-directory
Network: true
*/

function etivite_bp_groups_directory_init() {

	if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
		load_textdomain( 'bp-groups-directory', dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' );

    require( dirname( __FILE__ ) . '/bp-groups-directory.php' );
    
    $data = maybe_unserialize( get_option( 'etivite_bp_groupsdirectory' ) );
    
    if ( $data['forum_link'] )
		add_action( 'etivite_action_groups_directory_actions', 'etivite_bp_groups_directory_loop_forum_link' );
		
	if ( $data['activity']['enabled'] )
		add_action( 'etivite_action_groups_directory_groups_item', 'etivite_bp_groups_directory_loop_activity_item' );
	
	add_action( bp_core_admin_hook(), 'etivite_bp_groups_directory_admin_add_admin_menu' );
	
}
add_action( 'bp_include', 'etivite_bp_groups_directory_init', 88 );
//add_action( 'bp_init', 'etivite_bp_groups_directory_init', 88 );

//add admin_menu page
function etivite_bp_groups_directory_admin_add_admin_menu() {
	global $bp;
	
	if ( !is_super_admin() )
		return false;
		
	require( dirname( __FILE__ ) . '/admin/bp-groups-directory-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Group Directory Admin', 'bp-groups-directory' ), __( 'Group Directory', 'bp-groups-directory' ), 'manage_options', 'bp-groups-directory-settings', 'etivite_bp_groups_directory_admin' );	

	//set up defaults

}

/* Stolen from Welcome Pack - thanks, Paul! then stolen from boone*/
function etivite_bp_groups_directory_admin_add_action_link( $links, $file ) {
	if ( 'buddypress-groups-directory-extras/bp-groups-directory-loader.php' != $file )
		return $links;

	if ( function_exists( 'bp_core_do_network_admin' ) ) {
		$settings_url = add_query_arg( 'page', 'bp-groups-directory-settings', bp_core_do_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	} else {
		$settings_url = add_query_arg( 'page', 'bp-groups-directory-settings', is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	}

	$settings_link = '<a href="' . $settings_url . '">' . __( 'Settings', 'bp-groups-directory' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'etivite_bp_groups_directory_admin_add_action_link', 10, 2 );

?>
