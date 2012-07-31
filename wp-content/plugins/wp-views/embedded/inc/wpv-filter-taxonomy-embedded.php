<?php

$taxonomy_checkboxes_defaults = array(
    'taxonomy_hide_empty' => true,
    'taxonomy_include_non_empty_decendants' => true,
    'taxonomy_pad_counts' => false,
);

add_filter('wpv_view_settings', 'wpv_taxonomy_default_settings', 10, 2);
function wpv_taxonomy_default_settings($view_settings) {
    global $taxonomy_checkboxes_defaults;
    
    if (!isset($view_settings['taxonomy_type'])) {
        $view_settings['taxonomy_type'] = array();
    }
    
    foreach($taxonomy_checkboxes_defaults as $key => $value) {
        if (!isset($view_settings[$key])) {
            $view_settings[$key] = $value;
        }
    }

    if (!isset($view_settings['taxonomy_parent_mode'][0])) {
        $view_settings['taxonomy_parent_mode'] = array(0 => '');
    }
    
    if (!isset($view_settings['taxonomy_terms'])) {
        $view_settings['taxonomy_terms'] = array();
    }

    return $view_settings;
}

function get_taxonomy_query($view_settings) {
    global $WP_Views;
    
    $taxonomies = get_taxonomies('', 'objects');
    $tax_query_settings = array(
        'hide_empty' => $view_settings['taxonomy_hide_empty'],
        'hierarchical' => $view_settings['taxonomy_include_non_empty_decendants'],
        'pad_counts' => $view_settings['taxonomy_pad_counts'],
        'orderby' => $view_settings['taxonomy_orderby'],
        'order' => $view_settings['taxonomy_order']
    );
    
    $tax_query_settings = apply_filters('wpv_filter_taxonomy_query', $tax_query_settings, $view_settings);
    
    if (isset($_GET['wpv_column_sort_id'])) {
        $field = $_GET['wpv_column_sort_id'];
        if ($field == 'taxonomy-link') {
            $tax_query_settings['orderby'] = 'name';
        } 
        if ($field == 'taxonomy-title') {
            $tax_query_settings['orderby'] = 'name';
        }
        if ($field == 'taxonomy-post_count') {
            $tax_query_settings['orderby'] = 'count';
        }
        
    }
    
    if (isset($_GET['wpv_column_sort_dir'])) {
        $tax_query_settings['order'] = strtoupper($_GET['wpv_column_sort_dir']);

    }    
    
    if (isset($taxonomies[$view_settings['taxonomy_type'][0]])) {
        $items = get_terms($taxonomies[$view_settings['taxonomy_type'][0]]->name, $tax_query_settings);
    } else {
        // taxonomy no longer exists.
        $items = array();
    }
    
    // get_terms doesn't sort by count when child count is included.
    // we need to do it manually.
    if ($view_settings['taxonomy_orderby'] == 'count') {
        if ($view_settings['taxonomy_order'] == 'ASC') {
            usort($items, '_wpv_taxonomy_sort_asc');
        } else {
            usort($items, '_wpv_taxonomy_sort_dec');
        }
    }

    // Filter by parent if required.
    // Note: We could use the 'parent' siggin in the tax_query_settings but
    // this doesn't return the correct post count.
    
    $parent_id = null;
    switch($view_settings['taxonomy_parent_mode'][0]) {
        case 'current_view':
            $parent_id = $WP_Views->get_parent_view_taxonomy();
            break;
            
        case 'this_parent':
            $parent_id = $view_settings['taxonomy_parent_id'];
            break;
    }
    
    if ($parent_id !== null) {
        foreach($items as $index => $item) {
            if ($item->parent != $parent_id) {
                unset($items[$index]);
            }
        }
    }
    
    if (sizeof($view_settings['taxonomy_terms'])) {
        // filter by indiviual taxonomy terms.
        
        $filtered_terms = array();
        
        foreach($items as $item) {
            if (in_array($item->term_id, $view_settings['taxonomy_terms'])) {
                // only add the terms in the 'taxonomy_terms' array.
                $filtered_terms[] = $item;
            }
        }
        
        $items = $filtered_terms;
        
    }


    $items = array_values($items);

    return apply_filters('wpv_filter_taxonomy_post_query', $items, $tax_query_settings, $view_settings);

}

function _wpv_taxonomy_sort_asc($a, $b) {
    if ($a->count == $b->count) {
        return 0;
    }
    
    return ($a->count < $b->count) ? -1 : 1;
}

function _wpv_taxonomy_sort_dec($a, $b) {
    if ($a->count == $b->count) {
        return 0;
    }
    
    return ($a->count < $b->count) ? 1 : -1;
}


/**
 * Views-Shortcode: wpv-no-taxonomy-found
 *
 * Description: The [wpv-no-taxonomy-found] shortcode will display the text inside
 * the shortcode if there are no taxonomys found by the Views query.
 * eg. [wpv-no-taxonomy-found]<strong>No taxonomy found</strong>[/wpv-no-taxonomy-found]
 *
 * Parameters:
 * This takes no parameters.
 *
 */
  
add_shortcode('wpv-no-taxonomy-found', 'wpv_no_taxonomy_found');
function wpv_no_taxonomy_found($atts, $value){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    
    if ($WP_Views->get_taxonomy_found_count() == 0) {
        // display the message when no taxonomys are found.
        return wpv_do_shortcode($value);
    } else {
        return '';
    }
    
}
    
