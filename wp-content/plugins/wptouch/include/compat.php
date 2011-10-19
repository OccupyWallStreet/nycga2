<?php
// Plugin compatability file
// to help with older versions of WordPress and WordPress MU
// some concepts taken from compatibility.php from the OpenID plugin at http://code.google.com/p/diso/ 

// this will also be the base include for AJAX routines
// so we need to check if WordPress is loaded, if not, load it
// we'll use ABSPATH, since that's defined when WordPress loads
// should be included in the init function of normal plugins

if ( !function_exists( 'compat_get_wp_content_dir' ) ) {
	function compat_get_wp_content_dir() {
		if ( defined( 'WP_CONTENT_DIR' ) ) {
			return WP_CONTENT_DIR;
		} else {
			return bloginfo( 'wpurl' ) . '/wp-content';	
		}
	}
}

if ( !function_exists( 'compat_get_wp_content_url' ) ) {
	function compat_get_wp_content_url() {
		if ( defined( 'WP_CONTENT_URL') ) {
			return WP_CONTENT_URL;
		} else {
			return ABSPATH . 'wp-content';
		}
	}
}

if ( !function_exists( 'compat_is_wordpress_mu' ) ) {
	function compat_is_wordpress_mu() {	
		return file_exists( compat_get_wp_content_dir() . '/mu-plugins' );
	}
}
	

if ( !function_exists( 'compat_get_base_plugin_dir' ) ) {
	function compat_get_base_plugin_dir() {
		if ( compat_is_wordpress_mu() && strpos( dirname( __FILE__ ), 'mu-plugins') !== false ) {
			return compat_get_wp_content_dir() . '/mu-plugins';
		} else {	
			return compat_get_wp_content_dir() . '/plugins';
		}
	}
}

if ( !function_exists( 'compat_get_base_plugin_url' ) ) {
	function compat_get_base_plugin_url() {
		if ( compat_is_wordpress_mu() && strpos( dirname( __FILE__ ), 'mu-plugins')  !== false ) {
			return compat_get_wp_content_url() . '/mu-plugins';
		} else {
			return compat_get_wp_content_url() . '/plugins';
		}
	}
}

if ( !function_exists( 'compat_get_plugin_dir') ) {
	function compat_get_plugin_dir( $plugin_name ) {
		return compat_get_base_plugin_dir() . '/' . $plugin_name;
	}
}

if ( !function_exists( 'compat_get_plugin_url' ) ) {
	function compat_get_plugin_url( $plugin_name ) {
		return compat_get_base_plugin_url() . '/' . $plugin_name;
	}	
}

if ( !function_exists( 'compat_get_upload_dir' ) ) {
	function compat_get_upload_dir() {
		if ( compat_is_wordpress_mu() ) {
			global $blog_id;
			return compat_get_wp_content_dir() . '/blogs.dir/' . $blog_id . '/uploads';
		} else {	
			$upload_info = wp_upload_dir();		
			return $upload_info['basedir'];
		}		
	}
}

if ( !function_exists( 'compat_get_upload_url' ) ) {
	function compat_get_upload_url() {
		if ( compat_is_wordpress_mu() ) {
			global $blog_id;
			return compat_get_wp_content_url() . '/blogs.dir/' . $blog_id . '/uploads';
		} else {	
			$upload_info = wp_upload_dir();		
			return $upload_info['baseurl'];
		}			
	}	
}