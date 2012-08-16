<?php
/**
 * Handles options access.
 */
class Wdpv_Options {
	var $timeframes;

	function Wdpv_Options () { $this->__construct(); }

	function __construct () {
		$this->timeframes = array(
			'this_week' => __('This week', 'wdpv'),
			'last_week' => __('Last week', 'wdpv'),
			'this_month' => __('This month', 'wdpv'),
			'last_month' => __('Last month', 'wdpv'),
			'this_year' => __('This year', 'wdpv'),
		);
	}

	/**
	 * Gets a single option from options storage.
	 */
	function get_option ($key) {
		//$opts = WP_ALLOW_MULTISITE ? get_site_option('wdpv') : get_option('wdpv');
		$opts = get_option('wdpv');
		return @$opts[$key];
	}

	/**
	 * Sets all stored options.
	 */
	function set_options ($opts) {
		return WP_NETWORK_ADMIN ? update_site_option('wdpv', $opts) : update_option('wdpv', $opts);
	}

	/**
	 * Populates options key for storage.
	 *
	 * @static
	 */
	function populate () {
		$site_opts = get_site_option('wdpv');
		$site_opts = is_array($site_opts) ? $site_opts : array();

		$opts = get_option('wdpv');
		$opts = is_array($opts) ? $opts : array();

		$res = array_merge($site_opts, $opts);
		update_option('wdpv', $res);
	}

}