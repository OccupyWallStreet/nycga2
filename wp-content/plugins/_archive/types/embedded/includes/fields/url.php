<?php

/**
 * Register data (called automatically).
 * 
 * @return type 
 */
function wpcf_fields_url() {
    return array(
        'id' => 'wpcf-url',
        'title' => 'URL',
        'description' => 'URL',
        'validate' => array('required', 'url'),
        'inherited_field_type' => 'textfield',
    );
}

/**
 * View function.
 * 
 * @param type $params 
 */
function wpcf_fields_url_view($params) {
    $title = '';
    $add = '';
    if (!empty($params['title'])) {
        $add .= ' title="' . $params['title'] . '"';
        $title .= $params['title'];
    } else {
        $add .= ' title="' . $params['field_value'] . '"';
        $title .= $params['field_value'];
    }
    if (!empty($params['class'])) {
        $add .= ' class="' . $params['class'] . '"';
    }
    if (!empty($params['style'])) {
        $add .= ' style="' . $params['style'] . '"';
    }
    $output = '<a href="' . $params['field_value'] . '"' . $add . '>'
            . $title . '</a>';
    return $output;
}

/**
 * Editor callback form.
 */
function wpcf_fields_url_editor_callback() {
    $last_settings = wpcf_admin_fields_get_field_last_settings($_GET['field_id']);
    $form = array();
    $form['#form']['callback'] = 'wpcf_fields_url_editor_submit';
    $form['title'] = array(
        '#type' => 'textfield',
        '#title' => __('Title', 'wpcf'),
        '#description' => __('If set, this text will be displayed instead of raw data'),
        '#name' => 'title',
        '#value' => isset($last_settings['title']) ? $last_settings['title'] : '',
    );
    $form['class'] = array(
        '#type' => 'textfield',
        '#title' => __('Class', 'wpcf'),
        '#name' => 'class',
        '#value' => isset($last_settings['class']) ? $last_settings['class'] : '',
    );
    $form['style'] = array(
        '#type' => 'textfield',
        '#title' => __('Style', 'wpcf'),
        '#name' => 'style',
        '#value' => isset($last_settings['style']) ? $last_settings['style'] : '',
    );
    $form['submit'] = array(
        '#type' => 'submit',
        '#name' => 'submit',
        '#value' => __('Save Changes'),
        '#attributes' => array('class' => 'button-primary'),
    );
    $f = wpcf_form('wpcf-form', $form);
    wpcf_admin_ajax_head('Insert URL', 'wpcf');
    echo '<form method="post" action="">';
    echo $f->renderForm();
    echo '</form>';
    wpcf_admin_ajax_footer();
}

/**
 * Editor callback form submit.
 */
function wpcf_fields_url_editor_submit() {
    $add = '';
    if (!empty($_POST['title'])) {
        $add .= ' title="' . strval($_POST['title']) . '"';
    }
    if (!empty($_POST['class'])) {
        $add .= ' class="' . $_POST['class'] . '"';
    }
    if (!empty($_POST['style'])) {
        $add .= ' style="' . $_POST['style'] . '"';
    }
    $field = wpcf_admin_fields_get_field($_GET['field_id']);
    if (!empty($field)) {
        $shortcode = wpcf_fields_get_shortcode($field, $add);
        wpcf_admin_fields_save_field_last_settings($_GET['field_id'], $_POST);
        echo editor_admin_popup_insert_shortcode_js($shortcode);
        die();
    }
}