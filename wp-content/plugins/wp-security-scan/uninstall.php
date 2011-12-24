<?php if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) { exit(); }
/*
 * Delete stored options from the options table
 */
delete_option('wsd_feed_data');

// this is a shared option so if there are
// any other plug-ins installed, then don't
// delete it.
$__1 = ABSPATH.'wp-content/plugins/websitedefender-wordpress-security';
$__2 = ABSPATH.'wp-content/plugins/secure-wordpress';
if (!is_dir($__1) || !is_dir($__2)) { delete_option('wsd_feed_data'); }