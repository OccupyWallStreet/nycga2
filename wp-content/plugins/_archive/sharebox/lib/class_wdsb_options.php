<?php
/**
 * Handles options access.
 */
class Wdsb_Options {
	var $_data;

	function __construct () {
		$this->_populate();
	}

	/**
	 * Gets a single option from options storage.
	 */
	function get_option ($key) {
		return @$this->_data[$key];
	}

	/**
	 * Sets all stored options.
	 */
	function set_options ($opts) {
		return WP_NETWORK_ADMIN ? update_site_option('wdsb', $opts) : update_option('wdsb', $opts);
	}

	/**
	 * Populates options key for storage.
	 *
	 */
	private function _populate () {
		$site_opts = get_site_option('wdsb');
		$site_opts = is_array($site_opts) ? $site_opts : array();

		$opts = get_option('wdsb');
		$opts = is_array($opts) ? $opts : $site_opts;

		$keys = array_unique(array_merge(array_keys($site_opts), array_keys($opts)));

		$result = array();
		foreach ($keys as $key) {
			$sopt = @$site_opts[$key];
			$opt = @$opts[$key];
			if (is_array($sopt) || is_array($opt)) {
				$result[$key] = $opt ? $opt : $sopt;
			} else {
				$result[$key] = (isset($opts[$key]) && $opt != $sopt) ? $opt : $sopt;
			}
		}

		$this->_data = $result;
		//$this->_data = array_merge($site_opts, $opts);
	}

}