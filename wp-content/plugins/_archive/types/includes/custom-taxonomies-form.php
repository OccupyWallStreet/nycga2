<?php
/*
 * Custom taxonomies form
 */

/**
 * Add/edit form structure
 */
function wpcf_admin_custom_taxonomies_form() {

    $ct = array();
    $id = false;
    $update = false;

    if (isset($_GET['wpcf-tax'])) {
        $id = $_GET['wpcf-tax'];
    } else if (isset($_POST['wpcf-tax'])) {
        $id = $_POST['wpcf-tax'];
    }

    if ($id) {
        $custom_taxonomies = get_option('wpcf-custom-taxonomies', array());
        if (isset($custom_taxonomies[$id])) {
            $ct = $custom_taxonomies[$id];
            $update = true;
            // Set rewrite if needed
            if (isset($_GET['wpcf-rewrite'])) {
                flush_rewrite_rules();
            }
        } else {
            wpcf_admin_message(__('Wrong custom taxonomy specified', 'wpcf'),
                    'error');
            return false;
        }
    } else {
        $ct = wpcf_custom_taxonomies_default();
    }

    $form = array();
    $form['#form']['callback'] = 'wpcf_admin_custom_taxonomies_form_submit';
    $form['#form']['redirection'] = false;

    if ($update) {
        $form['id'] = array(
            '#type' => 'hidden',
            '#value' => $id,
            '#name' => 'ct[wpcf-tax]',
        );
    }

    $form['table-1-open'] = array(
        '#type' => 'markup',
        '#markup' => '<table id="wpcf-types-form-name-table" class="wpcf-types-form-table widefat"><thead><tr><th colspan="2">' . __('Name and description',
                'wpcf') . '</th></tr></thead><tbody>',
    );
    $table_row = '<tr><td><LABEL></td><td><ERROR><ELEMENT></td></tr>';

    $form['name'] = array(
        '#type' => 'textfield',
        '#name' => 'ct[labels][name]',
        '#title' => __('Custom taxonomy name plural', 'wpcf') . ' (<strong>' . __('required',
                'wpcf') . '</strong>)',
        '#description' => '<strong>' . __('Enter in plural!', 'wpcf')
//        . '</strong><br />' . __('Alphanumeric with whitespaces only', 'wpcf')
        . '.',
        '#value' => isset($ct['labels']['name']) ? $ct['labels']['name'] : '',
        '#validate' => array(
            'required' => array('value' => true),
//            'alphanumeric' => array('value' => true),
        ),
        '#pattern' => $table_row,
        '#inline' => true,
    );
    $form['name-singular'] = array(
        '#type' => 'textfield',
        '#name' => 'ct[labels][singular_name]',
        '#title' => __('Custom taxonomy name singular', 'wpcf') . ' (<strong>' . __('required',
                'wpcf') . '</strong>)',
        '#description' => '<strong>' . __('Enter in singular!', 'wpcf')
        . '</strong><br />'
//        . __('Alphanumeric with whitespaces only', 'wpcf')
        . '.',
        '#value' => isset($ct['labels']['singular_name']) ? $ct['labels']['singular_name'] : '',
        '#validate' => array(
            'required' => array('value' => true),
//            'alphanumeric' => array('value' => true),
        ),
        '#pattern' => $table_row,
        '#inline' => true,
    );
    $form['slug'] = array(
        '#type' => 'textfield',
        '#name' => 'ct[slug]',
        '#title' => __('Slug', 'wpcf') . ' (<strong>' . __('required', 'wpcf') . '</strong>)',
        '#description' => '<strong>' . __('Enter in singular!', 'wpcf')
        . '</strong><br />' . __('Machine readable name.', 'wpcf')
        . '<br />' . __('If not provided - will be created from singular name.',
                'wpcf') . '<br />',
        '#value' => isset($ct['slug']) ? $ct['slug'] : '',
        '#pattern' => $table_row,
        '#inline' => true,
        '#validate' => array(
            'required' => array('value' => true),
            'nospecialchars' => array('value' => true),
        ),
    );
    $form['description'] = array(
        '#type' => 'textarea',
        '#name' => 'ct[description]',
        '#title' => __('Description', 'wpcf'),
        '#value' => isset($ct['description']) ? $ct['description'] : '',
        '#attributes' => array(
            'rows' => 4,
            'cols' => 60,
        ),
        '#pattern' => $table_row,
        '#inline' => true,
    );
    $form['table-1-close'] = array(
        '#type' => 'markup',
        '#markup' => '</tbody></table>',
    );
    $form['table-2-open'] = array(
        '#type' => 'markup',
        '#markup' => '<table id="wpcf-types-form-visibility-table" class="wpcf-types-form-table widefat"><thead><tr><th>' . __('Visibility',
                'wpcf') . '</th></tr></thead><tbody><tr><td>',
    );
    $form['public'] = array(
        '#type' => 'radios',
        '#name' => 'ct[public]',
        '#options' => array(
            __('Make this taxonomy public (will appear in the WordPress Admin menu)',
                    'wpcf') => 'public',
            __('Hidden - users cannot directly edit data in this taxonomy',
                    'wpcf') => 'hidden',
        ),
        '#default_value' => (isset($ct['public']) && strval($ct['public']) == 'hidden') ? 'hidden' : 'public',
        '#inline' => true,
    );
    $form['table-2-close'] = array(
        '#type' => 'markup',
        '#markup' => '</td></tr></tbody></table>',
    );

    $post_types = get_post_types('', 'objects');
    $options = array();

    foreach ($post_types as $post_type_slug => $post_type) {
        if (in_array($post_type_slug,
                        array('revision', 'view', 'view-template', 'nav_menu_item', 'attachment', 'mediapage'))
                || !$post_type->show_ui) {
            continue;
        }
        $options[$post_type_slug]['#name'] = 'ct[supports][' . $post_type_slug . ']';
        $options[$post_type_slug]['#title'] = $post_type->labels->singular_name;
        $options[$post_type_slug]['#default_value'] = !empty($ct['supports'][$post_type_slug]);
        $options[$post_type_slug]['#inline'] = true;
        $options[$post_type_slug]['#after'] = '&nbsp;&nbsp;';
    }

    $form['table-3-open'] = array(
        '#type' => 'markup',
        '#markup' => '<table id="wpcf-types-form-taxonomies-table" class="wpcf-types-form-table widefat"><thead><tr><th>' . __('Select Post Types',
                'wpcf') . '</th></tr></thead><tbody><tr><td>',
    );
    $form['types'] = array(
        '#type' => 'checkboxes',
        '#options' => $options,
        '#description' => __('Registered post types that will be used with this taxonomy.',
                'wpcf'),
        '#name' => 'ct[supports]',
        '#inline' => true,
    );
    $form['table-3-close'] = array(
        '#type' => 'markup',
        '#markup' => '</td></tr></tbody></table>',
    );
    $form['table-4-open'] = array(
        '#type' => 'markup',
        '#markup' => '<table id="wpcf-types-form-labels-table" class="wpcf-types-form-table widefat"><thead><tr><th colspan="3">' . __('Labels',
                'wpcf') . '</th></tr></thead><tbody>',
    );
    $labels = array(
        'search_items' => array('title' => __('Search %s', 'wpcf'), 'description' => __("The search items text. Default is __( 'Search Tags' ) or __( 'Search Categories' ).",
                    'wpcf')),
        'popular_items' => array('title' => __('Popular %s', 'wpcf'), 'description' => __("The popular items text. Default is __( 'Popular Tags' ) or null.",
                    'wpcf')),
        'all_items' => array('title' => __('All %s', 'wpcf'), 'description' => __("The all items text. Default is __( 'All Tags' ) or __( 'All Categories' ).",
                    'wpcf')),
        'parent_item' => array('title' => __('Parent %s', 'wpcf'), 'description' => __("The parent item text. This string is not used on non-hierarchical taxonomies such as post tags. Default is null or __( 'Parent Category' ).",
                    'wpcf')),
        'parent_item_colon' => array('title' => __('Parent %s:', 'wpcf'), 'description' => __("The same as parent_item, but with colon : in the end null, __( 'Parent Category:' ).",
                    'wpcf')),
        'edit_item' => array('title' => __('Edit %s', 'wpcf'), 'description' => __("The edit item text. Default is __( 'Edit Tag' ) or __( 'Edit Category' ).",
                    'wpcf')),
        'update_item' => array('title' => __('Update %s', 'wpcf'), 'description' => __("The update item text. Default is __( 'Update Tag' ) or __( 'Update Category' ).",
                    'wpcf')),
        'add_new_item' => array('title' => __('Add New %s', 'wpcf'), 'description' => __("The add new item text. Default is __( 'Add New Tag' ) or __( 'Add New Category' ).",
                    'wpcf')),
        'new_item_name' => array('title' => __('New %s Name', 'wpcf'), 'description' => __("The new item name text. Default is __( 'New Tag Name' ) or __( 'New Category Name' ).",
                    'wpcf')),
        'separate_items_with_commas' => array('title' => __('Separate %s with commas',
                    'wpcf'), 'description' => __("The separate item with commas text used in the taxonomy meta box. This string isn't used on hierarchical taxonomies. Default is __( 'Separate tags with commas' ), or null.",
                    'wpcf')),
        'add_or_remove_items' => array('title' => __('Add or remove %s', 'wpcf'), 'description' => __("the add or remove items text and used in the meta box when JavaScript is disabled. This string isn't used on hierarchical taxonomies. Default is __( 'Add or remove tags' ) or null.",
                    'wpcf')),
        'choose_from_most_used' => array('title' => __('Choose from the most used %s',
                    'wpcf'), 'description' => __("The choose from most used text used in the taxonomy meta box. This string isn't used on hierarchical taxonomies. Default is __( 'Choose from the most used tags' ) or null.",
                    'wpcf')),
        'menu_name' => array('title' => __('Menu Name', 'wpcf'), 'description' => __("The menu name text. This string is the name to give menu items. Defaults to value of name.",
                    'wpcf')),
    );
    foreach ($labels as $name => $data) {
        $form['labels-' . $name] = array(
            '#type' => 'textfield',
            '#name' => 'ct[labels][' . $name . ']',
            '#title' => ucwords(str_replace('_', ' ', $name)),
            '#description' => $data['description'],
            '#value' => isset($ct['labels'][$name]) ? $ct['labels'][$name] : '',
            '#inline' => true,
            '#pattern' => '<tr><td><LABEL></td><td><ELEMENT></td><td><DESCRIPTION></td>',
        );
    }
    $form['table-4-close'] = array(
        '#type' => 'markup',
        '#markup' => '</tbody></table>',
    );
    $form['table-6-open'] = array(
        '#type' => 'markup',
        '#markup' => '<table id="wpcf-types-form-supports-table" class="wpcf-types-form-table widefat"><thead><tr><th>' . __('Advanced',
                'wpcf') . '</th></tr></thead><tbody><tr><td>',
    );
    $form['make-hierarchical'] = array(
        '#type' => 'radios',
        '#name' => 'ct[hierarchical]',
        '#default_value' => (empty($ct['hierarchical']) || $ct['hierarchical'] == 'flat') ? 'flat' : 'hierarchical',
//        '#title' => __('hierarchical', 'wpcf'),
//        '#description' => __('Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.',
//                'wpcf') . '<br />' . __('Default: false.', 'wpcf'),
        '#inline' => true,
        '#options' => array(
            __('Hierarchical - like post categories, with parent / children relationship and checkboxes to select taxonomy',
                    'wpcf') => 'hierarchical',
            __('Flat - like post tags, with a text input to enter terms', 'wpcf') => 'flat'
        ),
        '#after' => '<br /><br />',
    );
    $form['rewrite-enabled'] = array(
        '#type' => 'checkbox',
        '#force_boolean' => true,
        '#title' => __('Rewrite', 'wpcf'),
        '#name' => 'ct[rewrite][enabled]',
        '#description' => __('Rewrite permalinks with this format. Default will use $taxonomy as query var.',
                'wpcf'),
        '#default_value' => !empty($ct['rewrite']['enabled']),
        '#inline' => true,
    );
    $hidden = empty($ct['rewrite']['enabled']) ? ' class="hidden"' : '';
    $form['rewrite-slug'] = array(
        '#type' => 'textfield',
        '#name' => 'ct[rewrite][slug]',
        '#title' => __('Prepend posts with this slug', 'wpcf'),
        '#description' => __('Optional', 'wpcf') . '. ' . __("Prepend posts with this slug - defaults to taxonomy's name.",
                'wpcf'),
        '#value' => isset($ct['rewrite']['slug']) ? $ct['rewrite']['slug'] : '',
        '#inline' => true,
        '#before' => '<div id="wpcf-types-form-rewrite-toggle"' . $hidden . '>',
        '#after' => '</div>',
        '#validate' => array('rewriteslug' => array('value' => 'true')),
    );
    $form['rewrite-with_front'] = array(
        '#type' => 'checkbox',
        '#force_boolean' => true,
        '#title' => __('Allow permalinks to be prepended with front base',
                'wpcf'),
        '#name' => 'ct[rewrite][with_front]',
        '#description' => __('Defaults to true.', 'wpcf'),
        '#default_value' => !empty($ct['rewrite']['with_front']),
        '#inline' => true,
    );
    $form['rewrite-hierarchical'] = array(
        '#type' => 'checkbox',
        '#name' => 'ct[rewrite][hierarchical]',
        '#title' => __('Hierarchical URLs', 'wpcf'),
        '#description' => sprintf(__('True or false allow hierarchical urls (implemented in %sVersion 3.1%s).',
                        'wpcf'),
                '<a href="http://codex.wordpress.org/Version_3.1" title="Version 3.1" target="_blank">',
                '</a>'),
        '#default_value' => !empty($ct['rewrite']['hierarchical']),
        '#inline' => true,
    );
    $form['vars'] = array(
        '#type' => 'checkboxes',
        '#name' => 'ct[advanced]',
        '#inline' => true,
        '#options' => array(
            'show_ui' => array(
                '#name' => 'ct[show_ui]',
                '#default_value' => !empty($ct['show_ui']),
                '#title' => __('show_ui', 'wpcf'),
                '#description' => __('Whether to generate a default UI for managing this taxonomy.',
                        'wpcf') . '<br />' . __('Default: if not set, defaults to value of public argument.',
                        'wpcf'),
                '#inline' => true,
            ),
            'show_in_nav_menus' => array(
                '#name' => 'ct[show_in_nav_menus]',
                '#default_value' => !empty($ct['show_in_nav_menus']),
                '#title' => __('show_in_nav_menus', 'wpcf'),
                '#description' => __('True makes this taxonomy available for selection in navigation menus.',
                        'wpcf') . '<br />' . __('Default: if not set, defaults to value of public argument.',
                        'wpcf'),
                '#inline' => true,
            ),
            'show_tagcloud' => array(
                '#name' => 'ct[show_tagcloud]',
                '#default_value' => !empty($ct['show_tagcloud']),
                '#title' => __('show_tagcloud', 'wpcf'),
                '#description' => __('Whether to allow the Tag Cloud widget to use this taxonomy.',
                        'wpcf') . '<br />' . __('Default: if not set, defaults to value of show_ui argument.',
                        'wpcf'),
                '#inline' => true,
            ),
        ),
    );
    $query_var = isset($ct['query_var']) ? $ct['query_var'] : '';
    $hidden = !empty($ct['query_var_enabled']) ? '' : ' class="hidden"';
    $form['query_var'] = array(
        '#type' => 'checkbox',
        '#name' => 'ct[query_var_enabled]',
        '#title' => 'query_var',
        '#description' => __('False to prevent queries, or string to customize query var. Default will use $taxonomy as query var.',
                'wpcf') . '<br />' . __('Default: $taxonomy.', 'wpcf'),
        '#default_value' => !empty($ct['query_var_enabled']),
        '#after' => '<div id="wpcf-types-form-queryvar-toggle"' . $hidden . '><input type="text" name="ct[query_var]" value="' . $query_var . '" class="wpcf-form-textfield form-textfield textfield" /><div class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">' . __('Optional',
                'wpcf') . '. ' . __('String to customize query var', 'wpcf') . '</div></div>',
        '#inline' => true,
    );
    $form['update_count_callback'] = array(
        '#type' => 'textfield',
        '#name' => 'ct[update_count_callback]',
        '#title' => 'update_count_callback', 'wpcf',
        '#description' => __('Function name that will be called to update the count of an associated $object_type, such as post, is updated.',
                'wpcf') . '<br />' . __('Default: None.', 'wpcf'),
        '#value' => !empty($ct['update_count_callback']) ? $ct['update_count_callback'] : '',
        '#inline' => true,
    );
    $form['table-6-close'] = array(
        '#type' => 'markup',
        '#markup' => '</td></tr></tbody></table>',
    );
    $form['submit'] = array(
        '#type' => 'submit',
        '#name' => 'submit',
        '#value' => __('Save Taxonomy', 'wpcf'),
        '#attributes' => array('class' => 'button-primary'),
    );

    return $form;
}

/**
 * Adds JS validation script.
 */
function wpcf_admin_tax_form_js_validation() {
    wpcf_form_render_js_validation();
}

/**
 * Submit function
 */
function wpcf_admin_custom_taxonomies_form_submit($form) {
    if (!isset($_POST['ct'])) {
        return false;
    }
    $data = $_POST['ct'];
    $update = false;

    // Sanitize data
    if (isset($data['wpcf-tax'])) {
        $update = true;
        $data['wpcf-tax'] = sanitize_title($data['wpcf-tax']);
    }
    if (isset($data['slug'])) {
        $data['slug'] = sanitize_title($data['slug']);
    }
    if (isset($data['rewrite']['slug'])) {
        $data['rewrite']['slug'] = remove_accents($data['rewrite']['slug']);
        $data['rewrite']['slug'] = strtolower($data['rewrite']['slug']);
        $data['rewrite']['slug'] = trim($data['rewrite']['slug']);
    }

    // Set tax name
    $tax = '';
    if (!empty($data['slug'])) {
        $tax = $data['slug'];
    } else if (!empty($data['wpcf-tax'])) {
        $tax = $data['wpcf-tax'];
    } else if (!empty($data['labels']['singular_name'])) {
        $tax = sanitize_title($data['labels']['singular_name']);
    }

    if (empty($tax)) {
        wpcf_admin_message(__('Please set taxonomy name', 'wpcf'), 'error');
        return false;
    }

    if (empty($data['labels']['singular_name'])) {
        $data['labels']['singular_name'] = $tax;
    }

    $data['slug'] = $tax;
    $custom_taxonomies = get_option('wpcf-custom-taxonomies', array());

    // Check if exists
    if ($update && !array_key_exists($data['wpcf-tax'], $custom_taxonomies)) {
        wpcf_admin_message(__("Custom taxonomy do not exist", 'wpcf'), 'error');
        return false;
    }

    // Check overwriting
    if (!$update && array_key_exists($tax, $custom_taxonomies)) {
        wpcf_admin_message(__('Custom taxonomy already exists', 'wpcf'), 'error');
        return false;
    }

    $built_in_tax = array('category', 'post_tag', 'post-tag', 'link_category', 'link-category');

    // Check if our tax overwrites some tax outside
    $tax_exists = get_taxonomy($tax);
    if (!$update && (!empty($tax_exists) || in_array($tax, $built_in_tax))) {
        wpcf_admin_message(__('Taxonomy already exists', 'wpcf'), 'error');
        return false;
    }
    if ($update && (in_array($data['wpcf-tax'], $built_in_tax) || in_array($tax,
                    $built_in_tax))) {
        wpcf_admin_message(__('Taxonomy already exists', 'wpcf'), 'error');
        return false;
    }

    // Check if renaming
    if ($update && $data['wpcf-tax'] != $tax) {
        global $wpdb;
        $wpdb->update($wpdb->term_taxonomy, array('taxonomy' => $tax),
                array('taxonomy' => $data['wpcf-tax']), array('%s'), array('%s')
        );
        // Delete old type
        unset($custom_taxonomies[$data['wpcf-tax']]);
    }

    // Check if active
    if (isset($custom_taxonomies[$tax]['disabled'])) {
        $data['disabled'] = $custom_taxonomies[$tax]['disabled'];
    }

    // Sync with post types
    if (!empty($data['supports'])) {
        $post_types = get_option('wpcf-custom-types', array());
        foreach ($post_types as $id => $type) {
            if (array_key_exists($id, $data['supports'])) {
                $post_types[$id]['taxonomies'][$data['slug']] = 1;
            } else {
                unset($post_types[$id]['taxonomies'][$data['slug']]);
            }
        }
        update_option('wpcf-custom-types', $post_types);
    }

    $custom_taxonomies[$tax] = $data;
    update_option('wpcf-custom-taxonomies', $custom_taxonomies);

    // WPML register strings
    wpcf_custom_taxonimies_register_translation($tax, $data);

    wpcf_admin_message_store(__('Custom taxonomy saved', 'wpcf'));

    // Flush rewrite rules
    flush_rewrite_rules();

    // Redirect
    wp_redirect(admin_url('admin.php?page=wpcf-edit-tax&wpcf-tax=' . $tax . '&wpcf-rewrite=1'));
    die();
}

/**
 * Registers translation data.
 * 
 * @param type $post_type
 * @param type $data 
 */
function wpcf_custom_taxonimies_register_translation($taxonomy, $data) {
    if (!function_exists('icl_register_string')) {
        return $data;
    }
    $default = wpcf_custom_taxonomies_default();
    if (isset($data['description'])) {
        wpcf_translate_register_string('Types-TAX', $taxonomy . ' description',
                $data['description']);
    }
    foreach ($data['labels'] as $label => $string) {
        if ($label == 'name' || $label == 'singular_name') {
            wpcf_translate_register_string('Types-TAX', $taxonomy . ' ' . $label, $string);
            continue;
        }
        if (!isset($default['labels'][$label]) || $string !== $default['labels'][$label]) {
            wpcf_translate_register_string('Types-TAX', $taxonomy . ' ' . $label, $string);
        }
    }
}