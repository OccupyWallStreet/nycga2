<?php

/**
 * Wincache class
 */
if (!defined('W3TC')) {
    die();
}

require_once W3TC_LIB_W3_DIR . '/Cache/Base.php';

/**
 * Class W3_Cache_Wincache
 */
class W3_Cache_Wincache extends W3_Cache_Base {
    /**
     * Adds data
     *
     * @param string $key
     * @param mixed $var
     * @param integer $expire
     * @return boolean
     */
    function add($key, &$var, $expire = 0) {
        return wincache_ucache_add($key, serialize($var), $expire);
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
        return wincache_ucache_set($key, serialize($var), $expire);
    }

    /**
     * Returns data
     *
     * @param string $key
     * @return mixed
     */
    function get($key) {
        return @unserialize(wincache_ucache_get($key));
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
        return wincache_ucache_delete($key);
    }

    /**
     * Flushes all data
     *
     * @return boolean
     */
    function flush() {
        return wincache_ucache_clear();
    }
}
