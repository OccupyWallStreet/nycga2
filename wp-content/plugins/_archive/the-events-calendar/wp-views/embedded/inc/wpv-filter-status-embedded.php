<?php

/**
 * Add a filter to add the query by post status to the $query
 *
 */

add_filter('wpv_filter_query', 'wpv_filter_post_status', 10, 2);
function wpv_filter_post_status($query, $view_settings) {
    
    if (isset($view_settings['post_status'])) {
        $query['post_status'] = $view_settings['post_status'];
    }
    
    return $query;
}
