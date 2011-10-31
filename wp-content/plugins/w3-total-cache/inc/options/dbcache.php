<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
        Database caching via
        <strong><?php echo w3_get_engine_name($this->_config->get_string('dbcache.engine')); ?></strong>
        is currently <span class="w3tc-<?php if ($dbcache_enabled): ?>enabled">enabled<?php else: ?>disabled">disabled<?php endif; ?></span>.
    </p>
    <p>
		To rebuild the database cache use the
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="w3tc_flush_dbcache" value="empty cache"<?php if (! $dbcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
		operation.
    </p>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
		<?php echo $this->postbox_header('General'); ?>
        <table class="form-table">
        	<tr>
        		<th>
        			<input type="hidden" name="dbcache.reject.logged" value="0" />
        			<label><input type="checkbox" name="dbcache.reject.logged" value="1"<?php checked($this->_config->get_boolean('dbcache.reject.logged'), true); ?> /> Don't cache queries for logged in users</label>
					<br /><span class="description">Enabling this option is recommended to maintain default WordPress behavior.</span>
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
        	<?php if ($this->_config->get_string('dbcache.engine') == 'memcached'): ?>
        	<tr>
        		<th><label for="memcached_servers">Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:</label></th>
        		<td>
        			<input id="memcached_servers" type="text" name="dbcache.memcached.servers" value="<?php echo htmlspecialchars(implode(',', $this->_config->get_array('dbcache.memcached.servers'))); ?>" size="100" />
        			<input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test" />
        			<span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
        			<br /><span class="description">Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122</span>
        		</td>
        	</tr>
        	<?php endif; ?>
        	<tr>
        		<th style="width: 250px;"><label for="dbcache_lifetime">Maximum lifetime of cache objects:</label></th>
        		<td><input id="dbcache_lifetime" type="text" name="dbcache.lifetime" value="<?php echo $this->_config->get_integer('dbcache.lifetime'); ?>" size="8" /> seconds
        			<br /><span class="description">Determines the natural expiration time of unchanged cache items. The higher the value, the larger the cache.</span>
    			</td>
        	</tr>
        	<tr>
        		<th><label for="dbcache_file_gc">Garbage collection interval:</label></th>
        		<td><input id="dbcache_file_gc" type="text" name="dbcache.file.gc" value="<?php echo $this->_config->get_integer('dbcache.file.gc'); ?>" size="8" /> seconds
        			<br /><span class="description">If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.</span>
    			</td>
        	</tr>
        	<tr>
        		<th><label for="dbcache_reject_uri">Never cache the following pages:</label></th>
        		<td>
        			<textarea id="dbcache_reject_uri" name="dbcache.reject.uri" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('dbcache.reject.uri'))); ?></textarea><br />
        			<span class="description">Always ignore the specified pages / directories.</span>
        		</td>
        	</tr>
        	<tr>
        		<th><label for="dbcache_reject_sql">Ignored query stems:</label></th>
        		<td>
        			<textarea id="dbcache_reject_sql" name="dbcache.reject.sql" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('dbcache.reject.sql'))); ?></textarea><br />
        			<span class="description">Do not cache queries that contain these terms. Any entered prefix (set in wp-config.php) will be replaced with current database prefix (default: wp_). Query stems can be identified using debug mode.</span>
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

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>