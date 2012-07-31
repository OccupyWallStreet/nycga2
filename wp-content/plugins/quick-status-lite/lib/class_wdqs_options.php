<?php
/**
 * Network/site options related operations.
 */
class Wdqs_Options {

	private $_data;

	function __construct () {
		$site = get_site_option('wdqs', false);
		$site = $site ? $site : array();
		$blog = get_option('wdqs', false);
		$blog = $blog ? $blog : array();

		$this->_data = array_merge($site, $blog);
	}

	function get ($key, $default=false) {
		return isset($this->_data[$key]) ? $this->_data[$key] : false;
	}

	function set ($key, $value) {
		$this->_data[$key] = $value;
		return update_option('wdqs', $this->_data);
	}
}