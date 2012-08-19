<?php
/**
* Functions and filters for adding custom columns to Edit Posts & Edit Pages screens
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @since 3.2
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', DFCG_DOMAIN) );
}


/**
* Add columns to Posts and Pages Edit screen to display dfcg-image custom field contents.
*
* This can be turned off in the DCG Settings Page. 
*
* @uses	manage_posts_column filter
* @uses	manage_posts_custom_column action
* @since 3.2
*/
// Filters and Actions to add the dfcg-image columns
if( isset($dfcg_options['posts-column']) && $dfcg_options['posts-column'] == "true" ) {
	add_filter('manage_posts_columns', 'dfcg_posts_columns');
	add_action('manage_posts_custom_column', 'dfcg_custom_posts_column', 10, 2);
}
if( isset($dfcg_options['pages-column']) && $dfcg_options['pages-column'] == "true" ) {
	add_filter('manage_pages_columns', 'dfcg_posts_columns');
	add_action('manage_pages_custom_column', 'dfcg_custom_posts_column', 10, 2);
}

// Filters and Actions to add the dfcg-desc columns
if( isset($dfcg_options['posts-desc-column']) && $dfcg_options['posts-desc-column'] == "true" ) {
	add_filter('manage_posts_columns', 'dfcg_posts_desc_columns');
	add_action('manage_posts_custom_column', 'dfcg_custom_posts_desc_column', 10, 2);
}
if( isset($dfcg_options['pages-desc-column']) && $dfcg_options['pages-desc-column'] == "true" ) {
	add_filter('manage_pages_columns', 'dfcg_posts_desc_columns');
	add_action('manage_pages_custom_column', 'dfcg_custom_posts_desc_column', 10, 2);
}

// Filters and Actions to add the dfcg-sort columns - only ever used on Edit Pages screen
if( isset($dfcg_options['pages-sort-column']) && $dfcg_options['pages-sort-column'] == "true" ) {
	add_filter('manage_pages_columns', 'dfcg_pages_sort_columns');
	add_action('manage_pages_custom_column', 'dfcg_custom_pages_sort_column', 10, 2);
}

/**
* Add dfcg-image columns
*
* @param array $defaults Default Edit screen columns
* @return array $defaults Modified Edit screen columns
* @since 3.2
*/
function dfcg_posts_columns($defaults) {
    $defaults['dfcg_image_col'] = __('DCG Image');
    return $defaults;
}


/**
* Populate new dfcg-image columns
*
* @param mixed $column_name	Name of Edit screen column
* @param mixed $post_id	ID of Post/Page being displayed on Edit screen
* @global array $wpdb WP database object
* @since 3.2
*/
function dfcg_custom_posts_column($column_name, $post_id) {
    
	global $wpdb;
    
	// Check we're only messing with my column
	if( $column_name == 'dfcg_image_col' ) {
        
		// Query. TODO: Is prepare necessary?
		$query = $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE $wpdb->postmeta.post_id = %d AND $wpdb->postmeta.meta_key = %s", $post_id, '_dfcg-image')
			);
        
        if( $query ) {
            $my_func = create_function('$att', 'return $att->meta_value;');
            $text = array_map( $my_func, $query );
            echo implode(', ',$text);
        } else {
            echo '<i>'.__('None').'</i>';
        }
    }
}


/**
* Add dfcg-desc columns
*
* @param array $defaults Default Edit screen columns
* @return array $defaults Modified Edit screen columns
* @since 3.2
*/
function dfcg_posts_desc_columns($defaults) {
    $defaults['dfcg_desc_col'] = __('DCG Desc');
    return $defaults;
}

/**
* Populate new dfcg-desc columns
*
* @param mixed $column_name	Name of Edit screen column
* @param mixed $post_id	ID of Post/Page being displayed on Edit screen
* @global array $wpdb WP database object
* @since 3.2
*/
function dfcg_custom_posts_desc_column($column_name, $post_id) {
    
	global $wpdb;
    
	// Check we're only messing with my column
	if( $column_name == 'dfcg_desc_col' ) {
        
		// Query. TODO: Is prepare necessary?
		$query = $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE $wpdb->postmeta.post_id = %d AND $wpdb->postmeta.meta_key = %s", $post_id, '_dfcg-desc')
			);
        
        if( $query ) {
            // Anonymous function to get meta_value
			$my_func = create_function('$att', 'return $att->meta_value;');
			// Run function on each element of $dfcg_query array
			$text = array_map( $my_func, $query );
            // Shorten description with helper function
			$text = array_map( "dfcg_shorten_desc", $text);
			echo implode(', ',$text);
        } else {
            echo '<i>'.__('None').'</i>';
        }
    }
}


/**
* Add dfcg-sort columns
*
* @param array $defaults Default Edit screen columns
* @return array $defaults Modified Edit screen columns
* @since 3.2
*/
function dfcg_pages_sort_columns($defaults) {
    $defaults['dfcg_sort_col'] = __('DCG Page Sort');
    return $defaults;
}

/**
* Populate new dfcg-sort columns
*
* @param mixed $column_name	Name of Edit screen column
* @param mixed $post_id	ID of Post/Page being displayed on Edit screen
* @global array $wpdb WP database object
* @since 3.2
*/
function dfcg_custom_pages_sort_column($column_name, $post_id) {
    
	global $wpdb;
    
	// Check we're only messing with my column
	if( $column_name == 'dfcg_sort_col' ) {
        
		// Query. TODO: Is prepare necessary?
		$query = $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE $wpdb->postmeta.post_id = %d AND $wpdb->postmeta.meta_key = %s", $post_id, '_dfcg-sort')
			);
        
        if( $query ) {
            $my_func = create_function('$att', 'return $att->meta_value;');
            $text = array_map( $my_func, $query );
            echo implode(', ',$text);
        } else {
            echo '<i>'.__('None').'</i>';
        }
    }
}


/**
* Helper function to shorten the length of dfcg-desc when displayed in Post/Page Edit screen
*
* Based on my Limit Title plugin
*
* @param string $string	 Contents of dfcg-desc custom field
* @return string $string Shortened dfcg-desc text
* @since 3.0
*/
function dfcg_shorten_desc($string) {

	$length = '30';
	$replacer = ' [...]';
   
	if(strlen($string) > $length)
		$string = (preg_match('/^(.*)\W.*$/', substr($string, 0, $length+1), $matches) ? $matches[1] : substr($string, 0, $length)) . $replacer;

	return $string;
}