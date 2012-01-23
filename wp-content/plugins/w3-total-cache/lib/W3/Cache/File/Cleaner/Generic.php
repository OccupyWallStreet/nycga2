<?php

/**
 * Generic file cache cleaner class
 */
if (!defined('ABSPATH')) {
    die();
}

require_once W3TC_LIB_W3_DIR . '/Cache/File/Cleaner.php';

/**
 * Class W3_Cache_File_Cleaner_Generic
 */
class W3_Cache_File_Cleaner_Generic extends W3_Cache_File_Cleaner {
    /**
     * Cache expire time
     *
     * @var int
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
     * @return void
     */
    function W3_Cache_File_Cleaner_Generic($config = array()) {
        $this->__construct($config);
    }

    /**
     * Checks if file is valid
     *
     * @param string $file
     * @return bool
     */
    function is_valid($file) {
        if (file_exists($file)) {
            $ftime = @filemtime($file);

            if ($ftime && $ftime > (time() - $this->_expire)) {
                return true;
            }
        }

        return false;
    }
}
