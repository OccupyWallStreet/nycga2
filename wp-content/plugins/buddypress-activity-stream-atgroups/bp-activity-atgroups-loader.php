<?php
/*
Plugin Name: BuddyPress Activity Stream AtGroups
Plugin URI: http://wordpress.org/extend/plugins/buddypress-activity-stream-atgroups/
Description: Enable @(group_slug) linking and =(group_slug) updates within activity stream content
Author: rich @etiviti
Author URI: http://etivite.com
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.1.0
Text Domain: bp-activity-atgroups
Network: true
*/

//ability to select different 'regex triggers'
//mention count/search activity page

function etivite_bp_activity_atgroups_init() {

	if ( !bp_is_active( 'activity' ) && bp_is_active( 'groups' ) )
		return;		

	if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
		load_textdomain( 'bp-activity-atgroups', dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' );

	require( dirname( __FILE__ ) . '/bp-activity-atgroups.php' );
	
	
	$data = maybe_unserialize( get_option( 'etivite_bp_activity_atgroups' ) );
	
	if ( $data['postit'] ) {
		add_filter( 'bp_activity_before_save', 'etivite_bp_activity_atgroups_postit_filter_updates' );
	}
	if ( $data['mention'] ) {
		add_filter( 'bp_activity_after_save', 'etivite_bp_activity_atgroups_at_name_filter_updates' );
		add_filter( 'pre_comment_content',                   'etivite_bp_activity_atgroups_at_name_filter' );
		add_filter( 'group_forum_topic_text_before_save',    'etivite_bp_activity_atgroups_at_name_filter' );
		add_filter( 'group_forum_post_text_before_save',     'etivite_bp_activity_atgroups_at_name_filter' );
		add_filter( 'bp_get_activity_content_body',     'etivite_bp_activity_atgroups_at_name_filter' );
	}
	
	add_action( bp_core_admin_hook(), 'etivite_bp_activity_atgroups_admin_add_admin_menu' );
	
}
add_action( 'bp_include', 'etivite_bp_activity_atgroups_init', 88 );
//add_action( 'bp_init', 'etivite_bp_activity_atgroups_init', 88 );


//add admin_menu page
function etivite_bp_activity_atgroups_admin_add_admin_menu() {
	global $bp;
	
	if ( !is_super_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	//require ( dirname( __FILE__ ) . '/admin/bp-activity-atgroups-admin.php' );

	//add_submenu_page( 'bp-general-settings', __( 'Activity AtGroups Admin', 'bp-activity-atgroups' ), __( 'Activity AtGroups', 'bp-activity-atgroups' ), 'manage_options', 'bp-activity-atgroups-settings', 'etivite_bp_activity_atgroups_admin' );	

	//set up defaults
	$new = Array();
	$new['postit'] = true;
	$new['mention'] = true;
	add_option( 'etivite_bp_activity_atgroups', $new );

}

/* Stolen from Welcome Pack - thanks, Paul! then stolen from boone*/
function etivite_bp_activity_atgroups_admin_add_action_link( $links, $file ) {
	if ( 'buddypress-activity-stream-atgroups/bp-activity-atgroups-loader.php' != $file )
		return $links;

	if ( function_exists( 'bp_core_do_network_admin' ) ) {
		$settings_url = add_query_arg( 'page', 'bp-activity-atgroups-settings', bp_core_do_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	} else {
		$settings_url = add_query_arg( 'page', 'bp-activity-atgroups-settings', is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	}

	$settings_link = '<a href="' . $settings_url . '">' . __( 'Settings', 'bp-activity-atgroups' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'etivite_bp_activity_atgroups_admin_add_action_link', 10, 2 );
?>
