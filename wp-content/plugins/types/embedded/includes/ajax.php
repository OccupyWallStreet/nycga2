<?php

/**
 * All AJAX calls go here.
 */
function wpcf_ajax_embedded() {
    if (!isset($_REQUEST['_wpnonce'])
            || !wp_verify_nonce($_REQUEST['_wpnonce'], $_REQUEST['wpcf_action'])) {
        die('Verification failed');
    }
    switch ($_REQUEST['wpcf_action']) {

        case 'editor_insert_date':
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields/date.php';
            wpcf_fields_date_editor_form();
            break;

        case 'insert_skype_button':
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields/skype.php';
            wpcf_fields_skype_meta_box_ajax();
            break;

        case 'editor_callback':
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
            $field = wpcf_admin_fields_get_field($_GET['field_id']);
            if (!empty($field)) {
                $function = 'wpcf_fields_' . $field['type'] . '_editor_callback';
                if (function_exists($function)) {
                    call_user_func($function);
                }
            }
            break;

        case 'dismiss_message':
            if (isset($_GET['id'])) {
                $messages = get_option('wpcf_dismissed_messages', array());
                $messages[] = $_GET['id'];
                update_option('wpcf_dismissed_messages', $messages);
            }
            break;

        case 'pr_add_child_post':
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
            require_once WPCF_EMBEDDED_ABSPATH . '/includes/post-relationship.php';
            $output = 'Passed wrong parameters';
            if (isset($_GET['post_id']) && isset($_GET['post_type_child']) && isset($_GET['post_type_parent'])) {
                $relationships = get_option('wpcf_post_relationship', array());
                $post = get_post($_GET['post_id']);
                $post_type = $_GET['post_type_child'];
                $parent_post_type = $_GET['post_type_parent'];
                $data = $relationships[$parent_post_type][$post_type];
                $headers = wpcf_pr_admin_post_meta_box_has_form_headers($post, $post_type, $parent_post_type, $data);
                $output = wpcf_pr_admin_post_meta_box_has_row($post, $post_type,
                        $data, $parent_post_type, false, $headers);
            }
            echo json_encode(array(
                'output' => $output,
            ));
            break;

        case 'pr_save_child_post':
            ob_start(); // Try to catch any errors
            require_once WPCF_EMBEDDED_ABSPATH . '/includes/post-relationship.php';
            $output = array();
            if (isset($_GET['post_id']) && isset($_GET['post_type_child'])) {
                $post = get_post($_GET['post_id']);
                $post_type = $_GET['post_type_child'];
                $output = wpcf_pr_admin_save_post_hook($_GET['post_id']);
            }
            $errors = ob_get_clean();
            echo json_encode(array(
                'output' => $output,
                'errors' => $errors
            ));
            break;

        case 'pr_delete_child_post':
            require_once WPCF_EMBEDDED_ABSPATH . '/includes/post-relationship.php';
            $output = 'Passed wrong parameters';
            if (isset($_GET['post_id'])) {
                $output = wpcf_pr_admin_delete_child_item($_GET['post_id']);
            }
            echo json_encode(array(
                'output' => $output,
            ));
            break;

        case 'pr-update-belongs':
            require_once WPCF_EMBEDDED_ABSPATH . '/includes/post-relationship.php';
            $output = 'Passed wrong parameters';
            if (isset($_POST['post_id']) && isset($_POST['wpcf_pr_belongs'])) {
                $output = wpcf_pr_admin_update_belongs($_POST['post_id'],
                        $_POST['wpcf_pr_belongs']);
            }
            echo json_encode(array(
                'output' => $output,
            ));
            break;

        case 'pr_pagination':
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
            require_once WPCF_EMBEDDED_ABSPATH . '/includes/post-relationship.php';
            $output = 'Passed wrong parameters';
            if (isset($_GET['post_id']) && isset($_GET['post_type'])) {
                $post = get_post($_GET['post_id']);
                $post_type = $_GET['post_type'];
                $has = wpcf_pr_admin_get_has($post->post_type);
                $output = wpcf_pr_admin_post_meta_box_has_form($post,
                        $post_type, $has[$post_type], $post->post_type);
            }
            echo json_encode(array(
                'output' => $output,
            ));
            break;

        case 'pr_sort':
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
            require_once WPCF_EMBEDDED_ABSPATH . '/includes/post-relationship.php';
            $output = 'Passed wrong parameters';
            if (isset($_GET['field']) && isset($_GET['sort']) && isset($_GET['post_id']) && isset($_GET['post_type'])) {
                $post = get_post($_GET['post_id']);
                $post_type = $_GET['post_type'];
                $has = wpcf_pr_admin_get_has($post->post_type);
                $output = wpcf_pr_admin_post_meta_box_has_form($post,
                        $post_type, $has[$post_type], $post->post_type);
            }
            echo json_encode(array(
                'output' => $output,
            ));
            break;

        case 'pr_sort_parent':
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
            require_once WPCF_EMBEDDED_ABSPATH . '/includes/post-relationship.php';
            $output = 'Passed wrong parameters';
            if (isset($_GET['field']) && isset($_GET['sort']) && isset($_GET['post_id']) && isset($_GET['post_type'])) {
                $post = get_post($_GET['post_id']);
                $post_type = $_GET['post_type'];
                $has = wpcf_pr_admin_get_has($post->post_type);
                $output = wpcf_pr_admin_post_meta_box_has_form($post,
                        $post_type, $has[$post_type], $post->post_type);
            }
            echo json_encode(array(
                'output' => $output,
            ));
            break;

        case 'pr_save_all':
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
            require_once WPCF_EMBEDDED_ABSPATH . '/includes/post-relationship.php';
            $output = array();
            if (isset($_POST['post_id']) && isset($_POST['wpcf_post_relationship'])) {
                $output = wpcf_pr_admin_save_post_hook($_POST['post_id']);
            }
            echo json_encode(array(
                'output' => $output,
            ));
            break;

        case 'repetitive_add':
            if (isset($_GET['field_id'])) {
                require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
                require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
                $field = wpcf_admin_fields_get_field($_GET['field_id']);
                // Pass as normal
                unset($field['data']['repetitive']);
                $fields = array($_GET['field_id'] => $field);
                $element = wpcf_admin_post_process_fields(false, $fields, false,
                        false, 'repetitive');
                if ($field['type'] == 'skype') {
                    $key = key($element);
                    unset($element[$key]['#title']);
                    echo json_encode(array(
                        'output' => wpcf_form_simple($element) . wpcf_form_render_js_validation('#post',
                                false),
                    ));
                } else {
                    $element = array_shift($element);
                    if (!in_array($field['type'], array('checkbox'))) {
                        unset($element['#title']);
                    }
                    echo json_encode(array(
                        'output' => wpcf_form_simple(array('repetitive' => $element)) . wpcf_form_render_js_validation('#post',
                                false),
                    ));
                }
            } else {
                echo json_encode(array(
                    'output' => 'params missing',
                ));
            }
            break;

        case 'repetitive_delete':
            if (isset($_POST['post_id']) && isset($_POST['field_id']) && isset($_POST['old_value'])) {
                require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
                $field = wpcf_admin_fields_get_field($_POST['field_id']);
                if (!empty($field)) {
                    if ($field['type'] == 'date') {
                        delete_post_meta($_POST['post_id'],
                                wpcf_types_get_meta_prefix($field) . $field['id'],
                                strtotime(base64_decode($_POST['old_value'])));
                    } else if ($field['type'] == 'skype') {
                        delete_post_meta($_POST['post_id'],
                                wpcf_types_get_meta_prefix($field) . $field['id'],
                                unserialize(base64_decode($_POST['old_value'])));
                    } else {
                        delete_post_meta($_POST['post_id'],
                                wpcf_types_get_meta_prefix($field) . $field['id'],
                                base64_decode($_POST['old_value']));
                    }
                    echo json_encode(array(
                        'output' => 'deleted',
                    ));
                } else {
                    echo json_encode(array(
                        'output' => 'field not found',
                    ));
                }
            } else {
                echo json_encode(array(
                    'output' => 'params missing',
                ));
            }
            break;

        case 'cd_verify':
            if (!is_array($_POST['wpcf'])) {
                die();
            }
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/conditional-display.php';
            $passed_fields = array();
            $failed_fields = array();
            $post = false;
            if (isset($_SERVER['HTTP_REFERER'])) {
                $split = explode('?', $_SERVER['HTTP_REFERER']);
                if (isset($split[1])) {
                    parse_str($split[1], $vars);
                    if (isset($vars['post'])) {
                        $_POST['post_ID'] = $vars['post'];
                        $post = get_post($vars['post']);
                    }
                }
            }
            // Dummy post
            if (!$post) {
                $post = new stdClass();
                $post->ID = 1;
            }
            // Filter meta values (switch them with $_POST values)
            add_filter('get_post_metadata',
                    'wpcf_cd_meta_ajax_validation_filter', 10, 4);

            foreach ($_POST['wpcf'] as $field_id => $field_value) {
                $element = array();
                $field = wpcf_admin_fields_get_field($field_id);
                if (!empty($field['data']['conditional_display']['conditions'])) {
                    $element = wpcf_cd_post_edit_field_filter($element, $field,
                            $post, 'group');
                    if (isset($element['__wpcf_cd_status']) && $element['__wpcf_cd_status'] == 'passed') {
                        $passed_fields[] = 'wpcf[' . $field['id'] . ']';
                    } else {
                        $failed_fields[] = 'wpcf[' . $field['id'] . ']';
                    }
                }
            }

            // Remove filter meta values (switch them with $_POST values)
            remove_filter('get_post_metadata',
                    'wpcf_cd_meta_ajax_validation_filter', 10, 4);

            if (!empty($passed_fields) || !empty($failed_fields)) {
                $execute = '';
                foreach ($passed_fields as $field_name) {
                    $execute .= 'jQuery(\'[name^="' . $field_name . '"]\').parents(\'.wpcf-cd\').show().removeClass(\'wpcf-cd-failed\').addClass(\'wpcf-cd-passed\');' . " ";
                }
                foreach ($failed_fields as $field_name) {
                    $execute .= 'jQuery(\'[name^="' . $field_name . '"]\').parents(\'.wpcf-cd\').hide().addClass(\'wpcf-cd-failed\').removeClass(\'wpcf-cd-passed\');' . " ";
                }
                echo json_encode(array(
                    'output' => '',
                    'execute' => $execute,
                    'wpcf_nonce_ajax_callback' => wp_create_nonce('execute'),
                ));
            }
            die();
            break;

        case 'cd_group_verify':
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/conditional-display.php';
            $group = wpcf_admin_fields_get_group($_POST['group_id']);
            if (empty($group)) {
                echo json_encode(array(
                    'output' => ''
                ));
                die();
            }
            $execute = '';
            $group['conditional_display'] = get_post_meta($group['id'],
                    '_wpcf_conditional_display', true);
            // Filter meta values (switch them with $_POST values)
            add_filter('get_post_metadata',
                    'wpcf_cd_meta_ajax_validation_filter', 10, 4);
            $post = false;
            if (isset($_SERVER['HTTP_REFERER'])) {
                $split = explode('?', $_SERVER['HTTP_REFERER']);
                if (isset($split[1])) {
                    parse_str($split[1], $vars);
                    if (isset($vars['post'])) {
                        $_POST['post_ID'] = $vars['post'];
                        $post = get_post($vars['post']);
                    }
                }
            }
            // Dummy post
            if (!$post) {
                $post = new stdClass();
                $post->ID = 1;
            }
            if (!empty($group['conditional_display']['conditions'])) {
                $result = wpcf_cd_post_groups_filter(array(0 => $group), $post,
                        'group');
                if (!empty($result)) {
                    $result = array_shift($result);
                    $passed = $result['_conditional_display'] == 'passed' ? true : false;
                } else {
                    $passed = false;
                }
                if (!$passed) {
                    $execute = 'jQuery("#' . $group['slug'] . '").slideUp().find(".wpcf-cd-group").addClass(\'wpcf-cd-group-failed\').removeClass(\'wpcf-cd-group-passed\').hide();';
                } else {
                    $execute = 'jQuery("#' . $group['slug'] . '").show().find(".wpcf-cd-group").addClass(\'wpcf-cd-group-passed\').removeClass(\'wpcf-cd-group-failed\').slideDown();';
                }
            }
            // Remove filter meta values (switch them with $_POST values)
            remove_filter('get_post_metadata',
                    'wpcf_cd_meta_ajax_validation_filter', 10, 4);
            echo json_encode(array(
                'output' => '',
                'execute' => $execute,
                'wpcf_nonce_ajax_callback' => wp_create_nonce('execute'),
            ));
            break;

        case 'pr_verify':
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
            require_once WPCF_EMBEDDED_INC_ABSPATH . '/conditional-display.php';
            $passed_fields = array();
            $failed_fields = array();
            $post = false;
            if (isset($_SERVER['HTTP_REFERER'])) {
                $split = explode('?', $_SERVER['HTTP_REFERER']);
                if (isset($split[1])) {
                    parse_str($split[1], $vars);
                    if (isset($vars['post'])) {
                        $_POST['post_ID'] = $vars['post'];
                        $post = get_post($vars['post']);
                    }
                }
            }
            // Dummy post
            if (!$post) {
                $post = new stdClass();
                $post->ID = 1;
            }
            // Filter meta values (switch them with $_POST values)
            add_filter('get_post_metadata',
                    'wpcf_cd_pr_meta_ajax_validation_filter', 10, 4);

            if (isset($_POST['wpcf_post_relationship'])) {
                $child_post_id = key($_POST['wpcf_post_relationship']);
                $data = $_POST['wpcf_post_relationship'] = array_shift($_POST['wpcf_post_relationship']);
                foreach ($data as $field_id => $field_value) {
                    $element = array();
                    $field = wpcf_admin_fields_get_field(str_replace(WPCF_META_PREFIX,
                                    '', $field_id));
                    if (!empty($field['data']['conditional_display']['conditions'])) {
                        $element = wpcf_cd_post_edit_field_filter($element,
                                $field, $post, 'group');
                        if (isset($element['__wpcf_cd_status']) && $element['__wpcf_cd_status'] == 'passed') {
                            $passed_fields[] = 'wpcf_post_relationship_'
                                    . $child_post_id . '_' . $field['id'];
                        } else {
                            $failed_fields[] = 'wpcf_post_relationship_'
                                    . $child_post_id . '_' . $field['id'];
                        }
                    }
                }
            }

            // Remove filter meta values (switch them with $_POST values)
            remove_filter('get_post_metadata',
                    'wpcf_cd_pr_meta_ajax_validation_filter', 10, 4);

            if (!empty($passed_fields) || !empty($failed_fields)) {
                $execute = '';
                foreach ($passed_fields as $field_name) {
                    $execute .= 'jQuery(\'#' . $field_name . '\').parents(\'.wpcf-cd\').show().removeClass(\'wpcf-cd-failed\').addClass(\'wpcf-cd-passed\');' . " ";
                }
                foreach ($failed_fields as $field_name) {
                    $execute .= 'jQuery(\'#' . $field_name . '\').parents(\'.wpcf-cd\').hide().addClass(\'wpcf-cd-failed\').removeClass(\'wpcf-cd-passed\');' . " ";
                }
                echo json_encode(array(
                    'output' => '',
                    'execute' => $execute,
                    'wpcf_nonce_ajax_callback' => wp_create_nonce('execute'),
                ));
            }
            die();
            break;

        default:
            break;
    }
    if (function_exists('wpcf_ajax')) {
        wpcf_ajax();
    }
    die();
}