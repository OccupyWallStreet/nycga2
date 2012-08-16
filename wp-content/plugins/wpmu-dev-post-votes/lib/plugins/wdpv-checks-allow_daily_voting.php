<?php
/*
Plugin Name: Allow daily voting
Description: Activate this add-on to allow your visitors to vote once a day, instead of voting once for all.
Plugin URI: http://premium.wpmudev.org/project/post-voting-plugin
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Wdpv_Checks_AllowDailyVoting {

	private function __construct () {}

	public static function serve () {
		$me = new Wdpv_Checks_AllowDailyVoting;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_filter('wdpv-cookie-expiration_time', array($this, 'reset_cookie_expiration'));
		add_filter('wdpv-sql-where-user_id_check', array($this, 'add_timeframe_condition'));
		add_filter('wdpv-sql-where-user_ip_check', array($this, 'add_timeframe_condition'));
	}

	function reset_cookie_expiration ($time) {
		return time() + 24*3600;
	}

	function add_timeframe_condition ($where) {
		$yesterday = date('Y-m-d', strtotime("-1 days"));
		return "{$where} AND date > '{$yesterday}'";
	}
}

if (is_admin()) Wdpv_Checks_AllowDailyVoting::serve();