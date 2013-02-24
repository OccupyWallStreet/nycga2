<?php
// the path to wp-load.php
require_once '../../config.php';

function fb_find_wp_config_path() {
	
	$dir = dirname(__FILE__);
	
	do {
		if( file_exists( $dir . "/wp-config.php" ) ) {
			return $dir;
			var_dump($dir);
		}
	} while ( $dir = realpath( "$dir/.." ) );
	
	return NULL;
}

if ( ! defined( 'ABSPATH' ) ) {
	
	//get_wp_root( dirname( dirname(__FILE__) ) );
	if ( ! empty( $wp_siteurl ) ) {
		if ( ! file_exists( $wp_siteurl . '/wp-load.php' ) ) {
			die( 'Cheatin&#8217; or you have the wrong path to <code>wp-load.php</code>, see the <a href="http://wordpress.org/extend/plugins/adminer/installation/">readme</a>?');
			exit;
		}
		
		define( 'WP_USE_THEMES', FALSE );
		require_once( $wp_siteurl . '/wp-load.php' );
	} else {
		define( 'WP_USE_THEMES', FALSE );
		require_once( fb_find_wp_config_path() . '/wp-config.php' );
	}
	
}

if ( ! defined( 'ABSPATH' ) ) {
	wp_die( __('Cheatin&#8217; uh?') );
	exit;
}

if ( ! current_user_can( 'unfiltered_html' ) ) {
	wp_die( __( 'Cheatin&#8217; uh? You do not have permission to use this.' ) );
	exit;
}

/**
 * Call Adminer with custom params
 * 
 * @return  class AdminerUser
 */
function adminer_object() {
	
	class AdminerUser extends Adminer {
		
		function name() {
			
			return 'Adminer';
		}
		
		function credentials() {
			
			return array( DB_HOST, DB_USER, DB_PASSWORD );
		}
		
		function database() {
			
			return DB_NAME;
		}
		
		function login( $login, $password ) {
			
			if ( current_user_can( 'unfiltered_html' ) )
				return ( $login == DB_USER );
			else {
				wp_die( __( 'Cheatin&#8217; uh? You do not have permission to use this.' ) );
				exit;
			}
		}
		
	}
	
	return new AdminerUser;
}

include_once( 'index.php' );
