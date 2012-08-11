<?php
/**
 * Handles all Admin access functionality.
 */
class Wdsb_PublicPages {

	var $data;

	function Wdsb_PublicPages () { $this->__construct(); }

	function __construct () {
		$this->data = new Wdsb_Options;
	}

	/**
	 * Main entry point.
	 *
	 * @static
	 */
	function serve () {
		$me = new Wdsb_PublicPages;
		$me->add_hooks();
	}


	function js_load_scripts () {
		if (defined('WDSB_SCRIPTS_PRINTED')) return false;;
		wp_enqueue_script('jquery');
		wp_enqueue_script('wdsb', WDSB_PLUGIN_URL . '/js/wdsb.js', array('jquery'));

		$horizontal_position = $this->data->get_option('horizontal_relative');
		$horizontal_position = $horizontal_position ? $horizontal_position : "page";

		$horizontal_direction = $this->data->get_option('horizontal_direction');
		$horizontal_direction = $horizontal_direction ? $horizontal_direction : "left";

		$top_position = $this->data->get_option('top_relative');
		$top_position = $top_position ? $top_position : "page-bottom";

		$zidx = $this->data->get_option('z-index');
		$zidx = $zidx ? $zidx : 10000000;
		printf(
			'<script type="text/javascript">
				var _wdsb_data = {
					"min_width": %d,
					"horizontal_selector": "%s",
					"top_selector": "%s",
					"z_index": %d,
					"allow_fixed": %d,
					"offset": {"htype": "%s", "hdir": "%s", "hoffset": %d, "vtype": "%s", "voffset": %d},
					"limit": {"top_selector": "%s", "top_offset": %d, "bottom_selector": "%s", "bottom_offset": %d}
				};
			</script>',
			(int)$this->data->get_option('min_width'),
			$this->data->get_option('horizontal_selector'),
			$this->data->get_option('top_selector'),
			(int)$zidx,
			(int)$this->data->get_option('allow_fixed'),

			$horizontal_position,
			$horizontal_direction,
			(int)$this->data->get_option('horizontal_offset'),
			$top_position,
			(int)$this->data->get_option('top_offset'),

			$this->data->get_option('top_limit_selector'),
			(int)$this->data->get_option('top_limit_offset'),
			$this->data->get_option('bottom_limit_selector'),
			(int)$this->data->get_option('bottom_limit_offset')
		);
		define('WDSB_SCRIPTS_PRINTED', true, true);
	}

	function css_load_styles () {
		if (!current_theme_supports('wdsb')) {
			wp_enqueue_style('wdsb', WDSB_PLUGIN_URL . '/css/wdsb.css');
		}
	}

	function inject_box_markup ($markup='') {
		global $wp_current_filter;
		if (defined('WDSB_BOX_CREATED')) return $markup;
		$show_on_front =  $this->data->get_option('show_on_front_page');
		$show_on_archive = $this->data->get_option('show_on_archive_pages');
		if (!is_page() && !is_singular()) {
			if (!is_home() && !is_archive()) return $markup;
			else if (!$show_on_front && !$show_on_archive) return $markup;
		}
		if ((is_front_page() && !$show_on_front) || (is_archive() && !$show_on_archive)) return $markup;

		// Additional BP check (docs and such)
		if (function_exists('bp_current_component')) {
			if (!$this->data->get_option('show_on_buddypress_pages') && bp_current_component()) return $markup;
		}
		
		$is_excerpt = array_reduce($wp_current_filter, create_function('$ret,$val', 'return $ret ? true : preg_match("/excerpt/", $val);'), false);
		$is_head = array_reduce($wp_current_filter, create_function('$ret,$val', 'return $ret ? true : preg_match("/head\b|head[^w]/", $val);'), false);
		$is_title = array_reduce($wp_current_filter, create_function('$ret,$val', 'return $ret ? true : preg_match("/title/", $val);'), false);
		
		if ($is_excerpt || $is_head || $is_title) return $markup;

		$prevent_types = $this->data->get_option('prevent_types');
		$prevent_types = is_array($prevent_types) ? $prevent_types : array();
		if (in_array(@get_post_type(), $prevent_types)) return $markup;

		$prevent_items = $this->data->get_option('prevent_items');
		$prevent_items = is_array($prevent_items) ? $prevent_items : array();
		if (in_array(@get_the_ID(), $prevent_items)) return $markup;

		$style = '';

		$services = $this->data->get_option('services');
		$services = is_array($services) ? $services : array();
		if (!$services) return $markup;

		$skip_script = $this->data->get_option('skip_script');
		$skip_script = is_array($skip_script) ? $skip_script : array();

		$background = $this->data->get_option('background');
		$style .= $background ? "background:{$background};" : '';

		$border = $this->data->get_option('border');
		$style .= $border ? "border:{$border};" : '';

		$css = $this->data->get_option('css');

		include WDSB_PLUGIN_BASE_DIR . '/lib/forms/box_template.php';
		define('WDSB_BOX_CREATED', true, true);
		return $markup;
	}

	function postpone_front_page_init () {
		if (!$this->data->get_option('front_footer')) return;
		if (
			((is_home() || is_front_page()) && $this->data->get_option('show_on_front_page'))
			||
			(is_archive() && $this->data->get_option('show_on_archive_pages'))
			||
			(function_exists('bp_current_component') && $this->data->get_option('show_on_buddypress_pages') && bp_current_component())
		) {
			$hook = $this->data->get_option('front_hook');
			$hook = $hook ? $hook : 'get_footer';
			remove_filter('the_content', array($this, 'inject_box_markup'), 1);
			add_action($hook, array($this, 'inject_box_markup'));
		}
	}

	function add_hooks () {
		// Step0: Register options and menu
		add_action('wp_print_scripts', array($this, 'js_load_scripts'));
		add_action('wp_print_styles', array($this, 'css_load_styles'));

		if (!$this->data->get_option('manual_placement')) {
			add_filter('the_content', array($this, 'inject_box_markup'), 1); // Do this VERY early in content processing
			if ($this->data->get_option('show_on_front_page') || $this->data->get_option('show_on_archive_pages') || $this->data->get_option('show_on_buddypress_pages')) {
				if ($this->data->get_option('front_footer')) {
					add_action('init', array($this, 'postpone_front_page_init'));
				} else {
					add_action('loop_start', array($this, 'inject_box_markup'));
				}
			}
		} // else, manual placement through template tag call
	}
}

/**
 * Sharebox fetching template tag.
 * @return string Floating Social HTML markup.
 */
function wdsb_get_sharebox () {
	$wdsb = new Wdsb_PublicPages;
	return $wdsb->data->get_option('manual_placement') ? $wdsb->inject_box_markup() : '';
}