<?php

/**
 * W3 Plugin base class
 */

/**
 * Class W3_Plugin
 */
class W3_Plugin {
    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * PHP5 Constructor
     */
    function __construct() {
        $this->_config = & w3_instance('W3_Config');
    }

    /**
     * PHP4 Constructor
     *
     * @return W3_Plugin
     */
    function W3_Plugin() {
        $this->__construct();
    }

    /**
     * Runs plugin
     */
    function run() {
    }

    /**
     * Check if plugin is locked
     *
     * @return boolean
     */
    function locked() {
        static $locked = null;

        if ($locked === null) {
            if (w3_is_network() && function_exists('get_blog_list')) {
                global $blog_id;

                $blogs = get_blog_list();

                foreach ($blogs as $blog) {
                    if ($blog['blog_id'] != $blog_id) {
                        $active_plugins = get_blog_option($blog['blog_id'], 'active_plugins');

                        if (in_array(W3TC_FILE, $active_plugins)) {
                            $locked = true;
                            break;
                        }
                    }
                }
            } else {
                $locked = false;
            }
        }

        return $locked;
    }
}
