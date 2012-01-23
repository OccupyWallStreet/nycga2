<?php

/**
 * W3 Redirect
 */

/**
 * Class W3_Redirect
 */
class W3_Redirect {
    /**
     * PHP5 Constructor
     */
    function __construct() {
        $this->_config = & w3_instance('W3_Config');
        if ($this->_config->get_boolean('mobile.enabled')) {
            $this->_mobile = & w3_instance('W3_Mobile');
        }

        if ($this->_config->get_boolean('referrer.enabled')) {
            $this->_referrer = & w3_instance('W3_Referrer');
        }
    }

    /**
     * PHP4 Constructor
     */
    function W3_Redirect() {
        $this->__construct();
    }

    /**
     * Do logic
     */
    function process() {
        /**
         * Skip some pages
         */
        switch (true) {
            case defined('DOING_AJAX'):
            case defined('DOING_CRON'):
            case defined('APP_REQUEST'):
            case defined('XMLRPC_REQUEST'):
            case defined('WP_ADMIN'):
            case (defined('SHORTINIT') && SHORTINIT):
                return;
        }

        /**
         * Handle mobile or referrer redirects
         */
        if ($this->_mobile || $this->_referrer) {
            $mobile_redirect = $this->_mobile->get_redirect();
            $referrer_redirect = $this->_referrer->get_redirect();

            $redirect = ($mobile_redirect ? $mobile_redirect : $referrer_redirect);

            if ($redirect) {
                w3_redirect($redirect);
                exit();
            }
        }
    }
}
