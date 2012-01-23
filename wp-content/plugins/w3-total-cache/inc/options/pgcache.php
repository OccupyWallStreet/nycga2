<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
        Page caching via
        <strong><?php echo w3_get_engine_name($this->_config->get_string('pgcache.engine')); ?></strong>
        is currently <span class="w3tc-<?php if ($pgcache_enabled): ?>enabled">enabled<?php else: ?>disabled">disabled<?php endif; ?></span>.
    </p>
    <p>
		To rebuild the page cache use the
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="flush_pgcache" value="empty cache"<?php if (! $pgcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
		operation.
    </p>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
		<?php echo $this->postbox_header('General'); ?>
        <table class="form-table">
        	<tr>
        		<th>
        			<input type="hidden" name="pgcache.cache.home" value="0" />
        			<label><input type="checkbox" name="pgcache.cache.home" value="1"<?php checked($this->_config->get_boolean('pgcache.cache.home'), true); ?> /> Cache home page</label><br />
    				<span class="description">For many blogs this is your most visited page, it is recommended that you cache it.</span>
        		</th>
        	</tr>
        	<tr>
        		<th>
        			<input type="hidden" name="pgcache.cache.feed" value="0" />
        			<label><input type="checkbox" name="pgcache.cache.feed" value="1"<?php checked($this->_config->get_boolean('pgcache.cache.feed'), true); ?> /> Cache feeds: site, categories, tags, comments</label><br />
    				<span class="description">Even if using a feed proxy service (like <a href="http://en.wikipedia.org/wiki/FeedBurner" target="_blank">FeedBurner</a>), enabling this option is still recommended.</span>
        		</th>
        	</tr>
        	<tr>
        		<th>
        			<input type="hidden" name="pgcache.cache.ssl" value="0" />
        			<label><input type="checkbox" name="pgcache.cache.ssl" value="1"<?php checked($this->_config->get_boolean('pgcache.cache.ssl'), true); ?> /> Cache <acronym titlte="Secure Socket Layer">SSL</acronym> (<acronym title="HyperText Transfer Protocol over SSL">https</acronym>) requests</label><br />
    				<span class="description">Cache <acronym titlte="Secure Socket Layer">SSL</acronym> requests (uniquely) for improved performance.</span>
        		</th>
        	</tr>
        	<tr>
        		<th>
        			<input type="hidden" name="pgcache.cache.query" value="0"<?php if ($this->_config->get_string('pgcache.engine') == 'file_generic'): ?> disabled="disabled"<?php endif; ?> />
        			<label><input type="checkbox" name="pgcache.cache.query" value="1"<?php checked($this->_config->get_boolean('pgcache.cache.query'), true); ?><?php if ($this->_config->get_string('pgcache.engine') == 'file_generic'): ?> disabled="disabled"<?php endif; ?> /> Cache <acronym title="Uniform Resource Identifier">URI</acronym>s with query string variables</label><br />
    				<span class="description">Search result (and similar) pages will be cached if enabled.</span>
        		</th>
        	</tr>
        	<tr>
        		<th>
        			<input type="hidden" name="pgcache.cache.404" value="0" />
        			<label><input type="checkbox" name="pgcache.cache.404" value="1"<?php checked($this->_config->get_boolean('pgcache.cache.404'), true); ?> /> Cache 404 (not found) pages</label><br />
    				<span class="description">Reduce server load by caching 404 pages. If the disk enhanced method of disk caching is used, 404 pages will be returned with a 200 response code. Use at your own risk.</span>
        		</th>
        	</tr>
            <tr>
                <th>
                    <input type="hidden" name="pgcache.check.domain" value="0" />
                    <label><input type="checkbox" name="pgcache.check.domain" value="1"<?php checked($this->_config->get_boolean('pgcache.check.domain'), true); ?> /> Cache requests only for <?php echo w3_get_home_domain(); ?> hostname</label><br />
                    <span class="description">Cache only requests with the same <acronym title="Uniform Resource Indicator">URL</acronym> as the site's <a href="options-general.php">site address</a>.</span>
                </th>
            </tr>
        	<tr>
        		<th>
        			<input type="hidden" name="pgcache.reject.logged" value="0" />
        			<label><input type="checkbox" name="pgcache.reject.logged" value="1"<?php checked($this->_config->get_boolean('pgcache.reject.logged'), true); ?> /> Don't cache pages for logged in users</label><br />
    				<span class="description">Users that have signed in to WordPress (e.g. administrators) will never view cached pages if enabled.</span>
        		</th>
        	</tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
	    <?php echo $this->postbox_footer(); ?>

		<?php echo $this->postbox_header('Advanced'); ?>
        <table class="form-table">
        	<?php if ($this->_config->get_string('pgcache.engine') == 'memcached'): ?>
        	<tr>
        		<th><label for="memcached_servers">Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:</label></th>
        		<td>
        			<input id="memcached_servers" type="text" name="pgcache.memcached.servers" value="<?php echo htmlspecialchars(implode(',', $this->_config->get_array('pgcache.memcached.servers'))); ?>" size="100" />
        			<input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test" />
        			<span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
        			<br /><span class="description">Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122</span>
        		</td>
        	</tr>
        	<?php endif; ?>
        	<tr>
        		<th><label for="pgcache_file_gc">Garbage collection interval:</label></th>
        		<td>
        			<input id="pgcache_file_gc" type="text" name="pgcache.file.gc" value="<?php echo $this->_config->get_integer('pgcache.file.gc'); ?>" size="8"<?php if ($this->_config->get_string('pgcache.engine') != 'file' && $this->_config->get_string('pgcache.engine') != 'file_generic'): ?> disabled="disabled"<?php endif; ?> /> seconds
        			<br /><span class="description">If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.</span>
    			</td>
        	</tr>
        	<tr>
        		<th><label for="pgcache_reject_ua">Rejected User Agents:</label></th>
        		<td>
        			<textarea id="pgcache_reject_ua" name="pgcache.reject.ua" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.reject.ua'))); ?></textarea><br />
        			<span class="description">Never send cache pages for these user agents.</span>
        		</td>
        	</tr>
        	<tr>
        		<th><label for="pgcache_reject_cookie">Rejected Cookies:</label></th>
        		<td>
        			<textarea id="pgcache_reject_cookie" name="pgcache.reject.cookie" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.reject.cookie'))); ?></textarea><br />
        			<span class="description">Never cache pages that use the specified cookies.</span>
        		</td>
        	</tr>
        	<tr>
        		<th><label for="pgcache_reject_uri">Never cache the following pages:</label></th>
        		<td>
        			<textarea id="pgcache_reject_uri" name="pgcache.reject.uri" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.reject.uri'))); ?></textarea><br />
        			<span class="description">Always ignore the specified pages / directories.</span>
        		</td>
        	</tr>
        	<tr>
        		<th><label for="pgcache_accept_files">Cache exception list:</label></th>
        		<td>
        			<textarea id="pgcache_accept_files" name="pgcache.accept.files" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.accept.files'))); ?></textarea><br />
        			<span class="description">Cache the specified pages / directories even if listed in the "never cache the following pages" field.</span>
        		</td>
        	</tr>
            <?php if (substr($permalink_structure, -1) == '/'): ?>
            <tr>
                <th><label for="pgcache_accept_uri">Non-trailing slash pages:</label></th>
                <td>
                    <textarea id="pgcache_accept_uri" name="pgcache.accept.uri" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.accept.uri'))); ?></textarea><br />
                    <span class="description">Cache the specified pages even if they don't have tailing slash.</span>
                </td>
            </tr>
            <?php endif; ?>
        	<tr>
        		<th><label for="pgcache_cache_headers">Specify page headers:</label></th>
        		<td>
        			<textarea id="pgcache_cache_headers" name="pgcache.cache.headers" cols="40" rows="5"<?php if (!W3TC_PHP5 || $this->_config->get_string('pgcache.engine') == 'file_generic'): ?> disabled="disabled"<?php endif; ?>><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('pgcache.cache.headers'))); ?></textarea><br />
        			<span class="description">Specify additional page headers to cache.</span>
        		</td>
        	</tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
	    <?php echo $this->postbox_footer(); ?>

    	<?php echo $this->postbox_header('Cache Preload'); ?>
        <table class="form-table">
        	<tr>
        		<th colspan="2">
            		<input type="hidden" name="pgcache.prime.enabled" value="0" />
            		<label><input type="checkbox" name="pgcache.prime.enabled" value="1"<?php checked($this->_config->get_boolean('pgcache.prime.enabled'), true); ?> /> Automatically prime the page cache</label><br />
        		</th>
        	</tr>
        	<tr>
        		<th><label for="pgcache_prime_interval">Update interval:</label></th>
        		<td>
        			<input id="pgcache_prime_interval" type="text" name="pgcache.prime.interval" value="<?php echo $this->_config->get_integer('pgcache.prime.interval'); ?>" size="8" /> seconds<br />
        			<span class="description">The number of seconds to wait before creating another set of cached pages.</span>
    			</td>
        	</tr>
        	<tr>
        		<th><label for="pgcache_prime_limit">Pages per interval:</label></th>
        		<td>
        			<input id="pgcache_prime_limit" type="text" name="pgcache.prime.limit" value="<?php echo $this->_config->get_integer('pgcache.prime.limit'); ?>" size="8" /><br />
        			<span class="description">Limit the number of pages to create per batch. Fewer pages may be better for under-powered servers.</span>
    			</td>
        	</tr>
        	<tr>
        		<th><label for="pgcache_prime_sitemap">Sitemap <acronym title="Uniform Resource Indicator">URL</acronym>:</label></th>
        		<td>
        			<input id="pgcache_prime_sitemap" type="text" name="pgcache.prime.sitemap" value="<?php echo $this->_config->get_string('pgcache.prime.sitemap'); ?>" size="100" /><br />
        			<span class="description">A <a href="http://www.xml-sitemaps.com/validate-xml-sitemap.html" target="_blank">compliant</a> sitemap can be used to specify the pages to maintain in the primed cache. Pages will be cached according to the priorities specified in the <acronym title="Extensible Markup Language">XML</acronym> file. <a href="http://wordpress.org/extend/plugins/google-sitemap-generator/" target="_blank">Google <acronym title="Extensible Markup Language">XML</acronym> Sitemaps</a> is recommended for use with this feature.</span>
    			</td>
        	</tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
    	<?php echo $this->postbox_footer(); ?>

    	<?php echo $this->postbox_header('Purge Policy'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <p>Specify the pages and feeds to purge when posts are created, edited, or comments posted. The defaults are recommended because additional options may reduce server performance:</p>

                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <th style="padding-left: 0;">
                                <input type="hidden" name="pgcache.purge.home" value="0" />
                                <input type="hidden" name="pgcache.purge.post" value="0" />
                                <input type="hidden" name="pgcache.purge.feed.blog" value="0" />
                                <label><input type="checkbox" name="pgcache.purge.home" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.home'), true); ?> /> Home page</label><br />
                                <label><input type="checkbox" name="pgcache.purge.post" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.post'), true); ?> /> Post page</label><br />
                                <label><input type="checkbox" name="pgcache.purge.feed.blog" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.feed.blog'), true); ?> /> Blog feed</label><br />
                            </th>
                            <th>
                                <input type="hidden" name="pgcache.purge.comments" value="0" />
                                <input type="hidden" name="pgcache.purge.author" value="0" />
                                <input type="hidden" name="pgcache.purge.terms" value="0" />
                                <label><input type="checkbox" name="pgcache.purge.comments" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.comments'), true); ?> /> Post comments pages</label><br />
                                <label><input type="checkbox" name="pgcache.purge.author" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.author'), true); ?> /> Post author pages</label><br />
                                <label><input type="checkbox" name="pgcache.purge.terms" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.terms'), true); ?> /> Post terms pages</label><br />
                            </th>
                            <th>
                                <input type="hidden" name="pgcache.purge.feed.comments" value="0" />
                                <input type="hidden" name="pgcache.purge.feed.author" value="0" />
                                <input type="hidden" name="pgcache.purge.feed.terms" value="0" />
                                <label><input type="checkbox" name="pgcache.purge.feed.comments" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.feed.comments'), true); ?> /> Post comments feed</label><br />
                                <label><input type="checkbox" name="pgcache.purge.feed.author" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.feed.author'), true); ?> /> Post author feed</label><br />
                                <label><input type="checkbox" name="pgcache.purge.feed.terms" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.feed.terms'), true); ?> /> Post terms feeds</label>
                            </th>
                            <th>
                                <input type="hidden" name="pgcache.purge.archive.daily" value="0" />
                                <input type="hidden" name="pgcache.purge.archive.monthly" value="0" />
                                <input type="hidden" name="pgcache.purge.archive.yearly" value="0" />
                                <label><input type="checkbox" name="pgcache.purge.archive.daily" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.archive.daily'), true); ?> /> Daily archive pages</label><br />
                                <label><input type="checkbox" name="pgcache.purge.archive.monthly" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.archive.monthly'), true); ?> /> Monthly archive pages</label><br />
                                <label><input type="checkbox" name="pgcache.purge.archive.yearly" value="1"<?php checked($this->_config->get_boolean('pgcache.purge.archive.yearly'), true); ?> /> Yearly archive pages</label><br />
                            </th>
                        </tr>
                    </table>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    <p>Specify the feed types to purge:</p>
                    <input type="hidden" name="pgcache.purge.feed.types" value="" />
                    <?php foreach($feeds as $feed): ?>
                        <label><input type="checkbox" name="pgcache.purge.feed.types[]" value="<?php echo $feed; ?>"<?php checked(in_array($feed, $this->_config->get_array('pgcache.purge.feed.types')), true); ?> />
                        <?php echo $feed; ?>
                        <?php if ($feed == $default_feed): ?>(default)<?php endif; ?></label><br />
                    <?php endforeach; ?>
                </th>
            </tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
    	<?php echo $this->postbox_footer(); ?>

		<?php echo $this->postbox_header('Note(s):'); ?>
        <table class="form-table">
        	<tr>
        		<th colspan="2">
					<ul>
						<li>Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression in the "<acronym title="Hypertext Markup Language">HTML</acronym>" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.</li>
						<li>The <acronym title="Time to Live">TTL</acronym> of page cache files is set via the "Expires header lifetime" field in the "<acronym title="Hypertext Markup Language">HTML</acronym>" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.</li>
					</ul>
        		</th>
        	</tr>
        </table>
    	<?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>