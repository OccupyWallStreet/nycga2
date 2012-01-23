<?php

/**
 * Generic file cache
 */
if (!defined('ABSPATH')) {
    die();
}

require_once W3TC_INC_DIR . '/functions/file.php';
require_once W3TC_LIB_W3_DIR . '/Cache/File.php';

/**
 * Class W3_Cache_File_Generic
 */
class W3_Cache_File_Generic extends W3_Cache_File {
    /**
     * Expire
     *
     * @var integer
     */
    var $_expire = 0;

    /**
     * PHP5-style constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        parent::__construct($config);

        $this->_expire = (isset($config['expire']) ? (int) $config['expire'] : 0);

        if (!$this->_expire || $this->_expire > W3TC_CACHE_FILE_EXPIRE_MAX) {
            $this->_expire = W3TC_CACHE_FILE_EXPIRE_MAX;
        }
    }

    /**
     * PHP4-style constructor
     *
     * @param array $config
     */
    function W3_Cache_File_Generic($config = array()) {
        $this->__construct($config);
    }

    /**
     * Sets data
     *
     * @param string $key
     * @param string $var
     * @return boolean
     */
    function set($key, $var) {
        $sub_path = $this->_get_path($key);
        $path = $this->_cache_dir . '/' . $sub_path;

        $sub_dir = dirname($sub_path);
        $dir = dirname($path);

        if ((@is_dir($dir) || w3_mkdir($sub_dir, 0777, $this->_cache_dir))) {
            $fp = @fopen($path, 'w');

            if ($fp) {
                if ($this->_locking) {
                    @flock($fp, LOCK_EX);
                }

                @fputs($fp, $var);
                @fclose($fp);

                if ($this->_locking) {
                    @flock($fp, LOCK_UN);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Returns data
     *
     * @param string $key
     * @return string
     */
    function get($key) {
        $var = false;
        $path = $this->_cache_dir . '/' . $this->_get_path($key);

        if (is_readable($path)) {
            $ftime = @filemtime($path);

            if ($ftime && $ftime > (time() - $this->_expire)) {
                $fp = @fopen($path, 'r');

                if ($fp) {
                    if ($this->_locking) {
                        @flock($fp, LOCK_SH);
                    }

                    $var = '';

                    while (!@feof($fp)) {
                        $var .= @fread($fp, 4096);
                    }

                    @fclose($fp);

                    if ($this->_locking) {
                        @flock($fp, LOCK_UN);
                    }
                }
            }
        }

        return $var;
    }

    /**
     * Returns cache file path by key
     *
     * @param string $key
     * @return string
     */
    function _get_path($key) {
        return $key;
    }
}
