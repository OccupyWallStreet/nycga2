<?php


class more_fields_object extends more_plugins_object_sputnik_8 {

	var $settings, $field_types;

	function init ($settings) {
		$this->settings = $settings;
	
		// add_action('init', array(&$this, 'load_field_types'), 20);

 		add_action('admin_init', array(&$this, 'admin_init'), 20);

		add_action('init', array(&$this, 'set_default_data'), 1);
		
		// Get modified post type array
		add_action('admin_init', array(&$this, 'set_modified_data'), 19);
		
		
		// Load the field types
		add_filter('more_fields_field_types', 'more_fields_field_types');
		$this->field_types = array();
		$this->field_types = apply_filters('more_fields_field_types', $this->field_types);
		
		// Do the field types
		//add_filter('more_fields_write_css', 'more_fields_write_css');
		//add_filter('more_fields_write_js', 'more_fields_write_js');

	}
	function set_default_data() {
		global $wp_meta_boxes;
		$this->data_default = $wp_meta_boxes;		
	}
	function set_modified_data() {
		global $wp_meta_boxes;
		$this->data_modified = $this->trawl_data($wp_meta_boxes);
	}
	function trawl_data($data) {
//	__d($data);
		$boxes = array();
		foreach ((array) $data as $pt => $data1) {
			foreach ((array) $data1 as $data2) {
				foreach ((array) $data2 as $data3) {
					foreach ((array) $data3 as $box) {
						if ($id = $box['id']) {
							$boxes[$id]['label'] = $box['title'];
							$boxes[$id]['post_types'][] = $pt; // $box['title'];
						}
					}
				}
			}
		}
		return $boxes;
	}
	
	/*
	function read_data() {

		return $this->get_data();

//		global $wp_

		$data = get_option($this->settings['option_key'], array());

		// Stuff saved to file
		$data = $this->saved_data($data);
		
		// Data added eslewhere
//		if (!$this->wp_taxonomies) $this->wp_taxonomies = $wp_taxonomies;
//		$data = $this->elsewhere_data($data, $this->wp_taxonomies);		
		
		return $data;
	}
	*/
	
	function load_field_types () {
//		$field_types = mf_field_types();
	}
	
	/*
	**	admin_init()
	**
	*/
	function admin_init() {
		global $wp_meta_boxes, $wp_roles;

		if (!is_callable('add_meta_box')) return false;

		// Give More Types priority
		$plugins = get_option( 'active_plugins', array());
		$more_types = 'more-types/more-types.php';

		$this->load_objects();		
		
		$boxes = $this->get_objects(array('_plugin_saved', '_plugin'));

		// Remove boxes defined elsewhere if we're overwriting it
		$others = $this->get_objects(array('_other'));
		foreach ((array) $others as $key => $other) {
			$id = sanitize_title($other['label']);
			if (array_key_exists($id, (array) $boxes)) {
				foreach ((array) $other['post_types'] as $pt) {
					remove_meta_box($key, $pt, 'normal'); 
					remove_meta_box($key, $pt, 'advanced'); 
				}
			}		
		}

		// Hook the More Fields boxes into the WP meta box framework
		foreach((array) $boxes as $key => $box) {
					
			if (!($box = apply_filters('mf_box', $box))) continue;

			// Create the capability name
			$boxslug = (array_key_exists('slug', $box)) ? $box['slug'] : 0;
			$slug = ($s = $boxslug) ? $s : sanitize_title($box['label']);
			$capability = 'more_fields_box_' . $slug;

			$bmac = (array_key_exists('more_access_cap', $box)) ? $box['more_access_cap'] : array();
			foreach ((array) $bmac as $role) {
				if (is_object($wp_roles))
					$wp_roles->add_cap($role, $capability);
			}	

			// Can the curret user see this box?
			if (!empty($box['more_access_cap']))
				if (!current_user_can($capability)) continue;
			
			// If it's positioned to the right, then add an additional page type, not processed by WP
			$context = ($box['position'] == 'left') ? 'normal' : 'side';

//				$context = 'normal';
			// If more types is installed don't associate with any particular post type. 
		//	if (in_array($more_types, $plugins)) {
				// Do nothing
			// 	add_meta_box(sanitize_title($box['label']), $box['label'], 'mf_ua_callback', '', $context);
		//			add_meta_box($key, $box['label'], 'mf_ua_callback', 'kocksukker', $context);
		//	} else {
				// Add the box to the post type
				foreach ((array) $box['post_types'] as $b) {

					add_meta_box($key, $box['label'], 'mf_ua_callback', $b, $context);
				}
		//	}
		}
	}
}

?>