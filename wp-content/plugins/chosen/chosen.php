<?php
/*
Plugin Name: Chosen for WordPress
Plugin URI: http://wordpress.org/extend/plugins/chosen/
Description: Chosen makes long, unwieldy select boxes much more user-friendly. There are no settings. Chosen applies itself automatically.
Author: Brent Shepherd
Version: 0.2
Author URI: http://find.brentshepherd.com
License: GPLv2 or later
*/

WP_Chosen::init();

class WP_Chosen {

	/**
	 * URL to the directory housing Chosen Javascript files.
	 */
	public static $chosen_url;

	/**
	 * URL to the directory of this plugin
	 */
	public static $wp_chosen_url;


	/**
	 * Setup the class variables & hook functions.
	 */
	public static function init() {
		
		self::$wp_chosen_url = plugins_url( '', __FILE__ );
		self::$chosen_url    = plugins_url( 'chosen', __FILE__ );

		add_action( 'wp_print_scripts', __CLASS__ . '::maybe_enqueue_scripts' );

		add_shortcode( 'chosen', __CLASS__ . '::shortcode_handler' );
	}


	/**
	 * If the post/page contains a select element, enqueue the chosen & jquery scripts.
	 */
	public static function maybe_enqueue_scripts() {

		if( self::contains_select() && ! is_admin() ) {
			wp_enqueue_style(  'chosen', self::$chosen_url . '/chosen.css' );
			wp_enqueue_script( 'chosen', self::$chosen_url . '/chosen.jquery.min.js', array( 'jquery' ), false, true );
			wp_enqueue_script( 'wp-chosen', self::$wp_chosen_url . '/wp-chosen.js', array( 'chosen', 'jquery' ), false, true );
		}
	}


	/**
	 * Checks the post content to see if it contains a select element. 
	 */
	private static function contains_select( $content = '' ){
		global $post;

		if( empty( $content ) && is_object( $post ) )
			$content = $post->post_content;

		// Contains a vanilla select element
		if( strpos( $content, '<select' ) !== false )
			return true;
		// Contains Grunion Contact Form
		elseif( strpos( $content, '[contact-form' ) !== false )
			return true;
		// Brute force load
		elseif( strpos( $content, '[chosen' ) !== false )
			return true;
		else
			return false;
	}


	/**
	 * Return an empty string in place of the [chosen] shortcode. It's simply a flag to 
	 * enqueue the appropriate scripts & styles.
	 */
	public static function shortcode_handler( $atts, $content = null ) {
		return '';
	}

}
