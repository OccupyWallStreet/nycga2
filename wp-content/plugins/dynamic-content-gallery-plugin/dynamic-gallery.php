<?php
/**
* Front-end - This is the main file used to display the gallery
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* NOTE: This file was updated due to change from Pages Method to ID Method in v3.3
*
*	This is the file that displays the gallery, called by dynamic_content_gallery()
*	template tag function.
*
*	This file is included by dynamic_content_gallery() in dfcg-gallery-core.php
*	therefore local scope applies to variables here - unless global in dynamic_content_gallery()
*
*	Note: the name of this file is preserved because some users will still be using the
*	old method of calling the plugin (now replaced with template tag)
*
*	4 methods of populating the gallery as per Settings:
*		- Multi Option
*		- One Category
*		- ID Method
*		- Custom Post type Method - added in v3.3
*	Plus 2 script options for each gallery method
*
* @since 3.0
* @updated 3.3
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit(__( "Sorry, you are not allowed to access this file directly.", DFCG_DOMAIN ));
}


/*	Determine which scripts are being loaded */
if( $dfcg_options['scripts'] == 'mootools' ) {

	if($dfcg_options['populate-method'] == 'multi-option' ) {
		// Populate method = MULTI-OPTION
		dfcg_multioption_method_gallery();
	
	} elseif( $dfcg_options['populate-method'] == 'one-category' || $dfcg_options['populate-method'] == 'custom-post' ) {
		// Populate method = ONE CATEGORY or CUSTOM POST TYPE
		dfcg_onecategory_method_gallery();
	
	} elseif($dfcg_options['populate-method'] == 'id-method' ) {
		// Populate method = ID METHOD
		dfcg_id_method_gallery();
	}


} elseif( $dfcg_options['scripts'] == 'jquery' ) {
	
	if($dfcg_options['populate-method'] == 'multi-option' ) {
		// Populate method = MULTI-OPTION
		dfcg_jq_multioption_method_gallery();
	
	} elseif($dfcg_options['populate-method'] == 'one-category' || $dfcg_options['populate-method'] == 'custom-post' ) {
		// Populate method = ONE CATEGORY or CUSTOM POST TYPE
		dfcg_jq_onecategory_method_gallery();

	} elseif($dfcg_options['populate-method'] == 'id-method' ) {
		// Populate method = PAGES
		dfcg_jq_id_method_gallery();
	}

/* Something has gone horribly wrong and there's no output! */
} else {

	$output = '';
	$output .= $dfcg_errmsgs['public'];
	$output .= "\n" . $dfcg_errmsgs['10'] . "\n";
	echo $output;
}
?>