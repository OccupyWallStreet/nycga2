<?php
$bolCURL = function_exists('curl_init');
$bolFOpen = ini_get('allow_url_fopen');
if (!$bolFOpen && !$bolCURL) {
?><tr>	
	<td colspan="2">
		<strong><?php _e('Error: cURL is not enabled and fopen is not allowed to open URLs. WP-Piwik won\'t be able to connect to Piwik.'); ?></strong>
	</td>
</tr><?php } else { ?>
<tr><td colspan="2"><?php _e('Add tracking code', 'wp-piwik'); ?>: &nbsp; 
	<input 
		onchange="javascript:$j('#wp-piwik-tracking-settings').toggleClass('wp-piwik-form-table-hide');"
	type="checkbox" value="1" id="wp-piwik_addjs" name="wp-piwik_addjs"<?php echo (self::$aryGlobalSettings['add_tracking_code']?' checked="checked"':''); ?> />
	<label for="wp-piwik_addjs"><?php _e('If your template uses wp_footer(), WP-Piwik can automatically add the Piwik javascript code to your blog.', 'wp-piwik'); ?></label>
</td></tr>
</table>
<?php 
if (self::$aryGlobalSettings['add_tracking_code']) {
	$strJavaScript = $this->callPiwikAPI('SitesManager.getJavascriptTag');
	if (is_array($strJavaScript)) {
		if (isset($strJavaScript['result']) && $strJavaScript['result'] == 'error')
			self::showErrorMessage(__($strJavaScript['message'],'wp-piwik'));
	} else {	
		// Save javascript code
		self::$arySettings['tracking_code'] = $strJavaScript;
	}
	self::saveSettings();
}
?>
<table id="wp-piwik-tracking-settings" class="wp-piwik-form-table form-table<?php echo (!self::$aryGlobalSettings['add_tracking_code']?' wp-piwik-form-table-hide':''); ?>">
<tr><th><?php _e('Tracking code preview', 'wp-piwik'); ?>:</th><td>
<textarea id="wp-piwik_jscode" name="wp-piwik_jscode" readonly="readonly" rows="13" cols="100">
<?php echo (is_plugin_active_for_network('wp-piwik/wp-piwik.php')?'*** SITE SPECIFIC EXAMPLE CODE ***'."\n":'').htmlentities(self::$arySettings['tracking_code']); ?>
</textarea>
</td></tr>

<tr><th><?php _e('Default tracking', 'wp-piwik'); ?>:</th><td>
	<input type="radio" value="0" id="wp-piwik_default" name="wp-piwik_trackingmode"<?php echo (self::$aryGlobalSettings['track_mode']==0?' checked="checked"':''); ?> />
	<label for="wp-piwik_compress"><?php _e('WP-Piwik uses the Piwik default tracking code.', 'wp-piwik'); ?> <a href="http://demo.piwik.org/js/README">js/README</a>.</label>
</td></tr>
<tr><th><?php _e('Use js/index.php', 'wp-piwik'); ?>:</th><td>
	<input type="radio" value="1" id="wp-piwik_compress" name="wp-piwik_trackingmode"<?php echo (self::$aryGlobalSettings['track_mode']==1?' checked="checked"':''); ?> />
	<label for="wp-piwik_compress"><?php _e('WP-Piwik can automatically use js/index.php instead of piwik.js and piwik.php. See', 'wp-piwik'); ?> <a href="http://demo.piwik.org/js/README">js/README</a>.</label>
</td></tr>
<tr><th><?php _e('Use proxy script', 'wp-piwik'); ?>:</th><td>
	<input type="radio" value="2" id="wp-piwik_proxy" name="wp-piwik_trackingmode"<?php echo (self::$aryGlobalSettings['track_mode']==2?' checked="checked"':''); ?> />
	<label for="wp-piwik_compress"><?php _e('WP-Piwik will use the piwik.php proxy script. See', 'wp-piwik'); ?> <a href="http://piwik.org/faq/how-to/#faq_132">Piwik FAQ</a>.</label>
</td></tr>


<tr><th><?php _e('Disable cookies', 'wp-piwik'); ?>:</th><td>
	<input type="checkbox" value="1" id="wp-piwik_disable_cookies" name="wp-piwik_disable_cookies"<?php echo (self::$aryGlobalSettings['disable_cookies']?' checked="checked"':''); ?> />
	<label for="wp-piwik_disable_cookies"><?php echo _e('Disable all tracking cookies for a visitor.', 'wp-piwik'); ?></label>
</td></tr>

<tr><th><?php _e('Track search', 'wp-piwik'); ?>:</th><td>
	<input type="checkbox" value="1" id="wp-piwik_search" name="wp-piwik_search"<?php echo (self::$aryGlobalSettings['track_search']?' checked="checked"':''); ?> />
	<label for="wp-piwik_search"><?php echo _e('Use Piwik\'s advanced Site Search Analytics feature. See', 'wp-piwik'); ?> <a href="http://piwik.org/docs/javascript-tracking/#toc-tracking-internal-search-keywords-categories-and-no-result-search-keywords">Piwik documentation</a>.</label>
</td></tr>

<tr><th><?php _e('Track 404', 'wp-piwik'); ?>:</th><td>
	<input type="checkbox" value="1" id="wp-piwik_404" name="wp-piwik_404"<?php echo (self::$aryGlobalSettings['track_404']?' checked="checked"':''); ?> />
	<label for="wp-piwik_404"><?php echo _e('WP-Piwik can automatically add a 404-category to track 404-page-visits.', 'wp-piwik'); ?></label>
</td></tr>
<tr><th><?php _e('Avoid mod_security', 'wp-piwik'); ?>:</th><td>
	<input type="checkbox" value="1" id="wp-piwik_reqpost" name="wp-piwik_reqpost"<?php echo (self::$aryGlobalSettings['track_post']?' checked="checked"':''); ?> />
	<label for="wp-piwik_reqpost"><?php _e('WP-Piwik can automatically force the Tracking Code to sent data in POST. See', 'wp-piwik'); ?> <a href="http://piwik.org/faq/troubleshooting/#faq_100">Piwik FAQ</a>.</label>
</td></tr>
<tr><th><?php _e('Tracking filter', 'wp-piwik'); ?>:</th><td>
<?php
	global $wp_roles;
	$aryFilter = self::$aryGlobalSettings['capability_stealth'];
	foreach($wp_roles->role_names as $strKey => $strName)  {
		echo '<input type="checkbox" '.(isset($aryFilter[$strKey]) && $aryFilter[$strKey]?'checked="checked" ':'').'value="1" name="wp-piwik_filter['.$strKey.']" /> '.$strName.' &nbsp; ';
	}
?><br><?php _e('Choose users by user role you do <strong>not</strong> want to track.','wp-piwik'); ?></td></tr>
</table>
<table class="wp-piwik-form-table form-table">
<?php } ?>