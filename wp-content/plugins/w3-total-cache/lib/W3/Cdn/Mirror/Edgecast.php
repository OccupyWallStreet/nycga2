<?php

/**
 * W3 CDN Netdna Class
 */
if (!defined('ABSPATH')) {
    die();
}

define('W3TC_CDN_EDGECAST_PURGE_URL', 'http://api.edgecast.com/v2/mcc/customers/%s/edge/purge');
define('W3TC_CDN_EDGECAST_MEDIATYPE_WINDOWS_MEDIA_STREAMING', 1);
define('W3TC_CDN_EDGECAST_MEDIATYPE_FLASH_MEDIA_STREAMING', 2);
define('W3TC_CDN_EDGECAST_MEDIATYPE_HTTP_LARGE_OBJECT', 3);
define('W3TC_CDN_EDGECAST_MEDIATYPE_HTTP_SMALL_OBJECT', 8);
define('W3TC_CDN_EDGECAST_MEDIATYPE_APPLICATION_DELIVERY_NETWORK', 14);

require_once W3TC_LIB_W3_DIR . '/Cdn/Mirror.php';

/**
 * Class W3_Cdn_Mirror_Edgecast
 */
class W3_Cdn_Mirror_Edgecast extends W3_Cdn_Mirror {
    /**
     * PHP5 Constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $config = array_merge(array(
            'apiid' => '',
            'apikey' => ''
        ), $config);

        parent::__construct($config);
    }

    /**
     * PHP4 Constructor
     *
     * @param array $config
     */
    function W3_Cdn_Mirror_Edgecast($config = array()) {
        $this->__construct($config);
    }

    /**
     * Purges remote files
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function purge($files, &$results) {
        if (empty($this->_config['account'])) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'Empty account #.');

            return false;
        }

        if (empty($this->_config['token'])) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'Empty token.');

            return false;
        }

        foreach ($files as $local_path => $remote_path) {
            $url = $this->format_url($remote_path);

            $error = null;

            if ($this->_purge_content($url, W3TC_CDN_EDGECAST_MEDIATYPE_HTTP_SMALL_OBJECT, $error)) {
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
            } else {
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf('Unable to purge (%s).', $error));
            }
        }

        return !$this->_is_error($results);
    }

    /**
     * Purge content
     *
     * @param string $path
     * @param int $type
     * @param string $error
     * @return boolean
     */
    function _purge_content($path, $type, &$error) {
        $url = sprintf(W3TC_CDN_EDGECAST_PURGE_URL, $this->_config['account']);
        $args = array(
            'method' => 'PUT',
            'user-agent' => W3TC_POWERED_BY,
            'headers' => array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => sprintf('TOK:%s', $this->_config['token'])
            ),
            'body' => json_encode(array(
                'MediaPath' => $path,
                'MediaType' => $type
            ))
        );

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            $error = implode('; ', $response->get_error_messages());

            return false;
        }

        switch ($response['response']['code']) {
            case 200:
                return true;

            case 400:
                $error = 'Invalid Request Parameter';
                return false;

            case 403:
                $error = 'Authentication Failure or Insufficient Access Rights';
                return false;

            case 404:
                $error = 'Invalid Request URI';
                return false;

            case 405:
                $error = 'Invalid Request';
                return false;

            case 500:
                $error = 'Server Error';
                return false;
        }

        $error = 'Unknown error';

        return false;
    }
}
