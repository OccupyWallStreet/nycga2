<?php

/**
 * W3 Config object
 */

/**
 * Class W3_Config
 */
class W3_Config {
    /**
     * Tabs count
     *
     * @var integer
     */
    var $_tabs = 0;

    /**
     * Array of config values
     *
     * @var array
     */
    var $_config = array();

    /**
     * Config keys
     */
    var $_keys = array(
        'dbcache.enabled' => 'boolean',
        'dbcache.debug' => 'boolean',
        'dbcache.engine' => 'string',
        'dbcache.file.gc' => 'integer',
        'dbcache.file.locking' => 'boolean',
        'dbcache.memcached.servers' => 'array',
        'dbcache.memcached.persistant' => 'boolean',
        'dbcache.reject.logged' => 'boolean',
        'dbcache.reject.uri' => 'array',
        'dbcache.reject.cookie' => 'array',
        'dbcache.reject.sql' => 'array',
        'dbcache.lifetime' => 'integer',

        'objectcache.enabled' => 'boolean',
        'objectcache.debug' => 'boolean',
        'objectcache.engine' => 'string',
        'objectcache.file.gc' => 'integer',
        'objectcache.file.locking' => 'boolean',
        'objectcache.memcached.servers' => 'array',
        'objectcache.memcached.persistant' => 'boolean',
        'objectcache.groups.global' => 'array',
        'objectcache.groups.nonpersistent' => 'array',
        'objectcache.lifetime' => 'integer',

        'pgcache.enabled' => 'boolean',
        'pgcache.debug' => 'boolean',
        'pgcache.engine' => 'string',
        'pgcache.file.gc' => 'integer',
        'pgcache.file.nfs' => 'boolean',
        'pgcache.file.locking' => 'boolean',
        'pgcache.memcached.servers' => 'array',
        'pgcache.memcached.persistant' => 'boolean',
        'pgcache.check.domain' => 'boolean',
        'pgcache.cache.query' => 'boolean',
        'pgcache.cache.home' => 'boolean',
        'pgcache.cache.feed' => 'boolean',
        'pgcache.cache.ssl' => 'boolean',
        'pgcache.cache.404' => 'boolean',
        'pgcache.cache.flush' => 'boolean',
        'pgcache.cache.headers' => 'array',
        'pgcache.accept.uri' => 'array',
        'pgcache.accept.files' => 'array',
        'pgcache.reject.logged' => 'boolean',
        'pgcache.reject.uri' => 'array',
        'pgcache.reject.ua' => 'array',
        'pgcache.reject.cookie' => 'array',
        'pgcache.purge.home' => 'boolean',
        'pgcache.purge.post' => 'boolean',
        'pgcache.purge.comments' => 'boolean',
        'pgcache.purge.author' => 'boolean',
        'pgcache.purge.terms' => 'boolean',
        'pgcache.purge.archive.daily' => 'boolean',
        'pgcache.purge.archive.monthly' => 'boolean',
        'pgcache.purge.archive.yearly' => 'boolean',
        'pgcache.purge.feed.blog' => 'boolean',
        'pgcache.purge.feed.comments' => 'boolean',
        'pgcache.purge.feed.author' => 'boolean',
        'pgcache.purge.feed.terms' => 'boolean',
        'pgcache.purge.feed.types' => 'array',
        'pgcache.prime.enabled' => 'boolean',
        'pgcache.prime.interval' => 'integer',
        'pgcache.prime.limit' => 'integer',
        'pgcache.prime.sitemap' => 'string',

        'minify.enabled' => 'boolean',
        'minify.auto' => 'boolean',
        'minify.debug' => 'boolean',
        'minify.engine' => 'string',
        'minify.file.gc' => 'integer',
        'minify.file.nfs' => 'boolean',
        'minify.file.locking' => 'boolean',
        'minify.memcached.servers' => 'array',
        'minify.memcached.persistant' => 'boolean',
        'minify.rewrite' => 'boolean',
        'minify.options' => 'array',
        'minify.symlinks' => 'array',
        'minify.lifetime' => 'integer',
        'minify.upload' => 'boolean',
        'minify.html.enable' => 'boolean',
        'minify.html.engine' => 'string',
        'minify.html.reject.feed' => 'boolean',
        'minify.html.inline.css' => 'boolean',
        'minify.html.inline.js' => 'boolean',
        'minify.html.strip.crlf' => 'boolean',
        'minify.html.comments.ignore' => 'array',
        'minify.css.enable' => 'boolean',
        'minify.css.engine' => 'string',
        'minify.css.combine' => 'boolean',
        'minify.css.strip.comments' => 'boolean',
        'minify.css.strip.crlf' => 'boolean',
        'minify.css.imports' => 'string',
        'minify.css.groups' => 'array',
        'minify.js.enable' => 'boolean',
        'minify.js.engine' => 'string',
        'minify.js.combine.header' => 'boolean',
        'minify.js.combine.body' => 'boolean',
        'minify.js.combine.footer' => 'boolean',
        'minify.js.strip.comments' => 'boolean',
        'minify.js.strip.crlf' => 'boolean',
        'minify.js.groups' => 'array',
        'minify.yuijs.path.java' => 'string',
        'minify.yuijs.path.jar' => 'string',
        'minify.yuijs.options.line-break' => 'integer',
        'minify.yuijs.options.nomunge' => 'boolean',
        'minify.yuijs.options.preserve-semi' => 'boolean',
        'minify.yuijs.options.disable-optimizations' => 'boolean',
        'minify.yuicss.path.java' => 'string',
        'minify.yuicss.path.jar' => 'string',
        'minify.yuicss.options.line-break' => 'integer',
        'minify.ccjs.path.java' => 'string',
        'minify.ccjs.path.jar' => 'string',
        'minify.ccjs.options.compilation_level' => 'string',
        'minify.ccjs.options.formatting' => 'string',
        'minify.csstidy.options.remove_bslash' => 'boolean',
        'minify.csstidy.options.compress_colors' => 'boolean',
        'minify.csstidy.options.compress_font-weight' => 'boolean',
        'minify.csstidy.options.lowercase_s' => 'boolean',
        'minify.csstidy.options.optimise_shorthands' => 'integer',
        'minify.csstidy.options.remove_last_;' => 'boolean',
        'minify.csstidy.options.case_properties' => 'integer',
        'minify.csstidy.options.sort_properties' => 'boolean',
        'minify.csstidy.options.sort_selectors' => 'boolean',
        'minify.csstidy.options.merge_selectors' => 'integer',
        'minify.csstidy.options.discard_invalid_properties' => 'boolean',
        'minify.csstidy.options.css_level' => 'string',
        'minify.csstidy.options.preserve_css' => 'boolean',
        'minify.csstidy.options.timestamp' => 'boolean',
        'minify.csstidy.options.template' => 'string',
        'minify.htmltidy.options.clean' => 'boolean',
        'minify.htmltidy.options.hide-comments' => 'boolean',
        'minify.htmltidy.options.wrap' => 'integer',
        'minify.reject.logged' => 'boolean',
        'minify.reject.ua' => 'array',
        'minify.reject.uri' => 'array',
        'minify.error.last' => 'string',
        'minify.error.notification' => 'string',
        'minify.error.notification.last' => 'integer',

        'cdn.enabled' => 'boolean',
        'cdn.debug' => 'boolean',
        'cdn.engine' => 'string',
        'cdn.uploads.enable' => 'boolean',
        'cdn.includes.enable' => 'boolean',
        'cdn.includes.files' => 'string',
        'cdn.theme.enable' => 'boolean',
        'cdn.theme.files' => 'string',
        'cdn.minify.enable' => 'boolean',
        'cdn.custom.enable' => 'boolean',
        'cdn.custom.files' => 'array',
        'cdn.import.external' => 'boolean',
        'cdn.import.files' => 'string',
        'cdn.queue.interval' => 'integer',
        'cdn.queue.limit' => 'integer',
        'cdn.force.rewrite' => 'boolean',
        'cdn.autoupload.enabled' => 'boolean',
        'cdn.autoupload.interval' => 'integer',
        'cdn.ftp.host' => 'string',
        'cdn.ftp.port' => 'integer',
        'cdn.ftp.user' => 'string',
        'cdn.ftp.pass' => 'string',
        'cdn.ftp.path' => 'string',
        'cdn.ftp.pasv' => 'boolean',
        'cdn.ftp.domain' => 'array',
        'cdn.ftp.ssl' => 'string',
        'cdn.s3.key' => 'string',
        'cdn.s3.secret' => 'string',
        'cdn.s3.bucket' => 'string',
        'cdn.s3.cname' => 'array',
        'cdn.s3.ssl' => 'string',
        'cdn.cf.key' => 'string',
        'cdn.cf.secret' => 'string',
        'cdn.cf.bucket' => 'string',
        'cdn.cf.id' => 'string',
        'cdn.cf.cname' => 'array',
        'cdn.cf.ssl' => 'string',
        'cdn.cf2.key' => 'string',
        'cdn.cf2.secret' => 'string',
        'cdn.cf2.id' => 'string',
        'cdn.cf2.cname' => 'array',
        'cdn.cf2.ssl' => 'string',
        'cdn.rscf.user' => 'string',
        'cdn.rscf.key' => 'string',
        'cdn.rscf.location' => 'string',
        'cdn.rscf.container' => 'string',
        'cdn.rscf.cname' => 'array',
        'cdn.rscf.ssl' => 'string',
        'cdn.azure.user' => 'string',
        'cdn.azure.key' => 'string',
        'cdn.azure.container' => 'string',
        'cdn.azure.cname' => 'array',
        'cdn.azure.ssl' => 'string',
        'cdn.mirror.domain' => 'array',
        'cdn.mirror.ssl' => 'string',
        'cdn.netdna.apiid' => 'string',
        'cdn.netdna.apikey' => 'string',
        'cdn.netdna.domain' => 'array',
        'cdn.netdna.ssl' => 'string',
        'cdn.cotendo.username' => 'string',
        'cdn.cotendo.password' => 'string',
        'cdn.cotendo.zones' => 'array',
        'cdn.cotendo.domain' => 'array',
        'cdn.cotendo.ssl' => 'string',
        'cdn.edgecast.account' => 'string',
        'cdn.edgecast.token' => 'string',
        'cdn.edgecast.domain' => 'array',
        'cdn.edgecast.ssl' => 'string',
        'cdn.reject.admins' => 'boolean',
        'cdn.reject.ua' => 'array',
        'cdn.reject.uri' => 'array',
        'cdn.reject.files' => 'array',

        'cloudflare.enabled' => 'boolean',
        'cloudflare.email' => 'string',
        'cloudflare.key' => 'string',
        'cloudflare.zone' => 'string',

        'varnish.enabled' => 'boolean',
        'varnish.debug' => 'boolean',
        'varnish.servers' => 'array',

        'browsercache.enabled' => 'boolean',
        'browsercache.no404wp' => 'boolean',
        'browsercache.no404wp.exceptions' => 'array',
        'browsercache.cssjs.compression' => 'boolean',
        'browsercache.cssjs.expires' => 'boolean',
        'browsercache.cssjs.lifetime' => 'integer',
        'browsercache.cssjs.cache.control' => 'boolean',
        'browsercache.cssjs.cache.policy' => 'string',
        'browsercache.cssjs.etag' => 'boolean',
        'browsercache.cssjs.w3tc' => 'boolean',
        'browsercache.cssjs.replace' => 'boolean',
        'browsercache.html.compression' => 'boolean',
        'browsercache.html.expires' => 'boolean',
        'browsercache.html.lifetime' => 'integer',
        'browsercache.html.cache.control' => 'boolean',
        'browsercache.html.cache.policy' => 'string',
        'browsercache.html.etag' => 'boolean',
        'browsercache.html.w3tc' => 'boolean',
        'browsercache.html.replace' => 'boolean',
        'browsercache.other.compression' => 'boolean',
        'browsercache.other.expires' => 'boolean',
        'browsercache.other.lifetime' => 'integer',
        'browsercache.other.cache.control' => 'boolean',
        'browsercache.other.cache.policy' => 'string',
        'browsercache.other.etag' => 'boolean',
        'browsercache.other.w3tc' => 'boolean',
        'browsercache.other.replace' => 'boolean',

        'mobile.enabled' => 'boolean',
        'mobile.rgroups' => 'array',

        'referrer.enabled' => 'boolean',
        'referrer.rgroups' => 'array',

        'common.support' => 'string',
        'common.install' => 'integer',
        'common.tweeted' => 'boolean',

        'config.check' => 'boolean',
        'config.path' => 'string',

        'widget.latest.enabled' => 'boolean',
        'widget.latest.items' => 'integer',
        'widget.pagespeed.enabled' => 'boolean',
        'widget.pagespeed.key' => 'string',

        'notes.wp_content_perms' => 'boolean',
        'notes.php_is_old' => 'boolean',
        'notes.theme_changed' => 'boolean',
        'notes.wp_upgraded' => 'boolean',
        'notes.plugins_updated' => 'boolean',
        'notes.cdn_upload' => 'boolean',
        'notes.cdn_reupload' => 'boolean',
        'notes.need_empty_pgcache' => 'boolean',
        'notes.need_empty_minify' => 'boolean',
        'notes.need_empty_objectcache' => 'boolean',
        'notes.pgcache_rules_core' => 'boolean',
        'notes.pgcache_rules_cache' => 'boolean',
        'notes.pgcache_rules_legacy' => 'boolean',
        'notes.pgcache_rules_wpsc' => 'boolean',
        'notes.minify_rules_core' => 'boolean',
        'notes.minify_rules_cache' => 'boolean',
        'notes.minify_rules_legacy' => 'boolean',
        'notes.support_us' => 'boolean',
        'notes.no_curl' => 'boolean',
        'notes.no_zlib' => 'boolean',
        'notes.zlib_output_compression' => 'boolean',
        'notes.no_permalink_rules' => 'boolean',
        'notes.browsercache_rules_cache' => 'boolean',
        'notes.browsercache_rules_no404wp' => 'boolean',
        'notes.minify_error' => 'boolean',

        'timelimit.email_send' => 'integer',
        'timelimit.varnish_purge' => 'integer',
        'timelimit.cache_flush' => 'integer',
        'timelimit.cache_gc' => 'integer',
        'timelimit.cdn_upload' => 'integer',
        'timelimit.cdn_delete' => 'integer',
        'timelimit.cdn_purge' => 'integer',
        'timelimit.cdn_import' => 'integer',
        'timelimit.cdn_test' => 'integer',
        'timelimit.cdn_container_create' => 'integer',
        'timelimit.cloudflare_api_request' => 'integer',
        'timelimit.domain_rename' => 'integer',
        'timelimit.minify_recommendations' => 'integer'
    );

    var $_defaults = array(
        'dbcache.enabled' => false,
        'dbcache.debug' => false,
        'dbcache.engine' => 'file',
        'dbcache.file.gc' => 3600,
        'dbcache.file.locking' => false,
        'dbcache.memcached.servers' => array(
            '127.0.0.1:11211'
        ),
        'dbcache.memcached.persistant' => true,
        'dbcache.reject.logged' => true,
        'dbcache.reject.uri' => array(),
        'dbcache.reject.cookie' => array(),
        'dbcache.reject.sql' => array(
            'gdsr_',
            'wp_rg_'
        ),
        'dbcache.lifetime' => 180,

        'objectcache.enabled' => false,
        'objectcache.debug' => false,
        'objectcache.engine' => 'file',
        'objectcache.file.gc' => 3600,
        'objectcache.file.locking' => false,
        'objectcache.memcached.servers' => array(
            '127.0.0.1:11211'
        ),
        'objectcache.memcached.persistant' => true,
        'objectcache.groups.global' => array(
            'users',
            'userlogins',
            'usermeta',
            'user_meta',
            'site-transient',
            'site-options',
            'site-lookup',
            'blog-lookup',
            'blog-details',
            'rss',
            'global-posts'
        ),
        'objectcache.groups.nonpersistent' => array(
            'comment',
            'counts',
            'plugins'
        ),
        'objectcache.lifetime' => 180,

        'pgcache.enabled' => false,
        'pgcache.debug' => false,
        'pgcache.engine' => 'file_generic',
        'pgcache.file.gc' => 3600,
        'pgcache.file.nfs' => false,
        'pgcache.file.locking' => false,
        'pgcache.memcached.servers' => array(
            '127.0.0.1:11211'
        ),
        'pgcache.memcached.persistant' => true,
        'pgcache.check.domain' => true,
        'pgcache.cache.query' => true,
        'pgcache.cache.home' => true,
        'pgcache.cache.feed' => false,
        'pgcache.cache.ssl' => true,
        'pgcache.cache.404' => false,
        'pgcache.cache.flush' => false,
        'pgcache.cache.headers' => array(
            'Last-Modified',
            'Content-Type',
            'X-Pingback',
            'P3P'
        ),
        'pgcache.accept.uri' => array(
            'sitemap(_index)?\.xml(\.gz)?',
            '[a-z0-9_\-]+-sitemap([0-9]+)?\.xml(\.gz)?'
        ),
        'pgcache.accept.files' => array(
            'wp-comments-popup.php',
            'wp-links-opml.php',
            'wp-locations.php'
        ),
        'pgcache.reject.logged' => true,
        'pgcache.reject.uri' => array(
            'wp-.*\.php',
            'index\.php'
        ),
        'pgcache.reject.ua' => array(),
        'pgcache.reject.cookie' => array(),
        'pgcache.purge.home' => true,
        'pgcache.purge.post' => true,
        'pgcache.purge.comments' => false,
        'pgcache.purge.author' => false,
        'pgcache.purge.terms' => false,
        'pgcache.purge.archive.daily' => false,
        'pgcache.purge.archive.monthly' => false,
        'pgcache.purge.archive.yearly' => false,
        'pgcache.purge.feed.blog' => true,
        'pgcache.purge.feed.comments' => false,
        'pgcache.purge.feed.author' => false,
        'pgcache.purge.feed.terms' => false,
        'pgcache.purge.feed.types' => array(
            'rss2'
        ),
        'pgcache.prime.enabled' => false,
        'pgcache.prime.interval' => 900,
        'pgcache.prime.limit' => 10,
        'pgcache.prime.sitemap' => '',

        'minify.enabled' => false,
        'minify.auto' => false,
        'minify.debug' => false,
        'minify.engine' => 'file',
        'minify.file.gc' => 86400,
        'minify.file.nfs' => false,
        'minify.file.locking' => false,
        'minify.memcached.servers' => array(
            '127.0.0.1:11211'
        ),
        'minify.memcached.persistant' => true,
        'minify.rewrite' => true,
        'minify.options' => array(),
        'minify.symlinks' => array(),
        'minify.lifetime' => 86400,
        'minify.upload' => true,
        'minify.html.enable' => false,
        'minify.html.engine' => 'html',
        'minify.html.reject.feed' => false,
        'minify.html.inline.css' => false,
        'minify.html.inline.js' => false,
        'minify.html.strip.crlf' => false,
        'minify.html.comments.ignore' => array(
            'google_ad_',
            'RSPEAK_'
        ),
        'minify.css.enable' => true,
        'minify.css.engine' => 'css',
        'minify.css.combine' => false,
        'minify.css.strip.comments' => false,
        'minify.css.strip.crlf' => false,
        'minify.css.imports' => 'process',
        'minify.css.groups' => array(),
        'minify.js.enable' => true,
        'minify.js.engine' => 'js',
        'minify.js.combine.header' => false,
        'minify.js.combine.body' => false,
        'minify.js.combine.footer' => false,
        'minify.js.strip.comments' => false,
        'minify.js.strip.crlf' => false,
        'minify.js.groups' => array(),
        'minify.yuijs.path.java' => 'java',
        'minify.yuijs.path.jar' => 'yuicompressor.jar',
        'minify.yuijs.options.line-break' => 5000,
        'minify.yuijs.options.nomunge' => false,
        'minify.yuijs.options.preserve-semi' => false,
        'minify.yuijs.options.disable-optimizations' => false,
        'minify.yuicss.path.java' => 'java',
        'minify.yuicss.path.jar' => 'yuicompressor.jar',
        'minify.yuicss.options.line-break' => 5000,
        'minify.ccjs.path.java' => 'java',
        'minify.ccjs.path.jar' => 'compiler.jar',
        'minify.ccjs.options.compilation_level' => 'SIMPLE_OPTIMIZATIONS',
        'minify.ccjs.options.formatting' => '',
        'minify.csstidy.options.remove_bslash' => true,
        'minify.csstidy.options.compress_colors' => true,
        'minify.csstidy.options.compress_font-weight' => true,
        'minify.csstidy.options.lowercase_s' => false,
        'minify.csstidy.options.optimise_shorthands' => 1,
        'minify.csstidy.options.remove_last_;' => false,
        'minify.csstidy.options.case_properties' => 1,
        'minify.csstidy.options.sort_properties' => false,
        'minify.csstidy.options.sort_selectors' => false,
        'minify.csstidy.options.merge_selectors' => 2,
        'minify.csstidy.options.discard_invalid_properties' => false,
        'minify.csstidy.options.css_level' => 'CSS2.1',
        'minify.csstidy.options.preserve_css' => false,
        'minify.csstidy.options.timestamp' => false,
        'minify.csstidy.options.template' => 'default',
        'minify.htmltidy.options.clean' => false,
        'minify.htmltidy.options.hide-comments' => true,
        'minify.htmltidy.options.wrap' => 0,
        'minify.reject.logged' => false,
        'minify.reject.ua' => array(),
        'minify.reject.uri' => array(),
        'minify.error.last' => '',
        'minify.error.notification' => '',
        'minify.error.notification.last' => 0,

        'cdn.enabled' => false,
        'cdn.debug' => false,
        'cdn.engine' => 'ftp',
        'cdn.uploads.enable' => true,
        'cdn.includes.enable' => true,
        'cdn.includes.files' => '*.css;*.js;*.gif;*.png;*.jpg',
        'cdn.theme.enable' => true,
        'cdn.theme.files' => '*.css;*.js;*.gif;*.png;*.jpg;*.ico;*.ttf;*.otf,*.woff',
        'cdn.minify.enable' => true,
        'cdn.custom.enable' => true,
        'cdn.custom.files' => array(
            'favicon.ico',
            'wp-content/gallery/*',
            'wp-content/uploads/avatars/*',
            'wp-content/plugins/wordpress-seo/css/xml-sitemap.xsl'
        ),
        'cdn.import.external' => false,
        'cdn.import.files' => '*.jpg;*.png;*.gif;*.avi;*.wmv;*.mpg;*.wav;*.mp3;*.txt;*.rtf;*.doc;*.xls;*.rar;*.zip;*.tar;*.gz;*.exe',
        'cdn.queue.interval' => 900,
        'cdn.queue.limit' => 25,
        'cdn.force.rewrite' => false,
        'cdn.autoupload.enabled' => false,
        'cdn.autoupload.interval' => 3600,
        'cdn.ftp.host' => '',
        'cdn.ftp.port' => 21,
        'cdn.ftp.user' => '',
        'cdn.ftp.pass' => '',
        'cdn.ftp.path' => '',
        'cdn.ftp.pasv' => false,
        'cdn.ftp.domain' => array(),
        'cdn.ftp.ssl' => 'auto',
        'cdn.s3.key' => '',
        'cdn.s3.secret' => '',
        'cdn.s3.bucket' => '',
        'cdn.s3.cname' => array(),
        'cdn.s3.ssl' => 'auto',
        'cdn.cf.key' => '',
        'cdn.cf.secret' => '',
        'cdn.cf.bucket' => '',
        'cdn.cf.id' => '',
        'cdn.cf.cname' => array(),
        'cdn.cf.ssl' => 'auto',
        'cdn.cf2.key' => '',
        'cdn.cf2.secret' => '',
        'cdn.cf2.id' => '',
        'cdn.cf2.cname' => array(),
        'cdn.cf2.ssl' => 'auto',
        'cdn.rscf.user' => '',
        'cdn.rscf.key' => '',
        'cdn.rscf.location' => 'us',
        'cdn.rscf.container' => '',
        'cdn.rscf.cname' => array(),
        'cdn.rscf.ssl' => 'auto',
        'cdn.azure.user' => '',
        'cdn.azure.key' => '',
        'cdn.azure.container' => '',
        'cdn.azure.cname' => array(),
        'cdn.azure.ssl' => 'auto',
        'cdn.mirror.domain' => array(),
        'cdn.mirror.ssl' => 'auto',
        'cdn.netdna.apiid' => '',
        'cdn.netdna.apikey' => '',
        'cdn.netdna.domain' => array(),
        'cdn.netdna.ssl' => 'auto',
        'cdn.cotendo.username' => '',
        'cdn.cotendo.password' => '',
        'cdn.cotendo.zones' => array(),
        'cdn.cotendo.domain' => array(),
        'cdn.cotendo.ssl' => 'auto',
        'cdn.edgecast.account' => '',
        'cdn.edgecast.token' => '',
        'cdn.edgecast.domain' => array(),
        'cdn.edgecast.ssl' => 'auto',
        'cdn.reject.admins' => false,
        'cdn.reject.ua' => array(),
        'cdn.reject.uri' => array(),
        'cdn.reject.files' => array(
            'wp-content/uploads/wpcf7_captcha/*',
            'wp-content/uploads/imagerotator.swf'
        ),

        'cloudflare.enabled' => false,
        'cloudflare.email' => '',
        'cloudflare.key' => '',
        'cloudflare.zone' => '',

        'varnish.enabled' => false,
        'varnish.debug' => false,
        'varnish.servers' => array(),

        'browsercache.enabled' => true,
        'browsercache.no404wp' => false,
        'browsercache.no404wp.exceptions' => array(
            'robots\.txt',
            'sitemap(_index)?\.xml(\.gz)?',
            '[a-z0-9_\-]+-sitemap([0-9]+)?\.xml(\.gz)?'
        ),
        'browsercache.cssjs.compression' => true,
        'browsercache.cssjs.expires' => false,
        'browsercache.cssjs.lifetime' => 31536000,
        'browsercache.cssjs.cache.control' => false,
        'browsercache.cssjs.cache.policy' => 'cache_maxage',
        'browsercache.cssjs.etag' => false,
        'browsercache.cssjs.w3tc' => true,
        'browsercache.cssjs.replace' => false,
        'browsercache.html.compression' => true,
        'browsercache.html.expires' => false,
        'browsercache.html.lifetime' => 3600,
        'browsercache.html.cache.control' => false,
        'browsercache.html.cache.policy' => 'cache_maxage',
        'browsercache.html.etag' => false,
        'browsercache.html.w3tc' => true,
        'browsercache.html.replace' => false,
        'browsercache.other.compression' => true,
        'browsercache.other.expires' => false,
        'browsercache.other.lifetime' => 31536000,
        'browsercache.other.cache.control' => false,
        'browsercache.other.cache.policy' => 'cache_maxage',
        'browsercache.other.etag' => false,
        'browsercache.other.w3tc' => true,
        'browsercache.other.replace' => false,

        'mobile.enabled' => true,
        'mobile.rgroups' => array(
            'high' => array(
                'theme' => '',
                'enabled' => false,
                'redirect' => '',
                'agents' => array(
                    'acer\ s100',
                    'android',
                    'archos5',
                    'blackberry9500',
                    'blackberry9530',
                    'blackberry9550',
                    'blackberry\ 9800',
                    'cupcake',
                    'docomo\ ht\-03a',
                    'dream',
                    'htc\ hero',
                    'htc\ magic',
                    'htc_dream',
                    'htc_magic',
                    'incognito',
                    'ipad',
                    'iphone',
                    'ipod',
                    'kindle',
                    'lg\-gw620',
                    'liquid\ build',
                    'maemo',
                    'mot\-mb200',
                    'mot\-mb300',
                    'nexus\ one',
                    'opera\ mini',
                    'samsung\-s8000',
                    'series60.*webkit',
                    'series60/5\.0',
                    'sonyericssone10',
                    'sonyericssonu20',
                    'sonyericssonx10',
                    't\-mobile\ mytouch\ 3g',
                    't\-mobile\ opal',
                    'tattoo',
                    'webmate',
                    'webos'
                )
            ),
            'low' => array(
                'theme' => '',
                'enabled' => false,
                'redirect' => '',
                'agents' => array(
                    '2\.0\ mmp',
                    '240x320',
                    'alcatel',
                    'amoi',
                    'asus',
                    'au\-mic',
                    'audiovox',
                    'avantgo',
                    'benq',
                    'bird',
                    'blackberry',
                    'blazer',
                    'cdm',
                    'cellphone',
                    'danger',
                    'ddipocket',
                    'docomo',
                    'dopod',
                    'elaine/3\.0',
                    'ericsson',
                    'eudoraweb',
                    'fly',
                    'haier',
                    'hiptop',
                    'hp\.ipaq',
                    'htc',
                    'huawei',
                    'i\-mobile',
                    'iemobile',
                    'j\-phone',
                    'kddi',
                    'konka',
                    'kwc',
                    'kyocera/wx310k',
                    'lenovo',
                    'lg',
                    'lg/u990',
                    'lge\ vx',
                    'midp',
                    'midp\-2\.0',
                    'mmef20',
                    'mmp',
                    'mobilephone',
                    'mot\-v',
                    'motorola',
                    'netfront',
                    'newgen',
                    'newt',
                    'nintendo\ ds',
                    'nintendo\ wii',
                    'nitro',
                    'nokia',
                    'novarra',
                    'o2',
                    'openweb',
                    'opera\ mobi',
                    'opera\.mobi',
                    'palm',
                    'panasonic',
                    'pantech',
                    'pdxgw',
                    'pg',
                    'philips',
                    'phone',
                    'playstation\ portable',
                    'portalmmm',
                    '\bppc\b',
                    'proxinet',
                    'psp',
                    'qtek',
                    'sagem',
                    'samsung',
                    'sanyo',
                    'sch',
                    'sec',
                    'sendo',
                    'sgh',
                    'sharp',
                    'sharp\-tq\-gx10',
                    'small',
                    'smartphone',
                    'softbank',
                    'sonyericsson',
                    'sph',
                    'symbian',
                    'symbian\ os',
                    'symbianos',
                    'toshiba',
                    'treo',
                    'ts21i\-10',
                    'up\.browser',
                    'up\.link',
                    'uts',
                    'vertu',
                    'vodafone',
                    'wap',
                    'willcome',
                    'windows\ ce',
                    'windows\.ce',
                    'winwap',
                    'xda',
                    'zte'
                )
            )
        ),

        'referrer.enabled' => true,
        'referrer.rgroups' => array(
            'search_engines' => array(
                'theme' => '',
                'enabled' => false,
                'redirect' => '',
                'referrers' => array(
                    'google\.com',
                    'yahoo\.com',
                    'bing\.com',
                    'ask\.com',
                    'msn\.com'
                )
            )
        ),

        'common.support' => '',
        'common.install' => 0,
        'common.tweeted' => false,

        'config.check' => true,
        'config.path' => '',

        'widget.latest.enabled' => true,
        'widget.latest.items' => 3,
        'widget.pagespeed.enabled' => true,
        'widget.pagespeed.key' => '',

        'notes.wp_content_perms' => true,
        'notes.php_is_old' => true,
        'notes.theme_changed' => false,
        'notes.wp_upgraded' => false,
        'notes.plugins_updated' => false,
        'notes.cdn_upload' => false,
        'notes.cdn_reupload' => false,
        'notes.need_empty_pgcache' => false,
        'notes.need_empty_minify' => false,
        'notes.need_empty_objectcache' => false,
        'notes.pgcache_rules_core' => true,
        'notes.pgcache_rules_cache' => true,
        'notes.pgcache_rules_legacy' => true,
        'notes.pgcache_rules_wpsc' => true,
        'notes.minify_rules_core' => true,
        'notes.minify_rules_cache' => true,
        'notes.minify_rules_legacy' => true,
        'notes.support_us' => true,
        'notes.no_curl' => true,
        'notes.no_zlib' => true,
        'notes.zlib_output_compression' => true,
        'notes.no_permalink_rules' => true,
        'notes.browsercache_rules_cache' => true,
        'notes.browsercache_rules_no404wp' => true,
        'notes.minify_error' => false,

        'timelimit.email_send' => 180,
        'timelimit.varnish_purge' => 300,
        'timelimit.cache_flush' => 600,
        'timelimit.cache_gc' => 600,
        'timelimit.cdn_upload' => 600,
        'timelimit.cdn_delete' => 300,
        'timelimit.cdn_purge' => 300,
        'timelimit.cdn_import' => 600,
        'timelimit.cdn_test' => 300,
        'timelimit.cdn_container_create' => 300,
        'timelimit.cloudflare_api_request' => 180,
        'timelimit.domain_rename' => 120,
        'timelimit.minify_recommendations' => 600
    );

    /**
     * PHP5 Constructor
     * @param boolean $preview
     */
    function __construct($preview = null) {
        $this->load_defaults();
        $this->load($preview);

        if (!$this->get_integer('common.install')) {
            $this->set('common.install', time());
        }
    }

    /**
     * PHP4 Constructor
     * @param boolean $preview
     */
    function W3_Config($preview = null) {
        $this->__construct($preview);
    }

    /**
     * Returns config value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get($key, $default = null) {
        if (array_key_exists($key, $this->_keys) && array_key_exists($key, $this->_config)) {
            $value = $this->_config[$key];
        } else {
            if ($default === null && array_key_exists($key, $this->_defaults)) {
                $value = $this->_defaults[$key];
            } else {
                $value = $default;
            }
        }

        switch ($key) {
            /**
             * Check cache engines
             */
            case 'pgcache.engine':
            case 'dbcache.engine':
            case 'minify.engine':
            case 'objectcache.engine':
                /**
                 * Legacy support
                 */
                if ($value == 'file_pgcache') {
                    $value = 'file_generic';
                }

                switch (true) {
                    case ($value == 'file_generic' && !w3_can_check_rules()):
                    case ($value == 'apc' && !function_exists('apc_store')):
                    case ($value == 'eaccelerator' && !function_exists('eaccelerator_put')):
                    case ($value == 'xcache' && !function_exists('xcache_set')):
                    case ($value == 'wincache' && !function_exists('wincache_ucache_set')):
                    case ($value == 'memcached' && !class_exists('Memcache')):
                        return 'file';
                }
                break;

            /**
             * Check HTML minifier
             */
            case 'minify.html.engine':
                if ($value == 'htmltidy' && !class_exists('tidy')) {
                    return 'html';
                }
                break;

            /**
             * Disabled some page cache options when enhanced mode enabled
             */
            case 'pgcache.cache.query':
                if ($this->get_string('pgcache.engine') == 'file_generic') {
                    return false;
                }
                break;

            /**
             * Set default value to sitemap file
             */
            case 'pgcache.prime.sitemap':
                if (!$value) {
                    $value = w3_get_home_url() . '/sitemap.xml';
                }
                break;

            /**
             * Disabled minify when PHP5 is not installed
             */
            case 'minify.enabled':
                if (!W3TC_PHP5) {
                    return false;
                }
                break;

            /**
             * Disable minify rewrite when server rules are not supported
             */
            case 'minify.rewrite':
                if (!w3_can_check_rules()) {
                    return false;
                }
                break;

            /**
             * Disable minify options for auto mode
             */
            case 'minify.upload':
                if ($this->get_boolean('minify.auto')) {
                    return false;
                }
                break;

            /**
             * Minify groups legacy support
             */
            case 'minify.js.groups':
            case 'minify.css.groups':
                /**
                 * Support <= 0.8.5.2 versions
                 */
                if (is_array($value) && count($value)) {
                    $group = current($value);

                    if (is_array($group) && count($group)) {
                        $location = current($group);

                        if (isset($location['files'])) {
                            if (function_exists('get_theme')) {
                                $theme = get_theme(get_current_theme());
                                $theme_key = w3_get_theme_key($theme['Theme Root'], $theme['Template'], $theme['Stylesheet']);

                                $value = array(
                                    $theme_key => $value
                                );

                                return $value;
                            }
                        }
                    }
                }

                /**
                 * Support <= 0.9.1.3 versions
                 */
                $wp_themes = get_themes();

                foreach ($value as $theme_key => $templates) {
                    if (strlen($theme_key) == 5) {
                        break;
                    }

                    foreach ($wp_themes as $wp_theme) {
                        $wp_theme_key = w3_get_theme_key_legacy($wp_theme['Theme Root'], $wp_theme['Template'], $wp_theme['Stylesheet']);

                        if ($theme_key == $wp_theme_key) {
                            $new_theme_key = w3_get_theme_key($wp_theme['Theme Root'], $wp_theme['Template'], $wp_theme['Stylesheet']);

                            $value[$new_theme_key] = $templates;
                            unset($theme_key);
                            break;
                        }
                    }
                }
                break;

            /**
             * Disable CDN minify when PHP5 is not installed or minify is disabled
             */
            case 'cdn.minify.enable':
                if (!W3TC_PHP5 || !$this->get_boolean('minify.rewrite') || ($this->get_boolean('minify.auto') && !w3_is_cdn_mirror($this->get_string('cdn.engine')))) {
                    return false;
                }
                break;

            /**
             * Check CDN engines
             */
            case 'cdn.engine':
                /**
                 * Check for PHP5
                 */
                if (in_array($value, array('rscf', 'azure')) && !W3TC_PHP5) {
                    return 'mirror';
                }

                /**
                 * Check for CURL
                 */
                if (in_array($value, array('s3', 'cf', 'cf2', 'rscf')) && !function_exists('curl_init')) {
                    return 'mirror';
                }

                /**
                 * Check for FTP
                 */
                if ($value == 'ftp' && !function_exists('ftp_connect')) {
                    return 'mirror';
                }

                /**
                 * Check for SHA 256 functions
                 */
                if ($value == 'netdna' && !function_exists('hash') && !function_exists('mhash')) {
                    return 'mirror';
                }
                break;

            /**
             * Disable no 404 wp feature when server rules are not supported
             */
            case 'browsercache.no404wp':
                if (!w3_can_check_rules()) {
                    return false;
                }
                break;
        }

        return $value;
    }

    /**
     * Returns string value
     *
     * @param string $key
     * @param string $default
     * @param boolean $trim
     * @return string
     */
    function get_string($key, $default = '', $trim = true) {
        $value = (string) $this->get($key, $default);

        return ($trim ? trim($value) : $value);
    }

    /**
     * Returns integer value
     *
     * @param string $key
     * @param integer $default
     * @return integer
     */
    function get_integer($key, $default = 0) {
        return (integer) $this->get($key, $default);
    }

    /**
     * Returns boolean value
     *
     * @param string $key
     * @param boolean $default
     * @return boolean
     */
    function get_boolean($key, $default = false) {
        return (boolean) $this->get($key, $default);
    }

    /**
     * Returns array value
     *
     * @param string $key
     * @param array $default
     * @return array
     */
    function get_array($key, $default = array()) {
        return (array) $this->get($key, $default);
    }

    /**
     * Sets config value
     *
     * @param string $key
     * @param string $value
     */
    function set($key, $value) {
        /**
         * Legacy support for <= 0.9.1.3
         */
        switch ($key) {
            case 'pgcache.varnish.enabled':
                $key = 'varnish.enabled';
                break;

            case 'pgcache.varnish.servers':
                $key = 'varnish.servers';
                break;
        }

        if (array_key_exists($key, $this->_keys)) {
            $type = $this->_keys[$key];
            settype($value, $type);
            $this->_config[$key] = $value;
        }

        return false;
    }

    /**
     * Flush config
     */
    function flush() {
        $this->_config = array();
    }

    /**
     * Reads config from file
     *
     * @param string $file
     * @return array
     */
    function read($file) {
        if (file_exists($file) && is_readable($file)) {
            $config = @include $file;

            if (!is_array($config)) {
                return false;
            }

            foreach ($config as $key => $value) {
                $this->set($key, $value);
            }

            return true;
        }

        return false;
    }

    /**
     * Reads config from request
     */
    function read_request() {
        require_once W3TC_LIB_W3_DIR . '/Request.php';

        $request = W3_Request::get_request();

        foreach ($this->_keys as $key => $type) {
            $request_key = str_replace('.', '_', $key);

            if (!isset($request[$request_key])) {
                continue;
            }

            switch ($type) {
                case 'string':
                    $this->set($key, W3_Request::get_string($request_key));
                    break;

                case 'int':
                case 'integer':
                    $this->set($key, W3_Request::get_integer($request_key));
                    break;

                case 'float':
                case 'double':
                    $this->set($key, W3_Request::get_double($request_key));
                    break;

                case 'bool':
                case 'boolean':
                    $this->set($key, W3_Request::get_boolean($request_key));
                    break;

                case 'array':
                    $this->set($key, W3_Request::get_array($request_key));
                    break;
            }
        }
    }

    /**
     * Writes config
     *
     * @param string $file
     * @return boolean
     */
    function write($file) {
        $fp = @fopen($file, 'w');

        if ($fp) {
            @fputs($fp, "<?php\r\n\r\nreturn array(\r\n");

            $this->_tabs = 1;

            foreach ($this->_config as $key => $value) {
                $this->_write($fp, $key, $value);
            }

            @fputs($fp, ");");
            @fclose($fp);

            return true;
        }

        return false;
    }

    /**
     * Writes config pair
     *
     * @param resource $fp
     * @param string $key
     * @param mixed $value
     */
    function _write($fp, $key, $value) {
        @fputs($fp, str_repeat("\t", $this->_tabs));

        if (is_numeric($key) && (string) (int) $key === (string) $key) {
            @fputs($fp, sprintf("%d => ", $key));
        } else {
            @fputs($fp, sprintf("'%s' => ", addcslashes($key, "'\\")));
        }

        switch (gettype($value)) {
            case 'object':
            case 'array':
                @fputs($fp, "array(\r\n");
                ++$this->_tabs;
                foreach ((array) $value as $k => $v) {
                    $this->_write($fp, $k, $v);
                }
                --$this->_tabs;
                @fputs($fp, sprintf("%s),\r\n", str_repeat("\t", $this->_tabs)));
                return;

            case 'integer':
                $data = (string) $value;
                break;

            case 'double':
                $data = (string) $value;
                break;

            case 'boolean':
                $data = ($value ? 'true' : 'false');
                break;

            case 'NULL':
                $data = 'null';
                break;

            default:
            case 'string':
                $data = "'" . addcslashes($value, "'\\") . "'";
                break;
        }

        @fputs($fp, $data . ",\r\n");
    }

    /**
     * Loads config
     *
     * @param boolean $preview
     * @return boolean
     */
    function load($preview = null) {
        if ($preview === null) {
            $preview = w3_is_preview_mode();
        }

        if ($preview) {
            return $this->read(W3TC_CONFIG_PREVIEW_PATH);
        }

        return $this->read(W3TC_CONFIG_PATH);
    }

    /**
     * Loads master config (for WPMU)
     */
    function load_master() {
        return $this->read(W3TC_CONFIG_MASTER_PATH);
    }

    /**
     * Loads config dfefaults
     */
    function load_defaults() {
        foreach ($this->_defaults as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Set default option on plugin activate
     */
    function set_defaults() {
        $this->set('pgcache.enabled', true);
        $this->set('pgcache.cache.ssl', false);
        $this->set('minify.auto', true);
        $this->set('browsercache.enabled', true);
    }

    /**
     * Saves config
     *
     * @param boolean preview
     * @return boolean
     */
    function save($preview = null) {
        if ($preview === null) {
            $preview = w3_is_preview_mode();
        }

        if ($preview) {
            return $this->write(W3TC_CONFIG_PREVIEW_PATH);
        }

        return $this->write(W3TC_CONFIG_PATH);
    }
}
