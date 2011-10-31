<?php

/**
 * W3TC Mobile detection
 */

/**
 * Class W3_Mobile
 */
class W3_Mobile {
    /**
     * Mobile groups
     * @var array
     */
    var $groups = array();

    /**
     * PHP5-style constructor
     */
    function __construct() {
        $config = & w3_instance('W3_Config');

        $this->groups = $config->get_array('mobile.rgroups');
    }

    /**
     * PHP4-style constructor
     */
    function W3_Mobile() {
        $this->__construct();
    }

    /**
     * Detects mobile group
     *
     * @return string
     */
    function get_group() {
        static $mobile_group = null;

        if ($mobile_group === null) {
            foreach ($this->groups as $group => $config) {
                if (isset($config['enabled']) && $config['enabled'] && isset($config['agents'])) {
                    foreach ((array) $config['agents'] as $agent) {
                        if ($agent && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('~' . $agent . '~i', $_SERVER['HTTP_USER_AGENT'])) {
                            $mobile_group = $group;

                            return $mobile_group;
                        }
                    }
                }
            }

            $mobile_group = false;
        }

        return $mobile_group;
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
