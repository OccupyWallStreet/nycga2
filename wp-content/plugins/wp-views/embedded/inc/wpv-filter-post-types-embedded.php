<?php

add_filter('wpv_filter_query', 'wpv_filter_get_post_types_arg', 10, 2);
function wpv_filter_get_post_types_arg($query, $view_settings) {
    
    global $post;
    
    $post_type = $query['post_type'];
    // See if the post_type is exposed as a url arg.
    if (isset($view_settings['post_type_expose_arg']) && $view_settings['post_type_expose_arg']) {
        if ($_GET['wpv_post_type']) {
            $post_type = $_GET['wpv_post_type'];
        }
    }
    $query['post_type'] = $post_type;
    
    if (!isset($view_settings['post_type_dont_include_current_page']) || $view_settings['post_type_dont_include_current_page']) {

        if (is_single() || is_page()) {
        	global $wp_query;
            
            if (isset($wp_query->posts[0])) {
                $current_post = $wp_query->posts[0];
                $post_not_in_list = $current_post ? array($current_post->ID) : array();
            
                $query['post__not_in'] = $post_not_in_list;
            }
        }
    }
    
    return $query;

}

