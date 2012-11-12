<?php


add_action('wp', 'wp_rss_multi_activation');

add_action('wp_rss_multi_event', 'wp_rss_multi_cron');

function wp_rss_multi_activation() {
	if ( !wp_next_scheduled( 'wp_rss_multi_event' ) ) {
		wp_schedule_event( time(), 'hourly', 'wp_rss_multi_event');
	}
}

function wp_rss_multi_cron() {
	find_db_transients();
}


function find_db_transients() {

    global $wpdb;

    $expired = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_wprssmi_%';" );
	if ( $expired ) {
    	foreach( $expired as $transient ) {

        	$key = str_replace('_transient_wprssmi_', '', $transient);
			wp_rss_multi_importer_shortcode(array('category'=>$key));

    	}
	}
}


register_deactivation_hook(__FILE__, 'wp_rss_multi_deactivation');

function wp_rss_multi_deactivation() {
	wp_clear_scheduled_hook('wp_rss_multi_event');
}


?>