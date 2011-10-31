<?php if (!defined('W3TC')) die(); ?>
<tr>
	<th style="width: 300px;"><label for="cdn_rscf_user">Username:</label></th>
	<td>
		<input id="cdn_rscf_user" class="w3tc-ignore-change" type="text" name="cdn.rscf.user" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.rscf.user')); ?>" size="30" />
	</td>
</tr>
<tr>
	<th><label for="cdn_rscf_key"><acronym title="Application Programming Interface">API</acronym> key:</label></th>
	<td>
		<input id="cdn_rscf_key" class="w3tc-ignore-change" type="password" name="cdn.rscf.key" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.rscf.key')); ?>" size="60" />
	</td>
</tr>
<tr>
	<th><label for="cdn_rscf_location">Location:</label></th>
	<td>
        <select id="cdn_rscf_location" name="cdn.rscf.location">
            <option value="us"<?php echo selected($this->_config->get_string('cdn.rscf.location'), 'us'); ?>>US</option>
            <option value="uk"<?php echo selected($this->_config->get_string('cdn.rscf.location'), 'uk'); ?>>UK</option>
        </select>
	</td>
</tr>
<tr>
	<th><label for="cdn_rscf_container">Container:</label></th>
	<td>
		<input id="cdn_rscf_container" type="text" name="cdn.rscf.container" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.rscf.container')); ?>" size="30" />
		<input id="cdn_create_container" class="button {type: 'rscf', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Create container" />
		<span id="cdn_create_container_status" class="w3tc-status w3tc-process"></span>
	</td>
</tr>
<tr>
	<th><label for="cdn_rscf_ssl"><acronym title="Secure Sockets Layer">SSL</acronym> support:</label></th>
	<td>
		<select id="cdn_rscf_ssl" name="cdn.rscf.ssl">
			<option value="auto"<?php selected($this->_config->get_string('cdn.rscf.ssl'), 'auto'); ?>>Auto (determine connection type automatically)</option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.rscf.ssl'), 'enabled'); ?>>Enabled (always use SSL)</option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.rscf.ssl'), 'disabled'); ?>>Disabled (always use HTTP)</option>
		</select>
        <br /><span class="description">Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.</span>
	</td>
</tr>
<tr>
    <th>Replace site's hostname with:</th>
    <td>
		<?php $cnames = $this->_config->get_array('cdn.rscf.cname'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description">Enter the hostname provided by Rackspace Cloud Files, this value will replace your site's hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.</span>
    </td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'rscf', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test Cloud Files upload" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
