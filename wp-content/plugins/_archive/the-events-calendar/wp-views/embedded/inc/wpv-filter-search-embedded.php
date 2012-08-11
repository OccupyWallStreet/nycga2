<?php

/**
 * Add a filter to add the query by post search to the $query
 *
 */

add_filter('wpv_filter_query', 'wpv_filter_post_search', 10, 2);
function wpv_filter_post_search($query, $view_settings) {
    
    if (isset($view_settings['post_search_value']) && $view_settings['post_search_value'] != '') {
        $query['s'] = $view_settings['post_search_value'];
    }
    if (isset($_GET['wpv_post_search'])) {
        $query['s'] = $_GET['wpv_post_search'];
    }
    
    return $query;
}

/**
 * Add a filter to for taxonomy search
 *
 */

add_filter('wpv_filter_taxonomy_query', 'wpv_filter_taxonomy_search', 10, 2);
function wpv_filter_taxonomy_search($query, $view_settings) {
    
    if (isset($view_settings['taxonomy_search_value']) && $view_settings['taxonomy_search_value'] != '') {
        $query['search'] = $view_settings['taxonomy_search_value'];
    }
    if (isset($_GET['wpv_taxonomy_search'])) {
        $query['search'] = $_GET['wpv_taxonomy_search'];
    }
    
    return $query;
}

