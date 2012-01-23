<?php

/**
 * Recursive creates directory
 *
 * @param string $path
 * @param integer $mask
 * @param string $curr_path
 * @return boolean
 */
function w3_mkdir($path, $mask = 0777, $curr_path = '') {
    $path = w3_realpath($path);
    $path = trim($path, '/');
    $dirs = explode('/', $path);

    foreach ($dirs as $dir) {
        if ($dir == '') {
            return false;
        }

        $curr_path .= ($curr_path == '' ? '' : '/') . $dir;

        if (!@is_dir($curr_path)) {
            if (!@mkdir($curr_path, $mask)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Recursive remove dir
 *
 * @param string $path
 * @param array $exclude
 * @param bool $remove
 * @return void
 */
function w3_rmdir($path, $exclude = array(), $remove = true) {
    $dir = @opendir($path);

    if ($dir) {
        while (($entry = @readdir($dir)) !== false) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            foreach ($exclude as $mask) {
                if (fnmatch($mask, basename($entry))) {
                    continue 2;
                }
            }

            $full_path = $path . DIRECTORY_SEPARATOR . $entry;

            if (@is_dir($full_path)) {
                w3_rmdir($full_path, $exclude);
            } else {
                @unlink($full_path);
            }
        }

        @closedir($dir);

        if ($remove) {
            @rmdir($path);
        }
    }
}

/**
 * Recursive empty dir
 *
 * @param string $path
 * @param array $exclude
 * @return void
 */
function w3_emptydir($path, $exclude = array()) {
    w3_rmdir($path, $exclude, false);
}

/**
 * Check if file is write-able
 *
 * @param string $file
 * @return boolean
 */
function w3_is_writable($file) {
    $exists = file_exists($file);

    $fp = @fopen($file, 'a');

    if ($fp) {
        fclose($fp);

        if (!$exists) {
            @unlink($file);
        }

        return true;
    }

    return false;
}

/**
 * Cehck if dir is write-able
 *
 * @param string $dir
 * @return boolean
 */
function w3_is_writable_dir($dir) {
    $file = $dir . '/' . uniqid(mt_rand()) . '.tmp';

    return w3_is_writable($file);
}

/**
 * Returns dirname of path
 *
 * @param string $path
 * @return string
 */
function w3_dirname($path) {
    $dirname = dirname($path);

    if ($dirname == '.' || $dirname == '/' || $dirname == '\\') {
        $dirname = '';
    }

    return $dirname;
}

/**
 * Returns open basedirs
 *
 * @return array
 */
function w3_get_open_basedirs() {
    $open_basedir_ini = ini_get('open_basedir');
    $open_basedirs = (W3TC_WIN ? preg_split('~[;,]~', $open_basedir_ini) : explode(':', $open_basedir_ini));
    $result = array();

    foreach ($open_basedirs as $open_basedir) {
        $open_basedir = trim($open_basedir);
        if ($open_basedir != '') {
            $result[] = w3_realpath($open_basedir);
        }
    }

    return $result;
}

/**
 * Checks if path is restricted by open_basedir
 *
 * @param string $path
 * @return boolean
 */
function w3_check_open_basedir($path) {
    $path = w3_realpath($path);
    $open_basedirs = w3_get_open_basedirs();

    if (!count($open_basedirs)) {
        return true;
    }

    foreach ($open_basedirs as $open_basedir) {
        if (strstr($path, $open_basedir) !== false) {
            return true;
        }
    }

    return false;
}
