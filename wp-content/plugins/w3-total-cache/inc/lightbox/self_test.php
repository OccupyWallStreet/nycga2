<?php 

if (!defined('W3TC')) 
    die();

require_once W3TC_INC_DIR . '/functions/file.php';
require_once W3TC_INC_DIR . '/functions/rule.php';
 
?>
<h3>Compatibility Test</h3>

<fieldset>
    <legend>Legend</legend>

    <p>
        <code>Installed</code>: Functionality will work properly<br />
        <code>Not detected</code>: May be installed, but cannot be automatically confirmed<br />
        <code>Ok</code>: Current value is acceptable.<br />
        <code>Yes/No</code>: The value was successful detected.
    </p>
</fieldset>

<div id="w3tc-self-test">
    <h4 style="margin-top: 0;">Server Modules &amp; Resources:</h4>

    <ul>
        <li>
            Plugin Version: <code><?php echo W3TC_VERSION; ?></code>
        </li>

        <li>
            PHP Version:
            <?php if (PHP_VERSION >= 5): ?>
            <code><?php echo PHP_VERSION; ?></code>
            <?php else: ?>
            <code><?php echo PHP_VERSION; ?></code>;
            <?php endif; ?>
            <span class="w3tc-self-test-hint">(PHP5 required for Minify, Rackspace CloudFiles, Microsoft Azure support)</span>
        </li>

        <li>
            Web Server:
            <?php if (stristr($_SERVER['SERVER_SOFTWARE'], 'apache') !== false): ?>
            <code>Apache</code>
            <?php elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false): ?>
            <code>Lite Speed</code>
            <?php elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false): ?>
            <code>nginx</code>
            <?php elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'lighttpd') !== false): ?>
            <code>lighttpd</code>
            <?php elseif (stristr($_SERVER['SERVER_SOFTWARE'], 'iis') !== false): ?>
            <code>Microsoft IIS</code>
            <?php else: ?>
            <code>Not detected</code>
            <?php endif; ?>
        </li>

        <li>
            FTP functions:
            <?php if (function_exists('ftp_connect')): ?>
            <code>Installed</code>
            <?php else: ?>
            <code>Not installed</code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint">(required for Self-hosted (FTP) CDN support)</span>
        </li>

        <li>
            Multibyte String support:
            <?php if (function_exists('mb_substr')): ?>
            <code>Installed</code>
            <?php else: ?>
            <code>Not installed</code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint">(required for Rackspace Cloud Files support)</span>
        </li>

        <li>
            cURL extension:
            <?php if (function_exists('curl_init')): ?>
            <code>Installed</code>
            <?php else: ?>
            <code>Not installed</code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint">(required for Amazon S3, Amazon CloudFront, Rackspace CloudFiles support)</span>
        </li>

        <li>
            zlib extension:
            <?php if (function_exists('gzencode')): ?>
            <code>Installed</code>
            <?php else: ?>
            <code>Not installed</code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint">(required for compression support)</span>
        </li>

        <li>
            Opcode cache:
            <?php if (function_exists('apc_store')): ?>
            <code>Installed (APC)</code>
            <?php elseif (function_exists('eaccelerator_put')): ?>
            <code>Installed (eAccelerator)</code>
            <?php elseif (function_exists('xcache_set')): ?>
            <code>Installed (XCache)</code>
            <?php elseif (PHP_VERSION >= 6): ?>
            <code>PHP6</code>
            <?php else: ?>
            <code>Not installed</code>
            <?php endif; ?>
        </li>

        <li>
            Memcache extension:
            <?php if (class_exists('Memcache')): ?>
            <code>Installed</code>
            <?php else: ?>
            <code>Not installed</code>
            <?php endif; ?>
        </li>

        <li>
            HTML Tidy extension:
            <?php if (class_exists('tidy')): ?>
            <code>Installed</code>
            <?php else: ?>
            <code>Not installed</code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint">(required for HTML Tidy minifier suppport)</span>
        </li>

        <li>
            Mime type detection:
            <?php if (function_exists('finfo_open')): ?>
            <code>Installed (Fileinfo)</code>
            <?php elseif (function_exists('mime_content_type')): ?>
            <code>Installed (mime_content_type)</code>
            <?php else:  ?>
            <code>Not installed</code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint">(required for CDN support)</span>
        </li>

        <li>
            Hash function:
            <?php if (function_exists('hash')): ?>
            <code>Installed (hash)</code>
            <?php elseif (function_exists('mhash')): ?>
            <code>Installed (mhash)</code>
            <?php else: ?>
            <code>Not installed</code>
            <?php endif; ?>
            <span class="w3tc-self-test-hint">(required for NetDNA purge support)</span>
        </li>

        <li>
            Safe mode:
            <?php if (w3_to_boolean(ini_get('safe_mode'))): ?>
            <code>On</code>
            <?php else: ?>
            <code>Off</code>
            <?php endif; ?>
        </li>

        <li>
            Open basedir:
            <?php $open_basedir = ini_get('open_basedir'); if ($open_basedir): ?>
            <code>On: <?php echo htmlspecialchars($open_basedir); ?></code>
            <?php else: ?>
            <code>Off</code>
            <?php endif; ?>
        </li>

        <li>
            zlib output compression:
            <?php if (w3_to_boolean(ini_get('zlib.output_compression'))): ?>
            <code>On</code>
            <?php else: ?>
            <code>Off</code>
            <?php endif; ?>
        </li>

        <li>
            set_time_limit:
            <?php if (function_exists('set_time_limit')): ?>
            <code>Available</code>
            <?php else: ?>
            <code>Not available</code>
            <?php endif; ?>
        </li>

        <?php
        if (w3_is_apache()):
            $apache_modules = (function_exists('apache_get_modules') ? apache_get_modules() : false);

            $modules = array(
                'mod_deflate',
                'mod_env',
                'mod_expires',
                'mod_headers',
                'mod_mime',
                'mod_rewrite',
                'mod_setenvif'
            );
        ?>
            <?php foreach ($modules as $module): ?>
                <li>
                    <?php echo $module; ?>:
                    <?php if ($apache_modules): ?>
                        <?php if (in_array($module, $apache_modules)): ?>
                        <code>Installed</code>
                        <?php else: ?>
                        <code>Not installed</code>
                        <?php endif; ?>
                    <?php else: ?>
                    <code>Not detected</code>
                    <?php endif; ?>
                    <span class="w3tc-self-test-hint">(required for Page Cache (enhanced mode) and Browser Cache)</span>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <h4>WordPress Resources</h4>

    <ul>
        <?php
        $paths = array_unique(array(
            w3_get_pgcache_rules_core_path(),
            w3_get_browsercache_rules_cache_path(),
            w3_get_browsercache_rules_no404wp_path()
        ));
        ?>
        <?php foreach ($paths as $path): if ($path): ?>
        <li>
            <?php echo htmlspecialchars($path); ?>:
            <?php if (file_exists($path)): ?>
                <?php if (w3_is_writable($path)): ?>
                <code>OK</code>
                <?php else: ?>
                <code>Not write-able</code>
                <?php endif; ?>
            <?php else: ?>
                <?php if (w3_is_writable_dir(dirname($path))): ?>
                <code>Write-able</code>
                <?php else: ?>
                <code>Not write-able</code>
                <?php endif; ?>
            <?php endif; ?>
        </li>
        <?php endif; endforeach; ?>

        <li>
            <?php echo w3_path(WP_CONTENT_DIR); ?>:
            <?php if (w3_is_writable_dir(WP_CONTENT_DIR)): ?>
            <code>OK</code>
            <?php else: ?>
            <code>Not write-able</code>
            <?php endif; ?>
        </li>

        <li>
            <?php $uploads_dir = @wp_upload_dir(); ?>
            <?php echo htmlspecialchars($uploads_dir['path']); ?>:
            <?php if (!empty($uploads_dir['error'])): ?>
            <code>Error: <?php echo htmlspecialchars($uploads_dir['error']); ?></code>
            <?php elseif (!w3_is_writable_dir($uploads_dir['path'])): ?>
            <code>Not write-able</code>
            <?php else: ?>
            <code>OK</code>
            <?php endif; ?>
        </li>

        <li>
            Fancy permalinks:
            <?php $permalink_structure = get_option('permalink_structure'); if ($permalink_structure): ?>
            <code><?php echo htmlspecialchars($permalink_structure); ?></code>
            <?php else: ?>
            <code>Disabled</code>
            <?php endif; ?>
        </li>

        <li>
            WP_CACHE define:
            <?php if (defined('WP_CACHE')): ?>
            <code>Defined (<?php echo (WP_CACHE ? 'true' : 'false'); ?>)</code>
            <?php else: ?>
            <code>Not defined</code>
            <?php endif; ?>
        </li>

        <li>
            URL rewrite:
            <?php if (w3_can_check_rules()): ?>
            <code>Enabled</code>
            <?php else: ?>
            <code>Disabled</code>
            <?php endif; ?>
        </li>

        <li>
            Network mode:
            <?php if (w3_is_network()): ?>
            <code>Yes (<?php echo (w3_is_subdomain_install() ? 'subdomain' : 'subdir'); ?>)</code>
            <?php else: ?>
            <code>No</code>
            <?php endif; ?>
        </li>
    </ul>
</div>

<div id="w3tc-self-test-bottom">
    <input class="button-primary" type="button" value="Close" />
</div>
