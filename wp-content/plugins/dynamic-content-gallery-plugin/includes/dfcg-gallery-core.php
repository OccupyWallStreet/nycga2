<?php
/**
* Front-end - These are the core functions for loading scripts, creating template tag, etc
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info These are the 'public' functions which produce the gallery in the browser
* @info Loads header scripts
* @info Defines template tag
* @info Various helper functions used by the gallery constructor functions
*
* @since 3.0
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', DFCG_DOMAIN) );
}


/**
* Template tag to display gallery in theme files
*
* Do not use in the Loop.
*
* Note: DCG Widget can be used instead.
*
* @uses	dynamic-gallery.php
* @global array $dfcg_options Plugin options from db
* @since 2.1
*/
function dynamic_content_gallery() {
	global $dfcg_options;
	include_once( DFCG_DIR . '/dynamic-gallery.php' );
}


/***** Functions to display gallery ******************** */

/**
* Function to determine which pages get the MOOTOOLS scripts/css or JQUERY css loaded into wp_head.
*
* Hooked to 'wp_head' action
*
* Settings options are homepage, a page template or other.
* Settings "other" loads scripts into every page.
*
* @uses	dfcg_mootools_scripts()
* @uses dfcg_jquery_css()
*
* @global array $dfcg_options Plugin options from db
* @since 3.3
*/
function dfcg_load_scripts_header() {
	
	global $dfcg_options;
	
	if( $dfcg_options['limit-scripts'] == 'homepage' && ( is_home() || is_front_page() ) ) {
    	
    	if( $dfcg_options['scripts'] == 'mootools' ) {
			dfcg_mootools_scripts($dfcg_options);
    	} else {
			dfcg_jquery_css($dfcg_options);
		}
    
    } elseif( $dfcg_options['limit-scripts'] == 'pagetemplate' ) {
	
		$dfcg_page_filenames = $dfcg_options['page-filename'];
	
		// Turn list into an array
		$dfcg_page_filenames = explode(",", $dfcg_page_filenames);
	
		foreach ( $dfcg_page_filenames as $key) {
			if( is_page_template($key) ) {
				
				// Mootools or Jquery?
				if( $dfcg_options['scripts'] == 'mootools' ) {
					dfcg_mootools_scripts($dfcg_options);
				} else {
					dfcg_jquery_css($dfcg_options);
				}
    		}
    	}
		
	} elseif( $dfcg_options['limit-scripts'] == 'page' ) {
	
		$page_ids = $dfcg_options['page-ids'];
		
		// Turn list into array
		$page_ids = explode(",", $page_ids);
		
		foreach ( $page_ids as $key ) {
			if( is_page($key) ) {
			
				// Mootools or Jquery?
				if( $dfcg_options['scripts'] == 'mootools' ) {
					dfcg_mootools_scripts($dfcg_options);
				} else {
					dfcg_jquery_css($dfcg_options);
				}
			}
		}
		
    } elseif( $dfcg_options['limit-scripts'] == 'other' ) {
		
		if( $dfcg_options['scripts'] == 'mootools' ) {
	 		dfcg_mootools_scripts($dfcg_options);
		} else {		
			dfcg_jquery_css($dfcg_options);
		}
	}
}


/**
* Function to determine which pages get the JQUERY scripts loaded into wp_footer.
*
* Hooked to 'wp_footer' action
*
* Settings options are homepage, a page template or other.
* Settings "other" loads scripts into every page.
*
* @uses dfcg_jquery_smooth_scripts()
*
* @global array $dfcg_options Plugin options from db
* @since 3.3
*/
function dfcg_load_scripts_footer() {
	
	global $dfcg_options;
	
	if( $dfcg_options['scripts'] == 'jquery' ) {
	
		if( $dfcg_options['limit-scripts'] == 'homepage' && ( is_home() || is_front_page() ) ) {
    	
			dfcg_jquery_smooth_scripts($dfcg_options);

    
    	} elseif( $dfcg_options['limit-scripts'] == 'pagetemplate' ) {
	
			$dfcg_page_filenames = $dfcg_options['page-filename'];
	
			// Turn list into an array
			$dfcg_page_filenames = explode(",", $dfcg_page_filenames);
	
			foreach ( $dfcg_page_filenames as $key) {
				if( is_page_template($key) ) {
					dfcg_jquery_smooth_scripts($dfcg_options);
				}
    		}

		
		} elseif( $dfcg_options['limit-scripts'] == 'page' ) {
	
			$page_ids = $dfcg_options['page-ids'];
		
			// Turn list into array
			$page_ids = explode(",", $page_ids);
		
			foreach ( $page_ids as $key ) {
				if( is_page($key) ) {
					dfcg_jquery_smooth_scripts($dfcg_options);
				}
			}
		
    	} elseif( $dfcg_options['limit-scripts'] == 'other' ) {
		
			dfcg_jquery_smooth_scripts($dfcg_options);
		}
	}
}


/**
* Enqueue jQuery in header if JQUERY Scripts are used
*
* Adds jQuery framework to header using template_redirect hook
* Named dfcg_enqueue_scripts() prior to v3.3
*
* @uses wp_enqueue_script()
*
* @global array $dfcg_options Plugin options from db
* @since 3.2.2
* @updated 3.3
*/
function dfcg_enqueue_jquery() {

	global $dfcg_options;
	
	if( $dfcg_options['scripts'] == 'jquery' && !is_admin() ) {
	
		if( $dfcg_options['limit-scripts'] == 'homepage' && ( is_home() || is_front_page() ) ) {
    		
    		// Pull in jQuery
			wp_enqueue_script('jquery');
    	
		} elseif( $dfcg_options['limit-scripts'] == 'pagetemplate' ) {
	
			$dfcg_page_filenames = $dfcg_options['page-filename'];
	
			// Turn list into an array
			$dfcg_page_filenames = explode(",", $dfcg_page_filenames);
	
			foreach ( $dfcg_page_filenames as $key) {
				if( is_page_template($key) ) {
				
					// Pull in jQuery
					wp_enqueue_script('jquery');
    			}
    		}
		
		} elseif( $dfcg_options['limit-scripts'] == 'page' ) {
	
			$page_ids = $dfcg_options['page-ids'];
			
			// Turn list into array
			$page_ids = explode(",", $page_ids);
			
			foreach ( $page_ids as $key ) {
				if( is_page($key) ) {
					
					// Pull in jQuery
				wp_enqueue_script('jquery');
				}
			}
		
    	} elseif( $dfcg_options['limit-scripts'] == 'other' ) {
			
			// Pull in jQuery
			wp_enqueue_script('jquery');
		}
	}
}


/**
* Function to display MOOTOOLS header scripts and css
*
* Called by dfcg_load_scripts_header() which is hooked to wp_head action.
* Loads scripts and CSS into head
*
* @uses includes dfcg-user-styles.php
*
* @param array $dfcg_options, Plugin options from db
* @since 1.0
* @updated 3.3.2
*/
function dfcg_mootools_scripts($dfcg_options) {
    
	// Add CSS file
	echo "\n" . '<!-- Dynamic Content Gallery plugin version ' . DFCG_VER . ' www.studiograsshopper.ch  Begin scripts -->' . "\n";
	echo '<link type="text/css" rel="stylesheet" href="' . DFCG_URL . '/js-mootools/css/jd.gallery.css" />' . "\n";
	
	// Should mootools framework be loaded?
	if ( $dfcg_options['mootools'] !== '1' ) {
		echo '<script type="text/javascript" src="' . DFCG_URL . '/js-mootools/scripts/mootools-1.2.4-core-jm.js"></script>' . "\n";
		echo '<script type="text/javascript" src="' . DFCG_URL . '/js-mootools/scripts/mootools-1.2.4.4-more.js"></script>' . "\n";
	}
	
	// Add gallery javascript files
	echo '<script type="text/javascript" src="' . DFCG_URL . '/js-mootools/scripts/jd.gallery_1_2_4_4.js"></script>' . "\n";
	echo '<script type="text/javascript" src="' . DFCG_URL . '/js-mootools/scripts/jd.gallery.transitions_1_2_4_4.js"></script>' . "\n";
	
	// Add JS function call to gallery
	echo '<script type="text/javascript">
   function startGallery() {
      var myGallery = new gallery($("myGallery"), {
	  showArrows: '. $dfcg_options['showArrows'] .',
	  showCarousel: '. $dfcg_options['showCarousel'] .',
	  showInfopane: '. $dfcg_options['showInfopane'] .',
	  timed: '. $dfcg_options['timed'] .',
	  delay: '. $dfcg_options['delay'] .',
	  defaultTransition: "'. $dfcg_options['defaultTransition'] .'",
	  slideInfoZoneOpacity: '. $dfcg_options['slideInfoZoneOpacity'] .',
	  slideInfoZoneSlide: '. $dfcg_options['slideInfoZoneSlide'] .',
	  textShowCarousel: "'. $dfcg_options['textShowCarousel'] .'"
      });
   }
   window.addEvent("domready",startGallery);
</script>' . "\n";
	
	// Add user defined CSS
	include_once( DFCG_DIR . '/includes/dfcg-gallery-mootools-styles.php');
	
	// End of scripts
	echo '<!-- End of Dynamic Content Gallery scripts -->' . "\n\n";
}


/**
* Function to load JQUERY css
*
* Called by dfcg_load_scripts_header() which is hooked to wp_head action.
* Loads CSS into head
*
* @uses includes dfcg-gallery-jquery-smooth-styles.php
*
* @param array $dfcg_options, Plugin options from db
* @since 3.3
*/
function dfcg_jquery_css($dfcg_options) {
	
    // Add javascript and CSS files
	echo "\n" . '<!-- Dynamic Content Gallery plugin version ' . DFCG_VER . ' www.studiograsshopper.ch  Begin jQuery smoothSlideshow scripts -->';
	
	// Add user-defined CSS set in Settings page
	include_once( DFCG_DIR .'/includes/dfcg-gallery-jquery-smooth-styles.php');
	
	echo '<!-- End of Dynamic Content Gallery plugin scripts -->' . "\n";
}


/**
* Function to load JQUERY scripts
*
* Called by dfcg_load_scripts_footer() which is hooked to wp_footer action.
* Loads scripts into footer
*
* @param array $dfcg_options, Plugin options from db
* @since 3.3
* @updated 3.3.4
*/
function dfcg_jquery_smooth_scripts($dfcg_options) {
	
	if( $dfcg_options['scripts'] == 'jquery' ) {
		echo "\n" . '<!-- Dynamic Content Gallery plugin version ' . DFCG_VER . ' www.studiograsshopper.ch  Add jQuery smoothSlideshow scripts -->' . "\n";
		echo '<script type="text/javascript" src="' . DFCG_URL . '/js-jquery-smooth/scripts/dfcg-jq-script.min.js"></script>' . "\n";
		echo '<script type="text/javascript">
			jQuery("#dfcg-slideshow").smoothSlideshow("#dfcg-wrapper", {
				showArrows: '. $dfcg_options['showArrows'] .',
				showCarousel: '. $dfcg_options['showCarousel'] .',
				showInfopane: '. $dfcg_options['showInfopane'] .',
				timed: '. $dfcg_options['timed'] .',
				delay: '. $dfcg_options['delay'] .',
				thumbScrollSpeed:4,
				preloader: true,
				preloaderImage: true,
				preloaderErrorImage: true,
				elementSelector: "li",
				imgContainer:"#dfcg-image",
				imgPrevBtn:"#dfcg-imgprev",
				imgNextBtn:"#dfcg-imgnext",
				imgLinkBtn:"#dfcg-imglink",
				titleSelector: "h3",
				subtitleSelector: "p",
				linkSelector: "a",
				imageSelector: "img.full",
				thumbnailSelector: "a img",
				carouselContainerSelector: "#dfcg-thumbnails",
				thumbnailContainerSelector: "#dfcg-slider",
				thumbnailInfoSelector: "#dfcg-sliderInfo",
				carouselSlideDownSelector: "#dfcg-openGallery",
				carouselSlideDownSpeed: 500,
				infoContainerSelector:"#dfcg-text",
				borderActive:"#fff",
				slideInfoZoneOpacity: '. $dfcg_options['slideInfoZoneOpacity'] .',
				carouselOpacity: 0.3,
				thumbSpacing: 5,
				slideInfoZoneStatic: '. $dfcg_options['slideInfoZoneStatic'] .'
			});
		</script>';
		echo "\n" . '<!-- End of Dynamic Content Gallery plugin scripts -->' . "\n";
	}
}


/**
* Function to determine base URL of custom field images
*
* If FULL or AUTO, baseimgurl is empty; if PARTIAL, baseimgurl is pulled from options
*
* @global array $dfcg_options Plugin options from db
* @return string $output Either the base URL (PARTIAL) or empty (FULL)
* @since 3.0
* @updated 3.3
*/
function dfcg_baseimgurl() {

	global $dfcg_options;
	
	if ( $dfcg_options['image-url-type'] == "full" || $dfcg_options['image-url-type'] == "auto" ) {
		$output = '';
		
	} else {
		$output = $dfcg_options['imageurl'];
	}
	return $output;
}


/**
* Function to build an array of cat/off pairs from Multi Option Image Slot Settings
*
* Gets cat01 to cat10 and off01 to off10 from $dfcg_options array, skips empty image slots,
* and builds an array for use in WP_Query in Multi-Option constructors.
*
* Used by all js script framework constructors
*
* @global array $dfcg_options Plugin options from db
* @return array $query_list	Array of cat/off pairs
* @since 3.2
*/
function dfcg_query_list() {

	global $dfcg_options;

	// Set up variable to convert Slot to real Offset
	$offset = 1;

	$query_list = array();

	// Loop through the 9 possible cats/post selects
	for( $i=1; $i < 10; $i+=1 ) {
	
		// Set temp variables for catXX and offXX
		$tmpcat = 'cat0'.$i;
		$tmpoff = 'off0'.$i;
	
		// Get Settings
		$tmpcats = $dfcg_options[$tmpcat];
		$tmpoffs = $dfcg_options[$tmpoff];
	
		// If Post Select is empty, skip
		if( empty($tmpoffs) ) continue;
	
		// Convert Post Select to real Offset
		$tmpoffs = $tmpoffs-$offset;
	
		// Create temp assoc array $key=>$value pair
		$tmp_query_list[$tmpcats] = $tmpoffs;
	
		// Add this array to final array
		array_push($query_list, $tmp_query_list);
	
		// Empty temp array ready for next loop
		unset($tmp_query_list);
	}
	return $query_list;
}


/**
* Function to grab the first image attachment from Posts
*
* Used by gallery constructor functions
*
* Uses Studiopress Genesis functions as helpers (credit: Nathan Rice, Brian Gardner)
*
* @uses dfcg_get_image_id(), wp_get_attachment_image_src()
*
* @return string $output src of first image attachment
* @since 3.3
*/
function dfcg_grab_post_image($parent_id) {
	$args = array(
			'size' => 'full',
			'attr' => 'full',
			'num' => 0
			);
	// Get the image attachment ID
	$id = dfcg_get_image_id($parent_id, $args['num']);
	// Get the image details (returns array of src, width, height)
	$image = wp_get_attachment_image_src($id, $args['size']);
	// We only want the src
	$output = $image[0];
	return $output;
}

/**
 * Pulls an attachment ID from a post, if one exists
 *
 * Used by dfcg_grab_post_image($parent_id)
 *
 * Based on Studiopress Genesis functions as helpers (credit: Nathan Rice, Brian Gardner)
 *
 * @param string $parent_id ID of post/page
 * @param int $num used to specify that we are grabbing the first attachment ID
 * @return array $image_ids[$num] the ID of the first image attachment, or false if no image attachments
 * @since 3.3
 */
function dfcg_get_image_id($parent_id, $num = 0) {
	
	$image_ids = array_keys(
		get_children(
			array(
				'post_parent' => $parent_id,
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'orderby' => 'menu_order',
				'order' => 'ASC'
			)
		)
	);

	if ( isset($image_ids[$num]) )
		return $image_ids[$num];

	return false;
}


/**
* Function to populate the $postmeta array with correct postmeta key names
*
* Used by gallery constructor functions
*
* @global array $dfcg_postmeta_upgrade Plugin options from db
*
* @return array $postmeta
* @since 3.3
*/
function dfcg_postmeta_info() {
	global $dfcg_postmeta_upgrade;

	if( $dfcg_postmeta_upgrade['upgraded'] == 'completed' ) {
		$postmeta['desc'] = '_dfcg-desc';
		$postmeta['image'] = '_dfcg-image';
		$postmeta['exclude'] = '_dfcg-exclude';
		$postmeta['link'] = '_dfcg-link';
		$postmeta['link-window'] = '_dfcg-link-window';
	} else {
		$postmeta['desc'] = 'dfcg-desc';
		$postmeta['image'] = 'dfcg-image';
		$postmeta['exclude'] = 'dfcg-exclude';
		$postmeta['link'] = 'dfcg-link';
	}
	return $postmeta;
}


/**
* Function to get the thumbnail for carousel
*
* Used by gallery constructor functions
*
* Uses WP get_the_post_thumbnail() function
*
* @global array $dfcg_postmeta_upgrade Plugin options from db
*
* @return $thumb_html HTML markup for thumbnail
* @since 3.3
**/
function dfcg_get_thumbnail($id, $image_src, $title) {
	global $dfcg_options;
	
	// Get the thumbnail - uses Post Thumbnails if AUTO images are used
	if( current_theme_supports('post-thumbnails') && $dfcg_options['thumb-type'] == "post-thumbnails" ) {
		
		$args = array(
			"class" => "dfcg-postthumb-auto thumbnail",
			"alt" => esc_attr($title),
			);
		
		$thumb = get_the_post_thumbnail( $id, array(100,100), $args );
		
		if( $thumb ) {
			$thumb_html = $thumb;
		} else {
			// A Post Thumbnail has not been set for this post
			$thumb_html = '<img class="dfcg-postthumb-notset thumbnail" src="'. $image_src . '" alt="'. esc_attr($title) .'" />';
		}
		
	} else {
		// Legacy thumbnails, therefore just use $image_src, no resizing etc
		$thumb_html = '<img class="dfcg-thumb-legacy thumbnail" src="'. $image_src . '" alt="'. esc_attr($title) .'" />';
	}
	
	return $thumb_html;
}