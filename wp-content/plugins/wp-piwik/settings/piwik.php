<?php
$bolCURL = function_exists('curl_init');
$bolFOpen = ini_get('allow_url_fopen');
if (!$bolFOpen && !$bolCURL) {
?><tr>	
	<td colspan="2">
		<strong><?php _e('Error: cURL is not enabled and fopen is not allowed to open URLs. WP-Piwik won\'t be able to connect to Piwik.'); ?></strong>
	</td>
</tr><?php } else { ?><tr>
	<th colspan="2">
		<?php _e('To enable Piwik statistics, please enter', 'wp-piwik'); ?>:
		<ol>
			<li><?php _e('your Piwik base URL (like http://mydomain.com/piwik) or your Piwik server path (like /var/www/mydomain.com/httpdocs/piwik/)', 'wp-piwik'); ?></li>
			<li><?php _e('your personal Piwik authentification token. You can get the token on the API page inside your Piwik interface. It looks like &quot;1234a5cd6789e0a12345b678cd9012ef&quot;.', 'wp-piwik'); ?></li>
		</ol>
		<?php _e('No idea what I\'m talking about?', 'wp-piwik'); ?> <a href="http://peepbo.de/board/viewtopic.php?f=5&t=10"><?php _e('Get help.', 'wp-piwik'); ?></a>
	<?php if (!is_plugin_active_for_network('wp-piwik/wp-piwik.php')) { ?>
		<p><?php _e('<strong>Important note:</strong> If you do not host this blog on your own, your site admin is able to get your auth token from the database.', 'wp-piwik'); ?></p>
	<?php } ?>
	</th>
</tr><tr>
	<th><?php _e('Piwik URL', 'wp-piwik'); ?> (REST API):</th>
	<td>
		<input type="radio" name="wp-piwik_mode" onchange="javascript:$j('#wp-piwik_path,#wp-piwik_path-label').toggleClass('wp-piwik-input-hide');" value="http" <?php echo (self::$aryGlobalSettings['piwik_mode']=='http'?'checked="checked" ':''); ?>/>
		<input id="wp-piwik_url" name="wp-piwik_url" type="text" value="<?php echo self::$aryGlobalSettings['piwik_url']; ?>" />
		<label for="wp-piwik_url"></label>
	</td>
</tr><tr>
	<th><?php _e('Piwik path', 'wp-piwik'); ?> (PHP API, beta):</th>
	<td>
		<input type="radio" name="wp-piwik_mode" onchange="javascript:$j('#wp-piwik_path,#wp-piwik_path-label').toggleClass('wp-piwik-input-hide');" value="php" <?php echo (self::$aryGlobalSettings['piwik_mode']=='php'?'checked="checked" ':''); ?>/>
		<input <?php echo (self::$aryGlobalSettings['piwik_mode']!='php'?'class="wp-piwik-input-hide" ':''); ?>id="wp-piwik_path" name="wp-piwik_path" type="text" value="<?php echo self::$aryGlobalSettings['piwik_path']; ?>" />
		<label <?php echo (self::$aryGlobalSettings['piwik_mode']!='php'?'class="wp-piwik-input-hide" ':''); ?>id="wp-piwik_path-label" for="wp-piwik_path"><?php _e('If you like to use the PHP API and also to enable tracking by WP-Piwik, please enter your Piwik URL, too. Otherwise your tracking code may be erroneous.','wp-piwik'); ?> [<a href="http://dev.piwik.org/trac/ticket/3220">Details</a>]</label>
		<?php
			if (isset($_POST['wp-piwik_path']) && !empty($_POST['wp-piwik_path']) && realpath($_POST['wp-piwik_path']) === false)
				echo '<p class="wp-piwik-eyecatcher">'.__('Invalid path. Please enter the file path to Piwik.', 'wp-piwik').'</p>';
		?>
	</td>
</tr><tr>
	<th><?php _e('Auth token', 'wp-piwik'); ?>:</th>
	<td>
		<input name="wp-piwik_token" id="wp-piwik_token" type="text" value="<?php echo self::$aryGlobalSettings['piwik_token']; ?>" />
		<label for="wp-piwik_token"></label>
	</td>
</tr><?php if (!is_plugin_active_for_network('wp-piwik/wp-piwik.php')) { ?><tr>
	<th><?php _e('Auto config', 'wp-piwik'); ?>:</th>
	<td>
		<input name="wp-piwik_auto_site_config" id="wp-piwik_auto_site_config" value="1" type="checkbox"<?php echo (self::$aryGlobalSettings['auto_site_config']?' checked="checked"':'') ?>/>
		<label for="wp-piwik_auto_site_config"><?php _e('Check this to automatically choose your blog from your Piwik sites by URL. If your blog is not added to Piwik yet, WP-Piwik will add a new site.', 'wp-piwik') ?></label>
	</td>
</tr>
<?php 
if (!empty(self::$aryGlobalSettings['piwik_url']) && !empty(self::$aryGlobalSettings['piwik_token'])) { 
	$aryData = $this->callPiwikAPI('SitesManager.getSitesWithAtLeastViewAccess');
	if (empty($aryData)) {
		echo '<tr><td colspan="2">';
		self::showErrorMessage(__('Please check URL and auth token. You need at least view access to one site.', 'wp-piwik'));
		echo '</td></tr>';
	}
	elseif (isset($aryData['result']) && $aryData['result'] == 'error') {
		echo '<tr><td colspan="2">';
		self::showErrorMessage($aryData['message']);
		echo '</td></tr>';
	} else if (!self::$aryGlobalSettings['auto_site_config']) {
		echo '<tr><th>'.__('Choose site', 'wp-piwik').':</th><td>';
		echo '<select name="wp-piwik_siteid" id="wp-piwik_siteid">';
		$aryOptions = array();
		foreach ($aryData as $arySite)
			$aryOptions[$arySite['name'].'#'.$arySite['idsite']] = '<option value="'.$arySite['idsite'].
				'"'.($arySite['idsite']==self::$arySettings['site_id']?' selected="selected"':'').
				'>'.htmlentities($arySite['name'], ENT_QUOTES, 'utf-8').
				'</option>';
		ksort($aryOptions);
		foreach ($aryOptions as $strOption) echo $strOption;
			echo '</select></td></tr>';
	} else {
		if (empty(self::$arySettings['site_id']))
			$this->addPiwikSite();
		echo '<tr><th>'.__('Determined site', 'wp-piwik').':</th><td>';
		echo '<div class="input-text-wrap">';
		if (is_array(self::$arySettings['site_id']) && self::$arySettings['site_id']['result'] == 'error')
			self::showErrorMessage(self::$arySettings['site_id']['message']);
		else foreach ($aryData as $arySite) 
			if ($arySite['idsite'] == self::$arySettings['site_id']) {echo '<em>'.htmlentities($arySite['name'], ENT_QUOTES, 'utf-8').'</em>'; break;}		
		echo '<input type="hidden" name="wp-piwik_siteid" id="wp-piwik_siteid" value="'.(int)self::$arySettings['site_id'].'" /></td></tr>';
	}
}
}}
// Expert settings (cURL only)
?><tr>
	<th colspan="2"><strong><?php _e('Expert Settings', 'wp-piwik'); ?>:</strong></th>
</tr><tr>
	<th><label><?php _e('Connection timeout', 'wp-piwik'); ?>:</label></th>
	<td>
		<input style="width:50px;" type="text" name="wp-piwik_timeout" value="<?php echo self::$aryGlobalSettings['connection_timeout']; ?>" /> 
	</td>
</tr>
<?php if (function_exists('curl_init')) { ?>
<tr>
	<th><label <?php echo (self::$aryGlobalSettings['piwik_mode']=='php'?'class="wp-piwik-input-hide" ':''); ?>id="wp-piwik_disable_ssl_verify-label"><?php _e('Disable SSL peer verification', 'wp-piwik'); ?>:</label></th>
	<td>
		<input <?php echo (self::$aryGlobalSettings['piwik_mode']=='php'?'class="wp-piwik-input-hide" ':''); ?>id="wp-piwik_disable_ssl_verify" name="wp-piwik_disable_ssl_verify" type="checkbox"<?php echo (self::$aryGlobalSettings['disable_ssl_verify']?'checked="checked"':''); ?> /> (<?php _e('not recommended','wp-piwik'); ?>)
	</td>
</tr><tr>
	<th><label><?php _e('User agent', 'wp-piwik'); ?>:</label></th>
	<td>
		<input type="radio" onchange="javascript:$j('#wp-piwik-useragent').toggleClass('readonly="readonly"');" name="wp-piwik_useragent" value="php" <?php echo (self::$aryGlobalSettings['piwik_useragent']=='php'?'checked="checked" ':''); ?>/> PHP default (<?php echo ini_get('user_agent'); ?>)
	</td>
</tr><tr>
	<th></th>
	<td>
		<input type="radio" onchange="javascript:$j('#wp-piwik-useragent').toggleClass('wp-piwik-useragent-disable');" name="wp-piwik_useragent" value="own" <?php echo (self::$aryGlobalSettings['piwik_useragent']=='own'?'checked="checked" ':''); ?>/> <input type="text" id="wp-piwik-useragent" name="wp-piwik_useragent_string" value="<?php echo self::$aryGlobalSettings['piwik_useragent_string']; ?>" />
	</td>
</tr>
<?php } else { ?>
<tr>
	<td colspan="2"><?php _e('Further expert settings require cURL. See <a href="http://www.php.net/manual/curl.setup.php">PHP manual</a>', 'wp-piwik'); ?>.</td>
</tr>
<?php }