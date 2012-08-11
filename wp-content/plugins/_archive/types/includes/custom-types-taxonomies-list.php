<?php
/*
 * Custom Types and Taxonomies list functions
 */

/**
 * Renders 'widefat' table.
 */
function wpcf_admin_ctt_list() {
    $custom_types = get_option('wpcf-custom-types', array());
    $custom_taxonomies = get_option('wpcf-custom-taxonomies', array());

    if (empty($custom_types) && empty($custom_taxonomies)) {
        echo '<p>'
        . __('Custom Post Types are user-defined content types. Custom Taxonomies are used to categorize your content.',
                'wpcf')
        . ' ' . __('You can read more about Custom Post Types and Taxonomies in this tutorial. <a href="http://wp-types.com/learn/custom-post-types/" target="_blank">http://wp-types.com/learn/custom-post-types/ &raquo;</a>',
                'wpcf')
        . '</p>';
    }

    echo '<br /><a class="button-secondary" href="'
    . admin_url('admin.php?page=wpcf-edit-type')
    . '">' . __('Add Custom Post Type', 'wpcf') . '</a>'
    . '&nbsp;&nbsp;<a class="button-secondary" href="'
    . admin_url('admin.php?page=wpcf-edit-tax')
    . '">' . __('Add Custom Taxonomy', 'wpcf') . '</a><br /><br />';
    if (!empty($custom_types)) {
        $rows = array();
        $header = array(
            'name' => __('Post Type Name', 'wpcf'),
            'description' => __('Description', 'wpcf'),
            'active' => __('Active', 'wpcf'),
            'tax' => __('Taxonomies', 'wpcf'),
        );
        foreach ($custom_types as $post_type => $type) {
            $name = '';
            $name .= '<a href="'
                    . admin_url('admin.php?page=wpcf-edit-type&amp;wpcf-post-type='
                            . $post_type) . '">' . stripslashes(wpcf_translate($post_type . ' name',
                                    $type['labels']['name'], 'Types-CPT')) . '</a>';
            $name .= '<br />';
            $name .= '<a href="'
                    . admin_url('admin.php?page=wpcf-edit-type&amp;wpcf-post-type='
                            . $post_type) . '">' . __('Edit', 'wpcf') . '</a> | ';
            $name .= empty($type['disabled']) ? wpcf_admin_custom_types_get_ajax_deactivation_link($post_type) . ' | ' : wpcf_admin_custom_types_get_ajax_activation_link($post_type) . ' | ';
            $name .= '<a href="'
                    . admin_url('admin-ajax.php?action=wpcf_ajax&amp;'
                            . 'wpcf_action=delete_post_type&amp;wpcf-post-type='
                            . $post_type . '&amp;wpcf_ajax_update=wpcf_list_ajax_response_'
                            . $post_type) . '&amp;wpcf_ajax_callback=wpcfRefresh&amp;_wpnonce='
                    . wp_create_nonce('delete_post_type') . '&amp;wpcf_warning='
                    . __('Are you sure?', 'wpcf') . '" class="wpcf-ajax-link" id="wpcf-list-delete-'
                    . $post_type . '">'
                    . __('Delete Permanently', 'wpcf') . '</a>';
            $name .= '<div id="wpcf_list_ajax_response_' . $post_type . '"></div>';
            $rows[$post_type]['name'] = $name;
            $rows[$post_type]['description'] = isset($type['description']) ? htmlspecialchars(stripslashes(wpcf_translate($post_type . ' description',
                                            $type['description'], 'Types-CPT')),
                            ENT_QUOTES) : '';
            $rows[$post_type]['active-' . $post_type] = !empty($type['disabled']) ? __('No',
                            'wpcf') : __('Yes', 'wpcf');
            $rows[$post_type]['tax'] = array();
            if (!empty($type['taxonomies'])) {
                foreach ($type['taxonomies'] as $temp_tax => $true) {
                    $rows[$post_type]['tax'][] = stripslashes(wpcf_translate($temp_tax . ' name',
                                    $temp_tax, 'Types-TAX'));
                }
            }
            $rows[$post_type]['tax'] = !empty($rows[$post_type]['tax']) ? implode(', ',
                            $rows[$post_type]['tax']) : __('None', 'wpcf');
        }

        // Render table
        wpcf_admin_widefat_table('wpcf_types_list', $header, $rows);
    }

    if (!empty($custom_taxonomies)) {
        $rows = array();
        $header = array(
            'name' => __('Taxonomy Name', 'wpcf'),
            'description' => __('Description', 'wpcf'),
            'active' => __('Active', 'wpcf'),
            'post_types' => __('Post Types', 'wpcf'),
        );
        foreach ($custom_taxonomies as $taxonomy => $data) {
            $name = '';
            $name .= '<a href="'
                    . admin_url('admin.php?page=wpcf-edit-tax&amp;wpcf-tax='
                            . $taxonomy) . '">' . stripslashes(wpcf_translate($taxonomy . ' name',
                                    $data['labels']['name'], 'Types-TAX')) . '</a>';
            $name .= '<br />';
            $name .= '<a href="'
                    . admin_url('admin.php?page=wpcf-edit-tax&amp;wpcf-tax='
                            . $taxonomy) . '">' . __('Edit', 'wpcf') . '</a> | ';
            $name .= empty($data['disabled']) ? wpcf_admin_custom_taxonomies_get_ajax_deactivation_link($taxonomy) . ' | ' : wpcf_admin_custom_taxonomies_get_ajax_activation_link($taxonomy) . ' | ';
            $name .= '<a href="'
                    . admin_url('admin-ajax.php?action=wpcf_ajax&amp;'
                            . 'wpcf_action=delete_taxonomy&amp;wpcf-tax='
                            . $taxonomy . '&amp;wpcf_ajax_callback=wpcfRefresh&amp;wpcf_ajax_update=wpcf_list_ajax_response_'
                            . $taxonomy) . '&amp;_wpnonce=' . wp_create_nonce('delete_taxonomy')
                    . '&amp;wpcf_warning='
                    . __('Are you sure?', 'wpcf') . '" class="wpcf-ajax-link" id="wpcf-list-delete-'
                    . $taxonomy . '">'
                    . __('Delete Permanently', 'wpcf') . '</a>';
            $name .= '<div id="wpcf_list_ajax_response_' . $taxonomy . '"></div>';
            $rows[$taxonomy]['name'] = $name;
            $rows[$taxonomy]['description'] = isset($data['description']) ? htmlspecialchars(stripslashes(wpcf_translate($taxonomy . ' description',
                                            $data['description'], 'Types-TAX')),
                            ENT_QUOTES) : '';
            $rows[$taxonomy]['active-' . $taxonomy] = !empty($data['disabled']) ? __('No',
                            'wpcf') : __('Yes', 'wpcf');
            $rows[$taxonomy]['post_types'] = array();
            if (!empty($data['supports'])) {
                foreach ($data['supports'] as $temp_post_type => $true) {
                    $rows[$taxonomy]['post_types'][] = stripslashes(wpcf_translate($temp_post_type . ' name',
                                    $temp_post_type, 'Types-CPT'));
                }
            }
            $rows[$taxonomy]['post_types'] = !empty($rows[$taxonomy]['post_types']) ? implode(', ',
                            $rows[$taxonomy]['post_types']) : __('None', 'wpcf');
        }
        // Render table
        echo '<br />';
        wpcf_admin_widefat_table('wpcf_tax_list', $header, $rows);
    }
    do_action('wpcf_types_tax_list_table_after');
}