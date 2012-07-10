<?php
header( 'Content-type: text/javascript' );
header( 'Cache-Control: must-revalidate' );
$offset = 72000;
header( "Expires: " . gmdate( "D, d M Y H:i:s", time() + 72000) . " GMT");


if ( ! isset( $_GET )) exit; 

if ( ! isset( $_GET[ 'scr' ] ) || $_GET[ 'scr' ] == '' ) {
	echo "/* Uh-Oh! No scripts were needed for this page. */" ;
	exit;
} else {
	$scr = addslashes( $scr );
}

$cache = ( isset( $_GET[ 'cache' ] ) && ( $_GET[ 'cache' ] == 'off' ) ) ? false : true;

$scr_arr = explode( '|', $_GET[ 'scr' ] );

if ( ! $cache ) {
	$scrss = '';
	foreach ( $scr_arr as $scr ) {
		$scrss .= file_get_contents( 'select/' . $scr . '.js' ) . "\n";
	}
	$scrss .= file_get_contents( 'select/init.js' ) . "\n";

	@include dirname( __FILE__ ) . '/JSMIN.php';
	$scrss = JSMIN::minify( $scrss );
	echo $scrss;
	exit;
}
?><?php

/**
 * Create cache dir.
 */
$wpui_dir = str_ireplace( 'plugins/wp-ui/js', '', dirname(__FILE__)) . 'uploads/wp-ui/';

$cache_dir = $wpui_dir . 'cache/';

is_dir( $cache_dir ) || @mkdir( $cache_dir, 0644, true );
is_readable( $cache_dir ) || @chmod( $cache_dir, 0644 ); 


/**
 * Source directory - within plugin dir.
 */
$dir = dirname(__FILE__ ) . '/select/';
$dirA = @scandir(  $dir );

$name = '';
foreach( $dirA as $di ) {
	if ( filemtime( $dir . $di ) == FALSE ) continue;
	$names = date( "YmdHis", filemtime( $dir.$di ));
	$name = md5($names);
}

$filenamestr = implode( '_', $scr_arr ) . '_' . $name . '.js';



/**
 * Check if the file is cached.
 */
if( file_exists( $cache_dir . $filenamestr ) ) {
	@readfile( $cache_dir . $filenamestr );
} else {
	$scrss = '';
	foreach ( $scr_arr as $scr ) {
		$scrss .= file_get_contents( 'select/' . $scr . '.js' ) . "\n";
	}
	$scrss .= file_get_contents( 'select/init.js' ) . "\n";

	require 'JSMIN.php';
	$scrss = JSMIN::minify( $scrss );
	$globs = glob( $cache_dir . implode( '_', $scr_arr ) . '*.js' );
	foreach( $globs as $glos ) {
		@unlink( $cache_dir . $glos );
	}
	@file_put_contents( $cache_dir . $filenamestr, $scrss );
	
	echo $scrss;
}


exit; // Dont remove.
?>