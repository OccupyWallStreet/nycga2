<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<p>
	Content Delivery Network support via
	<strong><?php echo w3_get_engine_name($this->_config->get_string('cdn.engine')); ?></strong>
	is currently <span class="w3tc-<?php if ($cdn_enabled): ?>enabled">enabled<?php else: ?>disabled">disabled<?php endif; ?></span>.
</p>

<?php if ($cdn_mirror): ?>
<p>
	Maximize <acronym title="Content Delivery Network">CDN</acronym> usage by <input id="cdn_rename_domain" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="modify attachment URLs" /> or
	<input id="cdn_import_library" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="importing attachments into the Media Library" />.
    <?php if (w3_can_cdn_purge($cdn_engine)): ?>
    <input id="cdn_purge" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Purge" /> objects from the <acronym title="Content Delivery Network">CDN</acronym> using this tool.
    <?php endif; ?>
</p>
<?php else: ?>
<p>
	Prepare the <acronym title="Content Delivery Network">CDN</acronym> by:
	<input id="cdn_import_library" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="importing attachments into the Media Library" />.
	Check <input id="cdn_queue" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="unsuccessful file transfers" /> if some objects appear to be missing.
    <?php if (w3_can_cdn_purge($cdn_engine)): ?>
    <input id="cdn_purge" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Purge" /> objects from the <acronym title="Content Delivery Network">CDN</acronym> if needed.
    <?php endif; ?>
	<input id="cdn_rename_domain" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Modify attachment URLs" /> if the domain name of your site has ever changed.
</p>
<?php endif; ?>

<form id="cdn_form" action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
		<?php echo $this->postbox_header('General'); ?>
        <table class="form-table">
            <tr>
                <th<?php if ($cdn_mirror): ?> colspan="2"<?php else: ?> style="width: 300px;"<?php endif; ?>>
                    <input type="hidden" name="cdn.uploads.enable" value="0" />
                    <label><input type="checkbox" name="cdn.uploads.enable" value="1"<?php checked($this->_config->get_boolean('cdn.uploads.enable'), true); ?> /> Host attachments</label><br />
                    <span class="description">If checked, all attachments will be hosted with the <acronym title="Content Delivery Network">CDN</acronym>.</span>
                </th>
                <?php if (! $cdn_mirror): ?>
                <td>
	                <input id="cdn_export_library" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Upload attachments" />
                </td>
                <?php endif; ?>
            </tr>
        	<tr>
        		<th<?php if ($cdn_mirror): ?> colspan="2"<?php endif; ?>>
        			<input type="hidden" name="cdn.includes.enable" value="0" />
        			<label><input type="checkbox" name="cdn.includes.enable" value="1"<?php checked($this->_config->get_boolean('cdn.includes.enable'), true); ?> /> Host wp-includes/ files</label><br />
    				<span class="description">If checked, WordPress static core file types specified in the "wp-includes file types to upload" field below will be hosted with the <acronym title="Content Delivery Network">CDN</acronym>.</span>
        		</th>
        		<?php if (! $cdn_mirror): ?>
        		<td>
        			<input class="button cdn_export {type: 'includes', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Upload includes files" />
        		</td>
        		<?php endif; ?>
        	</tr>
        	<tr>
        		<th<?php if ($cdn_mirror): ?> colspan="2"<?php endif; ?>>
        			<input type="hidden" name="cdn.theme.enable" value="0" />
        			<label><input type="checkbox" name="cdn.theme.enable" value="1"<?php checked($this->_config->get_boolean('cdn.theme.enable'), true); ?> /> Host theme files</label><br />
    				<span class="description">If checked, all theme file types specified in the "theme file types to upload" field below will be hosted with the <acronym title="Content Delivery Network">CDN</acronym>.</span>
        		</th>
        		<?php if (! $cdn_mirror): ?>
        		<td>
    				<input class="button cdn_export {type: 'theme', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Upload theme files" />
        		</td>
        		<?php endif; ?>
        	</tr>
        	<tr>
        		<th<?php if ($cdn_mirror): ?> colspan="2"<?php endif; ?>>
        			<input type="hidden" name="cdn.minify.enable" value="0"<?php if (!$minify_enabled): ?> disabled="disabled"<?php endif; ?> />
        			<label><input type="checkbox" name="cdn.minify.enable" value="1"<?php checked($this->_config->get_boolean('cdn.minify.enable'), true); ?><?php if (!$minify_enabled): ?> disabled="disabled"<?php endif; ?> /> Host minified <acronym title="Cascading Style Sheet">CSS</acronym> and <acronym title="JavaScript">JS</acronym> files</label><br />
    				<span class="description">If checked, minified <acronym>CSS</acronym> and <acronym>JS</acronym> files will be hosted with the <acronym title="Content Delivery Network">CDN</acronym>.</span>
        		</th>
        		<?php if (! $cdn_mirror): ?>
        		<td>
    				<input class="button cdn_export {type: 'minify', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Upload minify files"<?php if (!$minify_enabled): ?> disabled="disabled"<?php endif; ?> />
        		</td>
        		<?php endif; ?>
        	</tr>
        	<tr>
        		<th<?php if ($cdn_mirror): ?> colspan="2"<?php endif; ?>>
        			<input type="hidden" name="cdn.custom.enable" value="0" />
        			<label><input type="checkbox" name="cdn.custom.enable" value="1"<?php checked($this->_config->get_boolean('cdn.custom.enable'), true); ?> /> Host custom files</label><br />
    				<span class="description">If checked, any file names or paths specified in the "custom file list" field below will be hosted with the <acronym title="Content Delivery Network">CDN</acronym>.</span>
        		</th>
        		<?php if (! $cdn_mirror): ?>
        		<td>
    				<input class="button cdn_export {type: 'custom', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Upload custom files" />
        		</td>
        		<?php endif; ?>
        	</tr>
        	<?php if (! $cdn_mirror): ?>
        	<tr>
        		<th colspan="2">
        			<input type="hidden" name="cdn.force.rewrite" value="0" />
        			<label><input type="checkbox" name="cdn.force.rewrite" value="1"<?php checked($this->_config->get_boolean('cdn.force.rewrite'), true); ?> /> Force over-writing of existing files</label><br />
        			<span class="description">If modified files are not always detected and replaced, use this option to over-write them.</span>
        		</th>
        	</tr>
        	<?php endif; ?>
        	<tr>
        		<th colspan="2">
        			<input type="hidden" name="cdn.import.external" value="0" />
        			<label><input type="checkbox" name="cdn.import.external" value="1"<?php checked($this->_config->get_boolean('cdn.import.external'), true); ?> /> Import external media library attachments</label><br />
        			<span class="description">Download attachments hosted elsewhere into your media library and deliver them via <acronym title="Content Delivery Network">CDN</acronym>.</span>
        		</th>
        	</tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
	    <?php echo $this->postbox_footer(); ?>

		<?php echo $this->postbox_header('Configuration'); ?>
        <table class="form-table">
    		<?php
                if (w3_is_cdn_engine($cdn_engine)) {
                    include W3TC_INC_DIR . '/options/cdn/' . $cdn_engine . '.php';
    			}
    		?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
	    <?php echo $this->postbox_footer(); ?>

		<?php echo $this->postbox_header('Advanced'); ?>
        <table class="form-table">
            <tr>
                <th colspan="2">
                    <input type="hidden" name="cdn.reject.admins" value="0" />
                    <label><input type="checkbox" name="cdn.reject.admins" value="1"<?php checked($this->_config->get_boolean('cdn.reject.admins'), true); ?> /> Don't replace <acronym title="Uniform Resource Indicator">URL</acronym>s for logged in administrators</label><br />
                    <span class="description">Authenticated administrators will use the origin server exclusively when this option is selected.</span>
                </th>
            </tr>
        	<?php if (! $cdn_mirror): ?>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="minify.upload" value="0"<?php if ($this->_config->get_boolean('minify.auto')): ?> disabled="disabled"<?php endif; ?> />
                    <label><input type="checkbox" name="minify.upload" value="1"<?php checked($this->_config->get_boolean('minify.upload'), true); ?><?php if ($this->_config->get_boolean('minify.auto')): ?> disabled="disabled"<?php endif; ?> /> Automatically upload minify files</label><br />
                    <span class="description">If <acronym title="Content Delivery Network">CDN</acronym> is enabled (and not using the origin pull method), your minified files will be automatically uploaded.</span>
                </th>
            </tr>
        	<tr>
        		<th colspan="2">
        			<input type="hidden" name="cdn.autoupload.enabled" value="0" />
        			<label><input type="checkbox" name="cdn.autoupload.enabled" value="1"<?php checked($this->_config->get_boolean('cdn.autoupload.enabled'), true); ?> /> Export changed files automatically</label><br />
        			<span class="description">Automatically attempt to find and upload changed files.</span>
        		</th>
        	</tr>
            <tr>
                <th><label for="cdn_autoupload_interval">Auto upload interval:</label></th>
                <td>
                    <input id="cdn_autoupload_interval" type="text" name="cdn.autoupload.interval" value="<?php echo $this->_config->get_integer('cdn.autoupload.interval'); ?>" size="8" /> seconds<br />
                    <span class="description">Specify the interval between upload of changed files.</span>
                </td>
            </tr>
        	<tr>
        		<th><label for="cdn_limit_interval">Re-transfer cycle interval:</label></th>
        		<td>
        			<input id="cdn_limit_interval" type="text" name="cdn.queue.interval" value="<?php echo htmlspecialchars($this->_config->get_integer('cdn.queue.interval')); ?>" size="10" /> seconds<br />
        			<span class="description">The number of seconds to wait before upload attempt.</span>
        		</td>
        	</tr>
        	<tr>
        		<th><label for="cdn_limit_queue">Re-transfer cycle limit:</label></th>
        		<td>
        			<input id="cdn_limit_queue" type="text" name="cdn.queue.limit" value="<?php echo htmlspecialchars($this->_config->get_integer('cdn.queue.limit')); ?>" size="10" /><br />
        			<span class="description">Number of files processed per upload attempt.</span>
        		</td>
        	</tr>
        	<?php endif; ?>
        	<tr>
        		<th style="width: 300px;"><label for="cdn_includes_files">wp-includes file types to upload:</label></th>
        		<td>
        			<input id="cdn_includes_files" type="text" name="cdn.includes.files" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.includes.files')); ?>" size="100" /><br />
        			<span class="description">Specify the file types within the WordPress core to host with the <acronym title="Content Delivery Network">CDN</acronym>.</span>
        		</td>
        	</tr>
        	<tr>
        		<th><label for="cdn_theme_files">Theme file types to upload:</label></th>
        		<td>
        			<input id="cdn_theme_files" type="text" name="cdn.theme.files" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.theme.files')); ?>" size="100" /><br />
        			<span class="description">Specify the file types in the active theme to host with the <acronym title="Content Delivery Network">CDN</acronym>.</span>
        		</td>
        	</tr>
        	<tr>
        		<th><label for="cdn_import_files">File types to import:</label></th>
        		<td>
        			<input id="cdn_import_files" type="text" name="cdn.import.files" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.import.files')); ?>" size="100" /><br />
        			<span class="description">Automatically import files hosted with 3rd parties of these types (if used in your posts / pages) to your media library.</span>
        		</td>
        	</tr>
        	<tr>
        		<th><label for="cdn_custom_files">Custom file list:</label></th>
        		<td>
        			<textarea id="cdn_custom_files" name="cdn.custom.files" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('cdn.custom.files'))); ?></textarea><br />
        			<span class="description">Specify any files outside of theme or other common directories to host with the <acronym title="Content Delivery Network">CDN</acronym>.</span>
        		</td>
        	</tr>
        	<tr>
        		<th><label for="cdn_reject_ua">Rejected user agents:</label></th>
        		<td>
        			<textarea id="cdn_reject_ua" name="cdn.reject.ua" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('cdn.reject.ua'))); ?></textarea><br />
        			<span class="description">Specify user agents that should not access files hosted with the <acronym title="Content Delivery Network">CDN</acronym>.</span>
        		</td>
        	</tr>
        	<tr>
        		<th><label for="cdn_reject_files">Rejected files:</label></th>
        		<td>
        			<textarea id="cdn_reject_files" name="cdn.reject.files" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('cdn.reject.files'))); ?></textarea><br />
        			<span class="description">Specify the path of files that should not use the <acronym title="Content Delivery Network">CDN</acronym>.</span>
        		</td>
        	</tr>
        	<tr>
        		<th colspan="2">
        			<input type="hidden" name="set_cookie_domain_old" value="<?php echo (int) $set_cookie_domain; ?>" />
        			<input type="hidden" name="set_cookie_domain_new" value="0" />
        			<label><input type="checkbox" name="set_cookie_domain_new" value="1"<?php checked($set_cookie_domain, true); ?> /> Set cookie domain to &quot;<?php echo htmlspecialchars($cookie_domain); ?>&quot;</label>
					<br /><span class="description">If using subdomain for <acronym title="Content Delivery Network">CDN</acronym> functionality, this setting helps prevent new users from sending cookies in requests to the <acronym title="Content Delivery Network">CDN</acronym> subdomain.</span>
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
						<li>If using Amazon Web Services or Self-Hosted <acronym title="Content Delivery Network">CDN</acronym> types, enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression in the "Media &amp; Other Files" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.</li>
					</ul>
        		</th>
        	</tr>
        </table>
    	<?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>