<?php
/**
 * Register data (called automatically).
 * 
 * @return type 
 */
function wpcf_fields_numeric() {
    return array(
        'id' => 'wpcf-numeric',
        'title' => __('Numeric', 'wpcf'),
        'description' => __('Numeric', 'wpcf'),
        'validate' => array('required', 'number' => array('forced' => true)),
        'inherited_field_type' => 'textfield',
        'meta_key_type' => 'NUMERIC',
    );
}

/**
 * Editor callback form.
 */
function wpcf_fields_numeric_editor_callback() {
    wp_enqueue_style('wpcf-fields', WPCF_EMBEDDED_RES_RELPATH . '/css/basic.css',
            array(), WPCF_VERSION);
    wp_enqueue_script('jquery');

    // Get field
    $field = wpcf_admin_fields_get_field($_GET['field_id']);
    if (empty($field)) {
        _e('Wrong field specified', 'wpcf');
        die();
    }

    $last_settings = wpcf_admin_fields_get_field_last_settings($_GET['field_id']);

    $form = array();
    $form['#form']['callback'] = 'wpcf_fields_numeric_editor_submit';
    $form['format'] = array(
        '#type' => 'textfield',
        '#title' => __('Output format', 'wpcf'),
        '#description' => __("Similar to sprintf function. Default: 'FIELD_NAME: FIELD_VALUE'.", 'wpcf'),
        '#name' => 'format',
        '#value' => isset($last_settings['format']) ? $last_settings['format'] : 'FIELD_NAME: FIELD_VALUE',
    );
    $form['submit'] = array(
        '#type' => 'submit',
        '#name' => 'submit',
        '#value' => __('Insert shortcode', 'wpcf'),
        '#attributes' => array('class' => 'button-primary'),
    );
    $f = wpcf_form('wpcf-form', $form);
    wpcf_admin_ajax_head('Insert numeric', 'wpcf');
    echo '<form method="post" action="">';
    echo $f->renderForm();
    echo '</form>';
}

/**
 * Editor callback form submit.
 */
function wpcf_fields_numeric_editor_submit() {
    $add = '';
    if (!empty($_POST['format'])) {
        $add .= ' format="' . strval($_POST['format']) . '"';
    }
    $field = wpcf_admin_fields_get_field($_GET['field_id']);
    if (!empty($field)) {
        $shortcode = wpcf_fields_get_shortcode($field, $add);
        wpcf_admin_fields_save_field_last_settings($_GET['field_id'],
                array('format' => $_POST['format'])
        );
        echo editor_admin_popup_insert_shortcode_js($shortcode);
        die();
    }
}

/**
 * View function.
 * 
 * @param type $params 
 */
function wpcf_fields_numeric_view($params) {
    $output = '';
    if (!empty($params['format'])) {
        $patterns = array('/FIELD_NAME/', '/FIELD_VALUE/');
        $replacements = array($params['field']['name'], $params['field_value']);
        $output = preg_replace($patterns, $replacements, $params['format']);
        $output = sprintf($output, $params['field_value']);
    } else {
        $output = $params['field_value'];
    }
    return $output;
}