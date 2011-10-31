<?php

/**
 * W3TC Referrer detection
 */
define('W3TC_REFERRER_COOKIE_NAME', 'w3tc_referrer');

/**
 * Class W3_Referrer
 */
class W3_Referrer {
    /**
     * Referrer groups
     * @var array
     */
    var $groups = array();

    /**
     * PHP5-style constructor
     */
    function __construct() {
        $config = & w3_instance('W3_Config');

        $this->groups = $config->get_array('referrer.rgroups');
    }

    /**
     * PHP4-style constructor
     */
    function W3_Referrer() {
        $this->__construct();
    }

    /**
     * Returns HTTP referrer value
     *
     * @return string
     */
    function get_http_referrer() {
        $http_referrer = '';

        if (isset($_COOKIE[W3TC_REFERRER_COOKIE_NAME])) {
            $http_referrer = $_COOKIE[W3TC_REFERRER_COOKIE_NAME];
        } elseif (isset($_SERVER['HTTP_REFERER'])) {
            $http_referrer = $_SERVER['HTTP_REFERER'];

            setcookie(W3TC_REFERRER_COOKIE_NAME, $http_referrer, 0, w3_get_base_path());
        }

        return $http_referrer;
    }

    /**
     * Detects referrer group
     *
     * @return string
     */
    function get_group() {
        static $referrer_group = null;

        if ($referrer_group === null) {
            $http_referrer = $this->get_http_referrer();

            if ($http_referrer) {
                foreach ($this->groups as $group => $config) {
                    if (isset($config['enabled']) && $config['enabled'] && isset($config['referrers'])) {
                        foreach ((array) $config['referrers'] as $referrer) {
                            if ($referrer && $http_referrer && preg_match('~' . $referrer . '~i', $http_referrer)) {
                                $referrer_group = $group;

                                return $referrer_group;
                            }
                        }
                    }
                }
            }

            $referrer_group = false;
        }

        return $referrer_group;
    }

    /**
     * Returns temaplte
     *
     * @return string
     */
    function get_template() {
        $theme = $this->get_theme();

        if ($theme) {
            list($template,) = explode('/', $theme);

            return $template;
        }

        return false;
    }

    /**
     * Returns stylesheet
     *
     * @return string
     */
    function get_stylesheet() {
        $theme = $this->get_theme();

        if ($theme) {
            list(, $stylesheet) = explode('/', $theme);

            return $stylesheet;
        }

        return false;
    }

    /**
     * Returns redirect
     *
     * @return string
     */
    function get_redirect() {
        $group = $this->get_group();

        if (isset($this->groups[$group]['redirect'])) {
            return $this->groups[$group]['redirect'];
        }

        return false;
    }

    /**
     * Returns theme
     *
     * @return string
     */
    function get_theme() {
        $group = $this->get_group();

        if (isset($this->groups[$group]['theme'])) {
            return $this->groups[$group]['theme'];
        }

        return false;
    }

    /**
     * Return array of themes
     *
     * @return array
     */
    function get_themes() {
        $themes = array();
        $wp_themes = get_themes();

        foreach ($wp_themes as $wp_theme) {
            $theme_key = sprintf('%s/%s', $wp_theme['Template'], $wp_theme['Stylesheet']);
            $themes[$theme_key] = $wp_theme['Name'];
        }

        return $themes;
    }
}
