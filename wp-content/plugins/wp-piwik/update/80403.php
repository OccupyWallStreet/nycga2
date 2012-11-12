<?php
// Capability read stats: Translate level to role
$aryTranslate = array(
	'level_10' => array('administrator' => true),
	'level_7' => array('editor' => true, 'administrator' => true),
	'level_2' => array('author' => true, 'editor' => true, 'administrator' => true),
	'level_1' => array('contributor' => true, 'author' => true, 'editor' => true, 'administrator' => true),
	'level_0' => array('subscriber' => true, 'contributor' => true, 'author' => true, 'editor' => true, 'administrator' => true)
);
$strDisplayToLevel = get_option('wp-piwik_displayto','level_10');
if (!is_array($strDisplayToLevel) && isset($aryTranslate[$strDisplayToLevel])) $aryDisplayToCap = $aryTranslate[$strDisplayToLevel];
else $aryDisplayToCap = array('administrator' => true);
// Build settings arrays
$aryDashboardWidgetRange = array(0 => false, 1 => 'yesterday', 2 => 'today', 3 => 'last30');
if (self::$bolWPMU) self::$aryGlobalSettings = array(
	'revision' 				=> get_site_option('wpmu-piwik_revision', 0),
	'add_tracking_code' 	=> true,
	'last_settings_update' 	=> get_site_option('wpmu-piwik_settingsupdate', time()),
	'piwik_token' 		=> get_site_option('wpmu-piwik_token', ''),
	'piwik_url'		=> get_site_option('wpmu-piwik_url', ''),
	'dashboard_widget' 	=> false,
	'capability_stealth' 	=> get_site_option('wpmu-piwik_filter', array()),
	'capability_read_stats' => $aryDisplayToCap,
	'piwik_shortcut' 	=> false,
);		
else self::$aryGlobalSettings = array(
	'revision' 		=> get_option('wp-piwik_revision',0),
	'add_tracking_code' 	=> get_option('wp-piwik_addjs'),
	'last_settings_update' 	=> get_option('wp-piwik_settingsupdate', time()),
	'piwik_token' 		=> get_option('wp-piwik_token', ''),
	'piwik_url' 		=> get_option('wp-piwik_url', ''),
	'dashboard_widget' 	=> $aryDashboardWidgetRange[get_option('wp-piwik_dbwidget', 0)],			
	'capability_stealth' 	=> get_option('wp-piwik_filter', array()),
	'capability_read_stats' => $aryDisplayToCap,
	'piwik_shortcut' 	=> get_option('wp-piwik_piwiklink',false),
);
$this->installSite(false);
// Remove deprecated option values
$aryRemoveOptions = array(
	'wp-piwik_disable_gapi','wp-piwik_displayto',
	'wp-piwik_revision','wp-piwik_addjs','wp-piwik_settingsupdate','wp-piwik_token',
	'wp-piwik_url','wp-piwik_dbwidget','wp-piwik_filter','wp-piwik_piwiklink'
);
foreach ($aryRemoveOptions as $strRemoveOption) {				
	if (self::$bolWPMU) delete_site_option($strRemoveOption);
	else delete_option($strRemoveOption);
}