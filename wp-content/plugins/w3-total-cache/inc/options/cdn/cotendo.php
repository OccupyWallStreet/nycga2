<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th style="width: 300px;"><label for="cdn_cotendo_username">Username:</label></th>
    <td>
        <input id="cdn_cotendo_username" class="w3tc-ignore-change" type="text" name="cdn.cotendo.username" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.cotendo.username')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="cdn_cotendo_password">Password:</label></th>
    <td>
        <input id="cdn_cotendo_password" class="w3tc-ignore-change" type="password" name="cdn.cotendo.password" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.cotendo.password')); ?>" size="60" />
    </td>
</tr>
<tr>
    <th><label for="cdn_cotendo_zones">Zones to purge:</label></th>
    <td>
        <textarea id="cdn_cotendo_zones" name="cdn.cotendo.zones" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('cdn.cotendo.zones'))); ?></textarea>
    </td>
</tr>
<tr>
	<th><label for="cdn_cotendo_ssl"><acronym title="Secure Sockets Layer">SSL</acronym> support:</label></th>
	<td>
		<select id="cdn_cotendo_ssl" name="cdn.cotendo.ssl">
			<option value="auto"<?php selected($this->_config->get_string('cdn.cotendo.ssl'), 'auto'); ?>>Auto (determine connection type automatically)</option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.cotendo.ssl'), 'enabled'); ?>>Enabled (always use SSL)</option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.cotendo.ssl'), 'disabled'); ?>>Disabled (always use HTTP)</option>
		</select>
        <br /><span class="description">Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.</span>
	</td>
</tr>
<tr>
    <th>Replace site's hostname with:</th>
    <td>
		<?php $cnames = $this->_config->get_array('cdn.cotendo.domain'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description">Enter the hostname provided by your <acronym>CDN</acronym> provider, this value will replace your site's hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.</span>
    </td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'cotendo', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test Cotendo" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
