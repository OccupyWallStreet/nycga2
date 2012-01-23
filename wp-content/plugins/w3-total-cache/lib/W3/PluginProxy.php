<?php

/**
 * W3 Total Cache Plugin Proxy
 */
if (!defined('W3TC')) {
    die();
}

require_once W3TC_LIB_W3_DIR . '/Plugin.php';

/**
 * Class W3_PluginProxy
 * Instantiates underlying plugin object only when it's active by configuration.
 * Underlying plugin must have 'activate' and 'deactivate' functions
 */
class W3_PluginProxy extends W3_Plugin {
    /**
     * PHP5 Constructor
     * @param string $worker_class classname of plugin doing job
     * @param string $option_name_enabled name of option if plugin is enabled
     */
    function __construct($worker_class, $option_name_enabled) {
        parent::__construct();

        $this->_worker_class = $worker_class; 
        $this->_enabled = $this->_config->get_boolean($option_name_enabled);
    }

    /**
     * PHP4 Constructor
     *
     * @param string $worker_class classname of plugin doing job
     * @param string $option_name_enabled name of option if plugin is enabled
     * @return W3_PluginProxy
     */
    function W3_PluginProxy($worker_class, $option_name_enabled) {
        $this->__construct($worker_class, $option_name_enabled);
    }

    /**
     * Run plugin
     */
    function run() {
        register_activation_hook(W3TC_FILE, array(
            &$this,
            'activate'
        ));

        register_deactivation_hook(W3TC_FILE, array(
            &$this,
            'deactivate'
        ));

        if ($this->_enabled) {
            $this->get_worker()->run();
        }
    }

    /**
     * Instantiates worker on demand
     *
     * @return mixed
     */
    function &get_worker() {
        return w3_instance($this->_worker_class);
    }

    /**
     * Activation action
     */
    function activate() {
        $this->get_worker()->activate();
    }

    /**
     * Deactivation action
     */
    function deactivate() {
        $this->get_worker()->deactivate();
    }
}
