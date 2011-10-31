<?php

/**
 * W3 ObjectCache plugin
 */
if (!defined('W3TC')) {
    die();
}

require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_ObjectCache
 */
class W3_Plugin_ObjectCache extends W3_Plugin {
    /**
     * Runs plugin
     */
    function run() {
        add_filter('cron_schedules', array(
            &$this,
            'cron_schedules'
        ));

        if ($this->_config->get_string('objectcache.engine') == 'file') {
            add_action('w3_objectcache_cleanup', array(
                &$this,
                'cleanup'
            ));
        }

        add_action('publish_phone', array(
            &$this,
            'on_change'
        ), 0);

        add_action('publish_post', array(
            &$this,
            'on_change'
        ), 0);

        add_action('edit_post', array(
            &$this,
            'on_change'
        ), 0);

        add_action('delete_post', array(
            &$this,
            'on_change'
        ), 0);

        add_action('comment_post', array(
            &$this,
            'on_change'
        ), 0);

        add_action('edit_comment', array(
            &$this,
            'on_change'
        ), 0);

        add_action('delete_comment', array(
            &$this,
            'on_change'
        ), 0);

        add_action('wp_set_comment_status', array(
            &$this,
            'on_change'
        ), 0);

        add_action('trackback_post', array(
            &$this,
            'on_change'
        ), 0);

        add_action('pingback_post', array(
            &$this,
            'on_change'
        ), 0);

        add_action('switch_theme', array(
            &$this,
            'on_change'
        ), 0);

        add_action('edit_user_profile_update', array(
            &$this,
            'on_change'
        ), 0);
    }

    /**
     * Activate plugin action (called by W3_PluginProxy)
     */
    function activate() {
        require_once W3TC_INC_DIR . '/functions/activation.php';

        if (!$this->locked() && !@copy(W3TC_INSTALL_FILE_OBJECT_CACHE, W3TC_ADDIN_FILE_OBJECT_CACHE)) {
            w3_writable_error(W3TC_ADDIN_FILE_OBJECT_CACHE);
        }

        $this->schedule();
    }

    /**
     * Deactivate plugin action (called by W3_PluginProxy)
     */
    function deactivate() {
        $this->unschedule();

        if (!$this->locked()) {
            @unlink(W3TC_ADDIN_FILE_OBJECT_CACHE);
        }
    }

    /**
     * Schedules events
     */
    function schedule() {
        if ($this->_config->get_boolean('objectcache.enabled') && $this->_config->get_string('objectcache.engine') == 'file') {
            if (!wp_next_scheduled('w3_objectcache_cleanup')) {
                wp_schedule_event(time(), 'w3_objectcache_cleanup', 'w3_objectcache_cleanup');
            }
        } else {
            $this->unschedule();
        }
    }

    /**
     * Unschedules events
     */
    function unschedule() {
        if (wp_next_scheduled('w3_objectcache_cleanup')) {
            wp_clear_scheduled_hook('w3_objectcache_cleanup');
        }
    }

    /**
     * Does disk cache cleanup
     *
     * @return void
     */
    function cleanup() {
        require_once W3TC_LIB_W3_DIR . '/Cache/File/Cleaner.php';

        @$w3_cache_file_cleaner = & new W3_Cache_File_Cleaner(array(
            'cache_dir' => W3TC_CACHE_FILE_OBJECTCACHE_DIR,
            'clean_timelimit' => $this->_config->get_integer('timelimit.cache_gc')
        ));

        $w3_cache_file_cleaner->clean();
    }

    /**
     * Cron schedules filter
     *
     * @paran array $schedules
     * @return array
     */
    function cron_schedules($schedules) {
        $gc = $this->_config->get_integer('objectcache.file.gc');

        return array_merge($schedules, array(
            'w3_objectcache_cleanup' => array(
                'interval' => $gc,
                'display' => sprintf('[W3TC] Object Cache file GC (every %d seconds)', $gc)
            )
        ));
    }

    /**
     * Change action
     */
    function on_change() {
        static $flushed = false;

        if (!$flushed) {
            $wp_object_cache = & w3_instance('W3_ObjectCache');
            $wp_object_cache->flush();
        }
    }
}
