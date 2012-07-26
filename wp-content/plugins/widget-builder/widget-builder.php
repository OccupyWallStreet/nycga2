<?php
/*
Plugin Name:	Widget Builder
Description:	This plugin creates predefined content widgets that can be used in multiple sidebars while being centrally configured.
Author:			Timothy Wood, Jonathan Brinley, Modern Tribe, Inc.
Version:		1.2
Author URI:		http://tri.be
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die();

if ( !class_exists('Tribe_Widget_Builder') ) {
	// Load the widget builder
	require_once( 'lib/tribe-widget-factory/tribe-widget-factory.php');
	require_once( 'classes/widget-builder.php' );
	add_action('plugins_loaded', array('Tribe_Widget_Builder', 'init'));

	// Load widget display
	require_once( 'classes/custom-widget-display.php' );
}

