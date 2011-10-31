<?php

/**
 * W3 Total Cache plugin
 */
if (!defined('W3TC')) {
    die();
}

require_once W3TC_LIB_W3_DIR . '/Plugin.php';
require_once W3TC_LIB_W3_DIR . '/PluginProxy.php';

/**
 * Class W3_Plugin_TotalCache
 */
class W3_Plugin_TotalCache extends W3_Plugin {
    /**
     * Runs plugin
     *
     * @return void
     */
    function run() {
        register_activation_hook(W3TC_FILE, array(
            &$this,
            'activate'
        ));

        register_deactivation_hook(W3TC_FILE, array(
            &$this,
            'deactivate'
        ));

        add_action('init', array(
            &$this,
            'init'
        ));

        add_action('admin_bar_menu', array(
            &$this,
            'admin_bar_menu'
        ), 150);

        if (isset($_REQUEST['w3tc_theme']) && isset($_SERVER['HTTP_USER_AGENT']) &&
                $_SERVER['HTTP_USER_AGENT'] == W3TC_POWERED_BY) {
            add_filter('template', array(
                &$this,
                'template_preview'
            ));

            add_filter('stylesheet', array(
                &$this,
                'stylesheet_preview'
            ));
        } elseif ($this->_config->get_boolean('mobile.enabled') || $this->_config->get_boolean('referrer.enabled')) {
            add_filter('template', array(
                &$this,
                'template'
            ));

            add_filter('stylesheet', array(
                &$this,
                'stylesheet'
            ));
        }

        /**
         * CloudFlare support
         */
        if ($this->_config->get_boolean('cloudflare.enabled')) {
            add_action('wp_set_comment_status', array(
                &$this,
                'cloudflare_set_comment_status'
            ), 1, 2);

            require_once W3TC_LIB_W3_DIR . '/CloudFlare.php';
            @$w3_cloudflare =& new W3_CloudFlare();

            $w3_cloudflare->fix_remote_addr();

        }

        if ($this->_config->get_string('common.support') == 'footer') {
            add_action('wp_footer', array(
                &$this,
                'footer'
            ));
        }

        if ($this->can_ob()) {
            ob_start(array(
                &$this,
                'ob_callback'
            ));
        }

        /**
         * Create plugin proxies, they should live during request since attaches to actions
         */
        $this->_plugins = array(
            new W3_PluginProxy('W3_Plugin_DbCache', 'dbcache.enabled'),
            new W3_PluginProxy('W3_Plugin_ObjectCache', 'objectcache.enabled'),
            new W3_PluginProxy('W3_Plugin_PgCache', 'pgcache.enabled'),
            new W3_PluginProxy('W3_Plugin_Cdn', 'cdn.enabled'),
            new W3_PluginProxy('W3_Plugin_BrowserCache', 'browsercache.enabled'),
            new W3_PluginProxy('W3_Plugin_Minify', 'minify.enabled'));

        if (is_admin()) {
            $plugin_totalcacheadmin = & w3_instance('W3_Plugin_TotalCacheAdmin');
            array_push($this->_plugins, $plugin_totalcacheadmin);
        }

        /**
         * Run plugins
         */
        foreach ($this->_plugins as $plugin)
            $plugin->run();
    }

    /**
     * Activate plugin action
     *
     * @return void
     */
    function activate() {
        $activation_worker = & w3_instance('W3_Plugin_TotalCacheActivation');
        $activation_worker->activate();
    }

    /**
     * Deactivate plugin action
     *
     * @return void
     */
    function deactivate() {
        $activation_worker = & w3_instance('W3_Plugin_TotalCacheActivation');
        $activation_worker->deactivate();
    }

    /**
     * Init action
     *
     * @return void
     */
    function init() {
        /**
         * Check request and handle w3tc_request_data requests
         */
        $pos = strpos($_SERVER['REQUEST_URI'], '/w3tc_request_data/');

        if ($pos !== false) {
            $hash = substr($_SERVER['REQUEST_URI'], $pos + 19, 32);

            if (strlen($hash) == 32) {
                $request_data = (array) get_option('w3tc_request_data');

                if (isset($request_data[$hash])) {
                    echo '<pre>';
                    foreach ($request_data[$hash] as $key => $value) {
                        printf("%s: %s\n", $key, $value);
                    }
                    echo '</pre>';

                    unset($request_data[$hash]);
                    update_option('w3tc_request_data', $request_data);
                } else {
                    echo 'Requested hash expired or invalid';
                }

                exit();
            }
        }

        /**
         * Check for rewrite test request
         */
        require_once W3TC_LIB_W3_DIR . '/Request.php';

        $rewrite_test = W3_Request::get_boolean('w3tc_rewrite_test');

        if ($rewrite_test) {
            echo 'OK';
            exit();
        }
    }

    /**
     * Admin bar menu
     *
     * @return void
     */
    function admin_bar_menu() {
        global $wp_admin_bar;

        if (current_user_can('manage_options')) {
            $menu_items = array(
                array(
                    'id' => 'w3tc',
                    'title' => 'Performance',
                    'href' => admin_url('admin.php?page=w3tc_general')
                ),
                array(
                    'id' => 'w3tc-empty-caches',
                    'parent' => 'w3tc',
                    'title' => 'Empty All Caches',
                    'href' => wp_nonce_url(admin_url('admin.php?page=w3tc_general&amp;w3tc_flush_all'), 'w3tc')
                ),
                array(
                    'id' => 'w3tc-faq',
                    'parent' => 'w3tc',
                    'title' => 'FAQ',
                    'href' => admin_url('admin.php?page=w3tc_faq')
                ),
                array(
                    'id' => 'w3tc-support',
                    'parent' => 'w3tc',
                    'title' => '<span style="color: red; background: none;">Support</span>',
                    'href' => admin_url('admin.php?page=w3tc_support')
                )
            );

            if ($this->_config->get_boolean('cloudflare.enabled')) {
                $menu_items = array_merge($menu_items, array(
                    array(
                        'id' => 'cloudflare',
                        'title' => 'CloudFlare',
                        'href' => 'https://www.cloudflare.com'
                    ),
                    array(
                        'id' => 'cloudflare-my-websites',
                        'parent' => 'cloudflare',
                        'title' => 'My Websites',
                        'href' => 'https://www.cloudflare.com/my-websites.html'
                    ),
                    array(
                        'id' => 'cloudflare-analytics',
                        'parent' => 'cloudflare',
                        'title' => 'Analytics',
                        'href' => 'https://www.cloudflare.com/analytics.html'
                    ),
                    array(
                        'id' => 'cloudflare-account',
                        'parent' => 'cloudflare',
                        'title' => 'Account',
                        'href' => 'https://www.cloudflare.com/my-account.html'
                    )
                ));
            }

            foreach ($menu_items as $menu_item) {
                $wp_admin_bar->add_menu($menu_item);
            }
        }
    }

    /**
     * Template filter
     *
     * @param $template
     * @return string
     */
    function template($template) {
        $w3_mobile = & w3_instance('W3_Mobile');

        $mobile_template = $w3_mobile->get_template();

        if ($mobile_template) {
            return $mobile_template;
        } else {
            $w3_referrer = & w3_instance('W3_Referrer');

            $referrer_template = $w3_referrer->get_template();

            if ($referrer_template) {
                return $referrer_template;
            }
        }

        return $template;
    }

    /**
     * Stylesheet filter
     *
     * @param $stylesheet
     * @return string
     */
    function stylesheet($stylesheet) {
        $w3_mobile = & w3_instance('W3_Mobile');

        $mobile_stylesheet = $w3_mobile->get_stylesheet();

        if ($mobile_stylesheet) {
            return $mobile_stylesheet;
        } else {
            $w3_referrer = & w3_instance('W3_Referrer');

            $referrer_stylesheet = $w3_referrer->get_stylesheet();

            if ($referrer_stylesheet) {
                return $referrer_stylesheet;
            }
        }

        return $stylesheet;
    }

    /**
     * Template filter
     *
     * @param $template
     * @return string
     */
    function template_preview($template) {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        $theme_name = W3_Request::get_string('w3tc_theme');

        $theme = get_theme($theme_name);

        if ($theme) {
            return $theme['Template'];
        }

        return $template;
    }

    /**
     * Stylesheet filter
     *
     * @param $stylesheet
     * @return string
     */
    function stylesheet_preview($stylesheet) {
        require_once W3TC_LIB_W3_DIR . '/Request.php';
        $theme_name = W3_Request::get_string('w3tc_theme');

        $theme = get_theme($theme_name);

        if ($theme) {
            return $theme['Stylesheet'];
        }

        return $stylesheet;
    }

    /**
     * Footer plugin action
     *
     * @return void
     */
    function footer() {
        echo '<div style="text-align: center;">Performance Optimization <a href="http://www.w3-edge.com/wordpress-plugins/" rel="external">WordPress Plugins</a> by W3 EDGE</div>';
    }

    /**
     * Output buffering callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer) {
        global $wpdb;

        if ($buffer != '' && w3_is_xml($buffer)) {
            if (w3_is_database_error($buffer)) {
                status_header(503);
            } else {
                /**
                 * Replace links for preview mode
                 */
                if (w3_is_preview_mode() && isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] != W3TC_POWERED_BY) {
                    $domain_url_regexp = w3_get_domain_url_regexp();

                    $buffer = preg_replace_callback('~(href|src|action)=([\'"])(' . $domain_url_regexp . ')?(/[^\'"]*)~', array(
                        &$this,
                        'link_replace_callback'
                    ), $buffer);
                }

                /**
                 * Add footer comment
                 */
                $date = date_i18n('Y-m-d H:i:s');
                $host = (!empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');

                if ($this->_config->get_string('common.support') != '' || $this->_config->get_boolean('common.tweeted')) {
                    $buffer .= sprintf("\r\n<!-- Served from: %s @ %s by W3 Total Cache -->", w3_escape_comment($host), $date);
                } else {
                    $strings = array();

                    if ($this->_config->get_boolean('minify.enabled') && !$this->_config->get_boolean('minify.debug')) {
                        $w3_plugin_minify = & w3_instance('W3_Plugin_Minify');

                        $strings[] = sprintf("Minified using %s%s", w3_get_engine_name($this->_config->get_string('minify.engine')), ($w3_plugin_minify->minify_reject_reason != '' ? sprintf(' (%s)', $w3_plugin_minify->minify_reject_reason) : ''));
                    }

                    if ($this->_config->get_boolean('pgcache.enabled') && !$this->_config->get_boolean('pgcache.debug')) {
                        $w3_pgcache = & w3_instance('W3_PgCache');

                        $strings[] = sprintf("Page Caching using %s%s", w3_get_engine_name($this->_config->get_string('pgcache.engine')), ($w3_pgcache->cache_reject_reason != '' ? sprintf(' (%s)', $w3_pgcache->cache_reject_reason) : ''));
                    }

                    if ($this->_config->get_boolean('dbcache.enabled') && !$this->_config->get_boolean('dbcache.debug') && is_a($wpdb, 'W3_Db')) {
                        $append = (is_user_logged_in() ? ' (user is logged in)' : '');

                        if ($wpdb->query_hits) {
                            $strings[] = sprintf("Database Caching %d/%d queries in %.3f seconds using %s%s", $wpdb->query_hits, $wpdb->query_total, $wpdb->time_total, w3_get_engine_name($this->_config->get_string('dbcache.engine')), $append);
                        } else {
                            $strings[] = sprintf("Database Caching using %s%s", w3_get_engine_name($this->_config->get_string('dbcache.engine')), $append);
                        }
                    }

                    if ($this->_config->get_boolean('objectcache.enabled') && !$this->_config->get_boolean('objectcache.debug')) {
                        $w3_objectcache = & w3_instance('W3_ObjectCache');

                        $strings[] = sprintf("Object Caching %d/%d objects using %s", $w3_objectcache->cache_hits, $w3_objectcache->cache_total, w3_get_engine_name($this->_config->get_string('objectcache.engine')));
                    }

                    if ($this->_config->get_boolean('cdn.enabled') && !$this->_config->get_boolean('cdn.debug')) {
                        $w3_plugin_cdn = & w3_instance('W3_Plugin_Cdn');
                        $w3_plugin_cdncommon = & w3_instance('W3_Plugin_CdnCommon');
                        $cdn = & $w3_plugin_cdncommon->get_cdn();
                        $via = $cdn->get_via();

                        $strings[] = sprintf("Content Delivery Network via %s%s", ($via ? $via : 'N/A'), ($w3_plugin_cdn->cdn_reject_reason != '' ? sprintf(' (%s)', $w3_plugin_cdn->cdn_reject_reason) : ''));
                    }

                    $buffer .= "\r\n<!-- Performance optimized by W3 Total Cache. Learn more: http://www.w3-edge.com/wordpress-plugins/\r\n";

                    if (count($strings)) {
                        $buffer .= "\r\n" . implode("\r\n", $strings) . "\r\n";
                    }

                    $buffer .= sprintf("\r\nServed from: %s @ %s -->", w3_escape_comment($host), $date);
                }
            }
        }

        return $buffer;
    }

    /**
     * Check if we can do modify contents
     *
     * @return boolean
     */
    function can_ob() {
        $enabled = w3_is_preview_mode();
        $enabled = $enabled || $this->_config->get_boolean('pgcache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('dbcache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('objectcache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('browsercache.enabled');
        $enabled = $enabled || $this->_config->get_boolean('minify.enabled');
        $enabled = $enabled || $this->_config->get_boolean('cdn.enabled');

        /**
         * Check if plugin enabled
         */
        if (!$enabled) {
            return false;
        }

        /**
         * Skip if admin
         */
        if (defined('WP_ADMIN')) {
            return false;
        }

        /**
         * Skip if doing AJAX
         */
        if (defined('DOING_AJAX')) {
            return false;
        }

        /**
         * Skip if doing cron
         */
        if (defined('DOING_CRON')) {
            return false;
        }

        /**
         * Skip if APP request
         */
        if (defined('APP_REQUEST')) {
            return false;
        }

        /**
         * Skip if XMLRPC request
         */
        if (defined('XMLRPC_REQUEST')) {
            return false;
        }

        /**
         * Check for WPMU's and WP's 3.0 short init
         */
        if (defined('SHORTINIT') && SHORTINIT) {
            return false;
        }

        /**
         * Check User Agent
         */
        if (isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], W3TC_POWERED_BY) !== false) {
            return false;
        }

        return true;
    }

    /**
     * Preview link replace callback
     *
     * @param array $matches
     * @return string
     */
    function link_replace_callback($matches) {
        list (, $attr, $quote, $domain_url, , , $path) = $matches;

        $path .= (strstr($path, '?') !== false ? '&amp;' : '?') . 'w3tc_preview=1';

        return sprintf('%s=%s%s%s', $attr, $quote, $domain_url, $path);
    }

    /**
     * Now actually allow CF to see when a comment is approved/not-approved.
     *
     * @param int $id
     * @param string $status
     * @return void
     */
    function cloudflare_set_comment_status($id, $status) {
        if ($status == 'spam') {
            $email = $this->_config->get_string('cloudflare.email');
            $key = $this->_config->get_string('cloudflare.key');

            if ($email && $key) {
                require_once W3TC_LIB_W3_DIR . '/CloudFlare.php';
                @$w3_cloudflare =& new W3_CloudFlare(array(
                    'email' => $email,
                    'key' => $key
                ));

                $comment = get_comment($id);

                $value = array(
                    'a' => $comment->comment_author,
                    'am' => $comment->comment_author_email,
                    'ip' => $comment->comment_author_IP,
                    'con' => substr($comment->comment_content, 0, 100)
                );

                $w3_cloudflare->external_event('WP_SPAM', json_encode($value));
            }
        }
    }
}
