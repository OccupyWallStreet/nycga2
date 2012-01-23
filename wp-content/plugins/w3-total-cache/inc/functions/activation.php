<?php

/**
 * Deactivate plugin after activation error
 *
 * @return void
 */
function w3_activation_cleanup() {
    $active_plugins = (array) get_option('active_plugins');
    $active_plugins_network = (array) get_site_option('active_sitewide_plugins');

    // workaround for WPMU deactivation bug
    remove_action('deactivate_' . W3TC_FILE, 'deactivate_sitewide_plugin');

    do_action('deactivate_plugin', W3TC_FILE);

    $key = array_search(W3TC_FILE, $active_plugins);

    if ($key !== false) {
        array_splice($active_plugins, $key, 1);
    }

    unset($active_plugins_network[W3TC_FILE]);

    do_action('deactivate_' . W3TC_FILE);
    do_action('deactivated_plugin', W3TC_FILE);

    update_option('active_plugins', $active_plugins);
    update_site_option('active_sitewide_plugins', $active_plugins_network);
}

/**
 * W3 activate error
 *
 * @param string $error
 * @return void
 */
function w3_activate_error($error) {
    w3_activation_cleanup();

    include W3TC_INC_DIR . '/error.php';
    exit();
}

/**
 * W3 writable error
 *
 * @param string $path
 * @return string
 */
function w3_writable_error($path) {
    $reactivate_url = wp_nonce_url('plugins.php?action=activate&plugin=' . W3TC_FILE, 'activate-plugin_' . W3TC_FILE);
    $reactivate_button = sprintf('<input type="button" value="re-activate plugin" onclick="top.location.href = \'%s\'" />', addslashes($reactivate_url));

    require_once W3TC_INC_DIR . '/functions/file.php';

    if (w3_check_open_basedir($path)) {
        $error = sprintf('<strong>%s</strong> could not be created, please run following command:<br /><strong style="color: #f00;">chmod 777 %s</strong><br />then %s.', $path, (file_exists($path) ? $path : dirname($path)), $reactivate_button);
    } else {
        $error = sprintf('<strong>%s</strong> could not be created, <strong>open_basedir</strong> restriction in effect, please check your php.ini settings:<br /><strong style="color: #f00;">open_basedir = "%s"</strong><br />then %s.', $path, ini_get('open_basedir'), $reactivate_button);
    }

    w3_activate_error($error);
}

/**
 * W3 Network activation error
 *
 * @return void
 */
function w3_network_activate_error() {
    w3_activation_cleanup();
    wp_redirect(plugins_url('pub/network_activation.php', W3TC_FILE));

    echo '<p><strong>W3 Total Cache Error:</strong> plugin cannot be activated network-wide.</p>';
    echo '<p><a href="javascript:history.back(-1);">Back</a>';
    exit();
}
