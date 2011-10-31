<?php

/**
 * W3 DbCache plugin
 */
if (!defined('W3TC')) {
    die();
}

require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_DbCache
 */
class W3_Plugin_DbCache extends W3_Plugin {
    /**
     * Runs plugin
     */
    function run() {
        add_filter('cron_schedules', array(
            &$this,
            'cron_schedules'
        ));

        if ($this->_config->get_string('dbcache.engine') == 'file') {
            add_action('w3_dbcache_cleanup', array(
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
        
        if (!$this->locked() && !@copy(W3TC_INSTALL_FILE_DB, W3TC_ADDIN_FILE_DB)) {
            w3_writable_error(W3TC_ADDIN_FILE_DB);
        }
        
        $this->schedule();
    }
    
    /**
     * Deactivate plugin action (called by W3_PluginProxy)
     */
    function deactivate() {
        $this->unschedule();
        
        if (!$this->locked()) {
            @unlink(W3TC_ADDIN_FILE_DB);
        }
    }
    
    /**
     * Schedules events
     */
    function schedule() {
        if ($this->_config->get_boolean('dbcache.enabled') && $this->_config->get_string('dbcache.engine') == 'file') {
            if (!wp_next_scheduled('w3_dbcache_cleanup')) {
                wp_schedule_event(time(), 'w3_dbcache_cleanup', 'w3_dbcache_cleanup');
            }
        } else {
            $this->unschedule();
        }
    }
    
    /**
     * Unschedules events
     */
    function unschedule() {
        if (wp_next_scheduled('w3_dbcache_cleanup')) {
            wp_clear_scheduled_hook('w3_dbcache_cleanup');
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
            'cache_dir' => W3TC_CACHE_FILE_DBCACHE_DIR,
            'clean_timelimit' => $this->_config->get_integer('timelimit.cache_gc')
        ));
        
        $w3_cache_file_cleaner->clean();
    }
    
    /**
     * Cron schedules filter
     *
     * @param array $schedules
     * @return array
     */
    function cron_schedules($schedules) {
        $gc = $this->_config->get_integer('dbcache.file.gc');

        return array_merge($schedules, array(
            'w3_dbcache_cleanup' => array(
                'interval' => $gc,
                'display' => sprintf('[W3TC] Database Cache file GC (every %d seconds)', $gc)
            )
        ));
    }

    /**
     * Change action
     */
    function on_change() {
        static $flushed = false;

        if (!$flushed) {
            require_once W3TC_LIB_W3_DIR . '/Db.php';
            @$w3_db = & W3_Db::instance();

            $w3_db->flush_cache();
        }
    }
}
