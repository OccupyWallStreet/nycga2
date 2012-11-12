<?php
$bolCURL = function_exists('curl_init');
$bolFOpen = ini_get('allow_url_fopen');
if (!$bolFOpen && !$bolCURL) {
?><tr>	
	<td colspan="2">
		<strong><?php _e('Error: cURL is not enabled and fopen is not allowed to open URLs. WP-Piwik won\'t be able to connect to Piwik.'); ?></strong>
	</td>
</tr><?php } else { ?>
<tr><th>Piwik <?php _e('Default date', 'wp-piwik'); ?>:</th><td>
	<select id="wp-piwik_default_date" name="wp-piwik_default_date">
		<option value="yesterday"<?php echo (self::$aryGlobalSettings['default_date'] == 'yesterday'?' selected="selected"':'');?>> <?php _e('yesterday', 'wp-piwik');?></option>
		<option value="today"<?php echo (self::$aryGlobalSettings['default_date'] == 'today'?' selected="selected"':'');?>> <?php _e('today', 'wp-piwik');?></option>
	</select>
	<label for="wp-piwik_default_date"><?php echo _e('Default date shown on statistics page.', 'wp-piwik'); ?></label>
</td></tr>
<tr><th><?php _e('Home Dashboard', 'wp-piwik'); ?>:</th><td>
	<select id="wp-piwik_dbwidget" name="wp-piwik_dbwidget">
		<option value="0"'<?php echo (!self::$aryGlobalSettings['dashboard_widget']?' selected="selected"':''); ?>><?php _e('Hide overview', 'wp-piwik'); ?></option>
		<option value="yesterday"<?php echo (self::$aryGlobalSettings['dashboard_widget'] == 'yesterday'?' selected="selected"':''); ?>><?php echo __('Show overview','wp-piwik').' ('.__('yesterday', 'wp-piwik').')'; ?></option>
		<option value="today"<?php echo (self::$aryGlobalSettings['dashboard_widget'] == 'today'?' selected="selected"':''); ?>><?php echo __('Show overview','wp-piwik').' ('.__('today', 'wp-piwik').')'; ?></option>
		<option value="last30"<?php echo (self::$aryGlobalSettings['dashboard_widget'] == 'last30'?' selected="selected"':''); ?>><?php echo __('Show overview','wp-piwik').' ('.__('last 30 days','wp-piwik').')'; ?></option>
	</select>
	<input type="checkbox" value="1" name="wp-piwik_dbchart" id="wp-piwik_dbchart"<?php echo (self::$aryGlobalSettings['dashboard_chart']?' checked="checked"':""); ?>/> <?php _e('Chart', 'wp-piwik'); ?>
	<input type="checkbox" value="1" name="wp-piwik_dbseo" id="wp-piwik_dbseo"<?php echo (self::$aryGlobalSettings['dashboard_seo']?' checked="checked"':""); ?>/> <?php _e('SEO <em>(slow!)</em>', 'wp-piwik'); ?>
	<br><?php _e('Configure WP-Piwik widgets to be shown on your WordPress Home Dashboard.', 'wp-piwik'); ?>
</td></tr>
<tr><th><?php _e('Show graph on WordPress Toolbar', 'wp-piwik'); ?>:</th><td>
	<input type="checkbox" value="1" id="wp-piwik_toolbar" name="wp-piwik_toolbar"<?php echo (self::$aryGlobalSettings['toolbar']?' checked="checked"':''); ?> />
	<label for="wp-piwik_toolbar"><?php echo _e('Display the last 30 days visitor stats on WordPress Toolbar.', 'wp-piwik'); ?></label>
</td></tr>
<tr><th><?php _e('SEO data', 'wp-piwik'); ?>:</th><td>
	<input type="checkbox" value="1" id="wp-piwik_statsseo" name="wp-piwik_statsseo"<?php echo (self::$aryGlobalSettings['stats_seo']?' checked="checked"':''); ?> />
	<label for="wp-piwik_statsseo"><?php echo _e('Display SEO ranking data on statistics page. <em>(Slow!)</em>', 'wp-piwik'); ?></label>
</td></tr>
<tr><th>Piwik <?php _e('Shortcut', 'wp-piwik'); ?>:</th><td>
	<input type="checkbox" value="1" id="wp-piwik_piwiklink" name="wp-piwik_piwiklink"<?php echo (self::$aryGlobalSettings['piwik_shortcut']?' checked="checked"':''); ?> />
	<label for="wp-piwik_piwiklink"><?php echo _e('Display a shortcut to Piwik itself.', 'wp-piwik'); ?></label>
</td></tr>
<tr><th>Piwik <?php _e('Display to', 'wp-piwik'); ?>:</th><td>
<?php
	global $wp_roles;
	foreach($wp_roles->role_names as $strKey => $strName)
		echo '<input name="wp-piwik_displayto['.$strKey.']" type="checkbox" value="1"'.(isset(self::$aryGlobalSettings['capability_read_stats'][$strKey]) && self::$aryGlobalSettings['capability_read_stats'][$strKey]?' checked="checked"':'').'/> '.$strName.' &nbsp; ';
?>
	<br><?php echo _e('Choose user roles allowed to see the statistics page.', 'wp-piwik'); ?>
</td></tr>
<tr><th><?php _e('Disable time limit', 'wp-piwik'); ?>:</th><td>
	<input type="checkbox" value="1" id="wp-piwik_disabletimelimit" name="wp-piwik_disabletimelimit"<?php echo (self::$aryGlobalSettings['disable_timelimit']?' checked="checked"':''); ?> />
	<label for="wp-piwik_disabletimelimit"><?php echo _e('Use set_time_limit(0) if stats page causes a time out.', 'wp-piwik'); ?></label>
</td></tr>
<?php } ?>