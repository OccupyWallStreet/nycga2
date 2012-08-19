<?php
/*
Plugin Name: Google Maps Lite
Plugin URI: http://premium.wpmudev.org/project/wordpress-google-maps-plugin
Description: Easily embed, customize, and use Google maps on your WordPress site - in posts, pages or as an easy to use widget, display local images and let your site visitors get directions in seconds.
Version: 2.5.4
Author: Ve Bailovity (Incsub)
Author URI: http://premium.wpmudev.org
WDP ID: 221

Copyright 2009-2011 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define ('AGM_PLUGIN_SELF_DIRNAME', basename(dirname(__FILE__)), true);

//Setup proper paths/URLs and load text domains
if (is_multisite() && defined('WPMU_PLUGIN_URL') && defined('WPMU_PLUGIN_DIR') && file_exists(WPMU_PLUGIN_DIR . '/' . basename(__FILE__))) {
	define ('AGM_PLUGIN_LOCATION', 'mu-plugins', true);
	define ('AGM_PLUGIN_BASE_DIR', WPMU_PLUGIN_DIR, true);
	define ('AGM_PLUGIN_URL', str_replace('http://', (@$_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://'), WPMU_PLUGIN_URL), true);
	$textdomain_handler = 'load_muplugin_textdomain';
} else if (defined('WP_PLUGIN_URL') && defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/' . AGM_PLUGIN_SELF_DIRNAME . '/' . basename(__FILE__))) {
	define ('AGM_PLUGIN_LOCATION', 'subfolder-plugins', true);
	define ('AGM_PLUGIN_BASE_DIR', WP_PLUGIN_DIR . '/' . AGM_PLUGIN_SELF_DIRNAME, true);
	define ('AGM_PLUGIN_URL', str_replace('http://', (@$_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://'), WP_PLUGIN_URL) . '/' . AGM_PLUGIN_SELF_DIRNAME, true);
	$textdomain_handler = 'load_plugin_textdomain';
} else if (defined('WP_PLUGIN_URL') && defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/' . basename(__FILE__))) {
	define ('AGM_PLUGIN_LOCATION', 'plugins', true);
	define ('AGM_PLUGIN_BASE_DIR', WP_PLUGIN_DIR, true);
	define ('AGM_PLUGIN_URL', str_replace('http://', (@$_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://'), WP_PLUGIN_URL), true);
	$textdomain_handler = 'load_plugin_textdomain';
} else {
	// No textdomain is loaded because we can't determine the plugin location.
	// No point in trying to add textdomain to string and/or localizing it.
	wp_die(__('There was an issue determining where Google Maps plugin is installed. Please reinstall.'));
}
$textdomain_handler('agm_google_maps', false, AGM_PLUGIN_SELF_DIRNAME . '/languages/');

// Post Indexer (http://premium.wpmudev.org/project/post-indexer) integration
define ('AGM_USE_POST_INDEXER', function_exists('post_indexer_make_current'), true);

require_once AGM_PLUGIN_BASE_DIR . '/lib/class_agm_map_model.php';
require_once AGM_PLUGIN_BASE_DIR . '/lib/class_agm_maps_widget.php';

require_once AGM_PLUGIN_BASE_DIR . '/lib/class_agm_plugins_handler.php';
AgmPluginsHandler::init();

require_once AGM_PLUGIN_BASE_DIR . '/lib/class_agm_plugin_installer.php';
register_activation_hook(__FILE__, array(AgmPluginInstaller, 'install'));
AgmPluginInstaller::check();

add_action('widgets_init', create_function('', "register_widget('AgmMapsWidget');"));

if (is_admin()) {
	require_once AGM_PLUGIN_BASE_DIR . '/lib/class_agm_admin_form_renderer.php';
	require_once AGM_PLUGIN_BASE_DIR . '/lib/class_agm_admin_maps.php';
	AgmAdminMaps::serve();
} else {
	require_once AGM_PLUGIN_BASE_DIR . '/lib/class_agm_marker_replacer.php';
	require_once AGM_PLUGIN_BASE_DIR . '/lib/class_agm_user_maps.php';
	AgmUserMaps::serve();
}