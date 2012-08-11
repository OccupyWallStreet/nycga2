<?php
/*
 * Plus functions.
 */

add_action('plugins_loaded', 'wpcf_access_init', 11);

/**
 * Init function. 
 */
function wpcf_access_init() {
    if (!defined('WPCF_VERSION')) {
        add_action('admin_notices', 'wpcf_access_admin_warning_types_inactive');
    } else {
        define('WPCF_PLUS', true);
        define('WPCF_ACCESS_VERSION', '0.1');
        define('WPCF_ACCESS_ABSPATH', dirname(__FILE__));
        define('WPCF_ACCESS_RELPATH',
                plugins_url() . '/' . basename(WPCF_ACCESS_ABSPATH));
        define('WPCF_ACCESS_INC', WPCF_ACCESS_ABSPATH . '/includes');
        if (is_admin()) {
            require_once WPCF_ACCESS_ABSPATH . '/admin.php';
        }
        add_filter('wpcf_type', 'wpcf_access_init_types_rules', 10, 2);
        add_action('wpcf_type_registered', 'wpcf_access_collect_types_rules');
        add_filter('wpcf_taxonomy_data', 'wpcf_access_init_tax_rules', 10, 3);
        add_action('wpcf_taxonomy_registered', 'wpcf_access_collect_tax_rules');
        add_action('registered_post_type',
                'wpcf_access_registered_post_type_hook', 10, 2);
        add_action('registered_taxonomy',
                'wpcf_access_registered_taxonomy_hook', 10, 3);
        add_filter('user_has_cap', 'wpcf_access_user_has_cap_filter', 10, 3);
//        add_filter('role_has_cap', 'wpcf_access_role_has_cap_filter', 10, 3);
        $locale = get_locale();
        load_textdomain('wpcf_access',
                WPCF_ACCESS_ABSPATH . '/locale/types-access-' . $locale . '.mo');
    }
}

/**
 * Renders warning when Types plugin is not active. 
 */
function wpcf_access_admin_warning_types_inactive() {
    echo '<div class="message error"><p>'
    . __('Types plugin is required in order to make Types Access plugin work',
            'wpcf_access')
    . '</p></div>';
}

/**
 * Adds capabilities on WPCF types before registration hook.
 * 
 * @param type $data
 * @param type $post_type
 * @return boolean 
 */
function wpcf_access_init_types_rules($data, $post_type) {
    $types = get_option('wpcf-custom-types', array());
    if (isset($types[$post_type]['_wpcf_access_capabilities'])) {
        if ($types[$post_type]['_wpcf_access_capabilities']['mode'] === 'not_managed') {
            return $data;
        }
        $data['capability_type'] = array(
            sanitize_title($data['labels']['singular_name']),
            sanitize_title($data['labels']['name'])
        );
        $data['map_meta_cap'] = true;
    }
    return $data;
}

/**
 * Adds capabilities on WPCF taxonomies before registration hook.
 * 
 * @global type $wpcf_access_taxonomies_rules
 * @param type $data
 * @param type $taxonomy
 * @param type $object_types
 * @return type 
 */
function wpcf_access_init_tax_rules($data, $taxonomy, $object_types) {
    global $wpcf_access_taxonomies_rules;
    if (!is_array($wpcf_access_taxonomies_rules)) {
        $wpcf_access_taxonomies_rules = array();
    }
    $mode = isset($data['_wpcf_access_capabilities']['mode']) ? $data['_wpcf_access_capabilities']['mode'] : 'follow';
    if ($mode == 'not_managed') {
        return $data;
    }
    $caps = wpcf_access_tax_caps();
    foreach ($caps as $cap_slug => $cap_data) {
        $new_cap_slug = str_replace('_terms',
                '_' . sanitize_title($data['labels']['name']), $cap_slug);
        $data['capabilities'][$cap_slug] = $new_cap_slug;
        if ($mode == 'follow') {
            $wpcf_access_taxonomies_rules[$new_cap_slug] = 'follow';
        } else if (isset($data['_wpcf_access_capabilities']['custom'][$cap_slug])) {
            $wpcf_access_taxonomies_rules[$new_cap_slug] = $data['_wpcf_access_capabilities']['custom'][$cap_slug];
        }
    }
    return $data;
}

/**
 * Adds capabilities on WPCF types after registration hook.
 * 
 * @global type $wpcf_access_types_rules
 * @param type $data 
 */
function wpcf_access_collect_types_rules($data) {
    global $wpcf_access_types_rules;
    if (!is_array($wpcf_access_types_rules)) {
        $wpcf_access_types_rules = array();
    }
    $mode = isset($data->_wpcf_access_capabilities['mode']) ? $data->_wpcf_access_capabilities['mode'] : 'predefined';
    if ($mode == 'not_managed') {
        return false;
    }
    if ($mode == 'custom' && isset($data->_wpcf_access_capabilities['custom'])) {
        foreach ($data->cap as $cap_slug => $cap_spec) {
            if (isset($data->_wpcf_access_capabilities['custom'][$cap_slug])) {
                $wpcf_access_types_rules[$cap_spec] = $data->_wpcf_access_capabilities['custom'][$cap_slug];
            }
        }
    } else if ($mode == 'predefined' && isset($data->_wpcf_access_capabilities['predefined'])) {
        $caps = wpcf_access_types_caps();
        $mapped = array();
        // Map predefined
        foreach ($caps as $cap_slug => $cap_spec) {
            if (isset($data->_wpcf_access_capabilities['predefined'][$cap_spec['predefined']])) {
                $mapped[$cap_slug] = $data->_wpcf_access_capabilities['predefined'][$cap_spec['predefined']];
            }
        }
        foreach ($data->cap as $cap_slug => $cap_spec) {
            if (isset($mapped[$cap_slug])) {
                $wpcf_access_types_rules[$cap_spec] = $mapped[$cap_slug];
            }
        }
    }
}

/**
 * 'has_cap' filter.
 * 
 * @global type $current_user
 * @global type $wpcf_access_types_rules
 * @param type $allcaps
 * @param type $caps
 * @param type $args
 * @return int 
 */
function wpcf_access_user_has_cap_filter($allcaps, $caps, $args) {
    global $current_user, $wpcf_access_types_rules, $wpcf_access_taxonomies_rules;
    $map = wpcf_access_role_to_level_map();

    // Check types
    $level_needed = !empty($wpcf_access_types_rules[$args[0]]['role']) && isset($map[$wpcf_access_types_rules[$args[0]]['role']]) ? $map[$wpcf_access_types_rules[$args[0]]['role']] : false;
    $user_needed = !empty($wpcf_access_types_rules[$args[0]]['users']) ? $wpcf_access_types_rules[$args[0]]['users'] : false;

    $level_passed = false;

    if ($level_needed) {
        if (!empty($current_user->allcaps[$level_needed])) {
            $allcaps[$args[0]] = 1;
            foreach ($caps as $cap) {
                $allcaps[$cap] = 1;
            }
            $level_passed = true;
        } else {
            unset($allcaps[$args[0]]);
        }
    }
    if (!$level_passed && is_array($user_needed)) {
        if (in_array($current_user->ID, $user_needed)) {
            $allcaps[$args[0]] = 1;
            foreach ($caps as $cap) {
                $allcaps[$cap] = 1;
            }
        } else {
            unset($allcaps[$args[0]]);
        }
    }

    // Check taxonomies
    $level_needed = !empty($wpcf_access_taxonomies_rules[$args[0]]['role']) && isset($map[$wpcf_access_taxonomies_rules[$args[0]]['role']]) ? $map[$wpcf_access_taxonomies_rules[$args[0]]['role']] : false;
    $user_needed = !empty($wpcf_access_taxonomies_rules[$args[0]]['users']) ? $wpcf_access_taxonomies_rules[$args[0]]['users'] : false;

    $level_passed = false;

    if ($level_needed) {
        if (!empty($current_user->allcaps[$level_needed])) {
            $allcaps[$args[0]] = 1;
            foreach ($caps as $cap) {
                $allcaps[$cap] = 1;
            }
            $level_passed = true;
        } else {
            unset($allcaps[$args[0]]);
        }
    }
    if (!$level_passed && is_array($user_needed)) {
        if (in_array($current_user->ID, $user_needed)) {
            $allcaps[$args[0]] = 1;
            foreach ($caps as $cap) {
                $allcaps[$cap] = 1;
            }
        } else {
            unset($allcaps[$args[0]]);
        }
    }
    // Check taxonomies 'follow'
    $follow = isset($wpcf_access_taxonomies_rules[$args[0]]) && $wpcf_access_taxonomies_rules[$args[0]] == 'follow';
    if ($follow) {
//        $debug = debug_backtrace();//debug($debug[6], false);
//        if (isset($debug[6]) && $debug[6]['function'] == '_wp_menu_output') {debug($debug);
//            unset($allcaps[$args[0]]);
//            return $allcaps;
//        }
        global $pagenow;
        // Determine post type
        global $post;
        $post_type = false;
        if (!empty($post)) {
            $post_type = get_post_type($post);
        } else if (isset($_GET['post_type'])) {
            $post_type = $_GET['post_type'];
        } else if (isset($_POST['post_type'])) {
            $post_type = $_POST['post_type'];
        } else if (isset($_GET['post'])) {
            $post_type = get_post_type($_GET['post']);
        } else if (isset($_POST['post'])) {
            $post_type = get_post_type($_POST['post']);
        } else if ($pagenow == 'post-new.php'
                || $pagenow == 'edit-tags.php'
                || $pagenow == 'edit.php') {
            $post_type = 'post';
        } else if (defined('DOING_AJAX') && isset($_SERVER['HTTP_REFERER'])) {
            $split = explode('?', $_SERVER['HTTP_REFERER']);
            if (isset($split[1])) {
                parse_str($split[1], $vars);
                if (isset($vars['post_type'])) {
                    $post_type = $vars['post_type'];
                } else if (isset($vars['post'])) {
                    $post_type = get_post_type($vars['post']);
                } else if (strpos($split[1], 'post-new.php') !== false) {
                    $post_type = 'post';
                }
            } else if (strpos($_SERVER['HTTP_REFERER'], 'post-new.php') !== false
                    || strpos($_SERVER['HTTP_REFERER'], 'edit-tags.php') !== false
                    || strpos($_SERVER['HTTP_REFERER'], 'edit.php') !== false) {
                $post_type = 'post';
            }
        }
        // If no post type determined, return FALSE
        if (!$post_type) {
            unset($allcaps[$args[0]]);
            return $allcaps;
        } else {
            $post_type = get_post_type_object($post_type);
            $post_type = sanitize_title($post_type->labels->name);
        }
        $allow = false;
        $tax_caps = wpcf_access_tax_caps();
        foreach ($tax_caps as $tax_cap_slug => $tax_slug_data) {
            foreach ($tax_slug_data['match'] as $match => $replace) {
                $level_passed = false;
                if (strpos($args[0], $match) === 0) {
                    if ($post_type
                            && !empty($wpcf_access_types_rules[$replace['match'] . $post_type])) {
                        $level_needed = !empty($wpcf_access_types_rules[$replace['match'] . $post_type]['role']) && isset($map[$wpcf_access_types_rules[$replace['match'] . $post_type]['role']]) ? $map[$wpcf_access_types_rules[$replace['match'] . $post_type]['role']] : false;
                        $user_needed = !empty($wpcf_access_types_rules[$replace['match'] . $post_type]['users']) ? $wpcf_access_types_rules[$replace['match'] . $post_type]['users'] : false;
                        if ($level_needed) {
                            if (!empty($current_user->allcaps[$level_needed])) {
                                $allow = true;
                                $level_passed = true;
                            }
                        }
                        if (!$level_passed && is_array($user_needed)) {
                            if (in_array($current_user->ID, $user_needed)) {
                                $allow = true;
                            }
                        }
                    } else if (!empty($allcaps[$replace['default']])) {
                        $allow = true;
                    }
                }
            }
        }
        if ($allow) {
            $allcaps[$args[0]] = 1;
            foreach ($caps as $cap) {
                $allcaps[$cap] = 1;
            }
        } else {
            unset($allcaps[$args[0]]);
        }
    }
    return $allcaps;
}

/**
 * 'role_has_cap' filter.
 * 
 * @global type $current_user
 * @global type $wpcf_access_types_rules
 * @param type $capabilities
 * @param type $cap
 * @param type $role
 * @return int 
 */
//function wpcf_access_role_has_cap_filter($capabilities, $cap, $role) {
//    die('role cap used');
//    global $current_user;
//    global $wpcf_access_types_rules;
//    $map = wpcf_access_role_to_level_map();
//    $level_needed = !empty($wpcf_access_types_rules[$cap]['role']) ? $map[$wpcf_access_types_rules[$cap]['role']] : false;
//    $user_needed = !empty($wpcf_access_types_rules[$cap]['users']) ? $wpcf_access_types_rules[$cap]['users'] : false;
//    if ($level_needed) {
//        if (!empty($current_user->allcaps[$level_needed])) {
//            $capabilities[$cap] = 1;
//        } else {
//            unset($capabilities[$cap]);
//        }
//    }
//    if ($user_needed) {
//        if (in_array($current_user->ID, $user_needed)) {
//            $capabilities[$cap] = 1;
//        } else {
//            unset($capabilities[$cap]);
//        }
//    }
//    return $capabilities;
//}

/**
 * Adds capabilities for post types registered outside of Types.
 * 
 * @param type $post_type
 * @param type $args 
 */
function wpcf_access_registered_post_type_hook($post_type, $args) {
    global $wpcf_access_types_rules;
    if (!is_array($wpcf_access_types_rules)) {
        $wpcf_access_types_rules = array();
    }
    $settings_access = get_option('wpcf-access-types', array());
    if (isset($settings_access[$post_type])) {
        $data = $settings_access[$post_type];
        $mode = isset($data['mode']) ? $data['mode'] : 'predefined';
        if ($mode == 'not_managed') {
            return false;
        }
        if ($mode == 'custom' && isset($data['custom'])) {
            foreach ($args->cap as $cap_slug => $cap_spec) {
                if (isset($data['custom'][$cap_slug])) {
                    $wpcf_access_types_rules[$cap_spec] = $data['custom'][$cap_slug];
                }
            }
        } else if ($mode == 'predefined' && isset($data['predefined'])) {
            $caps = wpcf_access_types_caps();
            $mapped = array();
            // Map predefined
            foreach ($caps as $cap_slug => $cap_spec) {
                if (isset($data['predefined'][$cap_spec['predefined']])) {
                    $mapped[$cap_slug] = $data['predefined'][$cap_spec['predefined']];
                }
            }
            foreach ($args->cap as $cap_slug => $cap_spec) {
                if (isset($mapped[$cap_slug])) {
                    $wpcf_access_types_rules[$cap_spec] = $mapped[$cap_slug];
                }
            }
        }
    }
}

/**
 * Adds capabilities for taxonomies registered outside of Types.
 * 
 * @param type $post_type
 * @param type $args 
 */
function wpcf_access_registered_taxonomy_hook($taxonomy, $object_type, $args) {
    global $wp_taxonomies, $wpcf_access_taxonomies_rules;
    if (!is_array($wpcf_access_taxonomies_rules)) {
        $wpcf_access_taxonomies_rules = array();
    }
    $settings_access = get_option('wpcf-access-taxonomies', array());
    if (isset($settings_access[$taxonomy]) && $wp_taxonomies[$taxonomy]) {
        $data = $settings_access[$taxonomy];
        $mode = isset($data['mode']) ? $data['mode'] : 'follow';
        if ($mode == 'not_managed') {
            return false;
        }
        $caps = wpcf_access_tax_caps();
        foreach ($caps as $cap_slug => $cap_data) {
            $new_cap_slug = str_replace('_terms',
                    '_' . sanitize_title($args['labels']->name), $cap_slug);
            // Alter if tax is built-in or other has default capability settings
            if (!empty($args['_builtin'])
                    || (isset($args['cap']->$cap_slug)
                    && $args['cap']->$cap_slug == $cap_data['default'])) {
                $wp_taxonomies[$taxonomy]->cap->$cap_slug = $new_cap_slug;
                if ($mode == 'follow') {
                    $wpcf_access_taxonomies_rules[$new_cap_slug] = 'follow';
                } else if (isset($data['custom'][$cap_slug])) {
                    $wpcf_access_taxonomies_rules[$new_cap_slug] = $data['custom'][$cap_slug];
                }
                // Otherwise just map capabilities
            } else if (isset($args['cap']->$cap_slug)
                    && isset($wpcf_access_taxonomies_rules[$args['cap']->$cap_slug])) {
                if ($mode == 'follow') {
                    $wpcf_access_taxonomies_rules[$args['cap']->$cap_slug] = 'follow';
                } else if (isset($data['custom'][$cap_slug])) {
                    $wpcf_access_taxonomies_rules[$args['cap']->$cap_slug] = $data['custom'][$cap_slug];
                }
            }
        }
    }
}