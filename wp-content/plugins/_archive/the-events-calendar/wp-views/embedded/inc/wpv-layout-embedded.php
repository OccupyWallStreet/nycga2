<?php

/*
 
    Shortcode for sorting by the column heading in
    table layout mode.
    
*/

add_shortcode('wpv-heading', 'wpv_header_shortcode');
function wpv_header_shortcode($atts, $value){
    extract(
        shortcode_atts( array(), $atts )
    );

    if (isset($atts['name']) && strpos($atts['name'], 'types-field-')) {
        $atts['name'] = strtolower($atts['name']);
    }
    
    global $WP_Views;
    $view_settings = $WP_Views->get_view_settings();
    
    //'wpv_column_sort_id'
    $order_class = 'wpv-header-no-sort';
    
    if ($view_settings['view-query-mode'] == 'normal' && $atts['name'] != 'post-body') {

        if (isset($_GET['wpv_column_sort_id']) && $_GET['wpv_column_sort_id'] == $atts['name']) {
            
            if (isset($_GET['wpv_column_sort_dir'])) {
                if ($_GET['wpv_column_sort_dir'] == 'asc') {
                    $order_class = 'wpv-header-asc';
                } else {
                    $order_class = 'wpv-header-desc';
                }
            } else {
                // use the default order
                $order_selected = $view_settings['order'];
                if ($order_selected == 'ASC') {
                    $order_class = 'wpv-header-asc';
                } else {
                    $order_class = 'wpv-header-desc';
                }
            }
        }
        if ($order_class == 'wpv-header-asc') {
            $dir = "desc";
        } else {
            $dir = "asc";
        }
        $link = '<a href="#" class="' . $order_class . '" onclick=" return wpv_column_head_click(\'' . $atts['name'] . '\', \'' . $dir . '\')">' . $value . '<span class="wpv-sorting-indicator"></span></a>';
        return $link;
    } else {
        return $value;
    }
}

add_shortcode('wpv-layout-start', 'wpv_layout_start_shortcode');
function wpv_layout_start_shortcode($atts){
    
    global $WP_Views;
    
    // TODO Check Additional JS
    $view_settings = $WP_Views->get_view_layout_settings();
    if (!empty($view_settings['additional_js'])) {
        $scripts = explode(',', $view_settings['additional_js']);
        $count = 1;
        foreach ($scripts as $script) {
            if (strpos($script, '[theme]') == 0) {
                $script = str_replace('[theme]', get_stylesheet_directory_uri(), $script);
            }
            add_action('wp_footer', create_function('$a=1, $script=\'' . $script. '\'', 'echo "<script type=\"text/javascript\" src=\"$script?ver=" . rand(1, 1000) . "\"></script>";'));
            $count++;
        }
    }
    $view_settings = $WP_Views->get_view_settings();
    $class = array();
    $style = array();
    if (($view_settings['pagination'][0] == 'enable' && $view_settings['ajax_pagination'][0] == 'enable') || $view_settings['pagination']['mode'] == 'rollover') {
        $class[] = 'wpv-pagination';
        if (!isset($view_settings['pagination']['preload_images'])) {
            $view_settings['pagination']['preload_images'] = false;
        }
        if (!isset($view_settings['rollover']['preload_images'])) {
            $view_settings['rollover']['preload_images'] = false;
        }
        $class[] = 'wpv-pagination';
        if (($view_settings['pagination']['mode'] == 'paged' && $view_settings['pagination']['preload_images'])
            || ($view_settings['pagination']['mode'] == 'rollover' && $view_settings['rollover']['preload_images'])) {
            $class[] = 'wpv-pagination-preload-images';
            $style[] = 'visibility:hidden;';
        }
        if (($view_settings['pagination']['mode'] == 'paged' && $view_settings['pagination']['preload_pages'])
            || ($view_settings['pagination']['mode'] == 'rollover' && $view_settings['pagination']['preload_pages'])) {
            $class[] = 'wpv-pagination-preload-pages';
        }
        
        $add = '';
        if (!empty($class)) {
            $add .= ' class="' . implode(' ', $class) . '"';
        }
        if (!empty($style)) {
            $add .= ' style="' . implode(' ', $style) . '"';
        }
        
        return "<div id=\"wpv-view-layout-" . $WP_Views->get_view_count() . "\"$add>\n";
    } else {
        return '';
    }
}

add_shortcode('wpv-layout-end', 'wpv_layout_end_shortcode');
function wpv_layout_end_shortcode($atts){
    global $WP_Views;
    
    $view_settings = $WP_Views->get_view_settings();
    if (($view_settings['pagination'][0] == 'enable' && $view_settings['ajax_pagination'][0] == 'enable') || $view_settings['pagination']['mode'] == 'rollover') {
        return '</div>';
    } else {
        return '';
    }
}

add_shortcode('wpv-layout-meta-html', 'wpv_layout_meta_html');
function wpv_layout_meta_html($atts) {
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    $view_layout_settings = $WP_Views->get_view_layout_settings();
    
    if (isset($view_layout_settings['layout_meta_html'])) {
        return wpv_do_shortcode($view_layout_settings['layout_meta_html']);
    } else {
        return '';
    }
}