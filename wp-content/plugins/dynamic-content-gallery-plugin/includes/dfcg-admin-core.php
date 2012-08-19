<?php
/**
* Admin Core functions - all the main stuff needed to run the backend
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info Core Admin Functions called by various add_filters and add_actions:
* @info	- Internationalisation
* @info	- Register Settings
* @info	- Add Settings Page
* @info	- Plugin action links
* @info	- Plugin row meta
* @info	- WP Version check
* @info	- Admin Notices for Settings reset
* @info	- Options handling and upgrading
*
* @since 3.0
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.') );
}


/***** Internationalisation *****/

/**
* Function to load textdomain for Internationalisation functionality
*
* Loads textdomain if $dfcg_text_loaded is false
*
* Note: .mo file should be named dynamic-content-gallery-plugin-xx_XX.mo and placed in the DCG plugin's languages folder.
* xx_XX is the language code, eg fr_FR for French etc.
*
* @global $dfcg_text_loaded bool defined in dynamic-gallery-plugin.php
* @uses load_plugin_textdomain()
* @since 3.2
*/
function dfcg_load_textdomain() {
	
	global $dfcg_text_loaded;
   	
	// If textdomain is already loaded, do nothing
	if( $dfcg_text_loaded ) {
   		return;
   	}
	
	// Textdomain isn't already loaded, let's load it
   	load_plugin_textdomain(DFCG_DOMAIN, false, dirname(plugin_basename(__FILE__)). '/languages');
   	
	// Change variable to prevent loading textdomain again
	$dfcg_text_loaded = true;
}



/***** Admin Init *****/

/**
* Register Settings as per new Settings API, 2.7+
*
* Hooked to 'admin_init'
*
* dfcg_plugin_settings_options 	= Options Group name
* dfcg_plugin_settings 			= Option Name in db
*
* @uses dfcg_sanitise(), callback function for sanitising options
*
* @since 3.0
*/
function dfcg_options_init() {

	register_setting( 'dfcg_plugin_settings_options', 'dfcg_plugin_settings', 'dfcg_sanitise' );
}



/***** Settings Page and Plugins Page Functions *****/

/**
* Create Admin settings page and populate options
*
* Hooked to 'admin_menu'
*
* Renamed in 3.2, was dfcg_add_page()
* No need to check credentials - already built in to core wp function
*
* @uses	dfcg_options_page()
* @uses dfcg_loadjs_admin_head()
* @uses	dfcg_set_gallery_options()
*
* @return string $dfcg_page_hook, wp page hook
* @since 3.2
* @updated 3.3.1
*/
function dfcg_add_to_options_menu() {
	
	// Populate plugin's options (since 3.3.1, now runs BEFORE settings page is added. Duh!)
	dfcg_set_gallery_options();
	
	// Add Settings Page
	$dfcg_page_hook = add_options_page('Dynamic Content Gallery Options', 'Dynamic Content Gallery', 'manage_options', DFCG_FILE_HOOK, 'dfcg_options_page');
	
	// Load all the jQuery stuff we need for back end
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	
	// Load Admin external scripts and CSS
	add_action( 'admin_head-settings_page_' . DFCG_FILE_HOOK, 'dfcg_loadjs_admin_head', 20 );
	
	return $dfcg_page_hook; // May need this later
}


/**
* Function to load Admin CSS and JS files
*
* Hooked to 'admin_print_scripts-settings_page_' in dfcg_add_to_options_menu()
*
* @since 3.2
* @updated 3.3
*/
function dfcg_loadjs_admin_head() {
	
	echo "\n" . '<!-- Dynamic Content Gallery plugin version ' . DFCG_VER . ' www.studiograsshopper.ch  Begin admin scripts -->' . "\n";
	echo '<link rel="stylesheet" href="' . DFCG_URL . '/admin-assets/dfcg-ui-admin.css" type="text/css" />' . "\n";
	echo '<link rel="stylesheet" href="' . DFCG_URL . '/admin-assets/dfcg-tabs-ui.css" type="text/css" />' . "\n";
	
	echo '<script type="text/javascript">
			jQuery(document).ready(function($) {
				var $tabs = $("#tabs").tabs();
				
				$(".dfcg-panel-image-link").click(function() { // bind click event to link
    				$tabs.tabs("select", 1); // switch to second tab
    			return false;
				});
				
				$(".dfcg-panel-gallery-link").click(function() { // bind click event to link
    				$tabs.tabs("select", 2); // switch to third tab
    			return false;
				});
				
				$(".dfcg-panel-desc-link").click(function() { // bind click event to link
    				$tabs.tabs("select", 3); // switch to fourth tab
    			return false;
				});
				
				$(".dfcg-panel-css-link").click(function() { // bind click event to link
    				$tabs.tabs("select", 4); // switch to fifth tab
    			return false;
				});
				
				$(".dfcg-panel-javascript-link").click(function() { // bind click event to link
    				$tabs.tabs("select", 5); // switch to sixth tab
    			return false;
				});
				
				$(".dfcg-panel-scripts-link").click(function() { // bind click event to link
    				$tabs.tabs("select", 6); // switch to seventh tab
    			return false;
				});
				
				$(".dfcg-panel-tools-link").click(function() { // bind click event to link
    				$tabs.tabs("select", 7); // switch to eighth tab
    			return false;
				});
				
				$(".dfcg-panel-help-link").click(function() { // bind click event to link
    				$tabs.tabs("select", 8); // switch to nine tab
    			return false;
				});
			});
		</script>' . "\n";
	echo '<script type="text/javascript" src="' . DFCG_URL . '/admin-assets/cluetip/jquery.cluetip.min.js"></script>' . "\n";
	echo '<link rel="stylesheet" href="' . DFCG_URL . '/admin-assets/cluetip/jquery.cluetip.css" type="text/css" />' . "\n";
	echo "<script type=\"text/javascript\">
			jQuery(document).ready(function($) {
				$('a.load-local').cluetip({local:true, cursor: 'pointer', sticky: true, closePosition: 'title'});
			});
		</script>" . "\n";
	echo '<!-- Dynamic Content Gallery plugin end admin scripts -->' . "\n";
}



/**
* Display the Settings page
*
* Used by dfcg_add_to_options_menu()
*
* @global array $dfcg_options db main options
* @since 3.2
*/
function dfcg_options_page(){
	
	// Needed because this is passed to dfcg_on_load_validation() in dfcg-admin-ui-screen.php
	global $dfcg_options;
	
	// Need to get these back from the db because they may have been updated since page was last loaded
	$dfcg_postmeta_upgrade = get_option('dfcg_plugin_postmeta_upgrade');
	
	if( $dfcg_postmeta_upgrade['upgraded'] == 'completed' ) {
		include_once( DFCG_DIR . '/includes/dfcg-admin-ui-screen.php' );
	
	} else {
		// We need to upgrade
		include_once( DFCG_DIR . '/includes/dfcg-admin-ui-upgrade-screen.php' );
	}
}


/**
* Display a Settings link in main Plugin page in Dashboard
*
* Puts the Settings link in with Deactivate/Activate links in Plugins Settings page
*
* Hooked to 'plugin_action_links' filter
*
* @return array $links Array of links shown in first column, main Dashboard Plugins page
* @since 1.0
*/
function dfcg_filter_plugin_actions($links, $file){
	static $this_plugin;

	if( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);

	if( $file == DFCG_FILE_NAME ) {
		$settings_link = '<a href="admin.php?page=' . DFCG_FILE_HOOK . '">' . __('Settings') . '</a>';
		$links = array_merge( array($settings_link), $links); // before other links
	}
	return $links;
}


/**
* Display Plugin Meta Links in main Plugin page in Dashboard
*
* Adds additional meta links in the plugin's info section in main Plugins Settings page
*
* Hooked to 'plugin_row_meta filter' so only works for WP 2.8+
*
* @param array $links Default links for each plugin row
* @param string $file plugins.php filehook
*
* @return array $links Array of customised links shown in plugin row after activation
* @since 3.0
*/
function dfcg_plugin_meta($links, $file) {
 
	// Check we're only adding links to this plugin
	if( $file == DFCG_FILE_NAME ) {
	
		// Create DCG links
		$settings_link = '<a href="admin.php?page=' . DFCG_FILE_HOOK . '">' . __('Settings') . '</a>';
		$config_link = '<a href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/" target="_blank">' . __('Configuration Guide', DFCG_DOMAIN) . '</a>';
		$faq_link = '<a href="http://www.studiograsshopper.ch/dynamic-content-gallery/faq/" target="_blank">' . __('FAQ', DFCG_DOMAIN) . '</a>';
		$docs_link = '<a href="http://www.studiograsshopper.ch/dynamic-content-gallery/documentation/" target="_blank">' . __('Documentation', DFCG_DOMAIN) . '</a>';
		
		return array_merge(
			$links,
			array( $settings_link, $config_link, $faq_link, $docs_link )
			
		);
	}
 
	return $links;
}


/**
* Function to do WP Version check AND check that theme has add_theme_support('post-thumbnails')
*
* DCG v3.0 requires WP 2.9+ to run. This function prints a warning
* message in the main Plugins screen and on the DCG Settings page if version is less than 2.9.
*
* Hooked to 'after_action_row_$plugin' filter
*
* @since 3.2
* @updated 3.3
*/	
function dfcg_wp_version_check() {
	
	$wp_valid = version_compare(get_bloginfo("version"), DFCG_WP_VERSION_REQ, '>=');
	
	$current_page = basename($_SERVER['PHP_SELF']);
	
	// Check we are on the right screen and version is not valid
	if( !$wp_valid && $current_page == "plugins.php" ) {
		
		$version_msg_start = '<tr class="plugin-update-tr"><td class="plugin-update" colspan="3">';
		$version_msg_start .= '<div class="update-message" style="background:#FFEBE8;border-color:#BB0000;">';
		$version_msg_end = '</div></td></tr>';
		
		if( !function_exists('wpmu_create_blog') ) {
			// We're in WP
			$version_msg = __('<strong>Warning!</strong> This version of Dynamic Content Gallery requires Wordpress', DFCG_DOMAIN) . ' <strong>' . DFCG_WP_VERSION_REQ . '</strong>+ ' . __('Please upgrade Wordpress to run this plugin.', DFCG_DOMAIN);
			echo $version_msg_start . $version_msg . $version_msg_end;
		
		} else {
			// We're in WPMU
			$version_msg = __('<strong>Warning!</strong> This version of Dynamic Content Gallery requires WPMU', DFCG_DOMAIN) . ' <strong>' . DFCG_WP_VERSION_REQ . '</strong>+ ' . __('Please contact your Site Administrator.', DFCG_DOMAIN);
			echo $version_msg_start . $version_msg . $version_msg_end;
		}
	}
	
	// Need to check for Theme Support for Post Thumbnails, introduced in WP2.9 and required by DCG v3.3
	if( !current_theme_supports('post-thumbnails') && $current_page == "plugins.php" ) {
		$msg_start = '<tr class="plugin-update-tr"><td class="plugin-update" colspan="3">';
		$msg_start .= '<div class="update-message" style="background:#FFEBE8;border-color:#BB0000;">';
		$msg_end = '</div></td></tr>';
		$msg = __('<strong>Warning!</strong> This version of Dynamic Content Gallery requires that your theme supports the WP Post Thumbnails feature.', DFCG_DOMAIN) . ' <a href="http://www.studiograsshopper.ch/dynamic-content-gallery/">' . __('Read more here.', DFCG_DOMAIN) . '</a>';
		echo $msg_start . $msg . $msg_end;
	}
	
	// The following will also show the version warning message on the DCG Settings page and at the top of the Plugins page
	// We only need to check against options-general.php because this part of the function
	// will only be run by the calling function dfcg_on_load_validation() which is only run when we're on the DCG page.
	// TODO: Would be better to hook to admin_notices though...
	if( !$wp_valid && ( $current_page == "options-general.php" || $current_page == "plugins.php" ) ) {
		
		$version_msg_start = '<div class="error"><p>';
		$version_msg_end = '</p></div>';
		
		if( !function_exists('wpmu_create_blog') ) {
			// We're in WP
			$version_msg = '<strong>' . __('Warning! This version of Dynamic Content Gallery requires Wordpress', DFCG_DOMAIN) . ' ' . DFCG_WP_VERSION_REQ . '+ ' . __('Please upgrade Wordpress to run this plugin.', DFCG_DOMAIN) . '</strong>';
			echo $version_msg_start . $version_msg . $version_msg_end;
			
		} else {
			// We're in WPMU
			$version_msg = '<strong>' . __('Warning! This version of Dynamic Content Gallery requires WPMU', DFCG_DOMAIN) . ' ' . DFCG_WP_VERSION_REQ . '+ ' . __('Please contact your Site Administrator.', DFCG_DOMAIN) . '</strong>';
			echo $version_msg_start . $version_msg . $version_msg_end;
		}
	}
	
	
	// Need to check for Theme Support for Post Thumbnails, introduced in WP2.9 and required by DCG v3.3
	if( !current_theme_supports('post-thumbnails') && ( $current_page == "options-general.php" || $current_page == "plugins.php" ) ) {
		$msg_start = '<div class="error"><p>';
		$msg_end = '</p></div>';
		$msg = __('<strong>Warning!</strong> This version of Dynamic Content Gallery requires that your theme supports the WP Post Thumbnails feature.', DFCG_DOMAIN) . ' <a href="http://www.studiograsshopper.ch/dynamic-content-gallery/">' . __('Read more here.', DFCG_DOMAIN) . '</a>';
		echo $msg_start . $msg . $msg_end;
	}
}


/**
* Function to display Admin Notices after Settings Page reset
*
* Displays Admin Notices after Settings are reset
*
* Hooked to 'admin_notices' action
*
* @global array $dfcg_options db main plugin options
* @since 3.0
*/	
function dfcg_admin_notice_reset() {
	
	global $dfcg_options;
	
	if( $dfcg_options['just-reset'] == 'true' ) {
	
		echo '<div id="message" class="updated fade" style="background-color:#ecfcde; border:1px solid #a7c886;"><p><strong>' . __('Dynamic Content Gallery Settings have been reset to default settings.', DFCG_DOMAIN) . '</strong></p></div>';

		// Reset just-reset to false and update db options
		$dfcg_options['just-reset'] = 'false';
		update_option('dfcg_plugin_settings', $dfcg_options);
	}
}



/***** Options handling and upgrading *****/

/**
* Function for building default options
*
* Contains the latest version's default options.
* Populates the options on first install (not upgrade) and
* when Settings Reset is performed.
*
* Used by the "upgrader" function dfcg_set_gallery_options().
*
* 86 options (6 are WP only)
*
* @since 3.2.2
* @updated 3.3.4
*/
function dfcg_default_options() {
	// Add WP/WPMU options - we'll deal with the differences in the Admin screens
	$default_options = array(
		'populate-method' => 'one-category',					// Populate method for how the plugin works - since 2.3: multi-option, one-category, 'id-method, custom-post
		'cat-display' => '1',									// one-category: the ID of the selected category - since 2.3
		'posts-number' => '5',									// one-category: the number of posts to display - since 2.3
		'cat01' => '1',											// multi-option: the category IDs
		'cat02' => '1',											// multi-option: the category IDs
		'cat03' => '1',											// multi-option: the category IDs
		'cat04' => '1',											// multi-option: the category IDs
		'cat05' => '1',											// multi-option: the category IDs
		'cat06' => '1',											// multi-option: the category IDs
		'cat07' => '1',											// multi-option: the category IDs
		'cat08' => '1',											// multi-option: the category IDs
		'cat09' => '1',											// multi-option: the category IDs
		'off01' => '1',											// multi-option: the post select
		'off02' => '1',											// multi-option: the post select
		'off03' => '1',											// multi-option: the post select
		'off04' => '1',											// multi-option: the post select
		'off05' => '1',											// multi-option: the post select
		'off06' => '',											// multi-option: the post select
		'off07' => '',											// multi-option: the post select
		'off08' => '',											// multi-option: the post select
		'off09' => '',											// multi-option: the post select
		'ids-selected' => '',									// ID method: Page/Post ID's in comma separated list - since 2.3 (renamed in 3.3)
		'custom-post-type' => '',								// post-type: the Custom Post type selected
		'custom-post-type-number' => '5',						// post-type: the number of posts to display
		'custom-post-type-tax' => '',							// post-type: the ID of the post type taxonomy to display posts from
		'homeurl' => get_option('home'),						// Stored, but not currently used...
		'image-url-type' => 'auto',								// WP only. All methods: URL type for dfcg-images - since 2.3: full, partial, auto (added 3.3)
		'imageurl' => '',										// WP only. All methods: URL for partial custom images
		'defimgmulti' => '',									// WP only. Multi-option: URL for default category image folder
		'defimgonecat' => '',									// WP only. One-category: URL for default category image folder
		'defimgid' => '',										// WP only. ID Method: URL for a default image
		'defimgcustompost' => '',								// WP only. Post-type: URL for default custom category image folder
		'defimagedesc' => '',									// all methods: default description
		'gallery-width' => '460',								// all methods: CSS
		'gallery-height' => '250',								// all methods: CSS
		'slide-height' => '50',									// all methods: CSS - mootools only
		'gallery-border-thick' => '0',							// all methods: CSS
		'gallery-border-colour' => '#000000',					// all methods: CSS
		'slide-h2-size' => '12',								// all methods: CSS
		'slide-h2-padtb' => '0',								// all methods: CSS
		'slide-h2-padlr' => '0',								// all methods: CSS
		'slide-h2-marglr' => '5',								// all methods: CSS
		'slide-h2-margtb' => '2',								// all methods: CSS
		'slide-h2-colour' => '#FFFFFF',							// all methods: CSS
		'slide-p-size' => '11',									// all methods: CSS
		'slide-p-padtb' => '0',									// all methods: CSS
		'slide-p-padlr' => '0',									// all methods: CSS
		'slide-p-marglr' => '5',								// all methods: CSS
		'slide-p-margtb' => '2',								// all methods: CSS
		'slide-p-colour' => '#FFFFFF',							// all methods: CSS
		'slide-h2-weight' => 'bold',							// all methods: CSS bold, normal
		'slide-p-line-height' => '14',							// all methods: CSS
		'slide-overlay-color' => '#000000',						// all methods: CSS
		'slide-p-a-color' => '#FFFFFF',							// all methods: More text CSS
		'slide-p-ahover-color' => '#FFFFFF',					// all methods: More text CSS
		'slide-p-a-weight' => 'normal',							// all methods: More text CSS: bold, normal
		'slide-p-ahover-weight' => 'bold',						// all methods: More text CSS: bold, normal
		'reset' => '0',											// Settings: Reset options state
		'mootools' => '0',										// Settings: Toggle on/off Mootools loading - mootools only
		'limit-scripts' => 'homepage',							// Settings: Select scripts loading: homepage, pagetemplate, other
		'page-filename' => '',									// Settings: Specify a Page Template filename, for loading scripts
		'timed' => 'true',										// JS option
		'delay' => '9000',										// JS option
		'showCarousel' => 'true',								// JS option
		'showInfopane' => 'true',								// JS option 
		'slideInfoZoneSlide' => 'true',							// JS option - mootools only
		'slideInfoZoneOpacity' => '0.7',						// JS option
		'textShowCarousel' => 'Featured Articles',				// JS option
		'defaultTransition' => 'fade',							// JS option - mootools only
		'errors' => 'false',									// all methods: Error reporting on/off
		'posts-column' => 'true',								// all methods: Show edit posts column dfcg-image
		'pages-column' => 'true',								// all methods: Show edit pages column dfcg-image
		'posts-desc-column' => 'true',							// all methods: Show edit pages column dfcg-desc
		'pages-desc-column' => 'true',							// all methods: Show edit pages column dfcg-desc
		'just-reset' => 'false',								// all methods: Used for controlling admin_notices messages
		'scripts' => 'mootools',								// all methods: Selects js framework: mootools, jquery
		'desc-method' => 'manual',								// all methods: Select how to display descriptions: manual, auto, none
		'max-char' => '100',									// all methods: No. of characters for custom excerpt
		'more-text' => '[more]',								// all methods: More text for custom excerpt
		'pages-sort-column' => 'true',							// ID: Show edit pages column _dfcg-sort: bool
		'id-sort-control' => 'false',							// ID: Allow custom sort of images using _dfcg-sort: bool
		'page-ids' => '',										// Restrict scripts: ordinary page ID numbers
		'thumb-type' => 'legacy',								// all methods: post-thumbnails or legacy - mootools only
		'showArrows' => 'true',									// JS option
		'slideInfoZoneStatic' => 'false',						// JS option (jquery only) added with v2.6 jquery script
		'gallery-background' => '#000000'						// all methods: CSS
	);
	
	// Return options array for use elsewhere
	return $default_options;
}


/**
* Function for loading and upgrading options
*
* Loads options on 'admin_menu' hook.
* Completely re-written - changed to "incremental" upgrading in v3.3.3
*
* Called by dfcg_add_page() which is hooked to 'admin_menu'
*
* In 2.3 - "imagepath" is deprecated, replaced by "imageurl" in 2.3
* In 2.3 - "defimagepath" is deprecated, replaced by "defimgmulti" and "defimgonecat"
* In 2.3 - 29 orig options + 30 new options added , total now is 59
*
* In RC2 - Change: "nourl" value of "image-url-type" is deprecated
*
* In RC3 - Added 2: "posts-column", "pages-column" added
* In RC3 - Total options is 59 + 2 = 61
*
* In RC4 - Added 13: "posts-desc-column", "pages-desc-column", "just-reset", "scripts", 9 jQuery options
* In RC4 - Change: "part" value of "image-url-type" is changed to "partial"
* In RC4 - Total options is 61 + 13 = 74
*
* In 3.1 - Added 7: "desc-method", "max-char", "more-text", "slide-p-a-color", "slide-p-ahover-color", "slide-p-a-weight", "slide-p-ahover-weight"
* In 3.1 - Total options = 74 + 7 = 81
*
* In 3.2 - Change: "desc-method" can now have three values - auto, manual, none
* In 3.2 - Added 2: 'pages-sort-column', 'pages-sort-control'
* In 3.2 - Total options = 81 + 2 = 83
*
* In 3.2.2 - Added 1: 'page-ids'
* In 3.2.2 - Change: new value 'page' added to 'limit-scripts' option
* In 3.2.2 - Total options = 83 + 1 = 84
*
* In 3.3 - Change: new value 'auto' added to 'image-url-type' option
* In 3.3 - Change: 'pages-selected' option renamed as 'ids-selected' (handles Post and Page IDs)
* In 3.3 - Change: 'defimgpages' option renamed as 'defimgid'
* In 3.3 - Change: 'pages-sort-control' option renamed as 'id-sort-control'
* In 3.3 - Change: 'pages' value of "populate-method" is changed to 'id-method'
* In 3.3 - Deleted 6: 'nav-theme', 'pause-on-hover', 'transition-speed', 'fade-panels', 'slide-overlay-position', 'gallery-background'
* In 3.3 - Added 5: 'thumb-type' 'defimgcustompost', 'custom-post-type', 'custom-post-type-number', 'custom-post-type-tax'
* In 3.3 - Change: 'custom-post' value added to 'populate-method' option
* In 3.3 - Total options = 84 - 6 + 5 = 83
*
* In 3.3.1 - Corrected '==' syntax to '=' for new options that should have been added in 3.3. What an idiot,eh?
*
* In 3.3.2 - Added 1: 'showArrows' for mootools and jQuery
* In 3.3.2 - Total options = 83 + 1 = 84
*
* In 3.3.3 - Total options = 84
*
* In 3.3.4 - Added 'slideInfoZoneStatic' options for fixed or sliding Slide Pane with jQuery
* In 3.3.4 - Added 'gallery-background' option - mootools and jquery
*
* In 3.4.4 - Total options = 84 + 2 = 86
*
* In 3.3.5 - No change. Total options = 86
*
* @uses dfcg_default_options()
* @since 3.2.2
* @updated 3.3.5
*/
function dfcg_set_gallery_options() {
	
	// Get current version number (first introduced in 3.0 beta / 2.3)
	$existing_version = get_option('dfcg_version');
	
	// Existing version is same as this version - nothing to do here...
	if( $existing_version == DFCG_VER )
		return;
	
	
	/***** Ok, we need to do something - let's prepare some stuff *****/
	
	// Clean up version numbers, otherwise version_compare won't always work as expected
	if( $existing_version == '3.0 RC2' )
		$existing_version = '2.3.2';
		
	if( $existing_version == '3.0 RC3' )
		$existing_version = '2.3.3';
		
	if( $existing_version == '3.0 RC4' )
		$existing_version = '2.3.4';
	
	$postmeta_upgrade = get_option( 'dfcg_plugin_postmeta_upgrade' );
	$existing_opts = get_option( 'dfcg_plugin_settings' );



	/***** Clean install - it's a wasteland here *****/
	if ( empty( $existing_version ) && empty( $postmeta_upgrade ) && empty( $existing_opts ) ) {			
		
		$new_opts = dfcg_default_options();
		
		add_option( 'dfcg_plugin_settings', $new_opts );
		add_option( 'dfcg_version', DFCG_VER );
		
		$postmeta_upgrade = array();
		$postmeta_upgrade['upgraded'] = 'completed';
		add_option( 'dfcg_plugin_postmeta_upgrade', $postmeta_upgrade );
				
		return;
	}
	
	
	
	/***** Logic check in case $existing_version exists but there are no $existing_opts - eg bad uninstall *****/
	
	if( $existing_version && empty( $existing_opts ) ) {
		
		$new_opts = dfcg_default_options(); // Clean reinstall
		
		add_option( 'dfcg_plugin_settings', $new_opts );
		update_option( 'dfcg_version', DFCG_VER );
		
		// Check if postmeta was ever run
		if( $postmeta_upgrade['upgraded'] !== 'completed' ) {
			delete_option('dfcg_plugin_postmeta_upgrade'); // Force postmeta to be re-run when Settings page is loaded
		}
		
		return;
	}
	
	
	
	/***** Logic check in case $existing_version doesn't exist but there are $existing_opts *****/
	
	if( empty( $existing_version ) && $existing_opts ) {
		$existing_version = '2.2'; // Force upgrades to be run
	}
	
	
	/***** Upgrade to 2.3 from 2.2 *****/
	if ( version_compare($existing_version, '2.3', '<') ) {
	
		// 29 options
		//$existing = get_option( 'dfcg_plugin_settings' );
		
		// Add 1 new option - Assign old imagepath to new imageurl
		$existing_opts['imageurl'] = $existing_opts['homeurl'] . $existing_opts['imagepath'];
		
		// Add 2 new options - Assign old defimagepath to defimgmulti and defimgonecat
		$existing_opts['defimgmulti'] = $existing_opts['homeurl'] . $existing_opts['defimagepath'];
		$existing_opts['defimgonecat'] = $existing_opts['homeurl'] . $existing_opts['defimagepath'];
		
		// Delete 2 options
		unset($existing_opts['imagepath']);
		unset($existing_opts['defimagepath']);
		
		
		// Add new 29 options
		$new_opts = array(
			'populate-method' => 'multi-option',
			'cat-display' => '1',
			'posts-number' => '5',
			'pages-selected' => '',
			'image-url-type' => 'partial',
			'defimgpages' => '',
			'slide-h2-padtb' => '0',
			'slide-h2-padlr' => '0',
			'slide-p-padtb' => '0',
			'slide-p-padlr' => '0',
			'limit-scripts' => 'homepage',
			'page-filename' => '',
			'timed' => 'true',
			'delay' => '9000',
			'showCarousel' => 'true',
			'showInfopane' => 'true',
			'slideInfoZoneSlide' => 'true',
			'slideInfoZoneOpacity' => '0.7',
			'textShowCarousel' => 'Featured Articles',
			'defaultTransition' => 'fade',
			'cat06' => '1',
			'cat07' => '1',
			'cat08' => '1',
			'cat09' => '1',
			'off06' => '',
			'off07' => '',
			'off08' => '',
			'off09' => '',
			'errors' => 'true'
			);
		
		// Total options = 29 + 1 + 2 - 2 + 29 = 59
		$updated = wp_parse_args( $existing_opts, $new_opts );
		
		update_option( 'dfcg_plugin_settings', $updated );
	}
	
	
	
	/***** Upgrade to 3.0 RC2 (2.3.2) from 2.3 (aka 3.0 beta) *****/
	if ( version_compare($existing_version, '2.3.2', '<') ) {
	
		// 59 options
		$existing_opts = get_option( 'dfcg_plugin_settings' );
		
		// Value 'nourl' is deprecated
		if( $existing_opts['image-url-type'] == 'nourl' )
			$existing_opts['image-url-type'] = 'part';

		// Total options = 59
		update_option( 'dfcg_plugin_settings', $existing_opts );
	}
	
	
	
	/***** Upgrade to 3.0 RC3 (2.3.3) from 3.0 RC2 *****/
	if ( version_compare($existing_version, '2.3.3', '<') ) {
	
		// 59 options
		$existing_opts = get_option( 'dfcg_plugin_settings' );
	
		// Add new 2 options
		$new_opts = array(
			'posts-column' => 'true',
			'pages-column' => 'true'
			);
		
		// Total options = 59 + 2 = 61
		$updated = wp_parse_args( $existing_opts, $new_opts );
		
		update_option( 'dfcg_plugin_settings', $updated );
	}
	
	
	
	/***** Upgrade to 3.0 RC4 (2.3.4) from 3.0 RC3 *****/
	if ( version_compare($existing_version, '2.3.4', '<') ) {
	
		// 61 options
		$existing_opts = get_option( 'dfcg_plugin_settings' );
		
		// 'part' changed to 'partial'
		if( $existing_opts['image-url-type'] == 'part' )
			$existing_opts['image-url-type'] = 'partial';
		
		// Add new 13 options
		$new_opts = array(
			'posts-desc-column' => 'true',
			'pages-desc-column' => 'true',
			'just-reset' => 'false',
			'scripts' => 'mootools',
			'slide-h2-weight' => 'bold',							
			'slide-p-line-height' => '14',
			'slide-overlay-color' => '#000000',
			'slide-overlay-position' => 'bottom',
			'transition-speed' => '1500',
			'nav-theme' => 'light',
			'pause-on-hover' => 'true',
			'fade-panels' => 'true',
			'gallery-background' => '#000000'
		);
		
		// Total options = 61 + 13 = 74
		$updated = wp_parse_args( $existing_opts, $new_opts );
		
		update_option( 'dfcg_plugin_settings', $updated );
	}
	
	
	
	/***** Upgrade to 3.0 from 3.0 RC4 *****/
	if ( version_compare($existing_version, '3.0', '<') ) {
		
		// Nothing to do here...
	}
	
	
	
	/***** Upgrade to 3.1 from 3.0 *****/
	if ( version_compare($existing_version, '3.1', '<') ) {
	
		// 74 options
		$existing_opts = get_option( 'dfcg_plugin_settings' );
		
		// Add new 7 options
		$new_opts = array(
			'desc-method' => 'manual',
			'max-char' => '100',
			'more-text' => '[more]',
			'slide-p-a-color' => '#FFFFFF',
			'slide-p-ahover-color' => '#FFFFFF',
			'slide-p-a-weight' => 'normal',
			'slide-p-ahover-weight' => 'bold'
			);
			
		// Total options = 74 + 7 = 81
		$updated = wp_parse_args( $existing_opts, $new_opts );
		
		update_option( 'dfcg_plugin_settings', $updated );
	}
		
	
	
	/***** Upgrade to 3.2 from 3.1 *****/
	if ( version_compare($existing_version, '3.2', '<') ) {
	
		// 81 options
		$existing_opts = get_option( 'dfcg_plugin_settings' );
		
		// Add new 2 options
		$new_opts = array(
			'pages-sort-column' => 'true',
			'pages-sort-control' => 'false'
			);
		
		// Total options = 81 + 2 = 83
		$updated = wp_parse_args( $existing_opts, $new_opts );
		
		update_option( 'dfcg_plugin_settings', $updated );
	}
	
	
	
	/***** Upgrade to 3.2.1 from 3.2 *****/
	if ( version_compare($existing_version, '3.2.1', '<') ) {
		
		// Nothing to do here...
	}
	
	
	
	/***** Upgrade to 3.2.2 from 3.2.1 *****/
	if ( version_compare($existing_version, '3.2.2', '<') ) {
	
		// 83 options
		$existing_opts = get_option( 'dfcg_plugin_settings' );
		
		// Add new 1 option
		$new_opts = array(
			'page-ids' => ''
			);
	
		// Total options = 83 + 1 = 84
		$updated = wp_parse_args( $existing_opts, $new_opts );
		
		update_option( 'dfcg_plugin_settings', $updated );
	}
	
	
	
	/***** Upgrade to 3.2.3 from 3.2.2 *****/
	if ( version_compare($existing_version, '3.2.3', '<') ) {
		
		// Nothing to do here...
	}
	
	
	
	/***** Upgrade to 3.3 from 3.2.3 *****/
	if ( version_compare($existing_version, '3.3', '<') ) {
	
		// 84 options
		$existing_opts = get_option( 'dfcg_plugin_settings' );
		
				
		// Add new 3 options = renamed old options
		$existing_opts['ids-selected'] = $existing_opts['pages-selected'];
		$existing_opts['defimgid'] = $existing_opts['defimgpages'];
		$existing_opts['id-sort-control'] = $existing_opts['pages-sort-control'];
		
		// 'pages' changed to 'id-method'
		if( $existing_opts['populate-method'] == 'pages' ) {
			$existing_opts['populate-method'] = 'id-method';
		}
		
		// Delete 3 deprecated options (renamed in 3.3)
		unset( $existing_opts['pages-selected'] );
		unset( $existing_opts['defimgpages'] );
		unset( $existing_opts['pages-sort-control'] );
		
		// Delete 6 deprecated options
		unset($existing_opts['nav-theme']);
		unset($existing_opts['pause-on-hover']);
		unset($existing_opts['transition-speed']);
		unset($existing_opts['fade-panels']);
		unset($existing_opts['slide-overlay-position']);
		unset($existing_opts['gallery-background']);
		
		// Add new 5 options
		$new_opts = array(
			'thumb-type' => 'legacy',
			'custom-post-type' => '',
			'custom-post-type-tax' => '',
			'custom-post-type-number' => '5',
			'defimgcustompost' => ''
			);

		// Total options = 84 + 3 - 3 - 6 + 5 = 83
		$updated = wp_parse_args( $existing_opts, $new_opts );
		
		update_option( 'dfcg_plugin_settings', $updated );
	}
		
		
	
	/***** Upgrade to 3.3.1 from 3.3 *****/
	if ( version_compare($existing_version, '3.3.1', '<') ) {
		
		// Nothing to do here...
	}
	
	
	
	/***** Upgrade to 3.3.2 from 3.3.1 *****/
	if ( version_compare($existing_version, '3.3.2', '<') ) {
	
		// 83 options
		$existing_opts = get_option( 'dfcg_plugin_settings' );
	
		// Add new 1 options
		$new_opts = array(
			'showArrows' => 'true'
			);
		
		// Total options = 83 + 1 = 84
		$updated = wp_parse_args( $existing_opts, $new_opts );
		
		update_option( 'dfcg_plugin_settings', $updated );
	}
	
	
	
	/***** Upgrade to 3.3.3 from 3.3.2 *****/
	if ( version_compare($existing_version, '3.3.3', '<') ) {
	
		// Nothing to do here...
	}
	
	
	
	/***** Upgrade to 3.3.4 from 3.3.3 *****/
	if ( version_compare($existing_version, '3.3.4', '<') ) {
	
		// 84 options
		$existing_opts = get_option( 'dfcg_plugin_settings' );
	
		// Add new 1 option
		$new_opts = array(
			'slideInfoZoneStatic' => 'false',
			'gallery-background' => '#000000'
			);
		
		// Total options = 84 + 2 = 85
		$updated = wp_parse_args( $existing_opts, $new_opts );
		
		update_option( 'dfcg_plugin_settings', $updated );
	}
	
	
	
	/***** Upgrade to 3.3.5 from 3.3.4 *****/
	if ( version_compare($existing_version, '3.3.5', '<') ) {
	
		// Nothing to do here...
	}
	
	
	
	// FINALLY, Update version no. in the db
	update_option('dfcg_version', DFCG_VER );
}