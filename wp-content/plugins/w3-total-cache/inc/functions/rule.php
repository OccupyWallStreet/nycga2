<?php

/**
 * Check if WP permalink directives exists
 *
 * @return boolean
 */
function w3_is_permalink_rules() {
    if ((w3_is_apache() || w3_is_litespeed()) && !w3_is_network()) {
        $path = w3_get_home_root() . '/.htaccess';

        return (($data = @file_get_contents($path)) && strstr($data, W3TC_MARKER_BEGIN_WORDPRESS) !== false);
    }

    return true;
}

/**
 * Returns nginx rules path
 *
 * @return string
 */
function w3_get_nginx_rules_path() {
    $config = & w3_instance('W3_Config');

    $path = $config->get_string('config.path');

    if (!$path) {
        $path = w3_get_document_root() . '/nginx.conf';
    }

    return $path;
}

/**
 * Returns path of pagecache core rules file
 *
 * @return string
 */
function w3_get_pgcache_rules_core_path() {
    switch (true) {
        case w3_is_apache():
        case w3_is_litespeed():
            return w3_get_home_root() . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of pgcache cache rules file
 *
 * @return string
 */
function w3_get_pgcache_rules_cache_path() {
    switch (true) {
        case w3_is_apache():
        case w3_is_litespeed():
            return W3TC_CACHE_FILE_PGCACHE_DIR . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of browsercache cache rules file
 *
 * @return string
 */
function w3_get_browsercache_rules_cache_path() {
    switch (true) {
        case w3_is_apache():
        case w3_is_litespeed():
            return w3_get_home_root() . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of browsercache no404wp rules file
 *
 * @return string
 */
function w3_get_browsercache_rules_no404wp_path() {
    switch (true) {
        case w3_is_apache():
        case w3_is_litespeed():
            return w3_get_home_root() . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of minify rules file
 *
 * @return string
 */
function w3_get_minify_rules_core_path() {
    switch (true) {
        case w3_is_apache():
        case w3_is_litespeed():
            return W3TC_CACHE_FILE_MINIFY_DIR . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of minify rules file
 *
 * @return string
 */
function w3_get_minify_rules_cache_path() {
    switch (true) {
        case w3_is_apache():
        case w3_is_litespeed():
            return W3TC_CACHE_FILE_MINIFY_DIR . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}

/**
 * Returns path of minify rules file
 *
 * @return string
 */
function w3_get_cdn_rules_path() {
    switch (true) {
        case w3_is_apache():
        case w3_is_litespeed():
            return '.htaccess';

        case w3_is_nginx():
            return 'nginx.conf';
    }

    return false;
}

/**
 * Returns true if we can modify rules
 *
 * @param string $path
 * @return boolean
 */
function w3_can_modify_rules($path) {
    if (w3_is_network()) {
        if (w3_is_apache() || w3_is_litespeed()) {
            switch ($path) {
                case w3_get_pgcache_rules_cache_path():
                case w3_get_minify_rules_core_path():
                case w3_get_minify_rules_cache_path():
                    return true;
            }
        }

        return false;
    }

    return true;
}

/**
 * Trim rules
 *
 * @param string $rules
 * @return string
 */
function w3_trim_rules($rules) {
    $rules = trim($rules);

    if ($rules != '') {
        $rules .= "\n";
    }

    return $rules;
}

/**
 * Cleanup rewrite rules
 *
 * @param string $rules
 * @return string
 */
function w3_clean_rules($rules) {
    $rules = preg_replace('~[\r\n]+~', "\n", $rules);
    $rules = preg_replace('~^\s+~m', '', $rules);
    $rules = w3_trim_rules($rules);

    return $rules;
}

/**
 * Erases text from start to end
 *
 * @param string $rules
 * @param string $start
 * @param string $end
 * @return string
 */
function w3_erase_rules($rules, $start, $end) {
    $rules = preg_replace('~' . w3_preg_quote($start) . "\n.*?" . w3_preg_quote($end) . "\n*~s", '', $rules);
    $rules = w3_trim_rules($rules);

    return $rules;
}

/**
 * Check if rules exist
 *
 * @param string $rules
 * @param string $start
 * @param string $end
 * @return int
 */
function w3_has_rules($rules, $start, $end) {
    return preg_match('~' . w3_preg_quote($start) . "\n.*?" . w3_preg_quote($end) . "\n*~s", $rules);
}
