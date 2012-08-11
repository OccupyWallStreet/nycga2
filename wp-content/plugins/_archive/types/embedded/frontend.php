<?php
/*
 * Frontend functions.
 */

global $wp_version;

if (version_compare($wp_version, '3.3', '<')) {
    // add a the_content filter to allow types shortcodes to be closed.
    // This is a bit of a HACK for version 3.2.1 and less

    add_filter('the_content', 'wpcf_fix_closed_types_shortcodes', 9, 1);
    add_filter('the_content', 'wpcf_fix_closed_types_shortcodes_after', 11, 1);

    function wpcf_fix_closed_types_shortcodes($content) {
        $content = str_replace('][/types', ']###TYPES###[/types', $content);
        return $content;
    }

    function wpcf_fix_closed_types_shortcodes_after($content) {
        $content = str_replace('###TYPES###', '', $content);
        return $content;
    }

}

add_shortcode('types', 'wpcf_shortcode');

function wpcf_shortcode($atts, $content = null, $code = '') {

    // Switch the post if there is an attribute of 'id' in the shortcode.
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    $atts = array_merge(array(
        'field' => false,
        'style' => '',
        'show_name' => false,
        'raw' => false,
            ), $atts
    );
    if ($atts['field']) {
        return types_render_field($atts['field'], $atts, $content, $code);
    }
    return '';
}

/**
 * Calls view function for specific field type.
 * 
 * @param type $field
 * @param type $atts
 * @return type 
 */
function types_render_field($field_id, $params, $content = null, $code = '') {
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
    global $post;

    // Get field
    $field = wpcf_fields_get_field_by_slug($field_id);
    if (empty($field)) {
        if (!function_exists('wplogger')) {
            require_once WPCF_EMBEDDED_ABSPATH . '/common/wplogger.php';
        }
        global $wplogger;
        $wplogger->log('types_render_field call for missing field \''
                . $field_id . '\'', WPLOG_DEBUG);
        return '';
    }

    // See if repetitive
    if (wpcf_admin_is_repetitive($field)) {
        $meta = get_post_meta($post->ID,
                wpcf_types_get_meta_prefix($field) . $field['slug'], false);
        // Sometimes if meta is empty - array(0 => '') is returned
        if ((count($meta) == 1 && strval($meta[0]) == '')) {
            return '';
        }
        if (!empty($meta)) {
            $output = '';

            if (isset($params['index'])) {
                $index = $params['index'];
            } else {
                $index = '';
            }

            // Allow wpv-for-each shortcode to set the index
            $index = apply_filters('wpv-for-each-index', $index);


            if ($index === '') {
                $output = array();
                foreach ($meta as $temp_key => $temp_value) {
                    $params['field_value'] = $temp_value;
                    $temp_output = types_render_field_single($field, $params,
                            $content, $code);
                    if (!empty($temp_output)) {
                        $output[] = $temp_output;
                    }
                }
                if (!empty($output) && isset($params['separator'])) {
                    $output = implode($params['separator'], $output);
                } else if (!empty($output)) {
                    $output = implode('', $output);
                } else {
                    return '';
                }
            } else if (isset($meta[$index])) {
                $params['field_value'] = $meta[$index];
                return types_render_field_single($field, $params, $content,
                                $code);
            } else {
                return '';
            }
            return $output;
        } else {
            return '';
        }
    } else {
        $params['field_value'] = get_post_meta($post->ID,
                wpcf_types_get_meta_prefix($field) . $field['slug'], true);
        if ($params['field_value'] == '' && $field['type'] != 'checkbox') {
            return '';
        }
        return types_render_field_single($field, $params, $content, $code);
    }
}

/**
 * Calls view function for specific field type by single field.
 * 
 * @param type $field
 * @param type $atts
 * @return type 
 */
function types_render_field_single($field, $params, $content = null, $code = '') {
    global $post;

    // Count fields (if there are duplicates)
    static $count = array();

    // Count it
    if (!isset($count[$field['slug']])) {
        $count[$field['slug']] = 1;
    } else {
        $count[$field['slug']] += 1;
    }

    // Load type
    $type = wpcf_fields_type_action($field['type']);

    // If 'class' or 'style' parameters are set - force HTML output
    if ((!empty($params['class']) || !empty($params['style'])) && $field['type'] != 'date') {
        $params['output'] = 'html';
    }

    // Apply filters to field value
    $params['field_value'] = apply_filters('wpcf_fields_value_display',
            $params['field_value'], $params);
    $params['field_value'] = apply_filters('wpcf_fields_slug_' . $field['slug'] . '_value_display',
            $params['field_value'], $params);
    $params['field_value'] = apply_filters('wpcf_fields_type_' . $field['type'] . '_value_display',
            $params['field_value'], $params);
    // To make sure
    if (is_string($params['field_value'])) {
        $params['field_value'] = addslashes(stripslashes($params['field_value']));
    }

    // Set values
    $field['name'] = wpcf_translate('field ' . $field['id'] . ' name',
            $field['name']);
    $params['field'] = $field;
    $params['#content'] = htmlspecialchars($content);
    $params['#code'] = $code;


    $output = '';
    if (isset($params['raw']) && $params['raw'] == 'true') {
        // Skype is array
        if ($field['type'] == 'skype' && isset($params['field_value']['skypename'])) {
            $output = $params['field_value']['skypename'];
        } else {
            $output = $params['field_value'];
        }
    } else {
        $output = wpcf_fields_type_action($field['type'], 'view', $params);

        // Convert to string
        if (!empty($output)) {
            $output = strval($output);
        }

        // If no output
        if (empty($output) && !empty($params['field_value'])) {
            $output = wpcf_frontend_wrap_field_value($field,
                    $params['field_value'], $params);
            $output = wpcf_frontend_wrap_field($field, $output, $params);
        } else if ($output != '__wpcf_skip_empty') {
            $output = wpcf_frontend_wrap_field_value($field, $output, $params);
            $output = wpcf_frontend_wrap_field($field, $output, $params);
        } else {
            $output = '';
        }

        // Add count
        if (isset($count[$field['slug']]) && intval($count[$field['slug']]) > 1) {
            $add = '-' . intval($count[$field['slug']]);
            $output = str_replace('id="wpcf-field-' . $field['slug'] . '"',
                    'id="wpcf-field-' . $field['slug'] . $add . '"', $output);
        }
    }

    // Apply filters
    $output = strval(apply_filters('types_view', $output,
                    $params['field_value'], $field['type'], $field['slug'],
                    $field['name'], $params));

    return htmlspecialchars_decode(stripslashes($output));
}

/**
 * Wraps field content.
 * 
 * @param type $field
 * @param type $content
 * @return type 
 */
function wpcf_frontend_wrap_field($field, $content, $params = array()) {
    if (isset($params['output']) && $params['output'] == 'html') {
        $class = array();
        if (!empty($params['class'])
                && !in_array($field['type'],
                        array('file', 'image', 'email', 'url', 'wysiwyg'))) {
            $class[] = $params['class'];
        }
        $class[] = 'wpcf-field-' . $field['type'] . ' wpcf-field-'
                . $field['slug'];
        // Add name if needed
        if (isset($params['show_name']) && $params['show_name'] == 'true'
                && strpos($content,
                        'class="wpcf-field-' . $field['type']
                        . '-name ') === false) {
            $content = wpcf_frontend_wrap_field_name($field, $field['name'],
                            $params) . $content;
        }
        $output = '<div id="wpcf-field-' . $field['slug'] . '"'
                . ' class="' . implode(' ', $class) . '"';
        if (!empty($params['style'])
                && !in_array($field['type'],
                        array('date', 'file', 'image', 'email', 'url', 'wysiwyg'))) {
            $output .= ' style="' . $params['style'] . '"';
        }
        $output .= '>' . $content . '</div>';
        return $output;
    } else {
        if (isset($params['show_name']) && $params['show_name'] == 'true'
                && strpos($content, $field['name'] . ':') === false) {
            $content = wpcf_frontend_wrap_field_name($field,
                            $params['field']['name'], $params) . $content;
        }
        return $content;
    }
}

/**
 * Wraps field name.
 * 
 * @param type $field
 * @param type $content
 * @return type 
 */
function wpcf_frontend_wrap_field_name($field, $content, $params = array()) {
    if (isset($params['output']) && $params['output'] == 'html') {
        $class = array();
        if ($field['type'] == 'checkboxes' && isset($params['option'])) {
            if (isset($params['field']['data']['options'][$params['option']]['title'])) {
                $content = $params['field']['data']['options'][$params['option']]['title'];
            }
            $class[] = $params['option'] . '-name';
        }
        if (!in_array($field['type'],
                        array('file', 'image', 'email', 'url', 'wysiwyg'))
                && !empty($params['class'])) {
            $class[] = $params['class'];
        }
        $class[] = 'wpcf-field-name wpcf-field-' . $field['type'] . ' wpcf-field-'
                . $field['slug'] . '-name';
        if ($field['type'] == 'wysiwyg' || $field['type'] == 'textarea') {
            $output = '<div class="' . implode(' ', $class) . '"';
            if (!empty($params['style'])) {
                $output .= ' style="' . $params['style'] . '"';
            }
            $output .= '>' . stripslashes($content) . ':</div> ';
            return $output;
        }
        $output = '<span class="' . implode(' ', $class) . '"';
        if (!empty($params['style'])
                && !in_array($field['type'],
                        array('date', 'file', 'image', 'email', 'url', 'wysiwyg'))) {
            $output .= ' style="' . $params['style'] . '"';
        }
        $output .= '>' . stripslashes($content) . ':</span> ';
        return $output;
    } else {
        return stripslashes($content) . ': ';
    }
}

/**
 * Wraps field value.
 * 
 * @param type $field
 * @param type $content
 * @return type 
 */
function wpcf_frontend_wrap_field_value($field, $content, $params = array()) {
    if (isset($params['output']) && $params['output'] == 'html') {
        $class = array();
        if ($field['type'] == 'checkboxes' && isset($params['option'])) {
            $class[] = $params['option'] . '-value';
        }
        if (!empty($params['class'])
                && !in_array($field['type'],
                        array('file', 'image', 'email', 'url', 'wysiwyg'))) {
            $class[] = $params['class'];
        }
        $class[] = 'wpcf-field-value wpcf-field-' . $field['type']
                . '-value wpcf-field-' . $field['slug'] . '-value';
        if ($field['type'] == 'skype' || $field['type'] == 'image' || ($field['type'] == 'date' && $params['style'] == 'calendar')
                || $field['type'] == 'wysiwyg' || $field['type'] == 'textarea') {
            $output = '<div class="' . implode(' ', $class) . '"';
            if (!empty($params['style'])
                    && !in_array($field['type'],
                            array('date', 'file', 'image', 'email', 'url', 'wysiwyg'))) {
                $output .= ' style="' . $params['style'] . '"';
            }
            $output .= '>' . stripslashes($content) . '</div>';
            return $output;
        }
        $output = '<span class="' . implode(' ', $class) . '"';
        if (!empty($params['style'])
                && !in_array($field['type'],
                        array('date', 'file', 'image', 'email', 'url', 'wysiwyg'))) {
            $output .= ' style="' . $params['style'] . '"';
        }
        $output .= '>' . stripslashes($content) . '</span>';
        return $output;
    } else {
        return stripslashes($content);
    }
}

// Add a filter to handle Views queries with checkboxes.

add_filter('wpv_filter_query', 'wpcf_views_query', 12, 2); // after custom fields.

function wpcf_views_query($query, $view_settings) {
    
    $meta_filter_required = false;
    
    $opt = get_option('wpcf-fields');
    
    if (isset($query['meta_query'])) {
        foreach ($query['meta_query'] as $index => $meta) {
            $field_name = $meta['key'];
            if (_wpcf_is_checkboxes_field($field_name)) {
                
                // We'll use SQL regexp to find the checked items.
                // Note that we are creating something here that
                // then gets modified to a proper SQL REGEXP in
                // the get_meta_sql filter.

                $field_name = substr($field_name, 5);

                $meta_filter_required = true;
                $meta['compare'] = '=';
                
				$values = explode(',', $meta['value']);
                
                $meta['value'] = ' REGEXP(';

                $options = $opt[$field_name]['data']['options'];

                $count = 0;
                foreach ($values as $value) {
                    
                    foreach($options as $key => $option) {
                        if ($option['title'] == $value) {
                            if ($count > 0) {
                                $meta['value'] .= '|';
                            }
                            $meta['value'] .= $key;
                            break;
                        }
                    }
                    $count++;
                }
                
                $meta['value'] .= ')';
                
                $query['meta_query'][$index] = $meta;
            }
        }
    }

    if ($meta_filter_required) {
        add_filter('get_meta_sql', 'wpcf_views_get_meta_sql', 10, 6);
    }
    
    return $query;
}

function _wpcf_is_checkboxes_field($field_name) {
    $opt = get_option('wpcf-fields');
    if($opt && mb_ereg('^wpcf-', $field_name)) {
        $field_name = substr($field_name, 5);
        if (isset($opt[$field_name]['type'])) {
            $field_type = strtolower($opt[$field_name]['type']);
            if ( $field_type == 'checkboxes') {
                return true;
            }
        }
        
    }
    
    return false;
}

function wpcf_views_get_meta_sql($clause, $queries, $type, $primary_table, $primary_id_column, $context ) {
    
    // Look for the REGEXP code we added and covert it to a proper SQL REGEXP 
    $regex = '/= \'REGEXP\(([^\)]*)\)\'/siU';
    
	if(preg_match_all($regex, $clause['where'], $matches, PREG_SET_ORDER)) {
		foreach($matches as $match) {
            $clause['where'] = str_replace($match[0], 'REGEXP \'' . $match[1] . '\'', $clause['where']);
        }
        
    }
    
    remove_filter('get_meta_sql', 'wpcf_views_get_meta_sql', 10, 6);

    return $clause;
}