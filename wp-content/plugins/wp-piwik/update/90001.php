<?php
$aryWPMUConfig = get_site_option('wpmu-piwik_global-settings',false);
if (is_plugin_active_for_network('wp-piwik/wp-piwik.php') && $aryWPMUConfig) {
	self::$aryGlobalSettings = $aryWPMUConfig;
	delete_site_option('wpmu-piwik_global-settings');
	self::$aryGlobalSettings['auto_site_config'] = true;
} else self::$aryGlobalSettings['auto_site_config'] = false;
self::$aryGlobalSettings['dashboard_seo'] = false;
self::$aryGlobalSettings['stats_seo'] = false;
self::$aryGlobalSettings['track_404'] = self::$arySettings['track_404'];
self::$aryGlobalSettings['track_compress'] = false;
self::$aryGlobalSettings['track_post'] = false;