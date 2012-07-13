<?php
/*
Plugin Name: Sticky Custom Post Types
Plugin URI: http://superann.com/sticky-custom-post-types/
Description: Enables support for sticky custom post types. Set options in Settings &rarr; Reading.
Version: 1.2.2
Author: Ann Oyama
Author URI: http://superann.com
License: GPL2

Copyright 2011 Ann Oyama  (email : wordpress [at] superann.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function super_sticky_description() {
	echo '<p>'.__('Enable support for sticky custom post types.').'</p>';
}

function super_sticky_set_post_types() {
	$post_types = get_post_types(array('_builtin' => false, 'public' => true), 'names');
	if(!empty($post_types)) {
		$checked_post_types = super_sticky_post_types();
		foreach($post_types as $post_type) { ?>
			<div><input type="checkbox" id="post_type_<?php echo $post_type; ?>" name="sticky_custom_post_types[]" value="<?php echo $post_type; ?>" <?php checked(in_array($post_type, $checked_post_types)); ?> /> <label for="post_type_<?php echo $post_type; ?>"><?php echo $post_type; ?></label></div><?php
		}
	}
	else
		echo '<p>'.__('No public custom post types found.').'</p>';
}

function super_sticky_filter($query_type) {
	$filters = get_option('sticky_custom_post_types_filters');
	if(!is_array($filters)) $filters = array();
		return in_array($query_type, $filters);
}

function super_sticky_set_filters() { ?>
	<span><input type="checkbox" id="sticky_custom_post_types_filters_home" name="sticky_custom_post_types_filters[]" value="home" <?php checked(super_sticky_filter('home')); ?> /> <label for="sticky_custom_post_types_filters_home">home</label></span><?php
}

function super_sticky_admin_init() {
	register_setting('reading', 'sticky_custom_post_types');
	register_setting('reading', 'sticky_custom_post_types_filters');
	add_settings_section('super_sticky_options', 'Sticky Custom Post Types', 'super_sticky_description', 'reading');
	add_settings_field('sticky_custom_post_types', 'Show "Stick this..." checkbox on', 'super_sticky_set_post_types', 'reading', 'super_sticky_options');
	add_settings_field('sticky_custom_post_types_filters', 'Display selected post type(s) on', 'super_sticky_set_filters', 'reading', 'super_sticky_options');
}

add_action('admin_init', 'super_sticky_admin_init', 20);

function super_sticky_post_types($posts=false) {
	$post_types = get_option('sticky_custom_post_types');
	if(!is_array($post_types))
		$post_types = array();
	if($posts)
		$post_types[] = 'post';
	return $post_types;
}

function super_sticky_meta() { global $post; ?>
	<input id="super-sticky" name="sticky" type="checkbox" value="sticky" <?php checked(is_sticky($post->ID)); ?> /> <label for="super-sticky" class="selectit"><?php _e('Stick this to the front page') ?></label><?php
}

function super_sticky_add_meta_box() {
	foreach(super_sticky_post_types() as $post_type)
		if(current_user_can('edit_others_posts'))
			add_meta_box('super_sticky_meta', 'Sticky', 'super_sticky_meta', $post_type, 'side', 'high');
}

add_action('admin_init', 'super_sticky_add_meta_box');

function super_sticky_posts_filter($query) {
	if($query->is_home && !$query->get('suppress_filters'))
		if(super_sticky_filter('home'))
			$query->set('post_type', super_sticky_post_types(true));
		else
			$query->set('post_type', 'post');
	return $query;
}

add_filter('pre_get_posts', 'super_sticky_posts_filter');
?>