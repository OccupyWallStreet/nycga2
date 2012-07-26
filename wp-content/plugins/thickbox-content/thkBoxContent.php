<?php
/*
Plugin Name: ThickBox Content
Plugin URI: http://www.PhoenixHomes.com/tech/thickbox-content
Description: Thickbox Content is a plugin that provides a quick and easy way to display content in a thickbox effect (via page/post editor). It supports thickbox iFrame, Ajax and Inline content types.
Author: Max Chirkov
Version: 1.0.5
Author URI: http://www.ibsteam.net

Copyright 2009 PhoenixHomes.com

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
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class thkBoxContent {



	function thkBoxContent() {
		global $wp_version;
		// The current version
		define('thkBoxContent_VERSION', '1.0.5');
		
		// Check for WP2.6 installation
		if (!defined ('IS_WP26'))
			define('IS_WP26', version_compare($wp_version, '2.6', '>=') );
		
		//This works only in WP2.6 or higher
		if ( IS_WP26 == FALSE) {
			add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' . __('Sorry, thkBoxContent works only under WordPress 2.6 or higher',"thkBC") . '</strong></p></div>\';'));
			return;
		}
		
		// define URL
		define('thkBoxContent_ABSPATH', WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/' );
		define('thkBoxContent_URLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );
		//define('thkBoxContent_TAXONOMY', 'wt_tag');
		
		
		
		
		include_once (dirname (__FILE__)."/lib/shortcodes.php");
		include_once (dirname (__FILE__)."/tinymce/tinymce.php");
		
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox');
	
	}

}

function _load_thkBox_images() {
		// WP core reference relative to the images. Bad idea
		echo "\n" . '<script type="text/javascript">tb_pathToImage = "' . get_option('siteurl') . '/wp-includes/js/thickbox/loadingAnimation.gif";tb_closeImage = "' . get_option('siteurl') . '/wp-includes/js/thickbox/tb-close.png";</script>'. "\n";			
	}
function _load_admin_scripts(){
	echo "\n" . '<script type="text/javascript">var thkBoxTINYMCE = "'.thkBoxContent_URLPATH .'tinymce";</script>'. "\n";			
}

function thkBoxContent_ajax_tinymce(){
	// check for rights
    if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') ) 
    	die(__("You are not allowed to be here"));
        	
   	include_once( thkBoxContent_ABSPATH . 'tinymce/window.php');
    
    die();
}

// Start this plugin once all other plugins are fully loaded
add_action( 'plugins_loaded', create_function( '', 'global $thkBoxContent; $thkBoxContent = new thkBoxContent();' ) );
add_action( 'wp_footer', '_load_thkBox_images', 11 );
add_action( 'wp_print_scripts', '_load_admin_scripts');
add_action( 'wp_ajax_thkBoxContent_tinymce', 'thkBoxContent_ajax_tinymce' );
?>