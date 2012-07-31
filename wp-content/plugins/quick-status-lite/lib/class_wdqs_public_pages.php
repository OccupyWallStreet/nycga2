<?php
/**
 * Handles all Admin access functionality.
 */
class Wdqs_PublicPages {

	var $data;
	var $_link_type = 'link';

	function Wdqs_PublicPages () { $this->__construct(); }

	function __construct () {
		$this->data = new Wdqs_Options;
	}

	/**
	 * Main entry point.
	 *
	 * @static
	 */
	function serve () {
		$me = new Wdqs_PublicPages;
		$me->add_hooks();
	}


	function js_load_scripts () {
		if (!$this->_check_permissions()) return false;
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'media-upload' );
		add_thickbox();

		wp_enqueue_script('wdqs_widget', WDQS_PLUGIN_URL . '/js/widget.js');
		wp_localize_script('wdqs_widget', 'l10nWdqs', array(
			'no_thumbnail' => __('No thumbnail', 'wdqs'),
			'of' => __('of', 'wdqs'),
			'images_found' => __('images found', 'wdqs'),
			'use_default_title' => __('Use default title', 'wdqs'),
			'use_this_title' => __('Use this title', 'wdqs'),
			'post_title' => __('Post title', 'wdqs'),
			'height' => __('Height', 'wdqs'),
			'width' => __('Width', 'wdqs'),
			'leave_empty_for_defaults' => __('Leave these boxes empty for defaults', 'wdqs'),
		));
		echo '<script type="text/javascript">var _wdqs_ajaxurl="' . admin_url('admin-ajax.php') . '";</script>';
		echo '<script type="text/javascript">var _wdqs_adminurl="' . admin_url() . '";</script>';
	}

	function css_load_styles () {
		if (!current_theme_supports('wdqs')) {
			wp_enqueue_style('wdqs', WDQS_PLUGIN_URL . '/css/wdqs.css');
		}
		if (!$this->_check_permissions()) return false;
		wp_enqueue_style('thickbox');
		wp_enqueue_style('wdqs_widget', WDQS_PLUGIN_URL . '/css/widget.css');
		wp_enqueue_style('wdqs_widget-front', WDQS_PLUGIN_URL . '/css/widget-front.css');
	}

	function status_widget () {
		if (!$this->_check_permissions()) return false;
		echo "<div>";
		include(WDQS_PLUGIN_BASE_DIR . '/lib/forms/dashboard_widget.php');
		echo "</div>";
	}

	private function _check_permissions () {
		return false;
	}


	function add_hooks () {
		// Step0: Register options and menu
		if (!$this->data->get('show_on_public_pages')) return false;
		add_action('wp_print_scripts', array($this, 'js_load_scripts'));
		add_action('wp_print_styles', array($this, 'css_load_styles'));

		add_action('loop_start', array($this, 'status_widget'), 100);
	}
}