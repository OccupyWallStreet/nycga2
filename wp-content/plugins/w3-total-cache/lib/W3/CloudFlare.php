<?php

/**
 * W3 CloudFlare Class
 */
define('W3TC_CLOUDFLARE_API_URL', 'https://www.cloudflare.com/api_json.html');
define('W3TC_CLOUDFLARE_EXTERNAL_EVENT_URL', 'https://www.cloudflare.com/ajax/external-event.html');

/**
 * Class W3_CloudFlare
 */
class W3_CloudFlare {
    /**
     * Config array
     *
     * @var array
     */
    var $_config = array();

    /**
     * CloudFlare IP ranges
     *
     * @var array
     */
    var $_ip_ranges = array(
        '204.93.240.0/24',
        '204.93.177.0/24',
        '204.93.173.0/24',
        '199.27.128.0/21',
        '173.245.48.0/20',
        '103.22.200.0/2'
    );

    /**
     * PHP5-style constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $this->_config = array_merge(array(
            'email' => '',
            'key' => '',
            'zone' => ''
        ), $config);
    }

    /**
     * PHP4-style constructor
     *
     * @param array $config
     */
    function W3_CloudFlare($config = array()) {
        $this->__construct($config);
    }


    /**
     * Makes API request
     *
     * @param string $action
     * @param string $value
     * @return array
     */
    function api_request($action, $value = null) {
        require_once W3TC_INC_DIR . '/functions/http.php';

        $url = sprintf('%s?email=%s&tkn=%s&z=%s&a=%s', W3TC_CLOUDFLARE_API_URL, urlencode($this->_config['email']), urlencode($this->_config['key']), urlencode($this->_config['zone']), urlencode($action));

        if ($value !== null) {
            $url .= sprintf('&v=%s', urlencode($value));
        }

        $response = w3_http_get($url);

        if (!is_wp_error($response)) {
            return json_decode($response['body']);
        }

        return false;
    }

    /**
     * Makes external event request
     *
     * @param string $type
     * @param string $value
     * @return array
     */
    function external_event($type, $value) {
        require_once W3TC_INC_DIR . '/functions/http.php';

        $url = sprintf('%s?u=%s&tkn=%s&evnt_t=%s&evnt_v=%s', W3TC_CLOUDFLARE_EXTERNAL_EVENT_URL, urlencode($this->_config['email']), urlencode($this->_config['key']), urlencode($type), urlencode($value));
        $response = w3_http_get($url);

        if (!is_wp_error($response)) {
            return json_decode($response['body']);
        }

        return false;
    }

    /**
     * Fix client's IP-address
     *
     * @return void
     */
    function fix_remote_addr() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            foreach ($this->_ip_ranges as $range) {
                if ($this->_ip_in_range($_SERVER['REMOTE_ADDR'], $range)) {
                    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
                    break;
                }
            }
        }
    }

    /**
     * Check if IP address is in range
     *
     * @param string $ip
     * @param string $range
     * @return bool
     */
    function _ip_in_range($ip, $range) {
        if (strpos($range, '/') !== false) {
            list($range, $netmask) = explode('/', $range, 2);

            if (strpos($netmask, '.') !== false) {
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);

                return ((ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec));
            } else {
                $x = explode('.', $range);

                while (count($x) < 4) {
                    $x[] = '0';
                }

                list($a, $b, $c, $d) = $x;

                $range = sprintf('%u.%u.%u.%u', empty($a) ? '0' : $a, empty($b) ? '0' : $b, empty($c) ? '0' : $c, empty($d) ? '0' : $d);
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);
                $wildcard_dec = pow(2, (32 - $netmask)) - 1;
                $netmask_dec = ~$wildcard_dec;

                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        } else {
            if (strpos($range, '*') !== false) {
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = sprintf('%s-%s', $lower, $upper);
            }

            if (strpos($range, '-') !== false) {
                list($lower, $upper) = explode('-', $range, 2);

                $lower_dec = (float) sprintf('%u', ip2long($lower));
                $upper_dec = (float) sprintf('%u', ip2long($upper));
                $ip_dec = (float) sprintf('%u', ip2long($ip));

                return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
            }

            return false;
        }
    }
}
