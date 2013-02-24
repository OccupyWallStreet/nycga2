<?php
class Custom_Community{
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */
	function custom_community() {
		$this->__construct();
	}

	/**
	 * PHP 5 constructor
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function __construct() {
		global $bp;
			
		// Load predefined constants first thing
		add_action( 'cc_init', array( $this, 'load_constants' ), 2 );
		
		// Includes necessary files
		add_action( 'cc_init', array( $this, 'includes' ), 100, 4 );
		
		// Includes the necessary js
		add_action('wp_enqueue_scripts', array( $this, 'enqueue_script' ), 2 );
		
		// Let plugins know that Custom Community has started loading
		$this->init_hook();

		// Let other plugins know that Custom Community has finished initializing
		$this->loaded();
		
		if ( function_exists( 'bp_is_active' ) )
			BPUnifiedsearch::get_instance();
		
		if(!is_admin())
			$Theme_Generator = new CC_Theme_Generator();
	}
	
	/**
	 * defines custom community init action
	 *
	 * this action fires on WP's init action and provides a way for the rest of custom community,
	 * as well as other dependend plugins, to hook into the loading process in an
	 * orderly fashion.
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function init_hook() {
		do_action( 'cc_init' );
	}
	
	/**
	 * defines custom community action
	 *
	 * this action tells custom community and other plugins that the main initialization process has
	 * finished.
	 * 
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function loaded() {
		do_action( 'cc_loaded' );
	}
	
	/**
	 * defines constants needed throughout the theme.
	 *
	 * these constants can be overridden in bp-custom.php or wp-config.php.
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */		
	function load_constants() {
		
		// The slug used when deleting a doc
		if ( !defined( 'CC_TEMPLATE_PATH' ) )
			define( 'CC_TEMPLATE_PATH', 'CC_TEMPLATE_PATH' );
			
	}	
	
	/**
	 * includes files needed by custom community
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function includes() {
			
		require_once($this->require_path('/_inc/ajax.php'));
		
		// helper functions
		require_once($this->require_path('/core/includes/helper-functions.php'));
		
		// theme layout specific functions
		require_once($this->require_path('/core/includes/theme-generator/style.php'));
		require_once($this->require_path('/core/includes/theme-generator/theme-generator.php'));
		
		// wordpress specific functions
		require_once($this->require_path('/core/includes/wp/shortcodes.php'));
		//require_once($this->require_path('/core/includes/wp/templatetags.php'));
		require_once($this->require_path('/core/includes/wp/widgets.php'));

		// buddypress specific functions
		if(defined('BP_VERSION')){
			require_once($this->require_path('/core/includes/bp/templatetags.php'));
			require_once($this->require_path('/core/includes/bp/buddydev-search.php'));	
		}
		
		// themekraft framework specific functions
		require_once($this->require_path('/core/includes/tkf/wp/detect.php'));
		
		// admin specific functions
		//if ( is_admin() )
		//	require_once($this->require_path('admin/cheezcap.php'));
			
	}
	
	### add css and js
 	function enqueue_script() {
	     if( is_admin() )
	        return;
	
		// on single blog post pages with comments open and threaded comments
		if(defined('BP_VERSION')){
			if ( is_singular() && bp_is_blog_page() && get_option( 'thread_comments' ) ) {
		    // enqueue the javascript that performs in-link comment reply fanciness
	        wp_enqueue_script( 'comment-reply' ); 
	    	}
	    } else {
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) { 
	        // enqueue the javascript that performs in-link comment reply fanciness
	        wp_enqueue_script( 'comment-reply' ); 
	    	}
	    }
	        
	    wp_deregister_script( 'ep-jquery-css' );
	        
	    wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui' );	
		wp_enqueue_script( 'jquery-ui-tabs' );
		
		wp_register_script('reflection',get_template_directory_uri() . '/_inc/js/reflection.js','','' );
		wp_enqueue_script('reflection');
		
	}	
	
	/** check if it's a child theme or parent theme and return the correct path */
	function require_path($path){
	if( get_template_directory() != get_stylesheet_directory() && is_file(get_stylesheet_directory() . $path) ): 	
        return get_stylesheet_directory() . $path;
    else:
        return get_template_directory() . $path;
    endif;
	}
}
