<?php
/* 
 * Embedded functions.
 */

/**
 * Defines predefined capabilities.
 * 
 * @return array 
 */
function wpcf_access_types_caps_predefined() {
    $modes = array(
        'read-only' => array(
            'title' => __('Read-only', 'wpcf_access'),
            'role' => 'subscriber',
            'predefined' => 'read-only',
        ),
        'edit' => array(
            'title' => __('Edit'),
            'role' => 'contributor',
            'predefined' => 'edit',
        ),
        'publish' => array(
            'title' => __('Publish'),
            'role' => 'author',
            'predefined' => 'publish',
        ),
        'manage' => array(
            'title' => __('Manage', 'wpcf_access'),
            'role' => 'editor',
            'predefined' => 'manage',
        ),
    );
    return $modes;
}

/**
 * Defines capabilities.
 * 
 * @return type 
 */
function wpcf_access_types_caps() {
    $caps = array(
        'edit_post' => array(
            'title' => __('Edit post', 'wpcf_access'),
            'role' => 'contributor',
            'predefined' => 'edit',
        ),
        'read_post' => array(
            'title' => __('Read post', 'wpcf_access'),
            'role' => 'subscriber',
            'predefined' => 'read-only',
        ),
        'delete_post' => array(
            'title' => __('Delete post', 'wpcf_access'),
            'role' => 'contributor',
            'predefined' => 'edit',
        ),
        'edit_others_posts' => array(
            'title' => __('Edit others posts', 'wpcf_access'),
            'role' => 'editor',
            'predefined' => 'manage',
        ),
        'publish_posts' => array(
            'title' => __('Publish post', 'wpcf_access'),
            'role' => 'author',
            'predefined' => 'publish',
        ),
        'read_private_posts' => array(
            'title' => __('Read private posts', 'wpcf_access'),
            'role' => 'editor',
            'predefined' => 'manage',
        ),
        'edit_posts' => array(
            'title' => __('Edit posts', 'wpcf_access'),
            'role' => 'contributor',
            'predefined' => 'edit',
        ),
        // @TODO NOT SURE ABOUT THIS ONE
//        'read' => array(
//            'title' => __('Read', 'wpcf_access'),
//            'role' => 'subscriber',
//        ),
        'delete_posts' => array(
            'title' => __('Delete posts', 'wpcf_access'),
            'role' => 'contributor',
            'predefined' => 'edit',
        ),
        'delete_private_posts' => array(
            'title' => __('Delete private posts', 'wpcf_access'),
            'role' => 'editor',
            'predefined' => 'manage',
        ),
        'delete_published_posts' => array(
            'title' => __('Delete published posts', 'wpcf_access'),
            'role' => 'author',
            'predefined' => 'publish',
        ),
        'delete_others_posts' => array(
            'title' => __('Delete others posts', 'wpcf_access'),
            'role' => 'editor',
            'predefined' => 'manage',
        ),
        'edit_private_posts' => array(
            'title' => __('Edit private posts', 'wpcf_access'),
            'role' => 'editor',
            'predefined' => 'manage',
        ),
        'edit_published_posts' => array(
            'title' => __('Edit published posts', 'wpcf_access'),
            'role' => 'author',
            'predefined' => 'publish',
        ),
    );
    return apply_filters('wpcf_access_types_caps', $caps);
}

/**
 * Defines capabilities.
 * 
 * @return type 
 */
function wpcf_access_tax_caps() {
    $caps = array(
        'manage_terms' => array(
            'title' => __('Manage terms', 'wpcf_access'),
            'role' => 'editor',
            'predefined' => 'manage',
            'match' => array(
                'manage_' => array(
                    'match' => 'edit_others_',
                    'default' => 'manage_categories',
                ),
            ),
            'default' => 'manage_categories',
        ),
        'edit_terms' => array(
            'title' => __('Edit terms', 'wpcf_access'),
            'role' => 'contributor',
            'predefined' => 'edit',
            'match' => array(
                'edit_' => array(
                    'match' => 'edit_others_',
                    'default' => 'manage_categories',
                ),
            ),
            'default' => 'manage_categories',
        ),
        'delete_terms' => array(
            'title' => __('Delete terms', 'wpcf_access'),
            'role' => 'contributor',
            'predefined' => 'edit',
            'match' => array(
                'delete_' => array(
                    'match' => 'edit_others_',
                    'default' => 'manage_categories',
                ),
            ),
            'default' => 'manage_categories',
        ),
        'assign_terms' => array(
            'title' => __('Assign terms', 'wpcf_access'),
            'role' => 'contributor',
            'predefined' => 'edit',
            'match' => array(
                'assign_' => array(
                    'match' => 'edit_',
                    'default' => 'edit_posts',
                ),
            ),
            'default' => 'edit_posts',
        ),
    );
    return apply_filters('wpcf_access_tax_caps', $caps);
}

/**
 * Maps role to level.
 * 
 * @return string 
 */
function wpcf_access_role_to_level_map() {
    $map = array(
        'administrator' => 'level_10',
        'editor' => 'level_7',
        'author' => 'level_2',
        'contributor' => 'level_1',
        'subscriber' => 'level_0',
    );
    return $map;
}