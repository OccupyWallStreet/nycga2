<?php

// Check if uninstall call is valid
if (!defined('WP_UNINSTALL_PLUGIN')) exit();

global $wpdb;

if (function_exists('is_multisite') && is_multisite()) {
	delete_site_option('wp-piwik_global-settings');
	$aryBlogs = $wpdb->get_results('SELECT blog_id FROM '.$wpdb->blogs.' ORDER BY blog_id');
	if (is_array($aryBlogs))
		foreach ($aryBlogs as $aryBlog)
			delete_blog_option($aryBlog->blog_id, 'wp-piwik_settings');
}

delete_option('wp-piwik_global-settings');
delete_option('wp-piwik_settings');