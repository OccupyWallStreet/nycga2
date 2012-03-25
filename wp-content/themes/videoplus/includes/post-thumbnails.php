<?php

/* sets predefined Post Thumbnail dimensions */

if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
	
	//default thumbnail size
	add_image_size( 'featured-thumb', 109, 72, true );	
	add_image_size( 'entry-thumb', 216, 120, true );
	
};

// NOTE: You need to regenerate all thumbnails if you modified the default thumbnails size
// Regenerate Thumbnails Plugin: http://wordpress.org/extend/plugins/regenerate-thumbnails/

?>