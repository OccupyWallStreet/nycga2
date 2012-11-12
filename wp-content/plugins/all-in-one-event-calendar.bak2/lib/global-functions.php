<?php
//
//  global-functions.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2012-02-28.
//

/**
 * url_get_contents function
 *
 * @param string $url URL 
 *
 * @return string
 **/
function url_get_contents( $url ) {
	// holds the output
	$output = "";

	// To make a remote call in wordpress it's better to use the wrapper functions instead
	// of class methods. http://codex.wordpress.org/HTTP_API
	// SSL Verification was disabled in the cUrl call
	$result = wp_remote_get( $url, array( 'sslverify' => false, 'timeout' => 120 ) );
	// The wrapper functions return an WP_error if anything goes wrong.
	if( is_wp_error( $result ) ) {
		// We explicitly return false to notify an error. This is exactly the same behaviour we had before
		// because both curl_exec() and file_get_contents() returned false on error
		return FALSE;
	}

	$output = $result['body'];

	// check if data is utf-8
	if( ! SG_iCal_Parser::_ValidUtf8( $output ) ) {
		// Encode the data in utf-8
		$output = utf8_encode( $output );
	}

	return $output;
}

/**
 * is_curl_available function
 *
 * checks if cURL is enabled on the system
 *
 * @return bool
 **/
function is_curl_available() { 
	
	if( ! function_exists( "curl_init" )   && 
      ! function_exists( "curl_setopt" ) && 
      ! function_exists( "curl_exec" )   && 
      ! function_exists( "curl_close" ) ) {
			
			return false; 
	}
	
	return true;
}