<?php

/**
 * Varnish purge object
 */

/**
 * Class W3_Varnish
 */
class W3_Varnish {
    /**
     * Debug flag
     *
     * @var bool
     */
    var $_debug = false;

    /**
     * Varnish servers
     *
     * @var array
     */
    var $_servers = array();

    /**
     * Operation timeout
     *
     * @var int
     */
    var $_timeout = 30;

    /**
     * PHP5-style constructor
     */
    function __construct() {
        $config = & w3_instance('W3_Config');

        $this->_debug = $config->get_boolean('varnish.debug');
        $this->_servers = $config->get_array('varnish.servers');
        $this->_timeout = $config->get_integer('timelimit.varnish_purge');
    }

    /**
     * PHP4-style constructor
     */
    function W3_Varnish() {
        $this->__construct();
    }

    /**
     * Purge URI
     *
     * @param string $uri
     * @return boolean
     */
    function purge($uri) {
        require_once W3TC_INC_DIR . '/functions/http.php';

        @set_time_limit($this->_timeout);

        if (strpos($uri, '/') !== 0) {
            $uri = '/' . $uri;
        }

        foreach ((array) $this->_servers as $server) {
            $url = sprintf('http://%s%s', $server, $uri);

            $response = w3_http_request($url, array('method' => 'PURGE'));

            if (is_wp_error($response)) {
                $this->_log($url, sprintf('Unable to send request: %s.', implode('; ', $response->get_error_messages())));

                return false;
            }

            if ($response['response']['code'] !== 200) {
                $this->_log($url, 'Bad response code.');

                return false;
            }

            $this->_log($url, 'OK');
        }

        return true;
    }

    /**
     * Write log entry
     *
     * @param string $url
     * @param string $error
     * @return bool|int
     */
    function _log($url, $error) {
        if ($this->_debug) {
            $data = sprintf("[%s] [%s] %s\n", date('r'), $url, $error);

            return @file_put_contents(W3TC_VARNISH_LOG_FILE, $data, FILE_APPEND);
        }

        return true;
    }
}
