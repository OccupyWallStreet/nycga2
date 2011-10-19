<?php
/**
* Settings API Callback Function - to sanitise Settings page input
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info Sanitise Settings screen Options input.
* @info register_settings() callback function.
*
* @since 3.2
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', DFCG_DOMAIN) );
}


/**
* Settings API callback function
*
* @param array $input $_POST input from form
* @global array $dfcg_options plugin options from db
* @return $input Sanitised form input ready for db
* @since 3.2.2
* @updated 3.3.4
*/
function dfcg_sanitise($input) {
	
	global $dfcg_options;
	
	// Is the user allowed to do this? Probably not needed...
	if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
		die( __('Sorry. You do not have permission to do this.', DFCG_DOMAIN) );
	}
	
	
	
	/* If RESET is checked, reset the options, and don't bother sanitising */
	
	if ( $input['reset'] == "1" ) {
		
		// put back the defaults
		$input = dfcg_default_options();
		
		// we need this for use in add_action('admin_notices', 'dfcg_notice_reset')
		$input['just-reset'] = esc_attr('true');
		
		return $input;
	}
	
	
	/***** Some error messages for later *****/
	
	// Generic error message - triggered by wp_die
	$dfcg_sanitise_error = esc_attr__('An error has occurred. Go back and try again.', DFCG_DOMAIN);
	
	
	/***** Now correct certain options *****/
	
	// trim whitespace - all options
	foreach( $input as $key => $value ) {
		$input[$key] = trim($value);
	}
	
	// deal with just-reset option, overwrite it in case it's 'true'
	$input['just-reset'] = '0';
	
	// deal with One Category Method "All" option to suppress WP_Class Error if category_description() is passed a '0'.
	// WP_Query will fail gracefully because cat='' is ignored
	// TODO: Probably not needed now due to sanitisation routines below
	if( $input['cat-display'] == 0 ) {
		$input['cat-display'] = '';
	}
	
	
	
	/***** Organise the options by type etc, into arrays, then sanitise / validate / format correct *****/
	
	//	Whitelist options													(10)	(10)
	//	Path and URL options												(6)		(1)
	//	On-off options														(1)
	//	Bool options														(15)
	//	String options - no XHTML allowed									(5)
	//	String options - some XHTML allowed									(1)
	//	String options - CSS hexcodes										(7)
	//	String options - numeric comma separated only 						(2)
	//	String options - filenames											(1)
	//	Integer options - positive - can be blank, can't be zero 			(9)
	//	Integer options - positive - can be blank, can't be zero 			(1)
	//	Integer options - positive - can't be blank, can't be zero 			(9)
	//	Integer options - positive integer - can't be blank, can be zero 	(18)
	//	Integer options - positive - large									(1)
	//	Total 																85
	
	
	/***** Whitelist options (10/10) *****/
	
	if ( function_exists('wpmu_create_blog') ) {
		// We're in WPMU
		$whitelist_opts = array( 'image-url-type', 'populate-method', 'defaultTransition', 'limit-scripts', 'scripts', 'slide-h2-weight', 'desc-method', 'slide-p-a-weight', 'slide-p-ahover-weight', 'thumb-type' );
	} else {
		// We're in WP
		$whitelist_opts = array( 'image-url-type', 'populate-method', 'defaultTransition', 'limit-scripts', 'scripts', 'slide-h2-weight', 'desc-method', 'slide-p-a-weight', 'slide-p-ahover-weight', 'thumb-type' );
	}
	
	// Define whitelist of known values
	$dfcg_whitelist = array( 'full', 'partial', 'multi-option', 'one-category', 'id-method', 'custom-post', 'fade', 'fadeslideleft', 'continuousvertical', 'continuoushorizontal', 'homepage', 'pagetemplate', 'other', 'mootools', 'jquery', 'bold', 'normal', 'manual', 'auto', 'none', 'page', 'post-thumbnails', 'legacy' );
	
	// sanitise
	foreach( $whitelist_opts as $key ) {
		// If option value is not in whitelist
		if( !in_array( $input[$key], $dfcg_whitelist ) ) {
			//Used for testing: $input[$key] = 'dodgy';
			//var_dump($key, $input[$key]);
			wp_die( "Dynamic Content Gallery Message #20: " . $dfcg_sanitise_error . "<br />Error with option: " . $key . "<br />Value: " . $input[$key] );
		}
	}
	
	
	/***** Path and URL options (6/1) *****/
	
	if ( function_exists('wpmu_create_blog') ) {
		// We're in WPMU
		$abs_url_opts = array( 'homeurl' );
	} else {
		// We're in WP
		$abs_url_opts = array( 'imageurl', 'defimgmulti', 'defimgonecat', 'defimgid', 'defimgcustompost', 'homeurl' );
	}
	
	// sanitise and add trailing slash
	foreach( $abs_url_opts as $key ) {
		if( !empty($input[$key]) ) {
			if( $key == 'defimgid' ) {
				// Sanitise for db only
				$input[$key] = esc_url_raw( $input[$key] );
			} else {
				// Trailingslashit if there is something to do it to
				$input[$key] = trailingslashit( $input[$key] );
				// Sanitise for db
				$input[$key] = esc_url_raw( $input[$key] );
			}
		}
	}
	
	
	/***** On-off options (1) *****/
	
	$onoff_opts = array( 'mootools' );
	
	// sanitise, cast as 1 or 0, eg MOOTOOLS checkbox
	foreach( $onoff_opts as $key ) {
		$input[$key] = $input[$key] ? '1' : '0';
	}
	
	
	/***** Bool options (15) *****/
	
	$bool_opts = array( 'reset', 'showCarousel', 'showInfopane', 'timed', 'slideInfoZoneSlide', 'errors', 'posts-column', 'pages-column', 'posts-desc-column', 'pages-desc-column', 'just-reset', 'pages-sort-column', 'id-sort-control', 'showArrows', 'slideInfoZoneStatic' );
	
	// sanitise, eg RESET checkbox
	foreach( $bool_opts as $key ) {
		$input[$key] = $input[$key] ? 'true' : 'false';
	}
	
	
	/***** String options - no XHTML allowed (5) *****/
	
	$str_opts_no_html = array( 'textShowCarousel', 'slideInfoZoneOpacity', 'more-text', 'custom-post-type', 'custom-post-type-tax' );
	
	// sanitise
	foreach( $str_opts_no_html as $key ) {
		
		// Extract first 50 characters (v3.2: increased from 25 chars to allow longer Featured Article text) 
		$input[$key] = substr( $input[$key], 0, 50 );
		
		$input[$key] = wp_filter_nohtml_kses( $input[$key] );
	}
	
	
	/***** String options - some XHTML allowed (1) *****/
	
	$str_opts_html = array( 'defimagedesc' );
	
	// Note, form already includes stripslashes
	
	$allowed_html = array( 'a' => array('href' => array(),'title' => array() ), 'br' => array(), 'em' => array(), 'strong' => array() );
	
	$allowed_protocols = array( 'http', 'https', 'mailto', 'feed' );
	
	// sanitise
	foreach( $str_opts_html as $key ) {
		$input[$key] = wp_kses( $input[$key], $allowed_html, $allowed_protocols );
	} 
	
	
	/***** String options - CSS hexcodes () *****/
	
	$str_opts_hexcode = array( 'gallery-border-colour', 'slide-h2-colour', 'slide-p-colour', 'slide-overlay-color', 'slide-p-a-color', 'slide-p-ahover-color', 'gallery-background' );
	
	// TODO: This could be improved - regex doesn't validate whether a valid hex code.
	
	// deal with String options - CSS hexcodes
	foreach( $str_opts_hexcode as $key ) {
		
		// Strip out any whitespace within list
		$input[$key] = str_replace( " ", "", $input[$key] );
		
		// If first character in string is not a #
		if( !substr( $input[$key], 0, 1 ) == '#' ) {
			// Add one
			$input[$key] = substr_replace( $input[$key], '#', 0, 0 );
		}
		// Extract first 7 characters
		$input[$key] = substr( $input[$key], 0, 7 );
		
		// Make sure value contains only allowed numbers and characters
		if( !preg_match_all( '/^[#A-Za-z0-9]+$/i', $input[$key], $result ) ) {
			// If not, revert to existing value
			$input[$key] = $dfcg_options[$key];
		}
	}
	
	
	/***** String options - numeric comma separated only (2) *****/
	
	$str_opts_csv_num = array( 'ids-selected', 'page-ids' );
	
	// sanitise
	foreach( $str_opts_csv_num as $key ) {
		
		if( !empty( $input[$key] ) ) {
			// Strip out any whitespace within list
			$input[$key] = str_replace( " ", "", $input[$key] );
			
			// If first character in string is a comma
			if( substr( $input[$key], 0, 1 ) == ',' ) {
				// Remove the first comma in the list
				$input[$key] = substr( $input[$key], 1 );
			}
			
			// If last character in string is a comma
			if( substr( $input[$key], -1) == ',' ) {
				// Remove the final comma in the list
				$input[$key] = substr( $input[$key], 0, substr( $input[$key], -1)-1 );
			}
			
			// Make sure list only contains numbers and commas
			if( !preg_match_all( '/^[0-9,]+$/i', $input[$key], $result ) ) {
				// Resets the dodgy $input to the existing value. Better user-experience in case of failure.
				$input[$key] = $dfcg_options[$key];
			}
		}
	}
	
	
	/***** String options - filenames (1) *****/
	
	$str_opts_filename = array( 'page-filename' );
	
	// This can be a comma separated list of page template filenames
	
	// sanitise
	foreach( $str_opts_filename as $key ) {
		
		if( !empty( $input[$key] ) ) {
		
			// Convert filename list to array
			$filenames = explode(',', $input[$key]);
			
			foreach( $filenames as $filename ) {
				// Strip out any whitespace within list
				$filename = str_replace( " ", "", $filename );
			
				// Make sure filename is alpha-num plus hypens and underscores with .php extension
				if( preg_match_all('/^([A-Za-z0-9_-]+(?=\.(php))\.\2)$/i', $filename, $result) ) {
					// Add ok filename to temp array
					$temp_array[] = $filename;
				} 
			}
			// Convert array back to comma separated list
			$input[$key] = implode(',', $temp_array);
		}
	}
	
	
	/***** Integer options - positive - can be blank, can't be 0 (9) *****/
	
	$int_opts_can_be_blank = array( 'off01', 'off02', 'off03', 'off04', 'off05', 'off06', 'off07', 'off08', 'off09' );
	
	// sanitise, but leave blank as empty, not 0
	foreach( $int_opts_can_be_blank as $key ) {
		//
		if( $input[$key] == 0 || $input[$key] == '0' ) {
			$input[$key] = '';
		} else {
			// Strip out any whitespace within
			$input[$key] = str_replace( " ", "", $input[$key] );
			// Extract first 2 characters
			$input[$key] = substr( $input[$key], 0, 2 );
			// Cast as integer
			$input[$key] = absint( $input[$key] );
		}
	}
	
	
	/***** Integer options - positive - can be blank, can't be 0 (1) *****/
	
	// Note: cat-display can be blank to avoid WP_Query error on first loading plugin
	
	$int_opts_can_be_blank_big = array( 'cat-display' );
	
	// sanitise, but leave blank as empty, not 0
	foreach( $int_opts_can_be_blank_big as $key ) {
		//
		if( $input[$key] == 0 || $input[$key] == '0' ) {
			$input[$key] = '';
		} else {
			// Strip out any whitespace within
			$input[$key] = str_replace( " ", "", $input[$key] );
			// Extract first 4 characters
			$input[$key] = substr( $input[$key], 0, 4 );
			// Cast as integer
			$input[$key] = absint( $input[$key] );
		}
	}
	
	
	/***** Integer options - positive - can't be blank, can't be zero (9) *****/
	
	// Theoretically, this isn't needed, unless user turns off Select boxes in browser
	
	$int_opts_nonblank_nonzero = array( 'cat01', 'cat02', 'cat03', 'cat04', 'cat05', 'cat06', 'cat07', 'cat08', 'cat09' );
	
	// sanitise, but leave blank and zero as 1
	foreach( $int_opts_nonblank_nonzero as $key ) {
		//
		if( empty( $input[$key] ) ) {
			$input[$key] = 1;
		} else {
			// Extract first 6 characters - increased from 4 to allow for big cat ID numbers
			$input[$key] = substr( $input[$key], 0, 6 );
			// Cast as integer
			$input[$key] = absint( $input[$key] );
		}
	}
	
	
	/***** Integer options - positive integer - can't be blank, can be zero (18) *****/
	
	$int_opts_nonblank = array( 'posts-number', 'gallery-width', 'gallery-height', 'gallery-border-thick', 'slide-height', 'slide-h2-size', 'slide-h2-padtb', 'slide-h2-padlr', 'slide-h2-marglr', 'slide-h2-margtb', 'slide-p-size', 'slide-p-padtb', 'slide-p-padlr', 'slide-p-marglr', 'slide-p-margtb', 'slide-p-line-height', 'max-char', 'custom-post-type-number' );
	
	// sanitise, limit to 4 chars, convert blanks to 0
	foreach( $int_opts_nonblank as $key ) {
		// Strip out any whitespace within
		$input[$key] = str_replace( " ", "", $input[$key] );
		// Extract first 4 characters
		$input[$key] = substr( $input[$key], 0, 4 );
		// Cast as integer
		$input[$key] = absint( $input[$key] );
	}
	
	
	/***** Integer options - positive - large (1) *****/
	
	$int_opts_large = array( 'delay' );
	
	// sanitise, limit to 5 chars, can't be blank, minimum value = 1000
	foreach( $int_opts_large as $key ) {
		// Strip out any whitespace within
		$input[$key] = str_replace( " ", "", $input[$key] );
		// Extract first 5 characters
		$input[$key] = substr( $input[$key], 0, 5 );
		// Cast as integer
		$input[$key] = absint( $input[$key] );
		// Minimum value = 1000 (otherwise gallery js script will go crazy)
		$min_value = 1000;
		if( $input[$key] < $min_value ) {
			$input[$key] = 1000;
		}
	}
	
	
	/***** String options (2) *****/
	
	
	
	
	// Return sanitised options array ready for db
	return $input;
}
