<?php
/*
Plugin Name: WP Views
Plugin URI: http://wp-types.com/
Description: When you need to create lists of items, Views is the solution. Views will query the content from the database, iterate through it and let you display it with flair. You can also enable pagination, search, filtering and sorting by site visitors.
Author: ICanLocalize
Author URI: http://wpml.org
Version: 1.1.1
*/

if(defined('WPV_VERSION')) return;

define('WPV_VERSION', '1.1.1');
define('WPV_PATH', dirname(__FILE__));
define('WPV_PATH_EMBEDDED', dirname(__FILE__) . '/embedded');
define('WPV_FOLDER', basename(WPV_PATH));
define('WPV_URL', plugins_url() . '/' . WPV_FOLDER);
define('WPV_URL_EMBEDDED', WPV_URL . '/embedded');

if (!defined('EDITOR_ADDON_RELPATH')) {
    define('EDITOR_ADDON_RELPATH', WPV_URL . '/embedded/common/visual-editor');
}

require WPV_PATH . '/inc/constants.php';
require WPV_PATH . '/inc/functions-core.php';
require_once(WPV_PATH_EMBEDDED) . '/common/wplogger.php';
require_once(WPV_PATH_EMBEDDED) . '/common/wp-pointer.php';

$wpv_wp_pointer = new WPV_wp_pointer('views');

require WPV_PATH_EMBEDDED . '/inc/wpv-shortcodes.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-shortcodes-in-shortcodes.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-meta-html-embedded.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-widgets.php';
require WPV_PATH . '/inc/wpv-layout.php';
require WPV_PATH . '/inc/wpv-filter-controls.php';
require WPV_PATH . '/inc/wpv-admin-changes.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-layout-embedded.php';
require WPV_PATH . '/inc/wpv-filter.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-pagination-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-archive-loop.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-user-functions.php';

require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-status-embedded.php');
require_once( WPV_PATH . '/inc/wpv-filter-status.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-search-embedded.php');
require_once( WPV_PATH . '/inc/wpv-filter-search.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-category-embedded.php');
require_once( WPV_PATH . '/inc/wpv-filter-category.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-custom-field-embedded.php');
require_once( WPV_PATH . '/inc/wpv-filter-custom-field.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-parent-embedded.php');
require_once( WPV_PATH . '/inc/wpv-filter-parent.php');
require_once( WPV_PATH . '/inc/wpv-filter-taxonomy-term.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-post-relationship-embedded.php');
require_once( WPV_PATH . '/inc/wpv-filter-post-relationship.php');

require WPV_PATH . '/inc/wpv-plugin.class.php';

if (is_admin()) {
    require WPV_PATH . '/inc/upgrade_plugin.php';
    require WPV_PATH_EMBEDDED . '/inc/wpv-import-export-embedded.php';
    require WPV_PATH . '/inc/wpv-import-export.php';
}

require WPV_PATH_EMBEDDED . '/inc/wpv-condition.php';

require WPV_PATH_EMBEDDED . '/common/WPML/wpml-string-shortcode.php';

$WP_Views = new WP_Views_plugin;

require WPV_PATH . '/inc/views-templates/functions-templates.php';
require WPV_PATH . '/inc/views-templates/wpv-template-plugin.class.php';
$WPV_templates = new WPV_template_plugin();

register_activation_hook(__FILE__, 'wpv_views_plugin_activate');
register_deactivation_hook(__FILE__, 'wpv_views_plugin_deactivate');

add_action('admin_init', 'wpv_views_plugin_redirect');

add_filter('plugin_action_links', 'wpv_views_plugin_action_links', 10, 2);
