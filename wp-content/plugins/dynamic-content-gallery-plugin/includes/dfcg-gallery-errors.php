<?php
/**
* Front-end - Error Messages
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info Error messages generated in the event that Settings are not correct.
* @info Messages are printed to the browser and/or Page Source.
* @info This should help users get the gallery working.
* @info Note: Admin related error messages are handled in dfcg-admin-ui-validation.php
*
* @since 3.0
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', DFCG_DOMAIN) );
}



/**
* Function to control Error Reporting
*
* @uses	dfcg_errors()
*
* @global array $dfcg_options Array of plugin options from db
* @return array $errmsgs Array of error messages, if Errors have been turned on in settings
* @since 3.2
*/
function dfcg_errors_output() {
	
	global $dfcg_options;
	
	// If Error reporting is ON
	if( $dfcg_options['errors'] == "true" ) {
		// Create array of error messages
		$errmsgs = dfcg_errors();
		return $errmsgs;
	}
}


/**
* Function which manages Error Message content
*
* @return $dfcg_errmsgs	Array of all Error Messages
* @since 3.2.2
* @updated 3.3
*/
function dfcg_errors() {

	/* Public error messages - these are displayed in the browser */
	$errmsg_public = __('Dynamic Content Gallery Error: View page source for details.', DFCG_DOMAIN);

	/* Page Source error messages - standard texts */
	$errmsg_critical = '<!-- ' . __('Rating: Critical. Fix error in order to display gallery.', DFCG_DOMAIN) .' -->';
	$errmsg_noncritical = '<!-- ' . __('Rating: Non-critical. This error does not prevent the gallery from working properly.', DFCG_DOMAIN) .' -->';


	/* Page Source error messages - these are shown as HTML comments in the Page Source */

	/**	Error Message 1		Only one Page/Post ID has been specified in Settings.
	*	Populate-method: 	ID Method
	*	Trigger:			$dfcg_pages_selected_count < 2 returns TRUE.
	*	Rating:				Critical
	*	Reason:				Only one Page/Post ID has been defined in Settings.
	*	Action:				Print public error message, return, exit script.
	*	Fix:				Enter a minimum of 2 Page/Post IDs in the DCG Settings page
	*	Notes:				See $dfcg_errmsg_2 for when no Page/Post ID have been specified in DCG Settings
	*						See $dfcg_errmsg_9 for when SQL query only finds one valid Page/Post ID
	*						Updated 3.3
	*/
	$errmsg_1 = "\n" . $errmsg_public;
	$errmsg_1 .= "\n" . '<!-- ' . __('DCG Error Message 1: You are using the ID Method for populating the gallery, but you have only specified one Page/Post ID in the DCG Settings page.', DFCG_DOMAIN) .' -->';
	$errmsg_1 .= "\n" . $errmsg_critical;
	$errmsg_1 .= "\n" . '<!-- ' . __('Fix: Enter a minimum of 2 valid Page/Post IDs in the DCG Settings > Gallery Method > ID Method options for the gallery to work.', DFCG_DOMAIN) .' -->';


	/**	Error Message 2		No Page/Post Ids have been specified in Settings.
	*	Populate-method: 	ID Method
	*	Trigger:			!empty($dfcg_pages) returns FALSE.
	*	Rating:				Critical
	*	Reason:				No Page/Post IDs have been specified in Settings.
	*	Action:				Print public error message, return, exit script.
	*	Fix:				Enter a minimum of 2 Page/Post IDs in the DCG Settings page
	*	Notes:				Fix is same as $dfcg_errmsg_1 but Reason is different.
	*						Updated 3.3
	*/
	$errmsg_2 = "\n" . $errmsg_public;
	$errmsg_2 .= "\n" . '<!-- ' . __('DCG Error Message 2: You are using the ID Method for populating the gallery, but you have not specified any PagePost IDs in the DCG Settings page.', DFCG_DOMAIN) .' -->';
	$errmsg_2 .= "\n" . $errmsg_critical;
	$errmsg_2 .= "\n" . '<!-- ' . __('Fix: Enter a minimum of 2 valid Page/Post IDs in the DCG Settings > Gallery Method > ID Method options for the gallery to work.', DFCG_DOMAIN) .' -->';


	/**	Error Message 3		Missing Description
	*	Populate-method: 	ID Method, One Category, Multi Option
	*	Trigger:			get_post_meta($dfcg_get_page->ID, "dfcg-desc", true) returns FALSE and
	*						$dfcg_options['defimagedesc'] !== '' returns FALSE
	*	Rating:				Non-critical
	*	Reason:				Missing Description. The Post/Page does not have a custom field _dfcg-desc defined and neither is a
	*						Default Description defined in DCG Settings.
	*	Action:				$dfcg_errmsg_3 displayed as HTML comment underneath empty <p> tags in Source markup.
	*	Fix:				Either create a custom field _dfcg-desc for the relevant Page/Post, or define a Default Description in the DCG Settings page.
	*						Either of these fixes will clear both errmsg_public and this error message.
	*	Notes:				Informational only, this error does not prevent the gallery running.
	*						Updated 3.3
	*/
	$errmsg_3 = "\n" . '<!-- ' . __('DCG Error Message 3: DCG Metabox Slide Pane Description is empty and Default Description does not exist.', DFCG_DOMAIN) .' -->';
	$errmsg_3 .= "\n" . $errmsg_noncritical;
	$errmsg_3 .= "\n" . '<!--	' . __('Fix: Enter a description in the DCG Metabox Slide Pane Description field for this Page/Post and/or define a Default Description in the DCG Settings page.', DFCG_DOMAIN) .' -->';


	/*	Error Message 4		Missing Images
	*	Populate-method: 	ID, One Category, Multi Option
	*	Image Management:	Full or Partial
	*	Trigger:			ID:				get_post_meta($dfcg_get_page->ID, "_dfcg-image", true) returns FALSE and
	*										!empty($dfcg_options['defimgpages']) returns FALSE
	*						One Category:	get_post_meta($dfcg_get_page->ID, "_dfcg-image", true) returns FALSE and
	*										file_exists($filename) returns FALSE, therefore a Category Default image doesn't exist.
	*						Multi Option:	get_post_meta($dfcg_get_page->ID, "_dfcg-image", true) returns FALSE and
	*										file_exists($filename) returns FALSE, therefore a Category Default image doesn't exist.										
	*	Rating:				Non-critical
	*	Reason:				Missing image. The Post/Page does not have a custom field _dfcg-image defined and a Default Image has not been defined.
	*	Action:				$dfcg_errmsg_4 displayed as HTML comment underneath <img> tags in Source markup.
	*	Fix:				Either create a DCG Metabox Image URL for the relevant Page/Post, or create suitable Default Images.
	*	Notes:				Informational only, this error does not prevent the gallery running.
	*						This errmsg is NOT triggered if the information in dfcg-image and/or a default image is incorrect, ie the URL or path is wrong
	*						Error image should also be displayed in gallery.
	*/
	$errmsg_4 = "\n\t\t\t" . '<!-- ' . __('DCG Error Message 4: Default error image used: DCG Metabox Image URL is empty and Default Image does not exist.', DFCG_DOMAIN) .' -->';
	$errmsg_4 .= "\n" . $errmsg_noncritical;
	$errmsg_4 .= "\n" . '<!-- ' . __('Fix: Enter an image URL in the DCG Metabox Image URL field for this Page/Post and/or define a Default Image in the DCG Settings page.', DFCG_DOMAIN) .' -->';


	/*	Error Message 5		Number of Page/Post IDs found in db is not equal to the number of Page/Post IDs selected in Settings
	*	Populate-method: 	ID Method
	*	Trigger:			$dfcg_pages_selected_count !== $dfcg_pages_found_count
	*	Rating:				Non-critical
	*	Reason:				The number of Page/Post IDs found in db is not equal to the number of Page/Post IDs selected in Settings.
	*	Action:				Error message displayed in Page Source only.
	*	Fix:				Check the Page/Post IDs entered in the DCG Settings page
	*	Notes:				Informational only, this error does not prevent the gallery running.
	*						Updated 3.3
	*/
	$errmsg_5 = "\n" . '<!-- ' . __('DCG Error Message 5: You are using the ID Method for populating the gallery, but not all of the selected Page/Post IDs are valid Pages/Posts.', DFCG_DOMAIN) .' -->';
	$errmsg_5 .= "\n" . $errmsg_noncritical;
	$errmsg_5 .= "\n" . '<!-- ' . __('Fix: Check the Page/Post IDs entered in the DCG Settings > Gallery Method > ID Method options.', DFCG_DOMAIN) .' -->';


	/**	Error Message 6		No valid Page/Post ID's selected, or db query has failed
	*	Populate-method: 	ID Method
	*	Trigger:			if( $dfcg_pages_found ) returns FALSE
	*	Rating:				Critical
	*	Action:				Print public error message, return, exit script.
	*	Notes:				Updated 3.3
	*/
	$errmsg_6 = "\n" . $errmsg_public . "\n";
	$errmsg_6 .= "\n" . '<!-- ' . __('DCG Error Message 6: You are using the ID Method for populating the gallery, but none of the selected Page/Post IDs are valid Pages/Posts, or the database query has failed.', DFCG_DOMAIN) .' -->';
	$errmsg_6 .= "\n" . $errmsg_critical;
	$errmsg_6 .= "\n" . '<!-- ' . __('Fix: Check the validity of the Page/Post IDs entered in the DCG Settings > Gallery Method > ID Method options. At least 2 IDs must be valid.', DFCG_DOMAIN) .' -->';
	$errmsg_6 .= "\n" . '<!-- ' . __('Fix: If at least 2 of the selected IDs are valid, check server error logs.', DFCG_DOMAIN) .' -->';


	/*	Error Message 7
	*	Populate-method: 	One Category
	*	Trigger:			$counter - $dfcg_posts_number !== 0 returns FALSE
	*	Rating:				Non-critical
	*	Reason:				The number of Posts to display in Settings is not equal to the number of Posts found in WP_Query.
	*						This means that there are less Posts in the selected Category than have been selected in Settings.
	*	Action:				Error message is displayed in View Source.
	*	Fix:				Reduce the "Number of Posts to display" in the DCG Settings page to match the Number of Posts found.
	*	Notes:				Informational only, this error does not prevent the gallery running.
	*						
	*/
	$errmsg_7 = "\n" . '<!-- ' . __('DCG Error Message 7: You have less Posts in the selected Category than the number specified in the Settings Page.', DFCG_DOMAIN) .' -->';
	$errmsg_7 .= "\n" . $errmsg_noncritical;
	$errmsg_7 .= "\n" . '<!-- ' . __('Fix: Reduce the "Number of Posts to display" in the DCG Settings page to match the Number of Posts found.', DFCG_DOMAIN) .' -->';


	/*	Error Message 8
	*	Populate-method: 	One Category
	*	Trigger:			WP_Query returned no results.
	*	Rating:				Critical
	*	Reason:				No results returned by WP_Query. Theoretically, thanks to use of
							wp_dropdown_categories and dropdown select for number of Posts,
							this situation should never happen.
	*	Action:				Print public error message, return, exit script.
	*	Fix:				Reactivate plugin and try again.
							Check that WP is working properly.
	*	Notes:				This error message should never occur on a properly installed
	*						and working WP install.
	*						Updated 3.3
	*/
	$errmsg_8 = "\n" . $errmsg_public . "\n";
	$errmsg_8 .= "\n" . '<!-- ' . __('DCG Error Message 8: You are using the One Category gallery method but the plugin failed to find any Posts.', DFCG_DOMAIN) .' -->';
	$errmsg_8 .= "\n" . $errmsg_critical;
	$errmsg_8 .= "\n" . '<!-- ' . __('Fix: Deactivate and reactivate the plugin and try again.', DFCG_DOMAIN) .' -->';


	/**	Error Message 9		Only 1 Page ID selected in Settings is valid, as per SQL query results
	*	Populate-method: 	Pages
	*	Trigger:			$dfcg_pages_found_count < 2 returns TRUE
	*	Rating:				Critical
	*	Action:				Print public error message, return, exit script.
	*	Fix:				Ensure that there are a minimum of 2 valid Page IDs specified in the DCG Settings page.
	*	Notes:				This is similar to Error Message 1, but is triggered by a check on the SQL results,
	*						not on the number of selected Pages.
	*/
	$errmsg_9 = "\n" . $errmsg_public . "\n";
	$errmsg_9 .= "\n" . '<!-- ' . __('DCG Error Message 9: Only one of the Page IDs specified in the DCG Settings page is a valid Page ID in the database.', DFCG_DOMAIN) .' -->';
	$errmsg_9 .= "\n" . $errmsg_critical;
	$errmsg_9 .= "\n" . '<!-- ' . __('Fix: Ensure that there are a minimum of 2 valid Page IDs specified in the DCG Settings page.', DFCG_DOMAIN) .' -->';


	/**	Error Message 10	dynamic-gallery.php produces no output at all
	*	Populate-method: 	All
	*	Trigger:			dynamic-gallery.php produces no output at all, eg there is a missing included file.	
	*	Rating:				Critical
	*	Action:				Print public error message, return, exit script.
	*	Fix:				Check that plugin has been installed properly.
	*	Notes:				This shouldn't happen with a correct plugin install
	*						Updated 3.3
	*
	*/
	$errmsg_10 = "\n" . '<!-- ' . __('DCG Error Message 10: The plugin is unable to generate any output.', DFCG_DOMAIN) .' -->';
	$errmsg_10 .= "\n" . $errmsg_critical;
	$errmsg_10 .= "\n" . '<!-- ' . __('Fix: Check that the plugin has been installed properly and that all files contained within the download ZIP file have been uploaded to your server.', DFCG_DOMAIN) .' -->';


	/**	Error Message 11	Insufficient Post Selects have been defined in Settings
	*	Populate-method: 	Multi Option
	*	Trigger:			$dfcg_selected_slots < 2 returns TRUE.
	*	Rating:				Critical
	*	Reason:				Either 1 or 0 Post Selects have been defined in Settings
	*	Action:				Print public error message, return, exit script.
	*	Fix:				Enter a minimum of 2 Post Selects in the DCG Settings page
	*	Notes:				This is a pre-WP_Query validation check, ie checks what is in Settings only
	*						
	*/
	$errmsg_11 = "\n" . $errmsg_public . "\n";
	$errmsg_11 .= "\n" . '<!-- ' . __('DCG Error Message 11: You have defined less than 2 Post Selects in the DCG Settings page.', DFCG_DOMAIN) .' -->';
	$errmsg_11 .= "\n" . $errmsg_critical;
	$errmsg_11 .= "\n" . '<!-- ' . __('Fix: Enter a minimum of 2 valid Post Selects in the DCG Settings page for the gallery to work.', DFCG_DOMAIN) .' -->';


	/**	Error Message 12	WP_Query couldn't find a specific Post
	*	Populate-method: 	Multi Option
	*	Trigger:			if( $counter - $counter1 - $counter2 !== 0 ) returns TRUE
	*	Rating:				Non Critical
	*	Reason:				The number of Post Selects does not equal the number of posts found
	*						by the WP_Query loops.
	*	Action:				Print error message in Page Source only.
	*	Fix:				Check the Post Select for the Image Slot # in the DCG Settings page.
	*	Notes:				This is a post-WP_Query validation check, and simply compares the 2 counters.
	*						$counter = number of Post Selects
	*						$counter1 = number of times WP_Query is run
	*						$counter2 = number of Excluded Posts
	*/
	$errmsg_12 = "\n" . '<!-- ' . __('DCG Error Message 12: The Post for at least one of your chosen Image Slots could not be found.', DFCG_DOMAIN) .' -->';
	$errmsg_12 .= "\n" . $errmsg_noncritical;
	$errmsg_12 .= "\n" . '<!-- ' . __('This could be caused by, for example, defining a Post Select of 4 but only 3 Posts exist in that Category.', DFCG_DOMAIN) .' -->';
	$errmsg_12 .= "\n" . '<!-- ' . __('Look at the XHTML comments to see which Image # is missing.', DFCG_DOMAIN) .' -->'; 
	$errmsg_12 .= "\n" . '<!-- ' . __('Fix: Check the Post Select for this missing Image # in the DCG Settings page.', DFCG_DOMAIN) .' -->';


	/**	Error Message 13	WP_Query couldn't find FIRST Post
	*	Populate-method: 	Multi Option
	*	Trigger:			if( !$recent->have_posts() && $counter < 2) returns TRUE
	*						This means that the first WP_Query doesn't have any posts
	*	Rating:				Critical
	*	Reason:				The Category in cat01 doesn't have any posts. This is likely caused by a new install, and the default is cat id=1
	*						which has no posts. Therefore the gallery won't run if first image doesn't exist.
	*	Action:				Print public error message, error message in Page Source, then exit function.
	*	Fix:				Go to DCG Settings page and click Save Changes. This will clear the default cat01=1, and all can run normally.
	*	Notes:				This is a post-WP_Query validation check, only triggered by the first WP_Query.
	*						
	*/
	$errmsg_13 = "\n" . $errmsg_public . "\n";
	$errmsg_13 .= "\n" . '<!-- ' . __('DCG Error Message 13: The Post for Image Slot 1 could not be found.', DFCG_DOMAIN) .' -->';
	$errmsg_13 .= "\n" . $errmsg_critical;
	$errmsg_13 .= "\n" . '<!-- ' . __('This is because the plugin has set a default category for this Image Slot, but there are no posts in this category.', DFCG_DOMAIN) .' -->';
	$errmsg_13 .= '<!-- ' . __('Fix: Go to the DCG Settings page and click Save Changes. The error should then clear itself.', DFCG_DOMAIN) .' -->';


	/**	Error Message 14	The Post/Page doesn't have an Image Attachment
	*	Populate-method: 	All
	*	Trigger:			if $dfcg_grab_image() returns FALSE
	*	Rating:				Non Critical
	*	Reason:				The Post does not have an Image Attachment
	*	Action:				Print error message in Page Source only.
	*	Fix:				Add an image to the relevant Post via the Media Uploader
	*	Notes:				Relies on wp_get_attachment_image_src returning FALSE
	*/
	$errmsg_14 = "\n" . '<!-- ' . __('DCG Error Message 14: Image Attachment not found. DCG Metabox Image displayed instead.', DFCG_DOMAIN) .' -->';
	$errmsg_14 .= "\n" . $errmsg_noncritical;
	$errmsg_14 .= "\n" . '<!-- ' . __('Fix: Upload a suitable image to this Post/Page via the Media Uploader.', DFCG_DOMAIN) .' -->';


	/**	Error Message 15	The Post/Page doesn't have an Image Attachment, nor a DCG Metabox Image URL. Default image is displayed instead
	*	Populate-method: 	All
	*	Trigger:			if $dfcg_grab_image() returns FALSE and Metabox Image URL doesn't exist
	*	Rating:				Non Critical
	*	Reason:				The Post does not have an Image Attachment, nor a Metabox Image URL
	*	Action:				Print error message in Page Source only.
	*	Fix:				Add an image to the relevant Post via the Media Uploader
	*	Notes:				Relies on wp_get_attachment_image_src returning FALSE
	*/
	$errmsg_15 = "\n" . '<!-- ' . __('DCG Error Message 15: Image Attachment not found. DCG Metabox Image does not exist. Default Image displayed instead.', DFCG_DOMAIN) .' -->';
	$errmsg_15 .= "\n" . $errmsg_noncritical;
	$errmsg_15 .= "\n" . '<!-- ' . __('Fix: Upload a suitable image to this Post/Page via the Media Uploader, or enter an image URL in the DCG Metabox Image URL field for this Post/Page.', DFCG_DOMAIN) .' -->';
	
	
	/**	Error Message 16	The Post/Page doesn't have an Image Attachment, nor a DCG Metabox Image URL, and a Default image doesn't exist.
	*	Populate-method: 	All
	*	Trigger:			if $dfcg_grab_image() returns FALSE and Metabox Image URL doesn't exist and default image doesn't exist
	*	Rating:				Non Critical
	*	Reason:				The Post does not have an Image Attachment, nor a Metabox Image URL, nor a Default Image
	*	Action:				Print error message in Page Source only. Display Error Image in gallery.
	*	Fix:				Add an image to the relevant Post via the Media Uploader
	*	Notes:				Relies on wp_get_attachment_image_src returning FALSE
	*/
	$errmsg_16 = "\n" . '<!-- ' . __('DCG Error Message 16: Image Attachment not found. DCG Metabox Image does not exist. Default Image does not exist. Error image displayed instead.', DFCG_DOMAIN) .' -->';
	$errmsg_16 .= "\n" . $errmsg_noncritical;
	$errmsg_16 .= "\n" . '<!-- ' . __('Fix: Upload a suitable image to this Post/Page via the Media Uploader, or enter an image URL in the DCG Metabox Image URL field for this Post/Page, or create Default Images as described in the Configuration Guide.', DFCG_DOMAIN) .' -->';
	
	
	/**	Error Message 17	The Post/Page doesn't have a DCG Metabox Image URL. Default image is displayed instead
	*	Populate-method: 	All
	*	Trigger:			if $dfcg_grab_image() returns FALSE and Metabox Image URL doesn't exist
	*	Rating:				Non Critical
	*	Reason:				The Post does not have an Image Attachment, nor a Metabox Image URL
	*	Action:				Print error message in Page Source only.
	*	Fix:				Add an image to the relevant Post via the Media Uploader
	*	Notes:				Relies on wp_get_attachment_image_src returning FALSE
	*/
	$errmsg_17 = "\n" . '<!-- ' . __('DCG Error Message 17: DCG Metabox Image does not exist. Default Image displayed instead.', DFCG_DOMAIN) .' -->';
	$errmsg_17 .= "\n" . $errmsg_noncritical;
	$errmsg_17 .= "\n" . '<!-- ' . __('Fix: Enter an image URL in the DCG Metabox Image URL field for this Post/Page.', DFCG_DOMAIN) .' -->';
	
	
	/**	Error Message 18	The Post/Page doesn't have a DCG Metabox Image URL, and a Default image doesn't exist.
	*	Populate-method: 	All
	*	Trigger:			if $dfcg_grab_image() returns FALSE and Metabox Image URL doesn't exist and default image doesn't exist
	*	Rating:				Non Critical
	*	Reason:				The Post does not have an Image Attachment, nor a Metabox Image URL, nor a Default Image
	*	Action:				Print error message in Page Source only. Display Error Image in gallery.
	*	Fix:				Add an image to the relevant Post via the Media Uploader
	*	Notes:				Relies on wp_get_attachment_image_src returning FALSE
	*/
	$errmsg_18 = "\n" . '<!-- ' . __('DCG Error Message 18: DCG Metabox Image does not exist. Default Image does not exist. Error Image displayed instead.', DFCG_DOMAIN) .' -->';
	$errmsg_18 .= "\n" . $errmsg_noncritical;
	$errmsg_18 .= "\n" . '<!-- ' . __('Fix: Enter an image URL in the DCG Metabox Image URL field for this Post/Page. You should also create Default images as described in the Configuration Guide.', DFCG_DOMAIN) .' -->';
	
	
	/** Error Message 19	Info message
	*	Populate-method:	All
	*	Trigger:			Image attachment found
	*/
	$errmsg_19 = "\n" . '<!-- ' . __('DCG Info Message 19: Auto image selected. Image attachment found.', DFCG_DOMAIN) .' -->';
	
	
	/** Error Message 20	Info message
	*	Populate-method:	All
	*	Trigger:			Metabox image found
	*/
	$errmsg_20 = "\n" . '<!-- ' . __('DCG Info Message 20: Metabox image selected. Metabox image found.', DFCG_DOMAIN) .' -->';
	
	
	
	// Set up our error message array of all error messages
	// This will be handier when using global scope declaration in gallery display functions
	$errmsgs = array (
		'1' => $errmsg_1,
		'2' => $errmsg_2,
		'3' => $errmsg_3,
		'4' => $errmsg_4,
		'5' => $errmsg_5,
		'6' => $errmsg_6,
		'7' => $errmsg_7,
		'8' => $errmsg_8,
		'9' => $errmsg_9,
		'10' => $errmsg_10,
		'11' => $errmsg_11,
		'12' => $errmsg_12,
		'13' => $errmsg_13,
		'14' => $errmsg_14,
		'15' => $errmsg_15,
		'16' => $errmsg_16,
		'17' => $errmsg_17,
		'18' => $errmsg_18,
		'19' => $errmsg_19,
		'20' => $errmsg_20
	);
	
	// Return array of Error Messages
	return $errmsgs;
}