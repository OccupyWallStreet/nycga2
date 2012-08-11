<?php
/*
 * Custom types form
 */

/**
 * Add/edit form
 */
function wpcf_admin_custom_types_form() {

    $ct = array();
    $id = false;
    $update = false;

    if (isset($_GET['wpcf-post-type'])) {
        $id = $_GET['wpcf-post-type'];
    } else if (isset($_POST['wpcf-post-type'])) {
        $id = $_POST['wpcf-post-type'];
    }

    if ($id) {
        $custom_types = get_option('wpcf-custom-types', array());
        if (isset($custom_types[$id])) {
            $ct = $custom_types[$id];
            $update = true;
            // Set rewrite if needed
            if (isset($_GET['wpcf-rewrite'])) {
                flush_rewrite_rules();
            }
        } else {
            wpcf_admin_message(__('Wrong custom post type specified', 'wpcf'),
                    'error');
            return false;
        }
    } else {
        $ct = wpcf_custom_types_default();
    }

    $form = array();
    $form['#form']['callback'] = 'wpcf_admin_custom_types_form_submit';
    $form['#form']['redirection'] = false;

    if ($update) {
        $form['id'] = array(
            '#type' => 'hidden',
            '#value' => $id,
            '#name' => 'ct[wpcf-post-type]',
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
        '#title' => __('Custom post type name plural', 'wpcf') . ' (<strong>' . __('required',
                'wpcf') . '</strong>)',
        '#description' => '<strong>' . __('Enter in plural!', 'wpcf')
//        . '</strong><br />' . __('Alphanumeric with whitespaces only', 'wpcf')
        . '.',
        '#value' => isset($ct['labels']['name']) ? $ct['labels']['name'] : '',
        '#validate' => array(
            'required' => array('value' => 'true'),
//            'alphanumeric' => array('value' => 'true'),
        ),
        '#pattern' => $table_row,
        '#inline' => true,
    );
    $form['name-singular'] = array(
        '#type' => 'textfield',
        '#name' => 'ct[labels][singular_name]',
        '#title' => __('Custom post type name singular', 'wpcf') . ' (<strong>' . __('required',
                'wpcf') . '</strong>)',
        '#description' => '<strong>' . __('Enter in singular!', 'wpcf')
        . '</strong><br />'
//        . __('Alphanumeric with whitespaces only', 'wpcf')
        . '.',
        '#value' => isset($ct['labels']['singular_name']) ? $ct['labels']['singular_name'] : '',
        '#validate' => array(
            'required' => array('value' => 'true'),
//            'alphanumeric' => array('value' => 'true'),
        ),
        '#pattern' => $table_row,
        '#inline' => true,
    );
    $form['slug'] = array(
        '#type' => 'textfield',
        '#name' => 'ct[slug]',
        '#title' => __('Slug', 'wpcf') . ' (<strong>' . __('required', 'wpcf') . '</strong>)',
//        '#description' => '<strong>' . __('Enter in singular!', 'wpcf')
//        . '</strong><br />' . __('Machine readable name.', 'wpcf')
//        . '<br />' . __('If not provided - will be created from singular name.',
//                'wpcf') . '<br />',
        '#value' => isset($ct['slug']) ? $ct['slug'] : '',
        '#pattern' => $table_row,
        '#inline' => true,
        '#validate' => array(
            'required' => array('value' => 'true'),
            'nospecialchars' => array('value' => 'true'),
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
            __('Make this type public (will appear in the WordPress Admin menu)',
                    'wpcf') => 'public',
            __('Hidden - users cannot directly edit data in this type', 'wpcf') => 'hidden',
        ),
        '#default_value' => (isset($ct['public']) && strval($ct['public']) == 'hidden') ? 'hidden' : 'public',
        '#inline' => true,
    );
    $hidden = (isset($ct['public']) && strval($ct['public']) == 'hidden') ? ' class="hidden"' : '';
    $form['menu_position'] = array(
        '#type' => 'textfield',
        '#name' => 'ct[menu_position]',
        '#title' => __('Menu position', 'wpcf'),
        '#value' => isset($ct['menu_position']) ? $ct['menu_position'] : '',
        '#validate' => array('number' => array('value' => true)),
        '#inline' => true,
        '#pattern' => '<div' . $hidden . ' id="wpcf-types-form-visiblity-toggle"><table><tr><td><LABEL></td><td><ELEMENT><ERROR></td></tr>',
    );
    $form['menu_icon'] = array(
        '#type' => 'textfield',
        '#name' => 'ct[menu_icon]',
        '#title' => __('Menu icon', 'wpcf'),
        '#description' => __('The url to the icon to be used for this menu. Default: null - defaults to the posts icon.',
                'wpcf'),
        '#value' => isset($ct['menu_icon']) ? $ct['menu_icon'] : '',
        '#inline' => true,
        '#pattern' => '<tr><td><LABEL></td><td><ELEMENT><ERROR></td></tr></table></div>',
    );
    $form['table-2-close'] = array(
        '#type' => 'markup',
        '#markup' => '</td></tr></tbody></table>',
    );

    $taxonomies = get_taxonomies('', 'objects');
    $options = array();

    foreach ($taxonomies as $category_slug => $category) {
        if ($category_slug == 'nav_menu' || $category_slug == 'link_category'
                || $category_slug == 'post_format') {
            continue;
        }
        $options[$category_slug]['#name'] = 'ct[taxonomies][' . $category_slug . ']';
        $options[$category_slug]['#title'] = $category->labels->name;
        $options[$category_slug]['#default_value'] = !empty($ct['taxonomies'][$category_slug]);
        $options[$category_slug]['#inline'] = true;
        $options[$category_slug]['#after'] = '&nbsp;&nbsp;';
    }

    $form['table-3-open'] = array(
        '#type' => 'markup',
        '#markup' => '<table id="wpcf-types-form-taxonomies-table" class="wpcf-types-form-table widefat"><thead><tr><th>' . __('Select Taxonomies',
                'wpcf') . '</th></tr></thead><tbody><tr><td>',
    );
    $form['taxonomies'] = array(
        '#type' => 'checkboxes',
        '#options' => $options,
        '#description' => __('Registered taxonomies that will be used with this post type.',
                'wpcf'),
        '#name' => 'ct[taxonomies]',
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
        'add_new' => array('title' => __('Add New', 'wpcf'), 'description' => __('The add new text. The default is Add New for both hierarchical and non-hierarchical types.',
                    'wpcf')),
        'add_new_item' => array('title' => __('Add New %s', 'wpcf'), 'description' => __('The add new item text. Default is Add New Post/Add New Page.',
                    'wpcf')),
//        'edit' => array('title' => __('Edit', 'wpcf'), 'description' => __('The edit item text. Default is Edit Post/Edit Page.', 'wpcf')),
        'edit_item' => array('title' => __('Edit %s', 'wpcf'), 'description' => __('The edit item text. Default is Edit Post/Edit Page.',
                    'wpcf')),
        'new_item' => array('title' => __('New %s', 'wpcf'), 'description' => __('The view item text. Default is View Post/View Page.',
                    'wpcf')),
//        'view' => array('title' => __('View', 'wpcf'), 'description' => __('', 'wpcf')),
        'view_item' => array('title' => __('View %s', 'wpcf'), 'description' => __('The view item text. Default is View Post/View Page.',
                    'wpcf')),
        'search_items' => array('title' => __('Search %s', 'wpcf'), 'description' => __('The search items text. Default is Search Posts/Search Pages.',
                    'wpcf')),
        'not_found' => array('title' => __('No %s found', 'wpcf'), 'description' => __('The not found text. Default is No posts found/No pages found.',
                    'wpcf')),
        'not_found_in_trash' => array('title' => __('No %s found in Trash',
                    'wpcf'), 'description' => __('The not found in trash text. Default is No posts found in Trash/No pages found in Trash.',
                    'wpcf')),
        'parent_item_colon' => array('title' => __('Parent text', 'wpcf'), 'description' => __("The parent text. This string isn't used on non-hierarchical types. In hierarchical ones the default is Parent Page.",
                    'wpcf')),
        'all_items' => array('title' => __('All items', 'wpcf'), 'description' => __('The all items text used in the menu. Default is the Name label.',
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
    $form['table-5-open'] = array(
        '#type' => 'markup',
        '#markup' => '<table id="wpcf-types-form-supports-table" class="wpcf-types-form-table widefat"><thead><tr><th>' . __('Display Sections',
                'wpcf') . '</th></tr></thead><tbody><tr><td>',
    );
    $options = array(
        'title' => array(
            '#name' => 'ct[supports][title]',
            '#default_value' => !empty($ct['supports']['title']),
            '#title' => __('Title', 'wpcf'),
            '#description' => __('Text input field to create a post title.',
                    'wpcf'),
            '#inline' => true,
        ),
        'editor' => array(
            '#name' => 'ct[supports][editor]',
            '#default_value' => !empty($ct['supports']['editor']),
            '#title' => __('Editor', 'wpcf'),
            '#description' => __('Content input box for writing.', 'wpcf'),
            '#inline' => true,
        ),
        'comments' => array(
            '#name' => 'ct[supports][comments]',
            '#default_value' => !empty($ct['supports']['comments']),
            '#title' => __('Comments', 'wpcf'),
            '#description' => __('Ability to turn comments on/off.', 'wpcf'),
            '#inline' => true,
        ),
        'trackbacks' => array(
            '#name' => 'ct[supports][trackbacks]',
            '#default_value' => !empty($ct['supports']['trackbacks']),
            '#title' => __('Trackbacks', 'wpcf'),
            '#description' => __('Ability to turn trackbacks and pingbacks on/off.',
                    'wpcf'),
            '#inline' => true,
        ),
        'revisions' => array(
            '#name' => 'ct[supports][revisions]',
            '#default_value' => !empty($ct['supports']['revisions']),
            '#title' => __('Revisions', 'wpcf'),
            '#description' => __('Allows revisions to be made of your post.',
                    'wpcf'),
            '#inline' => true,
        ),
        'author' => array(
            '#name' => 'ct[supports][author]',
            '#default_value' => !empty($ct['supports']['author']),
            '#title' => __('Author', 'wpcf'),
            '#description' => __('Displays a dropdown menu for changing the post author.',
                    'wpcf'),
            '#inline' => true,
        ),
        'excerpt' => array(
            '#name' => 'ct[supports][excerpt]',
            '#default_value' => !empty($ct['supports']['excerpt']),
            '#title' => __('Excerpt', 'wpcf'),
            '#description' => __('A text area for writing a custom excerpt.',
                    'wpcf'),
            '#inline' => true,
        ),
        'thumbnail' => array(
            '#name' => 'ct[supports][thumbnail]',
            '#default_value' => !empty($ct['supports']['thumbnail']),
            '#title' => __('Thumbnail', 'wpcf'),
            '#description' => __('Add a box for uploading a featured image.',
                    'wpcf'),
            '#inline' => true,
        ),
        'custom-fields' => array(
            '#name' => 'ct[supports][custom-fields]',
            '#default_value' => !empty($ct['supports']['custom-fields']),
            '#title' => __('custom-fields', 'wpcf'),
            '#description' => __('Custom fields input area.', 'wpcf'),
            '#inline' => true,
        ),
        'page-attributes' => array(
            '#name' => 'ct[supports][page-attributes]',
            '#default_value' => !empty($ct['supports']['page-attributes']),
            '#title' => __('page-attributes', 'wpcf'),
            '#description' => __('Menu order, hierarchical must be true to show Parent option',
                    'wpcf'),
            '#inline' => true,
        ),
        'post-formats' => array(
            '#name' => 'ct[supports][post-formats]',
            '#default_value' => !empty($ct['supports']['post-formats']),
            '#title' => __('post-formats', 'wpcf'),
            '#description' => sprintf(__('Add post formats, see %sPost Formats%s',
                            'wpcf'),
                    '<a href="http://codex.wordpress.org/Post_Formats" title="Post Formats" target="_blank">',
                    '</a>'),
            '#inline' => true,
        ),
    );
    $form['supports'] = array(
        '#type' => 'checkboxes',
        '#options' => $options,
        '#name' => 'ct[supports]',
        '#inline' => true,
    );
    $form['table-5-close'] = array(
        '#type' => 'markup',
        '#markup' => '</td></tr></tbody></table>',
    );
    $form['table-6-open'] = array(
        '#type' => 'markup',
        '#markup' => '<table id="wpcf-types-form-supports-table" class="wpcf-types-form-table widefat"><thead><tr><th>' . __('Advanced',
                'wpcf') . '</th></tr></thead><tbody><tr><td>',
    );
    $form['rewrite-enabled'] = array(
        '#type' => 'checkbox',
        '#title' => __('Rewrite', 'wpcf'),
        '#name' => 'ct[rewrite][enabled]',
        '#description' => __('Rewrite permalinks with this format. False to prevent rewrite. Default: true and use post type as slug.',
                'wpcf'),
        '#default_value' => !empty($ct['rewrite']['enabled']),
        '#inline' => true,
    );
    $form['rewrite-custom'] = array(
        '#type' => 'radios',
        '#name' => 'ct[rewrite][custom]',
        '#options' => array(
            __('Use the normal WordPress URL logic', 'wpcf') => 'normal',
            __('Use a custom URL format', 'wpcf') => 'custom',
        ),
        '#default_value' => empty($ct['rewrite']['custom']) || $ct['rewrite']['custom'] != 'custom' ? 'normal' : 'custom',
        '#inline' => true,
        '#after' => '<br />',
    );
    $hidden = empty($ct['rewrite']['custom']) || $ct['rewrite']['custom'] != 'custom' ? ' class="hidden"' : '';
    $form['rewrite-slug'] = array(
        '#type' => 'textfield',
        '#name' => 'ct[rewrite][slug]',
        '#description' => __('Optional.', 'wpcf') . ' ' . __("Prepend posts with this slug - defaults to post type's name.",
                'wpcf'),
        '#value' => isset($ct['rewrite']['slug']) ? $ct['rewrite']['slug'] : '',
        '#inline' => true,
        '#before' => '<div id="wpcf-types-form-rewrite-toggle"' . $hidden . '>',
        '#after' => '</div>',
        '#validate' => array('rewriteslug' => array('value' => 'true')),
    );
    $form['rewrite-with_front'] = array(
        '#type' => 'checkbox',
        '#title' => __('Allow permalinks to be prepended with front base',
                'wpcf'),
        '#name' => 'ct[rewrite][with_front]',
        '#description' => __('Example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/.',
                'wpcf') . ' ' . __('Defaults to true.', 'wpcf'),
        '#default_value' => !empty($ct['rewrite']['with_front']),
        '#inline' => true,
    );
    $form['rewrite-feeds'] = array(
        '#type' => 'checkbox',
        '#name' => 'ct[rewrite][feeds]',
        '#title' => __('Feeds', 'wpcf'),
        '#description' => __('Defaults to has_archive value.', 'wpcf'),
        '#default_value' => !empty($ct['rewrite']['feeds']),
        '#value' => 1,
        '#inline' => true,
    );
    $form['rewrite-pages'] = array(
        '#type' => 'checkbox',
        '#name' => 'ct[rewrite][pages]',
        '#title' => __('Pages', 'wpcf'),
        '#description' => __('Defaults to true.', 'wpcf'),
        '#default_value' => !empty($ct['rewrite']['pages']),
        '#value' => 1,
        '#inline' => true,
    );
    $show_in_menu_page = isset($ct['show_in_menu_page']) ? $ct['show_in_menu_page'] : '';
    $hidden = !empty($ct['show_in_menu']) ? '' : ' class="hidden"';
    $form['vars'] = array(
        '#type' => 'checkboxes',
        '#name' => 'ct[vars]',
        '#inline' => true,
        '#options' => array(
            'has_archive' => array(
                '#name' => 'ct[has_archive]',
                '#default_value' => !empty($ct['has_archive']),
                '#title' => __('has_archive', 'wpcf'),
                '#description' => __('Allow custom post type to have index page.',
                        'wpcf') . '<br />' . __('Default: not set.', 'wpcf'),
                '#inline' => true,
            ),
            'show_in_menu' => array(
                '#name' => 'ct[show_in_menu]',
                '#default_value' => !empty($ct['show_in_menu']),
                '#title' => __('show_in_menu', 'wpcf'),
                '#description' => __('Whether to show the post type in the admin menu and where to show that menu. Note that show_ui must be true.',
                        'wpcf') . '<br />' . __('Default: null.', 'wpcf'),
                '#after' => '<div id="wpcf-types-form-showinmenu-toggle"' . $hidden . '><input type="text" name="ct[show_in_menu_page]" style="width:50%;" value="' . $show_in_menu_page . '" /><div class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">' . __('Optional.',
                        'wpcf') . ' ' . __("Top level page like 'tools.php' or 'edit.php?post_type=page'",
                        'wpcf') . '</div></div>',
                '#inline' => true,
            ),
            'show_ui' => array(
                '#name' => 'ct[show_ui]',
                '#default_value' => !empty($ct['show_ui']),
                '#title' => __('show_ui', 'wpcf'),
                '#description' => __('Generate a default UI for managing this post type.',
                        'wpcf') . '<br />' . __('Default: value of public argument.',
                        'wpcf'),
                '#inline' => true,
            ),
            'publicly_queryable' => array(
                '#name' => 'ct[publicly_queryable]',
                '#default_value' => !empty($ct['publicly_queryable']),
                '#title' => __('publicly_queryable', 'wpcf'),
                '#description' => __('Whether post_type queries can be performed from the front end.',
                        'wpcf') . '<br />' . __('Default: value of public argument.',
                        'wpcf'),
                '#inline' => true,
            ),
            'exclude_from_search' => array(
                '#name' => 'ct[exclude_from_search]',
                '#default_value' => !empty($ct['exclude_from_search']),
                '#title' => __('exclude_from_search', 'wpcf'),
                '#description' => __('Whether to exclude posts with this post type from search results.',
                        'wpcf') . '<br />' . __('Default: value of the opposite of the public argument.',
                        'wpcf'),
                '#inline' => true,
            ),
            'hierarchical' => array(
                '#name' => 'ct[hierarchical]',
                '#default_value' => !empty($ct['hierarchical']),
                '#title' => __('hierarchical', 'wpcf'),
                '#description' => __('Whether the post type is hierarchical. Allows Parent to be specified.',
                        'wpcf') . '<br />' . __('Default: false.', 'wpcf'),
                '#inline' => true,
            ),
            'can_export' => array(
                '#name' => 'ct[can_export]',
                '#default_value' => !empty($ct['can_export']),
                '#title' => __('can_export', 'wpcf'),
                '#description' => __('Can this post_type be exported.', 'wpcf') . '<br />' . __('Default: true.',
                        'wpcf'),
                '#inline' => true,
            ),
            'show_in_nav_menus' => array(
                '#name' => 'ct[show_in_nav_menus]',
                '#default_value' => !empty($ct['show_in_nav_menus']),
                '#title' => __('show_in_nav_menus', 'wpcf'),
                '#description' => __('Whether post_type is available for selection in navigation menus.',
                        'wpcf') . '<br />' . __('Default: value of public argument.',
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
        '#description' => __('False to prevent queries, or string value of the query var to use for this post type.',
                'wpcf') . '<br />' . __('Default: true - set to $post_type.',
                'wpcf'),
        '#default_value' => !empty($ct['query_var_enabled']),
        '#after' => '<div id="wpcf-types-form-queryvar-toggle"' . $hidden . '><input type="text" name="ct[query_var]" value="' . $query_var . '" style="width:50%;" /><div class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">' . __('Optional',
                'wpcf') . '. ' . __('String to customize query var', 'wpcf') . '</div></div>',
        '#inline' => true,
    );
    $form['permalink_epmask'] = array(
        '#type' => 'textfield',
        '#name' => 'ct[permalink_epmask]',
        '#title' => __('Permalink epmask', 'wpcf'),
        '#description' => sprintf(__('Default value EP_PERMALINK. More info here %s.',
                        'wpcf'),
                '<a href="http://core.trac.wordpress.org/ticket/12605" target="_blank">link</a>'),
        '#value' => isset($ct['permalink_epmask']) ? $ct['permalink_epmask'] : '',
        '#inline' => true,
    );
    $form['table-6-close'] = array(
        '#type' => 'markup',
        '#markup' => '</td></tr></tbody></table>',
    );

    $form = $form + apply_filters('wpcf_post_type_form', array(), $ct);

    $form['submit'] = array(
        '#type' => 'submit',
        '#name' => 'submit',
        '#value' => __('Save Custom Post Type', 'wpcf'),
        '#attributes' => array('class' => 'button-primary'),
    );

    return $form;
}

/**
 * Adds JS validation script.
 */
function wpcf_admin_types_form_js_validation() {
    wpcf_form_render_js_validation();
}

/**
 * Submit function
 */
function wpcf_admin_custom_types_form_submit($form) {
    if (!isset($_POST['ct'])) {
        return false;
    }
    $data = $_POST['ct'];
    $update = false;

    // Sanitize data
    if (isset($data['wpcf-post-type'])) {
        $update = true;
        $data['wpcf-post-type'] = sanitize_title($data['wpcf-post-type']);
    }
    if (isset($data['slug'])) {
        $data['slug'] = sanitize_title($data['slug']);
    }
    if (isset($data['rewrite']['slug'])) {
        $data['rewrite']['slug'] = remove_accents($data['rewrite']['slug']);
        $data['rewrite']['slug'] = strtolower($data['rewrite']['slug']);
        $data['rewrite']['slug'] = trim($data['rewrite']['slug']);
    }

    // Set post type name
    $post_type = '';
    if (!empty($data['slug'])) {
        $post_type = $data['slug'];
    } else if (!empty($data['wpcf-post-type'])) {
        $post_type = $data['wpcf-post-type'];
    } else if (!empty($data['labels']['singular_name'])) {
        $post_type = sanitize_title($data['labels']['singular_name']);
    }

    if (empty($post_type)) {
        wpcf_admin_message(__('Please set post type name', 'wpcf'), 'error');
//        $form->triggerError();
        return false;
    }

    $data['slug'] = $post_type;
    $custom_types = get_option('wpcf-custom-types', array());

    // Check overwriting
    if (!$update && array_key_exists($post_type, $custom_types)) {
        wpcf_admin_message(__('Custom post type already exists', 'wpcf'),
                'error');
//            $form->triggerError();
        return false;
    }

    // Check if renaming then rename all post entries and delete old type
    if (!empty($data['wpcf-post-type'])
            && $data['wpcf-post-type'] != $post_type) {
        global $wpdb;
        $wpdb->update($wpdb->posts, array('post_type' => $post_type),
                array('post_type' => $data['wpcf-post-type']), array('%s'),
                array('%s')
        );
        // Delete old type
        unset($custom_types[$data['wpcf-post-type']]);
    }

    // Check if active
    if (isset($custom_types[$post_type]['disabled'])) {
        $data['disabled'] = $custom_types[$post_type]['disabled'];
    }

    // Sync taxes with custom taxes
    if (!empty($data['taxonomies'])) {
        $taxes = get_option('wpcf-custom-taxonomies', array());
        foreach ($taxes as $id => $tax) {
            if (array_key_exists($id, $data['taxonomies'])) {
                $taxes[$id]['supports'][$data['slug']] = 1;
            } else {
                unset($taxes[$id]['supports'][$data['slug']]);
            }
        }
        update_option('wpcf-custom-taxonomies', $taxes);
    }

    $custom_types[$post_type] = $data;
    update_option('wpcf-custom-types', $custom_types);

    // WPML register strings
    wpcf_custom_types_register_translation($post_type, $data);

    wpcf_admin_message_store(__('Custom post type saved', 'wpcf'));

    // Flush rewrite rules
    flush_rewrite_rules();

    do_action('wpcf_custom_types_save', $data);

    // Redirect
    wp_redirect(admin_url('admin.php?page=wpcf-edit-type&wpcf-post-type=' . $post_type . '&wpcf-rewrite=1'));
    die();
}

/**
 * Registers translation data.
 * 
 * @param type $post_type
 * @param type $data 
 */
function wpcf_custom_types_register_translation($post_type, $data) {
    if (!function_exists('icl_register_string')) {
        return $data;
    }
    $default = wpcf_custom_types_default();
    if (isset($data['description'])) {
        wpcf_translate_register_string('Types-CPT', $post_type . ' description',
                $data['description']);
    }
    foreach ($data['labels'] as $label => $string) {
        if ($label == 'name' || $label == 'singular_name') {
            wpcf_translate_register_string('Types-CPT', $post_type . ' ' . $label, $string);
            continue;
        }
        if (!isset($default['labels'][$label]) || $string !== $default['labels'][$label]) {
            wpcf_translate_register_string('Types-CPT', $post_type . ' ' . $label, $string);
        }
    }
}