<?php
/*
Plugin Name: More Types
Version: 1.2
Author URI: http://labs.dagensskiva.com/
Plugin URI: http://labs.dagensskiva.com/
Description:  Add more post types to your WordPress installation. 
Author: Henrik Melin, Kal Ström
License: GPL2

	Copyright (C) 2010  Henrik Melin, Kal Ström
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, 
	MA  02110-1301, USA.
    
*/

/* 
** 		MANUAL RESET ** DELETES ALL DATA STORED IN MORE TYPES **
**
** 		To reset More Types completely change 0 to 1, click on 
** 		Settings->More Types, the set it back to 0
*/
if (0) update_option('more_types', array());

// Plugin settings
$fields = array(
		'var' => array('description', 'menu_icon', 'public', 'label', 'singular_label', 'name', 'exclude_from_search', 
		'publicly_queryable', 'show_ui', 'inherit_type', 'capability_type', 'hierarchical', 'template', 'rewrite_bool', 
		'rewrite_slug', 'revisions','can_export', 'menu_position', 'show_in_menu', 'has_archive', 
				'labels' => array('name', 'singular_name', 'add_new', 'add_new_item', 'edit_item', 'new_item', 'view_item', 'search_items', 'not_found', 'not_found_in_trash', 'menu_name')),
		'array' => array('supports', 'more_edit_type_cap', 'more_edit_cap', 'more_edit_others_cap', 'more_publish_others_cap', 'more_read_cap', 'more_delete_cap', 'taxonomies', 'boxes')
);
$default = array(
		'show_in_menu' => true,
		'has_arvhive' => true,
		'show_ui' => true,
		'publicly_queryable' => true,
		'hierarchical' => false,
		'public' => true,
		'capability_type' => 'post', 
		'supports' => array('title', 'editor'),
		'rewrite_bool' => true,
		'revisions' => true,
		'can_export' => true,
		'show_in_nav_menus' => true,
		'labels' => array(
			'add_new' => __('Add new', 'more-plugins'),
			'add_new_item' => __('Add new item', 'more-plugins'), 
			'edit_item' => __('Edit item', 'more-plugins'),
			'new_item' => __('New item', 'more-plugins'),
			'view_item' => __('View item', 'more-plugins'),
			'search_items' => __('Search item', 'more-plugins'),
			'not_found' => __('No items found', 'more-plugins'), 
			'not_found_in_trash' => __('No items found in Trash', 'more-plugins'),
			'menu_name' => ''
			),
);
$default_keys = array('post', 'page', 'revision', 'media', 'attachment', 'nav_menu_item');

$settings = array(
		'name' => 'More Types', 
		'option_key' => 'more_types',
		'fields' => $fields,
		'default' => $default,
		'default_keys' => $default_keys,
		'file' => __FILE__,
);

// Always on components
if (!defined('MORE_PLUGINS_DEV')) include('more-plugins/more-plugins.php');
else include(ABSPATH . '/wp-content/plugins/more-plugins.php');
include('more-types-object.php');

$more_types = new more_types_object($settings);

// Load admin components
if (is_admin()) {
	if (!defined('MORE_PLUGINS_DEV')) include('more-plugins/more-plugins-admin.php');
	else include(ABSPATH . '/wp-content/plugins/more-plugins-admin.php');
	include('more-types-settings-object.php');
	$more_types_settings = new more_types_admin($settings);	
}


?>
