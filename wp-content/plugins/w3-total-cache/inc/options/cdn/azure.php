<?php if (!defined('W3TC')) die(); ?>
<tr>
	<th style="width: 300px;"><label for="cdn_azure_user">Account name:</label></th>
	<td>
		<input id="cdn_azure_user" class="w3tc-ignore-change" type="text" name="cdn.azure.user" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.azure.user')); ?>" size="30" />
	</td>
</tr>
<tr>
	<th><label for="cdn_azure_key">Account key:</label></th>
	<td>
		<input id="cdn_azure_key" class="w3tc-ignore-change" type="password" name="cdn.azure.key" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.azure.key')); ?>" size="60" />
	</td>
</tr>
<tr>
	<th><label for="cdn_azure_container">Container:</label></th>
	<td>
		<input id="cdn_azure_container" type="text" name="cdn.azure.container" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.azure.container')); ?>" size="30" />
		<input id="cdn_create_container" class="button {type: 'azure', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Create container" />
        <span id="cdn_create_container_status" class="w3tc-status w3tc-process"></span>
	</td>
</tr>
<tr>
	<th><label for="cdn_s3_ssl"><acronym title="Secure Sockets Layer">SSL</acronym> support:</label></th>
	<td>
		<select id="cdn_s3_ssl" name="cdn.s3.ssl">
			<option value="auto"<?php selected($this->_config->get_string('cdn.azure.ssl'), 'auto'); ?>>Auto (determine connection type automatically)</option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.azure.ssl'), 'enabled'); ?>>Enabled (always use SSL)</option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.azure.ssl'), 'disabled'); ?>>Disabled (always use HTTP)</option>
		</select>
        <br /><span class="description">Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.</span>
	</td>
</tr>
<tr>
	<th>Replace site's hostname with:</th>
	<td>
		<?php if (($cdn_azure_user = $this->_config->get_string('cdn.azure.user')) != ''): ?>
		    <?php echo htmlspecialchars($cdn_azure_user); ?>.blob.core.windows.net
		<?php else: ?>
		    &lt;account name&gt;.blob.core.windows.net
		<?php endif; ?> or CNAME:
		<?php $cnames = $this->_config->get_array('cdn.azure.cname'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
	</td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'azure', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test Microsoft Azure Storage upload" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
