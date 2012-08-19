<?php
/**
* Settings Page options validation functions
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info Validates certain key Settings and produces validation messages after Settings Page form is submitted
*
* @since 3.0
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', DFCG_DOMAIN) );
}


/**
* Function for validation on loading of Settings Page
*	
*	Prints validation messages to the Settings Page.
*	Thanks to register_settings/settings_fields, Settings Page
*	is refreshed after Submit, so this function can be used
*	either on fresh load, or after submit. See dfcg-ui-admin-screen.php.
*
*	Updated for new Custom Post type Gallery Method in 3.3
*
* @uses dfcg_wp_version_check()
*
* @param array $options_array, Options from db
*
* @since 3.0
* @updated 3.3
*/
function dfcg_on_load_validation($options_array) {

	// Run WP version check - will display warning if WP version is less than 2.8+
	dfcg_wp_version_check();

	// If Partial URL is selected, imageurl must be defined
	if( $options_array['image-url-type'] == 'partial' && empty($options_array['imageurl']) ) {
		echo '<div class="error"><p><strong>' . __('Warning! You have selected the "Partial" URL option but you have not defined the URL to your images folder in the Image Management tab.', DFCG_DOMAIN) . '</strong></p></div>';
	}
	
	// If Pages, Page ID's must be defined
	if( $options_array['populate-method'] == 'id-method' && empty($options_array['ids-selected']) ) {
		echo '<div class="error"><p><strong>' . __('Warning! You are using the "ID Method" Gallery Method. You must enter at least two valid Post/Page ID\'s in the ID Method settings in the Gallery Method tab.', DFCG_DOMAIN) . '</strong></p></div>';
	}
	
	// If Multi Option, must be minimum of 2 Post Selects
	if( $options_array['populate-method'] == 'multi-option' ) {
	
		$multioption_raw_offsets = array (
			$options_array['off01'],
			$options_array['off02'],
			$options_array['off03'],
			$options_array['off04'],
			$options_array['off05'],
			$options_array['off06'],
			$options_array['off07'],
			$options_array['off08'],
			$options_array['off09'],
			);
		
		$multioption_offsets = array();
		
		foreach( $multioption_raw_offsets as $key => $value ) {
			$raw_offset = $multioption_raw_offsets[$key];
			if( !empty($raw_offset) ) {
				$temp_array = $multioption_raw_offsets[$key];
				array_push($multioption_offsets, $temp_array);
				unset($temp_array);
			}
		}
		
		if( count($multioption_offsets) < 2 ) {
			echo '<div class="error"><p><strong>' . __('Warning! You are using the "Multi Option" Gallery Method. You must enter at least 2 Posts Selects in the Multi Option settings in the Gallery Method tab.', DFCG_DOMAIN) . '</strong></p></div>';
		}
	}
	
	// Yellow warning messages
	if ( !function_exists('wpmu_create_blog') ) {
		
		// If Multi Option, defimgmulti should be defined
		if( $options_array['populate-method'] == 'multi-option' && empty($options_array['defimgmulti']) ) {
			echo '<div class="updated"><p><strong>' . __('Note: You are using the "Multi Option" Gallery Method. Enter the Path to your Category default images folder in the Multi Option settings in the Gallery Method tab to take advantage of the default image feature.', DFCG_DOMAIN) . '</strong></p></div>';
		}
		
		// If One Category, defimgonecat should be defined
		if( $options_array['populate-method'] == 'one-category' && empty($options_array['defimgonecat']) ) {
			echo '<div class="updated"><p><strong>' . __('Note: You are using the "One Category" Gallery Method. Enter the Path to your Category default images folder in the One Category settings in the Gallery Method tab to take advantage of the default image feature.', DFCG_DOMAIN) . '</strong></p></div>';
		}
	
		// If ID Method, defimgpages should be defined
		if( $options_array['populate-method'] == 'id-method' && empty($options_array['defimgid']) ) {
			echo '<div class="updated"><p><strong>' . __('Note: You are using the "ID Method" Gallery Method. Enter the URL of your default image in the ID Method settings in the Gallery Method tab to take advantage of the default image feature.', DFCG_DOMAIN) . '</strong></p></div>';
		}
		
		// If Custom Post, defimgcustompost should be defined
		if( $options_array['populate-method'] == 'custom-post' && empty($options_array['defimgcustompost']) ) {
			echo '<div class="updated"><p><strong>' . __('Note: You are using the "Custom Post Type" Gallery Method. Enter the Path to your Taxonomy default images folder in the Custom Post settings in the Gallery Method tab to take advantage of the default image feature.', DFCG_DOMAIN) . '</strong></p></div>';
		}
	}
}