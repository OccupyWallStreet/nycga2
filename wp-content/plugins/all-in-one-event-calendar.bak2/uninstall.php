<?php
//
//  uninstall.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

// plugin bootstrap
require_once( dirname( __FILE__ ) . '/all-in-one-event-calendar.php' );

/**
 * remove_taxonomy function
 *
 * Remove a taxonomy
 *
 * @return void
 **/
function remove_taxonomy( $taxonomy ) {
	global $wp_taxonomies, $ai1ec_app_helper;

	// add event categories and event tags taxonomies
	// if missing
	if( ! taxonomy_exists( $taxonomy ) ) {
		$ai1ec_app_helper->create_post_type();
	}

	// get all terms in $taxonomy
	$terms = get_terms( $taxonomy );

	// delete all terms in $taxonomy
	foreach( $terms as $term ) {
		wp_delete_term( $term->term_id, $taxonomy );
	}

	// deregister $taxonomy
	unset( $wp_taxonomies[$taxonomy] );

	// do we need to flush the rewrite rules?
	$GLOBALS['wp_rewrite']->flush_rules();
}

// ====================================================================
// = Trigger Uninstall process only if WP_UNINSTALL_PLUGIN is defined =
// ====================================================================
if( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	
	global $wpdb, $wp_filesystem, $ai1ec_importer_plugin_helper;


	// Delete event categories taxonomy
	remove_taxonomy( 'events_categories' );

	// Delete event tags taxonomy
	remove_taxonomy( 'events_tags' );

	// Delete db version
	delete_option( 'ai1ec_db_version' );


	// Delete core themes version
	delete_option( 'ai1ec_themes_version' );

	// Delete cron version
	delete_option( 'ai1ec_cron_version' );

	// Delete settings
	delete_option( 'ai1ec_settings' );
	
	// Remove statistics version
	delete_option( 'ai1ec_n_cron_version' );
	
	// Remove update cron version 
	delete_option( 'ai1ec_u_cron_version' );
	
	// Delete scheduled update cron
	wp_clear_scheduled_hook( 'ai1ec_u_cron' );

	// Delete our scheduled statistics cron
	wp_clear_scheduled_hook( 'ai1ec_n_cron' );

	// Delete events
	$table_name = $wpdb->prefix . 'ai1ec_events';
	$query = "SELECT DISTINCT post_id FROM $table_name";
	foreach( $wpdb->get_col( $query ) as $postid ) {
		wp_delete_post( (int) $postid, true );
	}

	// Delete table events
	$wpdb->query("DROP TABLE IF EXISTS $table_name");

	// Delete table event instances
	$table_name = $wpdb->prefix . 'ai1ec_event_instances';
	$wpdb->query("DROP TABLE IF EXISTS $table_name");

	// Delete table event feeds
	$table_name = $wpdb->prefix . 'ai1ec_event_feeds';
	$wpdb->query("DROP TABLE IF EXISTS $table_name");

	// Delete table category colors
	$table_name = $wpdb->prefix . 'ai1ec_event_category_colors';
	$wpdb->query("DROP TABLE IF EXISTS $table_name");

	// Delete themes folder
	if( is_object( $wp_filesystem ) && ! is_wp_error( $wp_filesystem->errors ) ) {
		// Get the base plugin folder
		$themes_dir = $wp_filesystem->wp_content_dir() . AI1EC_THEMES_FOLDER;
		if( ! empty( $themes_dir ) ) {
			$themes_dir = trailingslashit( $themes_dir );
			$wp_filesystem->delete( $themes_dir, true );
		}
	}
	// Let the plugin run their uninstall procedures
	$ai1ec_importer_plugin_helper->run_uninstall_procedures();

}
