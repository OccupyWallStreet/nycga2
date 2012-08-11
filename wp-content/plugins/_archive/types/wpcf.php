<?php
/*
  Plugin Name: Types - Complete Solution for Custom Fields and Types 
  Plugin URI: http://wordpress.org/extend/plugins/types/
  Description: Define custom post types, custom taxonomy and custom fields.
  Author: ICanLocalize
  Author URI: http://wp-types.com
  Version: 1.0.4
 */
// Added check because of activation hook and theme embedded code
if (!defined('WPCF_VERSION')) {
    define('WPCF_VERSION', '1.0.4');
}
define('WPCF_ABSPATH', dirname(__FILE__));
define('WPCF_RELPATH', plugins_url() . '/' . basename(WPCF_ABSPATH));
define('WPCF_INC_ABSPATH', WPCF_ABSPATH . '/includes');
define('WPCF_INC_RELPATH', WPCF_RELPATH . '/includes');
define('WPCF_RES_ABSPATH', WPCF_ABSPATH . '/resources');
define('WPCF_RES_RELPATH', WPCF_RELPATH . '/resources');
require_once WPCF_INC_ABSPATH . '/constants.php';

if (!defined('EDITOR_ADDON_RELPATH')) {
    define('EDITOR_ADDON_RELPATH', WPCF_RELPATH . '/embedded/common/visual-editor');
}


add_action('plugins_loaded', 'wpcf_init');
add_action('after_setup_theme', 'wpcf_init_embedded_code', 999);
register_activation_hook(__FILE__, 'wpcf_upgrade_init');
register_deactivation_hook(__FILE__, 'wpcf_deactivate_init');

add_filter('plugin_action_links', 'wpcf_types_plugin_action_links', 10, 2);

/**
 * Main init hook.
 */
function wpcf_init() {
    if (is_admin()) {
        require_once WPCF_ABSPATH . '/admin.php';
    }
}

/**
 * Include embedded code if not used in theme.
 */
function wpcf_init_embedded_code() {
    if (!defined('WPCF_EMBEDDED_ABSPATH')) {
        require_once WPCF_ABSPATH . '/embedded/types.php';
        wpcf_embedded_init();
    } else {// Added because if plugin is active - theme embedded code won't fire
        require_once WPCF_EMBEDDED_ABSPATH . '/types.php';
        wpcf_embedded_init();
    }
}

/**
 * Upgrade hook.
 */
function wpcf_upgrade_init() {
    require_once WPCF_ABSPATH . '/upgrade.php';
    wpcf_upgrade();
    wpcf_types_plugin_activate();
}

// Local debug
if (($_SERVER['SERVER_NAME'] == '192.168.1.2' || $_SERVER['SERVER_NAME'] == 'localhost') && !function_exists('debug')) {

    function debug($data, $die = true) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if ($die) die();
    }
}

function wpcf_types_plugin_activate() {
    add_option('wpcf_types_plugin_do_activation_redirect', true);
}

function wpcf_deactivate_init() {
    delete_option('wpcf_types_plugin_do_activation_redirect', true);
}

function wpcf_types_plugin_redirect() {
    if (get_option('wpcf_types_plugin_do_activation_redirect', false)) {
        delete_option('wpcf_types_plugin_do_activation_redirect');
        wp_redirect(admin_url() . 'admin.php?page=wpcf-help');
        exit;
    }
}

function wpcf_types_plugin_action_links($links, $file) {
    $this_plugin = basename(WPCF_ABSPATH) . '/wpcf.php';
    if($file == $this_plugin) {
        $links[] = '<a href="admin.php?page=wpcf-help">' . __('Getting started', 'wpcf') . '</a>';
    }
    return $links;
}
