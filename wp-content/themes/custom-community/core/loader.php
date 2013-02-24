<?php

define( 'CC_VERSION', '1.9.1' );

/**
 * loads custom community files
 *
 * @package Custom Community
 * @since 1.8.3
 */
function cc_init() {
	
	require( dirname( __FILE__ ) . '/custom-community.php' );
	$cc = new Custom_Community;
	
}

add_action( 'init', 'cc_init',1,1 );

