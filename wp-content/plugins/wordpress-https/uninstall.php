<?php

require_once('wordpress-https.php');

if ( !defined('WP_UNINSTALL_PLUGIN') ) {
	die();
}

// Delete WordPress HTTPS options
delete_option('wordpress-https_external_urls');
delete_option('wordpress-https_unsecure_external_urls');
delete_option('wordpress-https_ssl_host');
delete_option('wordpress-https_ssl_port');
delete_option('wordpress-https_exclusive_https');
delete_option('wordpress-https_frontpage');
delete_option('wordpress-https_ssl_admin');
delete_option('wordpress-https_ssl_host_subdomain');

// Delete force_ssl custom_field from posts and pages
delete_metadata('post', null, 'force_ssl', null, true);