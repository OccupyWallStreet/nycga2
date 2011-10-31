<?php if (!defined('W3TC')) die(); ?>
<tr>
	<th colspan="2">
		<input type="hidden" name="cdn.ftp.pasv" value="0" />
		<label><input id="cdn_ftp_pasv" type="checkbox" name="cdn.ftp.pasv" value="1"<?php checked($this->_config->get_boolean('cdn.ftp.pasv'), true); ?> /> Use passive <acronym title="File Transfer Protocol">FTP</acronym> mode</label><br />
		<span class="description">Enable this option only if there are connectivity issues, otherwise it's not recommended.</span>
	</th>
</tr>
<tr>
	<th style="width: 300px;"><label for="cdn_ftp_host"><acronym title="File Transfer Protocol">FTP</acronym> hostname:</label></th>
	<td>
		<input id="cdn_ftp_host" type="text" name="cdn.ftp.host" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.ftp.host')); ?>" size="30" /><br />
		<span class="description">Specify the server's address, e.g.: "ftp.domain.com". Try "127.0.0.1" if using a sub-domain on the same server as your site.</span>
	</td>
</tr>
<tr>
	<th><label for="cdn_ftp_user"><acronym title="File Transfer Protocol">FTP</acronym> username:</label></th>
	<td>
		<input id="cdn_ftp_user" class="w3tc-ignore-change" type="text" name="cdn.ftp.user" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.ftp.user')); ?>" size="30" />
	</td>
</tr>
<tr>
	<th><label for="cdn_ftp_pass"><acronym title="File Transfer Protocol">FTP</acronym> password:</label></th>
	<td>
		<input id="cdn_ftp_pass" class="w3tc-ignore-change" type="password" name="cdn.ftp.pass" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.ftp.pass')); ?>" size="30" />
	</td>
</tr>
<tr>
	<th><label for="cdn_ftp_path"><acronym title="File Transfer Protocol">FTP</acronym> path:</label></th>
	<td>
		<input id="cdn_ftp_path" type="text" name="cdn.ftp.path" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.ftp.path')); ?>" size="30" /><br />
		<span class="description">Specify the directory where files must be uploaded to be accessible in a web browser (the document root).</span>
	</td>
</tr>
<tr>
	<th><label for="cdn_ftp_ssl"><acronym title="Secure Sockets Layer">SSL</acronym> support:</label></th>
	<td>
		<select id="cdn_ftp_ssl" name="cdn.ftp.ssl">
			<option value="auto"<?php selected($this->_config->get_string('cdn.ftp.ssl'), 'auto'); ?>>Auto (determine connection type automatically)</option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.ftp.ssl'), 'enabled'); ?>>Enabled (always use SSL)</option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.ftp.ssl'), 'disabled'); ?>>Disabled (always use HTTP)</option>
		</select>
        <br /><span class="description">Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.</span>
	</td>
</tr>
<tr>
	<th>Replace site's hostname with:</th>
	<td>
		<?php $cnames = $this->_config->get_array('cdn.ftp.domain'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
		<br /><span class="description">Enter the hostname or CNAME(s) of your <acronym title="File Transfer Protocol">FTP</acronym> server configured above, these values will replace your site's hostname in the <acronym title="Hypertext Markup Language">HTML</acronym>.</span>
	</td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'ftp', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test FTP server" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
