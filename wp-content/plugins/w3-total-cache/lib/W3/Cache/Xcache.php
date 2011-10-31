<?php

/**
 * XCache class
 */
if (!defined('W3TC')) {
    die();
}

require_once W3TC_LIB_W3_DIR . '/Cache/Base.php';

/**
 * Class W3_Cache_Xcache
 */
class W3_Cache_Xcache extends W3_Cache_Base {
    /**
     * Adds data
     *
     * @param string $key
     * @param mixed $var
     * @param integer $expire
     * @return boolean
     */
    function add($key, &$var, $expire = 0) {
        if ($this->get($key) === false) {
            return $this->set($key, $var, $expire);
        }

        return false;
    }

    /**
     * Sets data
     *
     * @param string $key
     * @param mixed $var
     * @param integer $expire
     * @return boolean
     */
    function set($key, &$var, $expire = 0) {
        return xcache_set($key, serialize($var), $expire);
    }

    /**
     * Returns data
     *
     * @param string $key
     * @return mixed
     */
    function get($key) {
        return @unserialize(xcache_get($key));
    }

    /**
     * Replaces data
     *
     * @param string $key
     * @param mixed $var
     * @param integer $expire
     * @return boolean
     */
    function replace($key, &$var, $expire = 0) {
        if ($this->get($key) !== false) {
            return $this->set($key, $var, $expire);
        }

        return false;
    }

    /**
     * Deletes data
     *
     * @param string $key
     * @return boolean
     */
    function delete($key) {
        return xcache_unset($key);
    }

    /**
     * Flushes all data
     *
     * @return boolean
     */
    function flush() {
        xcache_clear_cache(XC_TYPE_VAR, 0);

        return true;
    }
}
