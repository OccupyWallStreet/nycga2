<?php

/**
 * Register data (called automatically).
 * 
 * @return type 
 */
function wpcf_fields_radio() {
    return array(
        'id' => 'wpcf-radio',
        'title' => __('Radio', 'wpcf'),
        'description' => __('Radio', 'wpcf'),
        'validate' => array('required'),
    );
}

/**
 * Form data for post edit page.
 * 
 * @param type $field 
 */
function wpcf_fields_radio_meta_box_form($field) {
    $options = array();
    $default_value = '';

    if (!empty($field['data']['options'])) {
        foreach ($field['data']['options'] as $option_key => $option) {
            // Skip default value record
            if ($option_key == 'default') {
                continue;
            }
            // Set default value
            if (!empty($field['data']['options']['default'])
                    && $option_key == $field['data']['options']['default']) {
                $default_value = $option['value'];
            }
            $options[$option['title']] = array(
                '#value' => $option['value'],
                '#title' => wpcf_translate('field ' . $field['id'] . ' option '
                        . $option_key . ' title', $option['title']),
            );
        }
    }
    
    if (!empty($field['value'])
            || ($field['value'] === 0 || $field['value'] === '0')) {
        $default_value = $field['value'];
    }

    return array(
        '#type' => 'radios',
        '#default_value' => $default_value,
        '#options' => $options,
    );
}

/**
 * Editor callback form.
 */
function wpcf_fields_radio_editor_callback() {
    wpcf_admin_ajax_head('Insert checkbox', 'wpcf');
    $field = wpcf_admin_fields_get_field($_GET['field_id']);
    if (empty($field)) {
        echo '<div class="message error"><p>' . __('Wrong field specified',
                'wpcf') . '</p></div>';
        wpcf_admin_ajax_footer();
        return '';
    }
    $form = array();
    $form['#form']['callback'] = 'wpcf_fields_radio_editor_submit';
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
                '#title' => __('Show one of these values:', 'wpcf'),
                '#name' => 'display',
                '#value' => 'value',
            ),
        ),
        '#inline' => true,
    );
    if (!empty($field['data']['options'])) {
        $form['table-open'] = array(
            '#type' => 'markup',
            '#markup' => '<table style="margin-top:20px;" cellpadding="0" cellspacing="8">',
        );
        foreach ($field['data']['options'] as $option_id => $option) {
            if ($option_id == 'default') {
                continue;
            }
            $value = isset($option['display_value']) ? $option['display_value'] : $option['value'];
            $form['display-value-' . $option_id] = array(
                '#type' => 'textfield',
                '#title' => $option['title'],
                '#name' => 'options[' . $option_id . ']',
                '#value' => $value,
                '#inline' => true,
                '#pattern' => '<tr><td style="text-align:right;"><LABEL></td><td><ELEMENT></td></tr>',
                '#attributes' => array('style' => 'width:200px;'),
            );
        }
        $form['table-close'] = array(
            '#type' => 'markup',
            '#markup' => '</table>',
        );
    }
    $form['submit'] = array(
        '#type' => 'submit',
        '#name' => 'submit',
        '#value' => __('Save Changes'),
        '#attributes' => array('class' => 'button-primary'),
    );
    $f = wpcf_form('wpcf-form', $form);

    echo '<form method="post" action="">';
    echo $f->renderForm();
    echo '</form>';
    wpcf_admin_ajax_footer();
}

/**
 * Editor callback form submit.
 */
function wpcf_fields_radio_editor_submit() {
    $add = '';
    $field = wpcf_admin_fields_get_field($_GET['field_id']);
    if (!empty($field)) {
        if ($_POST['display'] == 'value' && !empty($_POST['options'])) {
            $shortcode = '';
            foreach ($_POST['options'] as $option_id => $value) {
                $shortcode .= '[types field="' . $field['slug']
                        . '" option="' . $option_id . '"]' . $value
                        . '[/types] ';
            }
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
function wpcf_fields_radio_view($params) {
    if ($params['style'] == 'raw') {
        return '';
    }
    $field = wpcf_fields_get_field_by_slug($params['field']['slug']);
    $output = '';

    // See if user specified output for each field
    if (isset($params['option'])) {
        foreach ($field['data']['options'] as $option_key => $option) {
            if (isset($option['value'])
                    && $option['value'] == $params['field_value']
                    && $option_key == $params['option']) {
                return htmlspecialchars_decode($params['#content']);
            }
        }
//        return ' ';
        return '__wpcf_skip_empty';
    }

    if (!empty($field['data']['options'])) {
        $field_value = $params['field_value'];
        foreach ($field['data']['options'] as $option_key => $option) {
            if (isset($option['value'])
                    && $option['value'] == $params['field_value']) {
                $field_value = wpcf_translate('field ' . $params['field']['id'] . ' option '
                        . $option_key . ' title', $option['title']);
                if (isset($params['field']['data']['display'])
                        && $params['field']['data']['display'] != 'db'
                        && !empty($option['display_value'])) {
                    $field_value = wpcf_translate('field ' . $params['field']['id'] . ' option '
                            . $option_key . ' display value',
                            $option['display_value']);
                }
            }
        }
        $output = $field_value;
    }
    return $output;
}