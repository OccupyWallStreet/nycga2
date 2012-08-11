<?php
/*
 * Edit access page.
 */

/**
 * Admin page form. 
 */
function wpcf_access_admin_edit_access($enabled = true) {
    $roles = get_editable_roles();
    $output = '';
    $output .= '<form id="wpcf_access_admin_form" method="post" action="">';

    // Types
    $types = get_option('wpcf-custom-types', array());

    // Merge with other types
    $settings_access = get_option('wpcf-access-types', array());
    $types_other = get_post_types(array('show_ui' => true), 'objects');
    foreach ($types_other as $type_slug => $type_data) {
        if (isset($types[$type_slug])) {
            continue;
        }
        $types[$type_slug] = (array) $type_data;
        unset($types[$type_slug]->labels, $types[$type_slug]->cap);
        $types[$type_slug]['labels'] = (array) $type_data->labels;
        $types[$type_slug]['cap'] = (array) $type_data->cap;
        if (isset($settings_access[$type_slug])) {
            $types[$type_slug]['_wpcf_access_capabilities'] = $settings_access[$type_slug];
        }
    }

    if (!empty($types)) {
        $output .= '<h3>' . __('Custom Types', 'wpcf') . '</h3>';
        foreach ($types as $type_slug => $type_data) {
            if ($type_data['public'] === 'hidden') {
                continue;
            }
            // Set data
            $mode = isset($type_data['_wpcf_access_capabilities']['mode']) ? $type_data['_wpcf_access_capabilities']['mode'] : 'predefined';
            // For built-in set default to 'not_managed'
            if (in_array($type_slug, array('post', 'page'))) {
                $mode = isset($type_data['_wpcf_access_capabilities']['mode']) ? $type_data['_wpcf_access_capabilities']['mode'] : 'not_managed';
            }
            $predefined_data = wpcf_access_types_caps_predefined();
            if (isset($type_data['_wpcf_access_capabilities']['predefined'])) {
                foreach ($type_data['_wpcf_access_capabilities']['predefined'] as $cap_slug => $cap_data) {
                    $predefined_data[$cap_slug]['role'] = $cap_data['role'];
                    $predefined_data[$cap_slug]['users'] = isset($cap_data['users']) ? $cap_data['users'] : array();
                }
            }
            $custom_data = wpcf_access_types_caps();
            if (isset($type_data['_wpcf_access_capabilities']['custom'])) {
                foreach ($type_data['_wpcf_access_capabilities']['custom'] as $cap_slug => $cap_data) {
                    $custom_data[$cap_slug]['role'] = $cap_data['role'];
                    $custom_data[$cap_slug]['users'] = isset($cap_data['users']) ? $cap_data['users'] : array();
                }
            }

            $output .= '<div class="wpcf-access-type-item">';
            $output .= '<strong>' . $type_data['labels']['name'] . '</strong>';
            if ($mode == 'not_managed') {
                $output .= '&nbsp;' . __('(not managed)', 'wpcf_access');
            }
            $output .= '&nbsp;<a href="javascript:void(0);" '
                    . 'class="button-secondary wpcf-access-edit-type">'
                    . __('Edit')
                    . '</a>';

            $output .= '<div class="wpcf-access-mode" style="display:none;">';
            $output .= '<p>' . __('How do you want to manage access control for this type?',
                            'wpcf_access') . '</p>';
            $output .= '<label><input type="radio" name="types['
                    . $type_slug . '][mode]" value="predefined" class="wpcf-access-switch-mode"';
            $output .= $mode == 'predefined' ? ' checked="checked" />' : ' />';
            $output .= __('Simple settings', 'wpcf_access') . '</label>&nbsp;';
            $output .= '<label><input type="radio" name="types['
                    . $type_slug . '][mode]" value="custom" class="wpcf-access-switch-mode"';
            $output .= $mode == 'custom' ? ' checked="checked" />' : ' />';
            $output .= __('Advanced settings', 'wpcf_access') . '</label>&nbsp;';
            $output .= '<label><input type="radio" name="types['
                    . $type_slug . '][mode]" value="not_managed" class="wpcf-access-switch-mode"';
            $output .= $mode == 'not_managed' ? ' checked="checked" />' : ' />';
            $output .= __('Not managed by Types Access', 'wpcf_access') . '</label>';

            $output .= '<div class="wpcf-access-mode-predefined"';
            $output .= $mode == 'predefined' ? '>' : ' style="display:none;">';
            $output .= wpcf_access_admin_predefined($type_slug, $roles,
                    'types[' . $type_slug . '][predefined]', $predefined_data,
                    $enabled);
            $output .= '</div>';

            $output .= '<div class="wpcf-access-mode-custom"';
            $output .= $mode == 'custom' ? '>' : ' style="display:none;">';
            $output .= wpcf_access_admin_edit_access_types_item($type_slug,
                    $roles, 'types[' . $type_slug . '][custom]', $custom_data,
                    $enabled);
            $output .= '</div>';

            $output .= '<div class="wpcf-access-mode-not_managed"';
            $output .= $mode == 'not_managed' ? '>' : ' style="display:none;">';
            $output .= '</div>';

            $output .= '<a href="javascript:void(0);" '
                    . 'class="button-primary wpcf-access-edit-type-done">'
                    . __('Done')
                    . '</a>';

            $output .= '</div><!-- wpcf-access-mode -->';
            $output .= '<div style="clear:both;"></div></div><!-- wpcf-access-type-item -->';
        }
    }

    // Taxonomies
    $taxonomies = get_option('wpcf-custom-taxonomies', array());

    // Merge with other taxonomies
    $settings_access = get_option('wpcf-access-taxonomies', array());
    $taxonomies_other = get_taxonomies(array('show_ui' => true), 'objects');
    foreach ($taxonomies_other as $tax_slug => $tax_data) {
        if (isset($taxonomies[$tax_slug])) {
            continue;
        }
        $taxonomies[$tax_slug] = (array) $tax_data;
        unset($taxonomies[$tax_slug]->labels, $taxonomies[$tax_slug]->cap);
        $taxonomies[$tax_slug]['labels'] = (array) $tax_data->labels;
        $taxonomies[$tax_slug]['cap'] = (array) $tax_data->cap;
        $taxonomies[$tax_slug]['supports'] = array_flip($tax_data->object_type);
        if (isset($settings_access[$tax_slug])) {
            $taxonomies[$tax_slug]['_wpcf_access_capabilities'] = $settings_access[$tax_slug];
        }
    }

    // See if taxonomies are shared between types with different settings
    if ($enabled) {
        $supports_check = array();
        foreach ($taxonomies as $tax_slug => $tax_data) {
            $mode = isset($tax_data['_wpcf_access_capabilities']['mode']) ? $tax_data['_wpcf_access_capabilities']['mode'] : 'follow';
            // Only check if in 'follow' mode
            if ($mode != 'follow' || empty($tax_data['supports'])) {
                continue;
            }
            foreach ($tax_data['supports'] as $supports_type => $true) {
                if (!isset($types[$supports_type]['_wpcf_access_capabilities']['mode'])) {
                    continue;
                }
                $mode = $types[$supports_type]['_wpcf_access_capabilities']['mode'];
                if (!isset($types[$supports_type]['_wpcf_access_capabilities'][$mode])) {
                    continue;
                }
                $supports_check[$tax_slug][md5($mode . serialize($types[$supports_type]['_wpcf_access_capabilities'][$mode]))][] = $types[$supports_type]['labels']['name'];
            }
        }
    }

    if (!empty($taxonomies)) {
        $output .= '<h3>' . __('Custom Taxonomies', 'wpcf') . '</h3>';
        foreach ($taxonomies as $tax_slug => $tax_data) {
            if ($tax_data['public'] === 'hidden') {
                continue;
            }
            // Set data
            $mode = isset($tax_data['_wpcf_access_capabilities']['mode']) ? $tax_data['_wpcf_access_capabilities']['mode'] : 'follow';
            // For built-in set default to 'not_managed'
            if (in_array($tax_slug, array('category', 'post_tag'))) {
                $mode = isset($tax_data['_wpcf_access_capabilities']['mode']) ? $tax_data['_wpcf_access_capabilities']['mode'] : 'not_managed';
            }
            $custom_data = wpcf_access_tax_caps();
            if (isset($tax_data['_wpcf_access_capabilities']['custom'])) {
                foreach ($tax_data['_wpcf_access_capabilities']['custom'] as $cap_slug => $cap_data) {
                    $custom_data[$cap_slug]['role'] = $cap_data['role'];
                    $custom_data[$cap_slug]['users'] = isset($cap_data['users']) ? $cap_data['users'] : array();
                }
            }

            $output .= '<div class="wpcf-access-type-item">';
            $output .= '<strong>' . $tax_data['labels']['name'] . '</strong>';
            if ($mode == 'not_managed') {
                $output .= '&nbsp;' . __('(not managed)', 'wpcf_access');
            }
            $output .= '&nbsp;<a href="javascript:void(0);" '
                    . 'class="button-secondary wpcf-access-edit-type">'
                    . __('Edit')
                    . '</a>';

            // Add warning if shared and settings are different
            if ($enabled && isset($supports_check[$tax_slug])
                    && count($supports_check[$tax_slug]) > 1) {
                $txt = array();
                foreach ($supports_check[$tax_slug] as $sc_tax_md5 => $sc_tax_md5_data) {
                    $txt = array_merge($txt, $sc_tax_md5_data);
                }
                $last_element = array_pop($txt);
                $warning = '<br /><img src="' . WPCF_EMBEDDED_RES_RELPATH . '/images/warning.png" style="position:relative;top:2px;" />&nbsp;' . sprintf(__('Notice: %s belongs to %s and %s, which have different access settings. The WordPress admin menu might appear confusing to some users.'),
                                $tax_data['labels']['name'],
                                implode(', ', $txt), $last_element);
                $output .= $warning;
            }

            $output .= '<div class="wpcf-access-mode" style="display:none;">';
            $output .= '<p>' . __('How do you want to manage access control for this taxonomy?',
                            'wpcf_access') . '</p>';
            $output .= '<label><input type="radio" name="tax['
                    . $tax_slug . '][mode]" value="follow" class="wpcf-access-switch-mode"';
            $output .= $mode == 'follow' ? ' checked="checked" />' : ' />';
            $output .= __('Same as parent post', 'wpcf_access') . '</label>&nbsp;';
            $output .= '<label><input type="radio" name="tax['
                    . $tax_slug . '][mode]" value="custom" class="wpcf-access-switch-mode"';
            $output .= $mode == 'custom' ? ' checked="checked" />' : ' />';
            $output .= __('Advanced settings', 'wpcf_access') . '</label>&nbsp;';
            $output .= '<label><input type="radio" name="tax['
                    . $tax_slug . '][mode]" value="not_managed" class="wpcf-access-switch-mode"';
            $output .= $mode == 'not_managed' ? ' checked="checked" />' : ' />';
            $output .= __('Not managed by Types Access', 'wpcf_access') . '</label>';

            $output .= '<div class="wpcf-access-mode-custom"';
            $output .= $mode == 'custom' ? '>' : ' style="display:none;">';
            $output .= wpcf_access_admin_edit_access_tax_item($tax_slug, $roles,
                    'tax[' . $tax_slug . '][custom]', $custom_data, $enabled);
            $output .= '</div>';

            $output .= '<br /><br /><a href="javascript:void(0);" '
                    . 'class="button-primary wpcf-access-edit-type-done">'
                    . __('Done')
                    . '</a>';

            $output .= '</div><!-- wpcf-access-mode -->';
            $output .= '<div style="clear:both;"></div></div><!-- wpcf-access-type-item -->';
        }
    }

    $output .= wpcf_access_admin_set_custom_roles_level_form($roles, $enabled);

    $output .= wp_nonce_field('wpcf-access-edit', '_wpnonce', true, false);
    if ($enabled) {
        $output .= get_submit_button();
    } else {
        $output .= get_submit_button(__('Save Changes'), 'primary', 'submit',
                true, array('disabled' => 'disabled'));
    }
    $output .= '</form>';
    echo $output;
}

/**
 * Renders dropdown with editable roles.
 * 
 * @param type $roles
 * @param type $name
 * @param type $data
 * @return string 
 */
function wpcf_access_admin_roles_dropdown($roles, $name, $data = array(),
        $dummy = false, $enabled = true) {
    $output = '';
    $output .= '<select name="' . $name . '"';
    $output .= isset($data['predefined']) ? 'class="wpcf-access-predefied-'
            . $data['predefined'] . '">' : '>';
    if ($dummy) {
        $output .= "\n\t<option";
        if (empty($data)) {
            $output .= ' selected="selected" disabled="disabled"';
        }
        $output .= ' value="0">' . $dummy . '</option>';
    }
    foreach ($roles as $role => $details) {
        $title = translate_user_role($details['name']);
        $output .= "\n\t<option";
        if (isset($data['role']) && $data['role'] == $role) {
            $output .= ' selected="selected"';
        }
        if (!$enabled) {
            $output .= ' disabled="disabled"';
        }
        $output .= ' value="' . esc_attr($role) . "\">$title</option>";
    }
    $output .= '</select>';
    return $output;
}

//function wpcf_access_admin_roles_checkboxes($roles, $name, $selected = array(),
//        $cap_data = array()) {
//    $output = '';
//    foreach ($roles as $role => $details) {
//        $title = translate_user_role($details['name']);
//        $output .= "\n\t<label><input type=\"checkbox\" name=\"" . $name . "[" . $role . "]\"";
//        if (in_array($role, $selected)) {
//            $output .= ' checked="checked"';
//        }
//        $output .= ' />' . $title . '</label><br />';
//    }
//    return $output;
//}

/**
 * Auto-suggest users search.
 * 
 * @param type $data
 * @param type $name
 * @return string 
 */
function wpcf_access_admin_users_form($data, $name, $enabled = true) {
    $output = '';
    $output .= '<div class="wpcf-access-add-user-wrapper">';
    $output .= '<input type="text" name="wpcf_access_ignore[add_user][]" value="'
            . __('Add specific user', 'wpcf_access')
            . '" class="wpcf-access-add-user" onclick="if (this.value == \''
            . __('Add specific user', 'wpcf_access')
            . '\') this.value = \'\';"';
    if (!$enabled) {
        $output .= ' disabled="disabled"';
    }
    $output .= ' />';
    $output .= '<div class="wpcf-access-user-list">';
    if ($enabled && isset($data['users']) && is_array($data['users'])) {
        foreach ($data['users'] as $user_id) {
            $user = get_userdata($user_id);
            if (!empty($user)) {
                $output .= '<div class="wpcf-access-remove-user-wrapper"><a href="javascript:void(0);" class="wpcf-access-remove-user">&nbsp;</a><input type="hidden" name="'
                        . $name . '[users][]" value="' . $user->user_login . '" />'
                        . $user->user_login . '</div>';
            }
        }
    }
    $output .= '</div><div style="clear:both;"></div></div>';
    return $output;
}

/**
 * Auto-suggest JS. 
 */
function wpcf_access_suggest_js() {

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery(".wpcf-access-add-user").each(function(){
                var object = jQuery(this);
                object.suggest("<?php echo admin_url('admin-ajax.php?action=wpcf_access_add_user&_wpnonce=' . wp_create_nonce('wpcf_access_add_user')); ?>",{
                    onSelect: function() {
                        if (object.parents('tr').find('.wpcf-access-user-list').find('input[value="' + this.value + '"]').length) {
                            object.val('');
                            return false;
                        }
                        var name = object.parents('tr').find('.wpcf-access-name-holder').val()+'[users][]';
                        object.parents('tr').find('.wpcf-access-user-list').append('<div class="wpcf-access-remove-user-wrapper"><a href="javascript:void(0);" class="wpcf-access-remove-user">&nbsp;</a><input type="hidden" name="'+name+'" value="' + this.value + '" />' + this.value + '</div>');
                        object.val('');
                    }});
            })});
    </script>
    <?php
}

/**
 * Renders pre-defined table.
 * 
 * @param type $type_slug
 * @param type $roles
 * @param type $name
 * @param type $data
 * @return string 
 */
function wpcf_access_admin_predefined($type_slug, $roles, $name, $data,
        $enabled = true) {
    $output = '';
    $output .= '<table class="wpcf-access-predefined-table">';
    foreach ($data as $mode => $mode_data) {
        if (!isset($mode_data['title']) || !isset($mode_data['role'])) {
            continue;
        }
        $output .= '<tr><td style="text-align:right;">' . $mode_data['title'] . '</td><td>';
        $output .= '<input type="hidden" class="wpcf-access-name-holder" name="wpcf_access_'
                . $type_slug . '_' . $mode . '" value="' . $name
                . '[' . $mode . ']" />';
        $output .= wpcf_access_admin_roles_dropdown($roles,
                $name . '[' . $mode . '][role]', $mode_data, false, $enabled);
        $output .= '</td><td>';
        $output .= wpcf_access_admin_users_form($mode_data,
                $name . '[' . $mode . ']', $enabled);
        $output .= '</td></tr>';
    }
    $output .= '</table>';
    return $output;
}

/**
 * Renders custom caps types table.
 * 
 * @param type $type_slug
 * @param type $roles
 * @param type $name
 * @param type $data
 * @return string 
 */
function wpcf_access_admin_edit_access_types_item($type_slug, $roles, $name,
        $data, $enabled = true) {
    $output = '';
    $output .= __('Set all capabilities to users of type:') . '&nbsp;'
            . wpcf_access_admin_roles_dropdown($roles,
                    'wpcf_access_bulk_set[' . $type_slug . ']', array(),
                    '-- ' . __('Choose user type', 'wpcf') . ' --', $enabled);
    $output .= wpcf_access_reset_button($type_slug, 'type', $enabled);
    $output .= '<table class="wpcf-access-caps-wrapper">';
    foreach ($data as $cap_slug => $cap_data) {
        $output .= '<tr><td style="text-align:right;">';
        $output .= $cap_data['title'] . '<td/><td>';
        $output .= wpcf_access_admin_roles_dropdown($roles,
                $name . '[' . $cap_slug . '][role]', $cap_data, false, $enabled);
        $output .= '<input type="hidden" class="wpcf-access-name-holder" name="wpcf_access_'
                . $type_slug . '_' . $cap_slug . '" value="' . $name
                . '[' . $cap_slug . ']" />';
        $output .= '</td><td>';
        $output .= wpcf_access_admin_users_form($cap_data,
                $name . '[' . $cap_slug . ']', $enabled);
        $output .= '</td></tr>';
    }
    $output .= '</td></tr></table>';
    return $output;
}

/**
 * Renders custom caps tax table.
 * 
 * @param type $type_slug
 * @param type $roles
 * @param type $name
 * @param type $data
 * @return string 
 */
function wpcf_access_admin_edit_access_tax_item($type_slug, $roles, $name,
        $data, $enabled = true) {
    $output = '';
    $output .= __('Set all capabilities to users of type:') . '&nbsp;'
            . wpcf_access_admin_roles_dropdown($roles,
                    'wpcf_access_bulk_set[' . $type_slug . ']', array(),
                    '-- ' . __('Choose user type', 'wpcf') . ' --', $enabled);
    $output .= wpcf_access_reset_button($type_slug, 'tax', $enabled);
    $output .= '<table class="wpcf-access-caps-wrapper">';
    foreach ($data as $cap_slug => $cap_data) {
        $output .= '<tr><td style="text-align:right;">';
        $output .= $cap_data['title'] . '<td/><td>';
        $output .= wpcf_access_admin_roles_dropdown($roles,
                $name . '[' . $cap_slug . '][role]', $cap_data, false, $enabled);
        $output .= '<input type="hidden" class="wpcf-access-name-holder" name="wpcf_access_'
                . $type_slug . '_' . $cap_slug . '" value="' . $name
                . '[' . $cap_slug . ']" />';
        $output .= '</td><td>';
        $output .= wpcf_access_admin_users_form($cap_data,
                $name . '[' . $cap_slug . ']', $enabled);
        $output .= '</td></tr>';
    }
    $output .= '</td></tr></table>';
    return $output;
}

/**
 * Reset caps button.
 * 
 * @param type $type_slug
 * @param type $type
 * @return string 
 */
function wpcf_access_reset_button($type_slug, $type = 'type', $enabled = true) {
    $output = '';
    $output .= '<a id="wpcf-access-reset-' . md5($type_slug . $type)
            . '" class="button-primary wpcf-access-reset"';
    if (!$enabled) {
        $output .= ' href="javascript:void(0);" disabled="disabled"';
    } else {
        $output .= ' href="' . admin_url('admin-ajax.php?action=wpcf_access_ajax_reset_to_default&amp;_wpnonce='
                        . wp_create_nonce('wpcf_access_ajax_reset_to_default') . '&amp;type='
                        . $type . '&amp;type_slug=' . $type_slug . '')
                . '" onclick="if (confirm(\''
                . addslashes(__('Are you sure? All permission settings for this type will change to their default values.',
                                'wpcf_access'))
                . '\')){ wpcfAccessReset(jQuery(this)); } return false;"';
    }
    $output .= '>' . __('Reset to defaults', 'wpcf_access') . '</a>';
    return $output;
}

/**
 * Custom roles form.
 * 
 * @param type $roles
 * @return string 
 */
function wpcf_access_admin_set_custom_roles_level_form($roles, $enabled = true) {
    $levels = wpcf_access_role_to_level_map();
    $builtin_roles = array();
    $custom_roles = array();
    $output = '';
    foreach ($roles as $role => $details) {
        if (isset($levels[$role])) {
            $level = intval(substr($levels[$role], 6));
            $builtin_roles[$level][$role] = $details;
            $builtin_roles[$level][$role]['name'] = translate_user_role($details['name']);
            $builtin_roles[$level][$role]['level'] = $level;
        } else {
            $compare = 'init';
            foreach ($details['capabilities'] as $capability => $true) {
                if (strpos($capability, 'level_') !== false && $true) {
                    $current_level = intval(substr($capability, 6));
                    if ($compare === 'init' || $current_level > intval($compare)) {
                        $compare = $current_level;
                    }
                }
            }
            $level = $compare !== 'init' ? $compare : 'not_set';
            $custom_roles[$level][$role] = $details;
            $custom_roles[$level][$role]['level'] = $compare !== 'init' ? $compare : 'not_set';
        }
    }
    if (empty($custom_roles)) {
        return '';
    }
    $output .= '<div id="wpcf-access-custom-roles-wrapper">';
    $output .= '<h3>' . __('Custom Roles', 'wpcf') . '</h3>';
    $output .= '<p>' . __('The user level determines which admin actions WordPress allows different kinds of users to perform.',
                    'wpcf_access') . '</p>';
    $output .= '<div id="wpcf-access-custom-roles-table-wrapper">';
    $output .= '<table cellpadding="10" cellspacing="0" class="wpcf-access-custom-roles-table"><tbody>';
    for ($index = 10; $index >= 0; $index--) {
        $level_empty = true;
        $row = '<tr><td><div class="wpcf-access-roles-level">'
                . sprintf(__('Level %d', 'wpcf_access'), $index)
                . '</div></td><td>';
        if (isset($builtin_roles[$index])) {
            $level_empty = false;
            foreach ($builtin_roles[$index] as $role => $details) {
                $row .= '<div class="wpcf-access-roles-builtin">'
                        . $details['name'] . '</div>';
            }
        }
        if (isset($custom_roles[$index])) {
            $level_empty = false;
            foreach ($custom_roles[$index] as $role => $details) {
                $dropdown = '<div class="wpcf-access-custom-roles-select-wrapper">'
                        . '<select name="roles[' . $role
                        . ']" class="wpcf-access-custom-roles-select">';
                for ($index2 = 10; $index2 > -1; $index2--) {
                    $dropdown .= '<option value="' . $index2 . '"';
                    if ($index == $index2) {
                        $dropdown .= ' selected="selected"';
                    }
                    if (!$enabled) {
                        $dropdown .= ' disabled="disabled"';
                    }
                    $dropdown .= '>' . sprintf(__('Level %d', 'wpcf_access'),
                                    $index2);
                    $dropdown .= '</option>';
                }
                $dropdown .= '</select>&nbsp;<a href="javascript:void(0);" '
                        . 'class="wpcf-access-change-level-apply button-primary">'
                        . __('Apply', 'wpcf_access') . '</a>&nbsp;<a href="javascript:void(0);" '
                        . 'class="wpcf-access-change-level-cancel button-secondary">'
                        . __('Cancel') . '</a>'
                        . '</div>';
                $row .= '<div class="wpcf-access-roles-custom">'
                        . $details['name'] . '&nbsp;'
                        . '<a href="javascript:void(0);"';
                if ($enabled) {
                    $row .= ' class="wpcf-access-change-level"';
                }
                $row .= '>' . __('Change level', 'wpcf_access') . '</a>'
                        . '&nbsp;';
                if ($enabled) {
                    $row .= $dropdown;
                }
                $row .= '</div>';
            }
        }
        $row .= '</td></tr>';
        if (!$level_empty) {
            $output .= $row;
        }
    }

    if (isset($custom_roles['not_set'])) {
        $output .= '<tr><td><div class="wpcf-access-roles-level">'
                . __('Undefined', 'wpcf_access') . '</div></td><td>';
        foreach ($custom_roles['not_set'] as $role => $details) {
            $dropdown = '<div class="wpcf-access-custom-roles-select-wrapper">'
                    . '<select name="roles[' . $role
                    . ']" class="wpcf-access-custom-roles-select">';
            for ($index2 = 10; $index2 >= 0; $index2--) {
                $dropdown .= '<option value="' . $index2 . '"';
                if ($index2 == 1) {
                    $dropdown .= ' selected="selected"';
                }
                if (!$enabled) {
                    $dropdown .= ' disabled="disabled"';
                }
                $dropdown .= '>'
                        . sprintf(__('Level %d', 'wpcf_access'), $index2)
                        . '</option>';
            }
            $dropdown .= '</select>&nbsp;<a href="javascript:void(0);" '
                    . 'class="wpcf-access-change-level-apply button-primary">'
                    . __('Apply', 'wpcf_access') . '</a>&nbsp;<a href="javascript:void(0);" '
                    . 'class="wpcf-access-change-level-cancel button-secondary">'
                    . __('Cancel') . '</a>'
                    . '</div></div>';
            $output .= '<div class="wpcf-access-roles-custom">'
                    . $details['name'] . '&nbsp;'
                    . '<a href="javascript:void(0);"';
            if ($enabled) {
                $output .= ' class="wpcf-access-change-level"';
            }
            $output .= '>' . __('Change level', 'wpcf_access') . '</a>'
                    . '&nbsp;';
            if ($enabled) {
                $output .= $dropdown;
            }
            $output .= '</div>';
        }
        $output .= '</td></tr>';
    }
    $output .= '</tbody></table>';
    $output .= '</div>';
    $output .= '</div>';
    return $output;
}