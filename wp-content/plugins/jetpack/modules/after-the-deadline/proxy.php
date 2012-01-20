<?php
/*
 *  This script redirects AtD AJAX requests to the AtD service
 */

/**
 * Returns array with headers in $response[0] and body in $response[1]
 * Based on a function from Akismet
 */
function AtD_http_post( $request, $host, $path, $port = 80 ) {
	$http_args = array(
		'body'			=> $request,
		'headers'		=> array(
			'Content-Type'	=> 'application/x-www-form-urlencoded; ' .
								'charset=' . get_option( 'blog_charset' ),
			'Host'			=> $host,
			'User-Agent'	=> 'AtD/0.1'
		),
		'httpversion'	=> '1.0',
		'timeout'		=> 15
	);
	$AtD_url = "http://{$host}{$path}";
	$response = wp_remote_post( $AtD_url, $http_args );
	
	if ( is_wp_error( $response ) )
		return array();
	
	return array( $response['headers'], $response['body'] );
}

/* 
 *  This function is called as an action handler to admin-ajax.php
 */
function AtD_redirect_call() {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
                $postText = trim(  file_get_contents( 'php://input' )  );

        $url = $_GET['url'];

	$service = 'service.afterthedeadline.com';
	if ( defined('WPLANG') ) {
		if ( strpos(WPLANG, 'pt') !== false )
			$service = 'pt.service.afterthedeadline.com';
		else if ( strpos(WPLANG, 'de') !== false )
			$service = 'de.service.afterthedeadline.com';
		else if ( strpos(WPLANG, 'es') !== false )
			$service = 'es.service.afterthedeadline.com';
		else if ( strpos(WPLANG, 'fr') !== false )
			$service = 'fr.service.afterthedeadline.com';
	}
	$user = wp_get_current_user();
	$guess = strcmp( AtD_get_setting( $user->ID, 'AtD_guess_lang' ), "true" ) == 0 ? "true" : "false";

        $data = AtD_http_post( $postText . "&guess=$guess", defined('ATD_HOST') ? ATD_HOST : $service, $url, defined('ATD_PORT') ? ATD_PORT : 80 );

        header( 'Content-Type: text/xml' );

		if ( !empty($data[1]) )
			echo $data[1];

		die();
}
