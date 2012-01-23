<?php

/**
 * W3 CDN Netdna Class
 */
if (!defined('ABSPATH')) {
    die();
}

define('W3TC_CDN_MIRROR_COTENDO_WSDL', 'https://api.cotendo.net/cws?wsdl');
define('W3TC_CDN_MIRROR_COTENDO_ENDPOINT', 'http://api.cotendo.net/cws?ver=1.0');
define('W3TC_CDN_MIRROR_COTENDO_NAMESPACE', 'http://api.cotendo.net/');

require_once W3TC_LIB_W3_DIR . '/Cdn/Mirror.php';

/**
 * Class W3_Cdn_Mirror_Cotendo
 */
class W3_Cdn_Mirror_Cotendo extends W3_Cdn_Mirror {
    /**
     * PHP5 Constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $config = array_merge(array(
            'username' => '',
            'password' => '',
            'zones' => array(),
        ), $config);

        parent::__construct($config);
    }

    /**
     * PHP4 Constructor
     *
     * @param array $config
     */
    function W3_Cdn_Mirror_Cotendo($config = array()) {
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
        if (empty($this->_config['username'])) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'Empty username.');

            return false;
        }

        if (empty($this->_config['password'])) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'Empty password.');

            return false;
        }

        if (empty($this->_config['zones'])) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'Empty zones list.');

            return false;
        }

        require_once W3TC_LIB_NUSOAP_DIR . '/nusoap.php';

        $client = new nusoap_client(
            W3TC_CDN_MIRROR_COTENDO_WSDL,
            'wsdl'
        );

        $error = $client->getError();

        if ($error) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, sprintf('Constructor error (%s).', $error));

            return false;
        }

        $client->authtype = 'basic';
        $client->username = $this->_config['username'];
        $client->password = $this->_config['password'];
        $client->forceEndpoint = W3TC_CDN_MIRROR_COTENDO_ENDPOINT;

        foreach ((array) $this->_config['zones'] as $zone) {
            $expressions = array();

            foreach ($files as $remote_path) {
                $expressions[] = '/' . $remote_path;
            }

            $expression = implode("\n", $expressions);

            $params = array(
                'cname' => $zone,
                'flushExpression' => $expression,
                'flushType' => 'hard',
            );

            $client->call('doFlush', $params, W3TC_CDN_MIRROR_COTENDO_NAMESPACE);

            if ($client->fault) {
                $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'Invalid response.');

                return false;
            }

            $error = $client->getError();

            if ($error) {
                $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, sprintf('Unable to purge (%s).', $error));

                return false;
            }
        }

        $results = $this->_get_results($files, W3TC_CDN_RESULT_OK, 'OK');

        return true;
    }
}
