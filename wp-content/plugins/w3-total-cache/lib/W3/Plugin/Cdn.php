<?php

/**
 * W3 Total Cache CDN Plugin
 */
if (!defined('W3TC')) {
    die();
}

require_once W3TC_INC_DIR . '/functions/file.php';
require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_Plugin_Cdn
 */
class W3_Plugin_Cdn extends W3_Plugin {

    /**
     * CDN reject reason
     *
     * @var string
     */
    var $cdn_reject_reason = '';

    /**
     * Run plugin
     */
    function run() {
        add_filter('cron_schedules', array(
            &$this,
            'cron_schedules'
        ));

        $cdn_engine = $this->_config->get_string('cdn.engine');

        if (!w3_is_cdn_mirror($cdn_engine)) {
            add_action('delete_attachment', array(
                &$this,
                'delete_attachment'
            ));

            add_filter('update_attached_file', array(
                &$this,
                'update_attached_file'
            ));

            add_filter('wp_update_attachment_metadata', array(
                &$this,
                'update_attachment_metadata'
            ));

            add_action('w3_cdn_cron_queue_process', array(
                &$this,
                'cron_queue_process'
            ));

            add_action('w3_cdn_cron_upload', array(
                &$this,
                'cron_upload'
            ));

            add_action('switch_theme', array(
                &$this,
                'switch_theme'
            ));

            add_filter('update_feedback', array(
                &$this,
                'update_feedback'
            ));
        }

        /**
         * Start rewrite engine
         */
        if ($this->can_cdn()) {
            ob_start(array(
                &$this,
                'ob_callback'
            ));
        }
    }

    /**
     * Instantiates worker with admin functionality on demand
     *
     * @return W3_Plugin_CdnAdmin
     */
    function &_get_admin() {
        return w3_instance('W3_Plugin_CdnAdmin');
    }

    /**
     * Instantiates worker with common functionality on demand
     *
     * @return W3_Plugin_CdnCommon
     */
    function &_get_common() {
        return w3_instance('W3_Plugin_CdnCommon');
    }

    /**
     * Activate plugin action (called by W3_PluginProxy)
     */
    function activate() {
        $this->_get_admin()->activate();
    }

    /**
     * Deactivate plugin action (called by W3_PluginProxy)
     */
    function deactivate() {
        $this->_get_admin()->deactivate();
    }

    /**
     * Cron queue process event
     */
    function cron_queue_process() {
        $queue_limit = $this->_config->get_integer('cdn.queue.limit');
        $this->_get_admin()->queue_process($queue_limit);
    }

    /**
     * Cron upload event
     */
    function cron_upload() {
        $files = $this->get_files();
        $document_root = w3_get_document_root();

        $upload = array();
        $results = array();

        foreach ($files as $file) {
            $upload[$document_root . '/' . $file] = $file;
        }

        $this->_get_common()->upload($upload, true, $results);
    }

    /**
     * Update attachment file
     *
     * Upload _wp_attached_file
     *
     * @param string $attached_file
     * @return string
     */
    function update_attached_file($attached_file) {
        $files = $this->_get_common()->get_files_for_upload($attached_file);
        $files = apply_filters('w3tc_cdn_update_attachment', $files);

        $results = array();

        $this->_get_common()->upload($files, true, $results);

        return $attached_file;
    }

    /**
     * On attachment delete action
     *
     * Delete _wp_attached_file, _wp_attachment_metadata, _wp_attachment_backup_sizes
     *
     * @param integer $attachment_id
     */
    function delete_attachment($attachment_id) {
        $files = $this->_get_common()->get_attachment_files($attachment_id);
        $files = apply_filters('w3tc_cdn_delete_attachment', $files);

        $results = array();

        $this->_get_common()->delete($files, true, $results);
    }

    /**
     * Update attachment metadata filter
     *
     * Upload _wp_attachment_metadata
     *
     * @param array $metadata
     * @return array
     */
    function update_attachment_metadata($metadata) {
        $files = $this->_get_common()->get_metadata_files($metadata);
        $files = apply_filters('w3tc_cdn_update_attachment_metadata', $files);

        $results = array();

        $this->_get_common()->upload($files, true, $results);

        return $metadata;
    }

    /**
     * Cron schedules filter
     *
     * @param array $schedules
     * @return array
     */
    function cron_schedules($schedules) {
        $queue_interval = $this->_config->get_integer('cdn.queue.interval');
        $autoupload_interval = $this->_config->get_integer('cdn.autoupload.interval');

        return array_merge($schedules, array(
            'w3_cdn_cron_queue_process' => array(
                'interval' => $queue_interval,
                'display' => sprintf('[W3TC] CDN queue process (every %d seconds)', $queue_interval)
            ),
            'w3_cdn_cron_upload' => array(
                'interval' => $autoupload_interval,
                'display' => sprintf('[W3TC] CDN auto upload (every %d seconds)', $autoupload_interval)
            )
        ));
    }

    /**
     * Switch theme action
     */
    function switch_theme() {
        $this->_config->set('notes.theme_changed', true);
        $this->_config->save();
    }

    /**
     * WP Upgrade action hack
     *
     * @param string $message
     */
    function update_feedback($message) {
        if ($message == __('Upgrading database')) {
            $this->_config->set('notes.wp_upgraded', true);
            $this->_config->save();
        }
    }

    /**
     * OB Callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer) {
        if ($buffer != '' && w3_is_xml($buffer)) {
            if ($this->can_cdn2($buffer)) {
                $regexps = array();
                $site_path = w3_get_site_path();
                $domain_url_regexp = w3_get_domain_url_regexp();

                if ($this->_config->get_boolean('cdn.uploads.enable')) {
                    require_once W3TC_INC_DIR . '/functions/http.php';

                    $upload_info = w3_upload_info();

                    if ($upload_info) {
                        if (preg_match('~' . $domain_url_regexp . '~i', $upload_info['baseurl'])) {
                            $regexps[] = '~(["\'])((' . $domain_url_regexp . ')?(' . w3_preg_quote($upload_info['baseurlpath']) . '([^"\'>]+)))~';
                        } else {
                            $regexps[] = '~(["\'])((' . w3_preg_quote($upload_info['baseurl']) . ')(([^"\'>]+)))~';
                        }
                    }
                }

                if ($this->_config->get_boolean('cdn.includes.enable')) {
                    $mask = $this->_config->get_string('cdn.includes.files');
                    if ($mask != '') {
                        $regexps[] = '~(["\'])((' . $domain_url_regexp . ')?(' . w3_preg_quote($site_path . WPINC) . '/(' . $this->get_regexp_by_mask($mask) . ')))~';
                    }
                }

                if ($this->_config->get_boolean('cdn.theme.enable')) {
                    $theme_dir = preg_replace('~' . $domain_url_regexp . '~i', '', get_theme_root_uri());

                    $mask = $this->_config->get_string('cdn.theme.files');

                    if ($mask != '') {
                        $regexps[] = '~(["\'])((' . $domain_url_regexp . ')?(' . w3_preg_quote($theme_dir) . '/(' . $this->get_regexp_by_mask($mask) . ')))~';
                    }
                }

                if ($this->_config->get_boolean('cdn.minify.enable')) {
                    if ($this->_config->get_boolean('minify.auto')) {
                        $regexps[] = '~(["\'])((' . $domain_url_regexp . ')?(' . w3_preg_quote($site_path . W3TC_CONTENT_MINIFY_DIR_NAME) . '/[a-f0-9]+\.[a-f0-9]+\.(css|js)))~U';
                    } else {
                        $regexps[] = '~(["\'])((' . $domain_url_regexp . ')?(' . w3_preg_quote($site_path . W3TC_CONTENT_MINIFY_DIR_NAME) . '/[a-f0-9]+/.+\.include(-(footer|body))?(-nb)?\.[a-f0-9]+\.(css|js)))~U';
                    }
                }

                if ($this->_config->get_boolean('cdn.custom.enable')) {
                    $masks = $this->_config->get_array('cdn.custom.files');
                    $masks = array_map('w3_parse_path', $masks);

                    if (count($masks)) {
                        $mask_regexps = array();

                        foreach ($masks as $mask) {
                            if ($mask != '') {
                                $mask = w3_normalize_file($mask);
                                $mask_regexps[] = $this->get_regexp_by_mask($mask);
                            }
                        }

                        $regexps[] = '~(["\'])((' . $domain_url_regexp . ')?(' . w3_preg_quote($site_path) . '(' . implode('|', $mask_regexps) . ')))~i';
                    }
                }

                foreach ($regexps as $regexp) {
                    $buffer = preg_replace_callback($regexp, array(
                        &$this,
                        'link_replace_callback'
                    ), $buffer);
                }
            }

            if ($this->_config->get_boolean('cdn.debug')) {
                $buffer .= "\r\n\r\n" . $this->get_debug_info();
            }
        }

        return $buffer;
    }

    /**
     * Returns array of files to upload
     *
     * @return array
     */
    function get_files() {
        $files = array();

        if ($this->_config->get_boolean('cdn.includes.enable')) {
            $files = array_merge($files, $this->get_files_includes());
        }

        if ($this->_config->get_boolean('cdn.theme.enable')) {
            $files = array_merge($files, $this->get_files_theme());
        }

        if ($this->_config->get_boolean('cdn.minify.enable')) {
            $files = array_merge($files, $this->get_files_minify());
        }

        if ($this->_config->get_boolean('cdn.custom.enable')) {
            $files = array_merge($files, $this->get_files_custom());
        }

        return $files;
    }

    /**
     * Exports includes to CDN
     *
     * @return array
     */
    function get_files_includes() {
        $includes_root = w3_path(ABSPATH . WPINC);
        $site_root = w3_get_site_root();
        $includes_path = ltrim(str_replace($site_root, rtrim(w3_get_site_path(), '/'), $includes_root), '/');

        $files = $this->search_files($includes_root, $includes_path, $this->_config->get_string('cdn.includes.files'));

        return $files;
    }

    /**
     * Exports theme to CDN
     *
     * @return array
     */
    function get_files_theme() {
        /**
         * If mobile or referrer support enabled
         * we should upload whole themes directory
         */
        if ($this->_config->get_boolean('mobile.enabled') || $this->_config->get_boolean('referrer.enabled')) {
            $themes_root = get_theme_root();
        } else {
            $themes_root = get_stylesheet_directory();
        }

        $themes_root = w3_path($themes_root);
        $site_root = w3_get_site_root();
        $themes_path = ltrim(str_replace($site_root, rtrim(w3_get_site_path(), '/'), $themes_root), '/');

        $files = $this->search_files($themes_root, $themes_path, $this->_config->get_string('cdn.theme.files'));

        return $files;
    }

    /**
     * Exports min files to CDN
     *
     * @return array
     */
    function get_files_minify() {
        $files = array();

        if (W3TC_PHP5 && $this->_config->get_boolean('minify.rewrite') && (!$this->_config->get_boolean('minify.auto') || w3_is_cdn_mirror($this->_config->get_string('cdn.engine')))) {
            require_once W3TC_INC_DIR . '/functions/http.php';

            $minify = & w3_instance('W3_Plugin_Minify');

            $document_root = w3_get_document_root();
            $site_root = w3_get_site_root();
            $minify_root = w3_path(W3TC_CACHE_FILE_MINIFY_DIR);
            $minify_path = ltrim(str_replace($site_root, rtrim(w3_get_site_path(), '/'), $minify_root), '/');

            $urls = $minify->get_urls();

            if ($this->_config->get_string('minify.engine') == 'file') {
                foreach ($urls as $url) {
                    w3_http_get($url);
                }

                $files = $this->search_files($minify_root, $minify_path, '*.css;*.js');
            } else {
                foreach ($urls as $url) {
                    $file = w3_normalize_file_minify($url);
                    $file = w3_translate_file($file);

                    if (!w3_is_url($file)) {
                        $file = $document_root . '/' . $file;
                        $file = ltrim(str_replace($minify_root, '', $file), '/');

                        $dir = dirname($file);

                        if ($dir) {
                            w3_mkdir($dir, 0777, $minify_root);
                        }

                        if (w3_download($url, $minify_root . '/' . $file) !== false) {
                            $files[] = $minify_path . '/' . $file;
                        }
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Exports custom files to CDN
     *
     * @return array
     */
    function get_files_custom() {
        $files = array();
        $document_root = w3_get_document_root();
        $custom_files = $this->_config->get_array('cdn.custom.files');
        $custom_files = array_map('w3_parse_path', $custom_files);

        foreach ($custom_files as $custom_file) {
            if ($custom_file != '') {
                $custom_file = w3_normalize_file($custom_file);
                $dir = trim(dirname($custom_file), '/\\');

                if ($dir == '.') {
                    $dir = '';
                }

                $mask = basename($custom_file);
                $files = array_merge($files, $this->search_files($document_root . '/' . $dir, $dir, $mask));
            }
        }

        return $files;
    }

    /**
     * Link replace callback
     *
     * @param array $matches
     * @return string
     */
    function link_replace_callback($matches) {
        global $wpdb;
        static $queue = null, $reject_files = null;

        list($match, $quote, $url, , , , $path) = $matches;

        $path = ltrim($path, '/');

        /**
         * Check if URL was already replaced
         */
        if (isset($this->replaced_urls[$url])) {
            return $quote . $this->replaced_urls[$url];
        }

        /**
         * Check URL for rejected files
         */
        if ($reject_files === null) {
            $reject_files = $this->_config->get_array('cdn.reject.files');
        }

        foreach ($reject_files as $reject_file) {
            if ($reject_file != '') {
                $reject_file = w3_normalize_file($reject_file);
                $reject_file_regexp = '~^(' . $this->get_regexp_by_mask($reject_file) . ')~i';

                if (preg_match($reject_file_regexp, $path)) {
                    return $match;
                }
            }
        }

        /**
         * Don't replace URL for files that are in the CDN queue
         */
        if ($queue === null) {
            if (!w3_is_cdn_mirror($this->_config->get_string('cdn.engine'))) {
                $sql = sprintf('SELECT remote_path FROM %s', $wpdb->prefix . W3TC_CDN_TABLE_QUEUE);
                $queue = $wpdb->get_col($sql);
            } else {
                $queue = false;
            }
        }

        if ($queue && in_array($path, $queue)) {
            return $match;
        }

        /**
         * Do replacement
         */
        $cdn = & $this->_get_common()->get_cdn();

        $new_url = $cdn->format_url($path);

        if ($new_url) {
            $this->replaced_urls[$url] = $new_url;

            return $quote . $new_url;
        }

        return $match;
    }

    /**
     * Search files
     *
     * @param string $search_dir
     * @param string $base_dir
     * @param string $mask
     * @param boolean $recursive
     * @return array
     */
    function search_files($search_dir, $base_dir, $mask = '*.*', $recursive = true) {
        static $stack = array();
        $files = array();
        $ignore = array(
            '.svn',
            '.git',
            '.DS_Store',
            'CVS',
            'Thumbs.db',
            'desktop.ini'
        );

        $dir = @opendir($search_dir);

        if ($dir) {
            while (($entry = @readdir($dir)) !== false) {
                if ($entry != '.' && $entry != '..' && !in_array($entry, $ignore)) {
                    $path = $search_dir . '/' . $entry;

                    if (@is_dir($path) && $recursive) {
                        array_push($stack, $entry);
                        $files = array_merge($files, $this->search_files($path, $base_dir, $mask, $recursive));
                        array_pop($stack);
                    } else {
                        $regexp = '~^(' . $this->get_regexp_by_mask($mask) . ')$~i';

                        if (preg_match($regexp, $entry)) {
                            $files[] = ($base_dir != '' ? $base_dir . '/' : '') . (($p = implode('/', $stack)) != '' ? $p . '/' : '') . $entry;
                        }
                    }
                }
            }

            @closedir($dir);
        }

        return $files;
    }

    /**
     * Returns regexp by mask
     *
     * @param string $mask
     * @return string
     */
    function get_regexp_by_mask($mask) {
        $mask = trim($mask);
        $mask = w3_preg_quote($mask);

        $mask = str_replace(array(
            '\*',
            '\?',
            ';'
        ), array(
            '@ASTERISK@',
            '@QUESTION@',
            '|'
        ), $mask);

        $regexp = str_replace(array(
            '@ASTERISK@',
            '@QUESTION@'
        ), array(
            '[^\\?\\*:\\|"<>]*',
            '[^\\?\\*:\\|"<>]'
        ), $mask);

        return $regexp;
    }

    /**
     * Returns debug info
     *
     * @return string
     */
    function get_debug_info() {
        $debug_info = "<!-- W3 Total Cache: CDN debug info:\r\n";
        $debug_info .= sprintf("%s%s\r\n", str_pad('Engine: ', 20), $this->_config->get_string('cdn.engine'));

        if ($this->cdn_reject_reason) {
            $debug_info .= sprintf("%s%s\r\n", str_pad('Reject reason: ', 20), $this->cdn_reject_reason);
        }

        if (count($this->replaced_urls)) {
            $debug_info .= "\r\nReplaced URLs:\r\n";

            foreach ($this->replaced_urls as $old_url => $new_url) {
                $debug_info .= sprintf("%s => %s\r\n", w3_escape_comment($old_url), w3_escape_comment($new_url));
            }
        }

        $debug_info .= '-->';

        return $debug_info;
    }

    /**
     * Check if we can do CDN logic
     * @return boolean
     */
    function can_cdn() {
        /**
         * Skip if admin
         */
        if (defined('WP_ADMIN')) {
            $this->cdn_reject_reason = 'wp-admin';

            return false;
        }

        /**
         * Check for WPMU's and WP's 3.0 short init
         */
        if (defined('SHORTINIT') && SHORTINIT) {
            $this->cdn_reject_reason = 'Short init';

            return false;
        }

        /**
         * Check User agent
         */
        if (!$this->check_ua()) {
            $this->cdn_reject_reason = 'user agent is rejected';

            return false;
        }

        /**
         * Check request URI
         */
        if (!$this->check_request_uri()) {
            $this->cdn_reject_reason = 'request URI is rejected';

            return false;
        }

        return true;
    }

    /**
     * Returns true if we can do CDN logic
     *
     * @param $buffer
     * @return string
     */
    function can_cdn2(&$buffer) {
        /**
         * Check for database error
         */
        if (w3_is_database_error($buffer)) {
            $this->cdn_reject_reason = 'Database Error occurred';

            return false;
        }

        /**
         * Check for DONOTCDN constant
         */
        if (defined('DONOTCDN') && DONOTCDN) {
            $this->cdn_reject_reason = 'DONOTCDN constant is defined';

            return false;
        }

        /**
         * Check logged in admin
         */
        if ($this->_config->get_boolean('cdn.reject.admins') && current_user_can('manage_options')) {
            $this->cdn_reject_reason = 'logged in admin is rejected';

            return false;
        }

        return true;
    }

    /**
     * Checks User Agent
     *
     * @return boolean
     */
    function check_ua() {
        $uas = array_merge($this->_config->get_array('cdn.reject.ua'), array(
            W3TC_POWERED_BY
        ));

        foreach ($uas as $ua) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], $ua) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks request URI
     *
     * @return boolean
     */
    function check_request_uri() {
        $auto_reject_uri = array(
            'wp-login',
            'wp-register'
        );

        foreach ($auto_reject_uri as $uri) {
            if (strstr($_SERVER['REQUEST_URI'], $uri) !== false) {
                return false;
            }
        }

        $reject_uri = $this->_config->get_array('cdn.reject.uri');
        $reject_uri = array_map('w3_parse_path', $reject_uri);

        foreach ($reject_uri as $expr) {
            $expr = trim($expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $_SERVER['REQUEST_URI'])) {
                return false;
            }
        }

        return true;
    }
}
