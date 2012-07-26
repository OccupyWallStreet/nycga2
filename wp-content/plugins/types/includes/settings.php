<?php
/*
 * Settings form
 */

/**
 * Settings form.
 * 
 * @return string 
 */
function wpcf_admin_settings_form() {
    $settings = wpcf_get_settings();
    $form = array();
    $form['#form']['callback'] = 'wpcf_admin_settings_form_submit';
    $form['images'] = array(
        '#name' => 'wpcf_settings[add_resized_images_to_library]',
        '#type' => 'checkbox',
        '#title' => __('Add resized images to the media library', 'wpcf'),
        '#description' => __('Types will automatically add the resized images as attachments to the media library. Choose this to automatically upload resized images to a CDN.',
                'wpcf'),
        '#inline' => true,
        '#default_value' => !empty($settings['add_resized_images_to_library']),
    );
    if (function_exists('icl_register_string')) {
        $form['register_translations_on_import'] = array(
            '#name' => 'wpcf_settings[register_translations_on_import]',
            '#type' => 'checkbox',
            '#title' => __("When importing, add texts to WPML's String Translation table",
                    'wpcf'),
            '#inline' => true,
            '#default_value' => !empty($settings['register_translations_on_import']),
            '#after' => '<br />',
        );
    }
    $show_credits = get_option('wpcf_footer_credit', array());
    $form['credits'] = array(
        '#name' => 'show_credits',
        '#type' => 'checkbox',
        '#title' => __('Display Types footer credits', 'wpcf'),
        '#description' => __("Show your support to Types, by telling people that you're using it. We'll add a small footer that tells just about Types.",
                'wpcf'),
        '#inline' => true,
        '#default_value' => !empty($show_credits['active']),
    );
    $form['submit'] = array(
        '#type' => 'submit',
        '#name' => 'submit',
        '#attributes' => array('class' => 'button-primary'),
        '#value' => __('Save Changes'),
    );
    return $form;
}

/**
 * Saves settings.
 * 
 * @param type $form 
 */
function wpcf_admin_settings_form_submit($form) {
    $settings = wpcf_get_settings();
    $data = $_POST['wpcf_settings'];
    foreach ($settings as $setting => $value) {
        if (!isset($data[$setting])) {
            $settings[$setting] = 0;
        } else {
            $settings[$setting] = $data[$setting];
        }
    }
    update_option('wpcf_settings', $settings);

    // Credits
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/footer-credit.php';
    $option = get_option('wpcf_footer_credit', array());
    if (!isset($option['message'])) {
        $data = wpcf_footer_credit_defaults();
        shuffle($data);
        $option['message'] = rand(0, count($data));
    }
    if (!isset($_POST['show_credits'])) {
        update_option('wpcf_footer_credit',
                array('active' => 0, 'message' => $option['message']));
    } else {
        update_option('wpcf_footer_credit',
                array('active' => 1, 'message' => $option['message']));
    }

    wpcf_admin_message_store(__('Settings saved', 'wpcf'));
}