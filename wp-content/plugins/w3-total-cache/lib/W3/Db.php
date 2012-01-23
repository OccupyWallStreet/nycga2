<?php

/**
 * W3 Database object
 */
if (!defined('ABSPATH')) {
    die();
}

if (!class_exists('W3_Db_Driver')) {
    require_once ABSPATH . 'wp-includes/wp-db.php';

    class W3_Db_Driver extends wpdb {
    }
}

/**
 * Class W3_Db
 */
class W3_Db extends W3_Db_Driver {
    /**
     * Array of queries
     *
     * @var array
     */
    var $query_stats = array();

    /**
     * Queries total
     *
     * @var integer
     */
    var $query_total = 0;

    /**
     * Query cache hits
     *
     * @var integer
     */
    var $query_hits = 0;

    /**
     * Query cache misses
     *
     * @var integer
     */
    var $query_misses = 0;

    /**
     * Time total
     *
     * @var integer
     */
    var $time_total = 0;

    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * Lifetime
     *
     * @var integer
     */
    var $_lifetime = null;

    /**
     * PHP5 constructor
     *
     * @param string $dbuser
     * @param string $dbpassword
     * @param string $dbname
     * @param string $dbhost
     */
    function __construct($dbuser, $dbpassword, $dbname, $dbhost) {
        $this->_config = & w3_instance('W3_Config');
        $this->_lifetime = $this->_config->get_integer('dbcache.lifetime');

        if ($this->_can_ob()) {
            ob_start(array(
                &$this,
                'ob_callback'
            ));
        }

        parent::__construct($dbuser, $dbpassword, $dbname, $dbhost);
    }

    /**
     * PHP4 constructor
     *
     * @param string $dbuser
     * @param string $dbpassword
     * @param string $dbname
     * @param string $dbhost
     */
    function W3_Db($dbuser, $dbpassword, $dbname, $dbhost) {
        $this->__construct($dbuser, $dbpassword, $dbname, $dbhost);
    }

    /**
     * Executes query
     *
     * @param string $query
     * @return integer
     */
    function query($query) {
        if (!$this->ready) {
            return false;
        }

        $reason = '';
        $cached = false;
        $data = false;
        $time_total = 0;

        $this->query_total++;

        $caching = $this->_can_cache($query, $reason);

        if ($caching) {
            $this->timer_start();
            $cache_key = $this->_get_cache_key($query);
            $cache = & $this->_get_cache();
            $data = $cache->get($cache_key);
            $time_total = $this->timer_stop();
        }

        if (is_array($data)) {
            $cached = true;
            $this->query_hits++;

            $this->last_error = $data['last_error'];
            $this->last_query = $data['last_query'];
            $this->last_result = $data['last_result'];
            $this->col_info = $data['col_info'];
            $this->num_rows = $data['num_rows'];

            $return_val = $data['return_val'];
        } else {
            $this->query_misses++;

            $this->timer_start();
            $return_val = parent::query($query);
            $time_total = $this->timer_stop();

            if ($caching) {
                $data = array(
                    'last_error' => $this->last_error,
                    'last_query' => $this->last_query,
                    'last_result' => $this->last_result,
                    'col_info' => $this->col_info,
                    'num_rows' => $this->num_rows,
                    'return_val' => $return_val
                );

                $cache = & $this->_get_cache();
                $cache->set($cache_key, $data, $this->_lifetime);
            }
        }

        if ($this->_config->get_boolean('dbcache.debug')) {
            $this->query_stats[] = array(
                'query' => $query,
                'caching' => $caching,
                'reason' => $reason,
                'cached' => $cached,
                'data_size' => ($data ? strlen(serialize($data)) : 0),
                'time_total' => $time_total
            );
        }

        $this->time_total += $time_total;

        return $return_val;
    }

    /**
    * Insert a row into a table.
    *
    * @param string $table
    * @param array $data
    * @param array|string $format
    * @return int|false
    */
    function insert($table, $data, $format = null) {
        return $this->_nocache_instance()->insert($table, $data, $format);
    }

    /**
    * Replace a row into a table.
    *
    * @param string $table
    * @param array $data
    * @param array|string $format
    * @return int|false
    */
    function replace($table, $data, $format = null) {
        return $this->_nocache_instance()->replace($table, $data, $format);
    }

    /**
    * Update a row in the table
    *
    * @param string $table
    * @param array $data
    * @param array $where
    * @param array|string $format
    * @param array|string $format_where
    * @return int|false
    */
    function update($table, $data, $where, $format = null, $where_format = null) {
        return $this->_nocache_instance()->update($table, $data, $where, $format, $where_format);
    }

    /**
     * Executes query without caching, completely ignored by cache
     *
     * @param string $query
     * @return integer
     */
    function query_nocache($query) {
        $return_val = parent::query($query);
        return $return_val;
    }

    /**
     * Flushes cache
     *
     * @return boolean
     */
    function flush_cache() {
        $cache = & $this->_get_cache();

        return $cache->flush();
    }

    /**
     * Returns onject instance. Called by WP engine
     *
     * @return W3_Db
     */
    function &instance() {
        static $instances = array();

        if (!isset($instances[0])) {
            $class = __CLASS__;
            @$instances[0] = & new $class(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
        }

        return $instances[0];
    }

    function _nocache_instance()
    {
        if (!isset($this->_nocache_instance)) {
            $this->_nocache_instance = new W3_Db_Nocache($this);
        }

        return $this->_nocache_instance;
    }

    /**
     * Output buffering callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer) {
        if ($buffer != '' && w3_is_xml($buffer)) {
            $buffer .= "\r\n\r\n" . $this->_get_debug_info();
        }

        return $buffer;
    }

    /**
     * Returns cache object
     *
     * @return W3_Cache_Base
     */
    function &_get_cache() {
        static $cache = array();

        if (!isset($cache[0])) {
            $engine = $this->_config->get_string('dbcache.engine');

            switch ($engine) {
                case 'memcached':
                    $engineConfig = array(
                        'servers' => $this->_config->get_array('dbcache.memcached.servers'),
                        'persistant' => $this->_config->get_boolean('dbcache.memcached.persistant')
                    );
                    break;

                case 'file':
                    $engineConfig = array(
                        'cache_dir' => W3TC_CACHE_FILE_DBCACHE_DIR,
                        'locking' => $this->_config->get_boolean('dbcache.file.locking'),
                        'flush_timelimit' => $this->_config->get_integer('timelimit.cache_flush')
                    );
                    break;

                default:
                    $engineConfig = array();
            }

            require_once W3TC_LIB_W3_DIR . '/Cache.php';
            @$cache[0] = & W3_Cache::instance($engine, $engineConfig);
        }

        return $cache[0];
    }

    /**
     * Check if can cache sql
     *
     * @param string $sql
     * @param string $cache_reject_reason
     * @return boolean
     */
    function _can_cache($sql, &$cache_reject_reason) {
        /**
         * Skip if disabled
         */
        if (!$this->_config->get_boolean('dbcache.enabled')) {
            $cache_reject_reason = 'Database caching is disabled';

            return false;
        }

        /**
         * Check for DONOTCACHEDB constant
         */
        if (defined('DONOTCACHEDB') && DONOTCACHEDB) {
            $cache_reject_reason = 'DONOTCACHEDB constant is defined';

            return false;
        }

        /**
         * Skip if doint AJAX
         */
        if (defined('DOING_AJAX')) {
            $cache_reject_reason = 'Doing AJAX';

            return false;
        }

        /**
         * Skip if doing cron
         */
        if (defined('DOING_CRON')) {
            $cache_reject_reason = 'Doing cron';

            return false;
        }

        /**
         * Skip if APP request
         */
        if (defined('APP_REQUEST')) {
            $cache_reject_reason = 'Application request';

            return false;
        }

        /**
         * Skip if XMLRPC request
         */
        if (defined('XMLRPC_REQUEST')) {
            $cache_reject_reason = 'XMLRPC request';

            return false;
        }

        /**
         * Skip if admin
         */
        if (defined('WP_ADMIN')) {
            $cache_reject_reason = 'wp-admin';

            return false;
        }

        /**
         * Check for WPMU's and WP's 3.0 short init
         */
        if (defined('SHORTINIT') && SHORTINIT) {
            $cache_reject_reason = 'Short init';

            return false;
        }

        /**
         * Skip if SQL is rejected
         */
        if (!$this->_check_sql($sql)) {
            $cache_reject_reason = 'Query is rejected';

            return false;
        }

        /**
         * Skip if request URI is rejected
         */
        if (!$this->_check_request_uri()) {
            $cache_reject_reason = 'Request URI is rejected';

            return false;
        }

        /**
         * Skip if cookie is rejected
         */
        if (!$this->_check_cookies()) {
            $cache_reject_reason = 'Cookie is rejected';

            return false;
        }

        /**
         * Skip if user is logged in
         */
        if ($this->_config->get_boolean('dbcache.reject.logged') && !$this->_check_logged_in()) {
            $cache_reject_reason = 'User is logged in';

            return false;
        }

        return true;
    }

    /**
     * Check if we can start OB
     *
     * @return boolean
     */
    function _can_ob() {
        /**
         * Database cache should be enabled
         */
        if (!$this->_config->get_boolean('dbcache.enabled')) {
            return false;
        }

        /**
         * Debug should be enabled
         */
        if (!$this->_config->get_boolean('dbcache.debug')) {
            return false;
        }

        /**
         * Skip if admin
         */
        if (defined('WP_ADMIN')) {
            return false;
        }

        /**
         * Skip if doint AJAX
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
     * Check SQL
     *
     * @param string $sql
     * @return boolean
     */
    function _check_sql($sql) {
        $auto_reject_strings = array(
            '^\s*insert\b',
            '^\s*delete\b',
            '^\s*update\b',
            '^\s*replace\b',
            '^\s*create\b',
            '^\s*alter\b',
            '^\s*show\b',
            '^\s*set\b',
            '\bsql_calc_found_rows\b',
            '\bfound_rows\(\)',
            '\bautoload\s+=\s+\'yes\'',
            '\bw3tc_request_data\b'
        );

        if (preg_match('~' . implode('|', $auto_reject_strings) . '~is', $sql)) {
            return false;
        }

        $reject_sql = $this->_config->get_array('dbcache.reject.sql');

        foreach ($reject_sql as $expr) {
            $expr = trim($expr);
            $expr = str_replace('{prefix}', $this->prefix, $expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $sql)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check request URI
     *
     * @return boolean
     */
    function _check_request_uri() {
        $auto_reject_uri = array(
            'wp-login',
            'wp-register'
        );

        foreach ($auto_reject_uri as $uri) {
            if (strstr($_SERVER['REQUEST_URI'], $uri) !== false) {
                return false;
            }
        }

        $reject_uri = $this->_config->get_array('dbcache.reject.uri');
        $reject_uri = array_map('w3_parse_path', $reject_uri);

        foreach ($reject_uri as $expr) {
            $expr = trim($expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $_SERVER['REQUEST_URI'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks for WordPress cookies
     *
     * @return boolean
     */
    function _check_cookies() {
        foreach (array_keys($_COOKIE) as $cookie_name) {
            if ($cookie_name == 'wordpress_test_cookie') {
                continue;
            }
            if (preg_match('/^wp-postpass|^comment_author/', $cookie_name)) {
                return false;
            }
        }

        foreach ($this->_config->get_array('dbcache.reject.cookie') as $reject_cookie) {
            foreach (array_keys($_COOKIE) as $cookie_name) {
                if (strstr($cookie_name, $reject_cookie) !== false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if user is logged in
     *
     * @return boolean
     */
    function _check_logged_in() {
        foreach (array_keys($_COOKIE) as $cookie_name) {
            if ($cookie_name == 'wordpress_test_cookie') {
                continue;
            }
            if (strpos($cookie_name, 'wordpress') === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns cache key
     *
     * @param string $sql
     * @return string
     */
    function _get_cache_key($sql) {
        $key = sprintf('w3tc_%s_sql_%s', w3_get_host_id(), md5($sql));

        /**
         * Allow to modify cache key by W3TC plugins
         */
        $key = w3tc_do_action('w3tc_dbcache_cache_key', $key);

        return $key;
    }

    /**
     * Returns debug info
     *
     * @return string
     */
    function _get_debug_info() {
        $debug_info = "<!-- W3 Total Cache: Db cache debug info:\r\n";
        $debug_info .= sprintf("%s%s\r\n", str_pad('Engine: ', 20), w3_get_engine_name($this->_config->get_string('dbcache.engine')));
        $debug_info .= sprintf("%s%d\r\n", str_pad('Total queries: ', 20), $this->query_total);
        $debug_info .= sprintf("%s%d\r\n", str_pad('Cached queries: ', 20), $this->query_hits);
        $debug_info .= sprintf("%s%.4f\r\n", str_pad('Total query time: ', 20), $this->time_total);

        if (count($this->query_stats)) {
            $debug_info .= "SQL info:\r\n";
            $debug_info .= sprintf("%s | %s | %s | % s | %s | %s\r\n",
                str_pad('#', 5, ' ', STR_PAD_LEFT), str_pad('Time (s)', 8, ' ', STR_PAD_LEFT),
                str_pad('Caching (Reject reason)', 30, ' ', STR_PAD_BOTH),
                str_pad('Status', 10, ' ', STR_PAD_BOTH),
                str_pad('Data size (b)', 13, ' ', STR_PAD_LEFT),
                'Query');

            foreach ($this->query_stats as $index => $query) {
                $debug_info .= sprintf("%s | %s | %s | %s | %s | %s\r\n",
                    str_pad($index + 1, 5, ' ', STR_PAD_LEFT),
                    str_pad(round($query['time_total'], 4), 8, ' ', STR_PAD_LEFT),
                    str_pad(($query['caching'] ? 'enabled'
                            : sprintf('disabled (%s)', $query['reason'])), 30, ' ', STR_PAD_BOTH),
                    str_pad(($query['cached'] ? 'cached' : 'not cached'), 10, ' ', STR_PAD_BOTH),
                    str_pad($query['data_size'], 13, ' ', STR_PAD_LEFT),
                    w3_escape_comment(trim($query['query'])));
            }
        }

        $debug_info .= '-->';

        return $debug_info;
    }
}

/**
 * Class W3_Db
 */
class W3_Db_Nocache extends W3_Db_Driver {
    function __construct($object_doing_query) {
        $this->_object_doing_query = $object_doing_query;

        # _real_escape is called on that object, 
        # which requires those inst. vars
        if (isset($object_doing_query->real_escape)) {
            $this->real_escape = $object_doing_query->real_escape;
        }
        if (isset($object_doing_query->dbh)) {
            $this->dbh = $object_doing_query->dbh;
        }

    }

    /**
     * PHP4 constructor
     *
     * @param string $dbhost
     */
    function W3_Db_Nocache($object_doing_query) {
        $this->__construct($object_doing_query);
    }

    /**
     * Executes query
     *
     * @param string $query
     * @return integer
     */
    function query($query) {
        return $this->_object_doing_query->query_nocache($query);
    }
}
