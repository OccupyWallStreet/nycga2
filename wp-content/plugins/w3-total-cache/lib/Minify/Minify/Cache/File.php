<?php

require_once W3TC_INC_DIR . '/functions/file.php';

/**
 * Class Minify_Cache_File
 * @package Minify
 */

class Minify_Cache_File {

    public function __construct($path = '', $exclude = array(), $locking = false, $flushTimeLimit = 0) {
        if (!$path) {
            require_once W3TC_LIB_MINIFY_DIR . '/Solar/Dir.php';
            $path = rtrim(Solar_Dir::tmp(), DIRECTORY_SEPARATOR);
        }

        $this->_path = $path;
        $this->_exclude = $exclude;
        $this->_locking = $locking;
        $this->_flushTimeLimit = $flushTimeLimit;
    }

    /**
     * Write data to cache.
     *
     * @param string $id cache id (e.g. a filename)
     *
     * @param string $data
     *
     * @return bool success
     */
    public function store($id, $data) {
        $path = $this->_path . '/' . $id;
        $flag = $this->_locking ? LOCK_EX : null;

        if (is_file($path)) {
            @unlink($path);
        }

        $dir = dirname($id);

        if ($dir) {
            w3_mkdir($dir, 0777, $this->_path);
        }

        if (!@file_put_contents($path, $data, $flag)) {
            return false;
        }

        // write control
        if ($data != $this->fetch($id)) {
            @unlink($path);

            return false;
        }

        return true;
    }

    /**
     * Get the size of a cache entry
     *
     * @param string $id cache id (e.g. a filename)
     *
     * @return int size in bytes
     */
    public function getSize($id) {
        $path = $this->_path . '/' . $id;

        return filesize($path);
    }

    /**
     * Does a valid cache entry exist?
     *
     * @param string $id cache id (e.g. a filename)
     *
     * @param int $srcMtime mtime of the original source file(s)
     *
     * @return bool exists
     */
    public function isValid($id, $srcMtime) {
        $path = $this->_path . '/' . $id;

        return (is_file($path) && (filemtime($path) >= $srcMtime));
    }

    /**
     * Send the cached content to output
     *
     * @param string $id cache id (e.g. a filename)
     */
    public function display($id) {
        $path = $this->_path . '/' . $id;

        if ($this->_locking) {
            $fp = @fopen($path, 'rb');

            if ($fp) {
                @flock($fp, LOCK_SH);
                @fpassthru($fp);
                @flock($fp, LOCK_UN);
                @fclose($fp);

                return true;
            }
        } else {
            return @readfile($path);
        }

        return false;
    }

    /**
     * Fetch the cached content
     *
     * @param string $id cache id (e.g. a filename)
     *
     * @return string
     */
    public function fetch($id) {
        $path = $this->_path . '/' . $id;

        if (is_readable($path)) {
            if ($this->_locking) {
                $fp = @fopen($path, 'rb');

                if ($fp) {
                    @flock($fp, LOCK_SH);

                    $ret = @stream_get_contents($fp);

                    @flock($fp, LOCK_UN);
                    @fclose($fp);

                    return $ret;
                }
            } else {
                return @file_get_contents($path);
            }
        }

        return false;
    }

    /**
     * Flush cache
     *
     * @return bool
     */
    public function flush() {
        @set_time_limit($this->_flushTimeLimit);

        return w3_emptydir($this->_path, $this->_exclude);
    }

    /**
     * Fetch the cache path used
     *
     * @return string
     */
    public function getPath() {
        return $this->_path;
    }

    private $_path = null;
    private $_exclude = null;
    private $_locking = null;
    private $_flushTimeLimit = null;
}
