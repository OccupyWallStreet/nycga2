<?php
/**
 * Handles all Post Voting plugins.
 */
class Wdpv_PluginsHandler {

	public static function init () {
		define('WDPV_PLUGIN_PLUGINS_DIR', WDPV_PLUGIN_BASE_DIR . '/lib/plugins', true);
		self::load_active_plugins();
	}

	public static function get_active_plugins () {
		$active = get_site_option('wdpv_activated_plugins');
		$active = $active ? $active : array();

		return $active;
	}

	public static function load_active_plugins () {
		$active = self::get_active_plugins();

		foreach ($active as $plugin) {
			$path = self::plugin_to_path($plugin);
			if (!file_exists($path)) continue;
			else @require_once($path);
		}
	}

	public static function get_all_plugins () {
		$all = glob(WDPV_PLUGIN_PLUGINS_DIR . '/*.php');
		$all = $all ? $all : array();
		$ret = array();
		foreach ($all as $path) {
			$ret[] = pathinfo($path, PATHINFO_FILENAME);
		}
		return $ret;
	}

	public static function plugin_to_path ($plugin) {
		$plugin = str_replace('/', '_', $plugin);
		return WDPV_PLUGIN_PLUGINS_DIR . '/' . "{$plugin}.php";
	}

	public static function get_plugin_info ($plugin) {
		$path = self::plugin_to_path($plugin);
		$default_headers = array(
			'Name' => 'Plugin Name',
			'Author' => 'Author',
			'Description' => 'Description',
			'Plugin URI' => 'Plugin URI',
			'Version' => 'Version',
		);
		return get_file_data($path, $default_headers, 'plugin');
	}

	public static function activate_plugin ($plugin) {
		$active = self::get_active_plugins();
		if (in_array($plugin, $active)) return true; // Already active

		$active[] = $plugin;
		return update_site_option('wdpv_activated_plugins', $active);
	}

	public static function deactivate_plugin ($plugin) {
		$active = self::get_active_plugins();
		if (!in_array($plugin, $active)) return true; // Already deactivated

		$key = array_search($plugin, $active);
		if ($key === false) return false; // Haven't found it

		unset($active[$key]);
		return update_site_option('wdpv_activated_plugins', $active);
	}
}