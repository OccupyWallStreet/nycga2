<?php

/**
 * Register data (called automatically).
 * 
 * @return type 
 */
function wpcf_fields_checkbox() {
    return array(
        'id' => 'wpcf-checkbox',
        'title' => __('Checkbox', 'wpcf'),
        'description' => __('Checkbox', 'wpcf'),
        'validate' => array('required'),
        'meta_key_type' => 'BINARY',
    );
}

/**
 * Form data for post edit page.
 * 
 * @param type $field 
 */
function wpcf_fields_checkbox_meta_box_form($field) {
    $checked = false;
    $field['data']['set_value'] = stripslashes($field['data']['set_value']);
    if ($field['value'] == $field['data']['set_value']) {
        $checked = true;
    }
    // If post is new check if it's checked by default
    global $pagenow;
    if ($pagenow == 'post-new.php' && !empty($field['data']['checked'])) {
        $checked = true;
    }
    return array(
        '#type' => 'checkbox',
        '#value' => $field['data']['set_value'],
        '#default_value' => $checked,
    );
}

/**
 * Editor callback form.
 */
function wpcf_fields_checkbox_editor_callback() {
    $form = array();
    $value_not_selected = '';
    $value_selected = '';
    if (isset($_GET['field_id'])) {
        $field = wpcf_admin_fields_get_field($_GET['field_id']);
        if (!empty($field)) {
            if (isset($field['data']['display_value_not_selected'])) {
                $value_not_selected = $field['data']['display_value_not_selected'];
            }
            if (isset($field['data']['display_value_selected'])) {
                $value_selected = $field['data']['display_value_selected'];
            }
        }
    }
    $form['#form']['callback'] = 'wpcf_fields_checkbox_editor_submit';
    $form['display'] = array(
        '#type' => 'radios',
        '#default_value' => 'db',
        '#name' => 'display',
        '#options' => array(
            'display_from_db' => array(
                '#title' => __('Display the value of this field from the database',
                        'wpcf'),
                '#name' => 'display',
                '#value' => 'db',
                '#inline' => true,
                '#after' => '<br />'
            ),
            'display_values' => array(
                '#title' => __('Show one of these two values:', 'wpcf'),
                '#name' => 'display',
                '#value' => 'value',
            ),
        ),
        '#inline' => true,
    );
    $form['display-value-1'] = array(
        '#type' => 'textfield',
        '#title' => '<td style="text-align:right;">'
        . __('Not selected:', 'wpcf') . '</td><td>',
        '#name' => 'display_value_not_selected',
        '#value' => $value_not_selected,
        '#inline' => true,
        '#before' => '<table><tr>',
        '#after' => '</td></tr>',
    );
    $form['display-value-2'] = array(
        '#type' => 'textfield',
        '#title' => '<td style="text-align:right;">'
        . __('Selected:', 'wpcf') . '</td><td>',
        '#name' => 'display_value_selected',
        '#value' => $value_selected,
        '#after' => '</tr></table>'
    );
    $form['submit'] = array(
        '#type' => 'submit',
        '#name' => 'submit',
        '#value' => __('Save Changes'),
        '#attributes' => array('class' => 'button-primary'),
    );
    $f = wpcf_form('wpcf-form', $form);
    wpcf_admin_ajax_head('Insert checkbox', 'wpcf');
    echo '<form method="post" action="">';
    echo $f->renderForm();
    echo '</form>';
    wpcf_admin_ajax_footer();
}

/**
 * Editor callback form submit.
 */
function wpcf_fields_checkbox_editor_submit() {
    $add = '';
    $field = wpcf_admin_fields_get_field($_GET['field_id']);
    if (!empty($field)) {
        if ($_POST['display'] == 'value') {
            $shortcode = '[types field="' . $field['slug'] . '" state="checked"]'
                    . $_POST['display_value_selected']
                    . '[/types] ';
            $shortcode .= '[types field="' . $field['slug'] . '" state="unchecked"]'
                    . $_POST['display_value_not_selected']
                    . '[/types]';
        } else {
            $shortcode = wpcf_fields_get_shortcode($field, $add);
        }
        echo editor_admin_popup_insert_shortcode_js($shortcode);
        die();
    }
}

/**
 * View function.
 * 
 * @param type $params 
 */
function wpcf_fields_checkbox_view($params) {
    $output = '';
    if (isset($params['state']) && $params['state'] == 'unchecked' && empty($params['field_value'])) {
        return htmlspecialchars_decode($params['#content']);
    } else if (isset($params['state']) && $params['state'] == 'unchecked') {
        return '__wpcf_skip_empty';
    }

    if (isset($params['state']) && $params['state'] == 'checked' && !empty($params['field_value'])) {
        return htmlspecialchars_decode($params['#content']);
    } else if (isset($params['state']) && $params['state'] == 'checked') {
        return '__wpcf_skip_empty';
    }
    if (!empty($params['#content'])) {
        return htmlspecialchars_decode($params['#content']);
    }
    
    if ($params['field']['data']['display'] == 'db' && $params['field_value'] != '') {
        $field = wpcf_fields_get_field_by_slug($params['field']['slug']);
        $output = $field['data']['set_value'];

        // Show the translated value if we have one.
        $output = wpcf_translate('field ' . $field['id'] . ' checkbox value',
                $output);
    } else if ($params['field']['data']['display'] == 'value'
            && $params['field_value'] != '') {
        if (!empty($params['field']['data']['display_value_selected'])) {
            $output = $params['field']['data']['display_value_selected'];
            $output = wpcf_translate('field ' . $params['field']['id'] . ' checkbox value selected',
                    $output);
        }
    } else if ($params['field']['data']['display'] == 'value') {
        if (!empty($params['field']['data']['display_value_not_selected'])) {
            $output = $params['field']['data']['display_value_not_selected'];
            $output = wpcf_translate('field ' . $params['field']['id'] . ' checkbox value not selected',
                    $output);
        }
    }

    return $output;
}