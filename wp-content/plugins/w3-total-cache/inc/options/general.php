<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<p>
	The plugin is currently <span class="w3tc-<?php if ($enabled): ?>enabled">enabled<?php else: ?>disabled">disabled<?php endif; ?></span>. If an option is disabled it means that either your current installation is not compatible or software installation is required.
</p>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
    	Perform a
    	<input type="button" class="button button-self-test {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="compatibility check" />,
        <?php echo $this->nonce_field('w3tc'); ?>
    	<input class="button" type="submit" name="w3tc_flush_all" value="empty all caches"<?php if (! $can_empty_memcache && ! $can_empty_opcode && ! $can_empty_file): ?> disabled="disabled"<?php endif; ?> /> at once or
    	<input class="button" type="submit" name="w3tc_flush_memcached" value="empty only the memcached cache(s)"<?php if (! $can_empty_memcache): ?> disabled="disabled"<?php endif; ?> /> or
    	<input class="button" type="submit" name="w3tc_flush_opcode" value="empty only the opcode cache"<?php if (! $can_empty_opcode): ?> disabled="disabled"<?php endif; ?> /> or
    	<input class="button" type="submit" name="w3tc_flush_file" value="empty only the disk cache(s)"<?php if (! $can_empty_file): ?> disabled="disabled"<?php endif; ?> />.
    </p>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
        <?php echo $this->postbox_header('General'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <label>
                        <input id="enabled" type="checkbox" name="enabled" value="1"<?php checked($enabled_checkbox, true); ?> />
                        Toggle all caching types on or off at once.
                    </label>
                </th>
            </tr>
            <tr>
                <th>Preview Mode:</th>
                <td>
                    <?php echo $this->nonce_field('w3tc'); ?>
                    <?php if ($preview): ?>
                    <input type="hidden" name="preview" value="0" />
                    <input type="submit" name="w3tc_preview_save" class="button-primary" value="Disable" />
                    <?php echo $this->button_link('Preview', w3_get_home_url() . '/?w3tc_preview=1', true); ?>
                    <?php echo $this->button_link('Deploy', sprintf('admin.php?page=%s&w3tc_preview_deploy', $this->_page)); ?>
                    <?php else: ?>
                    <input type="hidden" name="preview" value="1" />
                    <input type="submit" name="w3tc_preview_save" class="button-primary" value="Enable" />
                    <?php endif; ?>
                    <br /><span class="description">Use preview mode to test configuration scenarios prior to releasing them (deploy) on the actual site.</span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Page Cache'); ?>
        <p>Enable page caching to decrease the response time of your site.</p>

        <table class="form-table">
            <tr>
                <th>Page Cache:</th>
                <td>
                    <input type="hidden" name="pgcache.enabled" value="0" />
                    <label><input class="enabled" type="checkbox" name="pgcache.enabled" value="1"<?php checked($pgcache_enabled, true); ?> />&nbsp;<strong>Enable</strong></label>
                    <br /><span class="description">Caching pages will reduce the response time of your site and increase the scale of your web server.</span>
                </td>
            </tr>
            <tr>
                <th>Page Cache Method:</th>
                <td>
                    <select name="pgcache.engine">
                        <optgroup label="Shared Server (disk enhanced is best):">
                            <option value="file"<?php selected($this->_config->get_string('pgcache.engine'), 'file'); ?>>Disk: Basic</option>
                            <option value="file_generic"<?php selected($this->_config->get_string('pgcache.engine'), 'file_generic'); ?><?php if (! $check_rules): ?> disabled="disabled"<?php endif; ?>>Disk: Enhanced</option>
                        </optgroup>
                        <optgroup label="Dedicated / Virtual Server:">
                            <option value="apc"<?php selected($this->_config->get_string('pgcache.engine'), 'apc'); ?><?php if (! $check_apc): ?> disabled="disabled"<?php endif; ?>>Opcode: Alternative PHP Cache (APC)</option>
                            <option value="eaccelerator"<?php selected($this->_config->get_string('pgcache.engine'), 'eaccelerator'); ?><?php if (! $check_eaccelerator): ?> disabled="disabled"<?php endif; ?>>Opcode: eAccelerator</option>
                            <option value="xcache"<?php selected($this->_config->get_string('pgcache.engine'), 'xcache'); ?><?php if (! $check_xcache): ?> disabled="disabled"<?php endif; ?>>Opcode: XCache</option>
                        <option value="wincache"<?php selected($this->_config->get_string('pgcache.engine'), 'wincache'); ?><?php if (! $check_wincache): ?> disabled="disabled"<?php endif; ?>>Opcode: WinCache</option>
                        </optgroup>
                        <optgroup label="Multiple Servers:">
                            <option value="memcached"<?php selected($this->_config->get_string('pgcache.engine'), 'memcached'); ?><?php if (! $check_memcached): ?> disabled="disabled"<?php endif; ?>>Memcached</option>
                        </optgroup>
                    </select>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
            <input type="submit" name="w3tc_flush_pgcache" value="Empty cache"<?php if (! $pgcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Minify'); ?>
        <p>Reduce load time by decreasing the size and number of <acronym title="Cascading Style Sheet">CSS</acronym> and <acronym title="JavaScript">JS</acronym> files. Automatically remove unncessary data from <acronym title="Cascading Style Sheet">CSS</acronym>, <acronym title="JavaScript">JS</acronym>, feed, page and post <acronym title="Hypertext Markup Language">HTML</acronym>.</p>

        <table class="form-table">
            <tr>
                <th>Minify:</th>
                <td>
                    <input type="hidden" name="minify.enabled" value="0"<?php if (! W3TC_PHP5): ?> disabled="disabled"<?php endif; ?> />
                    <label><input class="enabled" type="checkbox" name="minify.enabled" value="1"<?php checked($minify_enabled, true); ?><?php if (! W3TC_PHP5): ?> disabled="disabled"<?php endif; ?> />&nbsp;<strong>Enable</strong></label>
                    <br /><span class="description">Minification can decrease file size of <acronym title="Hypertext Markup Language">HTML</acronym>, <acronym title="Cascading Style Sheet">CSS</acronym>, <acronym title="JavaScript">JS</acronym> and feeds respectively by ~10% on average.</span>
                </td>
            </tr>
            <tr>
                <th>Minify mode:</th>
                <td>
                    <label><input type="radio" name="minify.auto" value="1"<?php checked($this->_config->get_boolean('minify.auto'), true); ?><?php if (! W3TC_PHP5): ?> disabled="disabled"<?php endif; ?> /> Auto</label>
                    <label><input type="radio" name="minify.auto" value="0"<?php checked($this->_config->get_boolean('minify.auto'), false); ?><?php if (! W3TC_PHP5): ?> disabled="disabled"<?php endif; ?> /> Manual</label>
					<br /><span class="description">Select manual mode to use fields on the minify settings tab to specify files to be minified, otherwise files will be minified automatically, but will not use the <acronym title="Content Delivery Network">CDN</acronym>.</span>
                </td>
            </tr>
            <tr>
                <th>Minify Cache Method:</th>
                <td>
                    <select name="minify.engine"<?php if (! W3TC_PHP5): ?> disabled="disabled"<?php endif; ?>>
                        <optgroup label="Shared Server (disk is best):">
                            <option value="file"<?php selected($this->_config->get_string('minify.engine'), 'file'); ?>>Disk</option>
                        </optgroup>
                        <optgroup label="Dedicated / Virtual Server:">
                            <option value="apc"<?php selected($this->_config->get_string('minify.engine'), 'apc'); ?><?php if (! $check_apc): ?> disabled="disabled"<?php endif; ?>>Opcode: Alternative PHP Cache (APC)</option>
                            <option value="eaccelerator"<?php selected($this->_config->get_string('minify.engine'), 'eaccelerator'); ?><?php if (! $check_eaccelerator): ?> disabled="disabled"<?php endif; ?>>Opcode: eAccelerator</option>
                            <option value="xcache"<?php selected($this->_config->get_string('minify.engine'), 'xcache'); ?><?php if (! $check_xcache): ?> disabled="disabled"<?php endif; ?>>Opcode: XCache</option>
                            <option value="wincache"<?php selected($this->_config->get_string('minify.engine'), 'wincache'); ?><?php if (! $check_wincache): ?> disabled="disabled"<?php endif; ?>>Opcode: WinCache</option>
                        </optgroup>
                            <optgroup label="Multiple Servers:">
                            <option value="memcached"<?php selected($this->_config->get_string('minify.engine'), 'memcached'); ?><?php if (! $check_memcached): ?> disabled="disabled"<?php endif; ?>>Memcached</option>
                        </optgroup>
                    </select>
                </td>
            </tr>
            <tr>
                <th><acronym title="Hypertext Markup Language">HTML</acronym> minifier:</th>
                <td>
                    <select name="minify.html.engine"<?php if (! W3TC_PHP5): ?> disabled="disabled"<?php endif; ?>>
                        <option value="html"<?php selected($this->_config->get_string('minify.html.engine'), 'html'); ?>>Default</option>
                        <option value="htmltidy"<?php selected($this->_config->get_string('minify.html.engine'), 'htmltidy'); ?><?php if (! $check_tidy): ?> disabled="disabled"<?php endif; ?>>HTML Tidy</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><acronym title="JavaScript">JS</acronym> minifier:</th>
                <td>
                    <select name="minify.js.engine"<?php if (! W3TC_PHP5): ?> disabled="disabled"<?php endif; ?>>
                        <option value="js"<?php selected($this->_config->get_string('minify.js.engine'), 'js'); ?>>JSMin (default)</option>
                        <option value="yuijs"<?php selected($this->_config->get_string('minify.js.engine'), 'yuijs'); ?>>YUI Compressor</option>
                        <option value="ccjs"<?php selected($this->_config->get_string('minify.js.engine'), 'ccjs'); ?>>Closure Compiler</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><acronym title="Cascading Style Sheets">CSS</acronym> minifier:</th>
                <td>
                    <select name="minify.css.engine"<?php if (! W3TC_PHP5): ?> disabled="disabled"<?php endif; ?>>
                        <option value="css"<?php selected($this->_config->get_string('minify.css.engine'), 'css'); ?>>Default</option>
                        <option value="yuicss"<?php selected($this->_config->get_string('minify.css.engine'), 'yuicss'); ?>>YUI Compressor</option>
                        <option value="csstidy"<?php selected($this->_config->get_string('minify.css.engine'), 'csstidy'); ?>>CSS Tidy</option>
                    </select>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
            <input type="submit" name="w3tc_flush_minify" value="Empty cache"<?php if (! $minify_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Database Cache'); ?>
        <p>Enable database caching to reduce post, page and feed creation time.</p>

         <table class="form-table">
            <tr>
                <th>Database Cache:</th>
                <td>
                    <input type="hidden" name="dbcache.enabled" value="0" />
                    <label><input class="enabled" type="checkbox" name="dbcache.enabled" value="1"<?php checked($dbcache_enabled, true); ?> />&nbsp;<strong>Enable</strong></label>
                    <br /><span class="description">Caching database objects decreases the response time of your site. Best used if object caching is not possible.</span>
                </td>
            </tr>
            <tr>
                <th>Database Cache Method:</th>
                <td>
                    <select name="dbcache.engine">
                        <optgroup label="Shared Server:">
                            <option value="file"<?php selected($this->_config->get_string('dbcache.engine'), 'file'); ?>>Disk</option>
                        </optgroup>
                        <optgroup label="Dedicated / Virtual Server:">
                            <option value="apc"<?php selected($this->_config->get_string('dbcache.engine'), 'apc'); ?><?php if (! $check_apc): ?> disabled="disabled"<?php endif; ?>>Opcode: Alternative PHP Cache (APC)</option>
                            <option value="eaccelerator"<?php selected($this->_config->get_string('dbcache.engine'), 'eaccelerator'); ?><?php if (! $check_eaccelerator): ?> disabled="disabled"<?php endif; ?>>Opcode: eAccelerator</option>
                            <option value="xcache"<?php selected($this->_config->get_string('dbcache.engine'), 'xcache'); ?><?php if (! $check_xcache): ?> disabled="disabled"<?php endif; ?>>Opcode: XCache</option>
                            <option value="wincache"<?php selected($this->_config->get_string('dbcache.engine'), 'wincache'); ?><?php if (! $check_wincache): ?> disabled="disabled"<?php endif; ?>>Opcode: WinCache</option>
                    </optgroup>
                        <optgroup label="Multiple Servers:">
                            <option value="memcached"<?php selected($this->_config->get_string('dbcache.engine'), 'memcached'); ?><?php if (! $check_memcached): ?> disabled="disabled"<?php endif; ?>>Memcached</option>
                        </optgroup>
                    </select>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
            <input type="submit" name="w3tc_flush_dbcache" value="Empty cache"<?php if (! $dbcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Object Cache'); ?>
        <p>Enable object caching to further reduce execution time for common operations.</p>

        <table class="form-table">
            <tr>
                <th>Object Cache:</th>
                <td>
                    <input type="hidden" name="objectcache.enabled" value="0" />
                    <label><input class="enabled" type="checkbox" name="objectcache.enabled" value="1"<?php checked($objectcache_enabled, true); ?> />&nbsp;<strong>Enable</strong></label>
                    <br /><span class="description">Object caching greatly increases performance for highly dynamic sites (that use the Object Cache <acronym title="Application Programming Interface">API</acronym>).</span>
                </td>
            </tr>
            <tr>
                <th>Object Cache Method:</th>
                <td>
                    <select name="objectcache.engine">
                        <optgroup label="Shared Server:">
                            <option value="file"<?php selected($this->_config->get_string('objectcache.engine'), 'file'); ?>>Disk</option>
                        </optgroup>
                        <optgroup label="Dedicated / Virtual Server:">
                            <option value="apc"<?php selected($this->_config->get_string('objectcache.engine'), 'apc'); ?><?php if (! $check_apc): ?> disabled="disabled"<?php endif; ?>>Opcode: Alternative PHP Cache (APC)</option>
                            <option value="eaccelerator"<?php selected($this->_config->get_string('objectcache.engine'), 'eaccelerator'); ?><?php if (! $check_eaccelerator): ?> disabled="disabled"<?php endif; ?>>Opcode: eAccelerator</option>
                            <option value="xcache"<?php selected($this->_config->get_string('objectcache.engine'), 'xcache'); ?><?php if (! $check_xcache): ?> disabled="disabled"<?php endif; ?>>Opcode: XCache</option>
                            <option value="wincache"<?php selected($this->_config->get_string('objectcache.engine'), 'wincache'); ?><?php if (! $check_wincache): ?> disabled="disabled"<?php endif; ?>>Opcode: WinCache</option>
                    </optgroup>
                        <optgroup label="Multiple Servers:">
                            <option value="memcached"<?php selected($this->_config->get_string('objectcache.engine'), 'memcached'); ?><?php if (! $check_memcached): ?> disabled="disabled"<?php endif; ?>>Memcached</option>
                        </optgroup>
                    </select>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
            <input type="submit" name="w3tc_flush_objectcache" value="Empty cache"<?php if (! $objectcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Browser Cache'); ?>
        <p>Reduce server load and decrease response time by using the cache available in site visitor's web browser.</p>

        <table class="form-table">
            <tr>
                <th>Browser Cache:</th>
                <td>
                    <input type="hidden" name="browsercache.enabled" value="0" />
                    <label><input class="enabled" type="checkbox" name="browsercache.enabled" value="1"<?php checked($browsercache_enabled, true); ?> />&nbsp;<strong>Enable</strong></label>
                    <br /><span class="description">Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression and add headers to reduce server load and decrease file load time.</span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('<acronym title="Content Delivery Network">CDN</acronym>'); ?>
        <p>Host static files with your content delivery network provider to reduce page load time.</p>

        <table class="form-table">
            <tr>
                <th><acronym title="Content Delivery Network">CDN</acronym>:</th>
                <td>
                    <input type="hidden" name="cdn.enabled" value="0" />
                    <label><input class="enabled" type="checkbox" name="cdn.enabled" value="1"<?php checked($cdn_enabled, true); ?> />&nbsp;<strong>Enable</strong></label>
                    <br /><span class="description">Theme files, media library attachments, <acronym title="Cascading Style Sheet">CSS</acronym>, <acronym title="JavaScript">JS</acronym> files etc will appear to load instantly for site visitors.</span>
                </td>
            </tr>
            <tr>
                <th><acronym title="Content Delivery Network">CDN</acronym> Type:</th>
                <td>
                    <select name="cdn.engine">
                        <optgroup label="Origin Pull (mirror is best):">
                            <option value="cf2"<?php selected($this->_config->get_string('cdn.engine'), 'cf2'); ?><?php if (!W3TC_PHP5 || !$check_curl): ?> disabled="disabled"<?php endif; ?>>Amazon CloudFront</option>
                            <option value="cotendo"<?php selected($this->_config->get_string('cdn.engine'), 'cotendo'); ?>>Cotendo</option>
                            <option value="mirror"<?php selected($this->_config->get_string('cdn.engine'), 'mirror'); ?>>Generic Mirror</option>
                            <option value="edgecast"<?php selected($this->_config->get_string('cdn.engine'), 'edgecast'); ?>>Media Temple ProCDN / EdgeCast</option>
                            <option value="netdna"<?php selected($this->_config->get_string('cdn.engine'), 'netdna'); ?>>NetDNA / MaxCDN</option>
                        </optgroup>
                        <optgroup label="Origin Push:">
                            <option value="cf"<?php selected($this->_config->get_string('cdn.engine'), 'cf'); ?><?php if (!W3TC_PHP5 || !$check_curl): ?> disabled="disabled"<?php endif; ?>>Amazon CloudFront</option>
                            <option value="s3"<?php selected($this->_config->get_string('cdn.engine'), 's3'); ?><?php if (!W3TC_PHP5 || !$check_curl): ?> disabled="disabled"<?php endif; ?>>Amazon Simple Storage Service (S3)</option>
                            <option value="azure"<?php selected($this->_config->get_string('cdn.engine'), 'azure'); ?><?php if (!W3TC_PHP5): ?> disabled="disabled"<?php endif; ?>>Microsoft Azure Storage</option>
                            <option value="rscf"<?php selected($this->_config->get_string('cdn.engine'), 'rscf'); ?><?php if (!W3TC_PHP5 || !$check_curl): ?> disabled="disabled"<?php endif; ?>>Rackspace Cloud Files</option>
                            <option value="ftp"<?php selected($this->_config->get_string('cdn.engine'), 'ftp'); ?><?php if (!$check_ftp): ?> disabled="disabled"<?php endif; ?>>Self-hosted / File Transfer Protocol Upload</option>
                        </optgroup>
                    </select><br />
                    <span class="description">Select the <acronym title="Content Delivery Network">CDN</acronym> type you wish to use.</span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
            <input id="cdn_purge" type="button" value="Purge cache"<?php if (!$cdn_enabled || !w3_can_cdn_purge($this->_config->get_string('cdn.engine'))): ?> disabled="disabled"<?php endif; ?> class="button" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Varnish'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <input type="hidden" name="varnish.enabled" value="0" />
                    <label><input class="enabled" type="checkbox" name="varnish.enabled" value="1"<?php checked($varnish_enabled, true); ?> /> Enable varnish cache purging</label><br />
                </th>
            </tr>
             <tr>
                 <th><label for="pgcache_varnish_servers">Varnish servers:</label></th>
                 <td>
                    <textarea id="pgcache_varnish_servers" name="varnish.servers" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('varnish.servers'))); ?></textarea><br />
                    <span class="description">Specify the IP addresses of your varnish instances above. Your <acronym title="Varnish Configuration Language">VCL</acronym>'s <acronym title="Access Control List">ACL</acronym> must allow this request.</span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Network Performance &amp; Security powered by CloudFlare'); ?>
        <p>
            CloudFlare protects and accelerates websites. <a href="https://www.cloudflare.com/sign-up.html?affiliate=w3edge&amp;seed_domain=<?php echo w3_get_host(); ?>&amp;email=<?php echo htmlspecialchars($cloudflare_signup_email); ?>&amp;username=<?php echo htmlspecialchars($cloudflare_signup_user); ?>" target="_blank">Sign up now for free</a> to get started,
            or if you have an account simply log in to obtain your <acronym title="Application Programming Interface">API</acronym> key from the <a href="https://www.cloudflare.com/my-account.html">account page</a> to enter it below.
            Contact the CloudFlare <a href="http://www.cloudflare.com/help.html" target="_blank">support team</a> with any questions.
        </p>

        <table class="form-table">
            <tr>
                <th>CloudFlare:</th>
                <td>
                    <input type="hidden" name="cloudflare.enabled" value="0" />
                    <label><input class="enabled" type="checkbox" name="cloudflare.enabled" value="1"<?php checked($cloudflare_enabled, true); ?> />&nbsp;<strong>Enable</strong></label>
                </td>
            </tr>
            <tr>
                <th><label for="cloudflare_email">CloudFlare account email:</label></th>
                <td>
                    <input id="cloudflare_email" class="w3tc-ignore-change" type="text" name="cloudflare.email" value="<?php echo htmlspecialchars($this->_config->get_string('cloudflare.email')); ?>" size="60" />
                </td>
            </tr>
            <tr>
                <th><label for="cloudflare_key"><acronym title="Application Programming Interface">API</acronym> key:</label></th>
                <td>
                    <input id="cloudflare_key" class="w3tc-ignore-change" type="password" name="cloudflare.key" value="<?php echo htmlspecialchars($this->_config->get_string('cloudflare.key')); ?>" size="60" /> (<a href="https://www.cloudflare.com/my-account.html">find it here</a>)
                </td>
            </tr>
            <tr>
                <th>Domain:</th>
                <td>
                    <input id="cloudflare_zone" type="text" name="cloudflare.zone" value="<?php echo htmlspecialchars($this->_config->get_string('cloudflare.zone', w3_get_host())); ?>" size="40" />
                </td>
            </tr>
            <tr>
                <th>Security level:</th>
                <td>
                    <input type="hidden" name="cloudflare_seclvl_old" value="<?php echo $cloudflare_seclvl; ?>" />
                    <select name="cloudflare_seclvl_new" class="w3tc-ignore-change">
                        <?php foreach ($cloudflare_seclvls as $cloudflare_seclvl_key => $cloudflare_seclvl_label): ?>
                        <option value="<?php echo $cloudflare_seclvl_key; ?>"<?php selected($cloudflare_seclvl, $cloudflare_seclvl_key); ?>><?php echo $cloudflare_seclvl_label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Development mode:</th>
                <td>
                    <input type="hidden" name="cloudflare_devmode_old" value="<?php echo $cloudflare_devmode; ?>" />
                    <select name="cloudflare_devmode_new" class="w3tc-ignore-change">
                        <?php foreach ($cloudflare_devmodes as $cloudflare_devmode_key => $cloudflare_devmode_label): ?>
                        <option value="<?php echo $cloudflare_devmode_key; ?>"<?php selected($cloudflare_devmode, $cloudflare_devmode_key); ?>><?php echo $cloudflare_devmode_label; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($cloudflare_devmode_expire): ?>
                    Will automatically turn off at <?php echo date('m/d/Y H:i:s', $cloudflare_devmode_expire); ?>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
            <input id="cloudflare_purge_cache" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Purge cache"<?php if (! $cloudflare_enabled): ?> disabled="disabled"<?php endif; ?> />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Support Us'); ?>
        <p>We're working to make WordPress better. Please support us, here's how:</p>

        <p>
            <label>Link to us in your
                <select name="common.support">
                    <option value="">select one</option>
                    <?php foreach ($supports as $support_id => $support_name): ?>
                    <option value="<?php echo $support_id; ?>"<?php selected($support, $support_id); ?>><?php echo htmlspecialchars($support_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>, tell your friends with a <input type="button" class="button button-tweet" value="tweet" />
            (<input type="hidden" name="common.tweeted" value="0" /><label><input type="checkbox" name="common.tweeted" value="1"<?php checked($this->_config->get_boolean('common.tweeted', true)); ?> /> I've tweeted</label>)
            and give us a perfect <input type="button" class="button button-rating" value="rating" />.
        </p>

        <p>If you want to manually place a link, here is the code:</p>
        <p><textarea cols="80" rows="4">Performance Optimization &lt;a href=&quot;http://www.w3-edge.com/wordpress-plugins/&quot; rel=&quot;external&quot;&gt;WordPress Plugins&lt;/a&gt; by W3 EDGE</textarea></p>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>
        <?php echo $this->postbox_header('Miscellaneous'); ?>
        <table class="form-table">
            <?php if (w3_is_nginx()): ?>
            <tr>
                <th>Nginx server configuration file path</th>
                <td>
                    <input type="text" name="config.path" value="<?php echo htmlspecialchars($this->_config->get_string('config.path')); ?>" size="80" />
                    <br /><span class="description">If empty the default path will be used..</span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="config.check" value="0" />
                    <label><input type="checkbox" name="config.check" value="1"<?php checked($this->_config->get_boolean('config.check'), true); ?> /> Verify rewrite rules</label>
                    <br /><span class="description">Notify of server configuration errors, if this option is disabled, the server configuration for active settings can be found on the <a href="admin.php?page=w3tc_install">install</a> tab.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="file_locking" value="0"<?php if (! $can_empty_file): ?> disabled="disabled"<?php endif; ?> />
                    <label><input type="checkbox" name="file_locking" value="1"<?php checked($file_locking, true); ?><?php if (! $can_empty_file): ?> disabled="disabled"<?php endif; ?> /> Enable file locking</label>
                    <br /><span class="description">Not recommended for "slow" network-based file systems.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="file_nfs" value="0"<?php if (! $can_empty_file): ?> disabled="disabled"<?php endif; ?> />
                    <label><input type="checkbox" name="file_nfs" value="1"<?php checked($file_nfs, true); ?><?php if (! $can_empty_file): ?> disabled="disabled"<?php endif; ?> /> Optimize disk enhanced page and minify caching for <acronym title="Network File System">NFS</acronym></label>
                    <br /><span class="description">Try this option if your hosting environment uses a network based file system for a possible performance improvement.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="widget.latest.enabled" value="0" />
                    <label><input type="checkbox" name="widget.latest.enabled" value="1"<?php checked($this->_config->get_boolean('widget.latest.enabled'), true); ?> /> Enable news dashboard widget</label>
                    <br /><span class="description">Display latest tweets and support tips on the WordPress dashboard.</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="widget.pagespeed.enabled" value="0" />
                    <label><input type="checkbox" name="widget.pagespeed.enabled" value="1"<?php checked($this->_config->get_boolean('widget.pagespeed.enabled'), true); ?> />  Enable Google Page Speed dashboard widget</label>
                    <br /><span class="description">Display Google Page Speed results on the WordPress dashboard.</span>
                </th>
            </tr>
            <tr>
                <th><label for="widget_pagespeed_key">Page Speed <acronym title="Application Programming Interface">API</acronym> Key:</label></th>
                <td>
                    <input id="widget_pagespeed_key" type="text" name="widget.pagespeed.key" value="<?php echo $this->_config->get_string('widget.pagespeed.key'); ?>" size="60" /><br />
                    <span class="description">To acquire an <acronym title="Application Programming Interface">API</acronym> key, visit the <a href="https://code.google.com/apis/console" target="_blank"><acronym title="Application Programming Interface">API</acronym>s Console</a>. Go to the Project Home tab, activate the Page Speed Online <acronym title="Application Programming Interface">API</acronym>, and accept the Terms of Service.
                    Then go to the <acronym title="Application Programming Interface">API</acronym> Access tab. The <acronym title="Application Programming Interface">API</acronym> key is in the Simple <acronym title="Application Programming Interface">API</acronym> Access section.</span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

        <?php echo $this->postbox_header('Debug'); ?>
        <p>Detailed information about each cache will be appended in (publicly available) <acronym title="Hypertext Markup Language">HTML</acronym> comments in the page's source code. Performance in this mode will not be optimal, use sparingly and disable when not in use.</p>

        <table class="form-table">
            <tr>
                <th>Debug Mode:</th>
                <td>
                    <input type="hidden" name="pgcache.debug" value="<?php echo ((!$this->_config->get_boolean('pgcache.enabled') && $this->_config->get_boolean('pgcache.debug')) ? "1" : "0") ?>" />
                    <input type="hidden" name="minify.debug" value="<?php echo ((!$this->_config->get_boolean('minify.enabled') && $this->_config->get_boolean('minify.debug')) ? "1" : "0") ?>" />
                    <input type="hidden" name="dbcache.debug" value="<?php echo ((!$this->_config->get_boolean('dbcache.enabled') && $this->_config->get_boolean('dbcache.debug')) ? "1" : "0") ?>" />
                    <input type="hidden" name="objectcache.debug" value="<?php echo ((!$this->_config->get_boolean('objectcache.enabled') && $this->_config->get_boolean('objectcache.debug')) ? "1" : "0") ?>" />
                    <input type="hidden" name="cdn.debug" value="<?php echo ((!$this->_config->get_boolean('cdn.enabled') && $this->_config->get_boolean('cdn.debug')) ? "1" : "0") ?>" />
                    <input type="hidden" name="varnish.debug" value="<?php echo ((!$this->_config->get_boolean('varnish.enabled') && $this->_config->get_boolean('varnish.debug')) ? "1" : "0") ?>" />
                    <label><input type="checkbox" name="pgcache.debug" value="pgcache"<?php checked($this->_config->get_boolean('pgcache.debug') && $this->_config->get_boolean('pgcache.enabled'), true); ?> <?php if (!$this->_config->get_boolean('pgcache.enabled')): ?> disabled="disabled"<?php endif; ?>/> Page Cache</label><br />
                    <label><input type="checkbox" name="minify.debug" value="minify"<?php checked($this->_config->get_boolean('minify.debug') && $this->_config->get_boolean('minify.enabled'), true); ?> <?php if (!$this->_config->get_boolean('minify.enabled')): ?> disabled="disabled"<?php endif; ?>/> Minify</label><br />
                    <label><input type="checkbox" name="dbcache.debug" value="dbcache"<?php checked($this->_config->get_boolean('dbcache.debug') && $this->_config->get_boolean('dbcache.enabled'), true); ?> <?php if (!$this->_config->get_boolean('dbcache.enabled')): ?> disabled="disabled"<?php endif; ?>/> Database Cache</label><br />
                    <label><input type="checkbox" name="objectcache.debug" value="objectcache"<?php checked($this->_config->get_boolean('objectcache.debug') && $this->_config->get_boolean('objectcache.enabled'), true); ?> <?php if (!$this->_config->get_boolean('objectcache.enabled')): ?> disabled="disabled"<?php endif; ?>/> Object Cache</label><br />
                    <label><input type="checkbox" name="cdn.debug" value="cdn"<?php checked($this->_config->get_boolean('cdn.debug') && $this->_config->get_boolean('cdn.enabled'), true); ?> <?php if (!$this->_config->get_boolean('cdn.enabled')): ?> disabled="disabled"<?php endif; ?>/> Content Delivery Network</label><br />
                    <label><input type="checkbox" name="varnish.debug" value="varnish"<?php checked($this->_config->get_boolean('varnish.debug') && $this->_config->get_boolean('varnish.enabled'), true); ?> <?php if (!$this->_config->get_boolean('varnish.enabled')): ?> disabled="disabled"<?php endif; ?>/> Varnish</label><br />
                    <span class="description">If selected, detailed caching information will be appear at the end of each page in a <acronym title="Hypertext Markup Language">HTML</acronym> comment. View a page's source code to review.</span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
            <input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post" enctype="multipart/form-data">
    <div class="metabox-holder">
        <?php echo $this->postbox_header('Import / Export Settings'); ?>
        <?php echo $this->nonce_field('w3tc'); ?>
        <table class="form-table">
            <tr>
                <th>Import configuration:</th>
                <td>
                    <input type="file" name="config_file" />
                    <input type="submit" name="w3tc_config_import" class="button" value="Upload" />
                    <br /><span class="description">Upload and replace the active settings file.</span>
                </td>
            </tr>
            <tr>
                <th>Export configuration:</th>
                <td>
                    <input type="submit" name="w3tc_config_export" class="button" value="Download" />
                    <br /><span class="description">Download the active settings file.</span>
                </td>
            </tr>
            <tr>
                <th>Reset configuration:</th>
                <td>
                    <input type="submit" name="w3tc_config_reset" class="button" value="Restore Default Settings" />
                    <br /><span class="description">Revert all settings to the defaults. Any settings staged in preview mode will not be modified.</span>
                </td>
            </tr>
        </table>
        <?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>