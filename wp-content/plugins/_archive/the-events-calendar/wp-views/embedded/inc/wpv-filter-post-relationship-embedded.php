<?php

/**
 * Add a filter to add the query by post relationship to the $query
 *
 */

add_filter('wpv_filter_query', 'wpv_filter_post_relationship', 11, 2); // run after post types filter
function wpv_filter_post_relationship($query, $view_settings) {
    
    global $WP_Views, $wpdb;
    
    if (isset($view_settings['post_relationship_mode'][0])) {
        
        $post_owner_id = 0;
        
        if ($view_settings['post_relationship_mode'][0] == 'current_page') {
            $post_owner_id = $WP_Views->get_top_current_page()->ID;
        }

        if ($view_settings['post_relationship_mode'][0] == 'parent_view') {
            $post_owner_id = $WP_Views->get_current_page()->ID;
        }

        if ($view_settings['post_relationship_mode'][0] == 'this_page') {
            if (isset($view_settings['post_relationship_id']) && $view_settings['post_relationship_id'] > 0) {
                $post_owner_id = $view_settings['post_relationship_id'];
                if (function_exists('icl_object_id')) {
                    $post_type = $wpdb->get_var("SELECT post_type FROM {$wpdb->posts} WHERE ID = " . $post_owner_id);
                    if ($post_type) {
                        $post_owner_id = icl_object_id($post_owner_id, $post_type, true);
                    }
                }
            }
        }
        
        if ($post_owner_id > 0) {
            $post_type = $wpdb->get_var("SELECT post_type FROM {$wpdb->posts} WHERE ID = " . $post_owner_id);
            $key = '_wpcf_belongs_' . $post_type . '_id';
            
            $posts_to_include = $wpdb->get_col("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '{$key}' AND meta_value = {$post_owner_id}");
			
			if (isset($query['post__not_in'])) {
				$posts_to_include = array_diff($posts_to_include, $query['post__not_in']);
			}
            
            if (count($posts_to_include)) {
                if (isset($query['post__in'])) {
                    $query['post__in'] = array_merge($query['post__in'], $posts_to_include);
                } else {
                    $query['post__in'] = $posts_to_include;
                }
            }
            else {
            	$query['post__in'] = array(-1);
            }
        }
    }
    
    return $query;
}

/**
 * Check the current post to see if it belongs to any other post types
 * defined by Types
 *
 */


add_action('wpv-before-display-post', 'wpv_before_display_post_post_relationship', 10, 2);
function wpv_before_display_post_post_relationship($post, $view_id) {

    static $related = array();
    global $WP_Views;
    
    if (function_exists('wpcf_pr_get_belongs')) {
        
        if (!isset($related[$post->post_type])) {
            $related[$post->post_type] = wpcf_pr_get_belongs($post->post_type);
        }
        if (is_array($related[$post->post_type])) {
            foreach($related[$post->post_type] as $post_type => $data) {
                $related_id = wpcf_pr_post_get_belongs($post->ID, $post_type);
                if ($related_id) {
                    $WP_Views->set_variable($post_type . '_id', $related_id);
                }
            }
        }
    }
    
}

