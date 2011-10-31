<?php

/**
 * W3 Total Cache plugin
 */
if (!defined('W3TC')) {
    die();
}

require_once W3TC_INC_DIR . '/functions/file.php';
require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_TotalCacheActivation
 */
class W3_Plugin_TotalCacheActivation extends W3_Plugin {
    /**
     * Activate plugin action
     *
     * @return void
     */
    function activate() {
        require_once W3TC_INC_DIR . '/functions/activation.php';

        /**
         * Disable buggy sitewide activation in WPMU and WP 3.0
         */
        if ((w3_is_wpmu() && isset($_GET['sitewide'])) || (w3_is_multisite() && isset($_GET['networkwide']))) {
            w3_network_activate_error();
        }

        /**
         * Check installation files
         */
        $files = array(
            W3TC_INSTALL_FILE_ADVANCED_CACHE,
            W3TC_INSTALL_FILE_DB,
            W3TC_INSTALL_FILE_OBJECT_CACHE
        );

        $nonexistent_files = array();

        foreach ($files as $file) {
            if (!file_exists($file)) {
                $nonexistent_files[] = $file;
            }
        }

        if (count($nonexistent_files)) {
            $error = sprintf('Unfortunately core file(s): (<strong>%s</strong>) are missing, so activation will fail. Please re-start the installation process from the beginning.', implode(', ', $nonexistent_files));

            w3_activate_error($error);
        }

        if (!@is_dir(W3TC_CONTENT_DIR) && !@mkdir(W3TC_CONTENT_DIR)) {
            w3_writable_error(W3TC_CONTENT_DIR);
        }

        if (!@is_dir(W3TC_CACHE_FILE_DBCACHE_DIR) && !@mkdir(W3TC_CACHE_FILE_DBCACHE_DIR)) {
            w3_writable_error(W3TC_CACHE_FILE_DBCACHE_DIR);
        }

        if (!@is_dir(W3TC_CACHE_FILE_OBJECTCACHE_DIR) && !@mkdir(W3TC_CACHE_FILE_OBJECTCACHE_DIR)) {
            w3_writable_error(W3TC_CACHE_FILE_OBJECTCACHE_DIR);
        }

        if (!@is_dir(W3TC_CACHE_FILE_PGCACHE_DIR) && !@mkdir(W3TC_CACHE_FILE_PGCACHE_DIR)) {
            w3_writable_error(W3TC_CACHE_FILE_PGCACHE_DIR);
        }

        if (!@is_dir(W3TC_CACHE_FILE_MINIFY_DIR) && !@mkdir(W3TC_CACHE_FILE_MINIFY_DIR)) {
            w3_writable_error(W3TC_CACHE_FILE_MINIFY_DIR);
        }

        if (!@is_dir(W3TC_LOG_DIR) && !@mkdir(W3TC_LOG_DIR)) {
            w3_writable_error(W3TC_LOG_DIR);
        }

        if (!@is_dir(W3TC_TMP_DIR) && !@mkdir(W3TC_TMP_DIR)) {
            w3_writable_error(W3TC_TMP_DIR);
        }

        if (w3_is_network() && file_exists(W3TC_CONFIG_MASTER_PATH)) {
            /**
             * For multisite load master config
             */
            $this->_config->load_master();

            if (!$this->_config->save(false)) {
                w3_writable_error(W3TC_CONFIG_PATH);
            }
        } elseif (!file_exists(W3TC_CONFIG_PATH)) {
            /**
             * Set default settings
             */
            $this->_config->set_defaults();

            /**
             * If config doesn't exist enable preview mode
             */
            if (!$this->_config->save(true)) {
                w3_writable_error(W3TC_CONFIG_PREVIEW_PATH);
            }
        }

        /**
         * Save blognames into file
         */
        if (w3_is_network() && !w3_is_subdomain_install()) {
            if (!w3_save_blognames()) {
                w3_writable_error(W3TC_BLOGNAMES_PATH);
            }
        }

        delete_option('w3tc_request_data');
        add_option('w3tc_request_data', '', null, 'no');
    }

    /**
     * Deactivate plugin action
     *
     * @return void
     */
    function deactivate() {
        delete_option('w3tc_request_data');

        // keep for other blogs
        if (!$this->locked()) {
            @unlink(W3TC_BLOGNAMES_PATH);
        }

        @unlink(W3TC_CONFIG_PREVIEW_PATH);

        w3_rmdir(W3TC_TMP_DIR);
        w3_rmdir(W3TC_LOG_DIR);
        w3_rmdir(W3TC_CACHE_FILE_MINIFY_DIR);
        w3_rmdir(W3TC_CACHE_FILE_PGCACHE_DIR);
        w3_rmdir(W3TC_CACHE_FILE_DBCACHE_DIR);
        w3_rmdir(W3TC_CACHE_FILE_OBJECTCACHE_DIR);
        w3_rmdir(W3TC_CONTENT_DIR);
    }
}
