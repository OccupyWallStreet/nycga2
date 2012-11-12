<?php

/**
 * Handles public maps interface.
 */
class AgmUserMaps {

	/**
	 * Entry method.
	 *
	 * Creates and handles the Userland interface for the Plugin.
	 *
	 * @access public
	 * @static
	 */
	static function serve () {
		$me = new AgmUserMaps();
		$me->add_hooks();
		$me->model = new AgmMapModel();
	}

	/**
	 * Include Google Maps dependencies.
	 */
	function js_google_maps_api () {
		wp_enqueue_script('jquery');
		wp_enqueue_script('google_maps_api', AGM_PLUGIN_URL . '/js/google_maps_loader.js');

		wp_enqueue_script('agm_google_user_maps', AGM_PLUGIN_URL . '/js/google_maps_user.js', array('jquery'));
		wp_localize_script('agm_google_user_maps', 'l10nStrings', array(
			'close' => __('Close', 'agm_google_maps'),
			'get_directions' => __('Get Directions', 'agm_google_maps'),
			'geocoding_error' => __('There was an error geocoding your location. Check the address and try again', 'agm_google_maps'),
			'missing_waypoint' => __('Please, enter values for both point A and point B', 'agm_google_maps'),
			'directions' => __('Directions', 'agm_google_maps'),
			'posts' => __('Posts', 'agm_google_maps'),
			'showAll' => __('Show All', 'agm_google_maps'),
			'hide' => __('Hide', 'agm_google_maps'),
			'oops_no_directions' => __('Oops, we couldn\'t calculate the directions', 'agm_google_maps'),
		));
	}

	/**
	 * Introduces plugins_url() as root variable (global).
	 */
	function js_plugin_url () {
		printf(
			'<script type="text/javascript">var _agm_root_url="%s"; var _agm_ajax_url="%s"</script>',
			AGM_PLUGIN_URL, admin_url('admin-ajax.php')
		);
	}

	/**
	 * Introduces global list of maps to be initialized.
	 */
	function js_maps_global () {
		echo '<script type="text/javascript">if (typeof(_agmMaps) == "undefined") _agmMaps = [];</script>';
	}

	/**
	 * Includes required styles.
	 */
	function css_load_styles () {
		wp_enqueue_style('agm_google_maps_user_style', AGM_PLUGIN_URL . '/css/google_maps_user.css');
	}

	/**
	 * Include additional styles.
	 */
	function css_additional_styles () {
		$opts = get_option('agm_google_maps');
		$css = @$opts['additional_css'];
		if ($css) echo "<style type='text/css'>{$css}</style>";
	}

	/**
	 * Checks post meta and injects the map, if needed.
	 */
	function process_post_meta ($body) {

		global $wp_current_filter;
		if (in_array('get_the_excerpt', $wp_current_filter) || in_array('the_excerpt', $wp_current_filter)) return $body; // Do NOT do this in excerpts

		$opts = get_option('agm_google_maps');
		$fields = $opts['custom_fields_map'];
		$options = $opts['custom_fields_options'];
		$post_id = get_the_ID();

		// Check if we have already done this
		$map_id = get_post_meta($post_id, 'agm_map_created', true);

		if (!$map_id) {
			$latitude = $longitude = $address = false;
			if ($fields['latitude_field']) $latitude = get_post_meta($post_id, $fields['latitude_field'], true);
			if ($fields['longitude_field']) $longitude = get_post_meta($post_id, $fields['longitude_field'], true);
			if ($fields['address_field']) $address = get_post_meta($post_id, $fields['address_field'], true);

			if (!$latitude && !$longitude && !$address) return $body; // Nothing to process

			$map_id = $this->model->autocreate_map($post_id, $latitude, $longitude, $address);
		}

		if (!$map_id) return $body;

		if ($options['autoshow_map']) {
			if ('top' == $options['map_position']) {
				$body = "[map id=\"{$map_id}\"]\n" . $body;
			} else {
				$body .= "\n[map id=\"{$map_id}\"]";
			}
		}
		return $body;
	}

	/**
	 * Adds needed hooks.
	 *
	 * @access private
	 */
	function add_hooks () {
		// Step1a: Add root dependencies
		add_action('wp_print_scripts', array($this, 'js_maps_global'));
		add_action('wp_print_scripts', array($this, 'js_plugin_url'));
		add_action('wp_print_styles', array($this, 'css_load_styles'));

		// Step1b: Add Google Maps dependencies
		add_action('wp_print_scripts', array($this, 'js_google_maps_api'));

		// Step 1c: Additiona styles
		add_action('wp_head', array($this, 'css_additional_styles'));

		// Step2: Register custom fields processing
		$opts = get_option('agm_google_maps');
		if (@$opts['use_custom_fields']) {
			add_filter('the_content', array($this, 'process_post_meta'), 1); // Note the order
		}


		// Step3: Process map tags
		$rpl = AgmMarkerReplacer::register();
		//add_filter('the_content', array($rpl, 'process_tags'), 99);
	}
}