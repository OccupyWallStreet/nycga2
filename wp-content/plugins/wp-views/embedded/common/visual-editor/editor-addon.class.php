<?php

if (!class_exists('Editor_addon')) {

    if (!defined('ICL_COMMON_FUNCTIONS')) {
        require_once dirname(dirname(__FILE__)) . '/functions.php';
    }

    define('EDITOR_ADDON_ABSPATH', dirname(__FILE__));
    if (!defined('EDITOR_ADDON_RELPATH')) {
        define('EDITOR_ADDON_RELPATH', icl_get_file_relpath(__FILE__));
    }
    add_action('admin_print_styles', 'add_menu_css');

    function add_menu_css() {
        global $pagenow;

        if ($pagenow == 'post.php' || $pagenow == 'post-new.php') {
            wp_enqueue_style('editor_addon_menu',
                    EDITOR_ADDON_RELPATH . '/res/css/pro_dropdown_2.css');
            wp_enqueue_style('editor_addon_menu_scroll',
                    EDITOR_ADDON_RELPATH . '/res/css/scroll.css');
        }
    }

    if (is_admin()) {
        add_action('admin_print_scripts', 'editor_add_js');
    }

    class Editor_addon
    {

        function __construct($name, $button_text, $plugin_js_url,
                $media_button_image = '') {

            $this->name = $name;
            $this->plugin_js_url = $plugin_js_url;
            $this->button_text = $button_text;
            $this->media_button_image = $media_button_image;
            $this->initialized = false;

            $this->items = array();

            if ($media_button_image != '') {
                // Media buttons
                //Adding "embed form" button
                // WP 3.3 changes
                global $wp_version;
                if (version_compare($wp_version, '3.1.4', '>')) {
                    add_action('media_buttons', array($this, 'add_form_button'),
                            10, 2);
                } else {
                    add_action('media_buttons_context',
                            array($this, 'add_form_button'), 10, 2);
                }
            }

//            add_action('media_buttons', array($this, 'media_buttons'), 11);
//            wp_enqueue_style('editor_addon', plugins_url() . '/' . basename(dirname(dirname(dirname(__FILE__)))) . '/common/' . basename(dirname(__FILE__)) . '/res/css/style.css');
        }

        function __destruct() {
            
        }

        /*

          Add a menu item that will insert the shortcode.

          To use sub menus, add a '-!-' separator between levels in
          the $menu parameter.
          eg.  Field-!-image
          This will create/use a menu "Field" and add a sub menu "image"

          $function_name is the javascript function to call for the on-click
          If it's left blank then a function will be created that just
          inserts the shortcode.

         */

        function add_insert_shortcode_menu($text, $shortcode, $menu,
                $function_name = '') {
            $this->items[] = array($text, $shortcode, $menu, $function_name);
        }

        /**
         * Adding a "V" button to the menu
         * @param string $context
         * @param string $text_area
         * @param boolean $standard_v is this a standard V button
         */
        function add_form_button($context, $text_area = 'textarea#content', $standard_v = TRUE) {
            global $wp_version;
            // WP 3.3 changes ($context arg is actually a editor ID now)
            if (version_compare($wp_version, '3.1.4', '>') && !empty($context)) {
                $text_area = $context;
            }
            
            // Apply filters
            $this->items = apply_filters('editor_addon_items_' . $this->name,
                    $this->items);
            
            // add_filter('editor_addon_parent_items', array($this, 'wpv_add_parent_items'), 10, $this->items);
            // Apply filter parent items
            //apply_filters('editor_addon_parent_items', $this->items);
            // sort the items into menu levels.

            $menus = array();
            $sub_menus = array();
            
            foreach ($this->items as $item) {
                $parts = explode('-!-', $item[2]);
                $menu_level = &$menus;
                foreach ($parts as $part) {
                    if ($part != '') {
                        if (!array_key_exists($part, $menu_level)) {
                            $menu_level[$part] = array();
                        }
                        $menu_level = &$menu_level[$part];
                    }
                }
                $menu_level[$item[0]] = $item;
            }

            // Apply filters
            $menus = apply_filters('editor_addon_menus_' . $this->name, $menus);

            // add View Template links to the "Add Field" button
            if(!$standard_v) {
            	$this->add_view_templates($menus);
			}

            // Sort menus
            if(is_array($menus)) {
            	$menus = $this->sort_menus_alphabetically($menus);
            }
            
            
            $this->_media_menu_direct_links = array();
            $menus_output = $this->_output_media_menu($menus, $text_area, $standard_v);
            
            $direct_links = implode(' ', $this->_media_menu_direct_links);
            
            $addon_button = '<img src="' . $this->media_button_image . '">';
            if(!$standard_v) {
            	$addon_button = '<img src="' . $this->media_button_image . '" class="vicon">';
            	// $addon_button = '<input id="addingbutton" alt="#TB_inline?inlineId=add_field_popup" class="thickbox wpv_add_fields_button button-primary field_adder" type="button" value="'. __('Add field', 'wpv-views') .'" name="">';
            	//$addon_button = '<span class="wpv_add_fields_button button-primary field_adder">'. __('Add field', 'wpv-views') .'</span>';
            }
            
            // add search box
            $searchbar = $this->get_search_bar();
            
            // generate output content
            $out = '
<ul class="editor_addon_wrapper"><li>' . $addon_button . '<ul class="editor_addon_dropdown"><li><div class="title">'
                    . $this->button_text
                    . '</div><div class="close">&nbsp;</div></li><li><div>'
                    . apply_filters('editor_addon_dropdown_top_message_' . $this->name, '')
                    . '</div><div class="direct-links">'
                    . $direct_links . '</div>' .$searchbar. '<div class="scroll"><div class="wrapper">'
                    . $menus_output . '</div><div></div>'
                    . apply_filters('editor_addon_dropdown_bottom_message' . $this->name, '')
                    . '</div></li></ul></li></ul>';

            // WP 3.3 changes
            if (version_compare($wp_version, '3.1.4', '>')) {
                echo apply_filters('wpv_add_media_buttons', $out);
            } else {
                return apply_filters('wpv_add_media_buttons', $context . $out);
            }
        }

        /**
         * Output a single menu item
         * @param string $menu
         * @param string $text_area
         * @param boolean $standard_v
         * @return string media menu
         */
        function _output_media_menu($menu, $text_area, $standard_v) {
            $all_post_types = implode(' ', get_post_types(array('public' => true)));

            $out = '';
            if (is_array($menu)) {
                foreach ($menu as $key => $menu_item) {
                    if (isset($menu_item[0]) && !is_array($menu_item[0])) {
                    	if(!isset($menu_item[3])) { break; }
                        if ($menu_item[3] != '') {
                        	if(!($key == 'css')) { // hide unnecessary elements from the V popup
                        		if(!$standard_v && (strpos($menu_item[3], 'wpcfFieldsEditorCallback') !== false ||
                                                    strpos($menu_item[3], 'wpcfFieldsEmailEditorCallback') !== false ||
                                                    strpos($menu_item[3], 'wpv_insert_view_form_popup') !== false)) {
                        			$out .= $this->wpv_parse_menu_item_from_addfield($menu_item);
                        		} else {
                            		$out .= '<a href="javascript:void(0);" class="item" onclick="' . $menu_item[3] . '; return false;">' . $menu_item[0] . "</a>\n";
                        		}
                        	}
                        } else {
                            $short_code = '[' . $menu_item[1] . ']';
                            $short_code = base64_encode($short_code);
//                             echo "<pre>";
//                             var_dump($menu);
//                             echo "</pre>"; 
                            if($standard_v) {
                            	$out .= '<a href="#" class="item" onclick="insert_b64_shortcode_to_editor(\'' . $short_code . '\', \'' . $text_area . '\'); return false;">' . $menu_item[0] . "</a>\n";
                            } else {
                            	$out .= $this->wpv_parse_menu_item_from_addfield($menu_item);
                            }
                        }
                    } else {
                        // a sum menu.
                        $css_classes = isset($menu_item['css']) ? $menu_item['css'] : '';
                        if($key == __('Taxonomy', 'wpv-views') || $key == __('Basic', 'wpv-views')) {
                        	$css_classes = $all_post_types;
                        }
                        $this->_media_menu_direct_links[] = '<a href="#" class="editor-addon-top-link" id="editor-addon-link-' . md5($key) . '">' . $key . ' </a>';
                        $out .= '<div class="group '. $css_classes .'"><div class="group-title" id="editor-addon-link-' . md5($key) . '-target">' . $key . "&nbsp;&nbsp;\n</div>\n";
                        $out .= $this->_output_media_menu($menu_item, $text_area, $standard_v);
                        $out .= "</div>\n";
                    }
                }
            }

            return $out;
        }

        /**
         * Parser for menu items in the add-field
         * @param unknown_type $key
         * @param unknown_type $menu_item
         * @return string
         */
        function wpv_parse_menu_item_from_addfield($menu_item) {
        	$param1 = '';
        	$slug = $menu_item[1];
        	
        	// search for wpv- starting fields first
        	if(strpos($slug, 'wpv-') !== false) {
        		$menuitem_parts = explode(' ', $slug);
        		$slug = $menuitem_parts[0];
        	}
        	// find types fields
        	else if((strpos($menu_item[3], 'wpcfFieldsEditorCallback') !== false)
        				|| (strpos($menu_item[3], 'wpcfFieldsEmailEditorCallback') !== false)
                        || (strpos($menu_item[3], 'wpv_insert_view_form_popup') !== false)) {
                return '<a href="javascript:void(0);" class="item" onclick="on_add_field_wpv_types_callback(\'' . esc_js($menu_item[3]) . '\', \'' . esc_js($menu_item[0]) . '\'); return false;">' . $menu_item[0] . "</a>\n";
        	} 
        	else if((preg_match('/types field="(.+)"/', $slug, $matches) > 0)
        				|| (preg_match('/type="(.+)"/', $slug, $matches) > 0)) {
        		$types_slug = $matches[1];
                $types_slug = str_replace('" class="" style="', '', $types_slug);
        		// convert Types fields to Views fields
        		$slug = $types_slug;
        		$param1 = 'Types-!-wpcf';
        	} 
        	else if(preg_match('/type="(.+)"/', $slug, $matches) > 0) {
        		$types_slug = $matches[1];
                $types_slug = str_replace('" class="" style="', '', $types_slug);
        		// convert field to Views field
        		$slug = $types_slug;
        		$param1 = 'Types-!-wpcf';
        		
        		// apply_filters() for Types shortcodes
        	} 
        	// for Basic group fields
        	if($menu_item[2] == __('Basic', 'wpv-views')) {
        		// don't use slug here, just field name.
        		$slug = $menu_item[0];
        	}
        	// View Templates here
        	if($menu_item[2] == __('View templates', 'wpv-views')) {
        		$param1 = 'View template';
        	}
        	if(strpos($slug, 'wpv-post-field') !== false) {
        		$param1 = 'Field';
        		$slug = $menu_item[0];
        	}
        	// Taxonomies
       		if(strpos($menu_item[1], 'wpv-post-taxonomy') !== false) {
       			$slug = $menu_item[1];
        		$param1 = 'Taxonomy';
        		if(preg_match('/wpv-post-taxonomy type="([^"]*)"/', $slug, $matches) > 0) {
        			$slug = 'wpvtax-'.$matches[1]; // split up and pass text only
        		} else {
	        		$slug = esc_html($menu_item[1]);
	        		$slug = str_replace('wpv-post-taxonomy', 'wpv-taxonomy', $slug);
        		}
        		/* $slug = esc_html($menu_item[1]);
        		$slug = str_replace('wpv-post-taxonomy', 'wpv-taxonomy', $slug); */
        	}
        	
        	return '<a href="javascript:void(0);" class="item" onclick="on_add_field_wpv(\''. $param1 . '\', \'' . esc_js($slug) . '\', \'' . base64_encode($menu_item[0]) . '\')">' . $menu_item[0] . "</a>\n";
        }
        
        // add parent items for Views and View Templates
        function wpv_add_parent_items($items) {
        	global $post, $pagenow;
        	
        	if ($pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'view-template') {
        		$this->add_view_template_parent_groups($items);
        	}
        	if($pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'view') {
        		
        	}
        	else if($pagenow == 'post.php' && isset($_GET['action']) && $_GET['action'] == 'edit') {
        		$post_type = $post->post_type;

        		if($post_type == 'view') {
        			$items = $this->add_view_parent_groups($items);
        		}
        		else if($post_type == 'view-template') {
        			$items = $this->add_view_template_parent_groups($items);
        		}
        	}
        	
        	return $items;
        }
        
        function add_view_parent_groups($items) {
        	
        }
        
        // add parent groups for vew templates
        function add_view_template_parent_groups($items) {
        	global $post;
        	// get current View ID
        	$view_template_id = $post->ID;
        	
        	// get all view templates attached in the Settings page for single view
        	$view_template_relations = $this->get_view_template_settings();

        	// find view template groups and get their parents
        	$current_types = array();
        	$parent_types = array();
        	foreach($view_template_relations as $relation=>$value) {
        		if($value == $view_template_id) {
        			$current_types[] = $relation;
        			if (function_exists('wpcf_pr_get_belongs')) {
        				$parent_types[] = wpcf_pr_get_belongs($relation);
        			}
        		}
        	}
        	
        	// get parent groups
        	$all_parent_groups = array();
        	foreach($parent_types as $type) {
        		foreach($type as $typename=>$typeval) {
        			$parent_groups = wpcf_admin_get_groups_by_post_type($typename);
        			
        		}
        	}
        	
        	
        }
        /*

          Add the wpv_views button to the toolbar.

         */

        function wpv_mce_add_button($buttons)
        {
            array_push($buttons, "separator", str_replace('-', '_', $this->name));
            return $buttons;
        }

        /*

          Register this plugin as a mce 'addon'
          Tell the mce editor the url of the javascript file.
         */

        function wpv_mce_register($plugin_array)
        {
            $plugin_array[str_replace('-', '_', $this->name)] = $this->plugin_js_url;
            return $plugin_array;
        }
        
        /**
         * 
         * Sort menus (and menu content) in an alphabetical order
         * 
         * Still, keep Basic and Taxonomy on the top and Other Fields at the bottom
         * 
         * @param array $menu menu reference
         */
	    function sort_menus_alphabetically($menus) {
    		// keep main references if set (not set on every screen)
   			$menu_basic[__('Basic', 'wpv-views')] = isset($menus[__('Basic', 'wpv-views')]) ? $menus[__('Basic', 'wpv-views')] : array();
 			$menu_taxonomy[__('Taxonomy', 'wpv-views')] = isset($menus[__('Taxonomy', 'wpv-views')]) ? $menus[__('Taxonomy', 'wpv-views')] : array();
 			$menu_field[__('Other Fields', 'wpv-views')] = isset($menus[__('Field', 'wpv-views')]) ? $menus[__('Field', 'wpv-views')] : array();
			$menu_vtemplate[__('View templates', 'wpv-views')] = isset($menus[__('View templates', 'wpv-views')]) ? $menus[__('View templates', 'wpv-views')] : array();
 			
 			// remove them to preserve correct listing
 			unset($menus[__('Basic', 'wpv-views')]);
 			unset($menus[__('Taxonomy', 'wpv-views')]);
 			unset($menus[__('Field', 'wpv-views')]);
 			unset($menus[__('View templates', 'wpv-views')]);

 			// sort all elements by key
            ksort($menus);
            
           	// add main elements in the correct order
            $menus = !empty($menu_taxonomy[__('Taxonomy', 'wpv-views')]) ? array_merge($menu_taxonomy, $menus) : $menus;
            $menus = !empty($menu_vtemplate[__('View templates', 'wpv-views')]) ? array_merge($menu_vtemplate, $menus) : $menus;
            $menus = !empty($menu_basic[__('Basic', 'wpv-views')]) ? array_merge($menu_basic, $menus): $menus;
            $menus = !empty($menu_field[__('Other Fields', 'wpv-views')]) ? array_merge($menus, $menu_field) : $menus;
            
            // sort inner elements in the submenus
            foreach($menus as $key=>$menu_group) {
            	if(is_array($menu_group)) {
            		ksort($menu_group);
            	}
            }
            
            return $menus;
	    }
	    
	    function get_search_bar() {
	    	$searchbar = '<div class="searchbar">';
	    	$searchbar .= '<span>'. __('Search', 'wpv-views') .': </span>';
	    	$searchbar .= '<input type="text" class="search_field" onkeyup="wpv_on_search_filter(this)" />';
	    	$searchbar .= '<input type="button" class="search_clear" value="'.__('Clear', 'wpv-views'). '" onclick="wpv_search_clear(this)" style="display: none;" />';
	    	$searchbar .= '</div>';
	    
	    	return $searchbar;
	    }
	    
	    function add_view_templates(&$menus) {
	    	global $wpdb;
            $all_post_types = implode(' ', get_post_types(array('public' => true)));
	    	
	    	$view_templates_available = $wpdb->get_results("SELECT ID, post_title, post_name FROM {$wpdb->posts} WHERE post_type='view-template' AND post_status in ('publish')");
	    	$menus[__('View templates', 'wpv-views')] = array();
            $menus[__('View templates', 'wpv-views')]['css'] = $all_post_types;
	    	
	    	$vtemplate_index = 0;
	    	foreach($view_templates_available as $vtemplate) {
	    		 $menus[__('View templates', 'wpv-views')][$vtemplate_index] = array();
				 $menus[__('View templates', 'wpv-views')][$vtemplate_index][] = $vtemplate->post_title;
				 $menus[__('View templates', 'wpv-views')][$vtemplate_index][] = $vtemplate->post_name;
				 $menus[__('View templates', 'wpv-views')][$vtemplate_index][] = __('View templates', 'wpv-views');
				 $menus[__('View templates', 'wpv-views')][$vtemplate_index][] = '';
				 $vtemplate_index++; 
	    	}
	    }
	    
	    function get_view_template_settings() {
	    	$post_types = get_post_types();
	    	
	    	$options = array();
	    	$wpv_options = get_option('wpv_options');
	    	
	    	foreach($post_types as $type) {
	    		if(isset($wpv_options['views_template_for_'. $type]) && !empty($wpv_options['views_template_for_'. $type])) {
	    			$options[$type] = $wpv_options['views_template_for_'. $type];
	    		}		
	    	}		 
	    
	    	return $options;
	    }
	    
    }
    
    function editor_add_js() {
        global $pagenow;

        if ($pagenow == 'post.php' || $pagenow == 'post-new.php') {

            wp_enqueue_script('icl_editor-script',
                    EDITOR_ADDON_RELPATH . '/res/js/icl_editor_addon_plugin.js',
                    array());
        }
    }
    
    

    /**
     * Renders JS for inserting shortcode from thickbox popup to editor.
     * 
     * @param type $shortcode 
     */
    function editor_admin_popup_insert_shortcode_js($shortcode) {
    
        ?>
        <script type="text/javascript">
            //<![CDATA[
            window.parent.jQuery('#TB_closeWindowButton').trigger('click');
            
            if (window.parent.wpcfFieldsEditorCallback_redirect) {
                eval(window.parent.wpcfFieldsEditorCallback_redirect['function'] + '(\'<?php echo esc_js($shortcode); ?>\', window.parent.wpcfFieldsEditorCallback_redirect[\'params\'])');
            } else {
            
                if (window.parent.wpcfActiveEditor != false) {
                    if (window.parent.jQuery('textarea#'+window.parent.wpcfActiveEditor+':visible').length) {
                        // HTML editor
                        window.parent.jQuery('textarea#'+window.parent.wpcfActiveEditor).insertAtCaret('<?php echo $shortcode; ?>');
                    } else {
                        // Visual editor
                        window.parent.tinyMCE.execCommand('mceFocus', false, window.parent.wpcfActiveEditor);
                        window.parent.tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<?php echo $shortcode; ?>');
                    }
                } else if (window.parent.wpcfInsertMetaHTML == false) {
                    if (window.parent.jQuery('textarea#content:visible').length) {
                        // HTML editor
                        window.parent.jQuery('textarea#content').insertAtCaret('<?php echo $shortcode; ?>');
                    } else {
                        // Visual editor
                        window.parent.tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<?php echo $shortcode; ?>');
                    }
                } else {
                    window.parent.jQuery('#'+window.parent.wpcfInsertMetaHTML).insertAtCaret('<?php echo $shortcode; ?>');
                    window.parent.wpcfInsertMetaHTML = false;
                }
            }
            
            //]]>
        </script>
        <?php
    }

}

