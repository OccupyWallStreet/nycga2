<?php
/*
 * Conditional display embedded code.
 */
add_filter('wpcf_post_edit_field', 'wpcf_cd_post_edit_field_filter', 10, 4);
add_filter('wpcf_post_groups', 'wpcf_cd_post_groups_filter', 10, 3);

if (!function_exists('wplogger')) {
    require_once WPCF_EMBEDDED_ABSPATH . '/common/wplogger.php';
}
if (!function_exists('wpv_filter_parse_date')) {
    require_once WPCF_EMBEDDED_ABSPATH . '/common/wpv-filter-date-embedded.php';
}

/**
 * Filters groups on post edit page.
 * 
 * @param type $groups
 * @param type $post
 * @return type 
 */
function wpcf_cd_post_groups_filter($groups, $post, $context) {
    if ($context != 'group') {
        return $groups;
    }
    foreach ($groups as $key => &$group) {
        $meta_conditional = !isset($group['conditional_display']) ? get_post_meta($group['id'],
                        '_wpcf_conditional_display', true) : $group['conditional_display'];
        if (!empty($meta_conditional['conditions'])) {
            $group['conditional_display'] = $meta_conditional;
            add_action('admin_head', 'wpcf_cd_add_group_js');
            if (empty($post->ID)) {
                $group['_conditional_display'] = 'failed';
                continue;
            }
            $passed = true;
            if (isset($group['conditional_display']['custom_use'])) {
                if (empty($group['conditional_display']['custom'])) {
                    $group['_conditional_display'] = 'failed';
                    continue;
                }

                $evaluate = trim(stripslashes($group['conditional_display']['custom']));
                // Check dates
                $evaluate = wpv_filter_parse_date($evaluate);
                // Add quotes = > < >= <= === <> !==
                $strings_count = preg_match_all('/[=|==|===|<=|<==|<===|>=|>==|>===|\!===|\!==|\!=|<>]\s(?!\$)(\w*)[\)|\$|\W]/',
                        $evaluate, $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $temp_match) {
                        $temp_replace = is_numeric($temp_match) ? $temp_match : '\'' . $temp_match . '\'';
                        $evaluate = str_replace(' ' . $temp_match . ')',
                                ' ' . $temp_replace . ')', $evaluate);
                    }
                }
                preg_match_all('/\$([^\s]*)/',
                        $group['conditional_display']['custom'], $matches);
                if (empty($matches)) {
                    $group['_conditional_display'] = 'failed';
                    continue;
                }
                $fields = array();
                foreach ($matches[1] as $key => $field_name) {
                    $fields[$field_name] = wpcf_types_get_meta_prefix(wpcf_admin_fields_get_field($field_name)) . $field_name;
                    wpcf_cd_add_group_js('add', $field_name, '', '',
                            $group['id']);
                }
                $fields['evaluate'] = $evaluate;
                $check = wpv_condition($fields);
                $passed = $check;
                if (!is_bool($check)) {
                    $passed = false;
                    $group['_conditional_display'] = 'failed';
                } else if ($check) {
                    $group['_conditional_display'] = 'passed';
                } else {
                    $group['_conditional_display'] = 'failed';
                }
            } else {
                $passed_all = true;
                $passed_one = false;
                foreach ($group['conditional_display']['conditions'] as $condition) {
                    // Load field
                    $field = wpcf_admin_fields_get_field($condition['field']);
                    wpcf_fields_type_action($field['type']);

                    wpcf_cd_add_group_js('add', $condition['field'],
                            $condition['value'], $condition['operation'],
                            $group['id']);
                    $value = get_post_meta($post->ID,
                            wpcf_types_get_meta_prefix($condition['field']) . $condition['field'],
                            true);
                    $value = apply_filters('wpcf_conditional_display_compare_meta_value',
                            $value, $condition['field'],
                            $condition['operation'], $key, $post);
                    $condition['value'] = apply_filters('wpcf_conditional_display_compare_condition_value',
                            $condition['value'], $condition['field'],
                            $condition['operation'], $key, $post);
                    $check = wpcf_cd_admin_compare($condition['operation'],
                            $value, $condition['value']);
                    if (!$check) {
                        $passed_all = false;
                    } else {
                        $passed_one = true;
                    }
                }
                if (!$passed_all && $group['conditional_display']['relation'] == 'AND') {
                    $passed = false;
                }
                if (!$passed_one && $group['conditional_display']['relation'] == 'OR') {
                    $passed = false;
                }
            }
            if (!$passed) {
                $group['_conditional_display'] = 'failed';
            } else {
                $group['_conditional_display'] = 'passed';
            }
        }
    }
    return $groups;
}

/**
 * Checks if there is conditional display.
 * 
 * @param type $element
 * @param type $field
 * @param type $post
 * @return type 
 */
function wpcf_cd_post_edit_field_filter($element, $field, $post,
        $context = 'group') {
    if (defined('DOING_AJAX') && $context == 'repetitive') {
        return $element;
    }
    if (!empty($field['data']['conditional_display']['conditions'])) {
        add_action('admin_head', 'wpcf_cd_add_field_js');
        $passed = true;
        if (empty($post->ID)) {
            $passed = false;
        } else if (isset($field['data']['conditional_display']['custom_use'])) {
            if (empty($field['data']['conditional_display']['custom'])) {
                return array();
            }
            $evaluate = trim(stripslashes($field['data']['conditional_display']['custom']));
            // Check dates
            $evaluate = wpv_filter_parse_date($evaluate);
            // Add quotes = > < >= <= === <> !==
            $strings_count = preg_match_all('/[=|==|===|<=|<==|<===|>=|>==|>===|\!===|\!==|\!=|<>]\s(?!\$)(\w*)[\)|\$|\W]/',
                    $evaluate, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $temp_match) {
                    $temp_replace = is_numeric($temp_match) ? $temp_match : '\'' . $temp_match . '\'';
                    $evaluate = str_replace(' ' . $temp_match . ')',
                            ' ' . $temp_replace . ')', $evaluate);
                }
            }
            preg_match_all('/\$([^\s]*)/',
                    $field['data']['conditional_display']['custom'], $matches);
            if (empty($matches)) {
                $passed = false;
            } else {
                $fields = array();
                foreach ($matches[1] as $key => $field_name) {
                    $fields[$field_name] = wpcf_types_get_meta_prefix(wpcf_admin_fields_get_field($field_name)) . $field_name;
                }
                $fields['evaluate'] = $evaluate;
                $check = wpv_condition($fields);
                if (!is_bool($check)) {
                    $passed = false;
                } else {
                    $passed = $check;
                }
            }
        } else {
            $passed_all = true;
            $passed_one = false;
            foreach ($field['data']['conditional_display']['conditions'] as $condition) {
                // This is malformed condition and should be treated as passed
                // @TODO Approve it
                if (!isset($condition['field']) || !isset($condition['operation'])
                        || !isset($condition['value'])) {
                    $passed_one = true;
                    continue;
                }
                $value = get_post_meta($post->ID,
                        wpcf_types_get_meta_prefix($condition['field']) . $condition['field'],
                        true);
                $value = apply_filters('wpcf_conditional_display_compare_meta_value',
                        $value, $condition['field'], $condition['operation'],
                        $field['slug'], $post);
                $condition['value'] = apply_filters('wpcf_conditional_display_compare_condition_value',
                        $condition['value'], $condition['field'],
                        $condition['operation'], $field['slug'], $post);
                $check = wpcf_cd_admin_compare($condition['operation'], $value,
                        $condition['value']);
                if (!$check) {
                    $passed_all = false;
                } else {
                    $passed_one = true;
                }
            }
            if (!$passed_all && $field['data']['conditional_display']['relation'] == 'AND') {
                $passed = false;
            }
            if (!$passed_one && $field['data']['conditional_display']['relation'] == 'OR') {
                $passed = false;
            }
        }
        if (!$passed) {
            $wrap = '<div class="wpcf-cd wpcf-cd-failed" style="display:none;">';
            $element['__wpcf_cd_status'] = 'failed';
        } else {
            $wrap = '<div class="wpcf-cd wpcf-cd-passed">';
            $element['__wpcf_cd_status'] = 'passed';
        }
        if (isset($element['#before'])) {
            $element['#before'] = $wrap . $element['#before'];
        } else {
            $element['#before'] = $wrap;
        }
        if (isset($element['#after'])) {
            $element['#after'] = $element['#after'] . '</div>';
        } else {
            $element['#after'] = '</div>';
        }
    }
    return $element;
}

/**
 * Operations.
 * 
 * @return type 
 */
function wpcf_cd_admin_operations() {
    return array(
        '=' => __('Equal to', 'wpcf'),
        '>' => __('Larger than', 'wpcf'),
        '<' => __('Less than', 'wpcf'),
        '>=' => __('Larger or equal to', 'wpcf'),
        '<=' => __('Less or equal to', 'wpcf'),
        '===' => __('Identical to', 'wpcf'),
        '<>' => __('Not identical to', 'wpcf'),
        '!==' => __('Strictly not equal', 'wpcf'),
//        'between' => __('Between', 'wpcf'),
    );
}

/**
 * Compares values.
 * 
 * @param type $operation
 * @return type 
 */
function wpcf_cd_admin_compare($operation) {
    $args = func_get_args();
    switch ($operation) {
        case '=':
            return $args[1] == $args[2];
            break;

        case '>':
            return intval($args[1]) > intval($args[2]);
            break;

        case '>=':
            return intval($args[1]) >= intval($args[2]);
            break;

        case '<':
            return intval($args[1]) < intval($args[2]);
            break;

        case '<=':
            return intval($args[1]) <= intval($args[2]);
            break;

        case '===':
            return $args[1] === $args[2];
            break;

        case '!==':
            return $args[1] !== $args[2];
            break;

        case '<>':
            return $args[1] <> $args[2];
            break;

        case 'between':
            return intval($args[1]) > intval($args[2]) && intval($args[1]) < intval($args[3]);
            break;

        default:
            break;
    }
    return true;
}

/**
 * JS for fields AJAX.
 */
function wpcf_cd_add_field_js() {

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('.wpcf-cd').each(function(){
                jQuery(this).parents('.inside').find(':input').each(function(){
                    if (jQuery(this).hasClass('wpcf-cd-binded')) {
                        return false;
                    }
                    jQuery(this).addClass('wpcf-cd-binded');
                    if (jQuery(this).hasClass('radio')
                        || jQuery(this).hasClass('checkbox')) {
                        jQuery(this).bind('click', function(){
                            wpcfCdVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val());
                        });
                    } else if (jQuery(this).hasClass('select')) {
                        jQuery(this).bind('change', function(){
                            wpcfCdVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val());
                        });
                    } else if (jQuery(this).hasClass('wpcf-datepicker')) {
                        jQuery(this).bind('wpcfDateBlur', function(){
                            wpcfCdVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val());
                        });
                    } else {
                        jQuery(this).bind('blur', function(){
                            wpcfCdVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val());
                        });
                    }
                });
                if (typeof adminpage !== 'undefined' && adminpage == 'post-new-php') {
                    wpcfCdVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val());
                }
            });
        });
                                                                                                                                        
        function wpcfCdVerify(object, name, value) {
            if (object.hasClass('wpcf-pr-binded')) {
                return false;
            }
            var form = object.parents('.inside').find(':input');
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                data: form.serialize()+'<?php echo '&action=wpcf_ajax&wpcf_action=cd_verify&_wpnonce=' . wp_create_nonce('cd_verify'); ?>',
                cache: false,
                beforeSend: function() {
                },
                success: function(data) {
                    if (data != null) {
                        if (typeof data.execute != 'undefined'
                            && (typeof data.wpcf_nonce_ajax_callback != 'undefined'
                            && data.wpcf_nonce_ajax_callback == wpcf_nonce_ajax_callback)) {
                            eval(data.execute);
                        }
                    }
                }
            });
        }
    </script>
    <?php
}

/**
 * Register JS for groups AJAX.
 * 
 * @staticvar array $conditions
 * @param type $call
 * @param type $field
 * @param type $value
 * @param type $condition
 * @param type $group_id
 * @return string 
 */
function wpcf_cd_add_group_js($call, $field = false, $value = false,
        $condition = false, $group_id = false) {
    static $conditions = array();
    if ($call == 'add') {
        $conditions[$field] = array(
            'value' => $value,
            'condition' => $condition,
            'group_id' => $group_id
        );
        return '';
    }
    wpcf_cd_add_group_js_render($conditions);
}

/**
 * JS for groups AJAX.
 * 
 * @param type $conditions 
 */
function wpcf_cd_add_group_js_render($conditions = array()) {

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
    <?php
    foreach ($conditions as $field => $data) {

        ?>
                    if (jQuery('[name="wpcf[<?php echo $field; ?>]"]').hasClass('radio')
                        || jQuery('[name="wpcf[<?php echo $field; ?>]"]').hasClass('checkbox')) {
                        jQuery('[name="wpcf[<?php echo $field; ?>]"]').bind('click', function(){
                            wpcfCdGroupVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val(), <?php echo $data['group_id']; ?>);
                        });
                    } else if (jQuery('[name="wpcf[<?php echo $field; ?>]"]').hasClass('select')) {
                        jQuery('[name="wpcf[<?php echo $field; ?>]"]').bind('change', function(){
                            wpcfCdGroupVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val(), <?php echo $data['group_id']; ?>);
                        });
                    } else if (jQuery('[name="wpcf[<?php echo $field; ?>]"]').hasClass('wpcf-datepicker')) {
                        jQuery('[name="wpcf[<?php echo $field; ?>]"]').bind('wpcfDateBlur', function(){
                            wpcfCdGroupVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val(), <?php echo $data['group_id']; ?>);
                        });
                    } else {
                        jQuery('[name="wpcf[<?php echo $field; ?>]"]').bind('blur', function(){
                            wpcfCdGroupVerify(jQuery(this), jQuery(this).attr('name'), jQuery(this).val(), <?php echo $data['group_id']; ?>);
                        });
                    }
        <?php
    }

    ?>
            jQuery('.wpcf-cd-group-failed').parents('.postbox').hide();
        });
                                                                                                                                        
        function wpcfCdGroupVerify(object, name, value, group_id) {
            var form = jQuery('#post');
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                data: form.serialize()+'&group_id='+group_id+'<?php echo '&action=wpcf_ajax&wpcf_action=cd_group_verify&_wpnonce=' . wp_create_nonce('cd_group_verify'); ?>',
                cache: false,
                beforeSend: function() {
                },
                success: function(data) {
                    if (data != null) {
                        if (typeof data.execute != 'undefined'
                            && (typeof data.wpcf_nonce_ajax_callback != 'undefined'
                            && data.wpcf_nonce_ajax_callback == wpcf_nonce_ajax_callback)) {
                            eval(data.execute);
                        }
                    }
                }
            });
        }
    </script>
    <?php
}

/**
 * Passes $_POST values for AJAX call.
 * 
 * @param type $null
 * @param type $object_id
 * @param type $meta_key
 * @param type $single
 * @return type 
 */
function wpcf_cd_meta_ajax_validation_filter($null, $object_id, $meta_key,
        $single) {
    $meta_key = str_replace('wpcf-', '', $meta_key);
    $field = wpcf_admin_fields_get_field($meta_key);
    if (isset($_POST['wpcf'][$meta_key]) && !empty($field) && $field['type'] == 'date') {
        $time = strtotime($_POST['wpcf'][$meta_key]);
        if ($time) {
            return $time;
        }
    }
    return isset($_POST['wpcf'][$meta_key]) ? $_POST['wpcf'][$meta_key] : '';
}

/**
 * Passes $_POST values for AJAX call.
 * 
 * @param type $null
 * @param type $object_id
 * @param type $meta_key
 * @param type $single
 * @return type 
 */
function wpcf_cd_pr_meta_ajax_validation_filter($null, $object_id, $meta_key,
        $single) {
    $field = wpcf_admin_fields_get_field($meta_key);
    if (isset($_POST['wpcf_post_relationship'][$meta_key]) && !empty($field) && $field['type'] == 'date') {
        $time = strtotime($_POST['wpcf_post_relationship'][$meta_key]);
        if ($time) {
            return $time;
        }
    }
    return isset($_POST['wpcf_post_relationship'][$meta_key]) ? $_POST['wpcf_post_relationship'][$meta_key] : '';
}