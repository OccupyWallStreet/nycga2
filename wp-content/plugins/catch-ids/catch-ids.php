<?php
/*
Plugin Name: Catch IDs
Plugin URI: http://catchthemes.com/wp-plugins/catch-ids/
Description: Catch IDs is a simple and light weight plugin to show the Post ID, Page ID, Media ID, Links ID, Category ID, Tag ID and User ID in the Admin Section Table. This plugin was initially develop to support our themes features slider. Then we thought that this will be helpful to all the WordPress Admin Users.
Version: 1.0
Author: Catch Themes Team
Author URI: http://catchthemes.com
*/

/*
Copyright (C) 2012 Catch Themes, (info@catchinternet.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
 * @package Catch-IDs
 * @version 1.0
 */


if ( ! function_exists( 'catchids_column' ) ):
/**
 * Prepend the new column to the columns array
 */
function catchids_column($cols) {
	$cols['catchids'] = 'ID';
	return $cols;
}
endif; // catchids_column


if ( ! function_exists( 'catchids_value' ) ) :
/**
 * Echo the ID for the new column
 */ 
function catchids_value($column_name, $id) {
	if ($column_name == 'catchids')
		echo $id;
}
endif; // catchids_value


if ( ! function_exists( 'catchids_return_value' ) ) :
function catchids_return_value($value, $column_name, $id) {
	if ($column_name == 'catchids')
		$value = $id;
	return $value;
}
endif; // catchids_return_value


if ( ! function_exists( 'catchids_css' ) ) :
/**
 * Output CSS for width of new column
 */ 
function catchids_css() {
?>
<style type="text/css">
	#catchids { 
		width: 50px; 
	}
</style>
<?php	
}
endif; // catchids_css


if ( ! function_exists( 'catchids_add' ) ) :
/**
 * Actions/Filters for various tables and the css output
 */ 
function catchids_add() {
	add_action('admin_head', 'catchids_css');

	// For Post Management
	add_filter('manage_posts_columns', 'catchids_column');
	add_action('manage_posts_custom_column', 'catchids_value', 10, 2);

	// For Page Management
	add_filter('manage_pages_columns', 'catchids_column');
	add_action('manage_pages_custom_column', 'catchids_value', 10, 2);

	// For Media Management
	add_filter('manage_media_columns', 'catchids_column');
	add_action('manage_media_custom_column', 'catchids_value', 10, 2);

	// For Link Management
	add_filter('manage_link-manager_columns', 'catchids_column');
	add_action('manage_link_custom_column', 'catchids_value', 10, 2);

	// For Category Management
	add_action('manage_edit-link-categories_columns', 'catchids_column');
	add_filter('manage_link_categories_custom_column', 'catchids_return_value', 10, 3);

	// For Tags Management
	foreach ( get_taxonomies() as $taxonomy ) {
		add_action("manage_edit-${taxonomy}_columns", 'catchids_column');			
		add_filter("manage_${taxonomy}_custom_column", 'catchids_return_value', 10, 3);
	}

	// For User Management
	add_action('manage_users_columns', 'catchids_column');
	add_filter('manage_users_custom_column', 'catchids_return_value', 10, 3);

	// For Comment Management
	add_action('manage_edit-comments_columns', 'catchids_column');
	add_action('manage_comments_custom_column', 'catchids_value', 10, 2);
}
endif; // catchids_add

add_action('admin_init', 'catchids_add');