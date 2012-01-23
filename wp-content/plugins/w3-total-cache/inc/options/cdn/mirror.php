<?php if (!defined('W3TC')) die(); ?>
<tr>
	<th><label for="cdn_mirror_ssl"><acronym title="Secure Sockets Layer">SSL</acronym> support:</label></th>
	<td>
		<select id="cdn_mirror_ssl" name="cdn.mirror.ssl">
			<option value="auto"<?php selected($this->_config->get_string('cdn.mirror.ssl'), 'auto'); ?>>Auto (determine connection type automatically)</option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.mirror.ssl'), 'enabled'); ?>>Enabled (always use SSL)</option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.mirror.ssl'), 'disabled'); ?>>Disabled (always use HTTP)</option>
		</select>
        <br /><span class="description">Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.</span>
	</td>
</tr>
<tr>
	<th>Replace site's hostname with:</th>
	<td>
		<?php $cnames = $this->_config->get_array('cdn.mirror.domain'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
		<br /><span class="description">Enter the hostname provided by your <acronym>CDN</acronym> provider, this value will replace your site's hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.</span>
	</td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'mirror', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test Mirror" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
