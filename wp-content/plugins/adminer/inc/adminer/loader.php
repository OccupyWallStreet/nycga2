<?php
// search and include wp-load.php
// require_once('../../../../../wp-load.php');
function get_wp_root ( $directory ) {
	global $wp_root;
	
	foreach( glob( $directory . "/*" ) as $f ) {
		
		if ( 'wp-load.php' == basename($f) ) {
			$wp_root = str_replace( "\\", "/", dirname($f) );
			return TRUE;
		}
		
		if ( is_dir($f) )
			$newdir = dirname( dirname($f) );
	}
	
	if ( isset($newdir) && $newdir != $directory ) {
		if ( get_wp_root ( $newdir ) )
			return FALSE;
	}
	
	return FALSE;
} // end function to find wp-load.php

if ( ! function_exists('add_action') ) {

	get_wp_root ( dirname( dirname(__FILE__) ) );
	if ( $wp_root ) {
		include_once $wp_root . '/wp-load.php';
	} else {
		die( 'Cheatin&#8217; uh?');
		exit;
	}
}

if ( ! current_user_can('unfiltered_html') )
	wp_die( __('Cheatin&#8217; uh?') );

function adminer_object() {
	
	class AdminerUser extends Adminer {
		
		function name() {
			
			return get_option('blogname');
		}
		
		function credentials() {
			global $wpdb;
			
			return array(DB_HOST, DB_USER, DB_PASSWORD);
		}
		
		function database() {
			global $wpdb;
			
			return DB_NAME;
		}
		
		function login($login, $password) {
			global $wpdb;
			
			return ($login == DB_USER);
		}
		
	}
	
	return new AdminerUser;
}

include_once ( 'index.php' );
