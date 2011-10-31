<?php if (!defined('W3TC')) die(); ?>
<tr>
	<th style="width: 300px;"><label for="cdn_cf_key">Access key ID:</label></th>
	<td>
		<input id="cdn_cf_key" class="w3tc-ignore-change" type="text" name="cdn.cf.key" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.cf.key')); ?>" size="30" />
	</td>
</tr>
<tr>
	<th><label for="cdn_cf_secret">Secret key:</label></th>
	<td>
		<input id="cdn_cf_secret" class="w3tc-ignore-change" type="password" name="cdn.cf.secret" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.cf.secret')); ?>" size="60" />
	</td>
</tr>
<tr>
	<th><label for="cdn_cf_bucket">Bucket:</label></th>
	<td>
		<input id="cdn_cf_bucket" type="text" name="cdn.cf.bucket" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.cf.bucket')); ?>" size="30" />
		<input class="button button-cdn-cf-bucket-location cdn_cf {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Create bucket &amp; distribution" />
	</td>
</tr>
<tr>
	<th><label for="cdn_cf_ssl"><acronym title="Secure Sockets Layer">SSL</acronym> support:</label></th>
	<td>
		<select id="cdn_cf_ssl" name="cdn.cf.ssl">
			<option value="auto"<?php selected($this->_config->get_string('cdn.cf.ssl'), 'auto'); ?>>Auto (determine connection type automatically)</option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.cf.ssl'), 'enabled'); ?>>Enabled (always use SSL)</option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.cf.ssl'), 'disabled'); ?>>Disabled (always use HTTP)</option>
		</select>
        <br /><span class="description">Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.</span>
	</td>
</tr>
<tr>
	<th><label for="cdn_cf_id">Replace site's hostname with:</label></th>
	<td>
		<input id="cdn_cf_id" type="text" name="cdn.cf.id" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.cf.id')); ?>" size="18" style="text-align: right;" />.cloudfront.net or CNAME:
		<?php $cnames = $this->_config->get_array('cdn.cf.cname'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
		<br /><span class="description">If you have already added a <a href="http://docs.amazonwebservices.com/AmazonCloudFront/latest/DeveloperGuide/index.html?CNAMEs.html" target="_blank">CNAME</a> to your <acronym title="Domain Name System">DNS</acronym> Zone, enter it here.</span>
	</td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 'cf', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test S3 upload &amp; CloudFront distribution" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
