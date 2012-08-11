<?php
/*
 * Admin functions.
 */

add_action('wpcf_menu_plus', 'wpcf_access_admin_menu_hook', 11);
add_action('wp_ajax_wpcf_access_add_user', 'wpcf_access_ajax_add_user');
add_action('wp_ajax_wpcf_access_ajax_reset_to_default',
        'wpcf_access_ajax_reset_to_default');
add_action('load-post.php', 'wpcf_access_admin_post_page_load_hook');
add_action('load-post-new.php', 'wpcf_access_admin_post_page_load_hook');

/**
 * Menu hook. 
 */
function wpcf_access_admin_menu_hook() {
    $hook = add_submenu_page('wpcf', __('Access', 'wpcf_access'),
            __('Access', 'wpcf_access'), 'manage_options', 'wpcf-access',
            'wpcf_access_admin_menu_page');
    wpcf_admin_plugin_help($hook, 'wpcf-access');
    add_action('load-' . $hook, 'wpcf_access_admin_menu_load');
}

/**
 * Menu page load hook. 
 */
function wpcf_access_admin_menu_load() {
    if (isset($_POST['_wpnonce'])
            && wp_verify_nonce($_POST['_wpnonce'], 'wpcf-access-edit')) {
        if (isset($_POST['types'])) {
            $settings = get_option('wpcf-custom-types', array());
            $settings_access = get_option('wpcf-access-types', array());
            $caps = wpcf_access_types_caps();
            $caps_predefined = wpcf_access_types_caps_predefined();
            foreach ($_POST['types'] as $type => $data) {
                // Set user IDs
                if (isset($data['predefined'])) {
                    foreach ($data['predefined'] as $cap => $data_cap) {
                        if (!isset($caps_predefined[$cap])) {
                            unset($data['predefined'][$cap]);
                            continue;
                        }
                        if (isset($data_cap['users'])) {
                            foreach ($data_cap['users'] as $temp_key => $temp_name) {
                                $user = get_userdatabylogin($temp_name);
                                if (!empty($user)) {
                                    $data['predefined'][$cap]['users'][$temp_key] = $user->ID;
                                } else {
                                    unset($data['predefined'][$cap]['users'][$temp_key]);
                                }
                            }
                        }
                    }
                }
                if (isset($data['custom'])) {
                    foreach ($data['custom'] as $cap => $data_cap) {
                        if (!isset($caps[$cap])) {
                            unset($data['custom'][$cap]);
                            continue;
                        }
                        if (isset($data_cap['users'])) {
                            foreach ($data_cap['users'] as $temp_key => $temp_name) {
                                $user = get_userdatabylogin($temp_name);
                                if (!empty($user)) {
                                    $data['custom'][$cap]['users'][$temp_key] = $user->ID;
                                } else {
                                    unset($data['custom'][$cap]['users'][$temp_key]);
                                }
                            }
                        }
                    }
                }
                if (isset($settings[$type])) {
                    $settings[$type]['_wpcf_access_capabilities'] = $data;
                } else {
                    $settings_access[$type] = $data;
                }
            }
            update_option('wpcf-custom-types', $settings);
            update_option('wpcf-access-types', $settings_access);
        }
        if (isset($_POST['tax'])) {
            $settings = get_option('wpcf-custom-taxonomies', array());
            $settings_access = get_option('wpcf-access-taxonomies', array());
            $caps = wpcf_access_tax_caps();
            foreach ($_POST['tax'] as $tax => $data) {
                // Set user IDs
                if (isset($data['custom'])) {
                    foreach ($data['custom'] as $cap => $data_cap) {
                        if (!isset($caps[$cap])) {
                            unset($data['custom'][$cap]);
                            continue;
                        }
                        if (isset($data_cap['users'])) {
                            foreach ($data_cap['users'] as $temp_key => $temp_name) {
                                $user = get_userdatabylogin($temp_name);
                                if (!empty($user)) {
                                    $data['custom'][$cap]['users'][$temp_key] = $user->ID;
                                } else {
                                    unset($data['custom'][$cap]['users'][$temp_key]);
                                }
                            }
                        }
                    }
                }
                if (isset($settings[$tax])) {
                    $settings[$tax]['_wpcf_access_capabilities'] = $data;
                } else {
                    $settings_access[$tax] = $data;
                }
            }
            update_option('wpcf-custom-taxonomies', $settings);
            update_option('wpcf-access-taxonomies', $settings_access);
        }
        if (!empty($_POST['roles'])) {
            foreach ($_POST['roles'] as $role => $level) {
                $role_data = get_role($role);
                if (!empty($role)) {
                    for ($index = 0; $index < 11; $index++) {
                        if ($index <= intval($level)) {
                            $role_data->add_cap('level_' . $index, 1);
                        } else {
                            $role_data->remove_cap('level_' . $index);
                        }
                    }
                }
            }
        }
        wpcf_admin_message_store(__('Access rules saved', 'wpcf_access'));
        wp_redirect($_SERVER['REQUEST_URI']);
        die();
    }
    wp_enqueue_style('wpcf-access-wpcf', WPCF_RES_RELPATH . '/css/basic.css',
            array(), WPCF_VERSION);
    wp_enqueue_style('wpcf-access', WPCF_ACCESS_RELPATH . '/css/basic.css',
            array(), WPCF_VERSION);
    wp_enqueue_script('wpcf-access', WPCF_ACCESS_RELPATH . '/js/basic.js',
            array('jquery'));
    wp_enqueue_script('suggest');
}

/**
 * Menu page render hook. 
 */
function wpcf_access_admin_menu_page() {
    echo wpcf_add_admin_header(__('Types Access', 'wpcf'));
    require_once WPCF_ACCESS_INC . '/admin-edit-access.php';
    wpcf_access_admin_edit_access();
    echo wpcf_add_admin_footer();
    add_action('admin_footer', 'wpcf_access_suggest_js');
}

/**
 * AJAX suggest user call. 
 */
function wpcf_access_ajax_add_user() {
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'],
                    'wpcf_access_add_user')) {
        die('verification failed');
    }
    $found = get_users(array('search' => $_GET['q'] . '*'));
    if (!empty($found)) {
        foreach ($found as $user) {
            echo $user->user_login . "\r\n";
        }
    }
    die();
}

/**
 * AJAX revert to default call. 
 */
function wpcf_access_ajax_reset_to_default() {
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'],
                    'wpcf_access_ajax_reset_to_default')) {
        die('verification failed');
    }
    if ($_GET['type'] == 'type') {
        $caps = wpcf_access_types_caps();
    } else if ($_GET['type'] == 'tax') {
        $caps = wpcf_access_tax_caps();
    }
    if (!empty($caps) && isset($_GET['button_id'])) {
        $output = array();
        foreach ($caps as $cap => $cap_data) {
            $output[$cap] = $cap_data['role'];
        }
        echo json_encode(array(
            'output' => $output,
            'button_id' => $_GET['button_id'],
        ));
    }
    die();
}

/**
 * AJAX set levels default call. 
 */
function wpcf_access_ajax_set_level() {
    if (!isset($_POST['_wpnonce'])
            || !wp_verify_nonce($_POST['_wpnonce'], 'execute')) {
        die('verification failed');
    }
    require_once WPCF_ACCESS_INC . '/admin-edit-access.php';
    if (!empty($_POST['roles'])) {
        foreach ($_POST['roles'] as $role => $level) {
            $role_data = get_role($role);
            if (!empty($role)) {
                for ($index = 0; $index < 11; $index++) {
                    if ($index <= intval($level)) {
                        $role_data->add_cap('level_' . $index, 1);
                    } else {
                        $role_data->remove_cap('level_' . $index);
                    }
                }
            }
        }
    }
    echo json_encode(array(
        'output' => wpcf_access_admin_set_custom_roles_level_form(get_editable_roles(),
                true),
    ));
    die();
}

/**
 * Post edit page hook. 
 */
function wpcf_access_admin_post_page_load_hook() {
    if (!current_user_can('edit_posts')) {
        add_action('admin_footer', 'wpcf_access_admin_edit_post_js');
    }
}

/**
 * Post edit page JS. 
 */
function wpcf_access_admin_edit_post_js() {
    $preview_txt = addslashes(__("Preview might not work. Try right clicking on button and select 'Open in new tab'.", 'wpcf_access'));
    ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
       jQuery('#post-preview').after('<div style="color:Red;clear:both;"><?php echo $preview_txt; ?></div>'); 
    });
</script>
<?php
}