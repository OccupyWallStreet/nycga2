<?php
/*
Plugin Name: BuddyPress Group for Community Admins and Mods
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-for-community-admins-and-mods/
Description: Auto manage a private group for all community group adminstrators and moderators
Author: rich @etivite
Author URI: http://etivite.com
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.1.1
Text Domain: bp-groupforadminmod
Network: true
*/


//TODO - disable atme email notifications of nonmembers from within group
//TODO - disable request membership button on groups-loop

function etivite_bp_group_adminmod_init() {

	if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
		load_textdomain( 'bp-groupforadminmod', dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' );
		
	require( dirname( __FILE__ ) . '/bp-group-adminmod.php' );
	
	add_action( bp_core_admin_hook(), 'etivite_bp_group_adminmod_add_admin_menu' );
	
}
add_action( 'bp_include', 'etivite_bp_group_adminmod_init', 88 );
//add_action( 'bp_init', 'etivite_bp_group_adminmod_init', 88 );

//add admin_menu page
function etivite_bp_group_adminmod_add_admin_menu() {
	global $bp;
	
	if ( !is_super_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/admin/bp-group-adminmod-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Admin Mod Group', 'bp-groupforadminmod' ), __( 'Admin Mod Group', 'bp-groupforadminmod' ), 'manage_options', 'bp-groupadminmod-settings', 'etivite_bp_group_adminmod_admin' );

}

/* Stolen from Welcome Pack - thanks, Paul! then stolen from boone*/
function etivite_bp_group_adminmod_admin_add_action_link( $links, $file ) {
	if ( 'buddypress-group-for-community-admins-and-mods/bp-group-adminmod-loader.php' != $file )
		return $links;

	if ( function_exists( 'bp_core_do_network_admin' ) ) {
		$settings_url = add_query_arg( 'page', 'bp-groupadminmod-settings', bp_core_do_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	} else {
		$settings_url = add_query_arg( 'page', 'bp-groupadminmod-settings', is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	}

	$settings_link = '<a href="' . $settings_url . '">' . __( 'Settings', 'bp-groupforadminmod' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'etivite_bp_group_adminmod_admin_add_action_link', 10, 2 );

?>
