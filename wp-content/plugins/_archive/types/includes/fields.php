<?php
/*
 * Fields and groups functions
 */
require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';

/**
 * Gets post_types supported by specific group.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @return type 
 */
function wpcf_admin_get_post_types_by_group($group_id) {
    $post_types = get_post_meta($group_id, '_wp_types_group_post_types', true);
    if ($post_types == 'all') {
        return array();
    }
    $post_types = explode(',', trim($post_types, ','));
    return $post_types;
}

/**
 * Gets taxonomies supported by specific group.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @return type 
 */
function wpcf_admin_get_taxonomies_by_group($group_id) {
    global $wpdb;
    $terms = get_post_meta($group_id, '_wp_types_group_terms', true);
    if ($terms == 'all') {
        return array();
    }
    $terms = explode(',', trim($terms, ','));
    $taxonomies = array();
    if (!empty($terms)) {
        foreach ($terms as $term) {
            $term = $wpdb->get_row("SELECT tt.term_taxonomy_id, tt.taxonomy,
                    t.term_id, t.slug, t.name
                    FROM {$wpdb->prefix}term_taxonomy tt
            JOIN {$wpdb->prefix}terms t
            WHERE t.term_id = tt.term_id AND tt.term_id="
                    . intval($term), ARRAY_A);
            if (!empty($term)) {
                $taxonomies[$term['taxonomy']][$term['term_id']] = $term;
            }
        }
    } else {
        return array();
    }
    return $taxonomies;
}

/**
 * Gets templates supported by specific group.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @return type 
 */
function wpcf_admin_get_templates_by_group($group_id) {
    global $wpdb;
    $data = get_post_meta($group_id, '_wp_types_group_templates', true);
    if ($data == 'all') {
        return array();
    }
    $data = explode(',', trim($data, ','));
    $templates = get_page_templates();
    $templates[] = 'default';
    $templates_views = get_posts('post_type=view-template&numberposts=-1&status=publish');
    foreach ($templates_views as $template_view) {
        $templates[] = $template_view->ID;
    }
    $result = array();
    if (!empty($data)) {
        foreach ($templates as $template) {
            if (in_array($template, $data)) {
                $result[] = $template;
            }
        }
    }
    return $result;
}

/**
 * Activates group.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @return type 
 */
function wpcf_admin_fields_activate_group($group_id) {
    global $wpdb;
    return $wpdb->update($wpdb->posts, array('post_status' => 'publish'),
                    array('ID' => intval($group_id), 'post_type' => 'wp-types-group'),
                    array('%s'), array('%d', '%s')
    );
}

/**
 * Deactivates group.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @return type 
 */
function wpcf_admin_fields_deactivate_group($group_id) {
    global $wpdb;
    return $wpdb->update($wpdb->posts, array('post_status' => 'draft'),
                    array('ID' => intval($group_id), 'post_type' => 'wp-types-group'),
                    array('%s'), array('%d', '%s')
    );
}

/**
 * Removes specific field from group.
 * 
 * @global type $wpdb
 * @global type $wpdb
 * @param type $group_id
 * @param type $field_id
 * @return type 
 */
function wpcf_admin_fields_remove_field_from_group($group_id, $field_id) {
    $group_fields = get_post_meta($group_id, '_wp_types_group_fields', true);
    if (empty($group_fields)) {
        return false;
    }
    $group_fields = str_replace(',' . $field_id . ',', ',', $group_fields);
    update_post_meta($group_id, '_wp_types_group_fields', $group_fields);
}

/**
 * Bulk removal
 * 
 * @param type $group_id
 * @param type $fields
 * @return type 
 */
function wpcf_admin_fields_remove_field_from_group_bulk($group_id, $fields) {
    foreach ($fields as $field_id) {
        wpcf_admin_fields_remove_field_from_group($group_id, $field_id);
    }
}

/**
 * Deletes field.
 * 
 * @param type $field_id
 */
function wpcf_admin_fields_delete_field($field_id) {
    global $wpdb;
    $fields = get_option('wpcf-fields', array());
    if (isset($fields[$field_id])) {
        // Remove from groups
        $groups = wpcf_admin_fields_get_groups();
        foreach ($groups as $key => $group) {
            wpcf_admin_fields_remove_field_from_group($group['id'], $field_id);
        }
        // Remove from posts
        if (!wpcf_types_cf_under_control('check_outsider', $field_id)) {
            $results = $wpdb->get_results("SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_key = '" . wpcf_types_get_meta_prefix($fields[$field_id]) . strval($field_id) . "'");
            foreach ($results as $result) {
                delete_post_meta($result->post_id, $result->meta_key);
            }
        }
        unset($fields[$field_id]);
        wpcf_admin_fields_save_fields($fields, true);
        return true;
    } else {
        return false;
    }
}

/**
 * Deletes group by ID.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @return type 
 */
function wpcf_admin_fields_delete_group($group_id) {
    $group = get_post($group_id);
    if (empty($group) || $group->post_type != 'wp-types-group') {
        return false;
    }
    wp_delete_post($group_id, true);
}

/**
 * Saves group.
 * 
 * @param type $group
 * @return type 
 */
function wpcf_admin_fields_save_group($group) {
    if (!isset($group['name'])) {
        return false;
    }

    $post = array(
        'post_status' => 'publish',
        'post_type' => 'wp-types-group',
        'post_title' => $group['name'],
        'post_content' => !empty($group['description']) ? $group['description'] : '',
    );

    $update = false;
    if (isset($group['id'])) {
        $update = true;
        $post_to_update = get_post($group['id']);
        if (empty($post_to_update) || $post_to_update->post_type != 'wp-types-group') {
            return false;
        }
        $post['ID'] = $post_to_update->ID;
        $post['post_status'] = $post_to_update->post_status;
    }

    if ($update) {
        $group_id = wp_update_post($post);
        if (!$group_id) {
            return false;
        }
    } else {
        $group_id = wp_insert_post($post, true);
        if (is_wp_error($group_id)) {
            return false;
        }
    }

    if (!empty($group['filters_association'])) {
        update_post_meta($group_id, '_wp_types_group_filters_association',
                $group['filters_association']);
    } else {
        delete_post_meta($group_id, '_wp_types_group_filters_association');
    }

    // WPML register strings
    if (function_exists('icl_register_string')) {
        wpcf_translate_register_string('plugin Types',
                'group ' . $group_id . ' name', $group['name']);
        wpcf_translate_register_string('plugin Types',
                'group ' . $group_id . ' description', $group['description']);
    }

    return $group_id;
}

/**
 * Saves all fields.
 * 
 * @param type $fields 
 */
function wpcf_admin_fields_save_fields($fields, $forced = false) {
    $original = get_option('wpcf-fields', array());
    if (!$forced) {
        $fields = array_merge($original, $fields);
    }
    update_option('wpcf-fields', $fields);
}

/**
 * Saves field.
 * 
 * @param type $field
 * @return type 
 */
function wpcf_admin_fields_save_field($field) {
    if (!isset($field['name']) || !isset($field['type'])) {
        return false;
    }
    if (empty($field['slug'])) {
        $field['slug'] = sanitize_title($field['name']);
    } else {
        $field['slug'] = sanitize_title($field['slug']);
    }
    $field['id'] = $field['slug'];

    // Set field specific data
    // NOTE: This was $field['data'] = $field and seemed to work on most systems.
    // I changed it to asign via a temporary variable to fix on one system.
    $temp_field = $field;
    $field['data'] = $temp_field;
    // Unset default fields
    unset($field['data']['type'], $field['data']['slug'],
            $field['data']['name'], $field['data']['description'],
            $field['data']['user_id'], $field['data']['id'],
            $field['data']['data']);

    // Merge previous data (added because of outside fields)
    // @TODO Remember why
    if (wpcf_types_cf_under_control('check_outsider', $field['id'])) {
        $field_previous_data = wpcf_admin_fields_get_field($field['id']);
        if (!empty($field_previous_data['data'])) {
            $field['data'] = array_merge($field_previous_data['data'],
                    $field['data']);
        }
    }

    $field['data'] = apply_filters('wpcf_fields_' . $field['type'] . '_meta_data',
            $field['data'], $field);

    // Check validation
    if (isset($field['data']['validate'])) {
        foreach ($field['data']['validate'] as $method => $data) {
            if (!isset($data['active'])) {
                unset($field['data']['validate'][$method]);
            }
        }
        if (empty($field['data']['validate'])) {
            unset($field['data']['validate']);
        }
    }

    $save_data = array();
    $save_data['id'] = $field['id'];
    $save_data['slug'] = $field['slug'];
    $save_data['type'] = $field['type'];
    $save_data['name'] = $field['name'];
    $save_data['description'] = $field['description'];
    $save_data['data'] = $field['data'];
    $save_data['data']['disabled_by_type'] = 0;

    // For radios or select
    if (!empty($field['data']['options'])) {
        foreach ($field['data']['options'] as $name => $option) {
            if (isset($option['title'])) {
                $option['title'] = $field['data']['options'][$name]['title'] = htmlspecialchars_decode($option['title']);
            }
            if (isset($option['value'])) {
                $option['value'] = $field['data']['options'][$name]['value'] = htmlspecialchars_decode($option['value']);
            }
            if (isset($option['display_value'])) {
                $option['display_value'] = $field['data']['options'][$name]['display_value'] = htmlspecialchars_decode($option['display_value']);
            }
            // For checkboxes
            if ($field['type'] == 'checkboxes' && isset($option['set_value'])
                    && $option['set_value'] != '1') {
                $option['set_value'] = $field['data']['options'][$name]['set_value'] = htmlspecialchars_decode($option['set_value']);
            }
            if ($field['type'] == 'checkboxes' && !empty($option['display_value_selected'])) {
                $option['display_value_selected'] = $field['data']['options'][$name]['display_value_selected'] = htmlspecialchars_decode($option['display_value_selected']);
            }
            if ($field['type'] == 'checkboxes' && !empty($option['display_value_not_selected'])) {
                $option['display_value_not_selected'] = $field['data']['options'][$name]['display_value_not_selected'] = htmlspecialchars_decode($option['display_value_not_selected']);
            }
        }
    }

    // For checkboxes
    if ($field['type'] == 'checkbox' && $field['set_value'] != '1') {
        $field['set_value'] = htmlspecialchars_decode($field['set_value']);
    }
    if ($field['type'] == 'checkbox' && !empty($field['display_value_selected'])) {
        $field['display_value_selected'] = htmlspecialchars_decode($field['display_value_selected']);
    }
    if ($field['type'] == 'checkbox' && !empty($field['display_value_not_selected'])) {
        $field['display_value_not_selected'] = htmlspecialchars_decode($field['display_value_not_selected']);
    }

    // Save field
    $fields = get_option('wpcf-fields', array());
    $fields[$field['slug']] = $save_data;
    update_option('wpcf-fields', $fields);
    $field_id = $field['slug'];

    // WPML register strings
    if (function_exists('icl_register_string')) {
        wpcf_translate_register_string('plugin Types',
                'field ' . $field_id . ' name', $field['name']);
        wpcf_translate_register_string('plugin Types',
                'field ' . $field_id . ' description', $field['description']);

        // For radios or select
        if (!empty($field['data']['options'])) {
            foreach ($field['data']['options'] as $name => $option) {
                if ($name == 'default') {
                    continue;
                }
                if (isset($option['title'])) {
                    wpcf_translate_register_string('plugin Types',
                            'field ' . $field_id . ' option ' . $name . ' title',
                            $option['title']);
                }
                if (isset($option['value'])) {
                    wpcf_translate_register_string('plugin Types',
                            'field ' . $field_id . ' option ' . $name . ' value',
                            $option['value']);
                }
                if (isset($option['display_value'])) {
                    wpcf_translate_register_string('plugin Types',
                            'field ' . $field_id . ' option ' . $name . ' display value',
                            $option['display_value']);
                }
                // For checkboxes
                if (isset($option['set_value']) && $option['set_value'] != '1') {
                    wpcf_translate_register_string('plugin Types',
                            'field ' . $field_id . ' option ' . $name . ' value',
                            $option['set_value']);
                }
                if (!empty($option['display_value_selected'])) {
                    wpcf_translate_register_string('plugin Types',
                            'field ' . $field_id . ' option ' . $name . ' display value selected',
                            $option['display_value_selected']);
                }
                if (!empty($option['display_value_not_selected'])) {
                    wpcf_translate_register_string('plugin Types',
                            'field ' . $field_id . ' option ' . $name . ' display value not selected',
                            $option['display_value_not_selected']);
                }
            }
        }

        if ($field['type'] == 'checkbox' && $field['set_value'] != '1') {
            // we need to translate the check box value to store
            wpcf_translate_register_string('plugin Types',
                    'field ' . $field_id . ' checkbox value',
                    $field['set_value']);
        }

        if ($field['type'] == 'checkbox' && !empty($field['display_value_selected'])) {
            // we need to translate the check box value to store
            wpcf_translate_register_string('plugin Types',
                    'field ' . $field_id . ' checkbox value selected',
                    $field['display_value_selected']);
        }

        if ($field['type'] == 'checkbox' && !empty($field['display_value_not_selected'])) {
            // we need to translate the check box value to store
            wpcf_translate_register_string('plugin Types',
                    'field ' . $field_id . ' checkbox value not selected',
                    $field['display_value_not_selected']);
        }

        // Validation message
        if (!empty($field['data']['validate'])) {
            foreach ($field['data']['validate'] as $method => $validation) {
                if (!empty($validation['message'])) {
                    // Skip if it's same as default
                    $default_message = wpcf_admin_validation_messages($method);
                    if ($validation['message'] != $default_message) {
                        wpcf_translate_register_string('plugin Types',
                                'field ' . $field_id . ' validation message ' . $method,
                                $validation['message']);
                    }
                }
            }
        }
    }

    return $field_id;
}

/**
 * Changes field type.
 * 
 * @param type $fields
 * @param type $type 
 */
function wpcf_admin_custom_fields_change_type($fields, $type) {
    if (!is_array($fields)) {
        $fields = array(strval($fields));
    }
    $fields = wpcf_types_cf_under_control('add',
            array('fields' => $fields, 'type' => $type));
    $allowed = array(
        'textfield' => array('wysiwyg', 'textfield', 'textarea', 'email', 'url', 'date', 'phone', 'file', 'image', 'numeric'),
        'textarea' => array('wysiwyg', 'textfield', 'textarea', 'email', 'url', 'date', 'phone', 'file', 'image', 'numeric'),
        'date' => array('wysiwyg', 'date', 'textarea', 'textfield', 'email', 'url', 'phone', 'file', 'image', 'numeric'),
        'email' => array('wysiwyg', 'email', 'textarea', 'textfield', 'date', 'url', 'phone', 'file', 'image', 'numeric'),
        'file' => array('wysiwyg', 'file', 'textarea', 'textfield', 'email', 'url', 'phone', 'fdate', 'image', 'numeric'),
        'image' => array('wysiwyg', 'image', 'textarea', 'textfield', 'email', 'url', 'phone', 'file', 'idate', 'numeric'),
        'numeric' => array('wysiwyg', 'numeric', 'textarea', 'textfield', 'email', 'url', 'phone', 'file', 'image', 'date'),
        'phone' => array('wysiwyg', 'phone', 'textarea', 'textfield', 'email', 'url', 'date', 'file', 'image', 'numeric'),
        'select' => array('wysiwyg', 'select', 'textarea', 'textfield', 'date', 'email', 'url', 'phone', 'file', 'image', 'numeric'),
        'skype' => array('wysiwyg', 'skype', 'textarea', 'textfield', 'date', 'email', 'url', 'phone', 'file', 'image', 'numeric'),
        'url' => array('wysiwyg', 'url', 'textarea', 'textfield', 'email', 'date', 'phone', 'file', 'image', 'numeric'),
        'checkbox' => array('wysiwyg', 'checkbox', 'textarea', 'textfield', 'email', 'url', 'date', 'phone', 'file', 'image', 'numeric'),
        'radio' => array('wysiwyg', 'radio', 'textarea', 'textfield', 'email', 'url', 'date', 'phone', 'file', 'image', 'numeric'),
        'wysiwyg' => array('wysiwyg', 'textarea'),
    );
    $all_fields = wpcf_admin_fields_get_fields();
    foreach ($fields as $field_id) {
        if (!isset($all_fields[$field_id])) {
            continue;
        }
        $field = $all_fields[$field_id];
        if (!in_array($type, $allowed[$field['type']])) {
            wpcf_admin_message_store(sprintf(__('Field "%s" type was converted from %s to %s. You need to set some further settings in the group editor.',
                                    'wpcf'), $field['name'], $field['type'],
                            $type));
            $all_fields[$field_id]['data']['disabled_by_type'] = 1;
        } else {
            $all_fields[$field_id]['data']['disabled'] = 0;
            $all_fields[$field_id]['data']['disabled_by_type'] = 0;
        }
        $all_fields[$field_id]['type'] = $type;
    }
    update_option('wpcf-fields', $all_fields);
}

/**
 * Saves group's fields.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @param type $fields 
 */
function wpcf_admin_fields_save_group_fields($group_id, $fields, $add = false) {
    $fields = wpcf_types_cf_under_control('add', array('fields' => $fields));
    if ($add) {
        $existing_fields = wpcf_admin_fields_get_fields_by_group($group_id);
        $order = array();
        if (!empty($existing_fields)) {
            foreach ($existing_fields as $field_id => $field) {
                if (in_array($field['id'], $fields)) {
                    continue;
                }
                $order[] = $field['id'];
            }
            foreach ($fields as $field) {
                $order[] = $field;
            }
            $fields = $order;
        }
    }
    if (empty($fields)) {
        delete_post_meta($group_id, '_wp_types_group_fields');
        return false;
    }
    $fields = ',' . implode(',', (array) $fields) . ',';
    update_post_meta($group_id, '_wp_types_group_fields', $fields);
}

/**
 * Saves group's post types.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @param type $post_types 
 */
function wpcf_admin_fields_save_group_post_types($group_id, $post_types) {
    if (empty($post_types)) {
        update_post_meta($group_id, '_wp_types_group_post_types', 'all');
        return true;
    }
    $post_types = ',' . implode(',', (array) $post_types) . ',';
    update_post_meta($group_id, '_wp_types_group_post_types', $post_types);
}

/**
 * Saves group's terms.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @param type $terms 
 */
function wpcf_admin_fields_save_group_terms($group_id, $terms) {
    if (empty($terms)) {
        update_post_meta($group_id, '_wp_types_group_terms', 'all');
        return true;
    }
    $terms = ',' . implode(',', (array) $terms) . ',';
    update_post_meta($group_id, '_wp_types_group_terms', $terms);
}

/**
 * Saves group's templates.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @param type $terms 
 */
function wpcf_admin_fields_save_group_templates($group_id, $templates) {
    if (empty($templates)) {
        update_post_meta($group_id, '_wp_types_group_templates', 'all');
        return true;
    }
    $templates = ',' . implode(',', (array) $templates) . ',';
    update_post_meta($group_id, '_wp_types_group_templates', $templates);
}

/**
 * Returns HTML formatted AJAX activation link.
 * 
 * @param type $group_id
 * @return type 
 */
function wpcf_admin_fields_get_ajax_activation_link($group_id) {
    return '<a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;'
                    . 'wpcf_action=activate_group&amp;group_id='
                    . $group_id . '&amp;wpcf_ajax_update=wpcf_list_ajax_response_'
                    . $group_id) . '&amp;_wpnonce=' . wp_create_nonce('activate_group')
            . '" class="wpcf-ajax-link" id="wpcf-list-activate-'
            . $group_id . '">'
            . __('Activate', 'wpcf') . '</a>';
}

/**
 * Returns HTML formatted AJAX deactivation link.
 * @param type $group_id
 * @return type 
 */
function wpcf_admin_fields_get_ajax_deactivation_link($group_id) {
    return '<a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;'
                    . 'wpcf_action=deactivate_group&amp;group_id='
                    . $group_id . '&amp;wpcf_ajax_update=wpcf_list_ajax_response_'
                    . $group_id) . '&amp;_wpnonce=' . wp_create_nonce('deactivate_group')
            . '" class="wpcf-ajax-link" id="wpcf-list-activate-'
            . $group_id . '">'
            . __('Deactivate', 'wpcf') . '</a>';
}

/**
 * Gets all groups that contain specified field.
 * 
 * @static $cache
 * @param type $field_id 
 */
function wpcf_admin_fields_get_groups_by_field($field_id) {
    static $cache = array();
    $groups = wpcf_admin_fields_get_groups();
    $return = array();
    foreach ($groups as $group_id => $group) {
        if (isset($cache['groups'][$group_id])) {
            $fields = $cache['groups'][$group_id];
        } else {
            $fields = wpcf_admin_fields_get_fields_by_group($group['id']);
        }
        if (array_key_exists($field_id, $fields)) {
            $return[$group['id']] = $group;
        }
    }
    $cache['groups'][$group_id] = $fields;
    return $return;
}

/**
 * Check how many posts needs checkbox update.
 * 
 * @param type $field
 * @param type $action
 * @return boolean|int 
 */
function wpcf_admin_fields_checkbox_migrate_empty_check($field, $action) {
    $field = wpcf_admin_fields_get_field($field);
    if (empty($field) || $field['type'] != 'checkbox') {
        return false;
    }
    $filter = wpcf_admin_fields_get_filter_by_field($field['id']);
    if (!empty($filter)) {
        $posts = array();
        $meta_key = wpcf_types_get_meta_prefix($field) . $field['id'];
        $meta_query = '';
        if ($action == 'do_not_save_check') {
            $meta_query = "(m.meta_key = '$meta_key' AND m.meta_value = '0')";
            $posts = wpcf_admin_fields_get_posts_by_filter($filter, $meta_query);
        } else if ($action == 'save_check') {
            $posts = wpcf_admin_fields_get_posts_by_filter_missing_meta($filter,
                    $meta_key);
        }
        $option = get_option('wpcf_checkbox_migration', array());
        $cache_key = $action == 'do_not_save_check' ? 'do_not_save' : 'save';
        $option[$cache_key] = $posts;
        update_option('wpcf_checkbox_migration', $option);
        return $posts;
    }
    return false;
}

/**
 * Update posts checkboxes fields.
 * 
 * @param type $field
 * @param type $action
 * @return boolean|int 
 */
function wpcf_admin_fields_checkbox_migrate_empty($field, $action) {
    $field = wpcf_admin_fields_get_field($field);
    if (empty($field) || $field['type'] != 'checkbox') {
        return false;
    }
    $option = get_option('wpcf_checkbox_migration', array());
    $meta_key = wpcf_types_get_meta_prefix($field) . $field['id'];
    if (empty($option[$action])) {
        $posts = wpcf_admin_fields_checkbox_migrate_empty_check($field['id'],
                $action . '_check');
    } else {
        $posts = $option[$action];
    }

    if (!empty($posts)) {
        if ($action == 'do_not_save') {
            $count = 0;
            foreach ($posts as $temp_key => $post_id) {
                if ($count == 1000) {
                    $option[$action] = $posts;
                    update_option('wpcf_checkbox_migration', $option);
                    $data = array('offset' => $temp_key);
                    return $data;
                }
                delete_post_meta($post_id, $meta_key, 0);
                unset($posts[$temp_key]);
                $count++;
            }
            unset($option[$action]);
            update_option('wpcf_checkbox_migration', $option);
            return $posts;
        } else if ($action == 'save') {
            $count = 0;
            foreach ($posts as $temp_key => $post_id) {
                if ($count == 1000) {
                    $option[$action] = $posts;
                    update_option('wpcf_checkbox_migration', $option);
                    $data = array('offset' => $temp_key);
                    return $data;
                }
                update_post_meta($post_id, $meta_key, 0);
                unset($posts[$temp_key]);
                $count++;
            }
            unset($option[$action]);
            update_option('wpcf_checkbox_migration', $option);
            return $posts;
        }
    }
    return false;
}

/**
 * Gets all filters required for field to be used.
 * 
 * @param type $field
 * @return boolean|string 
 */
function wpcf_admin_fields_get_filter_by_field($field) {
    $field = wpcf_admin_fields_get_field($field);
    if (empty($field)) {
        return false;
    }
    $filter = array();
    $filter['types'] = array();
    $filter['terms'] = array();
    $filter['templates'] = array();
    $groups = wpcf_admin_fields_get_groups_by_field($field['id']);
    foreach ($groups as $group_id => $group_data) {
        // Get filters
        $filter['types'] = array_merge($filter['types'],
                explode(',',
                        trim(get_post_meta($group_id,
                                        '_wp_types_group_post_types', true), ',')));
        $filter['terms'] = array_merge($filter['terms'],
                explode(',',
                        trim(get_post_meta($group_id, '_wp_types_group_terms',
                                        true), ',')));
        $filter['templates'] = array_merge($filter['templates'],
                explode(',',
                        trim(get_post_meta($group_id,
                                        '_wp_types_group_templates', true), ',')));
        $filter['association'] = isset($group_data['filters_association']) && $group_data['filters_association'] == 'any' ? 'OR' : 'AND';
    }
    if (in_array('all', $filter['types'])) {
        $filter['types'] = 'all';
    }
    if (in_array('all', $filter['terms'])) {
        $filter['terms'] = 'all';
    }
    if (in_array('all', $filter['templates'])) {
        $filter['templates'] = 'all';
    }

    return $filter;
}

/**
 * Gets posts by filter fetched with wpcf_admin_fields_get_filter_by_field().
 * 
 * @global type $wpdb
 * @param type $filter
 * @return type 
 */
function wpcf_admin_fields_get_posts_by_filter($filter, $meta_query = '') {
    global $wpdb;
    $query = array();
    $join = array();
    if ($filter['types'] != 'all' && !empty($filter['types'])) {
        $query[] = 'p.post_type IN (\'' . implode('\',\'', $filter['types']) . '\')';
    } else {
        $post_types = get_post_types(array('show_ui' => true), 'names');
        foreach ($post_types as $post_type_slug => $post_type) {
            if (in_array($post_type_slug,
                            array('attachment', 'revision', 'nav_menu_item',
                        'view', 'view-template'))) {
                unset($post_types[$post_type_slug]);
            }
        }
        $query[] = 'p.post_type IN (\'' . implode('\',\'', $post_types) . '\')';
    }
    if ($filter['terms'] != 'all' && !empty($filter['terms'])) {
        $ttid = array();
        foreach ($filter['terms'] as $term_id) {
            $term_taxonomy_id = $wpdb->get_var($wpdb->prepare("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id=%d",
                            $term_id));
            if (!empty($term_taxonomy_id)) {
                $ttid[] = $term_taxonomy_id;
            }
        }
        $query[] = 't.term_taxonomy_id IN (\'' . implode('\',\'', $ttid) . '\')';
        $join[] = "LEFT JOIN $wpdb->term_relationships t ON p.ID = t.object_id ";
    }
    if ($filter['templates'] != 'all' && !empty($filter['templates'])) {
        $query[] = '(m.meta_key = \'_wp_page_template\' AND m.meta_value IN (\'' . implode('\',\'',
                        $filter['templates']) . '\'))';
    }
    if (!empty($meta_query)
            || ($filter['templates'] != 'all' && !empty($filter['templates']))) {
        $join[] = "LEFT JOIN $wpdb->postmeta m ON p.ID = m.post_id ";
    }

    $_query = "SELECT p.ID FROM $wpdb->posts p " . implode('', $join);
    if (!empty($query)) {
        $_query .= "WHERE " . implode(' ' . $filter['association'] . ' ', $query) . ' ';
        if (!empty($meta_query)) {
            $_query .= ' AND ' . $meta_query . ' ';
        }
    } else if (!empty($meta_query)) {
        $_query .= "WHERE " . $meta_query . ' ';
    }
    $_query .= "GROUP BY p.ID";
    $posts = $wpdb->get_col($_query);
    return $posts;
}

/**
 * Gets posts by filter with missing meta fetched
 * with wpcf_admin_fields_get_filter_by_field().
 * 
 * @global type $wpdb
 * @param type $filter
 * @return type 
 */
function wpcf_admin_fields_get_posts_by_filter_missing_meta($filter,
        $meta_key = '') {
    global $wpdb;
    $query = array();
    $join = array();
    if ($filter['types'] != 'all' && !empty($filter['types'])) {
        $query[] = 'p.post_type IN (\'' . implode('\',\'', $filter['types']) . '\')';
    } else {
        $post_types = get_post_types(array('show_ui' => true), 'names');
        foreach ($post_types as $post_type_slug => $post_type) {
            if (in_array($post_type_slug,
                            array('attachment', 'revision', 'nav_menu_item',
                        'view', 'view-template'))) {
                unset($post_types[$post_type_slug]);
            }
        }
        $query[] = 'p.post_type IN (\'' . implode('\',\'', $post_types) . '\')';
    }
    if ($filter['terms'] != 'all' && !empty($filter['terms'])) {
        $ttid = array();
        foreach ($filter['terms'] as $term_id) {
            $term_taxonomy_id = $wpdb->get_var($wpdb->prepare("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id=%d",
                            $term_id));
            if (!empty($term_taxonomy_id)) {
                $ttid[] = $term_taxonomy_id;
            }
        }
        $query[] = 't.term_taxonomy_id IN (\'' . implode('\',\'', $ttid) . '\')';
        $join[] = "LEFT JOIN $wpdb->term_relationships t ON p.ID = t.object_id ";
    }
    if ($filter['templates'] != 'all' && !empty($filter['templates'])) {
        $query[] = '(m.meta_key = \'_wp_page_template\' AND m.meta_value IN (\'' . implode('\',\'',
                        $filter['templates']) . '\'))';
        $join[] = "LEFT JOIN $wpdb->postmeta m ON p.ID = m.post_id ";
    }
    $_query = "SELECT DISTINCT p.ID FROM $wpdb->posts p " . implode('', $join);
    $_query .= "WHERE NOT EXISTS (SELECT * FROM $wpdb->postmeta mm WHERE p.ID = mm.post_id AND mm.meta_key = '$meta_key')";
    if (!empty($query)) {
        $_query .= "AND (" . implode(' ' . $filter['association'] . ' ', $query) . ') ';
    }
    $_query .= "GROUP BY p.ID";
    $posts = $wpdb->get_col($_query);
    return $posts;
}

/**
 * Check how many posts needs checkboxes update.
 * 
 * @param type $field
 * @param type $action
 * @return boolean|int 
 */
function wpcf_admin_fields_checkboxes_migrate_empty_check($field, $action) {
    $field = wpcf_admin_fields_get_field($field);
    if (empty($field) || $field['type'] != 'checkboxes' || empty($field['data']['options'])) {
        return false;
    }
    $filter = wpcf_admin_fields_get_filter_by_field($field['id']);
    if (!empty($filter)) {
        $posts = array();
        $meta_key = wpcf_types_get_meta_prefix($field) . $field['id'];
        $meta_query = '';
        // "wpcf-fields-checkboxes-option-1873650245";s:1:"1";
        if ($action == 'do_not_save_check') {
            $query = array();
            foreach ($field['data']['options'] as $option_id => $option_data) {
                $query[] = '\"' . $option_id . '\";s:1:\"0\";';
            }
            $meta_query = "(m.meta_key = '$meta_key' AND (m.meta_value LIKE '%%"
                    . implode("%%' OR m.meta_value LIKE '%%", $query) . "%%'))";
            $posts = wpcf_admin_fields_get_posts_by_filter($filter, $meta_query);
        } else if ($action == 'save_check') {
            $query = array();
            foreach ($field['data']['options'] as $option_id => $option_data) {
                $query[] = '\"' . $option_id . '\";s:1:\"0\";';
            }
            $meta_query = "(m.meta_key = '$meta_key' AND (m.meta_value NOT LIKE '%%"
                    . implode("%%' AND m.meta_value NOT LIKE '%%", $query) . "%%'))";
            $posts = wpcf_admin_fields_get_posts_by_filter($filter, $meta_query);
        }
        $option = get_option('wpcf_checkboxes_migration', array());
        $cache_key = $action == 'do_not_save_check' ? 'do_not_save' : 'save';
        $option[$cache_key] = $posts;
        update_option('wpcf_checkboxes_migration', $option);
        return $posts;
    }
    return false;
}

/**
 * Update posts checkboxes fields.
 * 
 * @param type $field
 * @param type $action
 * @return boolean|int 
 */
function wpcf_admin_fields_checkboxes_migrate_empty($field, $action) {
    $field = wpcf_admin_fields_get_field($field);
    if (empty($field) || $field['type'] != 'checkboxes' || empty($field['data']['options'])) {
        return false;
    }
    $option = get_option('wpcf_checkboxes_migration', array());
    $meta_key = wpcf_types_get_meta_prefix($field) . $field['id'];
    if (empty($option[$action])) {
        $posts = wpcf_admin_fields_checkboxes_migrate_empty_check($field['id'],
                $action . '_check');
    } else {
        $posts = $option[$action];
    }

    if (!empty($posts)) {
        if ($action == 'do_not_save') {
            $count = 0;
            foreach ($posts as $temp_key => $post_id) {
                if ($count == 1000) {
                    $option[$action] = $posts;
                    update_option('wpcf_checkboxes_migration', $option);
                    $data = array('offset' => $temp_key);
                    return $data;
                }
                $meta_saved = get_post_meta($post_id, $meta_key);
                if (!empty($meta_saved)) {
                    foreach ($meta_saved as $key => $value) {
                        if (!is_array($value)) {
                            $value_check = array();
                        } else {
                            $value_check = $value;
                        }
                        foreach ($field['data']['options'] as $option_id => $option_data) {
                            if (isset($value_check[$option_id])) {
                                unset($value_check[$option_id]);
                            }
                        }
                        update_post_meta($post_id, $meta_key, $value_check,
                                $value);
                    }
                }
                unset($posts[$temp_key]);
                $count++;
            }
            unset($option[$action]);
            update_option('wpcf_checkboxes_migration', $option);
            return $posts;
        } else if ($action == 'save') {
            $count = 0;
            foreach ($posts as $temp_key => $post_id) {
                if ($count == 1000) {
                    $option[$action] = $posts;
                    update_option('wpcf_checkboxes_migration', $option);
                    $data = array('offset' => $temp_key);
                    return $data;
                }
                $meta_saved = get_post_meta($post_id, $meta_key);
                if (!empty($meta_saved)) {
                    foreach ($meta_saved as $key => $value) {
                        if (!is_array($value)) {
                            $value_check = array();
                        } else {
                            $value_check = $value;
                        }
                        $set_value = array();
                        foreach ($field['data']['options'] as $option_id => $option_data) {
                            if (!isset($value_check[$option_id])) {
                                $set_value[$option_id] = 0;
                            }
                        }
                        $updated_value = $value_check + $set_value;
                        update_post_meta($post_id, $meta_key, $updated_value,
                                $value);
                    }
                }
                unset($posts[$temp_key]);
                $count++;
            }
            unset($option[$action]);
            update_option('wpcf_checkboxes_migration', $option);
            return $posts;
        }
    }
    return false;
}