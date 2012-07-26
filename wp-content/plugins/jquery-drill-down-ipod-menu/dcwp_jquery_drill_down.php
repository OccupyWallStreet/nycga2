<?php
/*
		Plugin Name: jQuery Drill Down iPod Menu
		Plugin URI: http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-jquery-drill-down-ipod-menu-widget/
		Tags: jquery, drill down, menu, slider, animated, css, navigation
		Description: Creates a widget, which allows you to create a drill down ipod style menu from any Wordpress custom menu using jQuery. Drill down menus are ideal for managing large, complicated menus in a small, compact and fixed area. Features include - handles multiple levels, saved state using cookies and multiple menus on the same page.
		Author: Lee Chestnutt
		Version: 1.3.1
		Author URI: http://www.designchemical.com
*/

global $registered_skins;

class dc_jqdrilldown {

	function dc_jqdrilldown(){
		global $registered_skins;
	
		if(!is_admin()){
			// Header styles
			add_action( 'wp_head', array('dc_jqdrilldown', 'header') );
		
			// Scripts
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquerycookie', dc_jqdrilldown::get_plugin_directory() . '/js/jquery.cookie.js', array('jquery') );
			wp_enqueue_script( 'jquerydcdrilldown', dc_jqdrilldown::get_plugin_directory() . '/js/jquery.dcdrilldown.1.2.js', array('jquery') );
		}
		add_action( 'wp_footer', array('dc_jqdrilldown', 'footer') );
		
		$registered_skins = array();
	}

	function header(){
		echo "\n\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".dc_jqdrilldown::get_plugin_directory()."/css/dcdrilldown.css\" media=\"screen\" />";
	}
	
	function footer(){
		//echo "\n\t";
	}
	
	function options(){}

	function get_plugin_directory(){
		return WP_PLUGIN_URL . '/jquery-drill-down-ipod-menu';	
	}

};

// Include the widget
include_once('dcwp_jquery_drill_down_widget.php');

// Initialize the plugin.
$dcjqaccordion = new dc_jqdrilldown();

// Register the widget
add_action('widgets_init', create_function('', 'return register_widget("dc_jqdrilldown_widget");'));

?>