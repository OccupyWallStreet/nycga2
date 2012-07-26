<?php
/*
Plugin Name: Tribe Widget Factory
Plugin URI: http://tri.be
Description: Enable registration of parameterized widget classes
Author: Modern Tribe, Inc.
Author URI: http://tri.be
Contributors: jbrinley, Timothy Wood
Version: 1.0
*/

/**
 * Load all the plugin files and initialize appropriately
 *
 * @return void
 */
if ( !function_exists('tribe_widget_factory_load') ) { // play nice
	function tribe_register_widget( $widget_class, $params ) {
		/** @var Tribe_WP_Widget_Factory $wp_widget_factory */
		global $wp_widget_factory;
		$wp_widget_factory->register( $widget_class, $params );
	}

	function tribe_widget_factory_load() {
		require_once('Tribe_WP_Widget_Factory.php');
		$GLOBALS['wp_widget_factory'] = Tribe_WP_Widget_Factory::get_instance();
	}

	// $GLOBALS['wp_widget_factory'] is created just before 'setup_theme' hook is called
	// @see wp-settings.php
	add_action('setup_theme', 'tribe_widget_factory_load', 0, 0);
}
