<?php

/**
 * W3 ObjectCache plugin
 */
if (!defined('W3TC')) {
    die();
}

require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_BrowserCache
 */
class W3_Plugin_BrowserCache extends W3_Plugin {
    /**
     * Runs plugin
     */
    function run() {
        if ($this->_config->get_boolean('browsercache.html.w3tc')) {
            add_action('send_headers', array(
                &$this,
                'send_headers'
            ));
        }

        if ($this->can_ob()) {
            ob_start(array(
                &$this,
                'ob_callback'
            ));
        }
    }

    /**
     * Instantiates worker with admin functionality on demand
     *
     * @return W3_Plugin_BrowserCacheAdmin
     */
    function &get_admin() {
        return w3_instance('W3_Plugin_BrowserCacheAdmin');
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
     * Check if we can start OB
     *
     * @return boolean
     */
    function can_ob() {
        /**
         * Replace feature should be enabled
         */
        if (!$this->_config->get_boolean('browsercache.cssjs.replace') && !$this->_config->get_boolean('browsercache.html.replace') && !$this->_config->get_boolean('browsercache.other.replace')) {
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
     * Output buffer callback
     *
     * @param string $buffer
     * @return mixed
     */
    function ob_callback(&$buffer) {
        if ($buffer != '' && w3_is_xml($buffer)) {
            $domain_url_regexp = w3_get_domain_url_regexp();

            $buffer = preg_replace_callback('~(href|src|action)=[\'"]((' . $domain_url_regexp . ')?(/[^\'"]*\.([a-z-_]+)(\?[^\'"]*)?))[\'"]~Ui', array(
                &$this,
                'link_replace_callback'
            ), $buffer);
        }

        return $buffer;
    }

    /**
     * Link replace callback
     *
     * @param string $matches
     * @return string
     */
    function link_replace_callback($matches) {
        static $id = null, $extensions = null;

        if ($id === null) {
            $id = $this->get_replace_id();
        }

        if ($extensions === null) {
            $extensions = $this->get_replace_extensions();
        }

        list ($match, $attr, $url, , , , , $extension) = $matches;

        if (in_array($extension, $extensions)) {
            $url = w3_remove_query($url);
            $url .= (strstr($url, '?') !== false ? '&amp;' : '?') . $id;

            return sprintf('%s="%s"', $attr, $url);
        }

        return $match;
    }

    /**
     * Returns replace ID
     *
     * @return string
     */
    function get_replace_id() {
        static $cache_id = null;

        if ($cache_id === null) {
            $keys = array(
                'browsercache.cssjs.compression',
                'browsercache.cssjs.expires',
                'browsercache.cssjs.lifetime',
                'browsercache.cssjs.cache.control',
                'browsercache.cssjs.cache.policy',
                'browsercache.cssjs.etag',
                'browsercache.cssjs.w3tc',
                'browsercache.html.compression',
                'browsercache.html.expires',
                'browsercache.html.lifetime',
                'browsercache.html.cache.control',
                'browsercache.html.cache.policy',
                'browsercache.html.etag',
                'browsercache.html.w3tc',
                'browsercache.other.compression',
                'browsercache.other.expires',
                'browsercache.other.lifetime',
                'browsercache.other.cache.control',
                'browsercache.other.cache.policy',
                'browsercache.other.etag',
                'browsercache.other.w3tc'
            );

            $values = array();

            foreach ($keys as $key) {
                $values[] = $this->_config->get($key);
            }

            $cache_id = substr(md5(implode('', $values)), 0, 6);
        }

        return $cache_id;
    }

    /**
     * Returns replace extensions
     *
     * @return array
     */
    function get_replace_extensions() {
        static $extensions = null;

        if ($extensions === null) {
            $types = array();
            $extensions = array();

            if ($this->_config->get_boolean('browsercache.cssjs.replace')) {
                $types = array_merge($types, array_keys($this->_get_cssjs_types()));
            }

            if ($this->_config->get_boolean('browsercache.html.replace')) {
                $types = array_merge($types, array_keys($this->_get_html_types()));
            }

            if ($this->_config->get_boolean('browsercache.other.replace')) {
                $types = array_merge($types, array_keys($this->_get_other_types()));
            }

            foreach ($types as $type) {
                $extensions = array_merge($extensions, explode('|', $type));
            }
        }

        return $extensions;
    }

    /**
     * Send headers
     */
    function send_headers() {
        @header('X-Powered-By: ' . W3TC_POWERED_BY);
    }

    /**
     * Returns CSS/JS mime types
     *
     * @return array
     */
    function _get_cssjs_types() {
        $mime_types = include W3TC_INC_DIR . '/mime/cssjs.php';

        return $mime_types;
    }

    /**
     * Returns HTML mime types
     *
     * @return array
     */
    function _get_html_types() {
        $mime_types = include W3TC_INC_DIR . '/mime/html.php';

        return $mime_types;
    }

    /**
     * Returns other mime types
     *
     * @return array
     */
    function _get_other_types() {
        $mime_types = include W3TC_INC_DIR . '/mime/other.php';

        return $mime_types;
    }

    /**
     * Returns cache config for CDN
     *
     * @return array
     */
    function get_cache_config() {
        $config = array();

        $cssjs_types = $this->_get_cssjs_types();
        $html_types = $this->_get_html_types();
        $other_types = $this->_get_other_types();

        $this->_get_cache_config($config, $cssjs_types, 'cssjs');
        $this->_get_cache_config($config, $html_types, 'html');
        $this->_get_cache_config($config, $other_types, 'other');

        return $config;
    }

    /**
     * Writes cache config
     *
     * @param string $config
     * @param string $mime_types
     * @param array $section
     * @return void
     */
    function _get_cache_config(&$config, $mime_types, $section) {
        $expires = $this->_config->get_boolean('browsercache.' . $section . '.expires');
        $lifetime = $this->_config->get_integer('browsercache.' . $section . '.lifetime');
        $cache_control = $this->_config->get_boolean('browsercache.' . $section . '.cache.control');
        $cache_policy = $this->_config->get_string('browsercache.' . $section . '.cache.policy');
        $etag = $this->_config->get_boolean('browsercache.' . $section . '.etag');
        $w3tc = $this->_config->get_boolean('browsercache.' . $section . '.w3tc');

        foreach ($mime_types as $mime_type) {
            $config[$mime_type] = array(
                'etag' => $etag,
                'w3tc' => $w3tc,
                'lifetime' => ($expires ? $lifetime : 0),
                'cache_control' => ($cache_control ? $cache_policy : false)
            );
        }
    }
}
