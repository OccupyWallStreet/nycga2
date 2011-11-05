<?php
/*
Plugin Name: GA Group Categories
Plugin URI: http://nycga.net/
Description: Adds extra categories to group directory
Version: 1.0
Author: Internet Working Group
*/
/*ini_set('display_errors',1); 
error_reporting(E_ALL);*/
register_activation_hook( __FILE__, 'group_categories_activation');
//register_deactivation_hook( __FILE__, 'status_deactivation');
function group_categories_activation() {
    $group_categories['groups'] = 'all';
    add_option('group-categories', $group_categories, '', 'yes');
}
add_action( 'bp_loaded', 'ga_categories_load' );
function ga_categories_load(){
	global $bp;
//	require ( dirname(__File__) . '/categories-cssjs.php');
	if ( is_admin()){
		require ( dirname(__File__) . '/categories-admin.php');
	}
	$group_categories = get_option('group-categories');
//	if ( (is_string($contact['groups']) && $contact['groups'] == 'all' ) || (is_array($contact['groups']) && in_array($bp->groups->current_group->id, $contact['groups'])) ){
		require ( dirname(__File__) . '/categories-loader.php');
//	}
}

// hook into group listing function, output the groups that match a given category
function gcats_show_groups_for_cat( $groups ) {
        global $bp, $groups_template, $gcats_done;

        #echo '<pre>ajax_querystring: '; print_r( $bp->ajax_querystring ); echo '</pre>';
        #echo '<pre>current_action: '; print_r( $bp->current_action ); echo '</pre>';
        #echo '<pre>POST: '; print_r( $_POST ); echo '</pre>';

        if ( $_POST['action'] !== 'groups_filter' || $_POST['groups_search_submit'] == 'Search' || $gcats_done )
                return $groups;

        if ( strpos($_POST['scope'], 'cat_') === 0 ){
                $cat = preg_replace('/cat_/', '', urldecode( $_POST['scope'] )); // this is what ajax sends if we are in group directory
        }else if ( $bp->current_action == 'cat' )
                $cat = urldecode( $bp->action_variables[0] ); // this is for the widget from all other places

        if ( $cat ) {
                echo '<div id="gtags-results">'.__('Results for category', 'gcats').': <b>' . stripslashes( $cat ) . '</b></div>';
                $gcats_groups = gcats_get_groups_by_cat( null, null, false, false, $cat );
                $groups_template->groups = $gcats_groups[groups];
                // turn off pagination
                $groups_template->group_count = $gcats_groups[total];
                $groups_template->total_group_count = $gcats_groups[total];
                $groups_template->pag_num = $gcats_groups[total];
                $groups_template->pag_page = 1;
                $groups_template->pag_links = '';
                $groups = $gcats_groups;
        }

        $gcats_done = true; // only run it once, so that the widgets function shows normal groups, not tags.
        return $groups;
}
add_filter( 'bp_has_groups', 'gcats_show_groups_for_cat', 4, 2 );

// Return an array of the groups for a specific tag (plus number of groups)
// this is a modified copy of many similar functions found in bp-groups-classes.php
function gcats_get_groups_by_cat( $limit = null, $page = null, $user_id = false, $search_terms = false, $group_cat = null ) {
        global $wpdb, $bp;
        
        if ( $limit && $page ) {
                $pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );
        }

        if ( !is_user_logged_in() || ( !is_super_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
                $hidden_sql = "AND g.status != 'hidden'";

        if ( $search_terms ) {
                $search_terms = like_escape( $wpdb->escape( $search_terms ) );
                $search_sql = " AND ( g.name LIKE '%%{$search_terms}%%' OR g.description LIKE '%%{$search_terms}%%' )";
        }

        if ( $group_cat ) {
                $group_cat = $wpdb->escape( $group_cat );
                $cat_sql = " AND ( gm3.meta_value = '$group_cat' )";
        }

        $sql = "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity, gm3.meta_value as gtags_group_tags FROM {$bp->groups->table_name_groupmeta} gm1, {$bp->groups->table_name_groupmeta} gm2, {$bp->groups->table_name_groupmeta} gm3, {$bp->groups->table_name} g WHERE g.id = gm1.group_id AND g.id = gm2.group_id AND g.id = gm3.group_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' AND gm3.meta_key = 'category'  {$hidden_sql} {$search_sql} {$cat_sql} ORDER BY CONVERT(gm1.meta_value, SIGNED) DESC {$pag_sql}";
        $paged_groups = $wpdb->get_results( $sql ) ;
        $total_groups = count($paged_groups);
        
        foreach ( (array)$paged_groups as $group ) $group_ids[] = $group->id;
        $group_ids = $wpdb->escape( join( ',', (array)$group_ids ) );
        $paged_groups = BP_Groups_Group::get_group_extras( &$paged_groups, $group_ids, 'popular' );

        return array( 'groups' => $paged_groups, 'total' => $total_groups );
}

