<?php
/*
 * Fields and groups list functions
 */

/**
 * Renders 'widefat' table.
 */
function wpcf_admin_fields_list() {
    $groups = wpcf_admin_fields_get_groups();
    
    if (empty($groups)) {
        echo '<p>'
        . __("Custom Fields (also known as post-meta) are information attached to WordPress posts. This data can then be queried and filtered in your database. With Types you can create Custom Field Groups which are then attached to any post type (including Posts, Pages and Custom Post Types) or taxonomy.",
                'wpcf')
        . '<br /><br />'
        . __('You can read more about Custom Fields in this tutorial: <a href="http://wp-types.com/user-guides/using-custom-fields/" target="_blank">http://wp-types.com/user-guides/using-custom-fields/ &raquo;</a>.',
                'wpcf')
        . '</p>';
    }
    
    echo '<br /><a class="button-secondary" href="'
    . admin_url('admin.php?page=wpcf-edit')
    . '">' . __('Add a custom fields group', 'wpcf') . '</a><br /><br />';
    if (!empty($groups)) {
        $rows = array();
        $header = array(
            'group_name' => __('Group name', 'wpcf'),
            'group_description' => __('Description', 'wpcf'),
            'group_active' => __('Active', 'wpcf'),
            'group_post_types' => __('Post types', 'wpcf'),
            'group_taxonomies' => __('Taxonomies', 'wpcf'),
        );
        foreach ($groups as $group) {

            // Set 'name' column
            $name = '';
            $name .= '<a href="'
                    . admin_url('admin.php?page=wpcf-edit&amp;group_id='
                            . $group['id']) . '">' . $group['name'] . '</a>';
            $name .= '<br />';
            $name .= '<a href="'
                    . admin_url('admin.php?page=wpcf-edit&amp;group_id='
                            . $group['id']) . '">' . __('Edit', 'wpcf') . '</a> | ';

            $name .= $group['is_active'] ? wpcf_admin_fields_get_ajax_deactivation_link($group['id']) . ' | ' : wpcf_admin_fields_get_ajax_activation_link($group['id']) . ' | ';

            $name .= '<a href="'
                    . admin_url('admin-ajax.php?action=wpcf_ajax&amp;'
                            . 'wpcf_action=delete_group&amp;group_id='
                            . $group['id'] . '&amp;wpcf_ajax_update=wpcf_list_ajax_response_'
                            . $group['id']) . '&amp;_wpnonce=' . wp_create_nonce('delete_group')
                    . '&amp;wpcf_warning='
                    . __('Are you sure?', 'wpcf') . '" class="wpcf-ajax-link" '
                    . 'id="wpcf-list-delete-' . $group['id'] . '">'
                    . __('Delete Permanently', 'wpcf') . '</a>';

            $name .= '<div id="wpcf_list_ajax_response_' . $group['id'] . '"></div>';

            $rows[$group['id']]['name'] = $name;


            $rows[$group['id']]['description'] = $group['description'];
            $rows[$group['id']]['active-' . $group['id']] = $group['is_active'] ? __('Yes', 'wpcf') : __('No', 'wpcf');

            // Set 'post_tpes' column
            $post_types = wpcf_admin_get_post_types_by_group($group['id']);
            $rows[$group['id']]['post_types'] = empty($post_types) ? __('None',
                            'wpcf') : implode(', ', $post_types);

            // Set 'taxonomies' column
            $taxonomies = wpcf_admin_get_taxonomies_by_group($group['id']);
            $output = '';
            if (empty($taxonomies)) {
                $output = __('None', 'wpcf');
            } else {
                foreach ($taxonomies as $taxonomy => $terms) {
                    $output .= '<em>' . $taxonomy . '</em>: ';
                    $terms_output = array();
                    foreach ($terms as $term_id => $term) {
                        $terms_output[] = $term['name'];
                    }
                    $output .= implode(', ', $terms_output) . '<br />';
                }
            }
            $rows[$group['id']]['tax'] = $output;
        }

        // Render table
        wpcf_admin_widefat_table('wpcf_groups_list', $header, $rows);
    }
    
    do_action('wpcf_groups_list_table_after');
}