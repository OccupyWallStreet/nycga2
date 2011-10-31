<?php

if (!defined('ABSPATH')) {
    die();
}

define('W3TC', true);
define('W3TC_VERSION', '0.9.2.4');
define('W3TC_POWERED_BY', 'W3 Total Cache/' . W3TC_VERSION);
define('W3TC_EMAIL', 'w3tc@w3-edge.com');
define('W3TC_PAYPAL_URL', 'https://www.paypal.com/cgi-bin/webscr');
define('W3TC_PAYPAL_BUSINESS', 'w3tc-team@w3-edge.com');
define('W3TC_LINK_URL', 'http://www.w3-edge.com/wordpress-plugins/');
define('W3TC_LINK_NAME', 'WordPress Plugins');
define('W3TC_FEED_URL', 'http://feeds.feedburner.com/W3TOTALCACHE');
define('W3TC_README_URL', 'http://plugins.trac.wordpress.org/browser/w3-total-cache/trunk/readme.txt?format=txt');
define('W3TC_SUPPORT_US_TIMEOUT', 2592000);

define('W3TC_PHP5', PHP_VERSION >= 5);
define('W3TC_WIN', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

defined('W3TC_DIR') || define('W3TC_DIR', realpath(dirname(__FILE__) . '/..'));
define('W3TC_FILE', 'w3-total-cache/w3-total-cache.php');
define('W3TC_INC_DIR', W3TC_DIR . '/inc');
define('W3TC_LIB_DIR', W3TC_DIR . '/lib');
define('W3TC_LIB_W3_DIR', W3TC_LIB_DIR . '/W3');
define('W3TC_LIB_MINIFY_DIR', W3TC_LIB_DIR . '/Minify');
define('W3TC_LIB_CF_DIR', W3TC_LIB_DIR . '/CF');
define('W3TC_LIB_CSSTIDY_DIR', W3TC_LIB_DIR . '/CSSTidy');
define('W3TC_LIB_MICROSOFT_DIR', W3TC_LIB_DIR . '/Microsoft');
define('W3TC_LIB_NUSOAP_DIR', W3TC_LIB_DIR . '/Nusoap');
define('W3TC_PLUGINS_DIR', W3TC_DIR . '/plugins');
define('W3TC_INSTALL_DIR', W3TC_DIR . '/wp-content');
define('W3TC_INSTALL_MINIFY_DIR', W3TC_INSTALL_DIR . '/w3tc/min');

define('W3TC_BLOGNAMES_PATH', WP_CONTENT_DIR . '/w3-total-cache-blognames.php');
define('W3TC_BLOGNAME', w3_get_blogname());
define('W3TC_SUFFIX', (W3TC_BLOGNAME != '' ? '-' . W3TC_BLOGNAME : ''));

defined('WP_CONTENT_DIR') || define('WP_CONTENT_DIR', realpath(W3TC_DIR . '/../..'));
define('WP_CONTENT_DIR_PATH', dirname(WP_CONTENT_DIR));
define('WP_CONTENT_DIR_NAME', basename(WP_CONTENT_DIR));
define('W3TC_CONTENT_DIR_NAME', WP_CONTENT_DIR_NAME . '/w3tc' . W3TC_SUFFIX);
define('W3TC_CONTENT_DIR', WP_CONTENT_DIR_PATH . '/' . W3TC_CONTENT_DIR_NAME);
define('W3TC_CONTENT_MINIFY_DIR_NAME', W3TC_CONTENT_DIR_NAME . '/min');
define('W3TC_CONTENT_MINIFY_DIR', WP_CONTENT_DIR_PATH . '/' . W3TC_CONTENT_DIR_NAME . '/min');
define('W3TC_CACHE_FILE_DBCACHE_DIR', W3TC_CONTENT_DIR . '/dbcache');
define('W3TC_CACHE_FILE_OBJECTCACHE_DIR', W3TC_CONTENT_DIR . '/objectcache');
define('W3TC_CACHE_FILE_PGCACHE_DIR', W3TC_CONTENT_DIR . '/pgcache');
define('W3TC_CACHE_FILE_MINIFY_DIR', W3TC_CONTENT_DIR . '/min');
define('W3TC_LOG_DIR', W3TC_CONTENT_DIR . '/log');
define('W3TC_TMP_DIR', W3TC_CONTENT_DIR . '/tmp');
define('W3TC_CONFIG_PATH', WP_CONTENT_DIR . '/w3-total-cache-config' . W3TC_SUFFIX . '.php');
define('W3TC_CONFIG_PREVIEW_PATH', WP_CONTENT_DIR . '/w3-total-cache-config' . W3TC_SUFFIX . '-preview.php');
define('W3TC_CONFIG_MASTER_PATH', WP_CONTENT_DIR . '/w3-total-cache-config.php');
define('W3TC_MINIFY_LOG_FILE', W3TC_LOG_DIR . '/minify.log');
define('W3TC_CDN_COMMAND_UPLOAD', 1);
define('W3TC_CDN_COMMAND_DELETE', 2);
define('W3TC_CDN_COMMAND_PURGE', 3);
define('W3TC_CDN_TABLE_QUEUE', 'w3tc_cdn_queue');
define('W3TC_CDN_LOG_FILE', W3TC_LOG_DIR . '/cdn.log');
define('W3TC_VARNISH_LOG_FILE', W3TC_LOG_DIR . '/varnish.log');

define('W3TC_MARKER_BEGIN_WORDPRESS', '# BEGIN WordPress');
define('W3TC_MARKER_BEGIN_PGCACHE_CORE', '# BEGIN W3TC Page Cache core');
define('W3TC_MARKER_BEGIN_PGCACHE_CACHE', '# BEGIN W3TC Page Cache cache');
define('W3TC_MARKER_BEGIN_PGCACHE_LEGACY', '# BEGIN W3TC Page Cache');
define('W3TC_MARKER_BEGIN_PGCACHE_WPSC', '# BEGIN WPSuperCache');
define('W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE', '# BEGIN W3TC Browser Cache');
define('W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP', '# BEGIN W3TC Skip 404 error handling by WordPress for static files');
define('W3TC_MARKER_BEGIN_MINIFY_CORE', '# BEGIN W3TC Minify core');
define('W3TC_MARKER_BEGIN_MINIFY_CACHE', '# BEGIN W3TC Minify cache');
define('W3TC_MARKER_BEGIN_MINIFY_LEGACY', '# BEGIN W3TC Minify');

define('W3TC_MARKER_END_WORDPRESS', '# END WordPress');
define('W3TC_MARKER_END_PGCACHE_CORE', '# END W3TC Page Cache core');
define('W3TC_MARKER_END_PGCACHE_CACHE', '# END W3TC Page Cache cache');
define('W3TC_MARKER_END_PGCACHE_LEGACY', '# END W3TC Page Cache');
define('W3TC_MARKER_END_PGCACHE_WPSC', '# END WPSuperCache');
define('W3TC_MARKER_END_BROWSERCACHE_CACHE', '# END W3TC Browser Cache');
define('W3TC_MARKER_END_BROWSERCACHE_NO404WP', '# END W3TC Skip 404 error handling by WordPress for static files');
define('W3TC_MARKER_END_MINIFY_CORE', '# END W3TC Minify core');
define('W3TC_MARKER_END_MINIFY_CACHE', '# END W3TC Minify cache');
define('W3TC_MARKER_END_MINIFY_LEGACY', '# END W3TC Minify');

define('W3TC_INSTALL_FILE_ADVANCED_CACHE', W3TC_INSTALL_DIR . '/advanced-cache.php');
define('W3TC_INSTALL_FILE_DB', W3TC_INSTALL_DIR . '/db.php');
define('W3TC_INSTALL_FILE_OBJECT_CACHE', W3TC_INSTALL_DIR . '/object-cache.php');

define('W3TC_ADDIN_FILE_ADVANCED_CACHE', WP_CONTENT_DIR . '/advanced-cache.php');
define('W3TC_ADDIN_FILE_DB', WP_CONTENT_DIR . '/db.php');
define('W3TC_ADDIN_FILE_OBJECT_CACHE', WP_CONTENT_DIR . '/object-cache.php');

require_once W3TC_INC_DIR . '/functions/compat.php';
require_once W3TC_INC_DIR . '/functions/plugin.php';

@ini_set('pcre.backtrack_limit', 4194304);
@ini_set('pcre.recursion_limit', 4194304);

/**
 * Returns current microtime
 *
 * @return double
 */
function w3_microtime() {
    list ($usec, $sec) = explode(' ', microtime());

    return ((double) $usec + (double) $sec);
}

/**
 * Check if content is HTML or XML
 *
 * @param string $content
 * @return boolean
 */
function w3_is_xml($content) {
    if (strlen($content) > 1000) {
        $content = substr($content, 0, 1000);
    }

    if (strstr($content, '<!--') !== false) {
        $content = preg_replace('~<!--.*?-->~s', '', $content);
    }

    $content = ltrim($content, "\x00\x09\x0A\x0D\x20\xBB\xBF\xEF");

    return (stripos($content, '<?xml') === 0 || stripos($content, '<html') === 0 || stripos($content, '<!DOCTYPE') === 0);
}

/**
 * Returns true if it's WPMU
 *
 * @return boolean
 */
function w3_is_wpmu() {
    static $wpmu = null;

    if ($wpmu === null) {
        $wpmu = file_exists(ABSPATH . 'wpmu-settings.php');
    }

    return $wpmu;
}

/**
 * Returns true if WPMU uses vhosts
 *
 * @return boolean
 */
function w3_is_subdomain_install() {
    return ((defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) || (defined('VHOST') && VHOST == 'yes'));
}

/**
 * Returns true if it's WP with enabled Network mode
 *
 * @return boolean
 */
function w3_is_multisite() {
    static $multisite = null;

    if ($multisite === null) {
        $multisite = ((defined('MULTISITE') && MULTISITE) || defined('SUNRISE') || w3_is_subdomain_install());
    }

    return $multisite;
}

/**
 * Returns if there is multisite mode
 *
 * @return boolean
 */
function w3_is_network() {
    return (w3_is_wpmu() || w3_is_multisite());
}

/**
 * Check if URL is valid
 *
 * @param string $url
 * @return boolean
 */
function w3_is_url($url) {
    return preg_match('~^(https?:)?//~', $url);
}

/**
 * Returns true if current connection is secure
 *
 * @return boolean
 */
function w3_is_https() {
    switch (true) {
        case (isset($_SERVER['HTTPS']) && w3_to_boolean($_SERVER['HTTPS'])):
        case (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] == 443):
            return true;
    }

    return false;
}

/**
 * Check if there was database error
 *
 * @param string $content
 * @return boolean
 */
function w3_is_database_error(&$content) {
    return (stristr($content, '<title>Database Error</title>') !== false);
}

/**
 * Returns true if preview config exists
 *
 * @return boolean
 */
function w3_is_preview_config() {
    return file_exists(W3TC_CONFIG_PREVIEW_PATH);
}

/**
 * Retuns true if preview settings active
 *
 * @return boolean
 */
function w3_is_preview_mode() {
    return (w3_is_preview_config() && (defined('WP_ADMIN') || isset($_REQUEST['w3tc_preview']) || (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], 'w3tc_preview') !== false)));
}

/**
 * Returns true if server is Apache
 *
 * @return boolean
 */
function w3_is_apache() {
    return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false);
}

/**
 * Check whether server is LiteSpeed
 *
 * @return bool
 */
function w3_is_litespeed() {
    return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false);
}

/**
 * Returns true if server is nginx
 *
 * @return boolean
 */
function w3_is_nginx() {
    return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false);
}

/**
 * Check whether $engine is correct CDN engine
 *
 * @param string $engine
 * @return boolean
 */
function w3_is_cdn_engine($engine) {
    return in_array($engine, array('ftp', 's3', 'cf', 'cf2', 'rscf', 'azure', 'mirror', 'netdna', 'cotendo', 'edgecast'));
}

/**
 * Returns true if CDN engine is mirror
 *
 * @param string $engine
 * @return bool
 */
function w3_is_cdn_mirror($engine) {
    return in_array($engine, array('mirror', 'netdna', 'cotendo', 'cf2', 'edgecast'));
}

/**
 * Returns domain from host
 *
 * @param string $host
 * @return string
 */
function w3_get_domain($host) {
    $host = strtolower($host);

    if (strpos($host, 'www.') === 0) {
        $host = substr($host, 4);
    }

    if (($pos = strpos($host, ':')) !== false) {
        $host = substr($host, 0, $pos);
    }

    $host = rtrim($host, '.');

    return $host;
}

/**
 * Returns array of all available blognames
 *
 * @return array
 */
function w3_get_blognames() {
    global $wpdb;

    $blognames = array();

    $sql = sprintf('SELECT domain, path FROM %s', $wpdb->blogs);
    $blogs = $wpdb->get_results($sql);

    if ($blogs) {
        $base_path = w3_get_base_path();

        foreach ($blogs as $blog) {
            $blogname = trim(str_replace($base_path, '', $blog->path), '/');

            if ($blogname) {
                $blognames[] = $blogname;
            }
        }
    }

    return $blognames;
}

/**
 * Load blognames from file
 *
 * @return array
 */
function w3_load_blognames() {
    $blognames = include W3TC_BLOGNAMES_PATH;

    return $blognames;
}

/**
 * Save blognames into file
 *
 * @param string $blognames
 * @return boolean
 */
function w3_save_blognames($blognames = null) {
    if (!$blognames) {
        $blognames = w3_get_blognames();
    }

    $strings = array();

    foreach ($blognames as $blogname) {
        $strings[] = sprintf("'%s'", addslashes($blogname));
    }

    $data = sprintf('<?php return array(%s);', implode(', ', $strings));

    return @file_put_contents(W3TC_BLOGNAMES_PATH, $data);
}

/**
 * Detect WPMU blogname
 *
 * @return string
 */
function w3_get_blogname() {
    static $blogname = null;

    if ($blogname === null) {
        if (w3_is_network()) {
            $host = w3_get_host();
            $domain = w3_get_domain($host);

            if (w3_is_subdomain_install()) {
                $blogname = $domain;
            } else {
                $uri = $_SERVER['REQUEST_URI'];
                $base_path = w3_get_base_path();

                if ($base_path != '' && strpos($uri, $base_path) === 0) {
                    $uri = substr_replace($uri, '/', 0, strlen($base_path));
                }

                $blogname = w3_get_blogname_from_uri($uri);

                if ($blogname != '') {
                    $blogname = $blogname . '.' . $domain;
                } else {
                    $blogname = $domain;
                }
            }
        } else {
            $blogname = '';
        }
    }

    return $blogname;
}

/**
 * Returns blogname from URI
 *
 * @param string $uri
 * @return string
 */
function w3_get_blogname_from_uri($uri) {
    $blogname = '';
    $matches = null;
    $uri = strtolower($uri);

    if (preg_match('~^/([a-z0-9-]+)/~', $uri, $matches)) {
        if (file_exists(W3TC_BLOGNAMES_PATH)) {
            // Get blognames from cache
            $blognames = w3_load_blognames();
        } elseif (isset($GLOBALS['wpdb'])) {
            // Get blognames from DB
            $blognames = w3_get_blognames();
        } else {
            $blognames = array();
        }

        if (is_array($blognames) && in_array($matches[1], $blognames)) {
            $blogname = $matches[1];
        }
    }

    return $blogname;
}

/**
 * Returns current blog ID
 *
 * @return integer
 */
function w3_get_blog_id() {
    return (isset($GLOBALS['blog_id']) ? (int) $GLOBALS['blog_id'] : 0);
}

/**
 * Returns URL regexp from URL
 *
 * @param string $url
 * @return string
 */
function w3_get_url_regexp($url) {
    $url = preg_replace('~(https?:)?//~i', '', $url);
    $url = preg_replace('~^www\.~i', '', $url);

    $regexp = '(https?:)?//(www\.)?' . w3_preg_quote($url);

    return $regexp;
}

/**
 * Returns SSL URL if current connection is https
 * @param string $url
 * @return string
 */
function w3_get_url_ssl($url) {
    if (w3_is_https()) {
        $url = str_replace('http://', 'https://', $url);
    }

    return $url;
}

/**
 * Get domain URL
 *
 * @return string
 */

function w3_get_domain_url() {
    $home_url = w3_get_home_url();
    $parse_url = @parse_url($home_url);

    if ($parse_url && isset($parse_url['scheme']) && isset($parse_url['host'])) {
        $scheme = $parse_url['scheme'];
        $host = $parse_url['host'];
        $port = (isset($parse_url['port']) && $parse_url['port'] != 80 ? ':' . (int) $parse_url['port'] : '');
        $domain_url = sprintf('%s://%s%s', $scheme, $host, $port);

        return $domain_url;
    }

    return false;
}

/**
 * Returns domain url regexp
 *
 * @return string
 */
function w3_get_domain_url_regexp() {
    $domain_url = w3_get_domain_url();
    $regexp = w3_get_url_regexp($domain_url);

    return $regexp;
}

/**
 * Returns home URL
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_home_url() {
    static $home_url = null;

    if ($home_url === null) {
        $home_url = get_option('home');
        $home_url = rtrim($home_url, '/');
    }

    return $home_url;
}

/**
 * Returns SSL home url
 *
 * @return string
 */
function w3_get_home_url_ssl() {
    $home_url = w3_get_home_url();
    $ssl = w3_get_url_ssl($home_url);

    return $ssl;
}

/**
 * Returns home url regexp
 *
 * @return string
 */
function w3_get_home_url_regexp() {
    $home_url = w3_get_home_url();
    $regexp = w3_get_url_regexp($home_url);

    return $regexp;
}

/**
 * Returns site URL
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_site_url() {
    static $site_url = null;

    if ($site_url === null) {
        $site_url = get_option('siteurl');
        $site_url = rtrim($site_url, '/');
    }

    return $site_url;
}

/**
 * Returns SSL site URL
 *
 * @return string
 */
function w3_get_site_url_ssl() {
    $site_url = w3_get_site_url();
    $ssl = w3_get_url_ssl($site_url);

    return $ssl;

}

/**
 * Returns absolute path to document root
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_document_root() {
    static $document_root = null;

    if ($document_root === null) {
        if (!empty($_SERVER['SCRIPT_FILENAME'])) {
            $document_root = substr(w3_path($_SERVER['SCRIPT_FILENAME']), 0, -strlen(w3_path($_SERVER['PHP_SELF'])));
        } elseif (!empty($_SERVER['PATH_TRANSLATED'])) {
            $document_root = substr(w3_path($_SERVER['PATH_TRANSLATED']), 0, -strlen(w3_path($_SERVER['PHP_SELF'])));
        } elseif (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $document_root = w3_path($_SERVER['DOCUMENT_ROOT']);
        } else {
            $document_root = w3_get_site_root();
        }

        $document_root = realpath($document_root);
        $document_root = w3_path($document_root);
    }

    return $document_root;
}

/**
 * Returns absolute path to home directory
 *
 * Example:
 *
 * DOCUMENT_ROOT=/var/www/vhosts/domain.com
 * Install dir=/var/www/vhosts/domain.com/site/blog
 * home=http://domain.com/site
 * siteurl=http://domain.com/site/blog
 * return /var/www/vhosts/domain.com/site
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_home_root() {
    if (w3_is_network()) {
        $path = w3_get_base_path();
    } else {
        $path = w3_get_home_path();
    }

    $home_root = w3_get_document_root() . $path;
    $home_root = realpath($home_root);
    $home_root = w3_path($home_root);

    return $home_root;
}

/**
 * Returns absolute path to blog install dir
 *
 * Example:
 *
 * DOCUMENT_ROOT=/var/www/vhosts/domain.com
 * install dir=/var/www/vhosts/domain.com/site/blog
 * return /var/www/vhosts/domain.com/site/blog
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_site_root() {
    $site_root = ABSPATH;
    $site_root = realpath($site_root);
    $site_root = w3_path($site_root);

    return $site_root;
}

/**
 * Returns blog path
 *
 * Example:
 *
 * siteurl=http://domain.com/site/blog
 * return /site/blog/
 *
 * With trailing slash!
 *
 * @return string
 */
function w3_get_site_path() {
    $site_url = w3_get_site_url();
    $parse_url = @parse_url($site_url);

    if ($parse_url && isset($parse_url['path'])) {
        $site_path = '/' . ltrim($parse_url['path'], '/');
    } else {
        $site_path = '/';
    }

    if (substr($site_path, -1) != '/') {
        $site_path .= '/';
    }

    return $site_path;
}

/**
 * Returns home domain
 *
 * @return string
 */
function w3_get_home_domain() {
    $home_url = w3_get_home_url();
    $parse_url = @parse_url($home_url);

    if ($parse_url && isset($parse_url['host'])) {
        return $parse_url['host'];
    }

    return w3_get_host();
}

/**
 * Returns home path
 *
 * Example:
 *
 * home=http://domain.com/site/
 * siteurl=http://domain.com/site/blog
 * return /site/
 *
 * With trailing slash!
 *
 * @return string
 */
function w3_get_home_path() {
    $home_url = w3_get_home_url();
    $parse_url = @parse_url($home_url);

    if ($parse_url && isset($parse_url['path'])) {
        $home_path = '/' . ltrim($parse_url['path'], '/');
    } else {
        $home_path = '/';
    }

    if (substr($home_path, -1) != '/') {
        $home_path .= '/';
    }

    return $home_path;
}

/**
 * Returns path to WP directory relative to document root
 *
 * Example:
 *
 * DOCUMENT_ROOT=/var/www/vhosts/domain.com/
 * Install dir=/var/www/vhosts/domain.com/site/blog/
 * return /site/blog/
 *
 * With trailing slash!
 *
 * @return string
 */
function w3_get_base_path() {
    $document_root = w3_get_document_root();
    $site_root = w3_get_site_root();

    $base_path = str_replace($document_root, '', $site_root);
    $base_path = '/' . ltrim($base_path, '/');

    if (substr($base_path, -1) != '/') {
        $base_path .= '/';
    }

    return $base_path;
}

/**
 * Returns server hostname
 *
 * @return string
 */
function w3_get_host() {
    static $host = null;

    if ($host === null) {
        $host = (!empty($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
    }

    return $host;
}

/**
 * Returns host ID
 *
 * @return string
 */
function w3_get_host_id() {
    static $host_id = null;

    if ($host_id === null) {
        $host = w3_get_host();
        $blog_id = w3_get_blog_id();

        $host_id = sprintf('%s_%d', $host, $blog_id);
    }

    return $host_id;
}

/**
 * Returns WP config file path
 *
 * @return string
 */
function w3_get_wp_config_path() {
    $search = array(
        ABSPATH . 'wp-config.php',
        dirname(ABSPATH) . '/wp-config.php'
    );

    foreach ($search as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    return false;
}

/**
 * Returns theme key
 *
 * @param string $theme_root
 * @param string $template
 * @param string $stylesheet
 * @return string
 */
function w3_get_theme_key($theme_root, $template, $stylesheet) {
    $site_root = w3_get_site_root();
    $theme_path = ltrim(str_replace($site_root, '', w3_path($theme_root)), '/');

    return substr(md5($theme_path . $template . $stylesheet), 0, 5);
}

/**
 * Returns theme key (legacy support)
 *
 * @param string $theme_root
 * @param string $template
 * @param string $stylesheet
 * @return string
 */
function w3_get_theme_key_legacy($theme_root, $template, $stylesheet) {
    return substr(md5($theme_root . $template . $stylesheet), 0, 6);
}

/**
 * Returns true if we can check rules
 *
 * @return bool
 */
function w3_can_check_rules() {
    return (w3_is_apache() || w3_is_litespeed() || w3_is_nginx());
}

/**
 * Returns true if CDN engine is supporting purge
 *
 * @param string $engine
 * @return bool
 */
function w3_can_cdn_purge($engine) {
    return in_array($engine, array('ftp', 's3', 'cf', 'cf2', 'rscf', 'azure', 'netdna', 'cotendo', 'edgecast'));
}

/**
 * Parses path
 *
 * @param string $path
 * @return mixed
 */
function w3_parse_path($path) {
    $path = str_replace(array(
        '%BLOG_ID%',
        '%POST_ID%',
        '%BLOGNAME%',
        '%HOST%',
        '%DOMAIN%',
        '%BASE_PATH%'
    ), array(
        (isset($GLOBALS['blog_id']) ? (int) $GLOBALS['blog_id'] : 0),
        (isset($GLOBALS['post_id']) ? (int) $GLOBALS['post_id'] : 0),
        w3_get_blogname(),
        w3_get_host(),
        w3_get_domain(w3_get_host()),
        trim(w3_get_base_path(), '/')
    ), $path);

    return $path;
}

/**
 * Normalizes file name
 *
 * Relative to site root!
 *
 * @param string $file
 * @return string
 */
function w3_normalize_file($file) {
    if (w3_is_url($file)) {
        if (strstr($file, '?') === false) {
            $home_url_regexp = '~' . w3_get_home_url_regexp() . '~i';
            $file = preg_replace($home_url_regexp, '', $file);
        }
    }

    if (!w3_is_url($file)) {
        $file = w3_path($file);
        $file = str_replace(w3_get_site_root(), '', $file);
        $file = ltrim($file, '/');
    }

    return $file;
}

/**
 * Normalizes file name for minify
 *
 * Relative to document root!
 *
 * @param string $file
 * @return string
 */
function w3_normalize_file_minify($file) {
    if (w3_is_url($file)) {
        if (strstr($file, '?') === false) {
            $domain_url_regexp = '~' . w3_get_domain_url_regexp() . '~i';
            $file = preg_replace($domain_url_regexp, '', $file);
        }
    }

    if (!w3_is_url($file)) {
        $file = w3_path($file);
        $file = str_replace(w3_get_document_root(), '', $file);
        $file = ltrim($file, '/');
    }

    return $file;
}

/**
 * Normalizes file name for minify
 *
 * Relative to document root!
 *
 * @param string $file
 * @return string
 */
function w3_normalize_file_minify2($file) {
    $file = w3_remove_query($file);
    $file = w3_normalize_file_minify($file);
    $file = w3_translate_file($file);

    return $file;
}

/**
 * Translates remote file to local file
 *
 * @param string $file
 * @return string
 */
function w3_translate_file($file) {
    if (!w3_is_url($file)) {
        $file = '/' . ltrim($file, '/');
        $regexp = '~^' . w3_preg_quote(w3_get_site_path()) . '~';
        $file = preg_replace($regexp, w3_get_base_path(), $file);
        $file = ltrim($file, '/');
    }

    return $file;
}

/**
 * Remove WP query string from URL
 *
 * @param string $url
 * @return string
 */
function w3_remove_query($url) {
    $url = preg_replace('~[&\?]+(ver=[a-z0-9-_\.]+|[0-9-]+)~i', '', $url);

    return $url;
}

/**
 * Converts win path to unix
 *
 * @param string $path
 * @return string
 */
function w3_path($path) {
    $path = preg_replace('~[/\\\]+~', '/', $path);
    $path = rtrim($path, '/');

    return $path;
}

/**
 * Returns real path of given path
 *
 * @param string $path
 * @return string
 */
function w3_realpath($path) {
    $path = w3_path($path);
    $parts = explode('/', $path);
    $absolutes = array();

    foreach ($parts as $part) {
        if ('.' == $part) {
            continue;
        }
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }

    return implode('/', $absolutes);
}

/**
 * Returns GMT date
 * @param integer $time
 * @return string
 */
function w3_http_date($time) {
    return gmdate('D, d M Y H:i:s \G\M\T', $time);
}

/**
 * Redirects to URL
 *
 * @param string $url
 * @param array $params
 * @return string
 */
function w3_redirect($url = '', $params = array()) {
    require_once W3TC_INC_DIR . '/functions/url.php';

    $url = w3_url_format($url, $params);

    @header('Location: ' . $url);
    exit();
}

/**
 * Returns caching engine name
 *
 * @param $engine
 * @return string
 */
function w3_get_engine_name($engine) {
    switch ($engine) {
        case 'memcached':
            $engine_name = 'memcached';
            break;

        case 'apc':
            $engine_name = 'apc';
            break;

        case 'eaccelerator':
            $engine_name = 'eaccelerator';
            break;

        case 'xcache':
            $engine_name = 'xcache';
            break;

        case 'wincache':
            $engine_name = 'wincache';
            break;

        case 'file':
            $engine_name = 'disk: basic';
            break;

        case 'file_generic':
            $engine_name = 'disk: enhanced';
            break;

        case 'ftp':
            $engine_name = 'self-hosted / file transfer protocol upload';
            break;

        case 's3':
            $engine_name = 'amazon simple storage service (s3)';
            break;

        case 'cf':
            $engine_name = 'amazon cloudfront';
            break;

        case 'cf2':
            $engine_name = 'amazon cloudfront';
            break;

        case 'rscf':
            $engine_name = 'rackspace cloud files';
            break;

        case 'azure':
            $engine_name = 'microsoft azure storage';
            break;

        case 'mirror':
            $engine_name = 'mirror';
            break;

        case 'netdna':
            $engine_name = 'netdna / maxcdn';
            break;

        case 'cotendo':
            $engine_name = 'cotendo';
            break;

        case 'edgecast':
            $engine_name = 'media template procdn / edgecast';
            break;

        default:
            $engine_name = 'n/a';
            break;
    }

    return $engine_name;
}

/**
 * Converts value to boolean
 *
 * @param mixed $value
 * @return boolean
 */
function w3_to_boolean($value) {
    if (is_string($value)) {
        switch (strtolower($value)) {
            case '+':
            case '1':
            case 'y':
            case 'on':
            case 'yes':
            case 'true':
            case 'enabled':
                return true;

            case '-':
            case '0':
            case 'n':
            case 'no':
            case 'off':
            case 'false':
            case 'disabled':
                return false;
        }
    }

    return (boolean) $value;
}

/**
 * Quotes regular expression string
 *
 * @param string $string
 * @param string $delimiter
 * @return string
 */
function w3_preg_quote($string, $delimiter = null) {
    $string = preg_quote($string, $delimiter);
    $string = strtr($string, array(
        ' ' => '\ '
    ));

    return $string;
}

/**
 * Returns true if zlib output compression is enabled otherwise false
 *
 * @return boolean
 */
function w3_zlib_output_compression() {
    return w3_to_boolean(ini_get('zlib.output_compression'));
}

/**
 * Recursive strips slahes from the var
 *
 * @param mixed $var
 * @return mixed
 */
function w3_stripslashes($var) {
    if (is_string($var)) {
        return stripslashes($var);
    } elseif (is_array($var)) {
        $var = array_map('w3_stripslashes', $var);
    }

    return $var;
}

/**
 * Escapes HTML comment
 *
 * @param string $comment
 * @return mixed
 */
function w3_escape_comment($comment) {
    while (strstr($comment, '--') !== false) {
        $comment = str_replace('--', '- -', $comment);
    }

    return $comment;
}

/**
 * Returns instance of singleton class
 *
 * @param string $class
 * @return object
 */
function &w3_instance($class) {
    static $instances = array();

    if (!isset($instances[$class])) {
        require_once W3TC_LIB_W3_DIR . '/' .
                str_replace('_', '/', substr($class, 3)) . '.php';
        @$instances[$class] = & new $class();
    }

    $v = $instances[$class];   // Don't return reference
    return $v;
}

/**
 * Loads plugins
 *
 * @return void
 */
function w3_load_plugins() {
    $dir = @opendir(W3TC_PLUGINS_DIR);

    if ($dir) {
        while (($entry = @readdir($dir)) !== false) {
            if (strrchr($entry, '.') === '.php') {
                require_once W3TC_PLUGINS_DIR . '/' . $entry;
            }
        }
        @closedir($dir);
    }
}
