<?php
if (wpcf_compare_wp_version('3.3', '<')) {
    return false;
}

/**
 * Register data (called automatically).
 * 
 * @return type 
 */
function wpcf_fields_wysiwyg() {
    $settings = array(
        'id' => 'wpcf-wysiwyg',
        'title' => __('WYSIWYG', 'wpcf'),
        'description' => __('WYSIWYG editor', 'wpcf'),
        'meta_box_css' => array(
            'wpcf-fields-wysiwyg' => array(
                'inline' => 'wpcf_fields_wysiwyg_css',
            ),
        ),
    );
    $settings['wp_version'] = '3.3';
    return $settings;
}

/**
 * Meta box form.
 * 
 * @param type $field
 * @return array 
 */
function wpcf_fields_wysiwyg_meta_box_form($field) {
    $set = array(
        'wpautop' => true, // use wpautop?
        'media_buttons' => true, // show insert/upload button(s)
        'textarea_name' => 'wpcf[' . $field['id'] . ']', // set the textarea name to something different, square brackets [] can be used here
        'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
        'tabindex' => '',
        'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
        'editor_class' => 'wpcf-wysiwyg', // add extra class(es) to the editor textarea
        'teeny' => false, // output the minimal editor config used in Press This
        'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
        'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
        'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
    );
    $form = array(
        '#type' => 'wysiwyg',
        '#attributes' => array('class' => 'wpcf-wysiwyg'),
        '#editor_settings' => $set,
    );

    return $form;
}

/**
 * CSS for styling TinyMCE Editor.
 */
function wpcf_fields_wysiwyg_css() {
    global $wp_version;

    ?>
    <style type="text/css">
        .wpcf-wysiwyg iframe, .wpcf-wysiwyg .mceIframeContainer {
            background-color: #FFFFFF !important;
        }
        .wpcf-wysiwyg table {
            border: 1px solid #DFDFDF !important;
        }
        .wpcf-media-buttons {
            margin-bottom: 10px;
        }
        .wpcf-media-buttons a {
            margin-left: 5px;
            text-decoration: none;
        }
        .wpcf-wysiwyg-switcher {
            float: right;
            margin-top: -24px;
            padding: 0;
        }
        .wpcf-wysiwyg-switcher a {
            padding: 10px;
            line-height: 25px;
            text-decoration: none;
            color: #000000;
            border: 1px solid #DFDFDF !important;
            border-bottom: none !important;
            background-color: #E8E8E8;
            margin-left: 2px;
        }
        <?php
// WP 3.3 changes
        if (version_compare($wp_version, '3.2.1', '<=')) {

            ?>
            .wpcf-wysiwyg .mceResize {
                margin-top: -25px !important;
            }
            <?php
        }

        ?>
    </style>
    <?php
}

/**
 * View function.
 * 
 * @param type $params
 * @return type 
 */
function wpcf_fields_wysiwyg_view($params) {
    $output = '';
    if (!empty($params['style']) || !empty($params['class'])) {
        $output .= '<div';
        if (!empty($params['style'])) {
            $output .= ' style="' . $params['style'] . '"';
        }
        if (!empty($params['class'])) {
            $output .= ' class="' . $params['class'] . '"';
        }
        $output .= '>';
    }
    $output .= apply_filters('the_content',
            htmlspecialchars_decode(stripslashes($params['field_value'])));
    if (!empty($params['style']) || !empty($params['class'])) {
        $output .= '</div>';
    }
    return $output;

//    $content = $params['field_value'];
//    $content = htmlspecialchars_decode(stripslashes($content));
//    $content = do_shortcode($content);
//    $content = wpautop($content);
//    return $content;
}