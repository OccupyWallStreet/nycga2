<?php
/**
 * Installs the database, if it's not already present.
 */
class Wdqs_Installer {

	/**
	 * @public
	 * @static
	 */
	function check () {
		$is_installed = get_site_option('wdqs', false);
		if (!$is_installed) Wdqs_Installer::install();
	}

	/**
	 * @private
	 * @static
	 */
	function install () {
		$me = new Wdqs_Installer;
		$me->create_default_options();
	}

	/**
	 * @private
	 */
	function create_default_options () {
		update_site_option('wdqs', array (
			'show_on_public_pages' => '0',
			'show_on_front_page' => '0',
			'show_on_dashboard' => '1',
		));
	}
}