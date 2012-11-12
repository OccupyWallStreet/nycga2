<?php
/**
 * Handles plugin installation,
 * envorinment sanity checks and initiates defaults.
 */
class Wdfb_Installer {

	var $data;

	var $_defaults = array (
		'api' => array (
	// Commented out, because this will probably won't be overriden in user settings.
	/*
			'api_key' => '',
			'app_key' => '',
			'secret_key' => '',
			'auth_tokens' => '', // Array
			'auth_accounts' => '', // Array
	*/
		),
		'connect' => array (
	/*
			'allow_facebook_registration' => 0,
			'force_facebook_registration' => 0,
	*/
		),
		'button' => array (
	/*
			'allow_facebook_button' => 0,
			'button_position' => 'top',
			'show_send_button' => 0,
			'button_appearance' => 'standard',
			'not_in_post_types' => array(),
	*/
		),
		'opengraph' => array (
	/*
			'use_opengraph' => 0,
			'always_use_image' => '',
			'fallback_image' => '',
	*/
		),
		'comments' => array (
	/*
			'import_fb_comments' => 0,
			'comment_limit' => 0,
			'notify_authors' => 0,
	*/
		),
		'autopost' => array (
	/*
			'allow_autopost' => 0,
	*/
		),
		'network' => array (),
		'widget_pack' => array (
	/*
			'albums_allowed' => 0,
			'events_allowed' => 0,
			'facepile_allowed' => 0,
			'likebox_allowed' => 0,
			'recommendations_allowed' => 0,
			'dashboard_permissions_allowed' => 0,
	*/
		),
	);

	function __construct() {
		$this->data =& Wdfb_OptionsRegistry::get_instance();
	}

	function Wdfb_Installer () {
		$this->__construct();
	}

	function check () {
		$me = new Wdfb_Installer;
		$me->install();
	}

	function install () {
		$sections = array_keys($this->_defaults);
		$api = get_site_option('wdfb_api');
		$propagate_api = isset($api['allow_propagation']) ? (int)$api['allow_propagation'] : false;

		foreach ($sections as $section) {
			$opts = get_option('wdfb_' . $section, false);
			$site_opts = get_site_option('wdfb_' . $section, false);
			if (!$site_opts || !is_array($site_opts)) {
				//update_site_option('wdfb_' . $section, $this->_defaults[$section]);
				$site_opts = $this->_defaults[$section];
			} else {
				$site_opts = array_merge($this->_defaults[$section], $site_opts);
			}
			if ('api' == $section && $propagate_api) {
				$opts = is_array($opts) ? $opts : array();
				$res = array_merge($site_opts, $opts);
				update_option('wdfb_' . $section, $res);
				$this->data->set_key('wdfb_' . $section, $res);
			} else {
				//$opts = is_array($opts) ? $opts : array();
				$opts = is_array($opts) ? $opts : $site_opts;
				$this->data->set_key('wdfb_' . $section, $opts);
			}
		}
	}

	/**
	 * @static
	 */
	function get_keys () {
		$me = new Wdfb_Installer;
		return array_keys($me->_defaults);
	}
}