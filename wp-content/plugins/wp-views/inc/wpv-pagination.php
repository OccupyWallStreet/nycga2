<?php

if (isset($_GET['wpv-pagination-spinner-media-insert'])) {
    // Add JS
    add_action('admin_head', 'wpv_pagination_spinner_media_admin_head');
    // Filter media TABs
    add_filter('media_upload_tabs',
            'wpv_pagination_spinner_media_upload_tabs_filter');
    // Add button
    add_filter('attachment_fields_to_edit',
            'wpv_pagination_spinner_attachment_fields_to_edit_filter', 10, 2);
}

/**
 * get the pagination display returned by ajax.
 */

function wpv_ajax_pagination() {
    if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_pagination_nonce')
            && !empty($_POST['_wpv_settings'])) {
        $settings['posts_per_page'] = $_POST['_wpv_settings']['posts_per_page'];
        if (isset($_POST['_wpv_settings']['include_page_selector_control'])) {
            $settings['include_page_selector_control'] = $_POST['_wpv_settings']['include_page_selector_control'];
        }
        if (isset($_POST['_wpv_settings']['include_prev_next_page_controls'])) {
            $settings['include_prev_next_page_controls'] = $_POST['_wpv_settings']['include_prev_next_page_controls'];
        }
        $settings['pagination'] = $_POST['_wpv_settings']['pagination'];
        $settings['ajax_pagination'] = $_POST['_wpv_settings']['ajax_pagination'];
        $settings['rollover'] = $_POST['_wpv_settings']['rollover'];

        $settings = apply_filters('wpv_view_settings_save', $settings);

        $view_settings = apply_filters('wpv_view_settings', $settings);
        
        wpv_pagination_admin($view_settings);
    }
    
    die();
    
}

/*
  
    Add controls to the admin page for specifying the pagination
    
*/

function wpv_pagination_admin($view_settings) {
    global $post;
    $rollover_effects = array(
        'fade' => __('Fade', 'wpv-views'),
        'fadefast' => __('Fade fast', 'wpv-views'),
        'fadeslow' => __('Fade slow', 'wpv-views'),
        'slideleft' => __('Slide Left', 'wpv-views'),
        'slideright' => __('Slide Right', 'wpv-views'),
        'slideup' => __('Slide Up', 'wpv-views'),
        'slidedown' => __('Slide Down', 'wpv-views'),
    );
    
    wp_nonce_field('wpv_pagination_nonce', 'wpv_pagination_nonce');
    
    ?>
        <?php if (!isset($view_settings['ajax'])): ?>
        <div id="wpv_pagination_admin">
        <?php endif; ?>
            <div id="wpv_pagination_admin_show">
                <p>
                <?php if ($view_settings['pagination']['mode'] == 'paged') { ?>
                <?php
                    if ($view_settings['pagination'][0] == 'disable') {
                        echo __('Show <strong>all</strong> items. Pagination is disabled.', 'wpv-views');
                    } else {
                        
                        echo sprintf(__('Show <strong>%s</strong> items per page.', 'wpv-views'), $view_settings['posts_per_page']);
                        $controls = '';
                        if (isset($view_settings['include_page_selector_control']) && $view_settings['include_page_selector_control']) {
                            switch ($view_settings['pagination']['page_selector_control_type']) {
                                case 'drop_down':
                                    $controls .= __('Show <strong>page selector</strong> drop down ', 'wpv-views');
                                    break;

                                case 'link':
                                    $controls .= __('Show <strong>page selector</strong> links ', 'wpv-views');
                                    break;
                            }
                        }
                        if (isset($view_settings['include_prev_next_page_controls']) && $view_settings['include_prev_next_page_controls']) {
                            if ($controls == '') {
                                $controls .= __('Show <strong>previous</strong> and <strong>next</strong> page controls.', 'wpv-views');
                            } else {
                                $controls .= __('and <strong>previous</strong> and <strong>next</strong> page controls.', 'wpv-views');
                            }
                        }
                        if ($view_settings['ajax_pagination'][0] == 'enable') {
                            if (isset($view_settings['ajax_pagination']['style'])) {
                                $effect = array('fade' => __('Fade', 'wpv-views'),
                                                'fadefast' => __('Fade fast', 'wpv-views'),
                                                'fadeslow' => __('Fade slow', 'wpv-views'),
                                                'slideh' => __('Slide horizontally', 'wpv-views'),
                                                'slidev' => __('Slide vertically', 'wpv-views'));
                                $effect = isset($effect[$view_settings['ajax_pagination']['style']]) ? $effect[$view_settings['ajax_pagination']['style']] : $effect['fade'];
                                $controls .= sprintf(__(' Use <strong>AJAX</strong> to update page content using <strong>%s</strong> transition effect.', 'wpv-views'), $effect);
                            } else {
                                $controls .= __(' Use <strong>AJAX</strong> to update page content.', 'wpv-views');
                            }
                        }
                        
                        echo ' ' . $controls;
                    }
                } else if ($view_settings['pagination']['mode'] == 'rollover') {
                    echo sprintf(__('<strong>Auto transition:</strong> Display <strong>%s</strong> items per page for <strong>%s</strong> seconds and then <strong>%s</strong> to the next items.', 'wpv-views'),
                            strval($view_settings['rollover']['posts_per_page']),
                            strval($view_settings['rollover']['speed']),
                            $rollover_effects[strval($view_settings['rollover']['effect'])]
                            );
                }
                ?>
                <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_pagination_edit()"/>
                </p>
            </div>
            <div id="wpv_pagination_admin_edit" style="background:<?php echo WPV_EDIT_BACKGROUND;?>;display:none">
                <div style="margin:20px;">
            
                    <br />
                    <p>
                        <?php
                        $pagination_default_mode = $view_settings['pagination']['mode'];
                        if ($view_settings['pagination'][0] == 'disable'
                                && $view_settings['pagination']['mode'] !== 'rollover') {
                            $pagination_default_mode = 'none';
                        }
                        $pagination_form = array();
                        $pagination_form['select_radios'] = array(
                            '#type' => 'radios',
                            '#name' => '_wpv_settings_dummy_mode',
                            '#default_value' => $pagination_default_mode,
                            '#options' => array(
                                'none' => array(
                                    '#title' => __('No pagination', 'wpv-views'),
                                    '#description' => __('All query results will display.', 'wpv-views'),
                                    '#value' => 'none',
                                    '#attributes' => array(
                                        'onclick' => "jQuery('.wpv_pagination_mode_toggle').hide();  jQuery('.wpv_pagination_enabled').slideUp(); jQuery('#_wpv_settings_dummy_pagination').val('disable'); jQuery('#_wpv_settings_dummy_mode').val('paged');",
                                    ),
                                    '#after' => '<br />',
                                ),
                                'paged' => array(
                                    '#title' => __('Pagination enabled with manual transition', 'wpv-views'),
                                    '#description' => __('The query results will display in pages, which visitors will switch.', 'wpv-views'),
                                    '#value' => 'paged',
                                    '#attributes' => array(
                                        'onclick' => "jQuery('.wpv_pagination_mode_toggle').hide();jQuery('#wpv_pagination_mode_'+jQuery(this).val()).show(); jQuery('.wpv_pagination_enabled').slideDown(); jQuery('#_wpv_settings_dummy_pagination').val('enable'); jQuery('#_wpv_settings_dummy_mode').val('paged'); if (jQuery('input[name=_wpv_settings\\\[ajax_pagination\\\]\\\[\\\]]:checked').val() == 'disable') { jQuery('.wpv_pagination_ajax_toggle').slideUp(); }",
                                    ),
                                    '#after' => '<br />',
                                ),
                                'rollover' => array(
                                    '#title' => __('Pagination enabled with automatic transition', 'wpv-views'),
                                    '#description' => __('The query results will display in pages, which will switch automatically (good for sliders).', 'wpv-views'),
                                    '#value' => 'rollover',
                                    '#attributes' => array(
                                        'onclick' => "jQuery('.wpv_pagination_mode_toggle').hide();jQuery('#wpv_pagination_mode_'+jQuery(this).val()).show(); jQuery('.wpv_pagination_enabled').slideDown(); jQuery('#_wpv_settings_dummy_pagination').val('enable'); jQuery('#_wpv_settings_dummy_mode').val('rollover');",
                                    ),
                                    '#after' => '<br /><input id="_wpv_settings_dummy_pagination" type="hidden" name="_wpv_settings[pagination][]" value="' . $view_settings['pagination'][0] . '" /><input id="_wpv_settings_dummy_mode" type="hidden" name="_wpv_settings[pagination][mode]" value="' . $view_settings['pagination']['mode'] . '" />',
                                ),
                            ),
                        );
                        echo wpv_form_control($pagination_form);
                        ?>
                    </p>
                    <div id="wpv_pagination_mode_paged" class="wpv_pagination_mode_toggle"<?php if ($view_settings['pagination']['mode'] != 'paged') { echo ' style="display: none;"'; } ?>>
                            <div class="wpv_pagination_enabled"<?php echo $view_settings['pagination'][0] == 'disable' && $view_settings['pagination']['mode'] != 'rollover' ? ' style="display:none;"' : ''; ?>>
                                <div style="margin-left:20px;">
                            <?php _e('Number of items per page:', 'wpv-views')?>
                            <select name="_wpv_settings[posts_per_page]">
                                <?php
                                    for($i = 1; $i < 50; $i++) {
                                        $selected = $view_settings['posts_per_page']==(string)$i ? ' selected="selected"' : '';
                                        echo '<option value="' . $i . '"' . $selected . '>'. $i . '</option>';
                                    }
                                ?>
                            </select>

                            <p>
                            <?php $checked = (isset($view_settings['include_page_selector_control']) && $view_settings['include_page_selector_control']) ? ' checked="checked"' : '';?>
                            <label><input id="_wpv_settings_include_page_selector_control" type="checkbox" name="_wpv_settings[include_page_selector_control]"<?php echo $checked; ?>>&nbsp;<?php _e('Include a page selector', 'wpv-views'); ?></label>
                            <select id="_wpv_settings_page_selector_control_type" name="_wpv_settings[pagination][page_selector_control_type]">
                                <option value="drop_down"<?php if ($view_settings['pagination']['page_selector_control_type'] == 'drop_down') { echo ' selected="selected"'; } ?>><?php _e('Drop down',  'wpv-views'); ?></option>
                                <option value="link"<?php if ($view_settings['pagination']['page_selector_control_type'] == 'link') { echo ' selected="selected"'; } ?>><?php _e('Links',  'wpv-views'); ?></option>
                            </select>
                            <br />
                            <?php $checked = (isset($view_settings['include_prev_next_page_controls']) && $view_settings['include_prev_next_page_controls']) ? ' checked="checked"' : '';?>
                            <label><input id="_wpv_settings_include_prev_next_page_controls" type="checkbox" name="_wpv_settings[include_prev_next_page_controls]"<?php echo $checked; ?>>&nbsp;<?php _e('Include next page and previous page controls', 'wpv-views'); ?></label>
                            </p>
                        </div>
                        <p>
                        <legend style="margin-bottom:5px"><strong><?php _e('AJAX:', 'wpv-views') ?></strong></legend>
                        </p>
                        <div style="margin-left:20px;">
                            <ul>
                                <?php $checked = $view_settings['ajax_pagination'][0] == 'disable' ? ' checked="checked"' : ''; ?>
                                <li><label><input type="radio"  value="disable" name="_wpv_settings[ajax_pagination][]" onclick="jQuery('.wpv_pagination_ajax_toggle').slideUp();"<?php echo $checked; ?>>&nbsp;<?php _e('Pagination updates the entire page', 'wpv-views'); ?></label></li>
                                <?php $checked = $view_settings['ajax_pagination'][0] == 'enable' ? ' checked="checked"' : ''; ?>
                                <li><label><input type="radio"  value="enable" name="_wpv_settings[ajax_pagination][]" onclick="jQuery('.wpv_pagination_ajax_toggle').slideDown();"<?php echo $checked; ?>>&nbsp;<?php _e('Pagination updates only the view (use AJAX)', 'wpv-views'); ?></label></li>
                                <li class="wpv_pagination_ajax_toggle"<?php if ($view_settings['ajax_pagination'][0] == 'disable') { echo ' style="display:none;"'; } ?>><label><select name="_wpv_settings[ajax_pagination][style]">
                                                <option value="fade"<?php if ($view_settings['ajax_pagination']['style'] == 'fade') { echo ' selected="selected"'; } ?>><?php _e('Fade',  'wpv-views'); ?></option>
                                                <option value="fadefast"<?php if ($view_settings['ajax_pagination']['style'] == 'fadefast') { echo ' selected="selected"'; } ?>><?php _e('Fade fast',  'wpv-views'); ?></option>
                                                <option value="fadeslow"<?php if ($view_settings['ajax_pagination']['style'] == 'fadeslow') { echo ' selected="selected"'; } ?>><?php _e('Fade slow',  'wpv-views'); ?></option>
                                                <option value="slideh"<?php if ($view_settings['ajax_pagination']['style'] == 'slideh') { echo ' selected="selected"'; } ?>><?php _e('Slide horizontally',  'wpv-views'); ?></option>
                                                <option value="slidev"<?php if ($view_settings['ajax_pagination']['style'] == 'slidev') { echo ' selected="selected"'; } ?>><?php _e('Slide vertically',  'wpv-views'); ?></option>
                                            </select><?php _e('Transition effect',  'wpv-views'); ?></label></li>
                                            <li class="wpv_pagination_ajax_toggle"<?php if ($view_settings['ajax_pagination'][0] == 'disable') { echo ' style="display:none;"'; } ?>>
                                                <label><input type="checkbox" name="_wpv_settings[pagination][preload_images]" value="1"<?php if ($view_settings['pagination']['preload_images']) { echo ' checked="checked"'; } ?> />&nbsp;<?php _e('Preload images before transition',  'wpv-views'); ?></label>
                                            </li>
                                                
                            </ul>
                        </div>
                    </div><!-- .wpv_pagination_enabled -->
                        </div>
                    <div id="wpv_pagination_mode_rollover" class="wpv_pagination_mode_toggle" style="margin-left:20px;<?php if ($view_settings['pagination']['mode'] != 'rollover') { echo ' display: none;'; } ?>">
                        <?php _e('Number of items per page:', 'wpv-views'); ?>
                            <select name="_wpv_settings[rollover][posts_per_page]">
                                <?php
                                    for($i = 1; $i < 50; $i++) {
                                        $selected = $view_settings['rollover']['posts_per_page']==(string)$i ? ' selected="selected"' : '';
                                        echo '<option value="' . $i . '"' . $selected . '>'. $i . '</option>';
                                    }
                                ?>
                            </select>
                        <br /><br />
                        <?php _e('Show each page for:', 'wpv-views')?>
                            <select name="_wpv_settings[rollover][speed]">
                                <?php
                                    for($i = 1; $i < 20; $i++) {
                                        $selected = $view_settings['rollover']['speed']==(string)$i ? ' selected="selected"' : '';
                                        echo '<option value="' . $i . '"' . $selected . '>'. $i . '</option>';
                                    }
                                ?>
                            </select>&nbsp;<?php _e('seconds', 'wpv-views')?>
                        <br /><br />
                        <?php _e('Transition effect:', 'wpv-views')?>
                            <select name="_wpv_settings[rollover][effect]">
                                <?php
                                    foreach($rollover_effects as $i => $title) {
                                        $selected = $view_settings['rollover']['effect']==(string)$i ? ' selected="selected"' : '';
                                        echo '<option value="' . $i . '"' . $selected . '>'. $title . '</option>';
                                    }
                                ?>
                            </select>
                        <br /><br />
                        <label><input type="checkbox" name="_wpv_settings[rollover][include_page_selector]" value="1"<?php if ($view_settings['rollover']['include_page_selector']) { echo ' checked="checked"'; } ?> />&nbsp;<?php _e('Include page selector links',  'wpv-views'); ?></label>
                        <br />
                        <label><input type="checkbox" name="_wpv_settings[rollover][include_prev_next_page_controls]" value="1"<?php if ($view_settings['rollover']['include_prev_next_page_controls']) { echo ' checked="checked"'; } ?> />&nbsp;<?php _e('Include next page and previous page controls',  'wpv-views'); ?></label>
                        <br />
                        <label><input type="checkbox" name="_wpv_settings[rollover][preload_images]" value="1"<?php if ($view_settings['rollover']['preload_images']) { echo ' checked="checked"'; } ?> />&nbsp;<?php _e('Preload images before transition',  'wpv-views'); ?></label>
                        <br /><br />
                    </div>
                    <div style="margin:0 0 20px 20px;<?php echo ($view_settings['pagination'][0] == 'disable' || $view_settings['ajax_pagination'][0] == 'disable') && $view_settings['pagination']['mode'] != 'rollover' ? 'display:none;' : ''; ?>" class="wpv_pagination_enabled wpv_pagination_ajax_toggle">
                        <label><input type="checkbox" name="_wpv_settings[pagination][cache_pages]" value="1"<?php if ($view_settings['pagination']['cache_pages']) { echo ' checked="checked"'; } ?> />&nbsp;<?php _e('Cache pages',  'wpv-views'); ?></label><br />
                        <label><input type="checkbox" name="_wpv_settings[pagination][preload_pages]" value="1"<?php if ($view_settings['pagination']['preload_pages']) { echo ' checked="checked"'; } ?> />&nbsp;<?php _e('Pre-load the next and previous pages - avoids loading delays when users move between pages',  'wpv-views'); ?></label>
                        <br /><br />
                        <label><input type="radio" onclick="jQuery('.wpv-spinner-selection').hide();jQuery('#wpv-spinner-default').show();" name="_wpv_settings[pagination][spinner]" value="default"<?php if ($view_settings['pagination']['spinner'] == 'default') { echo ' checked="checked"'; } ?> />&nbsp;<?php _e('Spinner graphics from Views', 'wpv-views'); ?></label>
                        <div id="wpv-spinner-default" class="wpv-spinner-selection" style="margin-left: 20px;<?php if ($view_settings['pagination']['spinner'] != 'default'){ echo ' display:none;"'; } ?>">
                        <?php
                        foreach (glob(WPV_PATH_EMBEDDED . "/res/img/ajax-loader*") as $file) {
                            $filename = WPV_URL_EMBEDDED . '/res/img/' . basename($file);
                            $filename2 = WPV_URL . '/res/img/' . basename($file);
                        ?>
                            <label><input type="radio" name="_wpv_settings[pagination][spinner_image]" value="<?php echo $filename; ?>"<?php if ($view_settings['pagination']['spinner_image'] == $filename || $view_settings['pagination']['spinner_image'] == $filename2) { echo ' checked="checked"'; } ?> />&nbsp;<img style="background-color: #FFFFFF;" src="<?php echo $filename; ?>" title="<?php echo $filename; ?>" /></label>&nbsp;&nbsp;
                        <?php } ?>
                        </div>
                        <br />
                        <label><input type="radio" onclick="jQuery('.wpv-spinner-selection').hide();jQuery('#wpv-spinner-uploaded').show();" name="_wpv_settings[pagination][spinner]" value="uploaded"<?php if ($view_settings['pagination']['spinner'] == 'uploaded') { echo ' checked="checked"'; } ?> />&nbsp;<?php _e('My custom spinner graphics', 'wpv-views'); ?></label>
                        <div id="wpv-spinner-uploaded" class="wpv-spinner-selection" style="margin-left: 20px;<?php if ($view_settings['pagination']['spinner'] != 'uploaded') { echo ' display:none;'; } ?>">
                            <input id="wpv-pagination-spinner-image" type="textfield" name="_wpv_settings[pagination][spinner_image_uploaded]" value="<?php echo $view_settings['pagination']['spinner_image_uploaded']; ?>" />&nbsp;<a href="" class="button-secondary" onclick="tb_show('<?php _e('Upload image'); ?>', 'media-upload.php?post_id=<?php echo $post->ID; ?>&type=image&wpv-pagination-spinner-media-insert=1&TB_iframe=true');return false;"><?php _e('Upload Image'); ?></a>
                            <br /><img id="wpv-pagination-spinner-image-preview" style="margin-top: 5px;" src="<?php echo $view_settings['pagination']['spinner_image_uploaded']; ?>" height="16" />
                        </div>
                        <br />
                        <label><input type="radio" onclick="jQuery('.wpv-spinner-selection').hide();"  name="_wpv_settings[pagination][spinner]" value="no"<?php if ($view_settings['pagination']['spinner'] == 'no') { echo ' checked="checked"'; } ?> />&nbsp;<?php _e('No spinner graphics', 'wpv-views'); ?></label>
                    </div>
<!--                    <div style="margin:0 0 20px 20px;<?php echo ($view_settings['pagination'][0] == 'disable'|| $view_settings['ajax_pagination'][0] == 'disable') && $view_settings['pagination']['mode'] != 'rollover' ? 'display:none;' : ''; ?>" class="wpv_pagination_enabled wpv_pagination_ajax_toggle">
                        <label><?php _e('Javascript callback function on next slide', 'wpv-views'); ?><br />
                        <input type="text" name="_wpv_settings[pagination][callback_next]" value="<?php if (!empty($view_settings['pagination']['callback_next'])) { echo $view_settings['pagination']['callback_next']; } ?>" />
                        </label>
                    </div>-->
                    <input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="jQuery('html,body').animate({scrollTop:jQuery('#wpv_settings').offset().top-25}, 1500); wpv_pagination_edit_ok();"/>
                    <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="jQuery('html,body').animate({scrollTop:jQuery('#wpv_settings').offset().top-25}, 1500); wpv_pagination_edit_cancel()"/>
                    <br />
                </div>
            </div>

        <?php if (!isset($view_settings['ajax'])): ?>
        </div>
            
        
            <script type="text/javascript">
                <?php
                    /* NOTE: we don't use _e() or __() to translate these.
                       We use [wpml-string] shortcodes instead
                    */
                ?>
                var page_x_of_n = "[wpml-string context=\"wpv-views\"]Showing page 1 of 9[/wpml-string]";
                var page_next = "[wpml-string context=\"wpv-views\"]Next[/wpml-string]";
                var page_previous = "[wpml-string context=\"wpv-views\"]Previous[/wpml-string]";
            </script>
        <?php endif; ?>

    <?php
    
}

/**
 * Media popup JS.
 */
function wpv_pagination_spinner_media_admin_head() {

    ?>
    <script type="text/javascript">
        function wpvPaginationSpinnerMediaTrigger(guid, type) {
            window.parent.jQuery('#wpv-pagination-spinner-image').val(guid);
            window.parent.jQuery('#wpv-pagination-spinner-image-preview').attr('src', guid);
            window.parent.jQuery('#TB_closeWindowButton').trigger('click');
        }
    </script>
    <style type="text/css">
        tr.submit { display: none; }
    </style>
    <?php
}

/**
 * Adds 'Spinner' column to media item table.
 * 
 * @param type $form_fields
 * @param type $post
 * @return type 
 */
function wpv_pagination_spinner_attachment_fields_to_edit_filter($form_fields, $post) {
    $type = (strpos($post->post_mime_type, 'image/') !== false) ? 'image' : 'file';
    $form_fields['wpcf_fields_file'] = array(
        'label' => __('Views Pagination', 'wpv-views'),
        'input' => 'html',
        'html' => '<a href="#" title="' . $post->guid
        . '" class="wpv-pagination-spinner-insert-button'
        . ' button-primary" onclick="wpvPaginationSpinnerMediaTrigger(\''
        . $post->guid . '\', \'' . $type . '\')">'
        . __('Use as spinner image', 'wpv-views') . '</a><br /><br />',
    );
    return $form_fields;
}

/**
 * Filters media TABs.
 * 
 * @param type $tabs
 * @return type 
 */
function wpv_pagination_spinner_media_upload_tabs_filter($tabs) {
    unset($tabs['type_url']);
    return $tabs;
}
