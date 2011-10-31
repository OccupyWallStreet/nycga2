<?php

/**
 * W3 Total Cache Database module
 */
if (!defined('ABSPATH')) {
    die();
}

if (!defined('W3TC_DIR')) {
    define('W3TC_DIR', WP_CONTENT_DIR . '/plugins/w3-total-cache');
}

if (!@is_dir(W3TC_DIR) || !file_exists(W3TC_DIR . '/inc/define.php')) {
    if (!defined('WP_ADMIN')) { // lets don't show error on front end
        require_once (ABSPATH . WPINC . '/wp-db.php');
    } else {
        @header('HTTP/1.1 503 Service Unavailable');
        die(sprintf('<strong>W3 Total Cache Error:</strong> some files appear to be missing or out of place. Please re-install plugin or remove <strong>%s</strong>.', __FILE__));
    }
} else {
    require_once W3TC_DIR . '/inc/define.php';

    $config = & w3_instance('W3_Config');
    if ($config->get_boolean('dbcache.enabled')) {
        if (defined('DB_TYPE')) {
            $db_driver_path = sprintf('%s/Db/%s.php', W3TC_LIB_W3_DIR, DB_TYPE);

            if (file_exists($db_driver_path)) {
                require_once $db_driver_path;
            } else {
                die(sprintf('<strong>W3 Total Cache Error:</strong> database driver doesn\'t exist: %s.', $db_driver_path));
            }
        }

        require_once W3TC_LIB_W3_DIR . '/Db.php';

        @$GLOBALS['wpdb'] = & W3_Db::instance();
    }
}
