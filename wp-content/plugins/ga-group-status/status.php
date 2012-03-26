<?php
/*
Plugin Name: GA Group Status
Plugin URI: http://nycga.net/
Description: Allows you to make groups active or inactive without deleting them.
Version: 1.0
Author: #OWS Tech Ops
*/
/*ini_set('display_errors',1); 
error_reporting(E_ALL);*/
register_activation_hook( __FILE__, 'group_status_activation');

//register_deactivation_hook( __FILE__, 'status_deactivation');
function group_status_activation() {
    $group_status['groups'] = 'all';
    add_option('group-status', $group_status, '', 'yes');
}
add_action( 'bp_loaded', 'ga_status_load' );
function ga_status_load(){
	global $bp;
	if ( is_admin()){
		require ( dirname(__File__) . '/status-admin.php');
	}
	$group_status = get_option('group-status');
		require ( dirname(__File__) . '/status-loader.php');
//	}
}

//allow hard-hiding of inactive groups
function bbg_redirect_from_inactive_group() { 
if ( bp_is_group() ) { 
if ( 'inactive' == groups_get_groupmeta( bp_get_current_group_id(), 'active_status' ) && !is_super_admin() ) { 
bp_core_redirect( "http://nycga.net/groups" ); 
} 
} 
}
add_action( 'bp_actions', 'bbg_redirect_from_inactive_group', 1 );


//create filter for group queries

function filter_out_inactive($sql) {
    $filter_clause = "
 WHERE g.id NOT IN (
   SELECT gm.group_id
   FROM wp_bp_groups_groupmeta gm
   WHERE gm.meta_key = 'active_status'
   AND gm.meta_value = 'inactive'
) ";
    if (strpos($sql, ' WHERE ') !== false) {
        $sql = str_replace(' WHERE ', $filter_clause . " AND ", $sql);
    } else {
        $sql .= $filter_clause;
    }
    return $sql;
}

add_filter( 'bp_groups_get_total_groups_sql', 'filter_out_inactive');
add_filter( 'bp_groups_get_paged_groups_sql', 'filter_out_inactive');
add_filter( 'bp_group_member_get_total_group_count', 'filter_out_inactive');
add_filter( 'bp_group_get_total_group_count', 'filter_out_inactive');

?>
