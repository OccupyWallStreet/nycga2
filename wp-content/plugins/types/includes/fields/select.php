<?php
/**
 * Types-field: Select
 *
 * Description: Displays a select box to the user.
 *
 * Rendering: The option title will be rendered or if set - specific value.
 * 
 * Parameters:
 * 'raw' => 'true'|'false' (display raw data stored in DB, default false)
 * 'output' => 'html' (wrap data in HTML, optional)
 * 'show_name' => 'true' (show field name before value e.g. My checkbox: $value)
 *
 * Example usage:
 * With a short code use [types field="my-select"]
 * In a theme use types_render_field("my-select", $parameters)
 * 
 */

/**
 * Form data for group form.
 * 
 * @return type 
 */
function wpcf_fields_select_insert_form($form_data = array(), $parent_name = '') {
    $id = 'wpcf-fields-select-' . mt_rand();
    $form['name'] = array(
        '#type' => 'textfield',
        '#title' => __('Name of custom field', 'wpcf'),
        '#description' => __('Under this name field will be stored in DB (sanitized)',
                'wpcf'),
        '#name' => 'name',
        '#attributes' => array('class' => 'wpcf-forms-set-legend'),
        '#validate' => array('required' => array('value' => true)),
    );
    $form['description'] = array(
        '#type' => 'textarea',
        '#title' => __('Description', 'wpcf'),
        '#description' => __('Text that describes function to user', 'wpcf'),
        '#name' => 'description',
        '#attributes' => array('rows' => 5, 'cols' => 1),
    );
    $form['options-markup-open'] = array(
        '#type' => 'markup',
        '#markup' => '<strong>' . __('Options', 'wpcf')
        . '</strong><br /><br /><div id="' . $id . '-sortable"'
        . ' class="wpcf-fields-select-sortable wpcf-compare-unique-value-wrapper">',
    );
    $options = !empty($form_data['options']) ? $form_data['options'] : array();
    $options = !empty($form_data['data']['options']) ? $form_data['data']['options'] : $options;
    if (!empty($options)) {
        foreach ($options as $option_key => $option) {
            if ($option_key == 'default') {
                continue;
            }
            $option['key'] = $option_key;
            $option['default'] = isset($options['default']) ? $options['default'] : null;
            $form = $form + wpcf_fields_select_get_option('', $option);
        }
    } else {
        $form = $form + wpcf_fields_select_get_option();
    }

    if (!empty($options)) {
        $count = count($options);
    } else {
        $count = 1;
    }

    $form['options-markup-close'] = array(
        '#type' => 'markup',
        '#markup' => '</div><div id="'
        . $id . '-add-option"></div><br /><a href="' . admin_url('admin-ajax.php?action=wpcf_ajax&amp;wpcf_action=add_select_option&amp;_wpnonce='
                . wp_create_nonce('add_select_option') . '&amp;wpcf_ajax_update_add=' . $id . '-sortable&amp;parent_name=' . urlencode($parent_name)
                . '&amp;count=' . $count)
        . '" onclick="wpcfFieldsFormCountOptions(jQuery(this));"'
        . ' class="button-secondary wpcf-ajax-link">'
        . __('Add option', 'wpcf') . '</a>',
    );
    $form['options-close'] = array(
        '#type' => 'markup',
        '#markup' => '<br /><br />',
    );
    return $form;
}

function wpcf_fields_select_get_option($parent_name = '', $form_data = array()) {
    $id = isset($form_data['key']) ? $form_data['key'] : 'wpcf-fields-select-option-' . mt_rand();
    $form = array();
    $value = isset($_GET['count']) ? __('Option title', 'wpcf') . ' ' . $_GET['count'] : __('Option title',
                    'wpcf') . ' 1';
    $value = isset($form_data['title']) ? $form_data['title'] : $value;
    $form[$id . '-title'] = array(
        '#type' => 'textfield',
        '#name' => $parent_name . '[options][' . $id . '][title]',
        '#value' => $value,
        '#inline' => true,
        '#attributes' => array('style' => 'width:80px;'),
        '#before' => '<div class="wpcf-fields-select-draggable"><img src="'
        . WPCF_RES_RELPATH
        . '/images/move.png" class="wpcf-fields-form-select-move-field" alt="'
        . __('Move this option', 'wpcf') . '" /><img src="'
        . WPCF_RES_RELPATH . '/images/delete.png"'
        . ' class="wpcf-fields-select-delete-option wpcf-pointer"'
        . ' onclick="if (confirm(\'' . __('Are you sure?', 'wpcf')
        . '\')) { jQuery(this).parent().fadeOut(function(){jQuery(this).remove();}); }"'
        . 'alt="' . __('Delete this option', 'wpcf') . '" />',
    );
    $value = isset($_GET['count']) ? $_GET['count'] : 1;
    $value = isset($form_data['value']) ? $form_data['value'] : $value;
    $form[$id . '-value'] = array(
        '#type' => 'textfield',
        '#name' => $parent_name . '[options][' . $id . '][value]',
        '#value' => $value,
        '#inline' => true,
        '#attributes' => array(
            'style' => 'width:80px;',
            'class' => 'wpcf-compare-unique-value',
        ),
    );
    $form[$id . '-default'] = array(
        '#type' => 'radio',
        '#inline' => true,
        '#title' => __('Default', 'wpcf'),
        '#after' => '</div>',
        '#name' => $parent_name . '[options][default]',
        '#value' => $id,
        '#default_value' => isset($form_data['default']) ? $form_data['default'] : false,
    );
    return $form;
}