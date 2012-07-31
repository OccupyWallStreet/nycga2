<?php

if(is_admin()){
	add_action('init', 'wpv_control_init');
	
	function wpv_control_init() {
        add_action('admin_head', 'wpv_controls_js');            
    }
}


function wpv_controls_js() {
	?>
	
    <script type="text/javascript">
		var wpv_submit_text = '<?php echo __('Apply', 'wpv-views'); ?>';
		var wpv_submit_button_text = '<?php echo __('Submit button', 'wpv-views'); ?>';
        var wpv_ok = '<?php echo __('OK', 'wpv-views'); ?>';
        var wpv_cancel = '<?php echo __('Cancel', 'wpv-views'); ?>';
        var wpv_remove = '<?php echo __('Remove', 'wpv-views'); ?>';
        var wpv_add_another_value = '<?php echo __('Add another value', 'wpv-views'); ?>';
        var wpv_edit_background = '<?php echo WPV_EDIT_BACKGROUND; ?>';
        var wpv_no_values = '<?php echo __('There are no values', 'wpv-views'); ?>';
        var wpv_values = '<?php echo __('Values', 'wpv-views'); ?>';
        var wpv_display_values = '<?php echo __('Display values', 'wpv-views'); ?>';
        var wpv_title = '<?php echo __('Title', 'wpv-views'); ?>';
        var wpv_url_param_deleted_message = '<?php echo __('The filter for this is not found.', 'wpv-views'); ?>';
        var wpv_auto_fill_on = '<?php echo __('Use existing custom field values when showing this control', 'wpv-views'); ?>';
        var wpv_auto_fill_off = '<?php echo __('Use the manually entered values', 'wpv-views'); ?>';
        var wpv_auto_fill_default = '<?php echo __('Use this as the default value', 'wpv-views'); ?>';
	</script>
	
    <script type="text/javascript">
        //<![CDATA[
        function wpv_insert_view_form_popup(view_id) {

            var url = "<?php echo admin_url('admin-ajax.php'); ?>?action=wpv_view_form_popup&_wpnonce=<?php echo wp_create_nonce('wpv_editor_callback'); ?>&view_id="+view_id+"&keepThis=true&TB_iframe=true&height=400&width=400";
            tb_show("<?php _e('Insert View Form', 'wpcf');    ?>", url);
        }
        
        var wpcfFieldsEditorCallback_redirect = null;
        
        function wpcfFieldsEditorCallback_set_redirect(function_name, params) {
            wpcfFieldsEditorCallback_redirect = {'function' : function_name, 'params' : params};
        }
        
        //]]>
    </script>
    
	<?php
}
function wpv_filter_controls_admin($view_settings){
    
    $select = '<select name="" >';
    $select .= '<option value="types-auto">' . __('Types auto style', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="radios">' . __('Radio', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="checkbox">' . __('Checkbox', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="checkboxes">' . __('Checkboxes', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="select">' . __('Select', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="textfield">' . __('Text field', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="datepicker">' . __('Date picker', 'wpv-views') . '&nbsp;</option>';
    $select .= '</select>';

    $select_tax = '<select name="" >';
    $select_tax .= '<option value="checkboxes">' . __('Checkboxes', 'wpv-views') . '&nbsp;</option>';
    $select_tax .= '<option value="select">' . __('Select', 'wpv-views') . '&nbsp;</option>';
    $select_tax .= '</select>';

    $select_search = '<select name="" >';
    $select_search .= '<option value="textfield">' . __('Text field', 'wpv-views') . '&nbsp;</option>';
    $select_search .= '</select>';

    $select_submit = '<select name="" >';
    $select_submit .= '<option value="submit-button">' . __('Submit button', 'wpv-views') . '&nbsp;</option>';
    $select_submit .= '</select>';

    $view_settings = _wpv_initialize_url_param_controls($view_settings);

	wp_nonce_field('wpv_get_types_field_name_nonce', 'wpv_get_types_field_name_nonce');
    
    ?>
    
    <div id="wpv_filter_controls_admin_summary" <?php echo count($view_settings['filter_controls_param']) > 0 ? '' : 'style="display:none"'; ?>>
        <strong><?php _e('Filter controls: ', 'wpv-views') ?></strong>
        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_filter_controls_edit()"/>
    </div>

    <div id="wpv_filter_controls_admin_edit" style="background:<?php echo WPV_EDIT_BACKGROUND;?>;display:none">
        <div style="margin:20px;">
            <br />
            <p>
                <strong><?php _e('Filter controls settings', 'wpv-views') ?></strong>
                <?php echo '<a href="' . WPV_ADD_FILTER_CONTROLS_LINK . '" target="_blank">' . sprintf(__('Learn more about filter controls', 'wpv-views')) . ' &raquo;</a>'; ?>
            </p>
            <table id="view_filter_controls_table" class="widefat fixed">
                <thead>
                    <tr>
                        <th><?php echo __('Enable', 'wpv-views'); ?></th>
                        <th width="170px"><?php echo __('Filter', 'wpv-views'); ?></th>
                        <th width="170px"><?php echo __('Label', 'wpv-views'); ?></th>
                        <th><?php echo __('Input type', 'wpv-views'); ?></th>
                        <th><?php echo __('Input values', 'wpv-views'); ?></th>
                        <th width="16px"></th>
                    </tr>
                </thead>
                
                <tfoot>
                    <tr>
                        <th></th><th></th><th></th><th></th><th></th><th></th>
                    </tr>
                </tfoot>
                
                <tbody>
                    <?php // Add a dummy row that we can copy when we add more controls via javascript. ?>
                    <tr style="display:none">
                        <td>
                            <input type="checkbox" name="_wpv_settings[filter_controls_enable][]" />
                            <input type="hidden" name="_wpv_settings[filter_controls_enable][]" value="0" />
                            <input type="hidden" name="_wpv_settings[filter_controls_param][]" />
                            <input type="hidden" name="_wpv_settings[filter_controls_mode][]" />
                        </td>
                        <td><input type="hidden" name="_wpv_settings[filter_controls_field_name][]" /><span></span></td>
                        <td><input type="text" width="100%" name="_wpv_settings[filter_controls_label][]" /></td>
                        <td><?php echo str_replace('name=""', 'name="_wpv_settings[filter_controls_type][]"', $select); ?></td>
                        <td><input type="hidden" name="_wpv_settings[filter_controls_values][]" /><input type="button" value="<?php _e('Edit', 'wpv-views'); ?>" class="button-secondary"/></td>
                        <td width="16px"><img src="<?php echo WPV_URL; ?>/res/img/move.png" class="move" style="cursor: move;" /></td>
                    </tr>
        
                    <?php
                    
                        for ($i = 0; $i < count($view_settings['filter_controls_param']); $i++) {
                            if ($view_settings['filter_controls_param'][$i] != '') {
                                $show_edit = '';
                                $filter = $view_settings['filter_controls_field_name'][$i] . ' (' . $view_settings['filter_controls_param'][$i] . ')';
                                
                                switch ($view_settings['filter_controls_mode'][$i]) {
                                    case 'cf':
                                        $new_select = str_replace('name=""', 'name="_wpv_settings[filter_controls_type][]"', $select);
                                        switch ($view_settings['filter_controls_type'][$i]) {
                                            case 'types-auto':
                                            case 'textfield':
											case 'datepicker':
                                                $show_edit = ' style="display:none" ';
                                                break;
                                            
                                            default:
                                                break;
                                        }
                                        break;
                                    
                                    case 'tax':
                                        $new_select = str_replace('name=""', 'name="_wpv_settings[filter_controls_type][]"', $select_tax);
                                        $show_edit = ' style="display:none" ';
                                        break;
                                    
                                    case 'search':
                                        $new_select = str_replace('name=""', 'name="_wpv_settings[filter_controls_type][]"', $select_search);
                                        $show_edit = ' style="display:none" ';
                                        break;

                                    case 'submit':
                                        $new_select = str_replace('name=""', 'name="_wpv_settings[filter_controls_type][]"', $select_submit);
                                        $show_edit = ' style="display:none" ';
                                        $filter = __('Submit button', 'wpv-views');
                                        break;
                                        
                                }
                                $new_select = str_replace('option value="' . $view_settings['filter_controls_type'][$i] . '"',
                                                          'option value="' . $view_settings['filter_controls_type'][$i] . '" selected="selected" ',
                                                          $new_select);
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="_wpv_settings[filter_controls_enable][]" <?php echo $view_settings['filter_controls_enable'][$i] ? 'checked="checked"' : ''; ?> />
                                        <input type="hidden" name="_wpv_settings[filter_controls_enable][]" value="0" />
                                        <input type="hidden" name="_wpv_settings[filter_controls_param][]" value="<?php echo $view_settings['filter_controls_param'][$i]; ?>" />
                                        <input type="hidden" name="_wpv_settings[filter_controls_mode][]" value="<?php echo $view_settings['filter_controls_mode'][$i]; ?>" />
                                    </td>
                                    <td>
                                        <input type="hidden" name="_wpv_settings[filter_controls_field_name][]" value="<?php echo sanitize_text_field($view_settings['filter_controls_field_name'][$i]); ?>" /><span><?php echo sanitize_text_field($filter); ?></span>
                                    </td>
                                    <td><input type="text" width="100%" name="_wpv_settings[filter_controls_label][]" value="<?php echo $view_settings['filter_controls_label'][$i]; ?>" /></td>
                                    <td><?php echo $new_select; ?></td>
                                    <td><input type="hidden" name="_wpv_settings[filter_controls_values][]" value="<?php echo htmlspecialchars($view_settings['filter_controls_values'][$i], ENT_QUOTES); ?>" /><input type="button" value="<?php _e('Edit', 'wpv-views'); ?>" class="button-secondary" <?php echo $show_edit; ?> /></td>
                                    <td width="16px"><img src="<?php echo WPV_URL; ?>/res/img/move.png" class="move" style="cursor: move;" /></td>
                                </tr>
                        <?php }
                        }
                        
                        ?>
                    
                </tbody>
            </table>
            
            <br />
            <input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="wpv_filter_controls_edit_ok()"/>
            <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_filter_controls_edit_cancel()"/>
            
            <br />
            <br />
        </div>
    </div>
    <?php
    
}

/**
 * Initialize any url param controls that haven't been set already
 * This is for Views that were created before we had front end filters.
 *
 */

function _wpv_initialize_url_param_controls($view_settings) {
    
    if (function_exists('wpcf_admin_fields_get_fields')) {
        $fields = wpcf_admin_fields_get_fields();
    } else {
        $fields = array();
    }
    
    if (!isset($view_settings['filter_controls_param'])) {
        $view_settings['filter_controls_field_name'] = array();
        $view_settings['filter_controls_param'] = array();
        $view_settings['filter_controls_enable'] = array();
        $view_settings['filter_controls_label'] = array();
        $view_settings['filter_controls_values'] = array();
        $view_settings['filter_controls_type'] = array();
        $view_settings['filter_controls_mode'] = array();
    }

    $url_params = wpv_custom_fields_get_url_params($view_settings);
    $url_params = array_merge($url_params, wpv_taxonomy_get_url_params($view_settings));
    $search_param = wpv_search_get_url_params($view_settings);
    $url_params = array_merge($url_params, $search_param);
    
    foreach($url_params as $url_param) {
        // see if it's already set
        $exists = false;
        foreach($view_settings['filter_controls_param'] as $param) {
            if ($param == $url_param['param']) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            // Doesn't exist so we add the control.
            
            $view_settings['filter_controls_field_name'][] = $url_param['name'];
            $view_settings['filter_controls_param'][] = $url_param['param'];
            $view_settings['filter_controls_enable'][] = 0;
            
            $label = $url_param['param'];
            $type = 'text';
            switch ($url_param ['mode']) {
                case 'cf':
                    foreach ($fields as $field) {
                        if ($url_param['name'] == wpcf_types_get_meta_prefix($field) . $field['slug']) {
                            $label = $field['name'];
                            $type = 'types-auto';
                            break;
                        }
                    }
                    break;
                
                case 'tax':
                    $label = $url_param['cat']->labels->name;
                    break;
                
                case 'search':
                    $label = $url_param['name'];
                    break;
            }
            
            $view_settings['filter_controls_label'][] = $label;
            
            $view_settings['filter_controls_values'][] = '';
            $view_settings['filter_controls_type'][] = $type;
            
            $view_settings['filter_controls_mode'][] = $url_param['mode'];
        }
    }
    
    return $view_settings;
}

add_filter('wpv_view_settings_save', 'wpv_filter_controls_save');

function wpv_filter_controls_save($view_settings) {
    
    if (isset($view_settings['filter_controls_enable'])) {

        // determine which items are checked.
    
        $result = array();
        
        $enabled = $view_settings['filter_controls_enable'];
        $skip_next = false;
        foreach($enabled as $enable) {
            if (!$skip_next) {
                if ($enable != '0') {
                    $result[] = true;
                    $skip_next = true;
                } else {
                    $result[] = false;
                    $skip_next = false;
                }
            } else {
                $skip_next = false;
            }
        }
        
        $view_settings['filter_controls_enable']= $result;
    }
    
    return $view_settings;
}


function wpv_ajax_wpv_view_form_popup() {
    
    global $wpdb;
    
    if (wp_verify_nonce($_GET['_wpnonce'], 'wpv_editor_callback')) {

        $view_id = $_GET['view_id'];
        
        $title = $wpdb->get_var("SELECT post_title FROM {$wpdb->posts} WHERE ID={$view_id}");
        
        // Find the posts that use this view
        
        $posts = $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} WHERE post_content LIKE '%name=\"{$title}\"%' AND post_type NOT IN ('revision')");
        
 
        wp_enqueue_script('jquery');

        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
            <head>
                <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
                <title></title>
                <?php
                if (wpcf_compare_wp_version('3.2.1', '<=')) {
                    wp_admin_css('global');
                }
                wp_admin_css();
                wp_admin_css('colors');
                wp_admin_css('ie');
                do_action('admin_print_styles');
                do_action('admin_print_scripts');
    
                ?>
                <style type="text/css">
                    html { height: auto; }
                </style>
            </head>
            <body style="padding: 20px;">
                <?php

                echo sprintf(__('Insert the filter section from View - %s', 'wpv-views'), '<strong>' . $title . '</strong>');

                ?>
                <input type="hidden" value="<?php echo $view_id; ?>" id="wpv_view_id">
                <br />
                
                <br />
                
                <?php echo __('When the form is submitted, go to this post:', 'wpv-views'); ?>
                <select id="wpv_filter_form_target" />
                    
                    <option value="0">None</option>
                    <?php
                        $first = true;
                        foreach($posts as $post) {
                            $post_title = $post->post_title;
                            if ($post_title == '') {
                                $post_title = $post->ID;
                            }
                            if ($first) {
                                echo '<option value="' . $post->ID . '" selected="selected" >' . $post->post_title . '</option>';
                            } else {
                                echo '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
                            }
                            $first = false;
                        }
                    
                    ?>
                
                </select>
                
                <br />
                <br />
                
                <?php
                    if (count($post) == 0) {
                        
                        echo '<strong>' . __('No target posts were found that use this View', 'wpv-views') . '</strong>';
                        echo '<br /><br />';
                    }
                ?>
        
                <input type="button" class="button-secondary" value="<?php echo __('Insert shortcode', 'wpv-views'); ?>" onclick="wpv_insert_form_shortcode()" />
                
                <script type="text/javascript">
                    //<![CDATA[
                    function wpv_insert_form_shortcode() {
                        
                        var data = '&action=wpv_insert_form_shortcode'
                        data += '&_wpnonce=<?php echo wp_create_nonce('wpv_editor_callback'); ?>';
                        data += '&view_id=' + jQuery('#wpv_view_id').val();
                        data += '&target=' + jQuery('#wpv_filter_form_target').val();
                        
                        jQuery.ajaxSetup({async:false});
                        jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
                            jQuery('body').append(response);
                            
                        });
                        
                    }
                    
                    //]]>
                </script>
                    
                
            </body>
        </html>
        
        <?php

    }        
    die();
}

function wp_ajax_wpv_insert_form_shortcode() {
    if (wp_verify_nonce($_POST['_wpnonce'], 'wpv_editor_callback')) {

        global $wpdb;
        
        $view_id = $_POST['view_id'];
        $target = $_POST['target'];
        $title = $wpdb->get_var("SELECT post_title FROM {$wpdb->posts} WHERE ID={$view_id}");
    
        editor_admin_popup_insert_shortcode_js('[wpv-form-view name="' . $title . '" target_id="' . $target . '"]');
    
    }
    die();
}