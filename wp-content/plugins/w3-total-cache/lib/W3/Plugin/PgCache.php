<?php

/**
 * W3 PgCache plugin
 */
if (!defined('W3TC')) {
    die();
}

require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_PgCache
 */
class W3_Plugin_PgCache extends W3_Plugin {
    /**
     * Runs plugin
     */
    function run() {
        add_filter('cron_schedules', array(
            &$this,
            'cron_schedules'
        ));
    
        if ($this->_config->get_string('pgcache.engine') == 'file' || 
                $this->_config->get_string('pgcache.engine') == 'file_generic') {
            add_action('w3_pgcache_cleanup', array(
                &$this,
                'cleanup'
            ));
        }

        add_action('w3_pgcache_prime', array(
            &$this,
            'prime'
        ));

        add_action('publish_phone', array(
            &$this,
            'on_post_edit'
        ), 0);

        add_action('publish_post', array(
            &$this,
            'on_post_edit'
        ), 0);

        add_action('edit_post', array(
            &$this,
            'on_post_change'
        ), 0);

        add_action('delete_post', array(
            &$this,
            'on_post_edit'
        ), 0);

        add_action('comment_post', array(
            &$this,
            'on_comment_change'
        ), 0);

        add_action('edit_comment', array(
            &$this,
            'on_comment_change'
        ), 0);

        add_action('delete_comment', array(
            &$this,
            'on_comment_change'
        ), 0);

        add_action('wp_set_comment_status', array(
            &$this,
            'on_comment_status'
        ), 0, 2);

        add_action('trackback_post', array(
            &$this,
            'on_comment_change'
        ), 0);

        add_action('pingback_post', array(
            &$this,
            'on_comment_change'
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
        $this->get_admin()->activate();
    }

    /**
     * Deactivate plugin action (called by W3_PluginProxy)
     */
    function deactivate() {
        $this->get_admin()->deactivate();
    }

    /**
     * Does disk cache cleanup
     *
     * @return void
     */
    function cleanup() {
        $this->get_admin()->cleanup();
    }

    /**
     * Prime cache
     *
     * @param integer $start
     * @return void
     */
    function prime($start = 0) {
        $this->get_admin()->prime();
    }

    /**
     * Instantiates worker on demand
     *
     * @return W3_Plugin_PgCacheAdmin
     */
    function &get_admin() {
        return w3_instance('W3_Plugin_PgCacheAdmin');
    }

    /**
     * Cron schedules filter
     *
     * @param array $schedules
     * @return array
     */
    function cron_schedules($schedules) {
        $gc_interval = $this->_config->get_integer('pgcache.file.gc');
        $prime_interval = $this->_config->get_integer('pgcache.prime.interval');

        return array_merge($schedules, array(
            'w3_pgcache_cleanup' => array(
                'interval' => $gc_interval,
                'display' => sprintf('[W3TC] Page Cache file GC (every %d seconds)', $gc_interval)
            ),
            'w3_pgcache_prime' => array(
                'interval' => $prime_interval,
                'display' => sprintf('[W3TC] Page Cache prime (every %d seconds)', $prime_interval)
            )
        ));
    }

    /**
     * Post edit action
     *
     * @param integer $post_id
     */
    function on_post_edit($post_id) {
        if ($this->_config->get_boolean('pgcache.cache.flush')) {
            $this->on_change();
        } else {
            $this->on_post_change($post_id);
        }
    }

    /**
     * Post change action
     *
     * @param integer $post_id
     */
    function on_post_change($post_id) {
        static $flushed_posts = array();

        if (!in_array($post_id, $flushed_posts)) {
            $w3_pgcache = & w3_instance('W3_PgCacheFlush');
            $w3_pgcache->flush_post($post_id);

            $flushed_posts[] = $post_id;
        }
    }

    /**
     * Comment change action
     *
     * @param integer $comment_id
     */
    function on_comment_change($comment_id) {
        $post_id = 0;

        if ($comment_id) {
            $comment = get_comment($comment_id, ARRAY_A);
            $post_id = !empty($comment['comment_post_ID']) ? (int) $comment['comment_post_ID'] : 0;
        }

        $this->on_post_change($post_id);
    }

    /**
     * Comment status action
     *
     * @param integer $comment_id
     * @param string $status
     */
    function on_comment_status($comment_id, $status) {
        if ($status === 'approve' || $status === '1') {
            $this->on_comment_change($comment_id);
        }
    }

    /**
     * Change action
     */
    function on_change() {
        static $flushed = false;

        if (!$flushed) {
            $w3_pgcache = & w3_instance('W3_PgCacheFlush');
            $w3_pgcache->flush();
        }
    }
}