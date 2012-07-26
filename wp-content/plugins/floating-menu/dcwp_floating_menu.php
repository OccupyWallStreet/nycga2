<?php
/*
		Plugin Name: Floating Menu
		Plugin URI: http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-jquery-floating-menu/
		Tags: jquery, flyout, drop down, floating, sliding, menu, vertical, animated, navigation, widget
		Description: Floating Menu creates a sticky, floating menu widget from any Wordpress custom menu using jQuery.
		Author: Lee Chestnutt
		Version: 1.4
		Author URI: http://www.designchemical.com
*/

global $registered_skins;

class dc_jqfloatingmenu {

	function dc_jqfloatingmenu(){
		global $registered_skins;
	
		if(!is_admin()){
			// Header styles
			add_action( 'wp_head', array('dc_jqfloatingmenu', 'header') );
			// Scripts
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jqueryeasing', dc_jqfloatingmenu::get_plugin_directory() . '/js/jquery.easing.js', array('jquery') );
			wp_enqueue_script( 'jqueryhoverintent', dc_jqfloatingmenu::get_plugin_directory() . '/js/jquery.hoverIntent.minified.js', array('jquery') );
			wp_enqueue_script( 'dcjqfloatingmenu', dc_jqfloatingmenu::get_plugin_directory() . '/js/jquery.floater.2.2.js', array('jquery') );
			// Shortcodes
			add_shortcode( 'dcfl-link', 'dcfl_menu_link_shortcode' );
		}
		add_action( 'wp_footer', array('dc_jqfloatingmenu', 'footer') );
		
		$registered_skins = array();
	}

	function header(){
		//echo "\n\t";
	}
	
	function footer(){
		//echo "\n\t";
	}
	
	function options(){}

	function get_plugin_directory(){
		return WP_PLUGIN_URL . '/floating-menu';	
	}

};

// Include the widget
include_once('dcwp_floating_menu_widget.php');

// Initialize the plugin.
$dcjqfloatingmenu = new dc_jqfloatingmenu();

// Register the widget
add_action('widgets_init', create_function('', 'return register_widget("dc_jqfloatingmenu_widget");'));

/**
* Create a link shortcode for opening/closing the menu
*/
function dcfl_menu_link_shortcode($atts){
	
	extract(shortcode_atts( array(
		'text' => 'Click Here',
		'action' => 'link'
	), $atts));

	return "<a href='#' class='dcfl-$action'>$text</a>";

}

?>