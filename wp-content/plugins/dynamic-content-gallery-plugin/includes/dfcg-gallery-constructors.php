<?php
/**
* Front-end - These are the constructor functions which produce the XHTML markup when using Mootools
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info One function for each of the 4 populate-methods.
*		- Multi Option		dfcg_multioption_method_gallery()
*		- One Category		dfcg_onecategory_method_gallery()
*		- Custom Post Type	dfcg_onecategory_method_gallery()
*		- Pages				dfcg_id_method_gallery()
*
* @since 3.3
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', DFCG_DOMAIN) );
}



/**
* This function builds the gallery from Multi Option options
*
* @uses dfcg_postmeta_info()		Builds array of postmeta key names (see dfcg-gallery-core.php)
* @uses	dfcg_errors_output()		Gets all Error Messages, if errors are on (see dfcg-gallery-errors.php)
* @uses	dfcg_baseimgurl()			Determines whether FULL or Partial URL applies (see dfcg-gallery-core.php)
* @uses	dfcg_query_list()			Builds array of cat/off pairs for WP_Query (see dfcg-gallery-core.php)
* @uses dfcg_the_content_limit()	Creates Auto description (see dfcg-gallery-content-limit.php)
* @uses dfcg_grab_post_image()		Gets the first image attachment from the Post (see dfcg-gallery-core.php)
*
* @var array	$postmeta				Array of postmeta keys, eg _dfcg-image, etc. Output of dfcg_postmeta_info()
* @var array	$dfcg_errmsgs			Array of error messages. Output of dfcg_errors_output()
* @var string 	$baseimgurl				Base URL for images. Empty if FULL URL. Output of dfcg_baseurl()
* @var string 	$def_img_folder_url		Holds DCG Option: 'defimgmulti', URL to default images folder
* @var string 	$def_img_folder_path	Absolute path to default images directory
* @var array 	$query_list				Array of cat/off pairs. Output of dfcg_query_list()
* @var string	$selected_slots			Number of pairs in $query_list array
* @var string	$counter				Stores how many times $query_list is run through foreach loop
* @var string 	$counter1				Stores how many times WP_Query is run (to do comparison for missing posts)
* @var string 	$counter2				Added 3.2: Stores how many posts are Excluded by _dfcg-exclude custom field being true
* @var string 	$slide_text				Slide Pane description text
* @var string	$link					Image link URL, either to Post/Page or External
* @var string 	$auto_image				First image attachment in the Post, as URL
* @var string	$image_src				SRC of gallery image
* @var string	$image_err				Error message, if relevant
* @var string 	$def_img_path			Stores absolute path, incl filename, of category default image for file_exists() check
* @var string	$thumb					Stores output of get_the_post_thumbnail() function, for accessing Post Thumbnails/Featured Image
* @var string	$thumb_html				Stores HTML of thumbnail IMG
*
* @global array $dfcg_options Plugin options array from db
* @global array $post Post object
*
* @since 3.2
* @updated 3.3
*/
function dfcg_multioption_method_gallery() {

	global $dfcg_options, $post;
	
	$postmeta = dfcg_postmeta_info();
	
	// Build array of error messages (NULL if Errors are off)
	$dfcg_errmsgs = NULL;
	if( function_exists( 'dfcg_errors_output' ) ) {
		$dfcg_errmsgs = dfcg_errors_output();
	}
	
	// Set $baseimgurl variable for image URL
	$baseimgurl = dfcg_baseimgurl();

	// Get the absolute URL to the default "Category" images folder from Settings
	$def_img_folder_url = $dfcg_options['defimgmulti'];

	// Convert URL to path. Strip domain name from URL, replace with ABSPATH. Default folder can now be anywhere
	$def_img_folder_path = str_replace( get_bloginfo('url'), ABSPATH, $def_img_folder_url );
	
	$query_list = dfcg_query_list();

	/* Collect some info about our array, for later */
	$selected_slots = count($query_list);
	
	/* Validate that $query_list has at least 2 items for gallery to work */
	if( $selected_slots < 2 ) {
		$output = $dfcg_errmsgs['11'] . "\n";
		echo $output;
		return;
	}

	// Validation of output - not much needs to be done. 
	// Clicking Save in Settings will automatically assign a valid cat to each image slot
	// because wp_dropdown_categories is set to "hide empty".
	// Any empty post selects will be ignored, as per foreach loop below,
	// therefore the only risk is that a post select is entered for a post
	// which doesn't exist. Eg, post #4, but there are only 3 in that cat.
	// This situation is dealt with by the counters...
	// We also validate that there are at least 2 post selects (see above)
	 
	// Set 3 counters to find out how many Posts are supposed to be output
	// by WP_Query, and how many posts are actually found by WP_Query, and how many posts were Excluded
	// $counter:	Adds an image # in the markup page source
	//				Counts how many times we go through $query_list foreach loop
	//				This is pre-WP_Query
	// $counter1:	Counts how many times WP_Query outputs anything
	// $counter2:	Counts how many Excluded Posts are found
	//				We can then compare the three values to see if anything is missing
	$counter = 0;
	$counter1 = 0;
	$counter2 = 0;

	
	// Start the Gallery Markup
	$output = "\n" . '<div id="myGallery"><!-- Start of Dynamic Content Gallery -->';

	
	/* Now loop through our array of all the cat/post selects and run the WP_Queries */
	foreach ($query_list as $value) {
	
		// Go down into inner arrays which contain the cat/offset pairs
		if( is_array($value) ) {
		
			// Increment the counter
			$counter++;
		
			// Loop through the inner array (this only happens once before passing back to the outer foreach loop
			foreach ($value as $key => $value1) {
					
				// Now run the query using $key for cat and $value for offset
				$recent = new WP_Query("cat=$key&showposts=1&offset=$value1");
				
				// Do we have any posts? If this is the first loop and no post is found, we need to abort
				// because the gallery won't display. Although this check is performed on every loop, we
				// don't need to abort after Image slot #1 is tested.
				if( !$recent->have_posts() && $counter < 2 ) :
					$output .= "\n" . $dfcg_errmsgs['13'];
					$output .= "\n" . '</div><!-- End of Dynamic Content Gallery output -->' . "\n";
					echo $output;
					return;
				
				else :
					while($recent->have_posts()) : $recent->the_post();
				
					// Exclude the post if _dfcg-exclude custom field is true
					if( get_post_meta($post->ID, $postmeta['exclude'], true) == 'true' ) {
						$output .= "\n\n" . '<!-- DCG Image #' . $counter . ' has been Excluded by user -->';
						$counter2++;
						continue;
					}
						
					// Increment the second counter
					$counter1++;
					
					// Open the imageElement div
					$output .= "\n\n" . '<div class="imageElement"><!-- DCG Image #' . $counter . ' -->';

					// Display the page title
					$output .= "\n\t" . '<h3>' . get_the_title() . '</h3>';

					// Get the slide pane description
					if( $dfcg_options['desc-method'] == 'none' ) {
						// we don't want any descriptions (note: smoothgallery needs <p> tags or won't work)
						$slide_text = '<p></p>';
					
					} elseif( $dfcg_options['desc-method'] == 'manual' ) {
						
						if( get_post_meta($post->ID, $postmeta['desc'], true) ){
							// We have a Custom field description
							$slide_text = '<p>' . get_post_meta($post->ID, $postmeta['desc'], true) . '</p>';

						} elseif( category_description($key) !== '' ) {
							// show the category description (note: no <p> tags required)
							$slide_text = category_description($key);

						} elseif( $dfcg_options['defimagedesc'] !== '' ) {
							// or show the default description
							$slide_text = '<p>' . stripslashes( $dfcg_options['defimagedesc'] ) . '</p>';
							
						} else {
							// Fall back to Auto custom excerpt
							$slide_text = dfcg_the_content_limit( $dfcg_options['max-char'], $dfcg_options['more-text'] );
						}
						
					} else {
						// We're using Auto custom excerpt
						$slide_text = dfcg_the_content_limit( $dfcg_options['max-char'], $dfcg_options['more-text'] );
					}
					
					// Output slide pane description
					$output .= "\n\t" . $slide_text;

       				// Get Image Link - based on code courtesy of Martin Downer
					if( get_post_meta($post->ID, $postmeta['link'], true) ){
						// We have an external/manual link
						$link = get_post_meta($post->ID, $postmeta['link'], true);
					} else {
						$link = get_permalink();
					}
					
					// Output Image Link
					$output .= "\n\t" . '<a href="'. $link .'" title="Read More" class="open"></a>';

					// Get the Image
					if( $dfcg_options['image-url-type'] == "auto" ) {
				
						$auto_image = dfcg_grab_post_image($post->ID);
				
						if( $auto_image ) {
							$image_src = $auto_image;
							$image_class = "dfcg-auto";
							$image_err = $dfcg_errmsgs['19'];
							// Note: No additional Error message will be shown if the attachment has been physically removed/moved by FTP for example, ie 404.

							
						} elseif( get_post_meta($post->ID, $postmeta['image'], true) ) {
							$image_src = $baseimgurl . get_post_meta($post->ID, $postmeta['image'], true);
							$image_class = "dfcg-auto-metabox";
       						$image_err = $dfcg_errmsgs['14'];
       						// Note: No Error message will be triggered if Metabox image is set but URL is wrong, ie 404.
							
						} else {
							// Path to Default Category image
							$def_img_path = $def_img_folder_path . $key . '.jpg';
							if( file_exists($def_img_path) ) {
								$image_src = $def_img_folder_url . $key . '.jpg';
								$image_class = "dfcg-auto-default";
								$image_err = $dfcg_errmsgs['15'];
								
							} else {
								$image_src = DFCG_ERRORIMGURL;
								$image_class = "dfcg-auto-error";
								$image_err = $dfcg_errmsgs['16'];
							}
						}
				
					} else {
						
						// or, Get the Metabox image
						$metabox_image = get_post_meta($post->ID, $postmeta['image'], true);
							
						if( $metabox_image ) {
							$image_src = $baseimgurl . $metabox_image;
							$image_class = "dfcg-metabox";
							$image_err = $dfcg_errmsgs['20'];
							// Note: No Error message will be triggered if Metabox image is set but URL is wrong, ie image gives 404
							
						} else {
							// Path to Default Category image
							$def_img_path = $def_img_folder_path . $key . '.jpg';
							if( file_exists($def_img_path) ) {
								$image_src = $def_img_folder_url . $key . '.jpg';
								$image_class = "dfcg-metabox-default";
								$image_err = $dfcg_errmsgs['17'];
							} else {
								$image_src = DFCG_ERRORIMGURL;
								$image_class = "dfcg-metabox-error";
        						$image_err = $dfcg_errmsgs['18'];
							}
						}
					}
					
					// Get the thumbnail - uses Post Thumbnails if AUTO images are used
					$thumb_html = dfcg_get_thumbnail($post->ID, $image_src, $post->post_title);
					
					// Output image and thumbnail
					$output .= "\n\t" . '<img src="'. $image_src . '" alt="'. get_the_title() .'" class="' . $image_class . ' full" />';
					$output .= "\n\t" . $thumb_html;
					$output .= $image_err;
					
					// Close ImageElement div
					$output .= "\n" . '</div>';

					endwhile; 

				endif; 	// End WP_Query if($recent... ) test
			} 			// End inner foreach loop
		} 				// End conditional check that $value is an array
	} 					// End outer foreach loop
			
	// Compare the 3 counters to see if outputs were as expected.
	// $counter = number of Post Selects in Settings. Also sets the "Image #" comment in Page Source.
	// $counter1 = number of WP_Query outputs.
	// $counter2 = number of excluded posts.
	// If these values are not the same, WP_Query couldn't find a Post. 
	if( $counter - $counter1 - $counter2 !== 0 ) {
		$output .= "\n\n" . $dfcg_errmsgs['12'];
		if( $dfcg_options['errors'] == "true" ) {
			$output .= "\n" . '<!-- ' . __('Number of Posts to display as per DCG Settings = ', DFCG_DOMAIN) . $counter . ' -->';
			$output .= "\n" . '<!-- ' . __('Number of Posts found = ', DFCG_DOMAIN) . $counter1 . ' -->';
			$output .= "\n" . '<!-- ' . __('Number of Posts excluded by user = ', DFCG_DOMAIN) . $counter2 . ' -->';
		}
	}
	
	// End of the gallery markup
	$output .= "\n\n" . '</div><!-- End of Dynamic Content Gallery output -->' . "\n\n";
	
	// Output the Gallery
	echo $output;
}


/**
* This function builds the gallery from One Category options
*
* @uses dfcg_postmeta_info()		Builds array of postmeta key names (see dfcg-gallery-core.php)
* @uses	dfcg_errors_output()		Gets all Error Messages, if errors are on (see dfcg-gallery-errors.php)
* @uses	dfcg_baseimgurl()			Determines whether FULL or Partial URL applies (see dfcg-gallery-core.php)
* @uses	dfcg_query_list()			Builds array of cat/off pairs for WP_Query (see dfcg-gallery-core.php)
* @uses dfcg_the_content_limit()	Creates Auto description (see dfcg-gallery-content-limit.php)
* @uses dfcg_grab_post_image()		Gets the first image attachment from the Post (see dfcg-gallery-core.php)
*
* @var array	$postmeta				Array of postmeta keys, eg _dfcg-image, etc. Output of dfcg_postmeta_info()
* @var array	$dfcg_errmsgs			Array of error messages. Output of dfcg_errors_output()
* @var string 	$baseimgurl				Base URL for images. Empty if FULL URL. Output of dfcg_baseurl()
* @var string 	$posts_number			DCG option: number of posts to display
* @var string 	$term_selected			DCG option: selected category or taxonomy term
* @var string 	$def_img_folder_url		DCG option: URL to default images folder
* @var string 	$def_img_folder_path	Absolute path to default images directory
* @var string	$def_img_name			Default image filename
* @var string	$def_img_path			Stores absolute path, incl filename, of category default image
* @var object	$recent					WP_Query object
* @var string	$counter				Stores how many times items in $recent wp_query loop
* @var string	$counter2				Added 3.2: Stores how many posts are Excluded by _dfcg-exclude custom field being true
* @var string 	$slide_text				Slide Pane description text
* @var string	$link					Image link URL, either to Post/Page or External
* @var string 	$auto_image				First image attachment in the Post, as URL
* @var string	$image_src				SRC of gallery image
* @var string	$image_err				Error message, if relevant
* @var string	$thumb					Stores output of get_the_post_thumbnail() function, for accessing Post Thumbnails/Featured Image
* @var string	$thumb_html				Stores HTML of thumbnail IMG
*
* @global array $dfcg_options Plugin options array from db
* @global array $post Post object
*
* @since 3.2
* @updated 3.3
*/
function dfcg_onecategory_method_gallery() {

	global $post, $dfcg_options;
	
	$postmeta = dfcg_postmeta_info();
	
	// Build array of error messages (NULL if Errors are off). Reset to NULL, just in case Settings have been changed
	$dfcg_errmsgs = NULL;
	if( function_exists( 'dfcg_errors_output' ) ) {
		$dfcg_errmsgs = dfcg_errors_output();
	}
	
	// Set $baseimgurl variable for image URL
	$baseimgurl = dfcg_baseimgurl();
	
	if( $dfcg_options['populate-method'] == 'one-category' ) {
		/* Get the number of Posts to display */
		// No need to check that there is a minimum of 2 posts, thanks to dropdown in Settings
		$posts_number = $dfcg_options['posts-number'];
		
		/* Get the Selected Category/Term */
		// No need to check Category existence, or whether it has Posts,
		// thanks to use of wp_dropdown_categories in Settings
		// With One Category Method, this is the cat ID
		$term_selected = $dfcg_options['cat-display'];
		
		/* Get the URL to the default "Category" images folder from Settings */
		$def_img_folder_url = $dfcg_options['defimgonecat'];
		
		/* The query */
		$query = 'cat=' . $term_selected . '&showposts=' . $posts_number;
	}

	if( $dfcg_options['populate-method'] == 'custom-post' ) {
		/* Get the Custom Post Type */
		$post_type = $dfcg_options['custom-post-type'];
		
		/* Get the number of Posts to display */
		// No need to check that there is a minimum of 2 posts, thanks to dropdown in Settings
		$posts_number = $dfcg_options['custom-post-type-number'];
		
		/* Get the Selected Category/Term */
		// In format "taxonomy_name=term_Name" eg ade_products=Guitars
		$term_selected = $dfcg_options['custom-post-type-tax'];
		
		/* Get the URL to the default "Category" images folder from Settings */
		$def_img_folder_url = $dfcg_options['defimgcustompost'];
		
		/* The query */
		$query = 'post_type=' . $post_type . '&' . $term_selected . '&showposts=' . $posts_number;
	}
	

	// Convert URL to path. Strip domain name from URL, replace with ABSPATH. Default folder can now be anywhere
	$def_img_folder_path = str_replace( get_bloginfo('url'), ABSPATH, $def_img_folder_url );
	
	// Set a variable for the category default image using the cat ID number for the image name
	if( $term_selected !== '' ) {
		$def_img_name = $term_selected .'.jpg';
	} else {
		$def_img_name = 'all.jpg';
	}
	
	// Absolute path to default image, needed for file_exists() check
	$def_img_path = $def_img_folder_path . $def_img_name;
	
	/* Do the WP_Query */
	$recent = new WP_Query( $query );
	// Do we have any posts?
	if ( $recent->have_posts() ) {

		// Set a counter to find out how many Posts are found in the WP_Query
		// Also used to add an image # in the markup page source
		$counter = 0;
		$counter2 = 0;

		// Start the gallery markup
		$output = "\n" . '<div id="myGallery"><!-- Start of Dynamic Content Gallery output -->';

		while($recent->have_posts()) : $recent->the_post();

			// Increment the counter
			$counter++;
			
			// Exclude the post if _dfcg-exclude custom field is true
			if( get_post_meta($post->ID, $postmeta['exclude'], true) == 'true' ) {
				$output .= "\n\n" . '<!-- DCG Image #' . $counter . ' has been Excluded by user -->';
				$counter2++;
				continue;
			}

			// Open the imageElement div
			$output .= "\n\n" . '<div class="imageElement"><!-- DCG Image #' . $counter . ' -->';

			// Display the page title
			$output .= "\n\t" . '<h3>'. get_the_title() .'</h3>';
			
			// Get the description based on 'none', 'manual' or 'auto'
			if( $dfcg_options['desc-method'] == 'none' ) {
				// we don't want any descriptions (note: smoothgallery needs <p> tags or won't work)
				$slide_text = '<p></p>';
				
			} elseif( $dfcg_options['desc-method'] == 'manual' ) {
			
				// Do we have a DCG Metabox _dfcg-desc?
				if( get_post_meta($post->ID, $postmeta['desc'], true) ) {
					$slide_text = '<p>'. get_post_meta($post->ID, $postmeta['desc'], true) . '</p>';
			
				// we have All cats
				} elseif( $term_selected == '' ) {
				
					// TODO: Cat descriptions are not used with ALL cats. Get the category ID so that cat descriptions can be displayed for ALL cats
				
					// Default description exists
					if( $dfcg_options['defimagedesc'] !== '' ) {
						// Show the default description
						$slide_text = '<p>' . stripslashes( $dfcg_options['defimagedesc'] ) . '</p>';
				
					} else {
						// We're using Auto custom excerpt as fallback
						$slide_text = dfcg_the_content_limit( $dfcg_options['max-char'], $dfcg_options['more-text'] );
					}
				
				// we have Single cat and category desc exists
				} elseif( category_description($term_selected) !== '') {
					// a category description exists
					$slide_text = category_description($term_selected);
				
				// we have a Single cat and a default description exists
				} elseif( $dfcg_options['defimagedesc'] !== '') {
					// a default description exists
					$slide_text = '<p>' . stripslashes( $dfcg_options['defimagedesc'] ) . '</p>';
			
				// we have Single cat and no description
				} else {
					// We're using Auto custom excerpt as fallback
					$slide_text = dfcg_the_content_limit( $dfcg_options['max-char'], $dfcg_options['more-text'] );
				}
				
			} else {
				// We're using Auto custom excerpt
				$slide_text = dfcg_the_content_limit( $dfcg_options['max-char'], $dfcg_options['more-text'] );
			}
			
			// Output slide pane description
			$output .= "\n\t" . $slide_text;

			// Get Image Link - based on code courtesy of Martin Downer
			if( get_post_meta($post->ID, $postmeta['link'], true) ){
				// We have an external/manual link
				$link = get_post_meta($post->ID, $postmeta['link'], true);
			} else {
				$link = get_permalink();
			}
			
			// Output Image Link
			$output .= "\n\t" . '<a href="'. $link .'" title="Read More" class="open"></a>';

			// Get the Image
			if( $dfcg_options['image-url-type'] == "auto" ) {
				
				$auto_image = dfcg_grab_post_image($post->ID);
				
				if( $auto_image ) {
					$image_src = $auto_image;
					$image_class = "dfcg-auto";
					$image_err = $dfcg_errmsgs['19'];
					// Note: No additional Error message will be shown if the attachment has been physically removed/moved by FTP for example, ie 404.
				
				} elseif( get_post_meta($post->ID, $postmeta['image'], true) ) {
					// For backwards compatibility - see if a DCG Metabox Image URL exists
					$image_src = $baseimgurl . get_post_meta($post->ID, $postmeta['image'], true);
					$image_class = "dfcg-auto-metabox";
        			$image_err = $dfcg_errmsgs['14'];
				
				} elseif( file_exists($def_img_path) ) {
					// Display the "Category" default image
					$image_src = $def_img_folder_url . $def_img_name;
					$image_class = "dfcg-auto-default";
        			$image_err = $dfcg_errmsgs['15'];
				
				} else {
					$image_src = DFCG_ERRORIMGURL;
					$image_class = "dfcg-auto-error";
					$image_err = $dfcg_errmsgs['16'];
				}
				
			} else {
				
				// or, Get the Metabox image
				$metabox_image = get_post_meta($post->ID, $postmeta['image'], true);
				
				if( $metabox_image ) {
					$image_src = $baseimgurl . $metabox_image;
					$image_class = "dfcg-metabox";
        			$image_err = $dfcg_errmsgs['20'];
					// Note: No Error message will be triggered if _dfcg-image is set but URL is wrong, ie 404.
			
				} elseif( file_exists($def_img_path) ) {
					// Display the "Category" default image
					$image_src = $def_img_folder_url . $def_img_name;
					$image_class = "dfcg-metabox-default";
        			$image_err = $dfcg_errmsgs['17'];
			
				} else {
					$image_src = DFCG_ERRORIMGURL;
					$image_class = "dfcg-metabox-error";
					$image_err = $dfcg_errmsgs['18'];
				}
			}
			
			// Get the thumbnail - uses Post Thumbnails if AUTO images are used
			$thumb_html = dfcg_get_thumbnail($post->ID, $image_src, $post->post_title);
			
			// Output image and thumbnail
			$output .= "\n\t" . '<img src="'. $image_src . '" alt="'. get_the_title() .'" class="' . $image_class . ' full" />';
			$output .= "\n\t" . $thumb_html;
			$output .= $image_err;

			// Close the ImageElement div
			$output .= "\n" . '</div>';

		endwhile;

		/*	Compare number of Posts selected as per Settings ($posts_number) with the WP_Query object output ($counter)
			to check that the number of gallery images is the same.	If it's not the
			same, then there are less Posts in this Category than the posts-number Setting */

		if( $posts_number - $counter !== 0 ) {
			$output .= "\n\n" . $dfcg_errmsgs['7'];
		}
		
		// Print out stats
		if( $dfcg_options['errors'] == "true" ) {
			$post_found = $counter - $counter2;
			$output .= "\n" . '<!-- ' . __('Number of Posts to display as per DCG Settings = ', DFCG_DOMAIN) . $posts_number . ' -->';
			$output .= "\n" . '<!-- ' . __('Number of Posts found = ', DFCG_DOMAIN) . $post_found . ' -->';
			$output .= "\n" . '<!-- ' . __('Number of Posts excluded by user = ', DFCG_DOMAIN) . $counter2 . ' -->';
		}

		// End of the gallery markup
		$output .= "\n\n" . '</div><!-- End of Dynamic Content Gallery output -->'."\n\n";

	} else {
		/* Oops! The WP_Query couldn't find any Posts */
		// Theoretically this can never happen unless there is a WP problem
		$output = "\n" . $dfcg_errmsgs['8'];
	}
	
	// Output the Gallery
	echo $output;
}


/**
* This function builds the gallery from ID Method options
*
* NOTE: This function was renamed in v3.3 (formally dfcg_pages_method_gallery() )
*
* @uses dfcg_postmeta_info()		Builds array of postmeta key names (see dfcg-gallery-core.php)
* @uses	dfcg_errors_output()		Gets all Error Messages, if errors are on (see dfcg-gallery-errors.php)
* @uses	dfcg_baseimgurl()			Determines whether FULL or Partial URL applies (see dfcg-gallery-core.php)
* @uses	dfcg_query_list()			Builds array of cat/off pairs for WP_Query (see dfcg-gallery-core.php)
* @uses dfcg_the_content_limit()	Creates Auto description (see dfcg-gallery-content-limit.php)
* @uses dfcg_grab_post_image()		Gets the first image attachment from the Post (see dfcg-gallery-core.php)
*
* @var array	$postmeta				Array of postmeta keys, eg _dfcg-image, etc. Output of dfcg_postmeta_info()
* @var array	$dfcg_errmsgs			Array of error messages. Output of dfcg_errors_output()
* @var string 	$baseimgurl				Base URL for images. Empty if FULL URL. Output of dfcg_baseurl()
* @var string 	$ids_selected			DCG option: comma separated list of Page/Post IDs
* @var string	$ids_selected_count		No. of Page/Post IDs specified in DCG options
* @var array	$ids_found				$wpdb query object
* @var string	$ids_found_count		Number of Pages in $wpdb query object
* @var string	$counter				Incremented variable to add image # in HTML comments markup
* @var string 	$slide_text				Slide Pane description text
* @var string	$chars					Stores value of $dfcg_options['max-char'], used as param in dfcg_the_content_limit()
* @var string	$more					Stores value of $dfcg_options['more-text'], used as param in dfcg_the_content_limit()
* @var string	$id_content				Stores value of $id_found->post_content, used as param in dfcg_the_content_limit()
* @var string	$id_id					Stores value of $id_found->ID, used as param in dfcg_the_content_limit()
* @var string	$link					Image link URL, either to Post/Page or External
* @var string 	$auto_image				First image attachment in the Post, as URL
* @var string	$image_src				SRC of gallery image
* @var string	$image_err				Error message, if relevant
* @var string	$thumb					Stores output of get_the_post_thumbnail() function, for accessing Post Thumbnails/Featured Image
* @var string	$thumb_html				Stores HTML of thumbnail IMG
*
* @global array $dfcg_options Plugin options array from db
* @global array $wpdb WP $wpdb database object
*
* @since 3.2
* @updated 3.3
*/
function dfcg_id_method_gallery() {

	global $dfcg_options;
	
	$postmeta = dfcg_postmeta_info();
	
	// Build array of error messages (NULL if Errors are off)
	$dfcg_errmsgs = NULL;
	if( function_exists( 'dfcg_errors_output' ) ) {
		$dfcg_errmsgs = dfcg_errors_output();
	}
	
	// Set $baseimgurl variable for image URL
	$baseimgurl = dfcg_baseimgurl();
	
	/* Get the comma separated list of Page ID's */
	$ids_selected = trim($dfcg_options['ids-selected']);

	if( !empty($ids_selected) ) {

		/* Get rid of the final comma so that the variable is ready for use in SQL query */
		// If last character in string is a comma
		if( substr( $ids_selected, -1) == ',' ) {
			// Remove the final comma in the list
			$ids_selected = substr( $ids_selected, 0, substr( $ids_selected, -1)-1 );
		}

		/* Turn the list into an array */
		$ids_selected = explode(",", $ids_selected);
		/* Store how many IDs were in list */
		$ids_selected_count = count($ids_selected);

		/* If only one Page ID has been specified in Settings: print error messages and exit */
		if( $ids_selected_count < 2 ) {
			$output = $dfcg_errmsgs['1'] . "\n";
			echo $output;
			return;
		}

	} else {
		/* There are no Page IDs in Settings: print error messages and exit */
		$output = $dfcg_errmsgs['2'] . "\n";
		echo $output;
		return;
	}

	/* Instantiate the $wpdb object */
	global $wpdb;
	
	if( $dfcg_options['id-sort-control'] == 'true' ) {
	
		/* User defined sort order for Pages */
		$sort = esc_attr('_dfcg-sort');
	
		/* Do the query - with thanks to Austin Matzko for sprintf help */
		$ids_found = $wpdb->get_results(
  			sprintf("SELECT ID,post_title,post_content
				FROM $wpdb->posts
				LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = '%s'
				WHERE $wpdb->posts.ID IN( %s )
				ORDER BY $wpdb->postmeta.meta_value ASC", $sort, implode(',', array_map( 'intval', $ids_selected )) )
			);
	
	} else {
		
		/* Do the query - with thanks to Austin Matzko for sprintf help */
		/* Note: simplified query without custom sort ordering */
		$ids_found = $wpdb->get_results(
  			sprintf("SELECT ID,post_title,post_content FROM $wpdb->posts WHERE $wpdb->posts.ID IN( %s )", implode(',', array_map( 'intval', $ids_selected ) ) )
			);
	}
	
	/* If we have results from the query */
	if( $ids_found ) {

		// Validation: Check how many Pages the query found
		// The results if this are printed to Page Source further down
		$ids_found_count = count($ids_found);
	
		// If less than 2, print error messages and exit function
		if( $ids_found_count < 2 ) {
			$output = "\n" . $dfcg_errmsgs['9'];
			if( $dfcg_options['errors'] == "true" ) {
				$output .= "\n" . '<!-- ' . __('Number of Pages selected in DCG Settings = ', DFCG_DOMAIN) . $ids_selected_count . ' -->';
				$output .= "\n" . '<!-- ' . __('Number of Pages found = ', DFCG_DOMAIN) . $ids_found_count . ' -->';
			}
			echo $output;
			return;
		}

		// Set a counter to add an image # in the markup page source
		$counter = 0;

		// Start the gallery markup
		$output = "\n" . '<div id="myGallery"><!-- Start of Dynamic Content Gallery output -->';

		foreach( $ids_found as $id_found ) :

			// Increment the image counter
			$counter++;

			// Open the imageElement div
			$output .= "\n" . '<div class="imageElement"><!-- DCG Image #' . $counter . '-->';

			// Display the page title
			$output .= "\n\t" . '<h3>'. esc_attr($id_found->post_title) .'</h3>';

			// Get the slide pane description
			if( $dfcg_options['desc-method'] == 'none' ) {
				// we don't want any descriptions (note: smoothgallery needs <p> tags or won't work)
				$slide_text = '<p></p>';
			
			} elseif( $dfcg_options['desc-method'] == 'manual' ) {
			
				// Do we have a _dfcg-desc?
				if( get_post_meta($id_found->ID, $postmeta['desc'], true) ) {
					$slide_text = '<p>' . get_post_meta($id_found->ID, $postmeta['desc'], true) . '</p>';

				} elseif( $dfcg_options['defimagedesc'] !== '' ) {
					// Show the default description
					$slide_text = '<p>' . stripslashes( $dfcg_options['defimagedesc'] ) . '</p>';

				} else {
					// We're using Auto custom excerpt as fallback
					$slide_text = dfcg_the_content_limit( $dfcg_options['max-char'], $dfcg_options['more-text'], $id_found->post_content, $id_found->ID );
				}
				
			} else {
				// We're using Auto custom excerpt
				$slide_text = dfcg_the_content_limit( $dfcg_options['max-char'], $dfcg_options['more-text'], $id_found->post_content, $id_found->ID );
			}
			
			// Output slide pane description
			$output .= "\n\t" . $slide_text;

			// Get Image Link - based on code courtesy of Martin Downer
			if( get_post_meta($id_found->ID, $postmeta['link'], true) ){
				// We have an external/manual link
				$link = get_post_meta($id_found->ID, $postmeta['link'], true);
			} else {
				$link = get_permalink($id_found->ID);
			}
			
			// Output Image Link
			$output .= "\n\t" . '<a href="'. $link .'" title="Read More" class="open"></a>';

			// Get the Image
			if( $dfcg_options['image-url-type'] == "auto" ) {
				
				$auto_image = dfcg_grab_post_image($id_found->ID);
				
				if( $auto_image ) {
					$image_src = $auto_image;
					$image_class = "dfcg-auto";
					$image_err = $dfcg_errmsgs['19'];
					// Note: No additional Error message will be shown if the attachment has been physically removed/moved by FTP for example, ie 404.
				
				} elseif( get_post_meta($id_found->ID, $postmeta['image'], true) ) {
					// For backwards compatibility - see if a DCG Metabox Image URL exists
					$image_src = $baseimgurl . get_post_meta($id_found->ID, $postmeta['image'], true);
					$image_class = "dfcg-auto-metabox";
					$image_err = $dfcg_errmsgs['14'];
					// Note: No Error message will be triggered if _dfcg-image is set but URL is wrong, ie 404.
				
				} elseif( !empty($dfcg_options['defimgid']) ) {
					// Display the "ID" default image
					$image_src = $dfcg_options['defimgid'];
					$image_class = "dfcg-auto-default";
        			$image_err = $dfcg_errmsgs['15'];
				
				} else {
					$image_src = DFCG_ERRORIMGURL;
					$image_class = "dfcg-auto-error";
					$image_err = $dfcg_errmsgs['16'];
				}
				
			} else {
			
				// Get the Metabox image
				$metabox_image = get_post_meta($id_found->ID, $postmeta['image'], true);
				
				if( $metabox_image ) {
					$image_src = $baseimgurl . $metabox_image;
					$image_class = "dfcg-metabox";
        			$image_err = $dfcg_errmsgs['20'];
					// Note: No Error message will be triggered if _dfcg-image is set but URL is wrong, ie 404.
			
				} elseif( !empty($dfcg_options['defimgid']) ) {
					// Display the "Pages" default image
					$image_src = $dfcg_options['defimgid'];
        			$image_class = "dfcg-metabox-default";
					$image_err = $dfcg_errmsgs['17'];
				
				} else {
					// Display Pages Error image
					$image_src = DFCG_ERRORIMGURL;
					$image_class = "dfcg-metabox-error";
					$image_err = $dfcg_errmsgs['18'];
				}
			}
			
			// Get the thumbnail - uses Post Thumbnails if AUTO images are used
			$thumb_html = dfcg_get_thumbnail($id_found->ID, $image_src, $id_found->post_title);
			
			// Output image and thumbnail
			$output .= "\n\t" . '<img src="'. $image_src . '" alt="'. esc_attr($id_found->post_title) .'" class="' . $image_class . ' full" />';
			$output .= "\n\t" . $thumb_html;
			$output .= $image_err;
			
			// Close the ImageElement div
			$output .= "\n" . '</div>';

		endforeach;

		/*	Compare $pages_selected_count with the db query object $pages_found_count
			to check that the number of gallery images is the same.	If it's not the
			same, then one or more of the selected Page IDs are not valid Pages */

		if( $ids_found_count !== $ids_selected_count) {
			$output .= "\n" . $dfcg_errmsgs['5'];
			if( $dfcg_options['errors'] == "true" ) {
				$output .= "\n" . '<!-- ' . __('Number of Pages selected in DCG Settings = ', DFCG_DOMAIN) . $ids_selected_count . ' -->';
				$output .= "\n" . '<!-- ' . __('Number of Pages found = ', DFCG_DOMAIN) . $ids_found_count . ' -->';
			}
		}

		// End of the gallery markup
		$output .= "\n" . '</div><!-- End of Dynamic Content Gallery output -->' . "\n\n";
		
	} else {
		/* Oops! Either none of the Page IDs are valid or the db query failed in some way */
		$output = "\n" . $dfcg_errmsgs['6'];
	}
	
	// Output the Gallery
	echo $output;
}