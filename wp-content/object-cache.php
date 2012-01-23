<?php

/**
 * W3 Total Cache Object Cache
 */
if (!defined('ABSPATH')) {
    die();
}

if (!defined('W3TC_DIR')) {
    define('W3TC_DIR', WP_CONTENT_DIR . '/plugins/w3-total-cache');
}

if (!@is_dir(W3TC_DIR) || !file_exists(W3TC_DIR . '/inc/define.php')) {
    if (!defined('WP_ADMIN')) { // lets don't show error on front end
        require_once (ABSPATH . WPINC . '/cache.php');
    } else {
        @header('HTTP/1.1 503 Service Unavailable');
        die(sprintf('<strong>W3 Total Cache Error:</strong> some files appear to be missing or out of place. Please re-install plugin or remove <strong>%s</strong>.', __FILE__));
    }
} else {
    require_once W3TC_DIR . '/inc/define.php';

    /**
     * Init cache
     *
     * @return void
     */
    function wp_cache_init() {
        $GLOBALS['wp_object_cache'] = & w3_instance('W3_ObjectCache');
    }

    /**
     * Close cache
     *
     * @return boolean
     */
    function wp_cache_close() {
        return true;
    }

    /**
     * Get from cache
     *
     * @param string $id
     * @param string $group
     * @return mixed
     */
    function wp_cache_get($id, $group = 'default') {
        global $wp_object_cache;

        return $wp_object_cache->get($id, $group);
    }

    /**
     * Set cache
     *
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function wp_cache_set($id, $data, $group = 'default', $expire = 0) {
        global $wp_object_cache;

        return $wp_object_cache->set($id, $data, $group, $expire);
    }

    /**
     * Delete from cache
     *
     * @param string $id
     * @param string $group
     * @return boolean
     */
    function wp_cache_delete($id, $group = 'default') {
        global $wp_object_cache;

        return $wp_object_cache->delete($id, $group);
    }

    /**
     * Add data to cache
     *
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function wp_cache_add($id, $data, $group = 'default', $expire = 0) {
        global $wp_object_cache;

        return $wp_object_cache->add($id, $data, $group, $expire);
    }

    /**
     * Replace data in cache
     *
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function wp_cache_replace($id, $data, $group = 'default', $expire = 0) {
        global $wp_object_cache;

        return $wp_object_cache->replace($id, $data, $group, $expire);
    }

    /**
     * Reset cache
     *
     * @return boolean
     */
    function wp_cache_reset() {
        global $wp_object_cache;

        return $wp_object_cache->reset();
    }

    /**
     * Flush cache
     *
     * @return boolean
     */
    function wp_cache_flush() {
        global $wp_object_cache;

        return $wp_object_cache->flush();
    }

    /**
     * Add global groups
     *
     * @param array $groups
     * @return void
     */
    function wp_cache_add_global_groups($groups) {
        global $wp_object_cache;

        $wp_object_cache->add_global_groups($groups);
    }

    /**
     * add non-persistent groups
     *
     * @param array $groups
     * @return void
     */
    function wp_cache_add_non_persistent_groups($groups) {
        global $wp_object_cache;

        $wp_object_cache->add_nonpersistent_groups($groups);
    }
}
