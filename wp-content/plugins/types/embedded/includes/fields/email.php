<?php
/**
 * Register data (called automatically).
 * 
 * @return type 
 */
function wpcf_fields_email() {
    return array(
        'id' => 'wpcf-email',
        'title' => __('Email', 'wpcf'),
        'description' => __('Email', 'wpcf'),
        'validate' => array('required', 'email'),
        'inherited_field_type' => 'textfield',
        'meta_box_js' => array(
            'wpcf-fields-email-inline' => array(
                'inline' => 'wpcf_fields_email_editor_callback_js',
            ),
        ),
        'editor_callback' => 'wpcfFieldsEmailEditorCallback(\'%s\')'
    );
}

/**
 * View function.
 * 
 * @param type $params 
 */
function wpcf_fields_email_view($params) {
    $add = '';
    if (!empty($params['title'])) {
        $add .= ' title="' . $params['title'] . '"';
        $title = $params['title'];
    } else {
        $add .= ' title="' . $params['field_value'] . '"';
        $title = $params['field_value'];
    }
    if (!empty($params['class'])) {
        $add .= ' class="' . $params['class'] . '"';
    }
    if (!empty($params['style'])) {
        $add .= ' style="' . $params['style'] . '"';
    }
    $output = '<a href="mailto:' . $params['field_value'] . '"' . $add . '>'
            . $title . '</a>';
    return $output;
}

/**
 * Editor callback JS function
 */
function wpcf_fields_email_editor_callback_js() {

    ?>
    <script type="text/javascript">
        //<![CDATA[
        function wpcfFieldsEmailEditorCallback(field_id) {
            var url = "<?php echo admin_url('admin-ajax.php'); ?>?action=wpcf_ajax&wpcf_action=editor_callback&field_id="+field_id+"&_wpnonce=<?php echo wp_create_nonce('editor_callback'); ?>&keepThis=true&TB_iframe=true&width=400&height=400";
            tb_show("<?php _e('Insert email',
            'wpcf'); ?>", url);
                }
                //]]>
    </script>
    <?php
}

/**
 * Editor callback form.
 */
function wpcf_fields_email_editor_callback() {
    $last_settings = wpcf_admin_fields_get_field_last_settings($_GET['field_id']);
    $form = array();
    $form['#form']['callback'] = 'wpcf_fields_email_editor_submit';
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
    wpcf_admin_ajax_head('Insert email', 'wpcf');
    echo '<form method="post" action="">';
    echo $f->renderForm();
    echo '</form>';
    wpcf_admin_ajax_footer();
}

/**
 * Editor callback form submit.
 */
function wpcf_fields_email_editor_submit() {
    $add = '';
    if (!empty($_POST['title'])) {
        $add = ' title="' . strval($_POST['title']) . '"';
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