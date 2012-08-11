<?php
/*
 * Conditional display code.
 */
require_once WPCF_EMBEDDED_ABSPATH . '/includes/conditional-display.php';

add_filter('wpcf_form_field', 'wpcf_cd_form_field_filter', 10, 2);
add_filter('wpcf_field_pre_save', 'wpcf_cd_field_pre_save_filter');
add_filter('wpcf_fields_form_additional_filters',
        'wpcf_cd_fields_form_additional_filters', 10, 2);
add_action('wpcf_save_group', 'wpcf_cd_save_group_action');
add_action('admin_footer', 'wpcf_cd_admin_form_js');

/**
 * Filters group field form.
 * 
 * @param type $form
 * @param type $data
 * @return type 
 */
function wpcf_cd_form_field_filter($form, $data) {
    if (defined('DOING_AJAX') && isset($_SERVER['HTTP_REFERER'])) {
        parse_str($_SERVER['HTTP_REFERER'], $vars);
    } else if (isset($_GET['group_id'])) {
        $vars = array();
        $vars['group_id'] = $_GET['group_id'];
    }
    if (!isset($vars['group_id'])) {
        return $form + array(
            'cd_not_available' => array(
                '#type' => 'markup',
                '#markup' => '<p>' . __('You will be able to set conditional field display once this group is saved.',
                        'wpcf') . '</p>',
            ),
        );
    }
    $form = $form + wpcf_cd_admin_form_filter($data);
    return $form;
}

/**
 * Field pre-save filter.
 * 
 * @param array $data
 * @return array 
 */
function wpcf_cd_field_pre_save_filter($data) {
    if (empty($data['conditional_display'])) {
        $data['conditional_display'] = array();
    }
    return $data;
}

/**
 * Conditional display form.
 * 
 * @param type $data
 * @param type $group
 * @return type 
 */
function wpcf_cd_admin_form_filter($data, $group = false) {
    if ($group) {
        $name = 'wpcf[group][conditional_display]';
    } else {
        $name = 'wpcf[fields][' . $data['id'] . '][conditional_display]';
    }
    $form = array();
    if (!$group) {
        $form['cd'] = array(
            '#type' => 'fieldset',
            '#title' => __('Conditional display', 'wpcf'),
            '#collapsed' => true,
            '#id' => $data['id'] . '_conditional_display',
            '#attributes' => array('class' => 'wpcf-cd-fieldset'),
        );
    } else {
        $form['cd']['wrap'] = array(
            '#type' => 'markup',
            '#markup' => '<strong>' . __('Data-dependent display filters',
                    'wpcf') . '</strong><br />'
            . __("Specify additional filters that control this group's display, based on values of custom fields.",
                    'wpcf')
            . '<br /><a class="button-secondary" onclick="jQuery(this).css(\'visibility\',\'hidden\').next().slideToggle();" style="line-height: 30px;" href="javascript:void(0);">'
            . __('Edit', 'wpcf') . '</a><div id="wpcf-cd-group" style="display:none;">',
        );
    }
    if (!empty($data['data']['conditional_display']['conditions'])) {
        $conditions = $data['data']['conditional_display']['conditions'];
        $count = count($conditions);
    } else {
        $count = 1;
    }
    $add = $group ? 'true' : 'false';
    $form['cd']['add'] = array(
        '#type' => 'markup',
        '#markup' => '<br /><a class="wpcf-ajax-link button-secondary" onclick="wpcfCdAddCondition(jQuery(this),' . $add . ');" href="javascript:void(0);">'
        . __('Add condition', 'wpcf') . '</a><br /><br /><div class="wpcf-cd-entries">',
    );
    if (!empty($data['data']['conditional_display']['conditions'])) {
        foreach ($data['data']['conditional_display']['conditions'] as $key => $condition) {
            $form['cd'] += wpcf_cd_admin_form_single_filter($data, $condition,
                    $key, $group);
        }
    }
    $form['cd']['add_close'] = array(
        '#type' => 'markup',
        '#markup' => '</div>',
    );
    $form['cd']['relation'] = array(
        '#type' => 'radios',
        '#name' => $name . '[relation]',
        '#options' => array(
            'AND' => array(
                '#title' => 'AND',
                '#attributes' => array('onclick' => 'wpcfCdCreateSummary(\'' . md5($data['id']) . '_cd_summary\')'),
                '#inline' => true,
                '#value' => 'AND',
                '#after' => '<br />',
            ),
            'OR' => array(
                '#title' => 'OR',
                '#attributes' => array('onclick' => 'wpcfCdCreateSummary(\'' . md5($data['id']) . '_cd_summary\')'),
                '#inline' => true,
                '#value' => 'OR'
            ),
        ),
        '#default_value' => isset($data['data']['conditional_display']['relation']) ? $data['data']['conditional_display']['relation'] : 'AND',
        '#inline' => true,
        '#before' => '<br /><div class="wpcf-cd-relation" style="display:none;">',
        '#after' => '</div><br />',
    );
    $form['cd']['toggle_open'] = array(
        '#type' => 'markup',
        '#markup' => '<div class="toggle-cd" style="display:none;">',
    );
    $prepopulate = !empty($data['data']['conditional_display']['custom']) ? ' jQuery(\'#' . md5($data['id']) . '_cd_summary\').val();' : ' wpcfCdCreateSummary(\'' . md5($data['id']) . '_cd_summary\');';
    $form['cd']['customize_display_logic_link'] = array(
        '#type' => 'markup',
        '#markup' => '<a href="javascript:void(0);" class="button-secondary wpcf-cd-enable-custom-mode" onclick="window.wpcfCdState_' . md5($data['id'])
        . ' = jQuery(\'#' . md5($data['id']) . '_cd_summary\').val();' . $prepopulate . ' jQuery(\'#' . md5($data['id']) . '_cd_summary\').parent().slideDown(); jQuery(this).hide().next().show();wpcfCdCheckDateCustomized(jQuery(this));">'
        . __('Customize the display logic', 'wpcf')
        . '</a>',
    );
    $form['cd']['revert_display_logic_link'] = array(
        '#type' => 'markup',
        '#markup' => '<a href="javascript:void(0);" class="button-secondary wpcf-cd-enable-custom-mode hidden" onclick="jQuery(\'#' . md5($data['id']) . '_cd_summary\').parent().slideUp().find(\'.checkbox\').removeAttr(\'checked\'); jQuery(this).hide().prev().show();">'
        . __('Go back to simple logic', 'wpcf')
        . '</a>',
    );
    $form['cd']['toggle_open_area'] = array(
        '#type' => 'markup',
        '#markup' => '<div class="area-toggle-cd" style="margin-top:10px;display:none;">',
    );
    $form['cd']['custom'] = array(
        '#type' => 'textarea',
        '#name' => $name . '[custom]',
        '#title' => __('Customize conditions', 'wpcf'),
        '#id' => md5($data['id']) . '_cd_summary',
        '#after' => '<br /><a href="javascript:void(0);" onclick="wpcfCdCreateSummary(\''
        . md5($data['id']) . '_cd_summary\');">'
        . __('Re-read structure', 'wpcf') . '</a><br />',
        '#inline' => true,
        '#value' => isset($data['data']['conditional_display']['custom']) ? $data['data']['conditional_display']['custom'] : '',
    );
    $form['cd']['custom_use'] = array(
        '#type' => 'checkbox',
        '#name' => $name . '[custom_use]',
        '#title' => __('Use customized conditions', 'wpcf'),
        '#inline' => true,
        '#default_value' => isset($data['data']['conditional_display']['custom_use']),
        '#after' => '',
    );
    $form['cd']['date_notice'] = array(
        '#type' => 'markup',
        '#markup' => '<div style="display:none; margin-top:15px;" class="wpcf-cd-notice-date">'
        . sprintf(__('%sDates can be entered using the date filters &raquo;%s', 'wpcf'), '<a href="http://wp-types.com/documentation/user-guides/date-filters/" target="_blank">', '</a>') . '</div>',
    );
    $form['cd']['apply_display_logic_link'] = array(
        '#type' => 'markup',
        '#markup' => '<br /><br /><a href="javascript:void(0);" class="button-primary" onclick="window.wpcfCdState_' . md5($data['id'])
        . ' = jQuery(\'#' . md5($data['id']) . '_cd_summary\').parent().slideUp().prev().hide().prev().show();">'
        . __('Apply', 'wpcf')
        . '</a>&nbsp&nbsp;',
    );
    $form['cd']['cancel_display_logic_link'] = array(
        '#type' => 'markup',
        '#markup' => '<a href="javascript:void(0);" class="button-primary" onclick="jQuery(\'#' . md5($data['id']) . '_cd_summary\').val(window.wpcfCdState_' . md5($data['id']) . ').parent().slideUp().prev().hide().prev().show();">'
        . __('Cancel', 'wpcf')
        . '</a>',
    );
    $form['cd']['toggle_close'] = array(
        '#type' => 'markup',
        '#markup' => '</div>',
    );
    $form['cd']['toggle_close_area'] = array(
        '#type' => 'markup',
        '#markup' => '</div>',
    );
    $form['cd']['count'] = array(
        '#type' => 'hidden',
        '#name' => '_wpcf_cd_count_' . $data['id'],
        '#value' => $count,
    );
    if ($group) {
        $form['cd']['wrap_close'] = array(
            '#type' => 'markup',
            '#markup' => '<br /><a class="button-primary" onclick="jQuery(this).parent().slideUp().prev().css(\'visibility\',\'visible\');" style="line-height: 30px;" href="javascript:void(0);">'
            . __('OK', 'wpcf') . '</a></div>',
        );
    }
    return $group ? $form['cd'] : $form;
}

/**
 * Single condition form elements.
 * 
 * @param type $data
 * @param type $condition
 * @param type $key
 * @return string 
 */
function wpcf_cd_admin_form_single_filter($data, $condition, $key = null,
        $group = false, $force_multi = false) {
    if ($group) {
        $name = 'wpcf[group][conditional_display]';
    } else {
        $name = 'wpcf[fields][' . $data['id'] . '][conditional_display]';
    }
    $group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : false;
    if ($group_id && !$group) {// Allow group to use other fields
        $fields = wpcf_admin_fields_get_fields_by_group($group_id);
    } else {
        $fields = wpcf_admin_fields_get_fields();
    }
    $options = array();
    foreach ($fields as $field_id => $field) {
        if (!$group && $data['id'] == $field_id) {
            continue;
        }
        if (wpcf_admin_is_repetitive($field)) {
            continue;
        }
        $options[$field_id] = array(
            '#value' => $field_id,
            '#title' => $field['name'],
            '#attributes' => array('class' => 'wpcf-conditional-select-' . $field['type']),
        );
    }
    if (!$group && empty($options)) {
        return array(
            'cd' => array(
                '#type' => 'markup',
                '#markup' => '<p>' . __('You will be able to set conditional field display when you save more fields.',
                        'wpcf') . '</p>',
            )
        );
    }
    $id = !is_null($key) ? $key : strval('condition_' . mt_rand());
    $form = array();
    $before = '<div class="wpcf-cd-entry"><br />';
    $form['cd']['field_' . $id] = array(
        '#type' => 'select',
        '#name' => $name . '[conditions][' . $id . '][field]',
        '#options' => $options,
        '#inline' => true,
        '#before' => $before,
        '#default_value' => isset($condition['field']) ? $condition['field'] : null,
        '#attributes' => array('class' => 'wpcf-cd-field'),
    );
    $form['cd']['operation_' . $id] = array(
        '#type' => 'select',
        '#name' => $name . '[conditions][' . $id . '][operation]',
        '#options' => array_flip(wpcf_cd_admin_operations()),
        '#inline' => true,
        '#default_value' => isset($condition['operation']) ? $condition['operation'] : null,
        '#attributes' => array('class' => 'wpcf-cd-operation'),
    );
    $form['cd']['value_' . $id] = array(
        '#type' => 'textfield',
        '#name' => $name . '[conditions][' . $id . '][value]',
        '#options' => wpcf_cd_admin_operations(),
        '#inline' => true,
        '#value' => isset($condition['value']) ? $condition['value'] : '',
        '#attributes' => array('class' => 'wpcf-cd-value'),
    );
    $form['cd']['remove_' . $id] = array(
        '#type' => 'button',
        '#name' => 'remove',
        '#value' => __('Remove condition', 'wpcf'),
        '#attributes' => array('onclick' => 'wpcfCdRemoveCondition(jQuery(this));', 'class' => 'wpcf-add-condition'),
        '#before' => '<br />',
        '#after' => '</div>',
    );
    return $form['cd'];
}

/**
 * Group coditional display filter.
 * 
 * @param type $filters
 * @param type $update
 * @return type 
 */
function wpcf_cd_fields_form_additional_filters($filters, $update) {
    $data = array();
    $data['id'] = !empty($update) ? $update['name'] : mt_rand();
    if ($update) {
        $data['data']['conditional_display'] = get_post_meta($update['id'],
                '_wpcf_conditional_display', true);
    } else {
        $data['data']['conditional_display'] = array();
    }
    $filters = $filters + wpcf_cd_admin_form_filter($data, true);
    return $filters;
}

/**
 * Save group action hook.
 * 
 * @param type $group 
 */
function wpcf_cd_save_group_action($group) {
    if (!empty($group['conditional_display'])) {
        update_post_meta($group['id'], '_wpcf_conditional_display',
                $group['conditional_display']);
    } else {
        update_post_meta($group['id'], '_wpcf_conditional_display', array());
    }
}

/**
 * Inline JS.
 */
function wpcf_cd_admin_form_js() {

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('.wpcf-cd-fieldset, #wpcf-cd-group').each(function(){
                if (jQuery(this).find('.wpcf-cd-entry').length > 1) {
                    jQuery(this).find('.toggle-cd').show();
                    jQuery(this).find('.wpcf-cd-relation').show();
                }
            });
        });
        function wpcfCdCreateSummary(id) {
            var condition = '';
            var skip = true;
            jQuery('#'+id).parents('fieldset, #wpcf-cd-group').find('.wpcf-cd-entry').each(function(){
                //                if (jQuery(this).parent().find('.wpcf-cd-relation').length > 0) {
                if (!skip) {
                    condition += jQuery(this).parent().parent().find('input[type=radio]:checked').val() + ' ';
                }
                skip = false;
                //                }
                condition += '($'+jQuery(this).find('.wpcf-cd-field').val();
                condition += ' ' + jQuery(this).find('.wpcf-cd-operation').val();
                condition += ' ' + jQuery(this).find('.wpcf-cd-value').val() + ') ';
            });
            jQuery('#'+id).val(condition);
        }
        function wpcfCdAddCondition(object, isGroup) {
            if (object.parent().parent().find('.wpcf-cd-entry').length > 0) {
                object.parent().parent().find('.toggle-cd').show();
                object.parent().parent().find('.wpcf-cd-relation').show();
            }
            var url = '<?php
    $group_id = isset($_GET['group_id']) ? '&group_id=' . $_GET['group_id'] : '';
    echo admin_url('admin-ajax.php?action=wpcf_ajax&wpcf_action=add_condition'
            . $group_id . '&_wpnonce='
            . wp_create_nonce('add_condition'));

    ?>&count='+object.parent().parent().find('input[type=hidden]').val();
            if (isGroup) {
                url += '&group=1';
            } else {
                url += '&field='+object.parent().parent().attr('id');
            }
            jQuery.get(url, function(data) {
                if (typeof data.output != 'undefined') {
                    object.parent().find('.wpcf-cd-entries').append(data.output);
                    var count = object.parent().find('input[type=hidden]').val();
                    object.parent().find('input[type=hidden]').val(parseInt(count)+1);
                }
            }, "json");
        }
        function wpcfCdRemoveCondition(object) {
            object.parent().fadeOut(function(){jQuery(this).remove();});
            var count = object.parent().parent().parent().find('input[type=hidden]').val();
            object.parent().parent().parent().find('input[type=hidden]').val(parseInt(count)-1);
            if (object.parent().parent().find('.wpcf-cd-entry').length < 3) {
                var customConditions = object.parent().parent().parent().find('.toggle-cd');
                customConditions.hide().find('.checkbox').removeAttr('checked');
                customConditions.find('.textarea').val('');
                object.parent().parent().parent().find('.wpcf-cd-relation').hide();
            }
        }
    </script>
    <?php
}