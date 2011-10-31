<?php

if (!function_exists('file_put_contents')) {
    if (!defined('FILE_APPEND')) {
        define('FILE_APPEND', 8);
    }

    function file_put_contents($filename, $data, $flags = 0) {
        $fp = fopen($filename, ($flags & FILE_APPEND ? 'a' : 'w'));

        if ($fp) {
            fputs($fp, $data);
            fclose($fp);

            return true;
        }

        return false;
    }
}

if (!function_exists('json_encode')) {
    function json_encode($string) {
        global $json;

        if (!is_a($json, 'Services_JSON')) {
            require_once W3TC_LIB_DIR . '/JSON.php';
            $json = new Services_JSON();
        }

        return $json->encodeUnsafe($string);
    }
}

if (!function_exists('json_decode')) {
    function json_decode($string, $assoc_array = false) {
        global $json;

        if (!is_a($json, 'Services_JSON')) {
            require_once W3TC_LIB_DIR . '/JSON.php';
            $json = new Services_JSON();
        }

        $res = $json->decode($string);

        if ($assoc_array) {
            $res = _json_decode_object_helper($res);
        }

        return $res;
    }

    function _json_decode_object_helper($data) {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        return (is_array($data) ? array_map(__FUNCTION__, $data) : $data);
    }
}

if (!function_exists('fnmatch')) {
    define('FNM_PATHNAME', 1);
    define('FNM_NOESCAPE', 2);
    define('FNM_PERIOD', 4);
    define('FNM_CASEFOLD', 16);

    function fnmatch($pattern, $string, $flags = 0) {
        $modifiers = null;
        $transforms = array(
            '\*' => '.*',
            '\?' => '.',
            '\[\!' => '[^',
            '\[' => '[',
            '\]' => ']',
            '\.' => '\.',
            '\\' => '\\\\'
        );

        // Forward slash in string must be in pattern:
        if ($flags & FNM_PATHNAME) {
            $transforms['\*'] = '[^/]*';
        }

        // Back slash should not be escaped:
        if ($flags & FNM_NOESCAPE) {
            unset($transforms['\\']);
        }

        // Perform case insensitive match:
        if ($flags & FNM_CASEFOLD) {
            $modifiers .= 'i';
        }

        // Period at start must be the same as pattern:
        if ($flags & FNM_PERIOD) {
            if (strpos($string, '.') === 0 && strpos($pattern, '.') !== 0) return false;
        }

        $pattern = '~^' . strtr(preg_quote($pattern, '~'), $transforms) . '$~' . $modifiers;

        return (boolean) preg_match($pattern, $string);
    }
}
