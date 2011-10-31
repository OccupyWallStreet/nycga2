<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
        Object caching via
        <strong><?php echo w3_get_engine_name($this->_config->get_string('objectcache.engine')); ?></strong>
        is currently <span class="w3tc-<?php if ($objectcache_enabled): ?>enabled">enabled<?php else: ?>disabled">disabled<?php endif; ?></span>.
    </p>
    <p>
		To rebuild the object cache use the
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="w3tc_flush_objectcache" value="empty cache"<?php if (! $objectcache_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
		operation.
    </p>
</form>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
		<?php echo $this->postbox_header('Advanced'); ?>
        <table class="form-table">
        	<?php if ($this->_config->get_string('objectcache.engine') == 'memcached'): ?>
        	<tr>
        		<th><label for="memcached_servers">Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:</label></th>
        		<td>
        			<input id="memcached_servers" type="text" name="objectcache.memcached.servers" value="<?php echo htmlspecialchars(implode(',', $this->_config->get_array('objectcache.memcached.servers'))); ?>" size="100" />
        			<input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test" />
        			<span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
        			<br /><span class="description">Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122</span>
        		</td>
        	</tr>
        	<?php endif; ?>
        	<tr>
        		<th style="width: 250px;"><label for="objectcache_lifetime">Default lifetime of cache objects:</label></th>
        		<td>
        			<input id="objectcache_lifetime" type="text" name="objectcache.lifetime" value="<?php echo $this->_config->get_integer('objectcache.lifetime'); ?>" size="8" /> seconds
        			<br /><span class="description">Determines the natural expiration time of unchanged cache items. The higher the value, the larger the cache.</span>
    			</td>
        	</tr>
        	<tr>
        		<th><label for="objectcache_file_gc">Garbage collection interval:</label></th>
        		<td>
        			<input id="objectcache_file_gc" type="text" name="objectcache.file.gc" value="<?php echo $this->_config->get_integer('objectcache.file.gc'); ?>" size="8" /> seconds
        			<br /><span class="description">If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.</span>
    			</td>
        	</tr>
        	<tr>
        		<th><label for="objectcache_groups_global">Global groups:</label></th>
        		<td>
        			<textarea id="objectcache_groups_global" name="objectcache.groups.global" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('objectcache.groups.global'))); ?></textarea>
					<br /><span class="description">Groups shared amongst sites in network mode.</span>
        		</td>
        	</tr>
        	<tr>
        		<th><label for="objectcache_groups_nonpersistent">Non-persistent groups:</label></th>
        		<td>
        			<textarea id="objectcache_groups_nonpersistent" name="objectcache.groups.nonpersistent" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('objectcache.groups.nonpersistent'))); ?></textarea>
					<br /><span class="description">Groups that should not be cached.</span>
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