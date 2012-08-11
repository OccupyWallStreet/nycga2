<?php

/**
 * Create the query to return the posts based on the settings
 * in the views query meta box.
 *
 */

function wpv_filter_get_posts($id) {
    global $WP_Views, $post, $wplogger;
    
    $view_settings_defaults = array(
        'post_type'         => 'any',
        'orderby'           => 'post-date',
        'order'             => 'DESC',
        'paged'             => '1',
        'posts_per_page'    =>  -1
    );
    extract($view_settings_defaults);
    $view_settings = $WP_Views->get_view_settings($id);
	$view_settings['view_id'] = $id;
    extract($view_settings, EXTR_OVERWRITE);
    
    if (isset($_GET['wpv_paged']) && isset($_GET['wpv_view_count']) && $_GET['wpv_view_count'] == $WP_Views->get_view_count()) {
        $paged = intval($_GET['wpv_paged']);
    }
    
    $query = array(
            'posts_per_page'    => $posts_per_page,
            'paged'             => $paged,
            'post_type'         => $post_type,
            'order'             => $order,
            'suppress_filters'  => false,
			'ignore_sticky_posts' => true
    );
    
    if (isset($view_settings['pagination'][0]) && $view_settings['pagination'][0] == 'disable' && isset($view_settings['pagination']['mode']) && $view_settings['pagination']['mode'] == 'paged') {
        // Show all the posts if pagination is disabled.
        $query['posts_per_page'] = -1;
    }
    if (isset($view_settings['pagination']['mode']) && $view_settings['pagination']['mode'] == 'rollover') {
        $query['posts_per_page'] = $view_settings['rollover']['posts_per_page'];
    }
    
    $query = apply_filters('wpv_filter_query', $query, $view_settings);
    
    $post_query = new WP_Query($query);
	
	$wplogger->log($post_query->query, WPLOG_DEBUG);
	$wplogger->log($post_query->request, WPLOG_DEBUG);
    
    $post_query = apply_filters('wpv_filter_query_post_process', $post_query, $view_settings);
    
    return $post_query;
}
