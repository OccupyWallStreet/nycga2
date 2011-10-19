<?php

// make sure we are called during the Deactivate process
if( !defined( 'ABSPATH') && !is_admin() ) {
	exit();
}

// include WP functions
require_once(ABSPATH . "/wp-blog-header.php");

// remove table
global $wpdb;
$table_name = $wpdb->prefix . "tweetblender";
$wpdb->query("DROP TABLE $table_name");

// remove options
delete_option('tweet-blender');

?>