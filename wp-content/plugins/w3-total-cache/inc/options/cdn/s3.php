<?php if (!defined('W3TC')) die(); ?>
<tr>
	<th style="width: 300px;"><label for="cdn_s3_key">Access key ID:</label></th>
	<td>
		<input id="cdn_s3_key" class="w3tc-ignore-change" type="text" name="cdn.s3.key" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.s3.key')); ?>" size="30" />
	</td>
</tr>
<tr>
	<th><label for="cdn_s3_secret">Secret key:</label></th>
	<td>
		<input id="cdn_s3_secret" class="w3tc-ignore-change" type="password" name="cdn.s3.secret" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.s3.secret')); ?>" size="60" />
	</td>
</tr>
<tr>
	<th><label for="cdn_s3_bucket">Bucket:</label></th>
	<td>
		<input id="cdn_s3_bucket" type="text" name="cdn.s3.bucket" value="<?php echo htmlspecialchars($this->_config->get_string('cdn.s3.bucket')); ?>" size="30" />
		<input class="button button-cdn-s3-bucket-location cdn_s3 {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Create bucket" />
	</td>
</tr>
<tr>
	<th><label for="cdn_s3_ssl"><acronym title="Secure Sockets Layer">SSL</acronym> support:</label></th>
	<td>
		<select id="cdn_s3_ssl" name="cdn.s3.ssl">
			<option value="auto"<?php selected($this->_config->get_string('cdn.s3.ssl'), 'auto'); ?>>Auto (determine connection type automatically)</option>
			<option value="enabled"<?php selected($this->_config->get_string('cdn.s3.ssl'), 'enabled'); ?>>Enabled (always use SSL)</option>
			<option value="disabled"<?php selected($this->_config->get_string('cdn.s3.ssl'), 'disabled'); ?>>Disabled (always use HTTP)</option>
		</select>
        <br /><span class="description">Some <acronym>CDN</acronym> providers may or may not support <acronym title="Secure Sockets Layer">SSL</acronym>, contact your vendor for more information.</span>
	</td>
</tr>
<tr>
	<th>Replace site's hostname with:</th>
	<td>
		<?php if (($cdn_s3_bucket = $this->_config->get_string('cdn.s3.bucket')) != ''): ?>
		    <?php echo htmlspecialchars($cdn_s3_bucket); ?>.s3.amazonaws.com
		<?php else: ?>
		    &lt;bucket&gt;.s3.amazonaws.com
		<?php endif; ?> or CNAME:
		<?php $cnames = $this->_config->get_array('cdn.s3.cname'); include W3TC_INC_DIR . '/options/cdn/common/cnames.php'; ?>
        <br /><span class="description">If you have already added a <a href="http://docs.amazonwebservices.com/AmazonS3/latest/DeveloperGuide/VirtualHosting.html#VirtualHostingCustomURLs" target="_blank">CNAME</a> to your <acronym title="Domain Name System">DNS</acronym> Zone, enter it here.</span>
	</td>
</tr>
<tr>
	<th colspan="2">
        <input id="cdn_test" class="button {type: 's3', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test S3 upload" /> <span id="cdn_test_status" class="w3tc-status w3tc-process"></span>
    </th>
</tr>
