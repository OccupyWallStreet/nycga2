<?php
/**
 * Installs the database, if it's not already present.
 */
class Wdsb_Installer {

	/**
	 * @public
	 * @static
	 */
	function check () {
		$is_installed = get_site_option('wdsb', false);
		$is_installed = $is_installed ? $is_installed : get_option('wdsb', false);
		if (!$is_installed) Wdsb_Installer::install();
	}

	/**
	 * @private
	 * @static
	 */
	function install () {
		$me = new Wdsb_Installer;
		$me->create_default_options();
	}

	/**
	 * @private
	 */
	function create_default_options () {
		update_site_option('wdsb', array (
			'services' => array('google', 'facebook', 'twitter', 'stumble_upon'),
			'min_width' => '1200',
		));
	}
}