<?php

class more_types_object extends more_plugins_object_sputnik_8 {
	
	var $settings, $saved, $wp_post_types;

	function init ($settings) {
		$this->settings = $settings;
		add_action('init', array(&$this, 'init_post_types'), 20);
		add_action('admin_init', array(&$this, 'add_boxes_to_post_type'), 21);
		add_filter('template_redirect', array(&$this, 'template_redirect'), 9);
		// Get the default set of post types
		add_action('init', array(&$this, 'set_default_data'), 1);
		// Get modified post type array
		add_action('init', array(&$this, 'set_modified_data'), 19);
	}
	function set_default_data() {
		global $wp_post_types;
		$this->data_default = $wp_post_types;		
	}
	function set_modified_data() {
		global $wp_post_types;
		$this->data_modified = $wp_post_types;	
	}
	function add_boxes_to_post_type () {
		global $wp_meta_boxes;

		if (!is_callable('add_meta_box')) return false;

		$pages = $this->get_objects(array('_plugin_saved', '_plugin'));
		$box_data = $this->get_existing_boxes();

		foreach ($pages as $name => $page) {
			if (array_key_exists('boxes', $page)) {
				foreach((array) $page['boxes'] as $box_key) {
					$box = $box_data[$box_key];
					// add_meta_box($box['id'], $box['label'], $box['callback'], $name, 'normal');
					$bp = (array_key_exists('position', (array) $box)) ? $box['position'] : '';
					$position = ($bp == 'left') ? 'normal' : 'advanced';
					// foreach ($box['post_types'] as $pt) {
						add_meta_box(sanitize_title($box['id']), $box['title'], $box['callback'], $name, $position);
					//}
				}
			}
		}
	}
	function template_redirect() {
		if (is_single()) {
			$pt = get_post_type();
			$mt = $this->read_data();
			if ($template = $mt[$pt]['template']) {
				$file = TEMPLATEPATH . '/' .$template;
				if (file_exists($file)) {
					include($file);
					exit(0);
				}
				return false;
			}
		}
	}
	function get_existing_boxes () {
		global $wp_meta_boxes, $more_fields;
		$data = $wp_meta_boxes;
		$boxes = array();
		foreach ((array) $data as $data1) {
			foreach ((array) $data1 as $data2) {
				foreach ((array) $data2 as $data3) {
					foreach ((array) $data3 as $box) {
						 if ($title = $box['title']) {
							 $boxes[$box['id']] = $box;
						 }
					}
				}
			}						
		}
		//if (is_object($more_fields)) {
	//		$mfs = $more_fields->get_objects(array('_plugin_saved', '_plugin'));
	//		foreach ($mfs as $mf_key => $mf) $boxes[$mf_key] = $mf;
	//	}
		//__d($wp_meta_boxes);

		return $boxes;
	}
	function read_data() {
		global $wp_post_types;

		// Get data from db
		$data = $this->load_objects();


//		$data['plugin'] = get_option($this->settings['option_key'], array());
		
//		$data['plugin_saved'] = 

//print_r($data);
//exit;
		// Data save to file
	//	$data = $this->saved_data($data);
		
		// Data added eslewhere
//		if (!$this->wp_post_types) $this->wp_post_types = $wp_post_types;
//		$data = $this->elsewhere_data($data, $this->wp_post_types);

		return $data;
	}
	function init_post_types() {
		global $wp_post_types, $wp_roles;
		
		$pages = $this->get_objects(array('_plugin_saved', '_plugin'));

		$caps = array(
			'edit_cap' => 'edit_%',
			'edit_type_cap' => 'edit_%s', 
			'edit_others_cap' => 'edit_others_%s',
			'publish_others_cap' => 'publish_%s',
			'read_cap' => 'read_%',
			'delete_cap' => 'delete_%'
		);

		foreach((array) $pages as $name => $page) {

			$options = array();

			// If this post type has a ancestor key, then
			// we need to remove it (it's been overridden).
			if ($k = $page['ancestor_key']) unset($wp_post_types[$k]);

			foreach ($caps as $cap_key => $template) {
				// Create the capability name
				$capability = str_replace('%', $name, $template);

				// Add capabilities to the post type if there are defined roles
				if (!empty($page['more_' . $cap_key])) 
					$options[$cap_key] = $capability;

				// Add capability!
				if (array_key_exists('more_' . $cap_key, $page))
					foreach ((array) $page['more_' . $cap_key] as $role) 
						$wp_roles->add_cap($role, $capability);
			}

			// Fill our options paramete
			$options = $this->populate($page, $options);

			if (array_key_exists('boxes', $page))
				foreach ((array) $page['boxes'] as $box) 
					$options['supports'][] = $box;
				
/*
			// Default text labels
			$default_labels = array(
				'add_new' => __('Add new', 'more-plugins'),
				'add_new_item' => __('Add new item', 'more-plugins'), 
				'edit_item' => __('Edit item', 'more-plugins'),
				'new_item' => __('New item', 'more-plugins'),
				'view_item' => __('View item', 'more-plugins'),
				'search_items' => __('Search item', 'more-plugins'),
				'not_found' => __('No items found', 'more-plugins'), 
				'not_found_in_trash' => __('No items found in Trash', 'more-plugins') 
			);
			foreach ($default_labels as $key => $text) {
				if (!$options['labels'][$key]) $options['labels'][$key] = $text;
			}
			*/
			
			// Some legacy labels

			if (!$options['name']) $options['name'] = $page['labels']['name'];
			if (!$options['label']) $options['label'] = $page['labels']['name'];
			if (!$options['singular_label']) $options['singular_label'] = $page['labels']['singular_name'];
			if (array_key_exists('label', (array) $page) && !$options['labels']['name']) 
				$options['labels']['name'] = $page['label'];
			if (array_key_exists('singular_label', (array) $page) && !$options['labels']['singular_name']) 
				$options['labels']['singular_name'] = $page['singular_label'];
			if (!array_key_exists('menu_name', $options['labels'])) $options['labels']['menu_name'] =  $page['labels']['name'];
			else if (!$options['labels']['menu_name']) $options['labels']['menu_name'] =  $page['labels']['name'];

			$options['labels']['parent_item_colon'] = '';

//			unset($options['name']);
			unset($options['label']);
			unset($options['singular_label']);
			
			
			if ($page['rewrite_bool'] && ($rw = $page['rewrite_slug'])) $options['rewrite'] = array('slug' => $rw);
			else $options['rewrite'] = false;
			unset($options['rewrite_bool']);
			unset($options['rewrite_slug']);
			unset($options['inherit_type']);

			// Enable positions
			if ($options['revisions']) $options['supports'][] = 'revisions';
			unset($options['revisions']);

			// This is bizzare, will leave for now.
			if ($page['show_in_menu']) $options['show_in_menu'] = true;
			
			if (!isset($page['has_archive'])) $options['has_archive'] = true;
			
			// Heter det har nat annat. 
			$options['capability_type'] = ($options['hierarchical']) ? 'page' : 'post';

			// Set if not set
		 	if (!array_key_exists('query_var', $options)) $options['query_var'] = true;
			if (!array_key_exists('menu_position', $options)) $options['menu_position'] = null;

			// Remove if nowt is set
			if (!$options['menu_icon']) unset($options['menu_icon']);

			// We'll add taxonomies below
			$options['taxonomies'] = array();

			// Menu position must be an integer 
			if ($mp = $options['menu_position']) $options['menu_position'] = intval($mp);
  
  			unset($options['taxonomies']);
			unset($options['boxes']);
			unset($options['template']);

			// Regiester the post type
			register_post_type($name, $options);
			
			// Add the taxonomies
			if (array_key_exists('taxonomies', $page))
	       	 	foreach ((array) $page['taxonomies'] as $taxonomy)  {
    	   	 		if (!$taxonomy) continue;
        			register_taxonomy_for_object_type($taxonomy, $name);
				}		
		}

		//exit();

	}
	
} // End class


?>
