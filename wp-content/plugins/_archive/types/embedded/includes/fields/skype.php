<?php

/**
 * Register data (called automatically).
 * 
 * @return type 
 */
function wpcf_fields_skype() {
    return array(
        'id' => 'wpcf-skype',
        'title' => __('Skype', 'wpcf'),
        'description' => __('Skype', 'wpcf'),
        'validate' => array('required'),
    );
}

add_filter('wpcf_pr_fields_type_skype_value_save',
        'wpcf_pr_fields_type_skype_value_save_filter', 10, 3);

/**
 * Form data for post edit page.
 * 
 * @param type $field 
 */
function wpcf_fields_skype_meta_box_form($field) {
    if (isset($field['value'])) {
        $field['value'] = maybe_unserialize($field['value']);
    }
    $form = array();
    add_filter('wpcf_fields_shortcode_slug_' . $field['slug'],
            'wpcf_fields_skype_shortcode_filter', 10, 2);
    $rand = mt_rand();
    $form['skypename'] = array(
        '#type' => 'textfield',
        '#value' => isset($field['value']['skypename']) ? $field['value']['skypename'] : '',
        '#name' => 'wpcf[' . $field['slug'] . '][skypename]',
        '#id' => 'wpcf-fields-skype-' . $field['slug'] . '-' . $rand . '-skypename',
        '#inline' => true,
        '#suffix' => '&nbsp;' . __('Skype name', 'wpcf'),
        '#description' => '',
        '#prefix' => !empty($field['description']) ? wpcf_translate('field ' . $field['id'] . ' description',
                        $field['description'])
                . '<br /><br />' : '',
        '#attributes' => array('style' => 'width:60%;'),
        '#_validate_this' => true,
        '#before' => '<div class="wpcf-skype">',
    );

    $form['style'] = array(
        '#type' => 'hidden',
        '#value' => isset($field['value']['style']) ? $field['value']['style'] : 'btn2',
        '#name' => 'wpcf[' . $field['slug'] . '][style]',
        '#id' => 'wpcf-fields-skype-' . $field['slug'] . '-' . $rand . '-style',
    );

    $preview_skypename = !empty($field['value']['skypename']) ? $field['value']['skypename'] : '--not--';
    $preview_style = !empty($field['value']['style']) ? $field['value']['style'] : 'btn2';
    $preview = wpcf_fields_skype_get_button_image($preview_skypename,
            $preview_style);

    // Set button
    if (isset($field['disable'])
            || (isset($field['wpml_action']) && $field['wpml_action'] == 'copy')) {
        $edit_button = '';
    } else {
        $edit_button = '<br />'
                . '<a href="'
                . admin_url('admin-ajax.php?action=wpcf_ajax&amp;'
                        . 'wpcf_action=insert_skype_button&amp;_wpnonce='
                        . wp_create_nonce('insert_skype_button')
                        . '&amp;update=wpcf-fields-skype-'
                        . $field['slug'] . '-' . $rand . '&amp;skypename=' . $preview_skypename
                        . '&amp;style=' . $preview_style
                        . '&amp;keepThis=true&amp;TB_iframe=true&amp;width=500&amp;height=500')
                . '"'
                . ' class="thickbox wpcf-fields-skype button-secondary"'
                . ' title="' . __('Edit Skype button', 'wpcf') . '"'
                . '>'
                . __('Edit Skype button', 'wpcf') . '</a>';
    }

    $form['markup'] = array(
        '#type' => 'markup',
        '#markup' => '<br /><div class="wpcf-form-item">'
        . '<div id="wpcf-fields-skype-'
        . $field['slug'] . '-' . $rand . '-preview">' . $preview . '</div>'
        . $edit_button . '</div>',
    );
    $form['markup-close'] = array(
        '#type' => 'markup',
        '#markup' => '</div>',
    );
    return $form;
}

/**
 * Shortcode filter.
 * 
 * @param type $shortcode
 * @param type $field
 * @return type 
 */
function wpcf_fields_skype_shortcode_filter($shortcode, $field) {
    return $shortcode;
    $add = '';
    $add .= isset($field['value']['skypename']) ? ' skypename="' . $field['value']['skypename'] . '"' : '';
//    $add .= isset($field['value']['style']) ? ' style="' . $field['value']['style'] . '"' : '';
    return str_replace(']', $add . ']', $shortcode);
}

/**
 * Edit Skype button submit.
 */
function wpcf_fields_skype_meta_box_submit() {
    $update = esc_attr($_GET['update']);
    $preview = wpcf_fields_skype_get_button_image(esc_attr($_POST['skypename']),
            esc_attr($_POST['buttonstyle']));

    ?>
    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready(function(){
            window.parent.jQuery('#<?php echo $update; ?>-skypename').val('<?php echo esc_js($_POST['skypename']); ?>');
            window.parent.jQuery('#<?php echo $update; ?>-style').val('<?php echo esc_js($_POST['buttonstyle']); ?>');
            window.parent.jQuery('#<?php echo $update; ?>-preview').html('<?php echo $preview; ?>');
            window.parent.jQuery('#TB_closeWindowButton').trigger('click');
        });
        //]]>
    </script>
    <?php
}

/**
 * Edit Skype button AJAX call.
 */
function wpcf_fields_skype_meta_box_ajax() {
    if (isset($_POST['_wpnonce_wpcf_form']) && wp_verify_nonce($_POST['_wpnonce_wpcf_form'],
                    'wpcf-form')) {
        add_action('admin_head_wpcf_ajax', 'wpcf_fields_skype_meta_box_submit');
    }
    wp_enqueue_script('jquery');
    wpcf_admin_ajax_head(__('Insert skype button', 'wpcf'));

    ?>
    <form method="post" action="">
        <div id="paddedContent"> 
            <div id="step1"> 
                <h2><?php
    _e('Enter your Skype Name', 'wpcf');

    ?></h2>  
                <p> 
                    <input id="btn-skypename" name="skypename" value="<?php echo $_GET['skypename']; ?>" type="text" /> 
                </p> 
            </div>  
            <div id="step2"> 
                <h2><?php
                _e('Select a button from below', 'wpcf');

    ?></h2>  
                <div id="static-buttons"> 
                    <table border="0" cellpadding="0" cellspacing="0" width="445">

                        <colgroup><col span="1" width="223">
                            <col span="1" width="222">
                        </colgroup><tbody><tr>
                                <td colspan="1" rowspan="1"> 
                                    <label for="btn1"> 
                                        <input <?php
                if ($_GET['style'] == 'btn1')
                    echo 'checked="checked" ';

                ?>id="btn1" name="buttonstyle" tabindex="2" value="btn1" type="radio" />  
                                        <img alt="" id="btn1-img" src="http://www.skypeassets.com/i/legacy/images/share/buttons/call_green_white_153x63.png" height="63" width="153" /> 
                                    </label> 
                                </td>
                                <td colspan="1" rowspan="1"> 
                                    <label for="btn2"> 
                                        <input <?php
                if ($_GET['style'] == 'btn2')
                    echo 'checked="checked" ';

                ?>id="btn2" name="buttonstyle" tabindex="3" value="btn2" type="radio" />  
                                        <img alt="" id="btn2-img" src="http://www.skypeassets.com/i/legacy/images/share/buttons/call_blue_white_124x52.png" height="52" width="125" /> 
                                    </label> 
                                </td>

                            </tr>
                            <tr>
                                <td colspan="1" rowspan="1"> 
                                    <label for="btn3"> 
                                        <input <?php
                if ($_GET['style'] == 'btn3')
                    echo 'checked="checked" ';

                ?>id="btn3" name="buttonstyle" tabindex="4" value="btn3" type="radio" />  
                                        <img alt="" id="btn3-img" src="http://www.skypeassets.com/i/legacy/images/share/buttons/call_green_white_92x82.png" height="82" width="92" /> 
                                    </label> 
                                </td>
                                <td colspan="1" rowspan="1"> 
                                    <label for="btn4"> 
                                        <input <?php
                if ($_GET['style'] == 'btn4')
                    echo 'checked="checked" ';

                ?>id="btn4" name="buttonstyle" tabindex="5" value="btn4" type="radio" />  
                                        <img alt="" id="btn4-img" src="http://www.skypeassets.com/i/legacy/images/share/buttons/call_blue_transparent_34x34.png" height="34" width="34" /> 
                                    </label> 
                                </td>

                            </tr>
                        </tbody></table> 
                </div>  
                <h2><?php
                _e('Skype buttons with status', 'wpcf');

    ?></h2>  
                <p><?php
                _e('If you choose to show your Skype status, your Skype button will always reflect your availability on Skype. This status will be shown to everyone, whether they’re in your contact list or not.',
                        'wpcf');

                ?></p>  
                <div id="status-buttons"> 
                    <table border="0" cellpadding="0" cellspacing="0" width="445">
                        <colgroup><col span="1" width="223">
                            <col span="1" width="222">
                        </colgroup><tbody><tr>

                                <td colspan="1" rowspan="1"> 
                                    <label for="btn5"> 
                                        <input <?php
                if ($_GET['style'] == 'btn5')
                    echo 'checked="checked" ';

                ?>id="btn5" name="buttonstyle" tabindex="6" value="btn5" type="radio" />  
                                        <img alt="" id="btn5-img" src="http://www.skypeassets.com/i/legacy/images/share/buttons/anim_balloon.gif" height="60" width="150" /> 
                                    </label> 
                                </td>
                                <td colspan="1" rowspan="1"> 
                                    <label for="btn6"> 
                                        <input <?php
                if ($_GET['style'] == 'btn6')
                    echo 'checked="checked" ';

                ?>id="btn6" name="buttonstyle" tabindex="7" value="btn6" type="radio" />  
                                        <img alt="" id="btn6-img" src="http://www.skypeassets.com/i/legacy/images/share/buttons/anim_rectangle.gif" height="44" width="182" /> 
                                    </label> 
                                </td>
                            </tr>
                        </tbody></table> 
                </div>
            </div>
                               <?php
                               wp_nonce_field('wpcf-form', '_wpnonce_wpcf_form');

                               ?>
            <br /><br /><input type="submit" class="button-primary" value="<?php
    _e('Insert skype button', 'wpcf');

                               ?>" />
    </form>
    <?php
        $update = esc_attr($_GET['update']);
        ?>
    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready(function(){
            jQuery('#btn-skypename').val(window.parent.jQuery('#<?php echo $update; ?>-skypename').val());
        });
        //]]>
    </script>
    <?php
    wpcf_admin_ajax_footer();
}

/**
 * Returns HTML formatted skype button.
 * 
 * @param type $skypename
 * @param type $template
 * @return type 
 */
function wpcf_fields_skype_get_button($skypename, $template = '') {

    if (empty($skypename)) {
        return '';
    }

    switch ($template) {

        case 'btn1':
// Call me big drawn
            $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<a href="skype:' . $skypename . '?call"><img src="http://download.skype.com/share/skypebuttons/buttons/call_green_white_153x63.png" style="border: none;" width="153" height="63" alt="Skype Me™!" /></a>';
            break;

        case 'btn4':
// Call me small
            $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<a href="skype:' . $skypename . '?call"><img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_transparent_34x34.png" style="border: none;" width="34" height="34" alt="Skype Me™!" /></a>';
            break;

        case 'btn3':
// Call me small drawn
            $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<a href="skype:' . $skypename . '?call"><img src="http://download.skype.com/share/skypebuttons/buttons/call_green_white_92x82.png" style="border: none;" width="92" height="82" alt="Skype Me™!" /></a>';
            break;

        case 'btn6':
// Status
            $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<a href="skype:' . $skypename . '?call"><img src="http://mystatus.skype.com/bigclassic/' . $skypename . '" style="border: none;" width="182" height="44" alt="My status" /></a>';
            break;

        case 'btn5':
// Status drawn
            $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<a href="skype:' . $skypename . '?call"><img src="http://mystatus.skype.com/balloon/' . $skypename . '" style="border: none;" width="150" height="60" alt="My status" /></a>';
            break;

        default:
// Call me big
            $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<a href="skype:' . $skypename . '?call"><img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_white_124x52.png" style="border: none;" width="124" height="52" alt="Skype Me™!" /></a>';
            break;
    }

    return $output;
}

/**
 * Returns HTML formatted skype button image.
 * 
 * @param type $skypename
 * @param type $template
 * @return type 
 */
function wpcf_fields_skype_get_button_image($skypename, $template = '') {

    if (empty($skypename)) {
        return '';
    }

    switch ($template) {

        case 'btn1':
// Call me big drawn
            $output = '<img src="http://download.skype.com/share/skypebuttons/buttons/call_green_white_153x63.png" style="border: none;" width="153" height="63" alt="Skype Me™!" />';
            break;

        case 'btn4':
// Call me small
            $output = '<img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_transparent_34x34.png" style="border: none;" width="34" height="34" alt="Skype Me™!" />';
            break;

        case 'btn3':
// Call me small drawn
            $output = '<img src="http://download.skype.com/share/skypebuttons/buttons/call_green_white_92x82.png" style="border: none;" width="92" height="82" alt="Skype Me™!" />';
            break;

        case 'btn6':
// Status
            $output = '<img src="http://mystatus.skype.com/bigclassic/' . $skypename . '" style="border: none;" width="182" height="44" alt="My status" />';
            break;

        case 'btn5':
// Status drawn
            $output = '<img src="http://mystatus.skype.com/balloon/' . $skypename . '" style="border: none;" width="150" height="60" alt="My status" />';
            break;

        default:
// Call me big
            $output = '<img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_white_124x52.png" style="border: none;" width="124" height="52" alt="Skype Me™!" />';
            break;
    }

    return $output;
}

/**
 * View function.
 * 
 * @param type $params 
 */
function wpcf_fields_skype_view($params) {
    if (empty($params['field_value']['skypename'])) {
        return '__wpcf_skip_empty';
    }
    if ($params['style'] == 'raw') {
        return $params['field_value']['skypename'];
    }
    // Style can be overrided by params (shortcode)
    if (!isset($params['field_value']['style'])) {
        $params['field_value']['style'] = '';
    }
    $style = (!empty($params['style']) && $params['style'] != 'default') ? $params['style'] : $params['field_value']['style'];
    $content = wpcf_fields_skype_get_button($params['field_value']['skypename'],
            $style);
    return $content;
}

/**
 * Filters post relationship save data.
 * 
 * @param type $data
 * @param type $meta_key
 * @param type $post_id
 * @return type 
 */
function wpcf_pr_fields_type_skype_value_save_filter($data, $meta_key = null,
        $post_id = null) {
    $meta = (array) get_post_meta($post_id, $meta_key, true);
    $meta['skypename'] = $data;
    $data = $meta;
    return $data;
}