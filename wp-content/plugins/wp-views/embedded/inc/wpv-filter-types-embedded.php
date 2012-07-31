<?php

add_filter('wpv_view_settings', 'wpv_types_defaults', 10, 2);
function wpv_types_defaults($view_settings, $view_id=null) {
    if (!isset($view_settings['query_type'][0])) {
        $view_settings['query_type'][0] = 'posts';
    }
    
    return $view_settings;
}
    
