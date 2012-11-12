<?php

/**
 * Handles quicktags replacement in text.
 */
class AgmMarkerReplacer {

	/**
	 * PHP4 compatibility constructor.
	 */
	function AgmMarkerReplacer () {
		$this->__construct();
	}

	function __construct() {
		$this->model = new AgmMapModel();
	}

	/**
	 * Creates a replacer and registers shortcodes.
	 *
	 * @access public
	 * @static
	 */
	static function register () {
		$me = new AgmMarkerReplacer();
		$me->register_shortcodes();
	}

	/**
	 * Registers shortcodes for processing.
	 *
	 * @access private
	 */
	function register_shortcodes () {
		add_shortcode('map', array($this, 'process_tags'));
	}

	/**
	 * Creates markup to insert a single map.
	 *
	 * @access private
	 */
	function create_tag ($map, $overrides=array()) {
		if (!$map['id']) return '';

		$map = array_merge($map, $overrides);

		$elid = 'map-' . md5(microtime() . rand());
		$rpl = '<div class="agm_google_maps" id="' . $elid . '"></div>';
		$rpl .= '<script type="text/javascript">_agmMaps[_agmMaps.length] = {selector: "#' . $elid . '", data: ' . json_encode($map) . '};</script>';
		return $rpl;
	}

	/**
	 * Creates markup to insert multiple maps.
	 *
	 * @access private
	 */
	function create_tags ($maps, $overrides=array()) {
		if (!is_array($maps)) return '';
		$ret = '';
		foreach ($maps as $map) {
			$ret .= $this->create_tag($map, $overrides);
		}
		return $ret;
	}

	/**
	 * Creates a map overlay.
	 * Takes all resulting maps from a query and merges all
	 * markers into one map with default settings.
	 *
	 * @access private
	 */
	function create_overlay_tag ($maps, $overrides=array()) {
		if (!is_array($maps)) return '';
		return $this->create_tag($this->model->merge_markers($maps), $overrides);
	}

	/**
	 * Inserts a map for tags with ID attribute set.
	 *
	 * @access private
	 */
	function process_map_id_tag ($map_id, $overrides=array()) {
		return $this->create_tag($this->model->get_map($map_id), $overrides);
	}

	/**
	 * Inserts a map for tags with query attribute set.
	 *
	 * @access private
	 */
	function process_map_query_tag ($query, $overrides=array(), $overlay=false, $network=false) {
		$method = $overlay ? 'create_overlay_tag' : 'create_tags';
		if ('random' == $query) return $this->$method($this->model->get_random_map(), $overrides);
		if ('all' == $query) return $this->$method($this->model->get_all_maps(), $overrides);
		return $network ?
			$this->$method($this->model->get_custom_network_maps($query), $overrides)
			:
			$this->$method($this->model->get_custom_maps($query), $overrides)
		;
	}

	/**
	 * Processes text and replaces recognized tags.
	 *
	 * @access public
	 */
	function process_tags ($atts, $content=null) {
		$body = false;
		$atts = shortcode_atts(array(
			'id' => false,
			'query' => false,
			'overlay' => false,
			'network' => false,
		// Appearance overrides
			'height' => false,
			'width' => false,
			'zoom' => false,
			'show_map' => false,
			'show_markers' => false,
			'show_images' => false,
			'show_posts' => false,
			'map_type' => false,
		// Command switches
			'plot_routes' => false,
		), $atts);

		$overrides = array();
		$map_types = array(
			'ROADMAP',
			'SATELLITE',
			'HYBRID',
			'TERRAIN'
		);

// Stacked queries fix
$atts['query'] = preg_replace(
	'/' . preg_quote('&#038;') . '/', '&', 
	preg_replace('/' . preg_quote('&amp;') . '/', '&', $atts['query'])
);

		if ($atts['height']) $overrides['height'] = $atts['height'];
		if ($atts['width']) $overrides['width'] = $atts['width'];
		if ($atts['zoom']) $overrides['zoom'] = $atts['zoom'];
		if ($atts['show_map']) $overrides['show_map'] = ('true' == $atts['show_map']) ? 1 : 0;
		if ($atts['show_markers']) $overrides['show_markers'] = ('true' == $atts['show_markers']) ? 1 : 0;
		if ($atts['show_images']) $overrides['show_images'] = ('true' == $atts['show_images']) ? 1 : 0;
		if ($atts['show_posts']) $overrides['show_posts'] = ('true' == $atts['show_posts']) ? 1 : 0;
		if ($atts['plot_routes']) $overrides['plot_routes'] = ('true' == $atts['plot_routes']) ? 1 : 0;
		if ($atts['map_type'] && in_array(strtoupper($atts['map_type']), $map_types)) $overrides['map_type'] = strtoupper($atts['map_type']);

		if (!AGM_USE_POST_INDEXER) $atts['network'] = false; // Can't do this without Post Indexer
		if ($atts['id']) $body = $this->process_map_id_tag($atts['id'], $overrides); // Single map, no overlay
		else if ($atts['query']) $body = $this->process_map_query_tag($atts['query'], $overrides, $atts['overlay'], $atts['network']);
		return $body ? $body : $content;
	}
}