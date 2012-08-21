<?php
/*
 Plugin Name: Custom Post Widget
 Plugin URI: http://www.vanderwijk.com/services/web-design/wordpress-custom-post-widget/
 Description: Show the content of a custom post of the type 'content_block' in a widget.
 Version: 1.9.5
 Author: Johan van der Wijk
 Author URI: http://www.vanderwijk.com
 License: GPL2

 Release notes: Version 1.9.5 Added option to disable content filter
 
 Copyright 2012 Johan van der Wijk (email: info@vanderwijk.com)
 
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License, version 2, as 
 published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

// Set constant path to the custom-post-widget plugin directory.
define( 'CUSTOM_POST_WIDGET_DIR', plugin_dir_path( __FILE__ ) );
define( 'CUSTOM_POST_WIDGET_URL', WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),'',plugin_basename(__FILE__)) );

// Launch the plugin.
add_action( 'plugins_loaded', 'custom_post_widget_plugin_init' );

// Load the required files needed for the plugin to run in the proper order and add needed functions to the required hooks.
function custom_post_widget_plugin_init() {
	// Load the translation of the plugin.
	load_plugin_textdomain( 'custom-post-widget', false, 'custom-post-widget/languages' );
	add_action( 'widgets_init', 'custom_post_widget_load_widgets' );
}

// Loads the widgets packaged with the plugin.
function custom_post_widget_load_widgets() {
	require_once( CUSTOM_POST_WIDGET_DIR . '/post-widget.php' );
	register_widget( 'custom_post_widget' );
}
?>