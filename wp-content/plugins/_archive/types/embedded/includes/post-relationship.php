<?php
/*
 * Post relationship code.
 */
require_once WPCF_EMBEDDED_INC_ABSPATH . '/editor-support/post-relationship-editor-support.php';

add_action('wpcf_admin_post_init', 'wpcf_pr_admin_post_init_action', 10, 4);
//add_action('save_post', 'wpcf_pr_admin_save_post_hook', 10, 2);
add_filter('get_post_metadata', 'wpcf_pr_meta_belongs_filter', 10, 4);

/**
 * Init function.
 * 
 * @param type $post_type
 * @param type $post
 * @param type $groups
 * @param type $wpcf_active 
 */
function wpcf_pr_admin_post_init_action($post_type, $post, $groups, $wpcf_active) {
    $has = wpcf_pr_admin_get_has($post_type);
    $belongs = wpcf_pr_admin_get_belongs($post_type);
    if (!empty($has) || !empty($belongs)) {
        add_action('admin_head', 'wpcf_pr_add_field_js');
        $output = wpcf_pr_admin_post_meta_box_output($post,
                array('post_type' => $post_type, 'has' => $has, 'belongs' => $belongs));
        add_meta_box('wpcf-post-relationship', __('Fields table', 'wpcf'),
                'wpcf_pr_admin_post_meta_box', $post_type, 'normal', 'default',
                array('output' => $output));
        if (!empty($output)) {
            wp_enqueue_script('wpcf-post-relationship',
                    WPCF_EMBEDDED_RELPATH . '/resources/js/post-relationship.js',
                    array('jquery'), WPCF_VERSION);
            wp_enqueue_style('wpcf-post-relationship',
                    WPCF_EMBEDDED_RELPATH . '/resources/css/post-relationship.css',
                    array(), WPCF_VERSION);
            if (!$wpcf_active) {
                wp_enqueue_style('wpcf-pr-basic',
                        WPCF_EMBEDDED_RES_RELPATH . '/css/basic.css', array(),
                        WPCF_VERSION);
                wp_enqueue_style('wpcf-pr-post',
                        WPCF_EMBEDDED_RES_RELPATH . '/css/fields-post.css',
                        array(), WPCF_VERSION);
                wp_enqueue_script('wpcf-form-validation',
                        WPCF_EMBEDDED_RES_RELPATH . '/js/'
                        . 'jquery-form-validation/jquery.validate.min.js',
                        array('jquery'), WPCF_VERSION);
                wp_enqueue_script('wpcf-form-validation-additional',
                        WPCF_EMBEDDED_RES_RELPATH . '/js/'
                        . 'jquery-form-validation/additional-methods.min.js',
                        array('jquery'), WPCF_VERSION);
            }
            wpcf_admin_add_js_settings('wpcf_pr_del_warning',
                    '\'' . __('Are you sure about deleting this post?', 'wpcf') . '\'');
            wpcf_admin_add_js_settings('wpcf_pr_pagination_warning',
                    '\'' . __('If you continue without saving your changes, it might get lost.',
                            'wpcf') . '\'');
        }
    }
}

/**
 * Gets post types that belong to current post type.
 * 
 * @param type $post_type
 * @return type 
 */
function wpcf_pr_admin_get_has($post_type) {
    if (empty($post_type)) {
        return false;
    }
    $relationships = get_option('wpcf_post_relationship', array());
    if (empty($relationships[$post_type])) {
        return false;
    }
    // See if enabled
    foreach ($relationships[$post_type] as $temp_post_type => $temp_post_type_data) {
        $active = get_post_type_object($temp_post_type);
        if (!$active) {
            unset($relationships[$post_type][$temp_post_type]);
        }
    }
    return !empty($relationships[$post_type]) ? $relationships[$post_type] : false;
}

/**
 * Gets post types that current post type belongs to.
 * 
 * @param type $post_type
 * @return type 
 */
function wpcf_pr_admin_get_belongs($post_type) {
    if (empty($post_type)) {
        return false;
    }
    $relationships = get_option('wpcf_post_relationship', array());
    $results = array();
    if (is_array($relationships)) {
        foreach ($relationships as $has => $belongs) {
            // See if enabled
            $active = get_post_type_object($has);
            if (!$active) {
                continue;
            }
            if (array_key_exists($post_type, $belongs)) {
                $results[$has] = $belongs[$post_type];
            }
        }
    }
    return !empty($results) ? $results : false;
}

/**
 * Meta boxes contents.
 * 
 * @param type $post
 * @param type $args 
 */
function wpcf_pr_admin_post_meta_box($post, $args) {
    if (!empty($args['args']['output'])) {
        echo $args['args']['output'];
    } else {
        _e('You will be able to add/edit child posts after saving the parent post.',
                'wpcf');
    }
}

/**
 * Meta boxes contents output.
 * 
 * @param type $post
 * @param type $args 
 */
function wpcf_pr_admin_post_meta_box_output($post, $args) {
    $output = '';
    $relationships = $args;
    $post_id = !empty($post->ID) ? $post->ID : -1;
    $current_post_type = wpcf_admin_get_post_type($post);
    if (!empty($relationships['has'])) {
        foreach ($relationships['has'] as $post_type => $data) {
            $output_temp = wpcf_pr_admin_post_meta_box_has_form($post,
                    $post_type, $data, $relationships['post_type']);
            if (!empty($output_temp)) {
                $post_type_object = get_post_type_object($post_type);
                $output .= '<div class="wpcf-pr-has-entries">'
                        . '<div class="wpcf-pr-has-title">' . $post_type_object->label . '</div>'
                        . '<a class="button-primary wpcf-pr-save-all-link" href="'
                        . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_save_all'
                                . '&amp;post_id=' . $post->ID . '&amp;post_type='
                                . $post_type . '&amp;_wpnonce='
                                . wp_create_nonce('pr_save_all')) . '">'
                        . __('Save All', 'wpcf') . '</a>'
                        . '<a href="'
                        . admin_url('admin-ajax.php?action=wpcf_ajax&amp;'
                                . 'wpcf_action=pr_add_child_post&amp;post_type_parent='
                                . $relationships['post_type']
                                . '&amp;post_id=' . $post_id
                                . '&amp;post_type_child='
                                . $post_type . '&_wpnonce=' . wp_create_nonce('pr_add_child_post'))
                        . '" style="line-height:40px;" class="wpcf-pr-ajax-link button-secondary">' . $post_type_object->labels->add_new_item
                        . '</a>'
                        . '<div class="wpcf-pr-pagination-update">'
                        . $output_temp . '</div>'
                        . '</div>';
            }
        }
    }
    if (!empty($relationships['belongs'])) {
        $meta = get_post_custom($post_id);
        $belongs = array('belongs' => array(), 'posts' => array());
        foreach ($meta as $meta_key => $meta_value) {
            if (strpos($meta_key, '_wpcf_belongs_') === 0) {
                $temp_post = get_post($meta_value[0]);
                if (!empty($temp_post)) {
                    $belongs['posts'][$temp_post->ID] = $temp_post;
                    $belongs['belongs'][$temp_post->post_type] = $temp_post->ID;
                }
            }
        }
        $output_temp = '';
        foreach ($relationships['belongs'] as $post_type => $data) {
            $output_temp .= wpcf_form_simple(wpcf_pr_admin_post_meta_box_belongs_form($post,
                            $post_type, $belongs));
        }
        if (!empty($output_temp)) {
            $output .= '<div style="margin: 20px 0 10px 0">' . sprintf(__('This %s belongs to:',
                                    'wpcf'), $current_post_type) . '</div>' . $output_temp;
        }
    }
    return $output;
}

/**
 * Post relationship has form headers.
 * 
 * @global type $wpcf_post_relationship_headers
 * @param type $post
 * @param type $post_type
 * @param type $parent_post_type
 * @param type $data
 * @return string 
 */
function wpcf_pr_admin_post_meta_box_has_form_headers($post, $post_type,
        $parent_post_type, $data) {
    // Sorting
    $dir = isset($_GET['sort']) && $_GET['sort'] == 'ASC' ? 'DESC' : 'ASC';
    $dir_default = 'ASC';
    $sort_field = isset($_GET['field']) ? $_GET['field'] : '';

    $headers = array();
    $wpcf_fields = wpcf_admin_fields_get_fields(true);
    if (empty($data['fields_setting'])) {
        $data['fields_setting'] = 'all_cf';
    }
    if ($data['fields_setting'] == 'specific') {
        $keys = array_keys($data['fields']);
        foreach ($keys as $k => $header) {
            if ($header == '_wpcf_pr_parents') {
                continue;
            }
            if ($header == '_wp_title') {
                $title_dir = $sort_field == '_wp_title' ? $dir : 'ASC';
                $headers[$header] = '';
                $headers[$header] .= $sort_field == '_wp_title' ? '<div class="wpcf-pr-sort-' . $dir . '"></div>' : '';
                $headers[$header] .= '<a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_sort&amp;field='
                                . '_wp_title&amp;sort=' . $title_dir . '&amp;post_id=' . $post->ID . '&amp;post_type='
                                . $post_type . '&amp;_wpnonce='
                                . wp_create_nonce('pr_sort')) . '">' . __('Post Title') . '</a>';
            } else if ($header == '_wp_body') {
                $body_dir = $sort_field == '_wp_body' ? $dir : $dir_default;
                $headers[$header] = '';
                $headers[$header] .= $sort_field == '_wp_body' ? '<div class="wpcf-pr-sort-' . $dir . '"></div>' : '';
                $headers[$header] .= '<a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_sort&amp;field='
                                . '_wp_body&amp;sort=' . $body_dir . '&amp;post_id=' . $post->ID . '&amp;post_type='
                                . $post_type . '&amp;_wpnonce='
                                . wp_create_nonce('pr_sort')) . '">' . __('Post Body') . '</a>';
            } else if (strpos($header, WPCF_META_PREFIX) === 0
                    && isset($wpcf_fields[str_replace(WPCF_META_PREFIX, '',
                                    $header)])) {
                wpcf_admin_post_field_load_js_css(wpcf_fields_type_action($wpcf_fields[str_replace(WPCF_META_PREFIX,
                                        '', $header)]['type']));
                $field_dir = $sort_field == $header ? $dir : $dir_default;
                $headers[$header] = '';
                $headers[$header] .= $sort_field == $header ? '<div class="wpcf-pr-sort-' . $dir . '"></div>' : '';
                $headers[$header] .= '<a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_sort&amp;field='
                                . $header . '&amp;sort=' . $field_dir . '&amp;post_id=' . $post->ID . '&amp;post_type='
                                . $post_type . '&amp;_wpnonce='
                                . wp_create_nonce('pr_sort')) . '">' . stripslashes($wpcf_fields[str_replace(WPCF_META_PREFIX,
                                        '', $header)]['name']) . '</a>';
                if (wpcf_admin_is_repetitive($wpcf_fields[str_replace(WPCF_META_PREFIX,
                                        '', $header)])) {
                    $repetitive_warning = true;
                }
            } else {
                $field_dir = $sort_field == $header ? $dir : $dir_default;
                $headers[$header] = '';
                $headers[$header] .= $sort_field == $header ? '<div class="wpcf-pr-sort-' . $dir . '"></div>' : '';
                $headers[$header] .= '<a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_sort&amp;field='
                                . $header . '&amp;sort=' . $field_dir . '&amp;post_id=' . $post->ID . '&amp;post_type='
                                . $post_type . '&amp;_wpnonce='
                                . wp_create_nonce('pr_sort')) . '">'
                        . stripslashes($header) . '</a>';
            }
        }
        if (!empty($data['fields']['_wpcf_pr_parents'])) {
            foreach ($data['fields']['_wpcf_pr_parents'] as $temp_parent => $temp_data) {
                if ($temp_parent == $parent_post_type) {
                    continue;
                }
                $temp_parent_type = get_post_type_object($temp_parent);
                if (empty($temp_parent_type)) {
                    continue;
                }
                $parent_dir = $sort_field == '_wpcf_pr_parent' ? $dir : $dir_default;
                $headers['_wpcf_pr_parent_' . $temp_parent] = $sort_field == '_wpcf_pr_parent' ? '<div class="wpcf-pr-sort-' . $dir . '"></div>' : '';
                $headers['_wpcf_pr_parent_' . $temp_parent] .= '<a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_sort&amp;field='
                                . '_wpcf_pr_parent&amp;sort='
                                . $parent_dir . '&amp;post_id=' . $post->ID . '&amp;post_type='
                                . $post_type . '&amp;post_type_sort_parent='
                                . $temp_parent . '&amp;_wpnonce='
                                . wp_create_nonce('pr_sort')) . '">' . $temp_parent_type->label . '</a>';
            }
        }
    } else {
        $item = new stdClass();
        $item->ID = 'new_' . mt_rand();
        $item->post_title = '';
        $item->post_content = '';
        $item->post_type = $post_type;
        $groups = wpcf_admin_post_get_post_groups_fields($item,
                'post_relationships_header');
        $title_dir = $sort_field == '_wp_title' ? $dir : $dir_default;
        $headers['_wp_title'] = '';
        $headers['_wp_title'] .= $sort_field == '_wp_title' ? '<div class="wpcf-pr-sort-' . $dir . '"></div>' : '';
        $headers['_wp_title'] .= '<a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_sort&amp;field='
                        . '_wp_title&amp;sort=' . $title_dir . '&amp;post_id=' . $post->ID . '&amp;post_type='
                        . $post_type . '&amp;_wpnonce='
                        . wp_create_nonce('pr_sort')) . '">' . __('Post Title') . '</a>';
        if ($data['fields_setting'] == 'all_cf_standard') {
            $body_dir = $sort_field == '_wp_body' ? $dir : $dir_default;
            $headers['_wp_body'] = '';
            $headers['_wp_body'] .= $sort_field == '_wp_body' ? '<div class="wpcf-pr-sort-' . $dir . '"></div>' : '';
            $headers['_wp_body'] = '<a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_sort&amp;field='
                            . '_wp_body&amp;sort=' . $body_dir . '&amp;post_id=' . $post->ID . '&amp;post_type='
                            . $post_type . '&amp;_wpnonce='
                            . wp_create_nonce('pr_sort')) . '">' . __('Post Body') . '</a>';
        }
        foreach ($groups as $group) {
            foreach ($group['fields'] as $field) {
                if (wpcf_admin_is_repetitive($field)) {
                    $repetitive_warning = true;
                }
                $header_key = wpcf_types_get_meta_prefix($field) . $field['slug'];
                wpcf_admin_post_field_load_js_css(wpcf_fields_type_action($field['type']));
                $field_dir = $sort_field == wpcf_types_get_meta_prefix($field) . $field['slug'] ? $dir : $dir_default;
                $headers[$header_key] = '';
                $headers[$header_key] .= $sort_field == wpcf_types_get_meta_prefix($field) . $field['slug'] ? '<div class="wpcf-pr-sort-' . $dir . '"></div>' : '';
                $headers[$header_key] .= '<a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_sort&amp;field='
                                . wpcf_types_get_meta_prefix($field) . $field['slug'] . '&amp;sort='
                                . $field_dir . '&amp;post_id=' . $post->ID . '&amp;post_type='
                                . $post_type . '&amp;_wpnonce='
                                . wp_create_nonce('pr_sort')) . '">'
                        . stripslashes($field['name']) . '</a>';
            }
        }
        // Get all parents
        $item_parents = wpcf_pr_admin_get_belongs($post_type);
        if ($item_parents) {
            foreach ($item_parents as $temp_parent => $temp_data) {
                if ($temp_parent == $parent_post_type) {
                    continue;
                }
                $temp_parent_type = get_post_type_object($temp_parent);
                $parent_dir = $sort_field == '_wpcf_pr_parent' ? $dir : $dir_default;
                $headers['_wpcf_pr_parent_' . $temp_parent] = '<a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_sort&amp;field='
                                . '_wpcf_pr_parent&amp;sort='
                                . $parent_dir . '&amp;post_id=' . $post->ID . '&amp;post_type='
                                . $post_type . '&amp;post_type_sort_parent='
                                . $temp_parent . '&amp;_wpnonce='
                                . wp_create_nonce('pr_sort')) . '">' . $temp_parent_type->label . '</a>';
            }
        }
    }
    return $headers;
}

/**
 * Has form.
 * 
 * @param type $post
 * @param type $post_type
 * @param type $data
 * @param type $parent_post_type
 * @return string 
 */
function wpcf_pr_admin_post_meta_box_has_form($post, $post_type, $data,
        $parent_post_type) {
    if (empty($post)) {
        return '';
    }
    global $wpdb;
    $output = array();
    $repetitive_warning = false;

    // Cleanup data
    if (empty($data['fields_setting'])) {
        $data['fields_setting'] = 'all_cf';
    }

    // List items
    if (isset($_GET['sort']) && isset($_GET['field'])) {

        if ($_GET['field'] == '_wp_title') {
            $items = get_posts('post_type=' . $post_type
                    . '&numberposts=-1&post_status=null&meta_key='
                    . '_wpcf_belongs_' . $parent_post_type . '_id&meta_value='
                    . $post->ID . '&orderby=title&suppress_filters=0&order='
                    . $_GET['sort']);
        } else if ($_GET['field'] == '_wpcf_pr_parent') {
            $items = get_posts('post_type='
                    . $post_type . '&numberposts=-1&post_status=null&meta_key='
                    . '_wpcf_belongs_' . $parent_post_type . '_id&meta_value='
                    . $post->ID . '&suppress_filters=0');
            if (!empty($items)) {
                $include = array();
                $additional = array();
                foreach ($items as $key => $item) {
                    $meta = get_post_meta($item->ID,
                            '_wpcf_belongs_' . $_GET['post_type_sort_parent'] . '_id',
                            true);
                    if (empty($meta)) {
                        $additional[] = $item;
                        continue;
                    }
                    $include[] = $item->ID;
                }
                if (!empty($include)) {
                    $items = get_posts('post_type='
                            . $post_type . '&numberposts=-1&post_status=null'
                            . '&meta_key=_wpcf_belongs_'
                            . $_GET['post_type_sort_parent'] . '_id'
                            . '&orderby=meta_value_num&order='
                            . esc_attr(strtoupper($_GET['sort']))
                            . '&suppress_filters=0&include=' . implode(',',
                                    $include));
                    $items = array_merge($items, $additional);
                }
            }
        } else if ($_GET['field'] == '_wp_body') {
            $query = "
        SELECT p.ID, p.post_title, p.post_content, p.post_type
        FROM $wpdb->posts p
        WHERE p.post_type = %s
        AND p.post_status <> 'auto-draft'
        GROUP BY p.ID
        ORDER BY p.post_content " . esc_attr(strtoupper($_GET['sort']));
            $items = $wpdb->get_results($wpdb->prepare($query, $post_type));
        } else {
            $field = wpcf_admin_fields_get_field(str_replace('wpcf-', '',
                            $_GET['field']));
            $orderby = isset($field['type']) && in_array($field['type'],
                            array('numeric', 'date')) ? 'meta_value_num' : 'meta_value';
            $items = get_posts('post_type='
                    . $post_type . '&numberposts=-1&post_status=null&meta_key='
                    . '_wpcf_belongs_' . $parent_post_type . '_id&meta_value='
                    . $post->ID . '&suppress_filters=0');
            if (!empty($items)) {
                $include = array();
                $additional = array();
                foreach ($items as $key => $item) {
                    $meta = get_post_meta($item->ID, $_GET['field'], true);
                    if (empty($meta)) {
                        $additional[] = $item;
                        continue;
                    }
                    $check = wpcf_cd_post_edit_field_filter(array(), $field,
                            $item, 'post-relationship-sort');
                    if (isset($check['__wpcf_cd_status'])
                            && $check['__wpcf_cd_status'] == 'failed') {
                        $additional[] = $item;
                    } else {
                        $include[] = $item->ID;
                    }
                }
                if (!empty($include)) {
                    $items = get_posts('post_type='
                            . $post_type . '&numberposts=-1&post_status=null&meta_key='
                            . $_GET['field'] . '&orderby=' . $orderby . '&order='
                            . esc_attr(strtoupper($_GET['sort']))
                            . '&suppress_filters=0&include=' . implode(',',
                                    $include));
                    $items = array_merge($items, $additional);
                }
            }
        }
    } else {
        $items = get_posts('post_type=' . $post_type
                . '&numberposts=-1&post_status=null&meta_key='
                . '_wpcf_belongs_' . $parent_post_type
                . '_id&suppress_filters=0&meta_value=' . $post->ID);
    }

    // Pagination
    $total_items = count($items);
    $per_page = 5;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $numberposts = $page == 1 ? 1 : ($page - 1) * $per_page;
    $slice = $page == 1 ? 0 : ($page - 1) * $per_page;
    $next = count($items) >= $numberposts + $per_page;
    $prev = $page == 1 ? false : true;
    if ($total_items > $per_page) {
        $items = array_splice($items, $slice, $per_page);
    }
    $headers = wpcf_pr_admin_post_meta_box_has_form_headers($post, $post_type,
            $parent_post_type, $data);
    // If headers are empty, that means there is nothing to render
    if (empty($headers)) {
        return '';
    }

    $header = '<thead><tr><th class="wpcf-sortable">'
            . implode('&nbsp;&nbsp;&nbsp;</th><th class="wpcf-sortable">',
                    $headers) . '&nbsp;&nbsp;&nbsp;</th><th>'
            . __('Action', 'wpcf')
            . '</th></tr></thead>';
    foreach ($items as $key => $item) {
        $output[] = wpcf_pr_admin_post_meta_box_has_row($post, $post_type,
                $data, $parent_post_type, $item, $headers);
    }

    $return = '';
    $return .= $repetitive_warning ? '<div class="wpcf-message wpcf-error"><p>'
            . __('Repeating fields should not be used in child posts. Types will update all field values.',
                    'wpcf') . '</p></div>' : '';
    $return .= wpcf_pr_admin_has_pagination($post, $post_type, $page, $prev,
            $next, $per_page, $total_items);
    $return .= '<div class="wpcf-pr-table-wrapper"><table id="wpcf_pr_table_sortable_'
            . md5($post_type) . '" class="tablesorter wpcf_pr_table_sortable"'
            . ' cellpadding="0" cellspacing="0" style="width:100%;">'
            . $header . '<tbody>' . implode($output) . '</tbody></table></div>';
    $return .= wpcf_form_render_js_validation('#post', false);
    return $return;
}

/**
 * Has form table row.
 * 
 * @param type $post
 * @param type $post_type
 * @param type $data
 * @param type $parent_post_type
 * @param stdClass $item
 * @return string 
 */
function wpcf_pr_admin_post_meta_box_has_row($post, $post_type, $data,
        $parent_post_type, $item, $headers) {
    if (empty($headers)) {
        return false;
    }
    $row_data = array_combine(array_keys($headers),
            array_fill(0, count($headers), ''));
    $new_item = false;
    $date_trigger = false;

    // Set item
    if (empty($item)) {
        $item = new stdClass();
        $item->ID = 'new_' . mt_rand();
        $item->post_title = '';
        $item->post_content = '';
        $item->post_type = $post_type;
        $new_item = true;
    }

    // Cleanup data
    if (empty($data['fields_setting'])) {
        $data['fields_setting'] = 'all_cf';
    }
    $item_parents = isset($data['fields']['_wpcf_pr_parents']) ? $data['fields']['_wpcf_pr_parents'] : array();
    unset($data['fields']['_wpcf_pr_parents']);

    $wpcf_fields = wpcf_admin_fields_get_fields();
    if ($data['fields_setting'] == 'specific' && !empty($data['fields'])) {
        foreach ($data['fields'] as $field_key => $true) {
            // Only add if exists in header
            if (!array_key_exists($field_key, $row_data)) {
                continue;
            }
            if ($field_key == '_wp_title') {
                $element = wpcf_form_simple(array('field' => array(
                        '#type' => 'textfield',
                        '#id' => 'wpcf_post_relationship_' . $item->ID . '_wp_title',
                        '#name' => 'wpcf_post_relationship[' . $item->ID . '][_wp_title]',
                        '#value' => $item->post_title,
                        '#inline' => true,
//                        '#attributes' => $new_item || $data['fields_setting'] == 'all_cf_standard' || isset($data['fields']['_wp_title']) ? array() : array('readonly' => 'readonly'),
                        )));
            } else if ($field_key == '_wp_body') {
                $value = wp_trim_words($item->post_content, 10, null);
                $element = wpcf_form_simple(array('field' => array(
                        '#type' => 'textarea',
                        '#id' => 'wpcf_post_relationship_' . $item->ID . '_' . $field_key,
                        '#name' => 'wpcf_post_relationship[' . $item->ID . '][' . $field_key . ']',
                        '#value' => $item->post_content,
                        '#attributes' => array('style' => 'width:300px;height:100px;'),
                        '#inline' => true,
                        )));
            } else {
                $wpcf_key = str_replace(WPCF_META_PREFIX, '', $field_key);
                if (strpos($field_key, WPCF_META_PREFIX) === 0
                        && isset($wpcf_fields[$wpcf_key])) {
                    // Date trigger
                    if ($wpcf_fields[$wpcf_key]['type'] == 'date') {
                        $date_trigger = true;
                    }
                    // Get WPCF form
                    $element = wpcf_admin_post_process_fields($item,
                            array('field' => $wpcf_fields[$wpcf_key]), false,
                            false, 'post_relationship');
                    $element = array_shift($element);
                    if (!in_array($wpcf_fields[$wpcf_key]['type'],
                                    array('image', 'file'))) {
                        $element['#id'] = 'wpcf_post_relationship_' . $item->ID . '_' . $wpcf_key;
                    }
                    $element['#name'] = 'wpcf_post_relationship[' . $item->ID . '][' . $field_key . ']';
                    $element['#inline'] = true;
                    unset($element['#title'], $element['#description']);
                    if ($wpcf_fields[$wpcf_key]['type'] == 'skype') {
                        unset($element['#prefix']);
                    }
                    if (in_array($wpcf_fields[$wpcf_key]['type'],
                                    array('wysiwyg'))) {
                        $element['#type'] = 'textarea';
                        $element['#attributes'] = array('style' => 'width:300px;height:100px;');
                    }
                    if (in_array($wpcf_fields[$wpcf_key]['type'],
                                    array('checkbox', 'checkboxes'))) {
                        if ($wpcf_fields[$wpcf_key]['type'] == 'checkbox') {
                            $element['#suffix'] = '<input type="hidden" name="wpcf_post_relationship_checkbox[' . $item->ID . '][' . $wpcf_key . ']" value="1" />';
                        } else if ($wpcf_fields[$wpcf_key]['type'] == 'checkboxes' && !empty($element['#options'])) {
                            foreach ($element['#options'] as $temp_key => $temp_value) {
                                $element['#options'][$temp_key]['#name'] = 'wpcf_post_relationship[' . $item->ID . '][' . $field_key . '][' . $temp_key . ']';
                                $element['#options'][$temp_key]['#inline'] = true;
                                $element['#options'][$temp_key]['#after'] = '<br />';
                            }
                            $element['#suffix'] = '<input type="hidden" name="wpcf_post_relationship_checkboxes[' . $item->ID . '][' . $field_key . ']" value="1" />';
                        }
                    }
                    $value = get_post_meta($item->ID, $field_key, true);
                    $element = wpcf_form_simple(array('field' => $element));
                } else {
                    // Just render textfield
                    $value = get_post_meta($item->ID, $field_key, true);
                    $element = wpcf_form_simple(array('field' => array(
                            '#type' => 'textfield',
                            '#id' => 'wpcf_post_relationship_' . $item->ID . '_' . $field_key,
                            '#name' => 'wpcf_post_relationship[' . $item->ID . '][' . $field_key . ']',
                            '#value' => $value,
                            '#inline' => true,
                            )));
                }
            }
            $row_data[$field_key] = $element;
        }

        // Get other parents
        foreach ($item_parents as $parent => $temp_data) {
            if ($parent == $parent_post_type) {
                continue;
            }
            $meta = get_post_meta($item->ID, '_wpcf_belongs_' . $parent . '_id',
                    true);
            $meta = empty($meta) ? 0 : $meta;
            $belongs_data = array('belongs' => array($parent => $meta));
            $temp_form = wpcf_pr_admin_post_meta_box_belongs_form($item,
                    $parent, $belongs_data);
            unset($temp_form[$parent]['#suffix'],
                    $temp_form[$parent]['#prefix'],
                    $temp_form[$parent]['#title']);
            $temp_form[$parent]['#name'] = 'wpcf_post_relationship[' . $item->ID . '][parents][' . $parent . ']';
            // Only add if exists in header
            if (array_key_exists('_wpcf_pr_parent_' . $parent, $row_data)) {
                $row_data['_wpcf_pr_parent_' . $parent] = wpcf_form_simple($temp_form);
            }
        }
    } else {
        $groups = wpcf_admin_post_get_post_groups_fields($item,
                'post_relationships');
        if (array_key_exists('_wp_title', $row_data)) {
            $element = wpcf_form_simple(array('field' => array(
                    '#type' => 'textfield',
                    '#id' => 'wpcf_post_relationship_' . $item->ID . '_wp_title',
                    '#name' => 'wpcf_post_relationship[' . $item->ID . '][_wp_title]',
                    '#value' => $item->post_title,
                    '#inline' => true,
//                        '#attributes' => $new_item || $data['fields_setting'] == 'all_cf_standard' || isset($data['fields']['_wp_title']) ? array() : array('readonly' => 'readonly'),
                    )));
            $row_data['_wp_title'] = $element;
        }
        if ($data['fields_setting'] == 'all_cf_standard' && array_key_exists('_wp_body',
                        $row_data)) {
            $element = wpcf_form_simple(array('field' => array(
                    '#type' => 'textarea',
                    '#id' => 'wpcf_post_relationship_' . $item->ID . '_wp_body',
                    '#name' => 'wpcf_post_relationship[' . $item->ID . '][_wp_body]',
                    '#value' => $item->post_content,
                    '#attributes' => array('style' => 'width:300px;height:100px;'),
                    '#inline' => true,
                    )));
            $row_data['_wp_body'] = $element;
        }
        foreach ($groups as $group) {
            foreach ($group['fields'] as $field) {
                // Only add if exists in header
                if (!array_key_exists(wpcf_types_get_meta_prefix($field) . $field['slug'],
                                $row_data)) {
                    continue;
                }
                // Date trigger
                if ($field['type'] == 'date') {
                    $date_trigger = true;
                }
                // Get WPCF form
                $element_org = wpcf_admin_post_process_fields($item,
                        array('field' => $field), false, false,
                        'post_relationship');
                $element = array_shift($element_org);
                if (!in_array($field['type'], array('image', 'file'))) {
                    $element['#id'] = 'wpcf_post_relationship_' . $item->ID . '_' . $field['id'];
                }
                $element['#name'] = 'wpcf_post_relationship[' . $item->ID . '][' . wpcf_types_get_meta_prefix($field) . $field['slug'] . ']';
                $element['#inline'] = true;
                unset($element['#title'], $element['#description']);
                if ($field['type'] == 'skype') {
                    unset($element['#prefix']);
                }
                if (in_array($field['type'], array('wysiwyg'))) {
                    $element['#type'] = 'textarea';
                    $element['#attributes'] = array('style' => 'width:300px;height:100px;');
                }
                if (in_array($field['type'], array('checkbox', 'checkboxes'))) {
                    if ($field['type'] == 'checkbox') {
                        $element['#suffix'] = '<input type="hidden" name="wpcf_post_relationship_checkbox[' . $item->ID . '][' . wpcf_types_get_meta_prefix($field) . $field['slug'] . ']" value="1" />';
                    } else if ($field['type'] == 'checkboxes' && !empty($element['#options'])) {
                        foreach ($element['#options'] as $temp_key => $temp_value) {
                            $element['#options'][$temp_key]['#name'] = 'wpcf_post_relationship[' . $item->ID . '][' . wpcf_types_get_meta_prefix($field) . $field['slug'] . '][' . $temp_key . ']';
                            $element['#options'][$temp_key]['#inline'] = true;
                            $element['#options'][$temp_key]['#after'] = '<br />';
                        }
                        $element['#suffix'] = '<input type="hidden" name="wpcf_post_relationship_checkboxes[' . $item->ID . '][' . wpcf_types_get_meta_prefix($field) . $field['slug'] . ']" value="1" />';
                    }
                }
                $value = get_post_meta($item->ID,
                        wpcf_types_get_meta_prefix($field) . $field['slug'],
                        true);
                $element = array('field' => $element);
                $element = wpcf_form_simple($element);
                $row_data[wpcf_types_get_meta_prefix($field) . $field['slug']] = $element;
            }
        }
        // Get all parents
        $item_parents = wpcf_pr_admin_get_belongs($post_type);
        if ($item_parents) {
            foreach ($item_parents as $parent => $temp_data) {
                if ($parent == $parent_post_type) {
                    continue;
                }
                $meta = get_post_meta($item->ID,
                        '_wpcf_belongs_' . $parent . '_id', true);
                $meta = empty($meta) ? 0 : $meta;
                $belongs_data = array('belongs' => array($parent => $meta));
                $temp_form = wpcf_pr_admin_post_meta_box_belongs_form($item,
                        $parent, $belongs_data);
                unset($temp_form[$parent]['#suffix'],
                        $temp_form[$parent]['#prefix'],
                        $temp_form[$parent]['#title']);
                $temp_form[$parent]['#name'] = 'wpcf_post_relationship[' . $item->ID . '][parents][' . $parent . ']';
                // Only add if exists in header
                if (array_key_exists('_wpcf_pr_parent_' . $parent, $row_data)) {
                    $row_data['_wpcf_pr_parent_' . $parent] = wpcf_form_simple($temp_form);
                }
            }
        }
    }

    if (!empty($row_data)) {
        $output = '';
        $output .= '<tr><td>' . implode('</td><td>', $row_data)
                . '<input type="hidden" name="wpcf_post_relationship['
                . $item->ID . '][post_type]" value="' . $post_type
                . '" /></td><td class="actions">'
                . '<a href="'
                . admin_url('admin-ajax.php?action=wpcf_ajax&amp;'
                        . 'wpcf_action=pr_save_child_post&amp;post_type_parent='
                        . $parent_post_type
                        . '&amp;post_id=' . $post->ID
                        . '&amp;post_type_child='
                        . $post_type . '&_wpnonce=' . wp_create_nonce('pr_save_child_post'))
                . '" class="wpcf-pr-save-ajax button-secondary">' . __('Save',
                        'wpcf') . '</a>';
        $output .= strpos($item->ID, 'new_') === false ?
                ' <a href="'
                . get_edit_post_link($item->ID)
                . '" class="button-secondary">' . __('Edit', 'wpcf') . '</a>' : '';
        $output .= strpos($item->ID, 'new_') === false ?
                ' <a href="'
                . admin_url('admin-ajax.php?action=wpcf_ajax&amp;'
                        . 'wpcf_action=pr_delete_child_post'
                        . '&amp;post_id=' . $item->ID
                        . '&_wpnonce=' . wp_create_nonce('pr_delete_child_post'))
                . '" class="wpcf-pr-delete-ajax button-secondary">' . __('Delete',
                        'wpcf') . '</a>' : '';
        if ($date_trigger) {
            $output .= '<script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready(function(){
            wpcfFieldsDateInit("#wpcf-post-relationship");
        });
        //]]>
    </script>';
        }
        if (defined('DOING_AJAX')) {
            $output .= '<script type="text/javascript">wpcfPrVerifyInit();</script>';
        }
        $output .= wpcf_form_render_js_validation('#post', false) . '</td></tr>';
        return $output;
    }
    return $output = '<tr><td><span style="color:Red;">' . __('Error occured',
                    'wpcf') . '</span></td></tr>';
}

/**
 * AJAX save child item call.
 * 
 * @param type $parent_post
 * @param type $post_type
 * @param type $data
 * @return string 
 */
function wpcf_pr_admin_save_child_item($parent_post, $post_type, $data) {
    if (!empty($data['wpcf_post_relationship'])) {
        $post_id = key($data['wpcf_post_relationship']);
        $original_post_id = $post_id;
        $save_fields = array_shift($data['wpcf_post_relationship']);
        if (strpos($post_id, 'new_') !== false) {
            $post_data['post_title'] = !empty($save_fields['_wp_title']) ? $save_fields['_wp_title'] : 'post ' . $post_id;
            $post_data['post_content'] = !empty($save_fields['_wp_body']) ? $save_fields['_wp_body'] : '';
            $post_data['post_type'] = $post_type;
            $post_data['post_status'] = 'publish';
        } else {
            $post_data = (array) get_post($post_id);
            $post_data['post_title'] = !empty($save_fields['_wp_title']) ? $save_fields['_wp_title'] : $post_data['post_title'];
            $post_data['post_content'] = !empty($save_fields['_wp_body']) ? $save_fields['_wp_body'] : $post_data['post_content'];
        }
        unset($save_fields['_wp_title'], $save_fields['_wp_body']);
        $post_id = wp_insert_post($post_data);

        // Set the language.        
        global $sitepress;
        if (isset($sitepress)) {
            $lang_details = $sitepress->get_element_language_details($parent_post->ID,
                    'post_' . $parent_post->post_type);
            if ($lang_details) {
                $sitepress->set_element_language_details($post_id,
                        'post_' . $post_type, null, $lang_details->language_code);
            }
        }



        // Save other parents
        if (isset($save_fields['parents'])) {
            foreach ($save_fields['parents'] as $parent_post_type => $parent_post_id) {
                update_post_meta($post_id,
                        '_wpcf_belongs_' . $parent_post_type . '_id',
                        $parent_post_id);
            }
            unset($save_fields['parents']);
        }

        foreach ($save_fields as $meta_key => $field_value) {
            // Process fields
            $field_slug = str_replace(WPCF_META_PREFIX, '', $meta_key);
            $field = wpcf_fields_get_field_by_slug($field_slug);
            if (!empty($field)) {
                // Apply filters
                $field_value = apply_filters('wpcf_fields_value_save',
                        $field_value, $field['type'], $field_slug);
                $field_value = apply_filters('wpcf_fields_slug_' . $field_slug
                        . '_value_save', $field_value);
                $field_value = apply_filters('wpcf_fields_type_' . $field['type']
                        . '_value_save', $field_value);
                $field_value = apply_filters('wpcf_pr_fields_type_' . $field['type']
                        . '_value_save', $field_value, $meta_key, $post_id);

                do_action('wpcf_fields_slug_' . $field_slug . '_save',
                        $field_value);
                do_action('wpcf_fields_type_' . $field['type'] . '_save',
                        $field_value);
            }
            update_post_meta($post_id, $meta_key, $field_value);
        }
        // Process single checkboxes
        if (isset($data['wpcf_post_relationship_checkbox'][$original_post_id])) {
            $check_meta = key($data['wpcf_post_relationship_checkbox'][$original_post_id]);
            if (!isset($save_fields[$check_meta])) {
                delete_post_meta($post_id, $check_meta);
            }
        }
        update_post_meta($post_id,
                '_wpcf_belongs_' . $parent_post->post_type . '_id',
                $parent_post->ID);

        $item = get_post($post_id);
        $relationships = get_option('wpcf_post_relationship', array());
        if (!isset($relationships[$parent_post->post_type][$item->post_type])) {
            return array('new_post_id' => 0, 'output' => __('Post not updated. Relationship data missing.',
                        'wpcf'));
        }
        $data = $relationships[$parent_post->post_type][$item->post_type];
        $headers = wpcf_pr_admin_post_meta_box_has_form_headers($parent_post,
                $item->post_type, $parent_post->post_type, $data);
        $output = wpcf_pr_admin_post_meta_box_has_row($parent_post,
                $item->post_type, $data, $parent_post->post_type, $item,
                $headers);

        return array('new_post_id' => $post_id, 'output' => $output);
    }
    return array('new_post_id' => 0, 'output' => __('Post not updated. Relationship data missing.',
                'wpcf'));
}

/**
 * AJAX delete child item call.
 * 
 * @param type $post_id
 * @return string 
 */
function wpcf_pr_admin_delete_child_item($post_id) {
    wp_delete_post($post_id, true);
    return __('Post deleted', 'wpcf');
}

/**
 * Belongs form.
 * 
 * @param type $post
 * @param type $post_type
 * @param type $data
 * @param type $parent_post_type
 */
function wpcf_pr_admin_post_meta_box_belongs_form($post, $type, $belongs) {
    if (empty($post)) {
        return array();
    }
    $temp_type = get_post_type_object($type);
    if (empty($temp_type)) {
        return array();
    }
    $form = array();
    $options = array(
        __('Not selected', 'wpcf') => 0,
    );
    $items = get_posts('post_type=' . $type . '&numberposts=-1&post_status=null&order=ASC&orderby=title&suppress_filters=0');
    if (empty($items)) {
        return array();
    }
    foreach ($items as $temp_post) {
        if ($temp_post->post_status == 'auto-draft') {
            continue;
        }
        $options[] = array(
            '#title' => $temp_post->post_title,
            '#value' => $temp_post->ID,
        );
    }
    $form[$type] = array(
        '#type' => 'select',
        '#name' => 'wpcf_pr_belongs[' . $type . ']',
        '#default_value' => isset($belongs['belongs'][$type]) ? $belongs['belongs'][$type] : 0,
        '#options' => $options,
        '#prefix' => $temp_type->label . '&nbsp;',
        '#suffix' => '&nbsp;<a href="'
        . admin_url('admin-ajax.php?action=wpcf_ajax'
                . '&amp;wpcf_action=pr-update-belongs&amp;_wpnonce='
                . wp_create_nonce('pr-update-belongs')
                . '&amp;post_id=' . $post->ID)
        . '" class="button-secondary wpcf-pr-update-belongs">' . __('Update',
                'wpcf') . '</a>',
    );
    return $form;
}

/**
 * Updates belongs data.
 * 
 * @param type $post_id
 * @param array $data $post_type => $post_id
 * @return string 
 */
function wpcf_pr_admin_update_belongs($post_id, $data) {
    $post_type = key($data);
    $post_owner_id = array_shift($data);
    if (!empty($post_id) && !empty($post_type) && !empty($post_owner_id)) {
        update_post_meta($post_id, '_wpcf_belongs_' . $post_type . '_id',
                $post_owner_id);
        return __('Post updated', 'wpcf');
    }
    return __('Passed wrong parameters', 'wpcf');
}

/**
 * Pagination link.
 * 
 * @param type $post
 * @param type $post_type
 * @param type $page
 * @param type $prev
 * @param type $next
 * @return string 
 */
function wpcf_pr_admin_has_pagination($post, $post_type, $page, $prev, $next,
        $per_page = 20, $count = 20) {
    $link = '';
    $add = '';
    if (isset($_GET['sort'])) {
        $add .= '&sort=' . $_GET['sort'];
    }
    if (isset($_GET['field'])) {
        $add .= '&field=' . $_GET['field'];
    }
    if (isset($_GET['post_type_sort_parent'])) {
        $add .= '&post_type_sort_parent=' . $_GET['post_type_sort_parent'];
    }
    if ($prev) {
        $link .= '<a class="button-secondary wpcf-pr-pagination-link wpcf-pr-prev" href="'
                . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_pagination&amp;page='
                        . ($page - 1) . '&amp;dir=prev&amp;post_id=' . $post->ID . '&amp;post_type='
                        . $post_type . '&amp;_wpnonce='
                        . wp_create_nonce('pr_pagination') . $add) . '">'
                . __('Prev', 'wpcf') . '</a>&nbsp;&nbsp;';
    }
    if ($per_page < $count) {
        $total_pages = ceil($count / $per_page);
        $link .= '<select class="wpcf-pr-pagination-select" name="wpcf-pr-pagination-select">';
        for ($index = 1; $index <= $total_pages; $index++) {
            $link .= '<option';
            if (($index) == $page) {
                $link .= ' selected="selected"';
            }
            $link .= ' value="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_pagination&amp;page='
                            . $index . '&amp;dir=next&amp;post_id=' . $post->ID . '&amp;post_type='
                            . $post_type . '&amp;_wpnonce='
                            . wp_create_nonce('pr_pagination') . $add) . '">' . $index . '</option>';
        }
        $link .= '</select>';
    }
    if ($next) {
        $link .= '<a class="button-secondary wpcf-pr-pagination-link wpcf-pr-next" href="'
                . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=pr_pagination&amp;page='
                        . ($page + 1) . '&amp;dir=next&amp;post_id=' . $post->ID . '&amp;post_type='
                        . $post_type . '&amp;_wpnonce='
                        . wp_create_nonce('pr_pagination') . $add) . '">'
                . __('Next', 'wpcf') . '</a>';
    }
    return $link;
}

/**
 * Save post hook.
 * 
 * @param type $parent_post_id
 * @return string 
 */
function wpcf_pr_admin_save_post_hook($parent_post_id) {

    // WPML check if it's translation of a child
    // Fix up the parent if it's the child of a related post and it doesn't yet have a parent
    if (function_exists('icl_object_id')) {

        remove_filter('get_post_metadata', 'wpcf_pr_meta_belongs_filter', 10, 4);

        $post = get_post($parent_post_id);
        $original_post_id = icl_object_id($parent_post_id, $post->post_type,
                false);
        if (!empty($original_post_id)) {
            // it has a translation
            $original_post = get_post($original_post_id);
            if (!empty($original_post)) {

                // look for _wpcf_belongs_xxxx_id fields.

                $metas = get_post_custom($original_post->ID);
                foreach ($metas as $meta_key => $meta) {
                    if (strpos($meta_key, '_wpcf_belongs_') !== false) {
                        $meta_post = get_post($meta[0]);
                        $exists = get_post_meta($parent_post_id, $meta_key, true);
                        if (!empty($meta_post) && empty($exists)) {
                            global $sitepress;
                            $ulanguage = $sitepress->get_language_for_element($parent_post_id,
                                    'post_' . $post->post_type);
                            $meta_translated_id = icl_object_id($meta_post->ID,
                                    $meta_post->post_type, false, $ulanguage);
                            // Only force if meta is not already set
                            if (!empty($meta_translated_id)) {
                                update_post_meta($parent_post_id, $meta_key,
                                        $meta_translated_id);
                            }
                        }
                    }
                }
            }
        }

        add_filter('get_post_metadata', 'wpcf_pr_meta_belongs_filter', 10, 4);
    }
    // Save child items
    if (defined('DOING_AJAX') && !isset($_REQUEST['wpcf_action'])) {
        return array();
    }
    remove_action('save_post', 'wpcf_pr_admin_save_post_hook', 10, 2);
    static $processed = array();
    if (isset($processed[$parent_post_id])) {
        return array();
    }
    $parent_post = get_post($parent_post_id);
    $results = array();
    if (!empty($_POST['wpcf_post_relationship'])) {
        foreach ($_POST['wpcf_post_relationship'] as $post_id => $data) {
            $post_id_sent = $post_id;
            $post_type = $data['post_type'];
            unset($data['post_type']);
            $send_data = array();
            $send_data['wpcf_post_relationship'][$post_id] = $data;
            if (isset($_POST['wpcf_post_relationship_checkbox'][$post_id])) {
                $send_data['wpcf_post_relationship_checkbox'][$post_id] = $_POST['wpcf_post_relationship_checkbox'][$post_id];
            }
            extract(wpcf_pr_admin_save_child_item($parent_post, $post_type,
                            $send_data));
            $results[$post_id_sent] = $output;
        }
    }
    if (isset($_POST['wpcf_post_relationship_checkboxes'])) {
        foreach ($_POST['wpcf_post_relationship_checkboxes'] as $post_id => $data) {
            foreach ($data as $temp_slug => $temp_options) {
                if (!isset($_POST['wpcf_post_relationship'][$post_id][$temp_slug])) {
                    $post_type = isset($_POST['post_type_child']) ? $_POST['post_type_child'] : $_POST['post_type'];
                    extract(wpcf_pr_admin_save_child_item($parent_post,
                                    $post_type,
                                    array('wpcf_post_relationship' => array(
                                    $new_post_id => array($new_post_id => array())
                                )
                                    )
                            ));
                    $results[$post_id] = $output;
                }
            }
        }
    }
    $processed[$parent_post_id] = 1;
    return implode('', $results);
}

/**
 * Returns translated '_wpcf_belongs_XXX_id' if any.
 * 
 * @global type $sitepress
 * @param type $value
 * @param type $object_id
 * @param type $meta_key
 * @param type $single
 * @return type 
 */
function wpcf_pr_meta_belongs_filter($value, $object_id, $meta_key, $single) {
    // WPML check if it's translation of a child
    // Only force if meta is not already set
    if (empty($value) && function_exists('icl_object_id') && strpos($meta_key,
                    '_wpcf_belongs_') !== false) {
        $post = get_post($object_id);
        $original_post_id = icl_object_id($object_id, $post->post_type, false);
        if (!empty($original_post_id)) {
            remove_filter('get_post_metadata', 'wpcf_pr_meta_belongs_filter',
                    10, 4);
            $original_post_meta = get_post_meta($original_post_id, $meta_key,
                    true);
            add_filter('get_post_metadata', 'wpcf_pr_meta_belongs_filter', 10, 4);
            if (!empty($original_post_meta)) {
                $meta_post = get_post($original_post_meta);
                if (!empty($meta_post)) {
                    global $sitepress;
                    $ulanguage = $sitepress->get_language_for_element($object_id,
                            'post_' . $post->post_type);
                    $meta_translated_id = icl_object_id($meta_post->ID,
                            $meta_post->post_type, false, $ulanguage);
                    if (!empty($meta_translated_id)) {
                        $value = $meta_translated_id;
                    }
                }
            }
        }
    }
    return $value;
}

/**
 * JS for fields AJAX.
 */
function wpcf_pr_add_field_js() {

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            wpcfPrVerifyInit();
        });
                                                                                                                
        function wpcfPrVerifyInit() {
            jQuery('.wpcf-pr-has-entries .wpcf-cd').each(function(){
                jQuery(this).parents('tr').find(':input').each(function(){
                    if (jQuery(this).hasClass('wpcf-pr-binded')) {
                        return false;
                    }
                    jQuery(this).addClass('wpcf-pr-binded');
                    if (jQuery(this).hasClass('radio')
                        || jQuery(this).hasClass('checkbox')) {
                        jQuery(this).bind('click', function(){
                            wpcfPrVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val());
                        });
                    } else if (jQuery(this).hasClass('select')) {
                        jQuery(this).bind('change', function(){
                            wpcfPrVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val());
                        });
                    } else if (jQuery(this).hasClass('wpcf-datepicker')) {
                        jQuery(this).bind('wpcfDateBlur', function(){
                            wpcfPrVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val());
                        });
                    } else {
                        jQuery(this).bind('blur', function(){
                            wpcfPrVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val());
                        });
                    }
                });
            });
        }
                                                                                                                                                                        
        function wpcfPrVerify(object, name, value) {
            var form = object.parents('tr').find(':input');
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                data: form.serialize()+'<?php echo '&action=wpcf_ajax&wpcf_action=pr_verify&_wpnonce=' . wp_create_nonce('pr_verify'); ?>',
                cache: false,
                beforeSend: function() {
                },
                success: function(data) {
                    if (data != null) {
                        if (typeof data.execute != 'undefined'
                            && (typeof data.wpcf_nonce_ajax_callback != 'undefined'
                            && data.wpcf_nonce_ajax_callback == wpcf_nonce_ajax_callback)) {
                            eval(data.execute);
                        }
                    }
                }
            });
        }
    </script>
    <?php
}