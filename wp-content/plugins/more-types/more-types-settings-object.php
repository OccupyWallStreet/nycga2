<?php

class more_types_admin extends more_plugins_admin_object_sputnik_8 {

	function add_actions () {
		add_action('admin_menu', array(&$this, 'admin_menu_prune'));

		// Add the ability to add the custom taxonomies		
		$taxonomies = get_taxonomies(array(), '');
		foreach ($taxonomies as $tax) {
			if (!$tax->hierarchical) array_push($this->fields['var'], 'default_taxonomy_' . $tax->name);
			else array_push($this->fields['array'], 'default_taxonomy_' . $tax->name);
		}	

		add_action('admin_head-post-new.php', array(&$this, 'add_new'));
		add_action('admin_head-post.php', array(&$this, 'add_new'));

		add_action('admin_head-post-new.php', array(&$this, 'add_js'));	
		add_action('admin_head-page-new.php', array(&$this, 'add_js'));	
		add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
		
		add_filter('post_updated_messages', array(&$this, 'post_updated_messages'));
		// add_action('post_updated_messages', array(&$this, 'post_updated_messages'));
		
//		__d($post);
	}
	
	function enqueue_scripts () {
//		wp_enqueue_script('tiny_mce');
	}
	function post_updated_messages ($messages) {
		global $post, $more_types, $more_types_script;
		$more_types_script = '';
		if ($post->filter == 'raw') {
			$types = $more_types->get_objects(array('_plugin', '_saved'));
			if (!array_key_exists($post->post_type, $types)) return $messages;
			foreach ((array) $types[$post->post_type]['taxonomies'] as $tax) {
				$default = (array_key_exists('default_taxonomy_' . $tax, $types[$post->post_type])) ? $types[$post->post_type]['default_taxonomy_' . $tax] : '';
				if (is_array($default)) {
					foreach ($default as $d) {
						$term = get_terms($tax, "slug=$d&hide_empty=0");
						$id = (array_key_exists(0, $term)) ? $term[0]->term_id : '';
						$more_types_script .= '$("#in-' . $tax . '-' . $id . '").attr({checked: "checked"});
												$("#in-popular-' . $tax . '-' . $id . '").attr({checked: "checked"});' . "\n";
					}								
				} else {
					$more_types_script .= '$("#new-tag-' . $tax . '").val("' . $default . '"); $(".tagadd").click();';
				}
			}
		}
		// echo $more_types_script;
		return $messages;	
	}

	function add_js () {
		global $more_types_script;
		?>
		<script type="text/javascript">
		//<![CDATA[
			jQuery(document).ready(function($){
				<?php echo $more_types_script; ?>
			});
		//]]>
		</script>
		<?php		
	
	}


	function add_new () {
		global $post;
		wp_enqueue_script('tiny_mce'); 

	//	$post->tax_input['record'] = array('3')	;
	//	__d($post);
	
	}
	function admin_menu_prune() {
		global $menu, $more_types;
		$types = $more_types->get_objects(array('_plugin'));
		$defaults = array('post' => 5, 'page' => 20, 'attachment' => 10);

		foreach ($types as $name => $type) {

			// Remove the menu item if we've overwriten the default
			$key = ($k = $type['ancestor_key']) ? $k : $name;
			if (array_key_exists($key, $defaults)) {
				unset($menu[$defaults[$key]]);
			}
		}
	}

	function get_boxes() {

		$divs = array(
			'title' => __('Title'),
			'editor' => __('Editor'),
			'thumbnail' => __('Thumbnail'),
			'excerpt' => __('Excerpt'),
			'custom-fields' => __('Custom Fields'),
			'author' => __('Author'),
			'comments' => __('Comments'),
//			'revisions' => __('Revisions'),
			'page-attributes' => __('Page Attributes'),
		);
		return $divs;
	}

	function validate_sumbission() {
		global $more_types;
		
		if ($this->action == 'save') {
		
			// Set the index to save to
			$name = sanitize_title($_POST['labels,singular_name']);

			$_POST['index'] = $name;
			$this->action_keys = array($name);

			$rwslug = esc_attr($_POST['rewrite_slug']);
			if (!$rwslug && array_key_exists('singular_label', $_POST)) 
				$_POST['rewrite_slug'] = sanitize_title(esc_attr($_POST['singular_label']));

			$a = esc_attr($_POST['labels,name']);
			$b = esc_attr($_POST['labels,singular_name']);

			if (!$a && !$b) {
				$this->set_navigation('post_type');
				return $this->error(__('You need both a plural and singular label for the post type!', 'more-plugins')); 
			}
			if (!$a) {
				$this->set_navigation('post_type');
				return $this->error(__('You need a label for the post type!', 'more-plugins')); 
			}
			if (!$b) {
				$this->set_navigation('post_type');
				return $this->error(__("You need a singular label for the post type! E.g. 'Cat' for the taxonomy 'Cats'", 'more-plugins')); 
			}
			
			// Add the taxonomies
			if (array_key_exists('taxonomies', $_POST))
				foreach ((array) $_POST['taxonomies'] as $t) {
					$a = get_taxonomy($t);
					if ($a->hierarchical) $this->fields['array'][] = 'default_taxonomy_' . $t;
					else $this->fields['var'][] = 'default_taxonomy_' . $t;
				}
//__d($a);
		}
		// If all is OK
		return true;
	}
	function load_objects() {
		global $more_types;		
		$this->data = $more_types->load_objects();
		return $this->data;
	}
	
	function default_data () {
		global $wp_post_types;
		return $this->object_to_array($wp_post_types);
	}	
	function get_post_types() {
		global $wp_post_types;
		$ret = array();
		foreach ($wp_post_types as $key => $pt) $ret[$key] = $pt->label;
		return $ret;
	}
	function get_templates() {
		$templates = get_page_templates();
		$arr = array();
		foreach ((array) $templates as $k => $t) {
			$arr[$t] = $k;
		}
		array_unshift($arr, '');
		return $arr;
	}
	
	/*
	**	update_from_more_plugin()
	**
	**	Handles cross-functionality between More Types and More Fields - any changes
	** 	made here are reflected in the More Types admin too.
	*/
	function update_from_more_plugin($external_object, $eo_key, $mt_key) {
		global $more_types;
		if (!is_object($external_object)) return false;
		$mts = $more_types->get_objects(array('_plugin'));
		if (!is_array($mts)) return false;
		$mfs = $external_object->get_objects(array('_plugin'));		
		$mts_keys = array_keys($mts);
		$mfs_keys = array_keys($mfs);
		foreach ($mfs as $mf_key => $mf) {

			// Are the boxes present or absent for the different post types
			$types = (array) $mf[$eo_key];
			$present = array_intersect($mts_keys, $types);
			$absent = array_diff($mts_keys, $types);

			// Add it
			foreach ((array) $present as $p) {
				if (array_key_exists($mt_key, $mts[$p]))
					if (!in_array($mf_key, (array) $mts[$p][$mt_key])) 
						$mts[$p][$mt_key][] = $mf_key;
			}

			// Remove it
			foreach ((array) $absent as $a) {
				if (array_key_exists($mt_key, $mts[$a]))
					if (in_array($mf_key, (array) $mts[$a][$mt_key])) {
						$indicies = array_flip($mts[$a][$mt_key]);
						unset($mts[$a][$mt_key][$indicies[$mf_key]]);
					}				
			}
		}
		
//		__d($mts);
		// Save the data
		$this->save_data($mts);

	}
	/*
	**
	**
	*/
	function list_post_type_with_key($key, $value, $text = '') {
		global $more_types;
		$in = array('_plugin', '_plugin_saved', '_other', '_default');
		$data = $more_types->get_objects($in);
		$editable = $more_types->get_objects(array('_plugin_saved', '_other'));

		$options = array();
		foreach ($data as $pkey => $pt) {
			$ret = array();
			$label = $pt['labels']['singular_name'];

			if (array_key_exists($key, $pt)) {
				if (is_array($pt[$key])) {
					if (in_array($value, $pt[$key]))
						$ret = array_merge($ret, array('value' => 'on'));
					else $ret = array_merge($ret, array('value' => ''));
					//	$ret[$pkey] = array('value' == 'on'); //$pkey;
				} else {
					if ($pt[$key] == $value)
						$ret = array_merge($ret, array('value' => 'on'));
					else $ret = array_merge($ret, array('value' => ''));
				}
			}
			// if (array_key_exists($pkey, $editable))
			//	$ret = array_merge($ret, array('disabled' => true, 'text' => $text ));

			$options[$pkey] = $ret;
		}
		
		return $options;
	}
	
} // End class


?>