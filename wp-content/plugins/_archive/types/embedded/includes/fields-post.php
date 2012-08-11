<?php
/*
 * Edit post page functions
 */
require_once WPCF_EMBEDDED_ABSPATH . '/includes/conditional-display.php';

/**
 * Init functions for post edit pages.
 * 
 * @param type $upgrade 
 */
function wpcf_admin_post_init($post = false) {

    wpcf_admin_add_js_settings('wpcf_nonce_toggle_group',
            '\'' . wp_create_nonce('group_form_collapsed') . '\'');
    wpcf_admin_add_js_settings('wpcf_nonce_toggle_fieldset',
            '\'' . wp_create_nonce('form_fieldset_toggle') . '\'');

    // Get post_type
    if ($post) {
        $post_type = get_post_type($post);
    } else {
        if (!isset($_GET['post_type'])) {
            $post_type = 'post';
        } else if (in_array($_GET['post_type'],
                        get_post_types(array('show_ui' => true)))) {
            $post_type = $_GET['post_type'];
        } else {
            return false;
        }
    }

    // Add items to View dropdown
    if (in_array($post_type, array('view', 'view-template'))) {
        add_filter('editor_addon_menus_wpv-views',
                'wpcf_admin_post_editor_addon_menus_filter');
        add_action('admin_footer', 'wpcf_admin_post_js_validation');
    }

    // Never show on 'Views' and 'View Templates'
    if (in_array($post_type, array('view', 'view-template'))) {
        return false;
    }

    // Add marketing box
    if (!in_array($post_type, array('post', 'page'))) {
        add_meta_box('wpcf-marketing',
                __('How-To Display Custom Content', 'wpcf'),
                'wpcf_admin_post_marketing_meta_box', $post_type, 'side', 'high');
    }

    // Get groups
    $groups = wpcf_admin_post_get_post_groups_fields($post);
    $wpcf_active = false;
    foreach ($groups as $key => $group) {
        if (!empty($group['fields'])) {
            $wpcf_active = true;
            // Process fields
            $group['fields'] = wpcf_admin_post_process_fields($post,
                    $group['fields'], true);
        }
        // Add meta boxes
        add_meta_box($group['slug'],
                wpcf_translate('group ' . $group['id'] . ' name', $group['name']),
                'wpcf_admin_post_meta_box', $post_type,
                $group['meta_box_context'], 'high', $group);
    }

    // Activate scripts
    if ($wpcf_active) {
        wp_enqueue_script('wpcf-fields-post',
                WPCF_EMBEDDED_RES_RELPATH . '/js/fields-post.js',
                array('jquery'), WPCF_VERSION);
        wp_enqueue_script('wpcf-form-validation',
                WPCF_EMBEDDED_RES_RELPATH . '/js/'
                . 'jquery-form-validation/jquery.validate.min.js',
                array('jquery'), WPCF_VERSION);
        wp_enqueue_script('wpcf-form-validation-additional',
                WPCF_EMBEDDED_RES_RELPATH . '/js/'
                . 'jquery-form-validation/additional-methods.min.js',
                array('jquery'), WPCF_VERSION);
        wp_enqueue_style('wpcf-fields-basic',
                WPCF_EMBEDDED_RES_RELPATH . '/css/basic.css', array(),
                WPCF_VERSION);
        wp_enqueue_style('wpcf-fields-post',
                WPCF_EMBEDDED_RES_RELPATH . '/css/fields-post.css',
                array('wpcf-fields-basic'), WPCF_VERSION);
        add_action('admin_footer', 'wpcf_admin_post_js_validation');
    }
    do_action('wpcf_admin_post_init', $post_type, $post, $groups, $wpcf_active);
}

/**
 * Renders meta box content.
 * 
 * @param type $post
 * @param type $group 
 */
function wpcf_admin_post_meta_box($post, $group) {
    if (!empty($group['args']['_conditional_display'])) {
        if ($group['args']['_conditional_display'] == 'failed') {
            echo '<div class="wpcf-cd-group wpcf-cd-group-failed" style="display:none;">';
        } else {
            echo '<div class="wpcf-cd-group wpcf-cd-group-passed">';
        }
    }
    if (!empty($group['args']['fields'])) {
        // Display description
        if (!empty($group['args']['description'])) {
            echo '<div class="wpcf-meta-box-description">'
            . wpautop(wpcf_translate('group ' . $group['args']['id'] . ' description',
                            $group['args']['description'])) . '</div>';
        }
        foreach ($group['args']['fields'] as $field_slug => $field) {
            if (empty($field)) {
                continue;
            }
            // Render form elements
            if (wpcf_compare_wp_version() && $field['#type'] == 'wysiwyg') {
                // Especially for WYSIWYG
                echo '<div class="wpcf-wysiwyg">';
                echo '<div id="wpcf-textarea-textarea-wrapper" class="form-item form-item-textarea wpcf-form-item wpcf-form-item-textarea">';
                echo isset($field['#before']) ? $field['#before'] : '';
                echo '
<label class="wpcf-form-label wpcf-form-textarea-label">' . $field['#title'] . '</label>';
                echo '<div class="description wpcf-form-description wpcf-form-description-textarea description-textarea">
' . wpautop($field['#description']) . '</div>';
                wp_editor($field['#value'], $field['#id'],
                        $field['#editor_settings']);
                $field['slug'] = str_replace(WPCF_META_PREFIX . 'wysiwyg-', '',
                        $field_slug);
                $field['type'] = 'wysiwyg';
                echo '</div>';
                echo isset($field['#after']) ? $field['#after'] : '';
                echo '</div><br /><br />';
            } else {
                if ($field['#type'] == 'wysiwyg') {
                    $field['#type'] = 'textarea';
                }
                echo wpcf_form_simple(array($field['#id'] => $field));
            }
            do_action('wpcf_fields_' . $field_slug . '_meta_box_form', $field);
            if (isset($field['wpcf-type'])) { // May be ignored
                do_action('wpcf_fields_' . $field['wpcf-type'] . '_meta_box_form',
                        $field);
            }
        }
    }
    if (!empty($group['args']['_conditional_display'])) {
        echo '</div>';
    }
}

/**
 * save_post hook.
 * 
 * @param type $post_ID
 * @param type $post 
 */
function wpcf_admin_post_save_post_hook($post_ID, $post) {
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'],
                    'update-' . $post->post_type . '_' . $post_ID)) {
        return false;
    }
    if (in_array($post->post_type,
                    array('revision', 'attachment', 'wp-types-group', 'view',
                'view-template'))) {
        return false;
    }

    // Get groups
    $groups = wpcf_admin_post_get_post_groups_fields($post);
    if (empty($groups)) {
        return false;
    }
    $all_fields = array();
    foreach ($groups as $group) {
        // Process fields
        $fields = wpcf_admin_post_process_fields($post, $group['fields'], true,
                false, 'validation');
        // Validate fields
        $form = wpcf_form_simple_validate($fields);
        $all_fields = $all_fields + $fields;
        $error = $form->isError();
        // Trigger form error
        if ($error) {
            wpcf_admin_message_store(
                    __('Please check your input data', 'wpcf'), 'error');
        }
    }

    // Save invalid elements so user can be informed after redirect
    if (!empty($all_fields)) {
        update_post_meta($post_ID, 'wpcf-invalid-fields', $all_fields);
    }

    // Save meta fields
    if (!empty($_POST['wpcf'])) {
        foreach ($_POST['wpcf'] as $field_slug => $field_value) {
            // Skip copied fields
            if (isset($_POST['wpcf_repetitive_copy'][$field_slug])) {
                continue;
            }
            $field = wpcf_fields_get_field_by_slug($field_slug);
            if (empty($field)) {
                continue;
            }
            $meta_key = wpcf_types_get_meta_prefix($field) . $field_slug;
            // Don't save invalid
            if (isset($all_fields[$meta_key]['#error'])) {
                continue;
            }
            // Repetitive fields
            if (isset($_POST['wpcf_repetitive'][$field_slug])) {
                delete_post_meta($post_ID, $meta_key);
                foreach ($field_value as $temp_id => $repetitive_data) {
                    // Skype specific
                    if ($field['type'] == 'skype') {
                        unset($repetitive_data['old_value']);
                        wpcf_admin_post_save_field($post_ID, $meta_key, $field,
                                $repetitive_data, true);
                    } else {
                        wpcf_admin_post_save_field($post_ID, $meta_key, $field,
                                $repetitive_data['new_value'], true);
                    }
                }
            } else {
                wpcf_admin_post_save_field($post_ID, $meta_key, $field,
                        $field_value);
            }
        }
    }

    // Process checkboxes
    foreach ($all_fields as $field) {
        if (!isset($field['#type'])) {
            continue;
        }
        if ($field['#type'] == 'checkbox'
                && !isset($_POST['wpcf'][$field['wpcf-slug']])) {
            $field_data = wpcf_admin_fields_get_field($field['wpcf-id']);
            if (isset($field_data['data']['save_empty'])
                    && $field_data['data']['save_empty'] == 'yes') {
                update_post_meta($post_ID,
                        wpcf_types_get_meta_prefix($field) . $field['wpcf-slug'],
                        0);
            } else {
                delete_post_meta($post_ID,
                        wpcf_types_get_meta_prefix($field) . $field['wpcf-slug']);
            }
        }
        if ($field['#type'] == 'checkboxes') {
            $field_data = wpcf_admin_fields_get_field($field['wpcf-id']);
            if (!empty($field_data['data']['options'])) {
                $update_data = array();
                foreach ($field_data['data']['options'] as $option_id => $option_data) {
                    if (!isset($_POST['wpcf'][$field['wpcf-slug']][$option_id])) {
                        if (isset($field_data['data']['save_empty'])
                                && $field_data['data']['save_empty'] == 'yes') {
                            $update_data[$option_id] = 0;
                        }
                    } else {
                        $update_data[$option_id] = $_POST['wpcf'][$field['wpcf-slug']][$option_id];
                    }
                }
                update_post_meta($post_ID,
                        wpcf_types_get_meta_prefix($field) . $field['wpcf-slug'],
                        $update_data);
            }
        }
    }
}

/**
 * Saves single field.
 * 
 * @param type $post_ID
 * @param type $all_fields
 * @param type $field_slug
 * @param type $field_value
 * @param string $old_value
 * @return boolean 
 */
function wpcf_admin_post_save_field($post_ID, $meta_key, $field, $field_value,
        $add = false) {
    // Apply filters
    $field_value = apply_filters('wpcf_fields_value_save', $field_value,
            $field['type'], $field['slug'], $field);
    $field_value = apply_filters('wpcf_fields_slug_' . $field['slug']
            . '_value_save', $field_value, $field);
    $field_value = apply_filters('wpcf_fields_type_' . $field['type']
            . '_value_save', $field_value, $field);

    // Save field
    if ($add) {
        add_post_meta($post_ID, $meta_key, $field_value);
    } else {
        update_post_meta($post_ID, $meta_key, $field_value);
    }

    do_action('wpcf_fields_save', $field_value, $field);
    do_action('wpcf_fields_slug_' . $field['slug'] . '_save', $field_value,
            $field);
    do_action('wpcf_fields_type_' . $field['type'] . '_save', $field_value,
            $field);
}

/**
 * Renders JS validation script.
 */
function wpcf_admin_post_js_validation() {
    wpcf_form_render_js_validation('#post');

    ?>
    <script type="text/javascript">
        //<![CDATA[
        function wpcfFieldsEditorCallback(field_id) {

            var url = "<?php echo admin_url('admin-ajax.php'); ?>?action=wpcf_ajax&wpcf_action=editor_callback&_wpnonce=<?php echo wp_create_nonce('editor_callback'); ?>&field_id="+field_id+"&keepThis=true&TB_iframe=true&height=400&width=400";
            tb_show("<?php
    _e('Insert field', 'wpcf');

    ?>", url);
        }
                                                        
        var wpcfFieldsEditorCallback_redirect = null;
                                                        
        function wpcfFieldsEditorCallback_set_redirect(function_name, params) {
            wpcfFieldsEditorCallback_redirect = {'function' : function_name, 'params' : params};
        }
                                                        
        //]]>
    </script>
    <?php
}

/**
 * Creates form elements.
 * 
 * @param type $post
 * @param type $fields
 * @return type 
 */
function wpcf_admin_post_process_fields($post = false, $fields = array(),
        $use_cache = true, $add_to_editor = true, $context = 'group') {

    // Get cached
    static $cache = array();
    $cache_key = !empty($post->ID) ? $post->ID . md5(serialize($fields)) : false;
    if ($use_cache && $cache_key && isset($cache[$cache_key])) {
        return $cache[$cache_key];
    }

    $fields_processed = array();

    // Get invalid fields (if submitted)
    $invalid_fields = array();
    if (!empty($post->ID)) {
        $invalid_fields = get_post_meta($post->ID, 'wpcf-invalid-fields', true);
        delete_post_meta($post->ID, 'wpcf-invalid-fields');
    }

    $original_cf = array();
    if (function_exists('wpml_get_copied_fields_for_post_edit')) {
        $original_cf = wpml_get_copied_fields_for_post_edit();
    }

    foreach ($fields as $field) {

        // Repetitive fields
        if (wpcf_admin_is_repetitive($field) && $context != 'post_relationship') {
            $temp_flag = false;

            // First check if repetitive fields are copied using WPML
            if (!empty($original_cf['fields'])) {

                // Check if marked
                if (in_array(wpcf_types_get_meta_prefix($field) . $field['slug'],
                                $original_cf['fields'])) {

                    // Get original post field values
                    $temp_fields = get_post_meta($original_cf['original_post_id'],
                            wpcf_types_get_meta_prefix($field) . $field['slug'],
                            false);

                    // If there are original field values stored
                    if (!empty($temp_fields)) {
                        foreach ($temp_fields as $temp_key => $temp_field) {
                            $single_field = $field;
                            $single_field['wpml_action'] = 'copy';
                            $single_field['value'] = $temp_field;
                            $temp_processed = wpcf_admin_post_process_field($post,
                                    $single_field, $use_cache, $add_to_editor,
                                    $context);
                            if ($temp_processed) {
                                $single_field = $temp_processed['field'];
                                $element = $temp_processed['element'];
                                if ($field['type'] == 'skype') {
                                    foreach ($element as $temp_element_key => $temp_element_value) {
                                        $fields_processed[$temp_element_value['#id']] = apply_filters('wpcf_post_edit_field',
                                                $temp_element_value, $field,
                                                $post, $context, $original_cf,
                                                $invalid_fields);
                                    }
                                } else {
                                    $fields_processed[$element['#id']] = apply_filters('wpcf_post_edit_field',
                                            $element, $single_field, $post,
                                            $context, $original_cf,
                                            $invalid_fields);
                                }
                            }
                        }

                        // If there are no original fields stored
                        // display empty element
                    } else {
                        $single_field = $field;
                        $single_field['wpml_action'] = 'copy';
                        $single_field['value'] = null;
                        $temp_processed = wpcf_admin_post_process_field($post,
                                $single_field, $use_cache, $add_to_editor,
                                $context);
                        if ($temp_processed) {
                            $single_field = $temp_processed['field'];
                            $element = $temp_processed['element'];
                            if ($field['type'] == 'skype') {
                                foreach ($element as $temp_element_key => $temp_element_value) {
                                    $fields_processed[$temp_element_value['#id']] = apply_filters('wpcf_post_edit_field',
                                            $temp_element_value, $field, $post,
                                            $context, $original_cf,
                                            $invalid_fields);
                                }
                            } else {
                                $fields_processed[$element['#id']] = apply_filters('wpcf_post_edit_field',
                                        $element, $single_field, $post,
                                        $context, $original_cf, $invalid_fields);
                            }
                        }
                    }
                } else {
                    $temp_flag = true;
                }
            } else {
                $temp_flag = true;
            }
            
            // Get repetitive fields values
            if ($temp_flag && !empty($post->ID)) {
                $temp_flag = false;
                $temp_fields = get_post_meta($post->ID,
                        wpcf_types_get_meta_prefix($field) . $field['slug'],
                        false);
                if (!empty($temp_fields)) {
                    $temp_start = true;
                    $fields_processed[$field['id'] . '_repetitive_wrapper_open'] = array(
                        '#type' => 'markup',
                        '#markup' => '<div id="wpcf_'
                        . $field['id']
                        . '_repetitive_wrapper_' . mt_rand()
                        . '" class="wpcf-repetitive-wrapper">',
                        '#id' => $field['id'] . '_repetitive_wrapper_open',
                    );
                    foreach ($temp_fields as $temp_key => $temp_field) {
                        $single_field = $field;
                        $single_field['value'] = $temp_field;
                        $temp_processed = wpcf_admin_post_process_field($post,
                                $single_field, $use_cache, $add_to_editor,
                                $context);
                        if ($temp_processed) {
                            $single_field = $temp_processed['field'];
                            $element = $temp_processed['element'];
                            if ($field['type'] == 'skype') {
                                foreach ($element as $temp_element_key => $temp_element_value) {
                                    if (!$temp_start && !in_array($field['type'],
                                                    array('checkbox'))) {
                                        unset($temp_element_value['#title']);
                                    }
                                    $temp_start = false;
                                    $fields_processed[$temp_element_value['#id']] = apply_filters('wpcf_post_edit_field',
                                            $temp_element_value, $field, $post,
                                            $context, $original_cf,
                                            $invalid_fields);
                                }
                            } else {
                                if (!$temp_start && !in_array($field['type'],
                                                array('checkbox'))) {
                                    unset($element['#title']);
                                }
                                $temp_start = false;
                                $fields_processed[$element['#id']] = apply_filters('wpcf_post_edit_field',
                                        $element, $single_field, $post,
                                        $context, $original_cf, $invalid_fields);
                            }
                        }
                    }
                    $fields_processed[$field['id'] . '_repetitive_wrapper_close'] = array(
                        '#type' => 'markup',
                        '#markup' => '</div>',
                        '#id' => $field['id'] . '_repetitive_wrapper_close',
                    );
                } else {
                    $temp_flag = true;
                }
            }

            // Temp flag for repetitive field is triggered if post is new
            // and field is not marked to be copied
            if ($temp_flag == true) {
                $temp_processed = wpcf_admin_post_process_field($post, $field,
                        $use_cache, $add_to_editor, $context);
                if ($temp_processed) {
                    $fields_processed[$field['id'] . '_repetitive_wrapper_open'] = array(
                        '#type' => 'markup',
                        '#markup' => '<div id="wpcf_'
                        . $field['id']
                        . '_repetitive_wrapper_' . mt_rand()
                        . '" class="wpcf-repetitive-wrapper">',
                        '#id' => $field['id'] . '_repetitive_wrapper_open',
                    );
                    $field = $temp_processed['field'];
                    $element = $temp_processed['element'];
                    if ($field['type'] == 'skype') {
                        foreach ($element as $temp_element_key => $temp_element_value) {
                            $fields_processed[$temp_element_value['#id']] = apply_filters('wpcf_post_edit_field',
                                    $temp_element_value, $field, $post,
                                    $context, $original_cf, $invalid_fields);
                        }
                    } else {
                        $fields_processed[$element['#id']] = apply_filters('wpcf_post_edit_field',
                                $element, $field, $post, $context, $original_cf,
                                $invalid_fields);
                    }
                    $fields_processed[$field['id'] . '_repetitive_wrapper_close'] = array(
                        '#type' => 'markup',
                        '#markup' => '</div>',
                        '#id' => $field['id'] . '_repetitive_wrapper_close',
                    );
                }
            }



            // Non-repetitive fields
        } else {
            if (!empty($post->ID)) {
                $field['value'] = get_post_meta($post->ID,
                        wpcf_types_get_meta_prefix($field) . $field['slug'],
                        true);
            }
            // Check if repetitive field is copied using WPML
            if (!empty($original_cf['fields'])) {
                if (in_array(wpcf_types_get_meta_prefix($field) . $field['slug'],
                                $original_cf['fields'])) {
                    $field['wpml_action'] = 'copy';
                    $field['value'] = get_post_meta($original_cf['original_post_id'],
                            wpcf_types_get_meta_prefix($field) . $field['slug'],
                            true);
                }
            }
            $temp_processed = wpcf_admin_post_process_field($post, $field,
                    $use_cache, $add_to_editor, $context);
            if ($temp_processed) {
                $field = $temp_processed['field'];
                $element = $temp_processed['element'];
                if ($field['type'] == 'skype') {
                    foreach ($element as $temp_element_key => $temp_element_value) {
                        $fields_processed[$temp_element_value['#id']] = apply_filters('wpcf_post_edit_field',
                                $temp_element_value, $field, $post, $context,
                                $original_cf, $invalid_fields);
                    }
                } else {
                    $fields_processed[$element['#id']] = apply_filters('wpcf_post_edit_field',
                            $element, $field, $post, $context, $original_cf,
                            $invalid_fields);
                }
            }
        }
    }

    // Cache results
    if ($cache_key) {
        $cache[$cache_key] = $fields_processed;
    }

    return $fields_processed;
}

/**
 * Processes single field.
 * 
 * @staticvar array $repetitive_started
 * @param type $post
 * @param type $field
 * @param type $use_cache
 * @param type $add_to_editor
 * @param type $context
 * @param type $original_cf
 * @param type $invalid_fields
 * @return mixed boolean|array
 */
function wpcf_admin_post_process_field($post = false, $field_unedited = array(),
        $use_cache = true, $add_to_editor = true, $context = 'group',
        $original_cf = array(), $invalid_fields = array()) {

    $field = wpcf_admin_fields_get_field($field_unedited['id']);
    if (!empty($field)) {

        // Set values
        $field['value'] = isset($field_unedited['value']) ? maybe_unserialize($field_unedited['value']) : '';
        $field['wpml_action'] = isset($field_unedited['wpml_action']) ? $field_unedited['wpml_action'] : '';

        $field_id = 'wpcf-' . $field['type'] . '-' . $field['slug'] . '-' . mt_rand();
        $field_init_data = wpcf_fields_type_action($field['type']);

        // Get inherited field
        $inherited_field_data = false;
        if (isset($field_init_data['inherited_field_type'])) {
            $inherited_field_data = wpcf_fields_type_action($field_init_data['inherited_field_type']);
        }

        // Apply filters
        $field['value'] = apply_filters('wpcf_fields_value_get',
                $field['value'], $field, $field_init_data);
        $field['value'] = apply_filters('wpcf_fields_slug_' . $field['slug']
                . '_value_get', $field['value'], $field, $field_init_data);
        $field['value'] = apply_filters('wpcf_fields_type_' . $field['type']
                . '_value_get', $field['value'], $field, $field_init_data);

        wpcf_admin_post_field_load_js_css($field_init_data);

        $element = array();

        // Set generic values
        $element = array(
            '#type' => isset($field_init_data['inherited_field_type']) ? $field_init_data['inherited_field_type'] : $field['type'],
            '#id' => $field_id,
            '#title' => wpcf_translate('field ' . $field['id'] . ' name',
                    $field['name']),
            '#description' => wpautop(wpcf_translate('field ' . $field['id'] . ' description',
                            $field['description'])),
            '#name' => 'wpcf[' . $field['slug'] . ']',
            '#value' => isset($field['value']) ? $field['value'] : '',
            'wpcf-id' => $field['id'],
            'wpcf-slug' => $field['slug'],
            'wpcf-type' => $field['type'],
        );

        // Set inherited values
        $element_inherited = array();
        if ($inherited_field_data) {
            if (function_exists('wpcf_fields_'
                            . $field_init_data['inherited_field_type']
                            . '_meta_box_form')) {
                $element_inherited = call_user_func_array('wpcf_fields_'
                        . $field_init_data['inherited_field_type']
                        . '_meta_box_form', array($field, $element));
            }
        }

        $element = array_merge($element, $element_inherited);

        if (isset($field['description_extra'])) {
            $element['#description'] .= wpautop($field['description_extra']);
        }

        // Set atributes #1
        if (isset($field['disable'])) {
            $field['#disable'] = $field['disable'];
        }
        if (!empty($field['disable'])) {
            $field['#attributes']['disabled'] = 'disabled';
        }
        if (!empty($field['readonly'])) {
            $field['#attributes']['readonly'] = 'readonly';
        }

        // Set specific values
        if (defined('WPCF_INC_ABSPATH')
                && file_exists(WPCF_INC_ABSPATH . '/fields/' . $field['type']
                        . '.php')) {
            require_once WPCF_INC_ABSPATH . '/fields/' . $field['type']
                    . '.php';
        }

        // Load field
        if (function_exists('wpcf_fields_' . $field['type']
                        . '_meta_box_form')) {
            $element_specific = call_user_func_array('wpcf_fields_'
                    . $field['type'] . '_meta_box_form', array($field, $element));
            // Check if it's single
            if (isset($element_specific['#type'])) {
                // Format description
                if (!empty($element_specific['#description'])) {
                    $element_specific['#description'] = wpautop($element_specific['#description']);
                }
                $element = array_merge($element, $element_specific);
                // Set validation element
                if (isset($field['data']['validate'])) {
                    $element['#validate'] = $field['data']['validate'];
                }
                // Repetitive fields
                if (wpcf_admin_is_repetitive($field) && $context != 'post_relationship') {
                    $element = wpcf_admin_post_process_repetitive_field($post,
                            $field, $element);
                }
            } else { // More fields, loop all
                // Only Skype for now have multiple fields, so process only that
                if ($field['type'] == 'skype') {
                    $skype_element = array();
                    foreach ($element_specific as $element_specific_fields_key => $element_specific_fields_value) {
                        $element_specific_fields_value['__element_key'] = $element_specific_fields_key;
                        // Format description
                        if (!empty($element_specific_fields_value['#description'])) {
                            $element_specific_fields_value['#description'] = wpautop($element_specific_fields_value['#description']);
                        }
                        // If no ID
                        if (!isset($element_specific_fields_value['#id'])) {
                            $element_specific_fields_value['#id'] = 'wpcf-'
                                    . $field['slug'] . '-' . mt_rand();
                        }
                        // Set validation element
                        if (!empty($element_specific_fields_value['#_validate_this']) && isset($field['data']['validate'])) {
                            $element_specific_fields_value['#validate'] = $field['data']['validate'];
                        }
                        if ($element_specific_fields_key != 'skypename') {
                            if (!isset($element_specific_fields_value['#name'])) {
                                $element_specific_fields_value['#name'] = 'wpcf[ignore]['
                                        . mt_rand() . ']';
                            }
                            $skype_element[$element_specific_fields_value['#id']] = $element_specific_fields_value;
                            continue;
                        }
                        // This one is actually value and keep it (#name is required)
                        $element = array_merge($element,
                                $element_specific_fields_value);
                        // Add it here to keep order
                        $skype_element[$element['#id']] = $element;
                    }
                    // Repetitive fields
                    if (wpcf_admin_is_repetitive($field) && $context != 'post_relationship') {
                        list($element, $skype_element) = wpcf_admin_post_process_repetitive_field_skype($post,
                                $field, $skype_element);
                    }
                }
            }
        } else {
            // Repetitive fields
            if (wpcf_admin_is_repetitive($field) && $context != 'post_relationship') {
                $element = wpcf_admin_post_process_repetitive_field($post,
                        $field, $element);
            }
        }

        // Set atributes #2 (override)
        if (isset($field['disable'])) {
            $element['#disable'] = $field['disable'];
        }
        if (!empty($field['disable'])) {
            $element['#attributes']['disabled'] = 'disabled';
        }
        if (!empty($field['readonly'])) {
            $element['#attributes']['readonly'] = 'readonly';
            if (!empty($element['#options'])) {
                foreach ($element['#options'] as $key => $option) {
                    if (!is_array($option)) {
                        $element['#options'][$key] = array(
                            '#title' => $key,
                            '#value' => $option,
                        );
                    }
                    $element['#options'][$key]['#attributes']['readonly'] = 'readonly';
                    if ($element['#type'] == 'select') {
                        $element['#options'][$key]['#attributes']['disabled'] = 'disabled';
                    }
                }
            }
            if ($element['#type'] == 'select') {
                $element['#attributes']['disabled'] = 'disabled';
            }
        }

        // Set validation element
        if ($field['type'] != 'skype' && empty($element['#validate']) && isset($field['data']['validate'])) {
            $element['#validate'] = $field['data']['validate'];
        }

        // Check if it was invalid on submit and add error message
        if ($post && !empty($invalid_fields)) {
            if (isset($invalid_fields[$element['#id']]['#error'])) {
                $element['#error'] = $invalid_fields[$element['#id']]['#error'];
            }
        }

        // Set WPML locked icon
        if (isset($field['wpml_action']) && $field['wpml_action'] == 'copy') {
            $element['#title'] .= '<img src="' . WPCF_EMBEDDED_RES_RELPATH . '/images/locked.png" alt="'
                    . __('This field is locked for editing because WPML will copy its value from the original language.',
                            'wpcf') . '" title="'
                    . __('This field is locked for editing because WPML will copy its value from the original language.',
                            'wpcf') . '" style="position:relative;left:2px;top:2px;" />';
        }

        // Add to editor
        if ($add_to_editor) {
            wpcf_admin_post_add_to_editor($field);
        }

        // Add repetitive class
        // @TODO Why not add repetitive class if copied?
        if (wpcf_admin_is_repetitive($field) && $context != 'post_relationship'
                && (!isset($field['wpml_action']) || $field['wpml_action'] != 'copy')) {
            if (!empty($element['#options']) && $element['#type'] != 'select') {
                foreach ($element['#options'] as $temp_key => $temp_value) {
                    $element['#options'][$temp_key]['#attributes']['class'] = isset($element['#attributes']['class']) ? $element['#attributes']['class'] . ' wpcf-repetitive' : 'wpcf-repetitive';
                }
            } else {
                $element['#attributes']['class'] = isset($element['#attributes']['class']) ? $element['#attributes']['class'] . ' wpcf-repetitive' : 'wpcf-repetitive';
            }
            wpcf_admin_add_js_settings('wpcfFormRepetitiveUniqueValuesCheckText',
                    '\'' . __('Warning: same values set', 'wpcf') . '\'');
        }

        // Set read-only if copied by WPML
        if (isset($field['wpml_action']) && $field['wpml_action'] == 'copy') {
            if (isset($element['#options'])) {
                foreach ($element['#options'] as $temp_key => $temp_value) {
                    if (isset($temp_value['#attributes'])) {
                        $element['#options'][$temp_key]['#attributes']['readonly'] = 'readonly';
                    } else {
                        $element['#options'][$temp_key]['#attributes'] = array('readonly' => 'readonly');
                    }
                }
            }
            if ($field['type'] == 'select') {
                if (isset($element['#attributes'])) {
                    $element['#attributes']['disabled'] = 'disabled';
                } else {
                    $element['#attributes'] = array('disabled' => 'disabled');
                }
            } else {
                if (isset($element['#attributes'])) {
                    $element['#attributes']['readonly'] = 'readonly';
                } else {
                    $element['#attributes'] = array('readonly' => 'readonly');
                }
            }
        }

        // Specific for Skype
        if ($field['type'] == 'skype') {
            $skype_element[$element['#id']] = $element;
            $element = $skype_element;
        }

        return array('field' => $field, 'element' => $element);
    }
    return false;
}

/**
 * Gets all groups and fields for post.
 * 
 * @param type $post_ID
 * @return type 
 */
function wpcf_admin_post_get_post_groups_fields($post = false,
        $context = 'group') {

    // Get post_type
    if (!empty($post)) {
        $post_type = get_post_type($post);
    } else {
        if (!isset($_GET['post_type'])) {
            $post_type = 'post';
        } else if (in_array($_GET['post_type'],
                        get_post_types(array('show_ui' => true)))) {
            $post_type = $_GET['post_type'];
        } else {
            $post_type = 'post';
        }
    }

    // Get post terms
    $support_terms = false;
    if (!empty($post)) {
        $post->_wpcf_post_terms = array();
        $taxonomies = get_taxonomies('', 'objects');
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $tax_slug => $tax) {
                $temp_tax = get_taxonomy($tax_slug);
                if (!in_array($post_type, $temp_tax->object_type)) {
                    continue;
                }
                $support_terms = true;
                $terms = wp_get_post_terms($post->ID, $tax_slug,
                        array('fields' => 'ids'));
                foreach ($terms as $term_id) {
                    $post->_wpcf_post_terms[] = $term_id;
                }
            }
        }
    }

    // Get post template
    if (empty($post)) {
        $post = new stdClass();
        $post->_wpcf_post_template = false;
        $post->_wpcf_post_views_template = false;
    } else {
        $post->_wpcf_post_template = get_post_meta($post->ID,
                '_wp_page_template', true);
        $post->_wpcf_post_views_template = get_post_meta($post->ID,
                '_views_template', true);
    }

    if (empty($post->_wpcf_post_terms)) {
        $post->_wpcf_post_terms = array();
    }

    // Filter groups
    $groups = array();
    $groups_all = wpcf_admin_fields_get_groups();
    foreach ($groups_all as $temp_key => $temp_group) {
        if (empty($temp_group['is_active'])) {
            unset($groups_all[$temp_key]);
            continue;
        }
        // Get filters
        $groups_all[$temp_key]['_wp_types_group_post_types'] = explode(',',
                trim(get_post_meta($temp_group['id'],
                                '_wp_types_group_post_types', true), ','));
        $groups_all[$temp_key]['_wp_types_group_terms'] = explode(',',
                trim(get_post_meta($temp_group['id'], '_wp_types_group_terms',
                                true), ','));
        $groups_all[$temp_key]['_wp_types_group_templates'] = explode(',',
                trim(get_post_meta($temp_group['id'],
                                '_wp_types_group_templates', true), ','));

        $post_type_filter = $groups_all[$temp_key]['_wp_types_group_post_types'][0] == 'all' ? -1 : 0;
        $taxonomy_filter = $groups_all[$temp_key]['_wp_types_group_terms'][0] == 'all' ? -1 : 0;
        $template_filter = $groups_all[$temp_key]['_wp_types_group_templates'][0] == 'all' ? -1 : 0;

        // See if post type matches
        if ($post_type_filter == 0 && in_array($post_type,
                        $groups_all[$temp_key]['_wp_types_group_post_types'])) {
            $post_type_filter = 1;
        }

        // See if terms match
        if ($taxonomy_filter == 0) {
            foreach ($post->_wpcf_post_terms as $temp_post_term) {
                if (in_array($temp_post_term,
                                $groups_all[$temp_key]['_wp_types_group_terms'])) {
                    $taxonomy_filter = 1;
                }
            }
        }

        // See if template match
        if ($template_filter == 0) {
            if ((!empty($post->_wpcf_post_template) && in_array($post->_wpcf_post_template,
                            $groups_all[$temp_key]['_wp_types_group_templates']))
                    || (!empty($post->_wpcf_post_views_template) && in_array($post->_wpcf_post_views_template,
                            $groups_all[$temp_key]['_wp_types_group_templates']))) {
                $template_filter = 1;
            }
        }
        // Filter by association
        if (empty($groups_all[$temp_key]['filters_association'])) {
            $groups_all[$temp_key]['filters_association'] = 'any';
        }
        // If context is post_relationship allow all groups that match post type
        if ($context == 'post_relationships_header') {
            $groups_all[$temp_key]['filters_association'] = 'any';
        }
        if ($post_type_filter == -1 && $taxonomy_filter == -1 && $template_filter == -1) {
            $passed = 1;
        } else if ($groups_all[$temp_key]['filters_association'] == 'any') {
            $passed = $post_type_filter == 1 || $taxonomy_filter == 1 || $template_filter == 1;
        } else {
            $passed = $post_type_filter != 0 && $taxonomy_filter != 0 && $template_filter != 0;
        }
        if (!$passed) {
            unset($groups_all[$temp_key]);
        } else {
            $groups_all[$temp_key]['fields'] = wpcf_admin_fields_get_fields_by_group($temp_group['id'],
                    'slug', true, false, true);
        }
    }
    $groups = apply_filters('wpcf_post_groups', $groups_all, $post, $context);
    return $groups;
}

/**
 * Stores fields for editor menu.
 * 
 * @staticvar array $fields
 * @param type $field
 * @return array 
 */
function wpcf_admin_post_add_to_editor($field) {
    static $fields = array();
    if ($field == 'get') {
        return $fields;
    }
    if (empty($fields)) {
        add_action('admin_enqueue_scripts', 'wpcf_admin_post_add_to_editor_js');
    }
    $fields[$field['id']] = $field;
}

/**
 * Renders JS for editor menu.
 * 
 * @return type 
 */
function wpcf_admin_post_add_to_editor_js() {
    global $post;
    $fields = wpcf_admin_post_add_to_editor('get');
    $groups = wpcf_admin_post_get_post_groups_fields($post);
    if (empty($fields) || empty($groups)) {
        return false;
    }
    $editor_addon = new Editor_addon('types',
                    __('Insert Types Shortcode', 'wpcf'),
                    WPCF_EMBEDDED_RES_RELPATH . '/js/types_editor_plugin.js',
                    WPCF_EMBEDDED_RES_RELPATH . '/images/bw-logo-16.png');

    foreach ($groups as $group) {
        if (empty($group['fields'])) {
            continue;
        }
        foreach ($group['fields'] as $group_field_id => $group_field) {
            if (!isset($fields[$group_field_id])) {
                continue;
            }
            $field = $fields[$group_field_id];
            $data = wpcf_fields_type_action($field['type']);
            $callback = '';
            if (isset($data['editor_callback'])) {
                $callback = sprintf($data['editor_callback'], $field['id']);
            } else {
                // Set callback if function exists
                $function = 'wpcf_fields_' . $field['type'] . '_editor_callback';
                $callback = function_exists($function) ? 'wpcfFieldsEditorCallback(\'' . $field['id'] . '\')' : '';
            }

            $editor_addon->add_insert_shortcode_menu(stripslashes($field['name']),
                    trim(wpcf_fields_get_shortcode($field), '[]'),
                    $group['name'], $callback);
        }
    }
}

/**
 * Adds items to view dropdown.
 * 
 * @param type $items
 * @return type 
 */
function wpcf_admin_post_editor_addon_menus_filter($items) {
    $groups = wpcf_admin_fields_get_groups();
    $all_post_types = implode(' ', get_post_types(array('public' => true)));
    $add = array();
    if (!empty($groups)) {
        // $group_id is blank therefore not equal to $group['id']
        // use array for item key and CSS class
        $item_styles = array();

        foreach ($groups as $group_id => $group) {
            $fields = wpcf_admin_fields_get_fields_by_group($group['id'],
                    'slug', true, false, true);
            if (!empty($fields)) {
                // code from Types used here without breaking the flow
                // get post types list for every group or apply all
                $post_types = get_post_meta($group['id'],
                        '_wp_types_group_post_types', true);
                if ($post_types == 'all') {
                    $post_types = $all_post_types;
                }
                $post_types = trim(str_replace(',', ' ', $post_types));
                $item_styles[$group['name']] = $post_types;

                foreach ($fields as $field_id => $field) {
                    // Get field data
                    $data = wpcf_fields_type_action($field['type']);

                    // Get inherited field
                    if (isset($data['inherited_field_type'])) {
                        $inherited_field_data = wpcf_fields_type_action($data['inherited_field_type']);
                    }

                    $callback = '';
                    if (isset($data['editor_callback'])) {
                        $callback = sprintf($data['editor_callback'],
                                $field['id']);
                    } else {
                        // Set callback if function exists
                        $function = 'wpcf_fields_' . $field['type'] . '_editor_callback';
                        $callback = function_exists($function) ? 'wpcfFieldsEditorCallback(\'' . $field['id'] . '\')' : '';
                    }
                    $add[$group['name']][stripslashes($field['name'])] = array(stripslashes($field['name']), trim(wpcf_fields_get_shortcode($field),
                                '[]'), $group['name'], $callback);

                    // Process JS
                    if (!empty($data['meta_box_js'])) {
                        foreach ($data['meta_box_js'] as $handle => $data_script) {
                            if (isset($data_script['inline'])) {
                                add_action('admin_footer',
                                        $data_script['inline']);
                                continue;
                            }
                            $deps = !empty($data_script['deps']) ? $data_script['deps'] : array();
                            wp_enqueue_script($handle, $data_script['src'],
                                    $deps, WPCF_VERSION);
                        }
                    }

                    // Process CSS
                    if (!empty($data['meta_box_css'])) {
                        foreach ($data['meta_box_css'] as $handle => $data_script) {
                            $deps = !empty($data_script['deps']) ? $data_script['deps'] : array();
                            if (isset($data_script['inline'])) {
                                add_action('admin_header',
                                        $data_script['inline']);
                                continue;
                            }
                            wp_enqueue_style($handle, $data_script['src'],
                                    $deps, WPCF_VERSION);
                        }
                    }
                }
            }
        }
    }

    $search_key = '';

    // Iterate all items to be displayed in the "V" menu
    foreach ($items as $key => $item) {
        if ($key == __('Basic', 'wpv-views')) {
            $search_key = 'found';
            continue;
        }
        if ($search_key == 'found') {
            $search_key = $key;
        }

        if ($key == __('Field', 'wpv-views') && isset($item[trim(wpcf_types_get_meta_prefix(),
                                '-')])) {
            unset($items[$key][trim(wpcf_types_get_meta_prefix(), '-')]);
        }
    }
    if (empty($search_key) || $search_key == 'found') {
        $search_key = count($items);
    }

    $insert_position = array_search($search_key, array_keys($items));
    $part_one = array_slice($items, 0, $insert_position);
    $part_two = array_slice($items, $insert_position);
    $items = $part_one + $add + $part_two;

    // apply CSS styles to each item based on post types
    foreach ($items as $key => $value) {
        if (isset($item_styles[$key])) {
            $items[$key]['css'] = $item_styles[$key];
        } else {
            $items[$key]['css'] = $all_post_types;
        }
    }

    return $items;
}

/**
 * Load JS and CSS for field type.
 * 
 * @staticvar array $cache
 * @param type $field_init_data
 * @return string 
 */
function wpcf_admin_post_field_load_js_css($field_init_data) {
    static $cache = array();
    if (isset($cache[$field_init_data['id']])) {
        return '';
    }
    // Process JS
    if (!empty($field_init_data['meta_box_js'])) {
        foreach ($field_init_data['meta_box_js'] as $handle => $data) {
            if (isset($data['inline'])) {
                add_action('admin_footer', $data['inline']);
                continue;
            }
            $deps = !empty($data['deps']) ? $data['deps'] : array();
            $in_footer = !empty($data['in_footer']) ? $data['in_footer'] : false;
            wp_register_script($handle, $data['src'], $deps, WPCF_VERSION,
                    $in_footer);
            wp_enqueue_script($handle);
        }
    }

    // Process CSS
    if (!empty($field_init_data['meta_box_css'])) {
        foreach ($field_init_data['meta_box_css'] as $handle => $data) {
            if (isset($data['src'])) {
                $deps = !empty($data['deps']) ? $data['deps'] : array();
                wp_enqueue_style($handle, $data['src'], $deps, WPCF_VERSION);
            } else if (isset($data['inline'])) {
                add_action('admin_head', $data['inline']);
            }
        }
    }
    $cache[$field_init_data['id']] = 1;
}

/**
 * Processes repetitive field.
 * 
 * @staticvar array $repetitive_started
 * @staticvar array $repetitive_index
 * @param string $field
 * @param type $element
 * @return string 
 */
function wpcf_admin_post_process_repetitive_field($post, $field, $element) {
    static $repetitive_started = array();
    static $repetitive_index = array();
    if (defined('DOING_AJAX')) {
        if ($field['type'] == 'skype') {
            $field['value'] = array();
        } else {
            $field['value'] = '__wpcf_repetitive_new_field';
        }
    }
    $field['value'] = apply_filters('wpcf_repetitive_field_old_value',
            $field['value'], $field, $post, $element);

    if (!isset($repetitive_index[$field['id']])) {
        if (defined('DOING_AJAX') && isset($_POST['count'])) {
            $repetitive_index[$field['id']] = $_POST['count'];
        } else {
            $repetitive_index[$field['id']] = 1;
        }
    }
    // Add hidden fields old_value and mark field repetitive
    $repetitive_form = '<input type="hidden" name="wpcf_repetitive['
            . $field['id'] . '][' . $repetitive_index[$field['id']]
            . ']" value="1" />';
    $repetitive_form .= '<input type="hidden" name="' . $element['#name']
            . '[' . $repetitive_index[$field['id']] . '][old_value]" value="'
            . base64_encode($field['value']) . '" />';

    // Alter element name
    $element['#name'] = $element['#name'] . '['
            . $repetitive_index[$field['id']] . '][new_value]';

    // Add repetitive control buttons if not copied by WPML
    if (!isset($field['wpml_action']) || $field['wpml_action'] != 'copy') {
        if (!isset($repetitive_started[$field['id']]) && !defined('DOING_AJAX')) {
            // Add 'Add' button
            $repetitive_form .= '<div class="wpcf-repetitive-buttons" style="margin-top:10px;">';
            $repetitive_form .= '<a href="'
                    . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=repetitive_add&amp;_wpnonce=' . wp_create_nonce('repetitive_add'))
                    . '&amp;field_id=' . $field['id'] . '&amp;field_id_md5='
                    . md5($field['id'])
                    . '" class="wpcf-repetitive-add button-primary">Add Another Field</a>';
            $repetitive_form .= '</div>';
        } else {
            $repetitive_form .= '<div class="wpcf-repetitive-buttons" style="margin-top:10px;">';

            // Add 'Delete' button
            if (!empty($post->ID) && !defined('DOING_AJAX')) {
                $repetitive_form .= '&nbsp;<a href="'
                        . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=repetitive_delete&amp;_wpnonce=' . wp_create_nonce('repetitive_delete')
                                . '&amp;post_id=' . $post->ID . '&amp;field_id='
                                . $field['id'] . '&amp;old_value='
                                . base64_encode($field['value']))
                        . '&amp;wpcf_warning=' . __('Are you sure?', 'wpcf')
                        . '&amp;field_id_md5='
                        . md5($field['id'])
                        . '" class="wpcf-repetitive-delete button-secondary">Delete Field</a>';
            } else {
                $repetitive_form .= '&nbsp;<a href="javascript:void(0);" onclick="jQuery(this).parent().parent().fadeOut(function(){jQuery(this).remove();});" class="wpcf-repetitive-delete button-secondary">Delete Field</a>';
            }
            $repetitive_form .= '</div>';
        }

        // Prepend to form element
        if (isset($element['#before'])) {
            $element['#before'] .= $repetitive_form;
        } else {
            $element['#before'] = $repetitive_form;
        }

        // Append AJAX response area to element
        if (!isset($repetitive_started[$field['id']]) && !defined('DOING_AJAX')) {
            if (isset($element['#after'])) {
                $element['#after'] .= '<div class="wpcf-repetitive-response"></div>';
            } else {
                $element['#after'] = '<div class="wpcf-repetitive-response"></div>';
            }
        }

        // Date trigger
        if (defined('DOING_AJAX') && $field['type'] == 'date') {
            $date_trigger = '<script type="text/javascript">
            wpcfFieldsDateInit("#"+jQuery("#' . $element['#id'] . '").parent().attr("id"));
    </script>';
            if (isset($element['#after'])) {
                $element['#after'] .= $date_trigger;
            } else {
                $element['#after'] = $date_trigger;
            }
        }

        // If copied with WPML add hidden element that will be used to skip field
    } else if (isset($field['wpml_action']) && $field['wpml_action'] == 'copy') {
        if (isset($element['#after'])) {
            $element['#after'] .= '<input type="hidden" name="wpcf_repetitive_copy['
                    . $field['id'] . '][' . $repetitive_index[$field['id']]
                    . ']" value="1" />';
        } else {
            $element['#after'] = '<input type="hidden" name="wpcf_repetitive_copy['
                    . $field['id'] . '][' . $repetitive_index[$field['id']]
                    . ']" value="1" />';
        }
    }

    // Mark that first repetitive field is processed
    $repetitive_started[$field['id']] = true;
    $repetitive_index[$field['id']] += 1;

    // Set JS var for counting repetitive fields
    wpcf_admin_add_js_settings('wpcf_repetitive_count_' . md5($field['id']),
            $repetitive_index[$field['id']]);

    return $element;
}

/**
 * Processes repetitive Skype field.
 * 
 * @staticvar array $repetitive_started
 * @staticvar array $repetitive_index
 * @param type $post
 * @param string $field
 * @param type $skype_element
 * @return string 
 */
function wpcf_admin_post_process_repetitive_field_skype($post, $field,
        $skype_element) {
    static $repetitive_started = array();
    static $repetitive_index = array();
    $repetitive_form = '';
    if (defined('DOING_AJAX')) {
        $field['value'] = '__wpcf_repetitive_new_field';
    }

    // Set index
    if (!isset($repetitive_index[$field['id']])) {
        if (defined('DOING_AJAX') && isset($_POST['count'])) {
            $repetitive_index[$field['id']] = $_POST['count'];
        } else {
            $repetitive_index[$field['id']] = 1;
        }
    }

    foreach ($skype_element as $element_key_temp => &$element) {
        $element_key = $element['__element_key'];
        if (!isset($field['value'][$element_key])) {
            $field['value'][$element_key] = '';
        }

        if ($element_key != 'markup') {
            // Alter element name
            $element['#name'] = 'wpcf[' . $field['slug'] . ']['
                    . $repetitive_index[$field['id']] . ']['
                    . $element_key . ']';
        }

        // If not marked as copied by WPML add control buttons
        if (!isset($field['wpml_action']) || $field['wpml_action'] != 'copy') {

            if ($element_key == 'skypename') {
                // Add hidden fields old_value and mark field repetitive
                $repetitive_form .= '<input type="hidden" name="wpcf_repetitive['
                        . $field['id'] . '][' . $repetitive_index[$field['id']]
                        . ']" value="1" />';
                $repetitive_form .= '<input type="hidden" name="wpcf[' . $field['slug']
                        . '][' . $repetitive_index[$field['id']] . '][old_value]" value="'
                        . base64_encode(serialize($field['value'])) . '" />';
            }

            if ($element_key == 'skypename' && !isset($repetitive_started[$field['id']]) && !defined('DOING_AJAX')) {
                // Add 'Add' button
                $repetitive_form .= '<div class="wpcf-repetitive-buttons" style="margin-top:10px;">';
                $repetitive_form .= '<a href="'
                        . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=repetitive_add&amp;_wpnonce=' . wp_create_nonce('repetitive_add'))
                        . '&amp;field_id=' . $field['id'] . '&amp;field_id_md5='
                        . md5($field['id'])
                        . '" class="wpcf-repetitive-add button-primary">Add Another Field</a>';
                $repetitive_form .= '</div>';
            } else if ($element_key == 'skypename') {
                $repetitive_form .= '<div class="wpcf-repetitive-buttons" style="margin-top:10px;">';

                // Add 'Delete' button
                if (!empty($post->ID)) {
                    $repetitive_form .= '&nbsp;<a href="'
                            . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=repetitive_delete&amp;_wpnonce=' . wp_create_nonce('repetitive_delete')
                                    . '&amp;post_id=' . $post->ID . '&amp;field_id='
                                    . $field['id'] . '&amp;field_id_md5='
                                    . md5($field['id']) . '&amp;old_value='
                                    . base64_encode(serialize($field['value'])))
                            . '&amp;wpcf_warning=' . __('Are you sure?', 'wpcf')
                            . '" class="wpcf-repetitive-delete button-secondary">Delete Field</a>';
                } else {
                    $repetitive_form .= '&nbsp;<a href="javascript:void(0);" onclick="jQuery(this).parent().parent().fadeOut(function(){jQuery(this).remove();});" class="wpcf-repetitive-delete button-secondary">Delete Field</a>';
                }
                $repetitive_form .= '</div>';
            }


            // Prepend to form element
            if ($element_key == 'skypename' && isset($element['#before'])) {
                $element['#before'] .= $repetitive_form;
            } else if ($element_key == 'skypename') {
                $element['#before'] = $repetitive_form;
            }

            // Append AJAX response area to element
            if ($element_key == 'markup'
                    && !isset($repetitive_started[$field['id']])
                    && !defined('DOING_AJAX')) {
                $element['#markup'] .= '<div class="wpcf-repetitive-response"></div>';
            }

            // If marked as copied by WPML add hidden field
        } else if ($element_key == 'skypename'
                && isset($field['wpml_action']) && $field['wpml_action'] == 'copy') {
            if (isset($element['#after'])) {
                $element['#after'] .= '<input type="hidden" name="wpcf_repetitive_copy['
                        . $field['id'] . '][' . $repetitive_index[$field['id']]
                        . ']" value="1" />';
            } else {
                $element['#after'] = '<input type="hidden" name="wpcf_repetitive_copy['
                        . $field['id'] . '][' . $repetitive_index[$field['id']]
                        . ']" value="1" />';
            }
        }

        if ($element_key == 'skypename') {
            $main_element = $element;
        }
    }

    // Mark that first repetitive field is processed
    $repetitive_started[$field['id']] = true;
    $repetitive_index[$field['id']] += 1;

    // Set JS var for counting repetitive fields
    wpcf_admin_add_js_settings('wpcf_repetitive_count_' . md5($field['id']),
            $repetitive_index[$field['id']]);

    return array($main_element, $skype_element);
}

/**
 * Marketing meta-box 
 */
function wpcf_admin_post_marketing_meta_box() {
    $output = '';
    if (defined('WPV_VERSION')) {
        $output .= '<p>' . sprintf(__("%sViews%s let's you create templates, query content from the database and display it.",
                                'wpcf'),
                        '<a href="http://wp-types.com/home/views-create-elegant-displays-for-your-content/?utm_source=types&utm_medium=plugin&utm_term=views&utm_content=promobox&utm_campaign=types" title="Views" target="_blank">',
                        '</a>') . '</p>';
        $output .= '<p><a href="' . admin_url('edit.php?post_type=view-template') . '">' . __('Create <strong>View Templates</strong> for single pages &raquo;',
                        'wpcf') . '</a></p>';
        $output .= '<p><a href="' . admin_url('edit.php?post_type=view') . '">' . __('Create <strong>Views</strong> for content lists &raquo;',
                        'wpcf') . '</a></p>';
    } else {
        $output .= '<p>' . __('Views makes it easy to display custom posts and fields on your site.',
                        'wpcf') . '</p>'
                . '<p>' . __('Learn how to:', 'wpcf') . '</p>'
                . '<ul>'
                . '<li><a href="http://wp-types.com/documentation/user-guides/view-templates/?utm_source=types&utm_medium=plugin&utm_term=view-templates&utm_content=post-edit-sidebar&utm_campaign=types" target="_blank">'
                . __('Display custom fields in templates &raquo;', 'wpcf')
                . '</a></li>'
                . '<li><a href="http://wp-types.com/documentation/user-guides/views/?utm_source=types&utm_medium=plugin&utm_term=views&utm_content=post-edit-sidebar&utm_campaign=types" target="_blank">'
                . __('Show custom content anywhere in the site &raquo;', 'wpcf')
                . '</a></li>'
                . '</ul>'
                . '<p style="margin-top:2em;"><a class="button button-highlighted" href="http://wp-types.com/buy/?utm_source=types&utm_medium=plugin&utm_term=buy&utm_content=post-edit-sidebar&utm_campaign=types" target="_blank">'
                . __('Buy Views ($49)', 'wpcf')
                . '</a></p>'
                . '<p style="font-size: 90%;">'
                . __('Risk free - 30 days money back guarantee', 'wpcf')
                . '</p>';
    }
    echo $output;
}
