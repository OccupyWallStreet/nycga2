<?php
/**
* Uninstall file as per WP 2.7+
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* Removes options from db when plugin is deleted via Dashboard
*
* @since 3.2
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit("Sorry, you are not allowed to access this file directly.");
}

if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}
// Delete options from database
delete_option('dfcg_plugin_settings');
delete_option('dfcg_version');
delete_option('dfcg_plugin_postmeta_upgrade');
?>