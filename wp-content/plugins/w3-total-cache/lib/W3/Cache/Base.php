<?php

/**
 * Base cache class
 */

/**
 * Class W3_Cache_Base
 */
class W3_Cache_Base {
    /**
     * Adds data
     *
     * @abstract
     * @param string $key
     * @param mixed $data
     * @param integer $expire
     * @return boolean
     */
    function add($key, &$data, $expire = 0) {
        return false;
    }

    /**
     * Sets data
     *
     * @abstract
     * @param string $key
     * @param mixed $data
     * @param integer $expire
     * @return boolean
     */
    function set($key, &$data, $expire = 0) {
        return false;
    }

    /**
     * Returns data
     *
     * @abstract
     * @param string $key
     * @return mixed
     */
    function get($key) {
        return false;
    }

    /**
     * Alias for get for minify cache
     *
     * @param string $key
     * @return mixed
     */
    function fetch($key) {
        return $this->get($key);
    }

    /**
     * Replaces data
     *
     * @abstract
     * @param string $key
     * @param mixed $data
     * @param integer $expire
     * @return boolean
     */
    function replace($key, &$data, $expire = 0) {
        return false;
    }

    /**
     * Deletes data
     *
     * @abstract
     * @param string $key
     * @return boolean
     */
    function delete($key) {
        return false;
    }

    /**
     * Flushes all data
     *
     * @abstract
     * @return boolean
     */
    function flush() {
        return false;
    }
}
