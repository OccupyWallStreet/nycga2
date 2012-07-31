<?php

class Wdfb_Tutorial {
	
	private $_tutorial;
	private $_steps = array(
		'intro',
		'api', 'api_begin', 'api_app_id', 'api_app_secret', 'api_locale', 'api_save', 'api_status',
	);
	
	private function __construct () {
		if (!class_exists('')) require_once WDFB_PLUGIN_BASE_DIR . '/lib/external/pointers_tutorial.php';
		$this->_tutorial = new Pointer_Tutorial('wdfb_options_tutorial', __('Ultimate Facebook settings', 'wdfb'), false, false);
		$this->_tutorial->add_icon(WDFB_PLUGIN_URL . '/img/facebook_icon.gif');
	}
	
	public static function serve () {
		$me = new Wdfb_Tutorial;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_action('admin_init', array($this, 'process_tutorial'));
		add_action('wp_ajax_wdfb_restart_tutorial', array($this, 'json_restart_tutorial'));
	}
	
	function process_tutorial () {
		if (isset($_GET['page']) && 'wdfb' == @$_GET['page']) $this->_init_tutorial($this->_steps);
		if (defined('DOING_AJAX')) {
			$this->_init_tutorial($this->_steps);
		}
		$this->_tutorial->initialize();
	}
	
	private function _init_tutorial ($steps) {
		$this->_tutorial->set_textdomain('wdfb');
		$this->_tutorial->set_capability('manage_options');
		
		foreach ($steps as $step) {
			$call_step = "add_{$step}_step";
			if (method_exists($this, $call_step)) $this->$call_step();
		}
	}
	
	function json_restart_tutorial () {
		$this->_tutorial->restart();
		exit();
	} 
	
/* ----- Actual tutorial steps ----- */

	function add_intro_step () {
		$this->_tutorial->add_step(
			admin_url('admin.php?page=wdfb'), 'toplevel_page_wdfb',
			'#icon-wdfb',
			__('Ultimate Facebook settings', 'wdsm'),
			array(
				'content' => '<p>' . esc_js(__('This tutorial will help you in configuring Ultimate Facebook settings. You can re-take the tutorial anytime by using the restart link in contextual help.', 'wdsm')) . '</p>',
				'position' => array('edge' => 'top', 'align' => 'left'),
			)
		);
	}
	
/* ----- API steps ----- */
	
	function add_api_step () {
		$this->_tutorial->add_step(
			admin_url('admin.php?page=wdfb#wdfb-section_header-wdfb_api'), 'toplevel_page_wdfb',
			'#wdfb-section_header-wdfb_api',
			__('Facebook API Settings', 'wdsm'),
			array(
				'content' => '<p>' . esc_js(__('This is the most important part of your plugin configuration. All other steps will not be available until you set this up.', 'wdsm')) . '</p>',
				'position' => array('edge' => 'top', 'align' => 'left'),
			)
		);
	}

	function add_api_begin_step () {
		$this->_tutorial->add_step(
			admin_url('admin.php?page=wdfb#wdfb-section_header-wdfb_api'), 'toplevel_page_wdfb',
			'#wdfb-section-wdfb_api td:first p:first',
			__('Create a Facebook app', 'wdsm'),
			array(
				'content' => '<p>' . esc_js(__('Follow the steps to create your Facebook app.', 'wdsm')) . '</p>',
				'position' => array('edge' => 'top', 'align' => 'left'),
			)
		);
	}

	function add_api_app_id_step () {
		$this->_tutorial->add_step(
			admin_url('admin.php?page=wdfb#wdfb-section_header-wdfb_api'), 'toplevel_page_wdfb',
			'#app_key',
			__('App ID/API key', 'wdsm'),
			array(
				'content' => '<p>' . esc_js(__('Paste your App ID here. Be careful not to include spaces.', 'wdsm')) . '</p>',
				'position' => array('edge' => 'top', 'align' => 'left'),
			)
		);
	}

	function add_api_app_secret_step () {
		$this->_tutorial->add_step(
			admin_url('admin.php?page=wdfb#wdfb-section_header-wdfb_api'), 'toplevel_page_wdfb',
			'#secret_key',
			__('Secret key', 'wdsm'),
			array(
				'content' => '<p>' . esc_js(__('Paste your Secret key here. Be careful not to include spaces.', 'wdsm')) . '</p>',
				'position' => array('edge' => 'top', 'align' => 'left'),
			)
		);
	}

	function add_api_locale_step () {
		$this->_tutorial->add_step(
			admin_url('admin.php?page=wdfb#wdfb-section_header-wdfb_api'), 'toplevel_page_wdfb',
			'#wdfb-api_locale',
			__('API locale', 'wdsm'),
			array(
				'content' => '<p>' . esc_js(__('Language used by Facebook on your site.', 'wdsm')) . '</p>',
				'position' => array('edge' => 'top', 'align' => 'left'),
			)
		);
	}
	
	function add_api_save_step () {
		$this->_tutorial->add_step(
			admin_url('admin.php?page=wdfb#wdfb-section_header-wdfb_api'), 'toplevel_page_wdfb',
			'#wdfb-section-wdfb_api .wdfb-save_settings:first',
			__('Save settings', 'wdsm'),
			array(
				'content' => '<p>' . esc_js(__('Once you are done configuring your API settings, click this button to apply your new configuration.', 'wdsm')) . '</p>',
				'position' => array('edge' => 'top', 'align' => 'left'),
			)
		);
	}

	function add_api_status_step () {
		$this->_tutorial->add_step(
			admin_url('admin.php?page=wdfb#wdfb-section_header-wdfb_api'), 'toplevel_page_wdfb',
			'#wdfb-section-wdfb_api .wdfb-api_connect-result:first',
			__('Sanity check', 'wdsm'),
			array(
				'content' => '<p>' . esc_js(__('Once the settings are applied, this line will tell you if everything went fine with your app configuration.', 'wdsm')) . '</p>',
				'position' => array('edge' => 'top', 'align' => 'left'),
			)
		);
	}
	
}
