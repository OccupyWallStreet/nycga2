<?php
/*
 * Limit and offset filter embedded.
 */

add_filter('wpv_view_settings', 'wpv_limit_default_settings', 10, 2);

function wpv_limit_default_settings($view_settings) {

    if (!isset($view_settings['limit'])) {
        $view_settings['limit'] = -1;
    }
    if (!isset($view_settings['offset'])) {
        $view_settings['offset'] = 0;
    }
    if (!isset($view_settings['taxonomy_limit'])) {
        $view_settings['taxonomy_limit'] = -1;
    }
    if (!isset($view_settings['taxonomy_offset'])) {
        $view_settings['taxonomy_offset'] = 0;
    }

    return $view_settings;
}

add_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 2);

function wpv_filter_limit_arg($query, $view_settings) {
    $limit = intval($view_settings['limit']);
    $offset = intval($view_settings['offset']);
    if ($offset != 0 || $limit != -1) {
        remove_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 2);
        add_filter('wpv_filter_query_post_process',
                'wpv_filter_limit_query_post_process_filter', 10, 2);
        unset($query['paged']);
        $query['posts_per_page'] = -1;
    }
    return $query;
}

function wpv_filter_limit_query_post_process_filter($query, $view_settings) {
    remove_filter('wpv_filter_query_post_process',
            'wpv_filter_limit_query_post_process_filter', 10, 2);
    if (!empty($query->posts)) {
        $limit = intval($view_settings['limit']);
        $offset = intval($view_settings['offset']);
        if ($limit == -1) {
            $posts = array_slice($query->posts, $offset);
        } else {
            $posts = array_slice($query->posts, $offset, $limit);
        }
        add_filter('wpv_filter_query', 'wpv_filter_limit_arg_post_in', 10, 2);
        global $wpv_limit_post_in;
        if (!empty($posts)) {
            $wpv_limit_post_in = array();
            foreach ($posts as $key => $post) {
                $wpv_limit_post_in[] = $post->ID;
            }
        } else {
            $wpv_limit_post_in = array(0);
        }
        $query = wpv_filter_get_posts($view_settings['view_id']);
        remove_filter('wpv_filter_query', 'wpv_filter_limit_arg_post_in', 10, 2);

        add_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 2);

        return $query;
    }
    
    add_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 2);
    
    return $query;
}

function wpv_filter_limit_arg_post_in($query, $view_settings) {
    global $wpv_limit_post_in;
    $query['post__in'] = $wpv_limit_post_in;
    return $query;
}

// Taxonomies
add_filter('wpv_filter_taxonomy_post_query',
        'wpv_filter_limit_taxonomy_post_query_filter', 10, 3);

function wpv_filter_limit_taxonomy_post_query_filter($items,
        $tax_query_settings, $view_settings) {
    $limit = intval($view_settings['taxonomy_limit']);
    $offset = intval($view_settings['taxonomy_offset']);
    if ($offset != 0 || $limit != -1) {
        if ($limit == -1) {
            $items = array_slice($items, $offset);
        } else {
            $items = array_slice($items, $offset, $limit);
        }
        if (empty($items)) {
            return array();
        }
    }
    return $items;
}