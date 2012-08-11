<?php
/*
Plugin Name: Slick Social Share Buttons
Plugin URI: http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-slick-social-share-buttons/
Tags: social media, facebook, linkedin, twitter, google+1, digg, delicious, social networks, bookmarks, buttons, animated, jquery, flyout, drop down, floating, sliding, pin it, reddit, pinterest, social statistics, social metrics
Description: Slick social share buttons adds facebook, twitter, google +1, linkedin, digg, stumbleupon, delicious, reddit, buffer and pinterest pin it social media buttons in a floating or slide out tab. Includes a social statistics page in the plugin admin panel showing summaries of all share totals
Author: Lee Chestnutt
Author URI: http://www.designchemical.com
Version: 2.4.3
*/

class dc_jqslicksocial {

	function dc_jqslicksocial(){
	
		if(!is_admin()){
		
			add_action('wp_enqueue_scripts', array('dc_jqslicksocial', 'dcssb_scripts'));
			add_action( 'wp_footer', array('dc_jqslicksocial', 'footer') );
			
			// Shortcodes
			add_shortcode( 'dcssb-link', 'dcssb_share_link_shortcode' );
			
			
				add_filter('language_attributes', array('dc_jqslicksocial','OpenGraph'));
				add_filter('language_attributes', array('dc_jqslicksocial','FGraph'));
			
		}
	}

	function header(){
		//echo "\n\t";
	}
	
	function footer(){
		//echo "\n\t";
		
		$dcjqslicksocial_buttons = new dc_jqslicksocial_buttons();
	}
	
	function dcssb_scripts(){
	
		$show = '0';
		if(dc_jqslicksocial_buttons::dcssb_check_category() == '0'){
			if(is_single()){
				$show = dc_jqslicksocial_buttons::get_dcssb_default('show_post') == true ? '1' : '0';
			}
			if(is_page()){
				$show = dc_jqslicksocial_buttons::get_dcssb_default('show_page') == true ? '1' : '0';
			}
			if(is_front_page()){
				$show = dc_jqslicksocial_buttons::get_dcssb_default('show_home') == true ? '1' : '0';
			}
			if(is_home()){
				$show = dc_jqslicksocial_buttons::get_dcssb_default('show_blog') == true ? '1' : '0';
			}
			if(is_category()){
				$show = dc_jqslicksocial_buttons::get_dcssb_default('show_category') == true ? '1' : '0';
			}
			if(is_archive()){
				$show = dc_jqslicksocial_buttons::get_dcssb_default('show_archive') == true ? '1' : '0';
			}
		}
		if($show == '1'){
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'gasocial', dc_jqslicksocial::get_plugin_directory() . '/js/ga.social_tracking.js', array('jquery') );
			if(dc_jqslicksocial_buttons::get_dcssb_default('method') == 'stick'){
				wp_enqueue_script( 'jqueryslick', dc_jqslicksocial::get_plugin_directory() . '/js/jquery.social.slick.1.0.js', array('jquery') );
			} else {
				wp_enqueue_script( 'jqueryeasing', dc_jqslicksocial::get_plugin_directory() . '/js/jquery.easing.js', array('jquery') );
				wp_enqueue_script( 'jqueryfloater', dc_jqslicksocial::get_plugin_directory() . '/js/jquery.social.float.1.3.js', array('jquery') );
			}
			
			dc_jqslicksocial_buttons::dcssb_styles();
			
			if( dc_jqslicksocial_buttons::get_dcssb_default('disable_opengraph') != 'true' ){
				dc_jqslicksocial_buttons::dcssb_opengraph();
			}
		}
	}
	
	function get_plugin_directory(){
		return WP_PLUGIN_URL . '/slick-social-share-buttons';	
	}
	
	/* OpenGraph Support */
	function OpenGraph($attr) {
        
		if( dc_jqslicksocial_buttons::get_dcssb_default('disable_opengraph') != 'true' ){
			$attr .= "\n xmlns:og='http://opengraphprotocol.org/schema/'"; 
		}
		return $attr;
	}
	
	function FGraph($attr) {
		
		if( dc_jqslicksocial_buttons::get_dcssb_default('disable_opengraph') != 'true' ){
			$attr .= "\n xmlns:fb='http://www.facebook.com/2008/fbml'";
		}
		return $attr;
	}
};

require_once('inc/dcwp_admin.php');
require_once('inc/dcwp_social.php');

if(is_admin()) {

	$dc_jqslicksocial_admin = new dc_jqslicksocial_admin();

}

// Initialize the plugin.
$dcjqslicksocial = new dc_jqslicksocial();

/**
* Create a link shortcode for opening/closing the form
*/
function dcssb_share_link_shortcode($atts){
	
	extract(shortcode_atts( array(
		'text' => 'Share',
		'action' => 'link'
	), $atts));

	return "<a href='#' class='dcssb-$action'>$text</a>";

}
?>