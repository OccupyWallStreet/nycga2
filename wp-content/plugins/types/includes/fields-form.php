<?php
/*
 * Fields and groups form functions.
 */
require_once WPCF_EMBEDDED_ABSPATH . '/classes/validate.php';
require_once WPCF_ABSPATH . '/includes/conditional-display.php';

/**
 * Saves fields and groups.
 * 
 * If field name is changed in specific group - new one will be created,
 * otherwise old one will be updated and will appear in that way in other grups.
 * 
 * @return type 
 */
function wpcf_admin_save_fields_groups_submit($form) {
    if (!isset($_POST['wpcf']['group']['name'])) {
        return false;
    }
    $_POST['wpcf']['group']['name'] = trim($_POST['wpcf']['group']['name']);

    $_POST['wpcf']['group'] = apply_filters('wpcf_group_pre_save',
            $_POST['wpcf']['group']);

    global $wpdb;

    $new_group = false;

    $group_slug = $_POST['wpcf']['group']['slug'] = sanitize_title($_POST['wpcf']['group']['name']);

    // Basic check
    if (isset($_REQUEST['group_id'])) {
        // Check if group exists
        $post = get_post($_REQUEST['group_id']);
        // Name changed
        if (strtolower($_POST['wpcf']['group']['name']) != strtolower($post->post_title)) {
            // Check if already exists
            $exists = get_page_by_title($_POST['wpcf']['group']['name'],
                    'OBJECT', 'wp-types-group');
            if (!empty($exists)) {
                $form->triggerError();
                wpcf_admin_message(sprintf(__("A group by name <em>%s</em> already exists. Please use a different name and save again.",
                                        'wpcf'), $_POST['wpcf']['group']['name']),
                        'error');
                return $form;
            }
        }
        if (empty($post) || $post->post_type != 'wp-types-group') {
            $form->triggerError();
            wpcf_admin_message(sprintf(__("Wrong group ID %d", 'wpcf'),
                            intval($_REQUEST['group_id'])), 'error');
            return $form;
        }
        $group_id = $post->ID;
    } else {
        $new_group = true;
        // Check if already exists
        $exists = get_page_by_title($_POST['wpcf']['group']['name'], 'OBJECT',
                'wp-types-group');
        if (!empty($exists)) {
            $form->triggerError();
            wpcf_admin_message(sprintf(__("A group by name <em>%s</em> already exists. Please use a different name and save again.",
                                    'wpcf'), $_POST['wpcf']['group']['name']),
                    'error');
            return $form;
        }
    }

    // Save fields for future use
    $fields = array();
    if (!empty($_POST['wpcf']['fields'])) {
        // Before anything - search unallowed characters
        foreach ($_POST['wpcf']['fields'] as $key => $field) {
            if ((empty($field['slug']) && preg_match('#[^a-zA-Z0-9\s\_\-]#',
                            $field['name']))
                    || (!empty($field['slug']) && preg_match('#[^a-zA-Z0-9\s\_\-]#',
                            $field['slug']))) {
                $form->triggerError();
                wpcf_admin_message(sprintf(__('Field slugs cannot contain non-English characters. Please edit this field name %s and save again.',
                                        'wpcf'), $field['name']), 'error');
                return $form;
            }
        }
        foreach ($_POST['wpcf']['fields'] as $key => $field) {
            $field = apply_filters('wpcf_field_pre_save', $field);
            if (!empty($field['is_new'])) {
                // Check name and slug
                if (wpcf_types_cf_under_control('check_exists',
                                sanitize_title($field['name']))) {
                    $form->triggerError();
                    wpcf_admin_message(sprintf(__('Field with name "%s" already exists',
                                            'wpcf'), $field['name']), 'error');
                    return $form;
                }
                if (isset($field['slug']) && wpcf_types_cf_under_control('check_exists',
                                sanitize_title($field['slug']))) {
                    $form->triggerError();
                    wpcf_admin_message(sprintf(__('Field with slug "%s" already exists',
                                            'wpcf'), $field['slug']), 'error');
                    return $form;
                }
            }
            // Field ID and slug are same thing
            $field_id = wpcf_admin_fields_save_field($field);
            if (!empty($field_id)) {
                $fields[] = $field_id;
            }
            // WPML
            if (function_exists('wpml_cf_translation_preferences_store')) {
                $wpml_save_cf = wpml_cf_translation_preferences_store($key,
                        wpcf_types_get_meta_prefix(wpcf_admin_fields_get_field($field_id)) . $field_id);
            }
        }
    }

    // Save group
    $post_types = isset($_POST['wpcf']['group']['supports']) ? $_POST['wpcf']['group']['supports'] : array();
    $taxonomies_post = isset($_POST['wpcf']['group']['taxonomies']) ? $_POST['wpcf']['group']['taxonomies'] : array();
    $terms = array();
    foreach ($taxonomies_post as $taxonomy) {
        foreach ($taxonomy as $tax => $term) {
            $terms[] = $term;
        }
    }
    // Rename if needed
    if (isset($_REQUEST['group_id'])) {
        $_POST['wpcf']['group']['id'] = $_REQUEST['group_id'];
    }

    $group_id = wpcf_admin_fields_save_group($_POST['wpcf']['group']);
    $_POST['wpcf']['group']['id'] = $group_id;

    // Set open fieldsets
    if ($new_group && !empty($group_id)) {
        $open_fieldsets = get_user_meta(get_current_user_id(),
                'wpcf-group-form-toggle', true);
        if (isset($open_fieldsets[-1])) {
            $open_fieldsets[$group_id] = $open_fieldsets[-1];
            unset($open_fieldsets[-1]);
            update_user_meta(get_current_user_id(), 'wpcf-group-form-toggle',
                    $open_fieldsets);
        }
    }

    // Rest of processes
    if (!empty($group_id)) {
        wpcf_admin_fields_save_group_fields($group_id, $fields);
        wpcf_admin_fields_save_group_post_types($group_id, $post_types);
        wpcf_admin_fields_save_group_terms($group_id, $terms);
        if (empty($_POST['wpcf']['group']['templates'])) {
            $_POST['wpcf']['group']['templates'] = array();
        }
        wpcf_admin_fields_save_group_templates($group_id,
                $_POST['wpcf']['group']['templates']);
        $_POST['wpcf']['group']['fields'] = isset($_POST['wpcf']['fields']) ? $_POST['wpcf']['fields'] : array();
        do_action('wpcf_save_group', $_POST['wpcf']['group']);
        wpcf_admin_message_store(__('Group saved', 'wpcf'));
        wp_redirect(admin_url('admin.php?page=wpcf-edit&group_id=' . $group_id));
        die();
    } else {
        wpcf_admin_message_store(__('Error saving group', 'wpcf'), 'error');
    }
}

/**
 * Generates form data.
 */
function wpcf_admin_fields_form() {
    wpcf_admin_add_js_settings('wpcf_nonce_toggle_group',
            '\'' . wp_create_nonce('group_form_collapsed') . '\'');
    wpcf_admin_add_js_settings('wpcf_nonce_toggle_fieldset',
            '\'' . wp_create_nonce('form_fieldset_toggle') . '\'');
    $default = array();

    // If it's update, get data
    $update = false;
    if (isset($_REQUEST['group_id'])) {
        $update = wpcf_admin_fields_get_group(intval($_REQUEST['group_id']));
        if (empty($update)) {
            $update = false;
            wpcf_admin_message(sprintf(__("Group with ID %d do not exist",
                                    'wpcf'), intval($_REQUEST['group_id'])));
        } else {
            $update['fields'] = wpcf_admin_fields_get_fields_by_group($_REQUEST['group_id']);
            $update['post_types'] = wpcf_admin_get_post_types_by_group($_REQUEST['group_id']);
            $update['taxonomies'] = wpcf_admin_get_taxonomies_by_group($_REQUEST['group_id']);
            $update['templates'] = wpcf_admin_get_templates_by_group($_REQUEST['group_id']);
        }
    }

    $form = array();
    $form['#form']['callback'] = array('wpcf_admin_save_fields_groups_submit');

    // Form sidebars

    $form['open-sidebar'] = array(
        '#type' => 'markup',
        '#markup' => '<div class="wpcf-form-fields-align-right">',
    );
    // Set help icon
    $form['help-icon'] = array(
        '#type' => 'markup',
        '#markup' => '<div class="wpcf-admin-fields-help"><img src="' . WPCF_EMBEDDED_RELPATH
        . '/common/res/images/question.png" style="position:relative;top:2px;" />&nbsp;<a href="http://wp-types.com/documentation/user-guides/using-custom-fields/" target="_blank">'
        . __('Custom fields help', 'wpcf') . '</a></div>',
    );
    $form['submit2'] = array(
        '#type' => 'submit',
        '#name' => 'save',
        '#value' => __('Save', 'wpcf'),
        '#attributes' => array('class' => 'button-primary'),
    );
    $form['fields'] = array(
        '#type' => 'fieldset',
        '#title' => __('Available fields', 'wpcf'),
    );

    // Get field types
    $fields_registered = wpcf_admin_fields_get_available_types();
    foreach ($fields_registered as $filename => $data) {
        $form['fields'][basename($filename, '.php')] = array(
            '#type' => 'markup',
            '#markup' => '<a href="' . admin_url('admin-ajax.php'
                    . '?action=wpcf_ajax&amp;wpcf_action=fields_insert'
                    . '&amp;field=' . basename($filename, '.php'))
            . '&amp;_wpnonce=' . wp_create_nonce('fields_insert') . '" '
            . 'class="wpcf-fields-add-ajax-link button-secondary">' . $data['title'] . '</a> ',
        );
        // Process JS
        if (!empty($data['group_form_js'])) {
            foreach ($data['group_form_js'] as $handle => $script) {
                if (isset($script['inline'])) {
                    add_action('admin_footer', $script['inline']);
                    continue;
                }
                $deps = !empty($script['deps']) ? $script['deps'] : array();
                $in_footer = !empty($script['in_footer']) ? $script['in_footer'] : false;
                wp_register_script($handle, $script['src'], $deps, WPCF_VERSION,
                        $in_footer);
                wp_enqueue_script($handle);
            }
        }

        // Process CSS
        if (!empty($data['group_form_css'])) {
            foreach ($data['group_form_css'] as $handle => $script) {
                if (isset($script['src'])) {
                    $deps = !empty($script['deps']) ? $script['deps'] : array();
                    wp_enqueue_style($handle, $script['src'], $deps,
                            WPCF_VERSION);
                } else if (isset($script['inline'])) {
                    add_action('admin_head', $script['inline']);
                }
            }
        }
    }

    // Link
//    $form['fields-link-tutorial-1'] = array(
//        '#type' => 'markup',
//        '#markup' => '<strong>' . __('Looking for repeater fields?', 'wpcf')
//        . '</strong><br />'
//        . sprintf(__('Learn about Types %sfield tables%s', 'wpcf'),
//                '<a href="http://wp-types.com/documentation/user-guides/bulk-content-editing-with-fields-table/" target="_blank">',
//                ' &raquo;</a>'),
//    );
    // Get fields created by user
    $fields = wpcf_admin_fields_get_fields(true, true);
    if (!empty($fields)) {
        $form['fields-existing'] = array(
            '#type' => 'fieldset',
            '#title' => __('User created fields', 'wpcf'),
            '#id' => 'wpcf-form-groups-user-fields',
        );
        foreach ($fields as $key => $field) {
            if (isset($update['fields']) && array_key_exists($key,
                            $update['fields'])) {
                continue;
            }
            if (!empty($field['data']['removed_from_history'])) {
                continue;
            }
            $form['fields-existing'][$key] = array(
                '#type' => 'markup',
                '#markup' => '<div id="wpcf-user-created-fields-wrapper-' . $field['id'] . '" style="float:left; margin-right: 10px;"><a href="' . admin_url('admin-ajax.php'
                        . '?action=wpcf_ajax'
                        . '&amp;wpcf_action=fields_insert_existing'
                        . '&amp;field=' . $field['id']) . '&amp;_wpnonce='
                . wp_create_nonce('fields_insert_existing') . '" '
                . 'class="wpcf-fields-add-ajax-link button-secondary" onclick="jQuery(this).parent().fadeOut();">'
                . htmlspecialchars(stripslashes($field['name'])) . '</a>'
                . '<a href="' . admin_url('admin-ajax.php'
                        . '?action=wpcf_ajax'
                        . '&amp;wpcf_action=remove_from_history'
                        . '&amp;field_id=' . $field['id']) . '&amp;_wpnonce='
                . wp_create_nonce('remove_from_history') . '&amp;wpcf_warning='
                . sprintf(__('Are you sure that you want to remove field %s from history?',
                                'wpcf'),
                        htmlspecialchars(stripslashes($field['name'])))
                . '&amp;wpcf_ajax_update=wpcf-user-created-fields-wrapper-'
                . $field['id'] . '" title="'
                . sprintf(__('Remove field %s', 'wpcf'),
                        htmlspecialchars(stripslashes($field['name'])))
                . '" class="wpcf-ajax-link"><img src="'
                . WPCF_RES_RELPATH
                . '/images/delete-2.png" style="postion:absolute;margin-top:5px;margin-left:-4px;" /></a></div>',
            );
        }
    }
    $form['close-sidebar'] = array(
        '#type' => 'markup',
        '#markup' => '</div>',
    );

    // Group data

    $form['open-main'] = array(
        '#type' => 'markup',
        '#markup' => '<div id="wpcf-form-fields-main">',
    );

    $form['title'] = array(
        '#type' => 'textfield',
        '#name' => 'wpcf[group][name]',
        '#id' => 'wpcf-group-name',
        '#value' => $update ? $update['name'] : __('Enter group title', 'wpcf'),
        '#inline' => true,
        '#attributes' => array('style' => 'width:100%;margin-bottom:10px;'),
        '#validate' => array(
            'required' => array(
                'value' => true,
            ),
        )
    );
    if (!$update) {
        $form['title']['#attributes']['onfocus'] = 'if (jQuery(this).val() == \'' . __('Enter group title',
                        'wpcf') . '\') { jQuery(this).val(\'\'); }';
        $form['title']['#attributes']['onblur'] = 'if (jQuery(this).val() == \'\') { jQuery(this).val(\'' . __('Enter group title',
                        'wpcf') . '\') }';
    }
    $form['description'] = array(
        '#type' => 'textarea',
        '#id' => 'wpcf-group-description',
        '#name' => 'wpcf[group][description]',
        '#value' => $update ? $update['description'] : __('Enter a description for this group',
                        'wpcf'),
    );
    if (!$update) {
        $form['description']['#attributes']['onfocus'] = 'if (jQuery(this).val() == \''
                . __('Enter a description for this group', 'wpcf') . '\') { jQuery(this).val(\'\'); }';
        $form['description']['#attributes']['onblur'] = 'if (jQuery(this).val() == \'\') { jQuery(this).val(\''
                . __('Enter a description for this group', 'wpcf') . '\') }';
    }

    // Support post types and taxonomies

    $post_types = get_post_types('', 'objects');
    $options = array();
    $post_types_currently_supported = array();
    $form_types = array();

    foreach ($post_types as $post_type_slug => $post_type) {
        if (in_array($post_type_slug,
                        array('attachment', 'revision', 'nav_menu_item',
                    'view', 'view-template'))
                || !$post_type->show_ui) {
            continue;
        }
        $options[$post_type_slug]['#name'] = 'wpcf[group][supports][' . $post_type_slug . ']';
        $options[$post_type_slug]['#title'] = $post_type->label;
        $options[$post_type_slug]['#default_value'] = ($update && !empty($update['post_types']) && in_array($post_type_slug,
                        $update['post_types'])) ? 1 : 0;
        $options[$post_type_slug]['#value'] = $post_type_slug;
        $options[$post_type_slug]['#inline'] = TRUE;
        $options[$post_type_slug]['#suffix'] = '<br />';
        $options[$post_type_slug]['#id'] = 'wpcf-form-groups-support-post-type-' . $post_type_slug;
        $options[$post_type_slug]['#attributes'] = array('class' => 'wpcf-form-groups-support-post-type');
        if ($update && !empty($update['post_types']) && in_array($post_type_slug,
                        $update['post_types'])) {
            $post_types_currently_supported[] = $post_type->label;
        }
    }

    if (empty($post_types_currently_supported)) {
        $post_types_currently_supported[] = __('Displayed on all content types',
                'wpcf');
    }

    $post_types_no_currently_supported_txt = __('Post Types:', 'wpcf') . ' '
            . __('Displayed on all content types', 'wpcf');

    $form_types = array(
        '#type' => 'checkboxes',
        '#options' => $options,
        '#name' => 'wpcf[group][supports]',
        '#inline' => true,
        '#before' => '<span id="wpcf-group-form-update-types-ajax-response"'
        . ' style="font-style:italic;font-weight:bold;display:inline-block;">'
        . __('Post Types:', 'wpcf') . ' ' . implode(', ',
                $post_types_currently_supported) . '</span>'
        . '&nbsp;&nbsp;<a href="javascript:void(0);" style="line-height: 30px;"'
        . ' class="button-secondary" onclick="'
        . 'window.wpcfPostTypesText = new Array(); window.wpcfFormGroupsSupportPostTypesState = new Array(); '
        . 'jQuery(this).next().slideToggle()'
        . '.find(\'.checkbox\').each(function(index){'
        . 'if (jQuery(this).is(\':checked\')) { '
        . 'window.wpcfPostTypesText.push(jQuery(this).next().html()); '
        . 'window.wpcfFormGroupsSupportPostTypesState.push(jQuery(this).attr(\'id\'));'
        . '}'
        . '});'
        . ' jQuery(this).css(\'visibility\', \'hidden\');">'
        . __('Edit', 'wpcf') . '</a>' . '<div class="hidden" id="wpcf-form-fields-post_types">',
        '#after' => '<a href="javascript:void(0);" style="line-height: 35px;" '
        . 'class="button-primary wpcf-groups-form-ajax-update-post-types-ok"'
        . ' onclick="window.wpcfPostTypesText = new Array(); window.wpcfFormGroupsSupportPostTypesState = new Array(); '
        . 'jQuery(this).parent().slideUp().find(\'.checkbox\').each(function(index){'
        . 'if (jQuery(this).is(\':checked\')) { '
        . 'window.wpcfPostTypesText.push(jQuery(this).next().html()); '
        . 'window.wpcfFormGroupsSupportPostTypesState.push(jQuery(this).attr(\'id\'));'
        . '}'
        . '});'
        . 'if (window.wpcfPostTypesText.length < 1) { '
        . 'jQuery(\'#wpcf-group-form-update-types-ajax-response\').html(\''
        . $post_types_no_currently_supported_txt . '\'); '
        . '} else { jQuery(\'#wpcf-group-form-update-types-ajax-response\').html(\''
        . __('Post Types:', 'wpcf') . ' \'+wpcfPostTypesText.join(\', \'));}'
        . ' jQuery(this).parent().parent().children(\'a\').css(\'visibility\', \'visible\');'
        . '">'
        . __('OK', 'wpcf') . '</a>&nbsp;'
        . '<a href="javascript:void(0);" style="line-height: 35px;" '
        . 'class="button-secondary wpcf-groups-form-ajax-update-post-types-cancel"'
        . ' onclick="jQuery(this).parent().slideUp().find(\'input\').removeAttr(\'checked\');'
        . 'if (window.wpcfFormGroupsSupportPostTypesState.length > 0) { '
        . 'for (var element in window.wpcfFormGroupsSupportPostTypesState) { '
        . 'jQuery(\'#\'+window.wpcfFormGroupsSupportPostTypesState[element]).attr(\'checked\', \'checked\'); }}'
        . 'jQuery(\'#wpcf-group-form-update-types-ajax-response\').html(\''
        . __('Post Types:', 'wpcf') . ' \'+window.wpcfPostTypesText.join(\', \'));'
        . ' jQuery(this).parent().parent().children(\'a\').css(\'visibility\', \'visible\');'
        . '">'
        . __('Cancel', 'wpcf') . '</a>' . '</div></div><br />',
    );

    $taxonomies = get_taxonomies('', 'objects');
    $options = array();
    $tax_currently_supported = array();
    $form_tax = array();
    $form_tax_single = array();

    foreach ($taxonomies as $category_slug => $category) {
        if ($category_slug == 'nav_menu' || $category_slug == 'link_category'
                || $category_slug == 'post_format') {
            continue;
        }
        $terms = get_terms($category_slug, array('hide_empty' => false));
        if (!empty($terms)) {
            $options = array();
            $add_title = '<div class="taxonomy-title">' . $category->labels->name . '</div>';
            $title = '';
            foreach ($terms as $term) {
                $checked = 0;
                if ($update && !empty($update['taxonomies']) && array_key_exists($category_slug,
                                $update['taxonomies'])) {
                    if (array_key_exists($term->term_id,
                                    $update['taxonomies'][$category_slug])) {
                        $checked = 1;
                        $tax_currently_supported[$term->term_id] = $title . $term->name;
                        $title = '';
                    }
                }
                $options[$term->term_id]['#name'] = 'wpcf[group][taxonomies]['
                        . $category_slug . '][' . $term->term_id . ']';
                $options[$term->term_id]['#title'] = $term->name;
                $options[$term->term_id]['#default_value'] = $checked;
                $options[$term->term_id]['#value'] = $term->term_id;
                $options[$term->term_id]['#inline'] = true;
                $options[$term->term_id]['#prefix'] = $add_title;
                $options[$term->term_id]['#suffix'] = '<br />';
                $options[$term->term_id]['#id'] = 'wpcf-form-groups-support-tax-' . $term->term_id;
                $options[$term->term_id]['#attributes'] = array('class' => 'wpcf-form-groups-support-tax');
                $add_title = '';
            }
            $form_tax_single['taxonomies-' . $category_slug] = array(
                '#type' => 'checkboxes',
                '#options' => $options,
                '#name' => 'wpcf[group][taxonomies][' . $category_slug . ']',
                '#suffix' => '<br />',
                '#inline' => true,
            );
        }
    }

    if (empty($tax_currently_supported)) {
        $tax_currently_supported[] = __('Not Selected', 'wpcf');
    }

    $tax_no_currently_supported_txt = __('Terms:', 'wpcf') . ' '
            . __('Not Selected', 'wpcf');

    $form_tax['taxonomies-open'] = array(
        '#type' => 'markup',
        '#markup' => '<span id="wpcf-group-form-update-tax-ajax-response" '
        . 'style="font-style:italic;font-weight:bold;display:inline-block;">'
        . __('Terms:', 'wpcf') . ' ' . implode(', ', $tax_currently_supported) . '</span>'
        . '&nbsp;&nbsp;<a href="javascript:void(0);" style="line-height: 30px;" '
        . 'class="button-secondary" onclick="'
        . 'window.wpcfTaxText = new Array(); window.wpcfFormGroupsSupportTaxState = new Array(); '
        . 'jQuery(this).next().slideToggle()'
        . '.find(\'.checkbox\').each(function(index){'
        . 'if (jQuery(this).is(\':checked\')) { '
        . 'window.wpcfTaxText.push(jQuery(this).next().html()); '
        . 'window.wpcfFormGroupsSupportTaxState.push(jQuery(this).attr(\'id\'));'
        . '}'
        . '});'
        . ' jQuery(this).css(\'visibility\', \'hidden\');">'
        . __('Edit', 'wpcf') . '</a>' . '<div class="hidden" id="wpcf-form-fields-taxonomies">',
    );

    $form_tax = $form_tax + $form_tax_single;

    $form_tax['taxonomies-close'] = array(
        '#type' => 'markup',
        '#markup' => '<a href="javascript:void(0);" style="line-height: 35px;" '
        . 'class="button-primary wpcf-groups-form-ajax-update-tax-ok"'
        . ' onclick="window.wpcfTaxText = new Array(); window.wpcfFormGroupsSupportTaxState = new Array(); '
        . 'jQuery(this).parent().slideUp().find(\'.checkbox\').each(function(index){'
        . 'if (jQuery(this).is(\':checked\')) { '
        . 'window.wpcfTaxText.push(jQuery(this).next().html()); '
        . 'window.wpcfFormGroupsSupportTaxState.push(jQuery(this).attr(\'id\'));'
        . '}'
        . '});'
        . 'if (window.wpcfTaxText.length < 1) { '
        . 'jQuery(\'#wpcf-group-form-update-tax-ajax-response\').html(\''
        . $tax_no_currently_supported_txt . '\'); '
        . '} else { jQuery(\'#wpcf-group-form-update-tax-ajax-response\').html(\''
        . __('Terms:', 'wpcf') . ' \'+wpcfTaxText.join(\', \'));'
        . '}'
        . ' jQuery(this).parent().parent().children(\'a\').css(\'visibility\', \'visible\');'
        . '">'
        . __('OK', 'wpcf') . '</a>&nbsp;'
        . '<a href="javascript:void(0);" style="line-height: 35px;" '
        . 'class="button-secondary wpcf-groups-form-ajax-update-tax-cancel"'
        . ' onclick="jQuery(this).parent().slideUp().find(\'input\').removeAttr(\'checked\');'
        . 'if (window.wpcfFormGroupsSupportTaxState.length > 0) { '
        . 'for (var element in window.wpcfFormGroupsSupportTaxState) { '
        . 'jQuery(\'#\'+window.wpcfFormGroupsSupportTaxState[element]).attr(\'checked\', \'checked\'); }}'
        . 'jQuery(\'#wpcf-group-form-update-tax-ajax-response\').html(\'' . __('Terms:',
                'wpcf')
        . ' \'+window.wpcfTaxText.join(\', \'));'
        . ' jQuery(this).parent().parent().children(\'a\').css(\'visibility\', \'visible\');'
        . '">'
        . __('Cancel', 'wpcf') . '</a>' . '</div><br />',
    );

    $form['supports-table-open'] = array(
        '#type' => 'markup',
        '#markup' => '<table class="widefat"><thead><tr><th>'
        . __('Where to display this group', 'wpcf')
        . '</th></tr></thead><tbody><tr><td>'
        . __('Each custom fields group can display on different content types or different taxonomy.',
                'wpcf') . '<br />',
    );

    $form['types'] = $form_types;
    $form = $form + $form_tax;

    // Choose templates
    $templates = get_page_templates();
    $templates_views = get_posts('post_type=view-template&numberposts=-1&status=publish');

    $options = array();
    $options['default-template'] = array(
        '#title' => __('Default Template'),
        '#default_value' => !empty($update['templates']) && in_array('default',
                $update['templates']),
        '#name' => 'wpcf[group][templates][]',
        '#value' => 'default',
        '#inline' => true,
        '#after' => '<br />',
    );
    foreach ($templates as $template_name => $template_filename) {
        $options[$template_filename] = array(
            '#title' => $template_name,
            '#default_value' => !empty($update['templates']) && in_array($template_filename,
                    $update['templates']),
            '#name' => 'wpcf[group][templates][]',
            '#value' => $template_filename,
            '#inline' => true,
            '#after' => '<br />',
        );
    }
    foreach ($templates_views as $template_view) {
        $options[$template_view->post_name] = array(
            '#title' => 'View Template ' . $template_view->post_title,
            '#default_value' => !empty($update['templates']) && in_array($template_view->ID,
                    $update['templates']),
            '#name' => 'wpcf[group][templates][]',
            '#value' => $template_view->ID,
            '#inline' => true,
            '#after' => '<br />',
        );
        $templates_view_list_text[$template_view->ID] = $template_view->post_title;
    }
    $text = '';
    $empty_txt = __('Not Selected', 'wpcf');
    if (!empty($update['templates'])) {
        $text = array();
        $templates = array_flip($templates);
        foreach ($update['templates'] as $template) {
            if ($template == 'default') {
                $template = __('Default Template');
            } else if (strpos($template, '.php') !== false) {
                $template = $templates[$template];
            } else {
                $template = 'View Template ' . $templates_view_list_text[$template];
            }
            $text[] = $template;
        }
        $text = implode(', ', $text);
    } else {
        $text = __('Not Selected', 'wpcf');
    }

    $form['templates'] = array(
        '#type' => 'checkboxes',
        '#name' => 'wpcf[group][templates]',
        '#options' => $options,
        '#inline' => true,
    );
    $form['templates'] = wpcf_admin_fields_form_nested_elements('templates',
            $form['templates'], __('Content templates:', 'wpcf'), $text,
            $empty_txt);

    $count = 0;
    $count +=!empty($update['post_types']) ? 1 : 0;
    $count +=!empty($update['taxonomies']) ? 1 : 0;
    $count +=!empty($update['templates']) ? 1 : 0;
    $display = $count > 1 ? '' : ' style="display:none;"';
    $form['filters_association'] = array(
        '#type' => 'radios',
        '#name' => 'wpcf[group][filters_association]',
        '#id' => 'wpcf-fields-form-filters-association',
        '#options' => array(
            __('Display this group when ANY of the above conditions is met',
                    'wpcf') => 'any',
            __('Display this group when ALL the above conditions is met', 'wpcf') => 'all',
        ),
        '#default_value' => !empty($update['filters_association']) ? $update['filters_association'] : 'any',
        '#inline' => true,
        '#before' => '<div id="wpcf-fields-form-filters-association-form"' . $display . '>',
        '#after' => '<div id="wpcf-fields-form-filters-association-summary" style="margin-top:10px;font-style:italic;"></div></div>',
    );
    wpcf_admin_add_js_settings('wpcf_filters_association_or',
            '\'' . __('This group will appear on %pt% edit pages where content belongs to taxonomy: %tx% or View Template is: %vt%',
                    'wpcf') . '\'');
    wpcf_admin_add_js_settings('wpcf_filters_association_and',
            '\'' . __('This group will appear on %pt% edit pages where content belongs to taxonomy: %tx% and View Template is: %vt%',
                    'wpcf') . '\'');
    wpcf_admin_add_js_settings('wpcf_filters_association_all_pages',
            '\'' . __('all', 'wpcf') . '\'');
    wpcf_admin_add_js_settings('wpcf_filters_association_all_taxonomies',
            '\'' . __('any', 'wpcf') . '\'');
    wpcf_admin_add_js_settings('wpcf_filters_association_all_templates',
            '\'' . __('any', 'wpcf') . '\'');

    $additional_filters = apply_filters('wpcf_fields_form_additional_filters',
            array(), $update);
    $form = $form + $additional_filters;

    $form['supports-table-close'] = array(
        '#type' => 'markup',
        '#markup' => '</td></tr></tbody></table><br />',
    );

    // Group fields

    $form['fields_title'] = array(
        '#type' => 'markup',
        '#markup' => '<h2>' . __('Fields', 'wpcf') . '</h2>',
    );
    $show_under_title = true;

    $form['ajax-response-open'] = array(
        '#type' => 'markup',
        '#markup' => '<div id="wpcf-fields-sortable" class="ui-sortable">',
    );

    // If it's update, display existing fields
    $existing_fields = array();
    if ($update && isset($update['fields'])) {
        foreach ($update['fields'] as $slug => $field) {
            $field['submitted_key'] = $slug;
            $field['group_id'] = $update['id'];
            $form_field = wpcf_fields_get_field_form_data($field['type'], $field);
            if (is_array($form_field)) {
                $form['draggable-open-' . rand()] = array(
                    '#type' => 'markup',
                    '#markup' => '<div class="ui-draggable">'
                );
                $form = $form + $form_field;
                $form['draggable-close-' . rand()] = array(
                    '#type' => 'markup',
                    '#markup' => '</div>'
                );
            }
            $existing_fields[] = $slug;
            $show_under_title = false;
        }
    }
    // Any new fields submitted but failed? (Don't double it)
    if (!empty($_POST['wpcf']['fields'])) {
        foreach ($_POST['wpcf']['fields'] as $key => $field) {
            if (in_array($key, $existing_fields)) {
                continue;
            }
            $field['submitted_key'] = $key;
            $form_field = wpcf_fields_get_field_form_data($field['type'], $field);
            if (is_array($form_field)) {
                $form['draggable-open-' . rand()] = array(
                    '#type' => 'markup',
                    '#markup' => '<div class="ui-draggable">'
                );
                $form = $form + $form_field;
                $form['draggable-close-' . rand()] = array(
                    '#type' => 'markup',
                    '#markup' => '</div>'
                );
            }
        }
        $show_under_title = false;
    }
    $form['ajax-response-close'] = array(
        '#type' => 'markup',
        '#markup' => '</div>' . '<div id="wpcf-ajax-response"></div>',
    );

    if ($show_under_title) {
        $form['fields_title']['#markup'] = $form['fields_title']['#markup']
                . '<div id="wpcf-fields-under-title">'
                . __('There are no fields in this group. To add a field, click on the field buttons at the right.',
                        'wpcf')
                . '</div>';
    }

    // If update, create ID field
    if ($update) {
        $form['group_id'] = array(
            '#type' => 'hidden',
            '#name' => 'group_id',
            '#value' => $update['id'],
            '#forced_value' => true,
        );
    }

    $form['submit'] = array(
        '#type' => 'submit',
        '#name' => 'save',
        '#value' => __('Save', 'wpcf'),
        '#attributes' => array('class' => 'button-primary'),
    );

    // Close main div
    $form['close-sidebar'] = array(
        '#type' => 'markup',
        '#markup' => '</div>',
    );

    $form = apply_filters('wpcf_form_fields', $form);

    // Add JS settings
    wpcf_admin_add_js_settings('wpcfFormUniqueValuesCheckText',
            '\'' . __('Warning: same values selected', 'wpcf') . '\'');
    wpcf_admin_add_js_settings('wpcfFormUniqueNamesCheckText',
            '\'' . __('Warning: field name already used', 'wpcf') . '\'');
    wpcf_admin_add_js_settings('wpcfFormUniqueSlugsCheckText',
            '\'' . __('Warning: field slug already used', 'wpcf') . '\'');

    return $form;
}

/**
 * Dynamically adds new field on AJAX call.
 * 
 * @param type $form_data 
 */
function wpcf_fields_insert_ajax($form_data = array()) {
    echo wpcf_fields_get_field_form($_GET['field']);
}

/**
 * Dynamically adds existing field on AJAX call.
 * 
 * @param type $form_data 
 */
function wpcf_fields_insert_existing_ajax() {
    $field = wpcf_admin_fields_get_field($_GET['field'], false, true);
    if (!empty($field)) {
        echo wpcf_fields_get_field_form($field['type'], $field);
    } else {
        echo '<div>' . __("Requested field don't exist", 'wpcf') . '</div>';
    }
}

/**
 * Returns HTML formatted field form (draggable).
 * 
 * @param type $type
 * @param type $form_data
 * @return type 
 */
function wpcf_fields_get_field_form($type, $form_data = array()) {
    $form = wpcf_fields_get_field_form_data($type, $form_data);
    if ($form) {
        return '<div class="ui-draggable">'
                . wpcf_form_simple($form)
                . '</div>';
    }
    return '<div>' . __('Wrong field requested', 'wpcf') . '</div>';
}

/**
 * Processes field form data.
 * 
 * @param type $type
 * @param type $form_data
 * @return type 
 */
function wpcf_fields_get_field_form_data($type, $form_data = array()) {

    // Get field type data
    $field_data = wpcf_fields_type_action($type);

    if (!empty($field_data)) {
        $form = array();

        // Set right ID if existing field
        if (isset($form_data['submitted_key'])) {
            $id = $form_data['submitted_key'];
        } else {
            $id = $type . '-' . rand();
        }

        // Set remove link
        $remove_link = isset($form_data['group_id']) ? admin_url('admin-ajax.php?'
                        . 'wpcf_ajax_callback=wpcfFieldsFormDeleteElement&amp;wpcf_warning='
                        . __('Are you sure?', 'wpcf')
                        . '&amp;action=wpcf_ajax&amp;wpcf_action=remove_field_from_group'
                        . '&amp;group_id=' . intval($form_data['group_id'])
                        . '&amp;field_id=' . $form_data['id'])
                . '&amp;_wpnonce=' . wp_create_nonce('remove_field_from_group') : admin_url('admin-ajax.php?'
                        . 'wpcf_ajax_callback=wpcfFieldsFormDeleteElement&amp;wpcf_warning='
                        . __('Are you sure?', 'wpcf')
                        . '&amp;action=wpcf_ajax&amp;wpcf_action=remove_field_from_group')
                . '&amp;_wpnonce=' . wp_create_nonce('remove_field_from_group');

        // Set move button
        $form['wpcf-' . $id . '-control'] = array(
            '#type' => 'markup',
            '#markup' => '<img src="' . WPCF_RES_RELPATH
            . '/images/move.png" class="wpcf-fields-form-move-field" alt="'
            . __('Move this field', 'wpcf') . '" /><a href="'
            . $remove_link . '" '
            . 'class="wpcf-form-fields-delete wpcf-ajax-link">'
            . '<img src="' . WPCF_RES_RELPATH . '/images/delete-2.png" alt="'
            . __('Delete this field', 'wpcf') . '" /></a>',
        );

        // Set fieldset

        $collapsed = wpcf_admin_fields_form_fieldset_is_collapsed('fieldset-' . $id);
        // Set collapsed on AJAX call (insert)
        $collapsed = defined('DOING_AJAX') ? false : $collapsed;

        // Set title
        $title = !empty($form_data['name']) ? $form_data['name'] : __('Untitled',
                        'wpcf');
        $title = '<span class="wpcf-legend-update">' . $title . '</span> - '
                . sprintf(__('%s field', 'wpcf'), $field_data['title']);
        if (!empty($form_data['data']['conditional_display']['conditions'])) {
            $title .= ' ' . __('(conditional)', 'wpcf');
        }
        $form['wpcf-' . $id] = array(
            '#type' => 'fieldset',
            '#title' => $title,
            '#id' => 'fieldset-' . $id,
            '#collapsible' => true,
            '#collapsed' => $collapsed,
        );

        // Get init data
        $field_init_data = wpcf_fields_type_action($type);

        // See if field inherits some other
        $inherited_field_data = false;
        if (isset($field_init_data['inherited_field_type'])) {
            $inherited_field_data = wpcf_fields_type_action($field_init_data['inherited_field_type']);
        }

        $form_field = array();

        // Force name and description
        $form_field['name'] = array(
            '#type' => 'textfield',
            '#name' => 'name',
            '#attributes' => array('class' => 'wpcf-forms-set-legend wpcf-forms-field-name', 'style' => 'width:100%;margin:10px 0 10px 0;'),
            '#validate' => array('required' => array('value' => true)),
            '#inline' => true,
            '#value' => __('Enter field name', 'wpcf'),
        );
        if (empty($form_data['name'])) {
            $form_field['name']['#attributes']['onclick'] = 'if (jQuery(this).val() == \''
                    . __('Enter field name', 'wpcf') . '\') { jQuery(this).val(\'\'); }';
            $form_field['name']['#attributes']['onblur'] = 'if (jQuery(this).val() == \'\') { jQuery(this).val(\''
                    . __('Enter field name', 'wpcf') . '\') }';
        }
        $form_field['slug'] = array(
            '#type' => 'textfield',
            '#name' => 'slug',
            '#attributes' => array('class' => 'wpcf-forms-field-slug', 'style' => 'width:100%;margin:0 0 10px 0;'),
            '#validate' => array('nospecialchars' => array('value' => true)),
            '#inline' => true,
            '#value' => __('Enter field slug', 'wpcf'),
        );
        if (empty($form_data['slug'])) {
            $form_field['slug']['#attributes']['onclick'] = 'if (jQuery(this).val() == \''
                    . __('Enter field slug', 'wpcf') . '\') { jQuery(this).val(\'\'); }';
            $form_field['slug']['#attributes']['onblur'] = 'if (jQuery(this).val() == \'\') { jQuery(this).val(\''
                    . __('Enter field slug', 'wpcf') . '\') }';
        }

        // If insert form callback is not provided, use generic form data
        if (function_exists('wpcf_fields_' . $type . '_insert_form')) {
            $form_field_temp = call_user_func('wpcf_fields_' . $type
                    . '_insert_form', $form_data,
                    'wpcf[fields]['
                    . $id . ']');
            if (is_array($form_field_temp)) {
                unset($form_field_temp['name'], $form_field_temp['slug']);
                $form_field = $form_field + $form_field_temp;
            }
        }

        $form_field['description'] = array(
            '#type' => 'textarea',
            '#name' => 'description',
            '#attributes' => array('rows' => 5, 'cols' => 1, 'style' => 'margin:0 0 10px 0;'),
            '#inline' => true,
            '#value' => __('Describe this field', 'wpcf'),
        );
        if (empty($form_data['description'])) {
            $form_field['description']['#attributes']['onfocus'] = 'if (jQuery(this).val() == \''
                    . __('Describe this field', 'wpcf') . '\') { jQuery(this).val(\'\'); }';
            $form_field['description']['#attributes']['onblur'] = 'if (jQuery(this).val() == \'\') { jQuery(this).val(\''
                    . __('Describe this field', 'wpcf') . '\') }';
        }

        if (wpcf_admin_can_be_repetitive($type)) {
            $temp_warning_message = '';
//            $temp_warning_message .= '<div class="wpcf-message wpcf-cd-repetitive-warning wpcf-error"';
//            if (empty($form_data['data']['repetitive'])) {
//                $temp_warning_message .= ' style="display:none;"';
//            }
//            $temp_warning_message .= '><p>'
//                    . __('Since this field is repeating, you cannot use it to control the display of other fields.',
//                            'wpcf')
//                    . '</p></div>';
            $form_field['repetitive'] = array(
                '#type' => 'radios',
                '#name' => 'repetitive',
                '#title' => __('Single or repeating field?', 'wpcf'),
                '#options' => array(
                    'repeat' => array(
                        '#title' => __('Allow multiple-instances of this field',
                                'wpcf'),
                        '#value' => '1',
                        '#attributes' => array('onclick' => 'jQuery(this).parent().parent().find(\'.wpcf-cd-warning\').hide(); jQuery(this).parent().find(\'.wpcf-cd-repetitive-warning\').show();'),
                    ),
                    'norepeat' => array(
                        '#title' => __('This field can have only one value',
                                'wpcf'),
                        '#value' => '0',
                        '#attributes' => array('onclick' => 'jQuery(this).parent().parent().find(\'.wpcf-cd-warning\').show(); jQuery(this).parent().find(\'.wpcf-cd-repetitive-warning\').hide();'),
                    ),
                ),
                '#default_value' => isset($form_data['data']['repetitive']) ? $form_data['data']['repetitive'] : '0',
//                '#attributes' => array('onclick' => 'if (jQuery(this).is(\':checked\')) { jQuery(this).parent().find(\'.wpcf-cd-warning\').hide(); jQuery(this).parent().find(\'.wpcf-cd-repetitive-warning\').show(); } else { jQuery(this).parent().find(\'.wpcf-cd-warning\').show(); jQuery(this).parent().find(\'.wpcf-cd-repetitive-warning\').hide(); }'),
                '#after' => wpcf_admin_is_repetitive($form_data) ? '<div class="wpcf-message wpcf-cd-warning wpcf-error" style="display:none;"><p>' . __("There may be multiple instances of this field already. When you switch back to single-field mode, all values of this field will be updated when it's edited.",
                                'wpcf') . '</p></div>' . $temp_warning_message : $temp_warning_message,
            );
        }

        // Process all form fields
        foreach ($form_field as $k => $field) {
            $form['wpcf-' . $id][$k] = $field;
            // Check if nested
            if (isset($field['#name']) && strpos($field['#name'], '[') === false) {
                $form['wpcf-' . $id][$k]['#name'] = 'wpcf[fields]['
                        . $id . '][' . $field['#name'] . ']';
            } else if (isset($field['#name'])) {
                $form['wpcf-' . $id][$k]['#name'] = 'wpcf[fields]['
                        . $id . ']' . $field['#name'];
            }
            if (!isset($field['#id'])) {
                $form['wpcf-' . $id][$k]['#id'] = $type . '-'
                        . $field['#type'] . '-' . rand();
            }
            if (isset($field['#name']) && isset($form_data[$field['#name']])) {
                $form['wpcf-'
                        . $id][$k]['#value'] = $form_data[$field['#name']];
                $form['wpcf-'
                        . $id][$k]['#default_value'] = $form_data[$field['#name']];
                // Check if it's in 'data'
            } else if (isset($field['#name']) && isset($form_data['data'][$field['#name']])) {
                $form['wpcf-'
                        . $id][$k]['#value'] = $form_data['data'][$field['#name']];
                $form['wpcf-'
                        . $id][$k]['#default_value'] = $form_data['data'][$field['#name']];
            }
        }

        // Set type
        $form['wpcf-' . $id]['type'] = array(
            '#type' => 'hidden',
            '#name' => 'wpcf[fields][' . $id . '][type]',
            '#value' => $type,
            '#id' => $id . '-type',
        );

        // Add validation box
        $form_validate = wpcf_admin_fields_form_validation('wpcf[fields]['
                . $id . '][validate]', call_user_func('wpcf_fields_' . $type),
                $form_data);
        foreach ($form_validate as $k => $v) {
            $form['wpcf-' . $id][$k] = $v;
        }

        // WPML Translation Preferences
        if (function_exists('wpml_cf_translation_preferences')) {
            $custom_field = !empty($form_data['slug']) ? wpcf_types_get_meta_prefix($form_data) . $form_data['slug'] : false;
            $suppress_errors = $custom_field == false ? true : false;
            $translatable = array('textfield', 'textarea', 'wysiwyg');
            $action = in_array($type, $translatable) ? 'translate' : 'copy';
            $form['wpcf-' . $id]['wpml-preferences'] = array(
                '#type' => 'fieldset',
                '#title' => __('Translation preferences', 'wpcf'),
                '#collapsed' => true,
            );
            $form['wpcf-' . $id]['wpml-preferences']['form'] = array(
                '#type' => 'markup',
                '#markup' => wpml_cf_translation_preferences($id, $custom_field,
                        'wpcf', false, $action, false, $suppress_errors),
            );
        }

        if (empty($form_data) || isset($form_data['is_new'])) {
            $form['wpcf-' . $id]['is_new'] = array(
                '#type' => 'hidden',
                '#name' => 'wpcf[fields][' . $id . '][is_new]',
                '#value' => '1',
            );
        }
        $form_data['id'] = $id;
        $form['wpcf-' . $id] = apply_filters('wpcf_form_field',
                $form['wpcf-' . $id], $form_data);
        return $form;
    }
    return false;
}

/**
 * Adds validation box.
 * 
 * @param type $name
 * @param string $field
 * @param type $form_data
 * @return type 
 */
function wpcf_admin_fields_form_validation($name, $field, $form_data = array()) {
    $form = array();

    if (isset($field['validate'])) {

        $form['validate-table-open'] = array(
            '#type' => 'markup',
            '#markup' => '<table class="wpcf-fields-form-validate-table" '
            . 'cellspacing="0" cellpadding="0"><thead><tr><td>'
            . __('Validation', 'wpcf') . '</td><td>' . __('Error message',
                    'wpcf')
            . '</td></tr></thead><tbody>',
        );

        // Process methods
        foreach ($field['validate'] as $k => $method) {

            // Set additional method data
            if (is_array($method)) {
                $form_data['data']['validate'][$k]['method_data'] = $method;
                $method = $k;
            }

            if (!Wpcf_Validate::canValidate($method)
                    || !Wpcf_Validate::hasForm($method)) {
                continue;
            }

            $form['validate-tr-' . $method] = array(
                '#type' => 'markup',
                '#markup' => '<tr><td>',
            );

            // Get method form data
            if (Wpcf_Validate::canValidate($method)
                    && Wpcf_Validate::hasForm($method)) {

                $field['#name'] = $name . '[' . $method . ']';
                $form_validate = call_user_func_array(
                        array('Wpcf_Validate', $method . '_form'),
                        array(
                    $field,
                    isset($form_data['data']['validate'][$method]) ? $form_data['data']['validate'][$method] : array()
                        )
                );

                // Set unique IDs
                foreach ($form_validate as $key => $element) {
                    if (isset($element['#type'])) {
                        $form_validate[$key]['#id'] = $element['#type'] . '-'
                                . mt_rand();
                    }
                    if (isset($element['#name']) && strpos($element['#name'],
                                    '[message]') !== FALSE) {
                        $before = '</td><td>';
                        $after = '</td></tr>';
                        $form_validate[$key]['#before'] = isset($element['#before']) ? $element['#before'] . $before : $before;
                        $form_validate[$key]['#after'] = isset($element['#after']) ? $element['#after'] . $after : $after;
                    }
                }

                // Join
                $form = $form + $form_validate;
            }
        }
        $form['validate-table-close'] = array(
            '#type' => 'markup',
            '#markup' => '</tbody></table>',
        );
    }

    return $form;
}

/**
 * Adds JS validation script.
 */
function wpcf_admin_fields_form_js_validation() {
    wpcf_form_render_js_validation();
}

/**
 * Saves open fieldsets.
 * 
 * @param type $action
 * @param type $fieldset
 * @param type $group_id 
 */
function wpcf_admin_fields_form_save_open_fieldset($action, $fieldset,
        $group_id = false) {
    $data = get_user_meta(get_current_user_id(), 'wpcf-group-form-toggle', true);
    if ($group_id && $action == 'open') {
        $data[intval($group_id)][$fieldset] = 1;
    } else if ($group_id && $action == 'close') {
        unset($data[intval($group_id)][$fieldset]);
    } else if ($action == 'open') {
        $data[-1][$fieldset] = 1;
    } else if ($action == 'close') {
        unset($data[-1][$fieldset]);
    }
    update_user_meta(get_current_user_id(), 'wpcf-group-form-toggle', $data);
}

/**
 * Saves open fieldsets.
 * 
 * @param type $action
 * @param type $fieldset
 * @param type $group_id 
 */
function wpcf_admin_fields_form_fieldset_is_collapsed($fieldset) {
    if (isset($_REQUEST['group_id'])) {
        $group_id = intval($_REQUEST['group_id']);
    } else {
        $group_id = -1;
    }
    $data = get_user_meta(get_current_user_id(), 'wpcf-group-form-toggle', true);
    if (!isset($data[$group_id])) {
        return true;
    }
    return array_key_exists($fieldset, $data[$group_id]) ? false : true;
}

/**
 * Adds 'Edit' and 'Cancel' buttons, expandable div.
 * 
 * @param type $id
 * @param type $element
 * @param type $title
 * @param type $list
 * @param type $empty_txt
 * @return string 
 */
function wpcf_admin_fields_form_nested_elements($id, $element, $title, $list,
        $empty_txt) {
    $form = array();
    $form = $element;
    $id = strtolower(strval($id));

    $form['#before'] = '<span id="wpcf-group-form-update-' . $id . '-ajax-response"'
            . ' style="font-style:italic;font-weight:bold;display:inline-block;">'
            . esc_html($title) . ' ' . $list . '</span>'
            . '&nbsp;&nbsp;<a href="javascript:void(0);" style="line-height: 30px;"'
            . ' class="button-secondary" onclick="'
            . 'window.wpcf' . ucfirst($id) . 'Text = new Array(); window.wpcfFormGroups' . ucfirst($id) . 'State = new Array(); '
            . 'jQuery(this).next().slideToggle()'
            . '.find(\'.checkbox\').each(function(index){'
            . 'if (jQuery(this).is(\':checked\')) { '
            . 'window.wpcf' . ucfirst($id) . 'Text.push(jQuery(this).next().html()); '
            . 'window.wpcfFormGroups' . ucfirst($id) . 'State.push(jQuery(this).attr(\'id\'));'
            . '}'
            . '});'
            . ' jQuery(this).css(\'visibility\', \'hidden\');">'
            . __('Edit', 'wpcf') . '</a>' . '<div class="hidden" id="wpcf-form-fields-' . $id . '">';

    $form['#after'] = '<a href="javascript:void(0);" style="line-height: 35px;" '
            . 'class="button-primary wpcf-groups-form-ajax-update-' . $id . '-ok"'
            . ' onclick="window.wpcf' . ucfirst($id) . 'Text = new Array(); window.wpcfFormGroups' . ucfirst($id) . 'State = new Array(); '
            . 'jQuery(this).parent().slideUp().find(\'.checkbox\').each(function(index){'
            . 'if (jQuery(this).is(\':checked\')) { '
            . 'window.wpcf' . ucfirst($id) . 'Text.push(jQuery(this).next().html()); '
            . 'window.wpcfFormGroups' . ucfirst($id) . 'State.push(jQuery(this).attr(\'id\'));'
            . '}'
            . '});'
            . 'if (window.wpcf' . ucfirst($id) . 'Text.length < 1) { '
            . 'jQuery(\'#wpcf-group-form-update-' . $id . '-ajax-response\').html(\''
            . esc_html($title) . ' ' . esc_html($empty_txt) . '\'); '
            . '} else { jQuery(\'#wpcf-group-form-update-' . $id . '-ajax-response\').html(\''
            . esc_html($title) . ' \'+wpcf' . ucfirst($id) . 'Text.join(\', \'));}'
            . ' jQuery(this).parent().parent().children(\'a\').css(\'visibility\', \'visible\');'
            . '">'
            . __('OK', 'wpcf') . '</a>&nbsp;'
            . '<a href="javascript:void(0);" style="line-height: 35px;" '
            . 'class="button-secondary wpcf-groups-form-ajax-update-' . $id . '-cancel"'
            . ' onclick="jQuery(this).parent().slideUp().find(\'input\').removeAttr(\'checked\');'
            . 'if (window.wpcfFormGroups' . ucfirst($id) . 'State.length > 0) { '
            . 'for (var element in window.wpcfFormGroups' . ucfirst($id) . 'State) { '
            . 'jQuery(\'#\'+window.wpcfFormGroups' . ucfirst($id) . 'State[element]).attr(\'checked\', \'checked\'); }}'
            . 'jQuery(\'#wpcf-group-form-update-' . $id . '-ajax-response\').html(\''
            . esc_html($title) . ' \'+window.wpcf' . ucfirst($id) . 'Text.join(\', \'));'
            . ' jQuery(this).parent().parent().children(\'a\').css(\'visibility\', \'visible\');'
            . '">'
            . __('Cancel', 'wpcf') . '</a>' . '</div></div><br />';

    return $form;
}
