<?php


/* Handle the short codes for creating a user query form
  
  [wpv-filter-start]
  [wpv-filter-end]
  [wpv-filter-submit]
  
*/

/**
 * Views-Shortcode: wpv-filter-start
 *
 * Description: The [wpv-filter-start] shortcode specifies the start point
 * for any controls that the views filter generates. Example controls are
 * pagination controls and search boxes. This shortcode is usually added
 * automatically to the Views Meta HTML.
 *
 * Parameters:
 * This takes no parameters.
 *
 */
  
add_shortcode('wpv-filter-start', 'wpv_filter_shortcode_start');
function wpv_filter_shortcode_start($atts){
    if (_wpv_filter_is_form_required()) {
        global $WP_Views;
    
        extract(
            shortcode_atts( array(), $atts )
        );
        
        $hide = '';
        if (isset($atts['hide']) && $atts['hide'] == 'true') {
            $hide = ' display:none;';
        }
        
        $url = get_permalink();
        $out = '<form style="margin:0; padding:0;' . $hide . '" id="wpv-filter-' . $WP_Views->get_view_count() . '" action="' . $url . '" method="GET" class="wpv-filter-form"' . ">\n";
        
        // add hidden inputs for any url parameters.
        // We need these for when the form is submitted.
        $url_query = parse_url($url, PHP_URL_QUERY);
        if ($url_query != '') {
            $query_parts = explode('&', $url_query);
            foreach($query_parts as $param) {
                $item = explode('=', $param);
                if (strpos($item[0], 'wpv_') !== 0) {
                    $out .= '<input id="wpv_param_' . $item[0] . '" type="hidden" name="' . $item[0] . '" value="' . $item[1] . '">' . "\n";
                }
            }
        }
        
        // add hidden inputs for column sorting id and direction.
        // these are empty to start off with but will be populated
        // when a column title is clicked.
        
        $view_layout_settings = $WP_Views->get_view_layout_settings();
        if (!isset($view_layout_settings['style'])) {
            return '';
        }
    
        $view_settings = $WP_Views->get_view_settings();
        
        if ($view_layout_settings['style'] == 'table_of_fields') {
            
            if ($view_settings['query_type'][0] == 'posts') {
                $sort_id = $view_settings['orderby'];
                $sort_dir = strtolower($view_settings['order']);
            }
            if ($view_settings['query_type'][0] == 'taxonomy') {
                $sort_id = $view_settings['taxonomy_orderby'];
                $sort_dir = strtolower($view_settings['taxonomy_order']);
            }

            if (isset($_GET['wpv_column_sort_id'])) {
                $sort_id = $_GET['wpv_column_sort_id'];
            }
            if (isset($_GET['wpv_column_sort_dir'])) {
                $sort_dir = $_GET['wpv_column_sort_dir'];
            }
            
            $out .= '<input id="wpv_column_sort_id" type="hidden" name="wpv_column_sort_id" value="' . $sort_id . '">' . "\n";
            $out .= '<input id="wpv_column_sort_dir" type="hidden" name="wpv_column_sort_dir" value="' . $sort_dir . '">' . "\n";
            
            $out .= "
            <script type=\"text/javascript\">
                function wpv_column_head_click(name, direction) {
                    jQuery('#wpv_column_sort_id').val(name);
                    jQuery('#wpv_column_sort_dir').val(direction);
                    wpv_add_url_controls_for_column_sort(jQuery('#wpv-filter-" . $WP_Views->get_view_count() . "'));
                    jQuery('#wpv-filter-" . $WP_Views->get_view_count() . "').submit();
                    return false;
                }
            </script>
            ";
        }
        
        // add a hidden input for the current page.
        $page = '1';
        if (isset($_GET['wpv_paged']) && isset($_GET['wpv_view_count']) && $_GET['wpv_view_count'] == $WP_Views->get_view_count()) {
            $page = $_GET['wpv_paged'];
        }
        $out .= '<input id="wpv_paged-' . $WP_Views->get_view_count() . '" type="hidden" name="wpv_paged" value="' . $page . '">' . "\n";
        $out .= '<input id="wpv_paged_max-' . $WP_Views->get_view_count() . '" type="hidden" name="wpv_paged_max" value="' . intval($WP_Views->get_max_pages()) . '">' . "\n";
        
        $out .= '<input id="wpv_widget_view-' . $WP_Views->get_view_count() . '" type="hidden" name="wpv_widget_view_id" value="' . intval($WP_Views->get_widget_view_id()) . '">' . "\n";
        $out .= '<input id="wpv_view_count-' . $WP_Views->get_view_count() . '" type="hidden" name="wpv_view_count" value="' . $WP_Views->get_view_count() . '">' . "\n";

        $view_data = $WP_Views->get_view_shortcodes_attributes();
        //$view_data['view_id'] = $WP_Views->get_current_view();
        $out .= '<input id="wpv_view_hash-' . $WP_Views->get_view_count() . '" type="hidden" name="wpv_view_hash" value="' . base64_encode(serialize($view_data)) . '">' . "\n";
    
        // Output the current page ID. This is used for ajax call back in pagination.
        $current_post = $WP_Views->get_top_current_page();
        if ($current_post && isset($current_post->ID)) {
            $out .= '<input id="wpv_post_id-' . $WP_Views->get_view_count() . '" type="hidden" name="wpv_post_id" value="' . $current_post->ID . '">' . "\n";
        }
        add_action('wp_footer', 'wpv_pagination_js');
        
        // Rollover
        if (isset($view_settings['pagination']['mode']) && $view_settings['pagination']['mode'] == 'rollover') {
            wpv_pagination_rollover_shortcode();
        }
        return $out;
    } else {
        return '';
    }
}

/**
 * Views-Shortcode: wpv-filter-end
 *
 * Description: The [wpv-filter-end] shortcode is the end point
 * for any controls that the views filter generates.
 *
 * Parameters:
 * This takes no parameters.
 *
 */
  
add_shortcode('wpv-filter-end', 'wpv_filter_shortcode_end');
function wpv_filter_shortcode_end($atts){
    if (_wpv_filter_is_form_required()) {
        extract(
            shortcode_atts( array(), $atts )
        );
        
        return '</form>';
    } else {
        return '';
    }
}
    
function _wpv_filter_is_form_required() {
    global $WP_Views;
    
    if ($WP_Views->rendering_views_form()) {
        return true;
    }
    
    $view_layout_settings = $WP_Views->get_view_layout_settings();
    if (!isset($view_layout_settings['style'])) {
        return false;
    }

    if ($view_layout_settings['style'] == 'table_of_fields') {
        // required for table sorting
        return true;
    }

    $view_settings = $WP_Views->get_view_settings();
    if ($view_settings['pagination'][0] == 'enable' || $view_settings['pagination']['mode'] == 'rollover') {
        return true;
    }

    $meta_html = $view_settings['filter_meta_html'];
    if(preg_match('#\\[wpv-filter-controls\\](.*?)\\[\/wpv-filter-controls\\]#is', $meta_html, $matches)) {
        return true;
    }


    if (isset($view_settings['post_search_value'])) {
        return true;
    }
    
    return false;
}

/**
 * Views-Shortcode: wpv-filter-submit
 *
 * Description: The [wpv-filter-submit] shortcode adds a submit button to
 * the form that the views filter generates. An example is the "Submit" button
 * for a search box
 *
 * Parameters:
 * 'hide' => 'true'|'false'
 * 'name' => The text to be used on the button.
 *
 */
  
add_shortcode('wpv-filter-submit', 'wpv_filter_shortcode_submit');
function wpv_filter_shortcode_submit($atts){

    if (_wpv_filter_is_form_required()) {
        extract(
            shortcode_atts( array(), $atts )
        );
        
        $hide = '';
        if (isset($atts['hide']) && $atts['hide'] == 'true') {
            $hide = ' style="display:none"';
        }
        
        $out = '';
        $out .= '<input type="submit" value="' . $atts['name'] . '" name="wpv_filter_submit"' . $hide . '>';
        return $out;
    } else {
        return '';
    }
}

/**
 * Views-Shortcode: wpv-post-count
 *
 * Description: The [wpv-post-count] shortcode will display the number of
 * posts that will be displayed on the page
 *
 * Parameters:
 * This takes no parameters.
 *
 */
  

add_shortcode('wpv-post-count', 'wpv_post_count');
function wpv_post_count($atts){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    
    $query = $WP_Views->get_query();
    
    if ($query) {
        return $query->post_count;
    } else {
        return '';
    }
}
    
/**
 * Views-Shortcode: wpv-found-count
 *
 * Description: The [wpv-found-count] shortcode will display the total number of
 * posts that have been found by the Views query
 *
 * Parameters:
 * This takes no parameters.
 *
 */
  
add_shortcode('wpv-found-count', 'wpv_found_count');
function wpv_found_count($atts){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    
    $query = $WP_Views->get_query();
    
    if ($query) {
        return $query->found_posts;
    } else {
        return '';
    }
}

/**
 * Views-Shortcode: wpv-posts-found
 *
 * Description: The wpv-posts-found shortcode will display the text inside
 * the shortcode if there are posts found by the Views query.
 * eg. [wpv-posts-found]<strong>Some posts were found</strong>[/wpv-posts-found]
 *
 * Parameters:
 * This takes no parameters.
 *
 */
  
add_shortcode('wpv-posts-found', 'wpv_posts_found');
function wpv_posts_found($atts, $value){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    
    $query = $WP_Views->get_query();

    if ($query && ($query->found_posts != 0 || $query->post_count != 0)) {
        // display the message when posts are found.
        return wpv_do_shortcode($value);
    } else {
        return '';
    }
    
}
    
/**
 * Views-Shortcode: wpv-no-posts-found
 *
 * Description: The wpv-no-posts-found shortcode will display the text inside
 * the shortcode if there are no posts found by the Views query.
 * eg. [wpv-no-posts-found]<strong>No posts found</strong>[/wpv-no-posts-found]
 *
 * Parameters:
 * This takes no parameters.
 *
 */
  
add_shortcode('wpv-no-posts-found', 'wpv_no_posts_found');
function wpv_no_posts_found($atts, $value){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    
    $query = $WP_Views->get_query();

    if ($query && $query->found_posts == 0 && $query->post_count == 0) {
        // display the message when no posts are found.
        return wpv_do_shortcode($value);
    } else {
        return '';
    }
    
}
    
/*
         
    This shows the user interface to the end user on page
    that contains the view.
    
*/

function wpv_filter_show_user_interface($name, $values, $selected, $style) {
    $out = '';
    $out .= "<div>\n";
    
    if ($style == 'drop_down') {
        $out .= '<select name="'. $name . '[]">' . "\n";
    }
    
    foreach($values as $v) {
        switch ($style) {
            case "checkboxes":
                if (is_array($selected)) {
                    $checked = @in_array($v, $selected) ? ' checked="checked"' : '';
                } else {
                    $checked = $v == $selected ? ' checked="checked"' : '';
                }
                $out .= '<label><input type="checkbox" name="' . $name. '[]" value="' . $v . '" ' . $checked . '>&nbsp;' . $v . "</label>\n";
                break;

            case "radios":
                if (is_array($selected)) {
                    $checked = @in_array($v, $selected) ? ' checked="checked"' : '';
                } else {
                    $checked = $v == $selected ? ' checked="checked"' : '';
                }
                $out .= '<label><input type="radio" name="' . $name. '[]" value="' . $v . '" ' . $checked . '>&nbsp;' . $v . "</label>\n";
                break;

            case "drop_down":
                if (is_array($selected)) {
                    $is_selected = @in_array($v, $selected) ? ' selected="selected"' : '';
                } else {
                    $is_selected = $v == $selected ? ' selected="selected"' : '';
                }
                $out .= '<option value="' . $v . '" ' . $is_selected . '>' . $v . "</option>\n";
                break;
        }
    }

    if ($style == 'drop_down') {
        $out .= "</select>\n";
    }
    
    $out .= "</div>\n";
    
    return $out;
}


/**
 * 
 *Views-Shortcode: wpv-control
*
* Description: Add filters for View
*
* Parameters:
* type: type of retrieved field layout (radio, checkbox, select, textfield, checkboxes, datepicker)
* url_param: the URL parameter passed as an argument
* values: Optional. a list of supplied values
* display_values: Optional. A list of values to display for the corresponding values
* auto_fill: Optional. When set to a "field-slug" the control will be populated with custom field values from the database.
* auto_fill_default: Optional. Used to set the default, unselected, value of the control. eg Ignore or Don't care
* field: Optional. a Types field to retrieve values from
* title: Optional. Use for the checkbox title
* taxonomy: Optional. Use when a taxonomy control should be displayed.
* date_format: Optional. Used for a datepicker control
*
* @param array $atts An associative array of arributes to be used.
 */
function wpv_shortcode_wpv_control($atts) {
	
	if(!isset($atts['url_param'])) {
		return __('The url_param is missing from the wpv-control shortcode argument.', 'wpv-views');	
	}
    
    if((!isset($atts['type']) || $atts == '') && !isset($atts['field'])) {
		return __('The "type" or "field" needs to be set in the wpv-control shortcode argument.', 'wpv-views');	
    }
	
	extract(
		shortcode_atts(array(
				'type' => '',
				'values' => array(),
                'display_values' => array(),
				'field' => '',
				'url_param' => '',
                'title' => '',
                'taxonomy' => '',
                'auto_fill' => '',
                'auto_fill_default' => '',
                'date_format' => ''
			), $atts)
	);
	
    if ($taxonomy != '') {
        return _wpv_render_taxonomy_control($taxonomy, $type, $url_param);
    }
    
    if ($auto_fill != '') {
        global $wpdb;
        
        $db_values = $wpdb->get_col("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key = '{$auto_fill}'" );
        
        if ($auto_fill_default != '') {
            $values = '';
            $display_values = $auto_fill_default;
            $first = false;
        } else {
            $values = '';
            $display_values = '';
            $first = true;
        }
        foreach($db_values as $value) {
            if ($value) {
                if (!$first) {
                    $values .= ',';
                    $display_values .= ',';
                }
                $values .= $value;
                $display_values .= $value;
                $first = $false;
                
            }            
        }
    }
    
	$out = '';
	
	// Use when values attributes are defined (predefined values to list)
	if(!empty($values)) {
		$values_arr = explode(',', $values);
        if (!empty($display_values)) {
            $display_values = explode(',', $display_values);
        }
        
		$options = array();
		
        if(!in_array($type, array('radio', 'radios', 'select', 'checkboxes'))) {
            $type = 'select';
        }
        
        if ($type == 'radio') {
            $type = 'radios';
        }

        switch ($type) {
            case 'checkboxes':
                $defaults = array();
                $original_get = null;
                if (isset($_GET[$url_param])) {
                    
                    $original_get = $_GET[$url_param];
                    
                    $defaults = $_GET[$url_param];
                    unset($_GET[$url_param]);
                }
                for($i = 0; $i < count($values_arr); $i++) {
                    $value = $values_arr[$i];
                    $value = trim($value);
                    
                    // Check for a display value.
                    if (isset($display_values[$i])) {
                        $display_value = $display_values[$i];
                    } else {
                        $display_value = $value;
                    }
                    $options[$value]['#name'] = $url_param . '[]';
                    $options[$value]['#title'] = $display_value;
                    $options[$value]['#value'] = $value;
                    $options[$value]['#default_value'] = in_array($value, $defaults);
//                    $options[$value]['#inline'] = true;
//                    $options[$value]['#after'] = '&nbsp;&nbsp;';
                }

                $element = wpv_form_control(array('field' => array(
                                '#type' => $type,
                                '#id' => 'wpv_control_' . $type . '_' . $url_param,
                                '#name' => $url_param . '[]',
                                '#attributes' => array('style' => ''),
                                '#inline' => true,
                                '#options' => $options,
                                )));
                
                if ($original_get) {
                    $_GET[$url_param] = $original_get;
                }

                break;
            
            default:
                for($i = 0; $i < count($values_arr); $i++) {
                    $value = $values_arr[$i];
                    $value = trim($value);
                    
                    // Check for a display value.
                    if (isset($display_values[$i])) {
                        $display_value = $display_values[$i];
                    } else {
                        $display_value = $value;
                    }
                    $options[$display_value] = $value;
                }

                $default_value = $values_arr[0];
                if(isset($_GET[$url_param]) && in_array($_GET[$url_param], $options)) {
                    $default_value = $_GET[$url_param];
                }
        
                $element = wpv_form_control(array('field' => array(
                                '#type' => $type,
                                '#id' => 'wpv_control_' . $type . '_' . $url_param,
                                '#name' => $url_param,
                                '#attributes' => array('style' => ''),
                                '#inline' => true,
                                '#options' => $options,
                                '#default_value' => $default_value,
                         )));
                         
                break;
        }
        
		
         return $element;
	} 
	
	// Use when field attribute is defined
	else if(!empty($field)) {
		// check if Types is active
		if(!function_exists('wpcf_admin_fields_get_field')) {
			if(defined('WPCF_ABSPATH')) {
				include WPCF_ABSPATH . '/embedded/includes/fields.php';
			} else {
				return __('Types plugin is required.', 'wpv-views');			
			}
		}
		if(!function_exists('wpv_form_control')) {
			include  '../common/functions.php';
		}
		
		// get field options
		$field_options = wpcf_admin_fields_get_field($field);
		if(empty($field_options)) {
			return __('Empty field values or incorrect field defined. ', 'wpv-views');
		}
			
			
		// get the type of custom field (radio, checkbox, other)
		$field_type = $field_options['type'];
		
		// override with type
		if(!empty($type)) {
			$field_type = $type;
		}
        
        if (!in_array($field_type, array('radio', 'checkbox', 'checkboxes', 'select', 'textfield', 'date', 'datepicker' ))) {
            $field_type = 'textfield';
        }
        
		// Radio field
		if($field_type == 'radio') {
			$field_radio_options = $field_options['data']['options'];
			$options = array();
				
			foreach($field_radio_options as $key=>$opts) {
				if(is_array($opts)) {
					$options[$opts['title']] = $opts['value'];
				} 
			}

            
				
			// get the form content
			$element = wpv_form_control(array('field' => array(
                        '#type' => 'radios',
                        '#id' => 'wpv_control_radio_' . $field,
                        '#name' => $url_param,
                        '#attributes' => array('style' => ''),
                        '#inline' => true,
				 		'#options' => $options,
                        '#default_value' => isset($_GET[$url_param]) ? $_GET[$url_param] : null,
                  )));
				
            return $element;
		} else if($field_type == 'checkbox') {
			
            if (isset($atts['title'])) {
                $checkbox_name = $title;
            } else {
                $checkbox_name = $field_options['name'];
            }

			$element = wpv_form_control(array('field' => array(
                        '#type' => 'checkbox',
                        '#id' => 'wpv_control_checkbox_' . $field,
                        '#name' => $url_param,
                        '#attributes' => array('style' => ''),
                        '#inline' => true,
				 		'#title' => $checkbox_name,
						'#value' => $field_options['data']['set_value'],
						'#default_value' => 0
                 )));
                        
            return $element;
		} else if($field_type == 'checkboxes') {
			
            $defaults = array();
            $original_get = null;
            if (isset($_GET[$url_param])) {

                $original_get = $_GET[$url_param];

                $defaults = $_GET[$url_param];
                unset($_GET[$url_param]);
            }
            foreach($field_options['data']['options'] as $value) {
                $value = trim($value['title']);
                $display_value = $value;

                $options[$value]['#name'] = $url_param . '[]';
                $options[$value]['#title'] = $display_value;
                $options[$value]['#value'] = $value;
                $options[$value]['#default_value'] = in_array($value, $defaults);
//                $options[$value]['#inline'] = true;
//                $options[$value]['#after'] = '&nbsp;&nbsp;';
            }

            $element = wpv_form_control(array('field' => array(
                            '#type' => 'checkboxes',
                            '#id' => 'wpv_control_checkbox_' . $field,
                            '#name' => $url_param . '[]',
                            '#attributes' => array('style' => ''),
                            '#inline' => true,
                            '#options' => $options,
                            )));
            
            if ($original_get) {
                $_GET[$url_param] = $original_get;
            }

            
            return $element;
                
		} else if($field_type == 'select') {
			$field_select_options = $field_options['data']['options'];
			$options = array();
				
			foreach($field_select_options as $key=>$opts) {
				if(is_array($opts)) {
					$options[$opts['title']] = $opts['value'];
				} 
			}			
			
			$default_value = false;
			if(isset($_GET[$url_param]) && in_array($_GET[$url_param], $options)) {
				$default_value = $_GET[$url_param];
			}
			
			$element = wpv_form_control(array('field' => array(
	                        '#type' => 'select',
	                        '#id' => 'wpv_control_select_' . $url_param,
	                        '#name' => $url_param,
	                        '#attributes' => array('style' => ''),
	                        '#inline' => true,
							'#options' => $options,
							'#default_value' => $default_value,
	                 )));
	                 
	        return $element;
		}
		else if($field_type == 'textfield') {
			$default_value = '';
			if(isset($_GET[$url_param])) {
				$default_value = $_GET[$url_param];
			}
			
			$element = wpv_form_control(array('field' => array(
	                        '#type' => 'textfield',
	                        '#id' => 'wpv_control_textfield_' . $url_param,
	                        '#name' => $url_param,
	                        '#attributes' => array('style' => ''),
	                        '#inline' => true,
							'#value' => $default_value,
	                 )));
	                 
	        return $element;
		}
		else if($field_type == 'date' || $field_type == 'datepicker') {
     
            $out = wpv_render_datepicker($url_param, $date_format);
            return $out;
        }
			
		return ''; 
	} else {
        // type parameter without values
        
        $default_value = '';
        if(isset($_GET[$url_param])) {
            $default_value = $_GET[$url_param];
        }
        
        switch ($type) {
            case 'checkbox':
                $element = array('field' => array(
                                '#type' => $type,
                                '#id' => 'wpv_control_' . $type . '_' . $url_param,
                                '#name' => $url_param,
                                '#attributes' => array('style' => ''),
                                '#inline' => true,
                                '#value' => $default_value,
                                ));
                
                $element['field']['#title'] = $title;
                    
                $element = wpv_form_control($element);
                
                break;
            
            case 'datepicker':
                $element = wpv_render_datepicker($url_param, $date_format);
                break;

            default:
                
                $element = array('field' => array(
                                '#type' => $type,
                                '#id' => 'wpv_control_' . $type . '_' . $url_param,
                                '#name' => $url_param,
                                '#attributes' => array('style' => ''),
                                '#inline' => true,
                                '#value' => $default_value,
                                ));
                $element = wpv_form_control($element);
                break;
        }

        return $element;
    }
}

function wpv_add_front_end_js() {
    ?>
        <script type="text/javascript">
            var front_ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            var wpv_calendar_image = '<?php echo WPV_URL_EMBEDDED; ?>/res/img/calendar.gif';
            var wpv_calendar_text = '<?php echo esc_js(__('Select date', 'wpv-views')); ?>';
        </script>
    
    <?php
    
}

function wpv_render_datepicker($url_param, $date_format) {
    
    if ($date_format == '') {
        $date_format = get_option('date_format');
    }
    
    if(isset($_GET[$url_param]) && $_GET[$url_param] != '' && $_GET[$url_param] != '0') {
        $date = $_GET[$url_param];
    } else {
        $date = time();
    }

    $display_date = date($date_format, intval($date));

    
    $out = '';
    $out .= '<span class="wpv_date_input" onclick="jQuery(this).next().next().next().datepicker(\'show\')" >' . $display_date . '</span> ';
    $out .= '<input type="hidden" name="' . $url_param . '" value="' . $date . '" />';
    $out .= '<input type="hidden" name="' . $url_param . '-format" value="' . $date_format . '" />';

    $datepicker_date = date('dmy', intval($date));
    $out .= '<input type="hidden" class="wpv-date-front-end" value="' . $datepicker_date . '"/>';

    return $out;    
}

class Walker_Category_select extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

    function __construct($selected_id, $slug_mode = false){
		$this->selected = $selected_id;
        $this->slug_mode = $slug_mode;
	}
	
	function start_lvl(&$output, $depth, $args) {
	}

	function end_lvl(&$output, $depth, $args) {
	}

	function start_el(&$output, $category, $depth, $args) {
		extract($args);
		
		$indent = str_repeat('-', $depth);
		if ($indent != '') {
			$indent = '&nbsp;' . str_repeat('&nbsp;', $depth) . $indent;
		}
		
        if ($this->slug_mode) {
            $selected = $this->selected == $category->slug ? ' selected="selected"' : '';
    		$output .= '<option value="' . $category->slug. '"' . $selected . '>' . $indent . $category->name . "</option>\n";
        } else {
            $selected = $this->selected == $category->term_id ? ' selected="selected"' : '';
    		$output .= '<option value="' . $category->term_id. '"' . $selected . '>' . $indent . $category->name . "</option>\n";
        }
	}

	function end_el(&$output, $category, $depth, $args) {
	}
}


function _wpv_render_taxonomy_control($taxonomy, $type, $url_param) {
    
    if(!class_exists('Walker_Category_Checklist')){
    
        // We need to include the taxonomy checkboxes as there not
        // available in the front end.
    
        class Walker_Category_Checklist extends Walker {
            var $tree_type = 'category';
            var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this
         
            function start_lvl(&$output, $depth, $args) {
                $indent = str_repeat("\t", $depth);
                $output .= "$indent<ul class='children'>\n";
                        //$output .= "$indent\n";
            }
         
            function end_lvl(&$output, $depth, $args) {
                $indent = str_repeat("\t", $depth);
                $output .= "$indent</ul>\n";
                        //$output .= "$indent\n";
            }
         
            function start_el(&$output, $category, $depth, $args) {
                extract($args);
                if ( empty($taxonomy) )
                    $taxonomy = 'category';
         
                if ( $taxonomy == 'category' )
                    $name = 'post_category';
                else
                    $name = $taxonomy;
         
                $class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
                // NOTE: were outputing the "slug" and not the "term-id".
                // WP outputs the "term-id"
                $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $category->slug . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->slug, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
            }
         
            function end_el(&$output, $category, $depth, $args) {
                $output .= "</li>\n";
                        //$output .= "\n";
            }
        }
    }
    /**
     * Taxonomy independent version of wp_category_checklist
     *
     * @since 3.0.0
     *
     * @param int $post_id
     * @param array $args
     */
    if(!function_exists('wp_terms_checklist')){
        function wp_terms_checklist($post_id = 0, $args = array()) {
            $defaults = array(
                'descendants_and_self' => 0,
                'selected_cats' => false,
                'popular_cats' => false,
                'walker' => null,
                'taxonomy' => 'category',
                'checked_ontop' => false
            );
            extract( wp_parse_args($args, $defaults), EXTR_SKIP );
         
            if ( empty($walker) || !is_a($walker, 'Walker') )
                $walker = new Walker_Category_Checklist;
         
            $descendants_and_self = (int) $descendants_and_self;
         
            $args = array('taxonomy' => $taxonomy);
         
            $tax = get_taxonomy($taxonomy);
            $args['disabled'] = false;
         
            if ( is_array( $selected_cats ) )
                $args['selected_cats'] = $selected_cats;
            elseif ( $post_id )
                $args['selected_cats'] = wp_get_object_terms($post_id, $taxonomy, array_merge($args, array('fields' => 'ids')));
            else
                $args['selected_cats'] = array();
         
            if ( is_array( $popular_cats ) )
                $args['popular_cats'] = $popular_cats;
            else
                $args['popular_cats'] = get_terms( $taxonomy, array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
         
            if ( $descendants_and_self ) {
                $categories = (array) get_terms($taxonomy, array( 'child_of' => $descendants_and_self, 'hierarchical' => 0, 'hide_empty' => 0 ) );
                $self = get_term( $descendants_and_self, $taxonomy );
                array_unshift( $categories, $self );
            } else {
                $categories = (array) get_terms($taxonomy, array('get' => 'all'));
            }
         
            if ( $checked_ontop ) {
                // Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
                $checked_categories = array();
                $keys = array_keys( $categories );
         
                foreach( $keys as $k ) {
                    if ( in_array( $categories[$k]->term_id, $args['selected_cats'] ) ) {
                        $checked_categories[] = $categories[$k];
                        unset( $categories[$k] );
                    }
                }
         
                // Put checked cats on top
                echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
            }
            // Then the rest of them
            echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
        }
    }

    
    $terms = array();
    
    if (isset($_GET[$url_param])) {
        if (is_array($_GET[$url_param])) {
            $terms = $_GET[$url_param];
        } else {
            // support csv terms
            $terms = explode(',', $_GET[$url_param]);
        }
    }    

    ob_start();
    ?>
        

		<?php
            if ($type == 'select') {
                $name = $taxonomy;
                if ($name == 'category') {
                    $name = 'post_category';
                }
        		echo '<select name="' . $name . '">';
    			echo '<option selected="selected" value="0"></option>';
                $temp_slug = 0;
                if (count($terms)) {
                    $temp_slug = $terms[0];
                }
        		$my_walker = new Walker_Category_select($temp_slug, true);
                wp_terms_checklist(0, array('taxonomy' => $taxonomy, 'selected_cats' => $terms, 'walker' => $my_walker));
                echo '</select>';
            } else {
        		echo '<ul class="categorychecklist form-no-clear">';
                wp_terms_checklist(0, array('taxonomy' => $taxonomy, 'selected_cats' => $terms));
        		echo '</ul>';
            }
            
        ?>
		
    <?php
    
    $taxonomy_check_list = ob_get_clean();
    
    if ($taxonomy == 'category') {
        $taxonomy_check_list = str_replace('name="post_category', 'name="' . $url_param, $taxonomy_check_list);
    } else {
        $taxonomy_check_list = str_replace('name="' . $taxonomy, 'name="' . $url_param, $taxonomy_check_list);
    }
    
    return $taxonomy_check_list;
    
}

add_shortcode('wpv-control', 'wpv_shortcode_wpv_control');


add_shortcode('wpv-filter-controls', 'wpv_shortcode_wpv_filter_controls');
function wpv_shortcode_wpv_filter_controls($atts, $value) {
    
    /**
     *
     * This is a do nothing shortcode. It's just a place holder for putting the
     * wpv-control shortcodes and allows for easier editing inside the meta HTML
     *
     */
    
    $value = str_replace("<!-- ADD USER CONTROLS HERE -->", '', $value);
    
    return wpv_do_shortcode($value);
    
}
