<?php
/**
* Functions for displaying contents of Settings page
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info These are the functions which produce the contents of the UI tabs and Settings page
*
* @info Functions ending _wp are only used for Wordpress
* @info Functions ending _wpmu are only used for WPMU
*
* @since 3.0
*/


/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.') );
}



/**
* Get list of all Custom Post Types
*
* @since 3.3
**/
function dfcg_get_custom_post_types() {
	
	$args=array(
  		'public'   => true,
  		'_builtin' => false
		); 
	$output = 'objects'; // names or objects
	$operator = 'and'; // 'and' or 'or'
	$post_types = get_post_types($args, $output, $operator);
	
	return $post_types; // An object
}



/**
* Active Settings display
*
* @global array $dfcg_options plugin options from db
* @since 3.2.2
* @updated 3.3
*/
function dfcg_ui_active_settings() {

	global $dfcg_options;
	
	$sep = ' | ';
	
	// Heading
	$output = '<h3>' . __('Your current key Settings: ', DFCG_DOMAIN) . '</h3>';
	
	$output .= '<p>' . __('Please provide this information if posting a question on the <a href="http://www.studiograsshopper.ch/forum/">Support Forum</a>.', DFCG_DOMAIN) . '</p>';
	
	// Image File Management
	if( $dfcg_options['image-url-type'] == 'auto' ) {
		$link_text = '';
	} else {
		$link_text = ' URL';
	}
	
	$output .= '<a class="dfcg-panel-image-link" href="#dfcg-panel-image">' . __('Image Management', DFCG_DOMAIN) . '</a>: <span class="key_settings">' . $dfcg_options['image-url-type'] . $link_text . '</span>';
	
	if( $dfcg_options['image-url-type'] == 'partial' ) {
		
		$output .= $sep . __('Images folder is: ', DFCG_DOMAIN);
		
		if( !empty( $dfcg_options['imageurl'] ) ) {
			$output .= '<span class="key_settings">' . $dfcg_options['imageurl'];
		} else {
			$output .= '<span class="key_settings">' . __('not defined', DFCG_DOMAIN);
		}
	
	$output .= '</span><br />';
	
	} else {
	
		$output .= '<br />';
	}
	
	// Thumbnails shown in Carousel
	$output .= '<a class="dfcg-panel-image-link" href="#dfcg-panel-image">' . __('Carousel Thumbnails', DFCG_DOMAIN) . '</a>: <span class="key_settings">' . $dfcg_options['thumb-type'] . '</span>';
	
	$output .= '<br />';
	
	
	// Gallery Method
	$output .= '<a class="dfcg-panel-gallery-link" href="#dfcg-panel-gallery">' . __('Gallery Method', DFCG_DOMAIN) . '</a>: <span class="key_settings">' . $dfcg_options['populate-method'] . '</span>';
	
	if( !function_exists('wpmu_create_blog') ) {
	
		$subhead = $sep . __('Default Images folder is: ', DFCG_DOMAIN) . '<span class="key_settings">';
		
		// TODO: Sort out a message if default image URL has not been defined
		
		if( $dfcg_options['populate-method'] == 'multi-option' ) {
			if( $dfcg_options['defimgmulti'] ) {
				$output .= $subhead . $dfcg_options['defimgmulti'];
			} else {
				$output .= $subhead . __('not defined', DFCG_DOMAIN);
			}
		
		} elseif( $dfcg_options['populate-method'] == 'one-category' ) {
			if( $dfcg_options['defimgonecat'] ) {
				$output .= $subhead . $dfcg_options['defimgonecat'];
			} else {
				$output .= $subhead . __('not defined', DFCG_DOMAIN);
			}
			
		} elseif( $dfcg_options['populate-method'] == 'custom-post' ) {
			if( $dfcg_options['defimgcustompost'] ) {
				$output .= $subhead . $dfcg_options['defimgcustompost'];
			} else {
				$output .= $subhead . __('not defined', DFCG_DOMAIN);
			}
		
		} elseif( $dfcg_options['defimgid'] ) {
			$output .= $sep . __('Default image is: ', DFCG_DOMAIN) . '<span class="key_settings">' . $dfcg_options['defimgid'];
		
		} else {
			$output .= $sep . __('Default image is: ', DFCG_DOMAIN) . '<span class="key_settings">' . __('not defined', DFCG_DOMAIN);
		}
		
		$output .= '</span><br />';
	
	} else {
	
		$output .= '<br />';
	
	}
	
	// Slide Pane Descriptions
	$output .= '<a class="dfcg-panel-desc-link" href="#default-desc">' . __('Descriptions', DFCG_DOMAIN) . '</a>: <span class="key_settings">' . $dfcg_options['desc-method'] . '</span><br />';
	
	
	// Script framework
	$output .= '<a class="dfcg-panel-javascript-link" href="#gallery-js-scripts">' . __('Javascript Options', DFCG_DOMAIN) . '</a>: <span class="key_settings">' . $dfcg_options['scripts'] . '</span><br />';
	
	// Restrict Scripts
	$output .= '<a class="dfcg-panel-scripts-link" href="#restrict-scripts">' . __('Load Scripts', DFCG_DOMAIN) . '</a>: <span class="key_settings">';
	
	if( $dfcg_options['limit-scripts'] == 'homepage' ) {
		$output .= __('Home Page', DFCG_DOMAIN);
	
	} elseif( $dfcg_options['limit-scripts'] == 'page' ) {
		$output .= __('Page ID => ', DFCG_DOMAIN) . $dfcg_options['page-ids'];
		
	} elseif( $dfcg_options['limit-scripts'] == 'pagetemplate' ) {
		$output .= __('Page Template => ', DFCG_DOMAIN) . $dfcg_options['page-filename'];
		
	} else {
		$output .= __('All pages', DFCG_DOMAIN);
		
	}
	
	$output .= '</span><br />';
	
	
	// Error Messages
	$output .= '<a class="dfcg-panel-tools-link" href="#error-messages">' . __('Tools - Error Message options', DFCG_DOMAIN) . '</a>: <span class="key_settings">';
		
	if( $dfcg_options['errors'] ) {
		$output .= __('on', DFCG_DOMAIN);
	
	} else {
		$output .= __('off', DFCG_DOMAIN);
		
	}
	
	$output .= '</span>';
	
	echo $output;
}


/**
* Intro box: menu and holder
*
* @uses dfcg_ui_intro_text()
* @uses dfcg_ui_sgr_info()
*
* @global array $dfcg_options plugin options from db
* @since 3.0
* @updated 3.3
*/
function dfcg_ui_intro_menu() {
	global $dfcg_options;
	?>

	<div style="float:left;width:690px;">
		<h3><?php esc_html_e("General Information:", DFCG_DOMAIN); ?></h3>
		<p><?php esc_html_e('Please go through the options in each tab and configure the plugin Settings. To get a general overview of how the plugin works, read the', DFCG_DOMAIN); ?> <a class="dfcg-panel-help-link" href="#dfcg-panel-help">Help tab</a>.</p>
						
		<div class="dfcg-info">
			<h3><?php esc_html_e('Validation messages:', DFCG_DOMAIN); ?></h3>
			<p><em><?php esc_html_e('After saving Settings, the plugin generates validation messages at the top of this page in the event of configuration errors. Messages in red must be fixed, otherwise the gallery will not display. Messages in yellow mean that the gallery will display, but you are not taking advantage of the default images feature.', DFCG_DOMAIN); ?></em></p>

			<h3><?php esc_html_e('Tips and Important Notes:', DFCG_DOMAIN); ?></h3>
			<p><em><?php esc_html_e('To help you understand the various options as you work through the Settings tabs, Tips and Important Notes are available as javascript popups, indicated as follows:', DFCG_DOMAIN); ?><br />
			<a class="load-local" href="#dfcg-tip-gen-tip" rel="#dfcg-tip-gen-tip" title="<?php esc_attr_e('Tip: Example', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="Tip" /></a><?php esc_html_e('a Tip', DFCG_DOMAIN); ?><br />
			<a class="load-local" href="#dfcg-tip-gen-note" rel="#dfcg-tip-gen-note" title="<?php esc_attr_e('Important Note: Example', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/exclamation.png'; ?>" alt="Important Note" /></a><?php esc_html_e('an Important Note - please read it!', DFCG_DOMAIN); ?></em>
			</p>
				
			<?php dfcg_ui_active_settings(); ?>
		</div>
					
	</div>
					
	<?php dfcg_ui_sgr_info(); ?>
										
	<div style="clear:both;"></div>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-gen-tip">
		<p><?php esc_attr_e('This is a Tip. It will contain useful info to help you understand the relevant option.', DFCG_DOMAIN); ?></p>
	</div>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-gen-note">
		<p><?php esc_attr_e('This is an Important Note. This will contain important information concerning this option which you should read.', DFCG_DOMAIN); ?></p>
	</div>
	
<?php }


/**
* Resources inner box: content
*
* @since 3.0
* @updated 3.3
*/
function dfcg_ui_sgr_info() {
?>
<div id="sgr-info">	
	<h3><?php _e('Resources & Support', DFCG_DOMAIN); ?></h3>
	
	<p><a href="http://www.studiograsshopper.ch"><img src="<?php echo DFCG_URL . '/admin-assets/sgr_icon_75.jpg'; ?>" alt="studiograsshopper" /></a><strong><?php _e('Dynamic Content Gallery for WordPress', DFCG_DOMAIN); ?></strong>.<br /><?php _e('Version', DFCG_DOMAIN); ?> <?php echo DFCG_VER; ?><br /><?php _e('Author', DFCG_DOMAIN); ?>: <a href="http://www.studiograsshopper.ch/">Ade Walker</a></p>
	
	<p><?php _e('For further information, or in case of configuration problems, please consult these comprehensive resources:', DFCG_DOMAIN); ?></p>
	<ul>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/"><?php _e('Plugin Home page', DFCG_DOMAIN); ?></a></li>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/"><?php _e('Configuration guide', DFCG_DOMAIN); ?></a></li>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/documentation/"><?php _e('Documentation', DFCG_DOMAIN); ?></a></li>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/faq/"><?php _e('FAQ', DFCG_DOMAIN); ?></a></li>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/error-messages/"><?php _e('Error messages', DFCG_DOMAIN); ?></a></li>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/forum/"><?php _e('Support Forum', DFCG_DOMAIN); ?></a></li>
	</ul>
	<p><?php _e('If you have found this plugin useful, please consider making a', DFCG_DOMAIN); ?> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=10131319"><?php _e('donation', DFCG_DOMAIN); ?></a> <?php _e('to help support future development. Your support will be much appreciated. Thank you!', DFCG_DOMAIN); ?></p>
	
</div><!-- end sgr-info -->
<?php }


/**
* Image File Management: box and content
*
* 3 options: ['image-url-type'], ['imageurl'], ['thumb-type']
* WPMS: 1 hidden ['imageurl']
* Mootools only: ['thumb-type']
*
* @global array $dfcg_options plugin options from db
* @since 3.3
*/
function dfcg_ui_1_image() {
	global $dfcg_options;
	?>
	
	<h3><?php esc_html_e('Image management (REQUIRED):', DFCG_DOMAIN); ?></h3>
	<p><?php esc_html_e('Complete the following settings to set up your gallery image file management preferences.', DFCG_DOMAIN); ?> <em><?php _e('Further information about these settings can be found in the', DFCG_DOMAIN); ?> <a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/"><?php esc_html_e('Configuration guide', DFCG_DOMAIN); ?></a>.</em></p>
	
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[image-url-type]" id="dfcg-autourl" type="radio" style="margin-right:5px;" value="auto" <?php checked('auto', $dfcg_options['image-url-type']); ?> />
				<label for="dfcg-autourl"><?php _e('Auto (Default)', DFCG_DOMAIN); ?></label></th>
				<td><p><?php esc_html_e('Gallery will automatically pull in the first image attachment from the Post.', DFCG_DOMAIN); ?><br />
				<?php esc_html_e('Select this option if you want automatic images.', DFCG_DOMAIN); ?>
				<a class="load-local" href="#dfcg-tip-im-auto" rel="#dfcg-tip-im-auto" title="<?php esc_attr_e('Tip: Auto', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></p></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[image-url-type]" id="dfcg-fullurl" type="radio" style="margin-right:5px;" value="full" <?php checked('full', $dfcg_options['image-url-type']); ?> />
				<label for="dfcg-fullurl"><?php esc_html_e('Full URL', DFCG_DOMAIN); ?></label></th>
				<td><p><?php esc_html_e('DCG Metabox requires the Image URL in this format:', DFCG_DOMAIN); ?> <span class="bold-italic">http://www.yourdomain.com/folder/anotherfolder/myimage.jpg</span><br />
				<?php _e('Select this option if you want complete freedom to reference images anywhere in your site and in multiple locations.', DFCG_DOMAIN); ?>
				<a class="load-local" href="#dfcg-tip-im-full" rel="#dfcg-tip-im-full" title="<?php esc_attr_e('Tip: Full URL', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></p></td>
			</tr>
			
			<?php if ( !function_exists('wpmu_create_blog') ) : // we're in normal WP ?>
			
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[image-url-type]" id="dfcg-parturl" type="radio" style="margin-right:5px;" value="partial" <?php checked('partial', $dfcg_options['image-url-type']); ?> />
				<label for="dfcg-parturl"><?php _e('Partial URL', DFCG_DOMAIN); ?></label></th>
				<td><p><?php _e('DCG Metabox requires the <strong>Image URL</strong> in this format (for example): ', DFCG_DOMAIN); ?><span class="bold-italic">subfoldername/myimage.jpg</span><br />
				<?php _e('Select this option if your images are organised into many sub-folders within one main folder. The URL to the main folder is entered in the field below.', DFCG_DOMAIN); ?></p>
				<?php _e('URL to images folder:', DFCG_DOMAIN); ?> <input name="dfcg_plugin_settings[imageurl]" id="dfcg-imageurl" size="75" value="<?php echo $dfcg_options['imageurl']; ?>" /><a class="load-local" href="#dfcg-tip-im-part" rel="#dfcg-tip-im-part" title="<?php esc_attr_e('Tip: Partial URL', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a>
</td>
			</tr>
			
			<?php endif; ?>
		
		</tbody>
	</table>
	
	<?php if( $dfcg_options['scripts'] == 'mootools' ) : ?>
	
	<h3><?php _e('Carousel Thumbnails', DFCG_DOMAIN); ?></h3>
	<p><?php _e('This option only applies if you are using the Mootools javascript framework. Select "Post Thumbnails" to automatically create resized thumbnails from your Posts\' or Pages\' "Featured Image", or select "Legacy" to use the old way of displaying thumbnails, ie using the main image (not resized) as the thumbnail.', DFCG_DOMAIN); ?></p>
	<table>
		<tbody>
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[thumb-type]" id="dfcg-thumb-type-post-thumbnails" type="radio" style="margin-right:5px;" value="post-thumbnails" <?php checked('post-thumbnails', $dfcg_options['thumb-type']); ?> />
				<label for="dfcg-thumb-type-post-thumbnails"><?php _e('Use Post Thumbnails', DFCG_DOMAIN); ?></label></th>
				<td><p><?php _e('The carousel thumbnail will use a resized version of the Featured Image set in the Write Posts/Pages screens.', DFCG_DOMAIN); ?></p></td>
			</tr>
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[thumb-type]" id="dfcg-thumb-type-legacy" type="radio" style="margin-right:5px;" value="legacy" <?php checked('legacy', $dfcg_options['thumb-type']); ?> />
				<label for="dfcg-thumb-type-legacy"><?php _e('Legacy', DFCG_DOMAIN); ?></label></th>
				<td><p><?php _e('The carousel thumbnail will use a cropped version of the main gallery image.', DFCG_DOMAIN); ?></p></td>
			</tr>
		</tbody>
	</table>
	<?php endif; ?>
	
	<?php if ( function_exists('wpmu_create_blog') ) : // we're in WPMS ?>
	<h3><?php _e('Uploading your images', DFCG_DOMAIN); ?></h3>
	<p><?php _e('Use the Media Uploader in the Write Post/Page screen to upload your gallery images. With the Media Uploader pop-up open, select "Choose Files to Upload" and browse to your chosen image. Once the Media Uploader screen has uploaded your file and finished "crunching", copy the URL shown in the "File URL" box and paste it in to the <strong>Image URL</strong> field in the DCG Metabox in the Write Post/Page screen.', DFCG_DOMAIN); ?></p>
	<?php endif; ?>
	
		
	<div class="dfcg-tip-hidden" id="dfcg-tip-im-auto">
		<p><?php _e('Images will be pulled automatically from the selected Posts or Pages. The image displayed will be the first Image Attachment for the relevent Post or Pages, therefore, in order for this to work, you must first attach one image to each relevant Post or Page.', DFCG_DOMAIN); ?></p>
		<p><?php _e('This is the default option and best one to use if you are using the plugin for the first time.', DFCG_DOMAIN); ?></p>
	</div>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-im-full">
		<p><?php esc_attr_e('This is the best "manual" option.', DFCG_DOMAIN); ?></p>
		<p><?php esc_attr_e('Associate images with the DCG by pasting the relevant image URL into the DCG Metabox Image URL field in the Write Post/Write Page screen.', DFCG_DOMAIN); ?></p>
		<p><?php esc_attr_e('Select this option if you keep images in many different directories both inside and outside of the /wp-content/uploads folder.', DFCG_DOMAIN); ?></p>
		<p><?php esc_attr_e('This was the default Image Management option for earlier versions of this plugin (pre version 3.3).', DFCG_DOMAIN); ?></p>
		<p><?php esc_attr_e('Note: select this option if your images are stored off-site eg Flickr, Picasa etc.', DFCG_DOMAIN); ?></p>
	</div>

	<?php // Need to hide these tool-tips if in WPMS
	if ( !function_exists('wpmu_create_blog') ) : ?>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-im-part">
		<p><?php esc_attr_e('This option is no longer recommended, and is only kept for backwards compatibility for those users who already use this option.', DFCG_DOMAIN); ?></p>
		<p><?php _e('If you selected <strong>Partial URL</strong> you must also specify the URL to the top-level folder which contains the relevant sub-folders and images.', DFCG_DOMAIN); ?></p><p><?php _e('Include your domain name in this URL, for example:', DFCG_DOMAIN); ?> <span class="bold-italic">http://www.yourdomain.com/myspecial_image_folder/</span></p>
	</div>
	
	<?php endif; ?>
	
<?php }


/**
* Gallery Method: box and content
*
* 1 options: ['populate-method']
*
* @global array $dfcg_options plugin options from db
* @since 3.3
*/
function dfcg_ui_2_method() {
	global $dfcg_options;
	?>
	<h3 class="top" id="gallery-method"><?php _e('Gallery Method (REQUIRED):', DFCG_DOMAIN); ?></h3>
	
	<p><?php _e('The Dynamic Content Gallery offers various methods for populating the gallery with images. Select the option most appropriate for your needs, then set up your chosen method\'s options in the relevant section below.', DFCG_DOMAIN); ?></p>

	<table class="optiontable form-table">
		<tbody>
			
			<tr valign="top">
				<th scope="row">
					<input name="dfcg_plugin_settings[populate-method]" id="dfcg-populate-multi" type="radio" style="margin-right:5px;" value="multi-option" <?php checked('multi-option', $dfcg_options['populate-method']); ?> />
					<label for="dfcg-populate-multi"><?php _e('Multi Option', DFCG_DOMAIN); ?></label>
					<a class="load-local" href="#dfcg-tip-gm-mo" rel="#dfcg-tip-gm-mo" title="<?php esc_attr_e('Tip: Multi Option Method', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a>
				</th>
				
				<td><p><?php _e('Complete freedom to select up to 9 images from a mix of categories. Set up the relevant options in', DFCG_DOMAIN); ?> <a href="#multi-option">MULTI OPTION <?php _e('Settings', DFCG_DOMAIN); ?></a></p></td>
			
			</tr>
			
			<tr valign="top">
				<th scope="row">
					<input name="dfcg_plugin_settings[populate-method]" id="dfcg-populate-one" type="radio" style="margin-right:5px;"  value="one-category" <?php checked('one-category', $dfcg_options['populate-method']); ?> />
					<label for="dfcg-populate-one"><?php _e('One Category', DFCG_DOMAIN); ?></label>
					<a class="load-local" href="#dfcg-tip-gm-oc" rel="#dfcg-tip-gm-oc" title="<?php esc_attr_e('Tip: One Category Method', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a>
				</th>
				
				<td><p><?php _e('Images are pulled from a user-definable number of Posts from one selected Category. Set up the relevant options in', DFCG_DOMAIN); ?> <a href="#one-category">ONE CATEGORY <?php _e('Settings', DFCG_DOMAIN); ?></a></p></td>
			
			</tr>
			
			<tr valign="top">
				<th scope="row">
					<input name="dfcg_plugin_settings[populate-method]" id="dfcg-populate-id-method" type="radio" style="margin-right:5px;" value="id-method" <?php checked('id-method', $dfcg_options['populate-method']); ?> />
					<label for="dfcg-populate-id-method"><?php _e('ID Method', DFCG_DOMAIN); ?></label>
					<a class="load-local" href="#dfcg-tip-gm-id" rel="#dfcg-tip-gm-id" title="<?php esc_attr_e('Tip: ID Method', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a>
				</th>
				
				<td><?php _e('Images are pulled from specific Pages and/or Posts selected using their ID numbers. Set up the relevant options in', DFCG_DOMAIN); ?> <a href="#id-method">ID Method <?php _e('Settings', DFCG_DOMAIN); ?></a></td>
			
			</tr>
			
			<?php
			$post_types = dfcg_get_custom_post_types();
			
			if( empty($post_types) ) : ?>
			
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[populate-method]" id="dfcg-populate-custom-post" type="radio" style="margin-right:5px;" value="custom-post" disabled="disabled" />
					<label for="dfcg-populate-custom-post" style="color:#999999;"><?php _e('Custom Post Type', DFCG_DOMAIN); ?></label></th>
				<td><?php esc_attr_e('You do not have any registered Custom Post Types, therefore the Custom Post Type Gallery method is currently disabled.', DFCG_DOMAIN); ?></td>
			</tr>
			
			<?php else : ?>
			
			<tr valign="top">
				<th scope="row">
					<input name="dfcg_plugin_settings[populate-method]" id="dfcg-populate-custom-post" type="radio" style="margin-right:5px;"  value="custom-post" <?php checked('custom-post', $dfcg_options['populate-method']); ?> />
					<label for="dfcg-populate-custom-post"><?php _e('Custom Post Type', DFCG_DOMAIN); ?></label>
					<a class="load-local" href="#dfcg-tip-gm-cp" rel="#dfcg-tip-gm-cp" title="<?php esc_attr_e('Tip: Custom Post Type Method', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a>
				</th>
				
				<td><?php _e('Images are pulled from a user-definable number of Posts from a Custom Post Type. Set up the relevant options in', DFCG_DOMAIN); ?> <a href="#custom-post">Custom Post Type <?php _e('Settings', DFCG_DOMAIN); ?></a></td>
			</tr>
			
			<?php endif; ?>
			
		</tbody>
	</table>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-gm-mo">
		<p><?php esc_attr_e('This is the original method used in previous versions of the plugin, and the option to choose if you want to mix posts from different categories.', DFCG_DOMAIN); ?></p>
	</div>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-gm-oc">
		<p><?php esc_attr_e('This is the best option if you use a Featured or News category for highlighting certain posts.', DFCG_DOMAIN); ?></p><p><?php esc_attr_e('You can also use this option to display the latest Posts from all categories.', DFCG_DOMAIN); ?></p>
	</div>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-gm-id">
		<p><?php esc_attr_e('Choose this option if you want a static gallery with specific featured Posts and/or Pages, which is not affected by the addition of new Posts and Pages to your site.', DFCG_DOMAIN); ?></p>
	</div>
	
	<?php if( !empty( $post_types ) ) : // Hide if no Custom Post Types registered ?>
	<div class="dfcg-tip-hidden" id="dfcg-tip-gm-cp">
		<p><?php esc_attr_e('This is the option to use if you have set up Custom Post Types and wish to display Custom Post Type posts in the gallery.', DFCG_DOMAIN); ?></p>
	</div>
	<?php endif; ?>
<?php }


/**
* Multi-Option: box and content
*
* 19 options: ['defimgmulti'], ['off01'] -> ['off09'], ['cat01'] -> ['cat09']
* WPMS: 1 hidden ['defimgmulti']
*
* @global array $dfcg_options plugin options from db
* @since 3.0
* @updated 3.3
*/
function dfcg_ui_multi() {
	global $dfcg_options;
	?>
	<h3 class="not-top" id="multi-option">MULTI OPTION <?php _e('Settings', DFCG_DOMAIN); ?></h3>
	
	<p><?php _e('Configure this section if you chose Multi Option in the', DFCG_DOMAIN); ?> <a href="#gallery-method">Gallery Method</a> <?php _e('Settings', DFCG_DOMAIN); ?>. <?php _e('The Multi Option method of populating the gallery provides up to 9 image "slots", each of which can be configured with its own Category and "Post Select". For the Post Select: enter <strong>1</strong> for the latest post, <strong>2</strong> for the last-but-one post, <strong>3</strong> for the post before that, and so on.', DFCG_DOMAIN); ?>
	<a class="load-local" href="#dfcg-tip-mo-1" rel="#dfcg-tip-mo-1" title="<?php esc_attr_e('Important Note:', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/exclamation.png'; ?>" alt="" /></a></p>
	
	<p><em><?php _e('Further information on the possible schemes can be found in the ', DFCG_DOMAIN); ?><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/"><?php _e('Configuration guide', DFCG_DOMAIN); ?></a>.</em></p>
			
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><strong><?php _e('Image "Slots"', DFCG_DOMAIN); ?></strong></th>
				<td width="250px"><strong><?php _e('Category Select', DFCG_DOMAIN); ?></strong></td>
				<td><strong><?php _e('Post Select', DFCG_DOMAIN); ?></strong></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('1st image', DFCG_DOMAIN); ?></th>
				<td><?php wp_dropdown_categories(array('selected' => $dfcg_options['cat01'], 'name' => 'dfcg_plugin_settings[cat01]', 'orderby' => 'Name' , 'hierarchical' => 1, 'hide_empty' => 1 )); ?></td>
				<td><input name="dfcg_plugin_settings[off01]" id="off01" size="5" value="<?php echo $dfcg_options['off01']; ?>" /><a class="load-local" href="#dfcg-tip-mo-ps" rel="#dfcg-tip-mo-ps" title="<?php esc_attr_e('Tip: Post Select', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('2nd image', DFCG_DOMAIN); ?></th>
				<td><?php wp_dropdown_categories(array('selected' => $dfcg_options['cat02'], 'name' => 'dfcg_plugin_settings[cat02]', 'orderby' => 'Name' , 'hierarchical' => 1, 'hide_empty' => 1 )); ?></td>
				<td><input name="dfcg_plugin_settings[off02]" id="off02" size="5" value="<?php echo $dfcg_options['off02']; ?>" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('3rd image', DFCG_DOMAIN); ?></th>
				<td><?php wp_dropdown_categories(array('selected' => $dfcg_options['cat03'], 'name' => 'dfcg_plugin_settings[cat03]', 'orderby' => 'Name' , 'hierarchical' => 1, 'hide_empty' => 1 )); ?></td>
				<td><input name="dfcg_plugin_settings[off03]" id="off03" size="5" value="<?php echo $dfcg_options['off03']; ?>" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('4th image', DFCG_DOMAIN); ?></th>
				<td><?php wp_dropdown_categories(array('selected' => $dfcg_options['cat04'], 'name' => 'dfcg_plugin_settings[cat04]', 'orderby' => 'Name' , 'hierarchical' => 1, 'hide_empty' => 1 )); ?></td>
				<td><input name="dfcg_plugin_settings[off04]" id="off04" size="5" value="<?php echo $dfcg_options['off04']; ?>" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('5th image', DFCG_DOMAIN); ?></th>
				<td><?php wp_dropdown_categories(array('selected' => $dfcg_options['cat05'], 'name' => 'dfcg_plugin_settings[cat05]', 'orderby' => 'Name' , 'hierarchical' => 1, 'hide_empty' => 1 )); ?></td>
				<td><input name="dfcg_plugin_settings[off05]" id="off05" size="5" value="<?php echo $dfcg_options['off05']; ?>" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('6th image', DFCG_DOMAIN); ?></th>
				<td><?php wp_dropdown_categories(array('selected' => $dfcg_options['cat06'], 'name' => 'dfcg_plugin_settings[cat06]', 'orderby' => 'Name' , 'hierarchical' => 1, 'hide_empty' => 1 )); ?></td>
				<td><input name="dfcg_plugin_settings[off06]" id="off06" size="5" value="<?php echo $dfcg_options['off06']; ?>" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('7th image', DFCG_DOMAIN); ?></th>
				<td><?php wp_dropdown_categories(array('selected' => $dfcg_options['cat07'], 'name' => 'dfcg_plugin_settings[cat07]', 'orderby' => 'Name' , 'hierarchical' => 1, 'hide_empty' => 1 )); ?></td>
				<td><input name="dfcg_plugin_settings[off07]" id="off07" size="5" value="<?php echo $dfcg_options['off07']; ?>" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('8th image', DFCG_DOMAIN); ?></th>
				<td><?php wp_dropdown_categories(array('selected' => $dfcg_options['cat08'], 'name' => 'dfcg_plugin_settings[cat08]', 'orderby' => 'Name' , 'hierarchical' => 1, 'hide_empty' => 1 )); ?></td>
				<td><input name="dfcg_plugin_settings[off08]" id="off08" size="5" value="<?php echo $dfcg_options['off08']; ?>" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('9th image', DFCG_DOMAIN); ?></th>
				<td><?php wp_dropdown_categories(array('selected' => $dfcg_options['cat09'], 'name' => 'dfcg_plugin_settings[cat09]', 'orderby' => 'Name' , 'hierarchical' => 1, 'hide_empty' => 1 )); ?></td>
				<td><input name="dfcg_plugin_settings[off09]" id="off09" size="5" value="<?php echo $dfcg_options['off09']; ?>" /></td>
			</tr>
		</tbody>
	</table>
	<?php if ( !function_exists('wpmu_create_blog') ) : ?>
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('URL to default "Category" images folder:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[defimgmulti]" id="dfcg-defimgmulti" size="75" value="<?php echo $dfcg_options['defimgmulti']; ?>" /><br />
				<?php _e('This must be an <b>absolute</b> URL.  For example, if your default images are stored in a folder named "default" in your <em>wp-content/uploads</em> folder, the URL entered here will be:', DFCG_DOMAIN); ?> <em>http://www.yourdomain.com/wp-content/uploads/default/</em>
				<a class="load-local" href="#dfcg-tip-mo-def" rel="#dfcg-tip-mo-def" title="<?php esc_attr_e('Tip: URL to default images folder', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></td>
			</tr>
			
		</tbody>
	</table>
	<?php endif; ?>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-mo-1"><p><?php esc_attr_e('If you want to pull in the latest posts from one category, do not use Multi Option, use the One Category Gallery Method instead - it is much more efficient in terms of database queries.', DFCG_DOMAIN); ?></p><p><?php esc_attr_e("Want to show less than 9 images? Delete the contents of the Post Select fields for image slots you don't need.", DFCG_DOMAIN); ?></p></div>

	<div class="dfcg-tip-hidden" id="dfcg-tip-mo-ps"><p><?php _e('Example: Enter <strong>1</strong> for latest post, <strong>2</strong> for the last-but-one post, etc.', DFCG_DOMAIN); ?></p></div>
	
	<?php if ( !function_exists('wpmu_create_blog') ) : // Hide if in WPMS ?>
	<div class="dfcg-tip-hidden" id="dfcg-tip-mo-def"><p><?php _e('Enter the URL to the folder which contains the default images.  The default images will be pulled into the gallery in the event that Posts do not have an image specified in the Write Post DCG Metabox Image URL field.', DFCG_DOMAIN); ?></p></div>
	<?php endif; ?>
	
<?php }



/**
* One Category: box and contents
*
* 3 options: ['cat-display'], ['posts-number'], ['defimgonecat']
* WPMS: 1 hidden ['defimgonecat']
*
* @global array $dfcg_options plugin options from db
* @since 3.0
* @updated 3.3
*/
function dfcg_ui_onecat() {
	global $dfcg_options;
	?>
	<h3 class="not-top" id="one-category">ONE CATEGORY <?php _e('Settings', DFCG_DOMAIN); ?></h3>
	<p><?php _e('Configure this section if you chose One Category in the', DFCG_DOMAIN); ?> <a href="#gallery-method">Gallery Method</a> <?php _e('Settings', DFCG_DOMAIN); ?>.</p>
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('Select the Category:', DFCG_DOMAIN); ?></th>
				<td><?php wp_dropdown_categories(array('selected' => $dfcg_options['cat-display'], 'name' => 'dfcg_plugin_settings[cat-display]', 'orderby' => 'Name' , 'hierarchical' => 0, 'hide_empty' => 1, 'show_option_all' => __('All', DFCG_DOMAIN) )); ?><span style="padding-left:30px"><em><?php _e('Posts from this category will be displayed in the gallery.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Number of Posts to display:', DFCG_DOMAIN); ?></th>
				<td><select name="dfcg_plugin_settings[posts-number]">
					<option style="padding-right:10px;" value="2" <?php selected('2', $dfcg_options['posts-number']); ?>>2</option>
					<option style="padding-right:10px;" value="3" <?php selected('3', $dfcg_options['posts-number']); ?>>3</option>
					<option style="padding-right:10px;" value="4" <?php selected('4', $dfcg_options['posts-number']); ?>>4</option>
					<option style="padding-right:10px;" value="5" <?php selected('5', $dfcg_options['posts-number']); ?>>5</option>
					<option style="padding-right:10px;" value="6" <?php selected('6', $dfcg_options['posts-number']); ?>>6</option>
					<option style="padding-right:10px;" value="7" <?php selected('7', $dfcg_options['posts-number']); ?>>7</option>
					<option style="padding-right:10px;" value="8" <?php selected('8', $dfcg_options['posts-number']); ?>>8</option>
					<option style="padding-right:10px;" value="9" <?php selected('9', $dfcg_options['posts-number']); ?>>9</option>
					<option style="padding-right:10px;" value="10" <?php selected('10', $dfcg_options['posts-number']); ?>>10</option>
					<option style="padding-right:10px;" value="11" <?php selected('11', $dfcg_options['posts-number']); ?>>11</option>
					<option style="padding-right:10px;" value="12" <?php selected('12', $dfcg_options['posts-number']); ?>>12</option>
					<option style="padding-right:10px;" value="13" <?php selected('13', $dfcg_options['posts-number']); ?>>13</option>
					<option style="padding-right:10px;" value="14" <?php selected('14', $dfcg_options['posts-number']); ?>>14</option>
					<option style="padding-right:10px;" value="15" <?php selected('15', $dfcg_options['posts-number']); ?>>15</option>
					</select>
					<span style="padding-left:30px"><em><?php _e('The minimum number of Posts is 2, the maximum is 15 (for performance reasons).', DFCG_DOMAIN); ?></em></span></td>
			</tr>
		</tbody>
	</table>
	
	<?php if ( !function_exists('wpmu_create_blog') ) : ?>
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('URL to default images folder:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[defimgonecat]" id="dfcg-defimgonecat" size="75" value="<?php echo $dfcg_options['defimgonecat']; ?>" /><br /><?php _e('This must be an <b>absolute</b> URL.  For example, if your default images are stored in a folder named "default" in your <em>wp-content/uploads</em> folder, the URL entered here will be:', DFCG_DOMAIN); ?> <em>http://www.yourdomain.com/wp-content/uploads/default/</em>
				
				<a class="load-local" href="#dfcg-tip-oc-def" rel="#dfcg-tip-oc-def" title="<?php esc_attr_e('Tip: URL to default images folder', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></td>
			</tr>
			
		</tbody>
	</table>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-oc-def"><p><?php esc_attr_e('Enter the URL to the folder which contains the default images.  The default images will be pulled into the gallery in the event that Posts do not have an image specified in the Write Post DCG Metabox Image URL field.', DFCG_DOMAIN); ?></p></div>
	<?php endif; ?>
	
<?php }


/**
* ID Method: box and content
*
* This function was named dfcg_ui_pages() in versions < 3.3
*
* 3 options: ['ids-selected'], ['id-sort-control'], ['defimgid']
* WPMS: 1 hidden ['defimgid'] 
*
* @global array $dfcg_options plugin options from db
* @since 3.0
* @updated 3.3
*/
function dfcg_ui_id() {
	global $dfcg_options;
	?>
	<h3 class="not-top" id="id-method">ID Method <?php _e('Settings', DFCG_DOMAIN); ?></h3>
	<p><?php _e('Configure this section if you chose ID Method in the', DFCG_DOMAIN); ?> <a href="#gallery-method">Gallery Method</a> <?php _e('Settings', DFCG_DOMAIN); ?>.</p>
	<table class="optiontable form-table">
		<tbody>
			<tr>
				<th scope="row"><?php _e('Page/Post ID numbers:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[ids-selected]" id="dfcg-ids-selected" size="75" value="<?php echo $dfcg_options['ids-selected']; ?>" />
				<a class="load-local" href="#dfcg-tip-id-numbers" rel="#dfcg-tip-id-numbers" title="<?php esc_attr_e('Tip: Page/Post ID numbers', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Use Custom Image Order:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[id-sort-control]" id="dfcg-id-sort-control" type="checkbox" value="1" <?php checked('true', $dfcg_options['id-sort-control']); ?> />
				<a class="load-local" href="#dfcg-tip-id-sort" rel="#dfcg-tip-id-sort" title="<?php esc_attr_e('Tip: Custom Image Order', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></td>
			</tr>
		</tbody>
	</table>
	
	<?php if ( !function_exists('wpmu_create_blog') ) : ?>
	<table class="optiontable form-table">
		<tbody>
			<tr>
				<th scope="row"><?php _e('Specify a default image:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[defimgid]" id="dfcg-defimgid" size="75" value="<?php echo $dfcg_options['defimgid']; ?>" />
				<br /><?php _e('Upload a suitable image to your server and enter the absolute URL to this default image.', DFCG_DOMAIN); ?>
				<br /><?php _e('For example: ', DFCG_DOMAIN); ?><em>http://www.yourdomain.com/somefolder/anotherfolder/mydefaultimage.jpg</em>
				<a class="load-local" href="#dfcg-tip-id-def" rel="#dfcg-tip-id-def" title="<?php esc_attr_e('Tip: Default Image', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a>
				</td>
			</tr>
			
		</tbody>
	</table>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-id-def"><p><?php esc_attr_e("This image will be displayed in the event that your selected Page/Post ID's do not have an image specified in the DCG Metabox Image URL field.", DFCG_DOMAIN); ?></p></div>

	<?php endif; ?>

	<div class="dfcg-tip-hidden" id="dfcg-tip-id-numbers"><p><?php esc_attr_e("Enter ID's in a comma separated list with no spaces, eg: 2,7,8,19,21", DFCG_DOMAIN); ?></p></div>
	<div class="dfcg-tip-hidden" id="dfcg-tip-id-sort"><p><?php _e("Check the box if you want to apply your own ordering to the images in the Gallery.", DFCG_DOMAIN); ?></p><p><?php esc_attr_e("This option activates a Sort Order field in the DCG Metabox. This lets you specify a sort order for the images in the gallery.", DFCG_DOMAIN); ?></p><p><?php esc_attr_e('The Sort Order field works in the same way that Pages Order works for ordering Pages in a menu.', DFCG_DOMAIN); ?></p></div>
<?php }


/**
* Custom Post Type: box and contents
*
* 4 options: ['custom-post-type'], ['custom-post-type-tax'], ['custom-post-type-number'], ['defimgcustompost']
* WPMS: 1 hidden ['defimgcustompost']
*
* @global array $dfcg_options plugin options from db
* @since 3.0
*/
function dfcg_ui_custom_post() {
	global $dfcg_options;
	?>
	
	<h3 class="not-top" id="custom-post">CUSTOM POST TYPE <?php _e('Settings', DFCG_DOMAIN); ?></h3>
	
	<?php $post_types = dfcg_get_custom_post_types(); ?>
	
	<?php if( empty($post_types) ) : ?>
	
		<p><?php esc_attr_e('You have not registered any Custom Post types, therefore this section is disabled.', DFCG_DOMAIN); ?></p>
	
	<?php else : ?>
	
	<p><?php esc_attr_e('Configure this section if you chose Custom Post Type in the', DFCG_DOMAIN); ?> <a href="#gallery-method">Gallery Method</a> <?php _e('Settings', DFCG_DOMAIN); ?>.</p>
	
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('Select the Custom Post Type:', DFCG_DOMAIN); ?></th>
				<td><select name="dfcg_plugin_settings[custom-post-type]">
						 
  						<?php
  						foreach ($post_types as $post_type ) : ?>
  							<option value="<?php echo $post_type->name; ?>" <?php selected($post_type->name, $dfcg_options['custom-post-type']); ?>><?php echo $post_type->label; ?></option>
					
						<?php endforeach; ?>
					
					</select>
					
					<span style="padding-left:30px"><em><?php _e('Posts from this Custom Post Type will be displayed in the gallery.', DFCG_DOMAIN); ?></em></span>
					<a class="load-local" href="#dfcg-tip-cpt-type" rel="#dfcg-tip-cpt-type" title="<?php esc_attr_e('Tip: Custom Post Type', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a>
					<a class="load-local" href="#dfcg-tip-cpt-warn" rel="#dfcg-tip-cpt-warn" title="<?php esc_attr_e('Important Note:', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/exclamation.png'; ?>" alt="" /></a>
				</td>
			</tr>
					
			<tr valign="top">
				<th scope="row"><?php _e('Select the Taxonomy:', DFCG_DOMAIN); ?></th>
				<td><select name="dfcg_plugin_settings[custom-post-type-tax]">
					<option value="" <?php selected('', $dfcg_options['custom-post-type-tax']); ?>><?php esc_attr_e('All', DFCG_DOMAIN); ?></option>
					
					<?php
					// Get array of taxonomies associated with this Custom Post Type			
					$taxonomies = get_object_taxonomies( $dfcg_options['custom-post-type'] );
					
					$terms = get_terms($taxonomies, 'orderby=name&hide_empty=1');
													
					foreach ( $terms as $term ) :
						$value = $term->taxonomy . '=' . $term->name; ?>
						
						<option value="<?php echo $value; ?>" <?php selected($value, $dfcg_options['custom-post-type-tax']); ?>><?php echo $term->name; ?></option>
					<?php endforeach; ?>
								
					</select>
					<span style="padding-left:30px"><em><?php _e('Posts from this Taxonomy will be displayed in the gallery.', DFCG_DOMAIN); ?></em></span><a class="load-local" href="#dfcg-tip-cpt-tax" rel="#dfcg-tip-cpt-tax" title="<?php esc_attr_e('Tip: Taxonomy', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a>
				</td>
			</tr>
					
			<tr valign="top">
				<th scope="row"><?php _e('Number of Custom Posts to display:', DFCG_DOMAIN); ?></th>
				<td><select name="dfcg_plugin_settings[custom-post-type-number]">
					<option style="padding-right:10px;" value="2" <?php selected('2', $dfcg_options['custom-post-type-number']); ?>>2</option>
					<option style="padding-right:10px;" value="3" <?php selected('3', $dfcg_options['custom-post-type-number']); ?>>3</option>
					<option style="padding-right:10px;" value="4" <?php selected('4', $dfcg_options['custom-post-type-number']); ?>>4</option>
					<option style="padding-right:10px;" value="5" <?php selected('5', $dfcg_options['custom-post-type-number']); ?>>5</option>
					<option style="padding-right:10px;" value="6" <?php selected('6', $dfcg_options['custom-post-type-number']); ?>>6</option>
					<option style="padding-right:10px;" value="7" <?php selected('7', $dfcg_options['custom-post-type-number']); ?>>7</option>
					<option style="padding-right:10px;" value="8" <?php selected('8', $dfcg_options['custom-post-type-number']); ?>>8</option>
					<option style="padding-right:10px;" value="9" <?php selected('9', $dfcg_options['custom-post-type-number']); ?>>9</option>
					<option style="padding-right:10px;" value="10" <?php selected('10', $dfcg_options['custom-post-type-number']); ?>>10</option>
					<option style="padding-right:10px;" value="11" <?php selected('11', $dfcg_options['custom-post-type-number']); ?>>11</option>
					<option style="padding-right:10px;" value="12" <?php selected('12', $dfcg_options['custom-post-type-number']); ?>>12</option>
					<option style="padding-right:10px;" value="13" <?php selected('13', $dfcg_options['custom-post-type-number']); ?>>13</option>
					<option style="padding-right:10px;" value="14" <?php selected('14', $dfcg_options['custom-post-type-number']); ?>>14</option>
					<option style="padding-right:10px;" value="15" <?php selected('15', $dfcg_options['custom-post-type-number']); ?>>15</option>
					</select>
					<span style="padding-left:70px"><em><?php _e('The minimum number of Custom Posts is 2, the maximum is 15 (for performance reasons).', DFCG_DOMAIN); ?></em></span>
				</td>
			</tr>
		</tbody>
	</table>
			
	<?php if ( !function_exists('wpmu_create_blog') ) : ?>
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('URL to default "Taxonomy" images folder:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[defimgcustompost]" id="dfcg-defimgcustompost" size="75" value="<?php echo $dfcg_options['defimgcustompost']; ?>" />
				<br /><?php _e('This must be an <b>absolute</b> URL.  For example, if your default images are stored in a folder named "default" in your <em>wp-content/uploads</em> folder, the URL entered here will be:', DFCG_DOMAIN); ?> <em>http://www.yourdomain.com/wp-content/uploads/default/</em><a class="load-local" href="#dfcg-tip-cpt-def" rel="#dfcg-tip-cpt-def" title="<?php esc_attr_e('Tip: Default Images folder', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></td>
			</tr>
			
		</tbody>
	</table>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-cpt-def"><p><?php esc_attr_e('Enter the URL to the folder which contains the default images.  The default images will be pulled into the gallery in the event that Posts do not have an image specified in the Write Post DCG Metabox Image URL.', DFCG_DOMAIN); ?></p></div>
			
	<?php endif; ?>
	
	<?php endif; ?>
	
	
	<?php if( !empty( $post_types ) ) : ?>
	<div class="dfcg-tip-hidden" id="dfcg-tip-cpt-type">
		<p><?php esc_attr_e('The dropdown contains a list of all registered Custom Post Types', DFCG_DOMAIN); ?></p>
	</div>
	<div class="dfcg-tip-hidden" id="dfcg-tip-cpt-tax">
		<p><?php esc_attr_e('Select the Taxonomy for your chosen Custom Post Type.', DFCG_DOMAIN); ?></p>
		<p><?php esc_attr_e('A note about taxonomies: The DCG searches for custom taxonomies (or built-in taxonomies) which have been associated with the selected Custom Post Type.', DFCG_DOMAIN); ?></p>
	</div>
	<div class="dfcg-tip-hidden" id="dfcg-tip-cpt-warn"><p><?php esc_attr_e("If you change this setting, click Save Changes to refresh the Settings Page, then return to this tab to reconfigure the Custom Post Type options below.", DFCG_DOMAIN); ?></p><p><?php esc_attr_e("You need to do this because the list of Custom Taxonomies id created dynamically depending on which Custom Post Type has been selected.", DFCG_DOMAIN); ?></p></div>
	
	<?php endif; ?>
<?php }


/**
* Default Desc: box and content
*
* 4 options: ['desc-method'], ['max-char'], ['more-text'], ['desc-method']
* 
* @global array $dfcg_options plugin options from db
* @since 3.2.2
* @updated 3.3
*/
function dfcg_ui_defdesc() {
	global $dfcg_options;
	?>
	<h3><?php _e('Slide Pane Descriptions:', DFCG_DOMAIN); ?></h3>
	<p><?php _e('The Slide Pane description appears under the Post/Page Title in the gallery.', DFCG_DOMAIN); ?> <em><?php _e('The Slide Pane has relatively little space. It is recommended to keep the description short, probably less than 25 words or so.', DFCG_DOMAIN); ?></em></p>
	
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[desc-method]" id="desc-method-auto" type="radio" style="margin-right:5px;" value="auto" <?php checked('auto', $dfcg_options['desc-method']); ?> />
				<label for="desc-method-auto"><?php _e('Auto', DFCG_DOMAIN); ?></label></th>
				<td><?php _e('Descriptions are created automatically as a custom excerpt from your Post/Page content. The length of this custom excerpt is set in the below input field.', DFCG_DOMAIN); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"></th>
				<td><input name="dfcg_plugin_settings[max-char]" id="dfcg-max-char" size="5" value="<?php echo $dfcg_options['max-char']; ?>" /><span style="padding-left:20px"><em><?php _e('Number of characters to display in the Slide Pane description.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"></th>
				<td><input name="dfcg_plugin_settings[more-text]" id="dfcg-more-text" size="15" value="<?php echo $dfcg_options['more-text']; ?>" /><span style="padding-left:20px"><em><?php _e('Text for "more" link added to Auto description.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[desc-method]" id="desc-method-manual" type="radio" style="margin-right:5px;" value="manual" <?php checked('manual', $dfcg_options['desc-method']); ?> />
				<label for="desc-method-manual"><?php _e('Manual', DFCG_DOMAIN); ?></label></th>
				<td><p><?php _e('With this method the plugin looks for the image description in this sequence:', DFCG_DOMAIN); ?><br /><?php _e('(1) a manual description entered in the Write Post/Page <strong>DCG Metabox</strong>, (2) a Category Description if that exists (not applicable to the Pages Gallery Method), (3) the default description created here, or finally (4) the Auto description.', DFCG_DOMAIN); ?><a class="load-local" href="#dfcg-tip-desc-man" rel="#dfcg-tip-desc-man" title="<?php esc_attr_e('Tip: Descriptions', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></p>
				<label for="dfcg-defimagedesc"><b><?php _e('Manual default Description:', DFCG_DOMAIN); ?></b> <em><?php _e('Allowed XHTML tags are:', DFCG_DOMAIN); ?></em> &lt;a href=" " title=" "&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;br /&gt;</label><br />
				<textarea name="dfcg_plugin_settings[defimagedesc]" id="dfcg-defimagedesc" cols="85" rows="2"><?php echo stripslashes( $dfcg_options['defimagedesc'] ); ?></textarea></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[desc-method]" id="desc-method-none" type="radio" style="margin-right:5px;" value="none" <?php checked('none', $dfcg_options['desc-method']); ?> />
				<label for="desc-method-none"><?php _e('None', DFCG_DOMAIN); ?></label></th>
				<td><p><?php _e('With this method no descriptions will be displayed in the Slide Pane, only the Post/Page title.', DFCG_DOMAIN); ?></p></td>
			</tr>
		</tbody>
	</table>
	<div class="dfcg-tip-hidden" id="dfcg-tip-desc-man"><p><?php esc_attr_e("Want to use Category Descriptions? Set them up in Dashboard>Posts>Categories.", DFCG_DOMAIN); ?></p><p><?php esc_attr_e('Even if you have selected Manual, if you intend to use Auto text as a fallback in the event no manual descriptions have been set for individual Posts/Pages, set the Auto number of characters and More link options shown under Auto options below.', DFCG_DOMAIN); ?></p></div>
<?php }


/**
* Gallery CSS: box and content
*
* 24 options: ['gallery-width'],['gallery-height'],['gallery-border-thick'],['gallery-border-colour'],['slide-height'],['slide-overlay-color'],['slide-h2-size'],['slide-h2-weight'],['slide-h2-colour'],['slide-h2-padtb'],['slide-h2-padlr'],['slide-h2-margtb'],['slide-h2-marglr'],['slide-p-size'],['slide-p-colour'],['slide-p-line-height'],['slide-p-padtb'],['slide-p-padlr'],['slide-p-margtb'],['slide-p-marglr'],['slide-p-a-color'],['slide-p-a-weight'],['slide-p-ahover-color'],['slide-p-ahover-weight'], ['gallery-background']
*
* Mootools only: ['slide-height']
* 
* @global array $dfcg_options plugin options from db
* @since 3.0
* @updated 3.3.4
*/
function dfcg_ui_css() {
	global $dfcg_options;
	?>
	<?php if( $dfcg_options['scripts'] == 'mootools' ) : ?>
		<h3><?php _e('Gallery size and CSS options (Mootools):', DFCG_DOMAIN); ?></h3>
	<?php else : ?>
		<h3><?php _e('Gallery size and CSS options (jQuery):', DFCG_DOMAIN); ?></h3>
	<?php endif; ?>
	
	<p><?php _e('Configure various layout and CSS options for your gallery including the size of the gallery, the height of the Slide Pane, gallery border, and the font sizes, colours and margins for the text displayed in the Slide Pane.', DFCG_DOMAIN); ?></p>	
			
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('Gallery Width:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[gallery-width]" id="dfcg-gallery-width" size="5" value="<?php echo $dfcg_options['gallery-width']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 460px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Gallery Height:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[gallery-height]" id="dfcg-gallery-height" size="5" value="<?php echo $dfcg_options['gallery-height']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 250px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Gallery Background:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[gallery-background]" id="dfcg-gallery-background" size="8" value="<?php echo $dfcg_options['gallery-background']; ?>" />&nbsp;<span style="padding-left:7px;"><em><?php _e('Enter color hex code like this #000000.', DFCG_DOMAIN); ?> <em><?php _e('Default is #000000.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Gallery border width:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[gallery-border-thick]" id="dfcg-gallery-border-thick" size="3" value="<?php echo $dfcg_options['gallery-border-thick']; ?>" />&nbsp;px <span style="padding-left:20px;"> <?php _e("If you don't want a border enter 0 in this box.", DFCG_DOMAIN); ?> <em><?php _e('Default is 1px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Gallery border colour:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[gallery-border-colour]" id="dfcg-gallery-border-colour" size="8" value="<?php echo $dfcg_options['gallery-border-colour']; ?>" />&nbsp;<span style="padding-left:7px;"><?php _e('Enter color hex code like this #000000.', DFCG_DOMAIN); ?> <em><?php _e('Default is #000000.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			
			<?php if( $dfcg_options['scripts'] == 'mootools' ) : ?>
			<tr valign="top">
				<th scope="row"><?php _e('Slide Pane Height:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-height]" id="dfcg-slide-height" size="3" value="<?php echo $dfcg_options['slide-height']; ?>" /> px <span style="padding-left:20px;"><em><?php _e('Default is 50px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<?php else : ?>
			<tr valign="top">
				<th scope="row"><?php _e('Slide Pane Height:', DFCG_DOMAIN); ?></th>
				<td><em><?php _e('This setting is not available when using jQuery script.', DFCG_DOMAIN); ?></em></td>
			</tr>
			<?php endif; ?>
			
			<tr valign="top">
				<th scope="row"><?php _e('Slide Pane Background:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-overlay-color]" id="dfcg-slide-overlay-color" size="8" value="<?php echo $dfcg_options['slide-overlay-color']; ?>" />&nbsp;<span style="padding-left:7px;"><?php _e('Enter color hex code like this #000000.', DFCG_DOMAIN); ?> <em><?php _e('Default is #000000.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Heading font size:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-h2-size]" id="dfcg-slide-h2-size" size="3" value="<?php echo $dfcg_options['slide-h2-size']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 12px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Heading font weight:', DFCG_DOMAIN); ?></th>
				<td><select name="dfcg_plugin_settings[slide-h2-weight]">
					<option style="padding-right:10px;" value="bold" <?php selected('bold', $dfcg_options['slide-h2-weight']); ?>>bold</option>
					<option style="padding-right:10px;" value="normal" <?php selected('normal', $dfcg_options['slide-h2-weight']); ?>>normal</option>
					</select>&nbsp;<span style="padding-left:6px;"><?php _e('Choose Heading font-weight.', DFCG_DOMAIN); ?> <em><?php _e('Default is bold.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Heading font colour:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-h2-colour]" id="dfcg-slide-h2-colour" size="8" value="<?php echo $dfcg_options['slide-h2-colour']; ?>" />&nbsp;<span style="padding-left:7px;"><?php _e('Enter color hex code like this #000000.', DFCG_DOMAIN); ?> <em><?php _e('Default is #FFFFFF.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Heading Padding top and bottom:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-h2-padtb]" id="dfcg-slide-h2-padtb" size="3" value="<?php echo $dfcg_options['slide-h2-padtb']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 0px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Heading Padding left and right:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-h2-padlr]" id="dfcg-slide-h2-padlr" size="3" value="<?php echo $dfcg_options['slide-h2-padlr']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 0px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Heading Margin top and bottom:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-h2-margtb]" id="dfcg-slide-h2-margtb" size="3" value="<?php echo $dfcg_options['slide-h2-margtb']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 2px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Heading Margin left and right:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-h2-marglr]" id="dfcg-slide-h2-marglr" size="3" value="<?php echo $dfcg_options['slide-h2-marglr']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 5px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description font size:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-p-size]" id="dfcg-slide-p-size" size="3" value="<?php echo $dfcg_options['slide-p-size']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 11px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description font colour:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-p-colour]" id="dfcg-slide-p-colour" size="8" value="<?php echo $dfcg_options['slide-p-colour']; ?>" />&nbsp;<span style="padding-left:7px;"><?php _e('Enter color hex code like this #000000.', DFCG_DOMAIN); ?> <em><?php _e('Default is #FFFFFF.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description line height:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-p-line-height]" id="dfcg-slide-p-line-height" size="3" value="<?php echo $dfcg_options['slide-p-line-height']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 14px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description Padding top and bottom:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-p-padtb]" id="dfcg-slide-p-padtb" size="3" value="<?php echo $dfcg_options['slide-p-padtb']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 0px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description Padding left and right:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-p-padlr]" id="dfcg-slide-p-padlr" size="3" value="<?php echo $dfcg_options['slide-p-padlr']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 0px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description Margin top and bottom:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-p-margtb]" id="dfcg-slide-p-margtb" size="3" value="<?php echo $dfcg_options['slide-p-margtb']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 2px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description Margin left and right:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-p-marglr]" id="dfcg-slide-p-marglr" size="3" value="<?php echo $dfcg_options['slide-p-marglr']; ?>" />&nbsp;px <span style="padding-left:20px;"><em><?php _e('Default is 5px.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<?php // More link CSS added for Auto description option ?>
			<tr valign="top">
				<th scope="row"><?php _e('Description More link colour:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-p-a-color]" id="dfcg-slide-p-a-color" size="8" value="<?php echo $dfcg_options['slide-p-a-color']; ?>" />&nbsp;<span style="padding-left:7px;"><?php _e('Only applicable if you selected', DFCG_DOMAIN); ?> <a class="dfcg-panel-desc-link" href="#default-desc"><?php _e('Auto Descriptions', DFCG_DOMAIN); ?></a>. <?php _e('Enter color hex code like this #000000.', DFCG_DOMAIN); ?> <em><?php _e('Default is #FFFFFF.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description More link font weight:', DFCG_DOMAIN); ?></th>
				<td><select name="dfcg_plugin_settings[slide-p-a-weight]">
					<option style="padding-right:10px;" value="bold" <?php selected('bold', $dfcg_options['slide-p-a-weight']); ?>>bold</option>
					<option style="padding-right:10px;" value="normal" <?php selected('normal', $dfcg_options['slide-p-a-weight']); ?>>normal</option>
					</select>&nbsp;<span style="padding-left:6px;"><?php _e('Only applicable if you selected', DFCG_DOMAIN); ?> <a class="dfcg-panel-desc-link" href="#default-desc"><?php _e('Auto Descriptions', DFCG_DOMAIN); ?></a>. <?php _e('Choose More link font-weight.', DFCG_DOMAIN); ?> <em><?php _e('Default is normal.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description More link hover colour:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slide-p-ahover-color]" id="dfcg-slide-p-ahover-color" size="8" value="<?php echo $dfcg_options['slide-p-ahover-color']; ?>" />&nbsp;<span style="padding-left:7px;"><?php _e('Only applicable if you selected', DFCG_DOMAIN); ?> <a class="dfcg-panel-desc-link" href="#default-desc"><?php _e('Auto Descriptions', DFCG_DOMAIN); ?></a>. <?php _e('Enter color hex code like this #000000.', DFCG_DOMAIN); ?> <em><?php _e('Default is #FFFFFF.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Description More link hover font weight:', DFCG_DOMAIN); ?></th>
				<td><select name="dfcg_plugin_settings[slide-p-ahover-weight]">
					<option style="padding-right:10px;" value="bold" <?php selected('bold', $dfcg_options['slide-p-ahover-weight']); ?>>bold</option>
					<option style="padding-right:10px;" value="normal" <?php selected('normal', $dfcg_options['slide-p-ahover-weight']); ?>>normal</option>
					</select>&nbsp;<span style="padding-left:6px;"><?php _e('Only applicable if you selected', DFCG_DOMAIN); ?> <a class="dfcg-panel-desc-link" href="#default-desc"><?php _e('Auto Descriptions', DFCG_DOMAIN); ?></a>. <?php _e('Choose More link hover font-weight.', DFCG_DOMAIN); ?> <em><?php _e('Default is bold.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
		</tbody>
	</table>
<?php }


/**
* Select Javascript Framework
*
* 1 options: ['scripts']
*
* @global array $dfcg_options plugin options from db
* @since 3.0
* @updated 3.3
*/
function dfcg_ui_js_framework() {
	global $dfcg_options;
	?>
	<h3 class="top"><?php _e('Select Javascript framework (OPTIONAL):', DFCG_DOMAIN); ?></h3>
	<p><?php _e('Select the javascript framework to be used to display the gallery.', DFCG_DOMAIN); ?><a class="load-local" href="#dfcg-tip-js-warn" rel="#dfcg-tip-js-warn" title="<?php esc_attr_e('Important Note:', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/exclamation.png'; ?>" alt="" /></a></p>
		
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[scripts]" id="dfcg-scripts-mootools" type="radio" style="margin-right:5px;" value="mootools" <?php checked('mootools', $dfcg_options['scripts']); ?> />
				<label for="dfcg-scripts-mootools">Mootools (Default)</label></th>
				<td><?php _e('Use SmoothGallery Mootools script. This is the default setting.', DFCG_DOMAIN); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[scripts]" id="dfcg-scripts-jquery" type="radio" style="margin-right:5px;" value="jquery" <?php checked('jquery', $dfcg_options['scripts']); ?> />
				<label for="dfcg-scripts-jquery">jQuery</label></th>
				<td><?php _e('Use jQuery script. Select this option in the event of javascript conflicts with other plugins.', DFCG_DOMAIN); ?></td>
			</tr>
		</tbody>
	</table>
	<div class="dfcg-tip-hidden" id="dfcg-tip-js-warn"><p><?php esc_attr_e("If you change this setting, click Save Changes to refresh the Settings Page, then return to this tab to configure the Javascript configuration options below. You may also need to re-configure some of the Gallery CSS options.", DFCG_DOMAIN); ?></p><p><?php esc_attr_e("You need to do this because the Javascript configuration and Gallery CSS options are not identical for both Frameworks.", DFCG_DOMAIN); ?></p></div>
<?php }


/**
* Javascript options: box and content
*
* 11 options:	[slideInfoZoneSlide],[defaultTransition],[mootools],['showCarousel'],[textShowCarousel],[showInfopane],[slideInfoZoneOpacity],
*				[timed],[delay],[showArrows],[slideInfoZoneStatic]
* Mootools only: [slideInfoZoneSlide], [defaultTransition], [mootools]
* jQuery only: [slideInfoZoneStatic]
*
* @global array $dfcg_options plugin options from db
* @since 3.0
* @updated 3.3.4
*/
function dfcg_ui_javascript() {
	global $dfcg_options;
	?>
	
	<?php if( $dfcg_options['scripts'] == 'mootools' ) : ?>
	<h3 class="not-top"><?php _e('Mootools Javascript configuration options (OPTIONAL):', DFCG_DOMAIN); ?></h3>
	
	<?php else : ?>
	
	<h3 class="not-top"><?php _e('jQuery Javascript configuration options (OPTIONAL):', DFCG_DOMAIN); ?></h3>
	
	<?php endif; ?>
	
	<p><?php _e("Configure various default javascript settings for your gallery. The inclusion of these options in this Settings page saves you having to customise the plugin's javascript files.", DFCG_DOMAIN); ?></p>

	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('Show Carousel:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[showCarousel]" id="dfcg-showCarousel" type="checkbox" value="true" <?php checked('true', $dfcg_options['showCarousel']); ?> /><span style="padding-left:50px"><em><?php _e('Check the box to display thumbnail Carousel. Default is CHECKED.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Carousel label:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[textShowCarousel]" id="dfcg-textShowCarousel" size="25" value="<?php echo $dfcg_options['textShowCarousel']; ?>" /><span style="padding-left:30px"><em><?php _e('Label for Carousel tab. Only visible if "Show Carousel" is checked. Default is Featured Articles.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Show Slide Pane:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[showInfopane]" id="dfcg-showInfopane" type="checkbox" value="1" <?php checked('true', $dfcg_options['showInfopane']); ?> /><span style="padding-left:50px"><em><?php _e('Check the box to display Slide Pane. Default is CHECKED.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			
			<?php if( $dfcg_options['scripts'] == 'mootools' ) : ?>
			<tr valign="top">
				<th scope="row"><?php _e('Animate Slide Pane:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slideInfoZoneSlide]" id="dfcg-slideInfoZoneSlide" type="checkbox" value="1" <?php checked('true', $dfcg_options['slideInfoZoneSlide']); ?> /><span style="padding-left:50px"><em><?php _e('Check the box to have Slide Pane slide into view. Default is CHECKED.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<?php endif; ?>
			
			<?php if( $dfcg_options['scripts'] == 'jquery' ) : ?>
			<tr valign="top">
				<th scope="row"><?php _e('Do not animate Slide Pane:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slideInfoZoneStatic]" id="dfcg-slideInfoZoneStatic" type="checkbox" value="1" <?php checked('true', $dfcg_options['slideInfoZoneStatic']); ?> /><span style="padding-left:50px"><em><?php _e('Check the box to keep Slide Pane static, ie not animated. Default is UNCHECKED.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<?php endif; ?>
			
			<tr valign="top">
				<th scope="row"><?php _e('Slide Pane Opacity:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[slideInfoZoneOpacity]" id="dfcg-slideInfoZoneOpacity" size="10" value="<?php echo $dfcg_options['slideInfoZoneOpacity']; ?>" /><span style="padding-left:30px"><em><?php _e('Opacity of Slide Pane. 1.0 is fully opaque, 0.0 is fully transparent. Default is 0.7.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Show Arrows:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[showArrows]" id="dfcg-showArrows" type="checkbox" value="true" <?php checked('true', $dfcg_options['showArrows']); ?> /><span style="padding-left:50px"><em><?php _e('Check the box to display left and right navigation arrows. Default is CHECKED.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Timed transitions:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[timed]" id="dfcg-timed" type="checkbox" value="1" <?php checked('true', $dfcg_options['timed']); ?> /><span style="padding-left:50px"><em><?php _e('Check the box to have timed image transitions. Default is CHECKED.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Transitions delay:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[delay]" id="dfcg-delay" size="10" value="<?php echo $dfcg_options['delay']; ?>" /><span style="padding-left:30px"><em><?php _e('Enter the delay time (in milliseconds, minimum 1000) between image transitions. Default is 9000.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			
			<?php if( $dfcg_options['scripts'] == 'mootools' ) : ?>
			<tr valign="top">
				<th scope="row"><?php _e('Transition type:', DFCG_DOMAIN); ?></th>
				<td><select name="dfcg_plugin_settings[defaultTransition]">
					<option style="padding-right:10px;" value="fade" <?php selected('fade', $dfcg_options['defaultTransition']); ?>>fade</option>
					<option style="padding-right:10px;" value="fadeslideleft" <?php selected('fadeslideleft', $dfcg_options['defaultTransition']); ?>>fadeslideleft</option>
					<option style="padding-right:10px;" value="continuousvertical" <?php selected('continuousvertical', $dfcg_options['defaultTransition']); ?>>continuousvertical</option>
					<option style="padding-right:10px;" value="continuoushorizontal" <?php selected('continuoushorizontal', $dfcg_options['defaultTransition']); ?>>continuoushorizontal</option>
					</select><span style="padding-left:30px"><em><?php _e('Select the type of image transition. Default is "fade".', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Disable Mootools:') ?></th>
				<td><input name="dfcg_plugin_settings[mootools]" id="dfcg-mootools" type="checkbox" value="1" <?php checked('1', $dfcg_options['mootools']); ?> /><span style="padding-left:50px"><em><?php _e('Check the box ONLY in the event that another plugin is already loading the Mootools Javascript library files in your site. Default is UNCHECKED.', DFCG_DOMAIN); ?></em></span></td>
			</tr>
			<?php endif; ?>
							
		</tbody>
	</table>
<?php }


/**
* Restrict Scripts loading: box and content
*
* 2 options: ['limit-scripts'], ['page-filename']
*
* @global array $dfcg_options plugin options from db
* @since 3.2.2
* @updated 3.3
*/
function dfcg_ui_restrict_scripts() {
	global $dfcg_options;
	?>
	<h3 class="top"><?php _e('Restrict script loading (REQUIRED):', DFCG_DOMAIN); ?></h3>
	<p><?php _e("This option lets you restrict the loading of the plugin's javascript to the page that will actually display the gallery. This prevents the scripts being loaded on all pages unnecessarily, which will help to minimise the impact of the plugin on page loading times. This option applies to both mootools and jquery <a class=\"dfcg-panel-javascript-link\" href=\"#gallery-js-scripts\">Javascript Framework settings</a>.", DFCG_DOMAIN); ?></p>

	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[limit-scripts]" id="limit-scripts-home" type="radio" style="margin-right:5px;" value="homepage" <?php checked('homepage', $dfcg_options['limit-scripts']); ?> />
				<label for="limit-scripts-home"><?php _e('Home page only (Default)', DFCG_DOMAIN); ?></label></th>
				<td><?php _e("Select this option to load the plugin's scripts ONLY on the home page.", DFCG_DOMAIN); ?>
				<a class="load-local" href="#dfcg-tip-script-home" rel="#dfcg-tip-script-home" title="<?php esc_attr_e('Tip: Home page', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></td>
			</tr>
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[limit-scripts]" id="limit-scripts-pages" type="radio" style="margin-right:5px;" value="page" <?php checked('page', $dfcg_options['limit-scripts']); ?> />
				<label for="limit-scripts-pages"><?php _e('Pages', DFCG_DOMAIN); ?></label></th>
				<td><?php _e("Select this option to load the plugin's scripts ONLY when a specific Page is being used to display the gallery.", DFCG_DOMAIN); ?>
				<a class="load-local" href="#dfcg-tip-script-page" rel="#dfcg-tip-script-page" title="<?php esc_attr_e('Tip: Pages', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a><br />
				<?php esc_attr_e('Enter Page ID(s): ', DFCG_DOMAIN); ?>
				<input name="dfcg_plugin_settings[page-ids]" id="dfcg-page-ids" size="45" value="<?php echo $dfcg_options['page-ids']; ?>" /><a class="load-local" href="#dfcg-tip-script-pid" rel="#dfcg-tip-script-pid" title="<?php esc_attr_e('Tip: Page ID\'s', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[limit-scripts]" id="limit-scripts-page" type="radio" style="margin-right:5px;" value="pagetemplate" <?php checked('pagetemplate', $dfcg_options['limit-scripts']); ?> />
				<label for="limit-scripts-page"><?php _e('Specific Page Template', DFCG_DOMAIN); ?></label></th>
				<td><?php _e("Select this option to load the plugin's scripts ONLY when a specific Page Template is being used to display the gallery.", DFCG_DOMAIN); ?>
				<a class="load-local" href="#dfcg-tip-script-ptemp" rel="#dfcg-tip-script-ptemp" title="<?php esc_attr_e('Tip: Specific Page Template', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a><br />
				<?php esc_attr_e('Enter Page Template: ', DFCG_DOMAIN); ?>
				<input name="dfcg_plugin_settings[page-filename]" id="dfcg-page-filename" size="45" value="<?php echo $dfcg_options['page-filename']; ?>" /><a class="load-local" href="#dfcg-tip-script-ptemp-file" rel="#dfcg-tip-script-ptemp-file" title="<?php esc_attr_e('Tip: Page Template filename', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><input name="dfcg_plugin_settings[limit-scripts]" id="limit-scripts-other" style="margin-right:5px;" type="radio" value="other" <?php checked('other', $dfcg_options['limit-scripts']); ?> />
				<label for="limit-scripts-other"><?php _e('Other', DFCG_DOMAIN); ?></label></th>
				<td><?php _e('Select this option if none of the above apply to your setup.', DFCG_DOMAIN); ?><a class="load-local" href="#dfcg-tip-script-other" rel="#dfcg-tip-script-other" title="<?php esc_attr_e('Tip: Other', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></td>
			</tr>
		</tbody>
	</table>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-script-home"><p><?php esc_attr_e('Best option if the gallery will only be used on the home page of your site. This is the default.', DFCG_DOMAIN); ?></p><p><?php esc_attr_e('Also, select this option if you use a Static Front Page defined in Dashboard > Settings > Reading and the gallery will only be shown on the home page.', DFCG_DOMAIN); ?></p></div>
	<div class="dfcg-tip-hidden" id="dfcg-tip-script-page"><p><?php esc_attr_e('Best option if the gallery is displayed using a Page.', DFCG_DOMAIN); ?></p></div>
	<div class="dfcg-tip-hidden" id="dfcg-tip-script-pid"><p><?php esc_attr_e('Enter ID of the Page, eg 42. Multiple pages are also possible, like this: 2,43,17', DFCG_DOMAIN); ?></p></div>
	<div class="dfcg-tip-hidden" id="dfcg-tip-script-ptemp"><p><?php esc_attr_e('Best option if the gallery is displayed using a Page Template.', DFCG_DOMAIN); ?></p></div>
	<div class="dfcg-tip-hidden" id="dfcg-tip-script-ptemp-file"><p><?php esc_attr_e('Filename of the Page Template, eg mypagetemplate.php.', DFCG_DOMAIN); ?></p><p><?php esc_attr_e('You can also enter more than one template filename using a comma separated list, eg template.php,template2.php,template3.php', DFCG_DOMAIN); ?></p></div>
	<div class="dfcg-tip-hidden" id="dfcg-tip-script-other"><p><?php esc_attr_e("The plugin's scripts will be loaded in every page. Not recommended.", DFCG_DOMAIN); ?></p></div>
<?php }


/**
* Error Messages: box and content
*
* 1 options: ['errors']
*
* @global array $dfcg_options plugin options from db
* @since 3.0
* @updated 3.3
*/
function dfcg_ui_errors() {
	global $dfcg_options;
	?>
	<h3 class="top"><?php _e('Error Message options (OPTIONAL)', DFCG_DOMAIN); ?></h3>
	<p><?php _e('The plugin produces informative error messages in the event that Posts, Pages, images and descriptions have not been configured properly, which will assist with troubleshooting. These error messages, if activated, are output to the Page Source of the gallery as HTML comments.', DFCG_DOMAIN); ?> <em><?php _e('Error message explanations can be found in the', DFCG_DOMAIN); ?> <a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/error-messages/">Dynamic Content Gallery <?php _e('Error Messages', DFCG_DOMAIN); ?></a> <?php _e('guide.', DFCG_DOMAIN); ?></em></p>
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('Show Error Messages:', DFCG_DOMAIN); ?></th>
				<td><input name="dfcg_plugin_settings[errors]" id="dfcg-errors" type="checkbox" value="1" <?php checked('true', $dfcg_options['errors']); ?> /><span style="font-size:11px;margin-left:20px;"><em><?php _e('Check box to show Page Source error messages, uncheck the box to hide them. Default is UNCHECKED.', DFCG_DOMAIN)?></em></span></td>
			</tr>
		</tbody>
	</table>
<?php }


/**
* Posts/Pages edit columns: box and content
*
* 5 options: [posts-column],[posts-desc-column],[pages-column],['pages-desc-column'],['pages-sort-column']
* 
* @global array $dfcg_options plugin options from db
* @since 3.2.2
* @updated 3.3
*/
function dfcg_ui_columns() {
	global $dfcg_options;
	?>
	<h3 class="not-top"><?php _e('Add Custom Field columns to Posts and Pages Edit screen (OPTIONAL)', DFCG_DOMAIN); ?></h3>
	<p><?php _e('These settings let you display a column in the Edit Posts and Edit Pages screens to show the value of the <strong>Image URL</strong> and manual <strong>Slide Pane Descriptions</strong> entered in the Write Post/Page DCG Metabox. This can be useful to help keep track of the Image URLs and manual Slide Pane Descriptions without having to open each individual Post or Page.', DFCG_DOMAIN); ?></p>
	<p><em><?php _e('To hide the additional columns in the Edit Posts and Edit Pages screens, uncheck the boxes then click the "Save Changes" button. Default is CHECKED.', DFCG_DOMAIN)?></em><a class="load-local" href="#dfcg-tip-col-cpt" rel="#dfcg-tip-col-cpt" title="<?php esc_attr_e('Tip: Custom Post Types', DFCG_DOMAIN); ?>"><img src="<?php echo  DFCG_URL . '/admin-assets/cluetip/images/help.png'; ?>" alt="" /></a></p>
	
	<table class="optiontable form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('Show columns in Edit Posts:', DFCG_DOMAIN); ?></th>
				<td>DCG Image: <input type="checkbox" name="dfcg_plugin_settings[posts-column]" id="dfcg-posts-column" value="1" <?php checked('true', $dfcg_options['posts-column']); ?> />
				<span style="padding-left:50px;"><?php _e('DCG Desc:', DFCG_DOMAIN); ?></span> <input type="checkbox" name="dfcg_plugin_settings[posts-desc-column]" id="dfcg-posts-desc-column" value="1" <?php checked('true', $dfcg_options['posts-desc-column']); ?> /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Show columns in Edit Pages:', DFCG_DOMAIN); ?></th>
				<td>DCG Image: <input type="checkbox" name="dfcg_plugin_settings[pages-column]" id="dfcg-pages-column" value="1" <?php checked('true', $dfcg_options['pages-column']); ?> />
				<span style="padding-left:50px;"><?php _e('DCG Desc:', DFCG_DOMAIN); ?></span> <input type="checkbox" name="dfcg_plugin_settings[pages-desc-column]" id="dfcg-pages-desc-column" value="1" <?php checked('true', $dfcg_options['pages-desc-column']); ?> />
				<span style="padding-left:50px;"><?php _e('Sort Order:', DFCG_DOMAIN); ?></span> <input type="checkbox" name="dfcg_plugin_settings[pages-sort-column]" id="dfcg-pages-sort-column" value="1" <?php checked('true', $dfcg_options['pages-sort-column']); ?> /></td>
			</tr>
		</tbody>
	</table>
	
	<div class="dfcg-tip-hidden" id="dfcg-tip-col-cpt"><p><?php esc_attr_e('Note that by default if "Show columns in Edit Posts" options are checked, these columns will also appear in the Edit screens for any Custom Post Types.', DFCG_DOMAIN); ?></p></div>
<?php }


/**
* Form hidden fields
* WP ONLY
*
* 2 options: [homeurl], [just-reset]
* If mootools not loaded, 4 hidden: [slideInfoZoneSlide],[defaultTransition],[mootools],[thumb-type]
* If jquery not loaded, 1 hidden: [slideInfoZoneStatic]
*
* @global array $dfcg_options plugin options from db
* @since 3.0
* @updated 3.3.4
*/
function dfcg_ui_hidden_wp() {
	global $dfcg_options;
	?>
	<?php // Always hidden in WP/WPMU ?>
	<input name="dfcg_plugin_settings[homeurl]" id="dfcg-homeurl" type="hidden" value="<?php echo $dfcg_options['homeurl']; ?>" />
	<input name="dfcg_plugin_settings[just-reset]" id="dfcg-just-reset" type="hidden" value="<?php echo $dfcg_options['just-reset']; ?>" />
	
	<?php if($dfcg_options['scripts'] == 'mootools' ) : // 1 Hidden if mootools is loaded ?>
	<input name="dfcg_plugin_settings[slideInfoZoneStatic]" id="dfcg-slideInfoZoneStatic" type="hidden" value="<?php echo $dfcg_options['slideInfoZoneStatic']; ?>" />
	<?php endif; ?>
	
	<?php if($dfcg_options['scripts'] == 'jquery' ) : // jquery is loaded, +5 Hidden if jquery is loaded ?>
	<input name="dfcg_plugin_settings[slide-height]" id="dfcg-slide-height" type="hidden" value="<?php echo $dfcg_options['slide-height']; ?>" />
	<input name="dfcg_plugin_settings[slideInfoZoneSlide]" id="dfcg-slideInfoZoneSlide" type="hidden" value="<?php echo $dfcg_options['slideInfoZoneSlide']; ?>" />
	<input name="dfcg_plugin_settings[defaultTransition]" id="dfcg-defaultTransition" type="hidden" value="<?php echo $dfcg_options['defaultTransition']; ?>" />
	<input name="dfcg_plugin_settings[mootools]" id="dfcg-mootools" type="hidden" value="<?php echo $dfcg_options['mootools']; ?>" />
	<input name="dfcg_plugin_settings[thumb-type]" id="dfcg-thumb-type" type="hidden" value="<?php echo $dfcg_options['thumb-type']; ?>" />
	<?php endif; ?>
	
	<?php
	$post_types = dfcg_get_custom_post_types();
	if( empty( $post_types ) ) : // If no Custom Post Types, need to hide these inputs ?>
	<input name="dfcg_plugin_settings[custom-post-type]" id="dfcg-custom-post-type" type="hidden" value="<?php echo $dfcg_options['custom-post-type']; ?>" />
	<input name="dfcg_plugin_settings[custom-post-type-tax]" id="dfcg-custom-post-type-tax" type="hidden" value="<?php echo $dfcg_options['custom-post-type-tax']; ?>" />
	<input name="dfcg_plugin_settings[custom-post-type-number]" id="custom-post-type-number" type="hidden" value="<?php echo $dfcg_options['custom-post-type-number']; ?>" />
	<input name="dfcg_plugin_settings[defimgcustompost]" id="dfcg-defimgcustompost" type="hidden" value="<?php echo $dfcg_options['defimgcustompost']; ?>" />
	<?php endif; ?>
	
<?php }


/**
* Form hidden fields
* WPMU ONLY
*
* 7 options: [homeurl], [just-reset],[imageurl],[defimgmulti],[defimgonecat],[defimgpages],[defimgcustompost] 
* If mootools not loaded, 4 hidden: [slideInfoZoneSlide],[defaultTransition],[mootools],[thumb-type]
* If jquery not loaded, 1 hidden: [slideInfoZoneStatic]
*
* @global array $dfcg_options plugin options from db
* @since 3.0
* @updated 3.3.4 
*/
function dfcg_ui_hidden_wpmu() {
	global $dfcg_options;
	?>
	<?php // Always hidden in WP/WPMS ?>
	<input name="dfcg_plugin_settings[homeurl]" id="dfcg-homeurl" type="hidden" value="<?php echo $dfcg_options['homeurl']; ?>" />
	<input name="dfcg_plugin_settings[just-reset]" id="dfcg-just-reset" type="hidden" value="<?php echo $dfcg_options['just-reset']; ?>" />
	
	<?php // Always hidden in WPMS ?>
	<input name="dfcg_plugin_settings[imageurl]" id="dfcg-imageurl" type="hidden" value="<?php echo $dfcg_options['imageurl']; ?>" />
	<input name="dfcg_plugin_settings[defimgmulti]" id="dfcg-defimgmulti" type="hidden" value="<?php echo $dfcg_options['defimgmulti']; ?>" />
	<input name="dfcg_plugin_settings[defimgonecat]" id="dfcg-defimgonecat" type="hidden" value="<?php echo $dfcg_options['defimgonecat']; ?>" />
	<input name="dfcg_plugin_settings[defimgpages]" id="dfcg-defimgpages" type="hidden" value="<?php echo $dfcg_options['defimgpages']; ?>" />
	<input name="dfcg_plugin_settings[defimgcustompost]" id="dfcg-defimgcustompost" type="hidden" value="<?php echo $dfcg_options['defimgcustompost']; ?>" />
	
	<?php if($dfcg_options['scripts'] == 'mootools' ) : // 1 Hidden if mootools is loaded ?>
	<input name="dfcg_plugin_settings[slideInfoZoneStatic]" id="dfcg-slideInfoZoneStatic" type="hidden" value="<?php echo $dfcg_options['slideInfoZoneStatic']; ?>" />
	<?php endif; ?>
	
	<?php if($dfcg_options['scripts'] == 'jquery' ) : // jquery is loaded, +5 Hidden if jquery is loaded ?>
	<input name="dfcg_plugin_settings[slide-height]" id="dfcg-slide-height" type="hidden" value="<?php echo $dfcg_options['slide-height']; ?>" />
	<input name="dfcg_plugin_settings[slideInfoZoneSlide]" id="dfcg-slideInfoZoneSlide" type="hidden" value="<?php echo $dfcg_options['slideInfoZoneSlide']; ?>" />
	<input name="dfcg_plugin_settings[defaultTransition]" id="dfcg-defaultTransition" type="hidden" value="<?php echo $dfcg_options['defaultTransition']; ?>" />
	<input name="dfcg_plugin_settings[mootools]" id="dfcg-mootools" type="hidden" value="<?php echo $dfcg_options['mootools']; ?>" />
	<input name="dfcg_plugin_settings[thumb-type]" id="dfcg-thumb-type" type="hidden" value="<?php echo $dfcg_options['thumb-type']; ?>" />
	<?php endif; ?>
	
	<?php
	$post_types = dfcg_get_custom_post_types();
	if( empty( $post_types ) ) : // If no Custom Post Types, need to hide these inputs ?>
	<input name="dfcg_plugin_settings[custom-post-type]" id="dfcg-custom-post-type" type="hidden" value="<?php echo $dfcg_options['custom-post-type']; ?>" />
	<input name="dfcg_plugin_settings[custom-post-type-tax]" id="dfcg-custom-post-type-tax" type="hidden" value="<?php echo $dfcg_options['custom-post-type-tax']; ?>" />
	<input name="dfcg_plugin_settings[custom-post-type-number]" id="custom-post-type-number" type="hidden" value="<?php echo $dfcg_options['custom-post-type-number']; ?>" />
	<?php endif; ?>
	
	
<?php }


/**
* Reset box and form end XHTML
*
* 1 options: ['reset']
*
* @global array $dfcg_options plugin options from db
* @since 3.0
*/
function dfcg_ui_reset_end() {
	global $dfcg_options;
	?>
	<div class="reset-sgr">
		<p>
		<input type="checkbox" name="dfcg_plugin_settings[reset]" id="dfcg-reset" value="1" <?php checked('true', $dfcg_options['reset']); ?> />
		<span style="font-weight:bold;padding-left:10px;"><?php _e('Reset all options to the Default settings', DFCG_DOMAIN); ?></span>
		<span style="font-size:11px;padding-left:10px;"><em><?php _e('Check the box, then click the "Save Changes" button.', DFCG_DOMAIN)?></em></span>
		</p>
	</div>
        
	
<?php }


/**
* Credits: box and content
*
* @since 3.0
*/
function dfcg_ui_credits() {
	?>
	<div class="sgr-credits">
		<p><?php _e('For further information please visit these resources:', DFCG_DOMAIN); ?></p>
		<p>
			<a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/"><?php _e('Configuration guide', DFCG_DOMAIN); ?></a> | 
		  	<a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/documentation/"><?php _e('Documentation page', DFCG_DOMAIN); ?></a> | 
			<a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/faq/"><?php _e('FAQ', DFCG_DOMAIN); ?></a> | 
			<a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/error-messages/"><?php _e('Error Messages', DFCG_DOMAIN); ?></a>
		</p>
		<p><?php _e('The Dynamic Content Gallery plugin uses the mootools SmoothGallery script developed by', DFCG_DOMAIN); ?> <a href="http://smoothgallery.jondesign.net/">Jonathan Schemoul</a> <?php _e('and a custom jQuery script developed by', DFCG_DOMAIN); ?> Maxim Palianytsia, <?php _e('and was forked from the original Featured Content Gallery v1.0 developed by Jason Schuller. Grateful acknowledgements to Jonathan, Maxim and Jason.', DFCG_DOMAIN); ?></p> 
		<p><?php _e('Dynamic Content Gallery plugin for Wordpress and Wordpress MultiSite', DFCG_DOMAIN); ?>&nbsp;&copy;&nbsp;2008-2010 <a href="http://www.studiograsshopper.ch/">Ade Walker</a>&nbsp;&nbsp;&nbsp;<strong><?php _e('Version: ', DFCG_DOMAIN); ?><?php echo DFCG_VER; ?></strong></p>      
		
	</div><!-- end sgr-credits -->
<?php }


/**
* Help tab
*
* This content used to be displayed in the WP contextual help dropdown, but is now moved to the DCG Settings
* Page Help tab for simplicity.
*
* @since 3.3
*/
function dfcg_ui_help() {
	?>
	<h3><?php esc_html_e('Dynamic Content Gallery - Quick Help', DFCG_DOMAIN); ?></h3>
	<p><?php esc_html_e('This Quick Help guide highlights some basic points only. Detailed guides to Documentation and Configuration can be found on the plugin\'s site here:', DFCG_DOMAIN); ?></p>
	<p>
		<a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/"><?php esc_html_e('Quick Start', DFCG_DOMAIN); ?></a> | 
		<a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/"><?php esc_html_e('Configuration Guide', DFCG_DOMAIN); ?></a> | 
		<a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/documentation/"><?php esc_html_e('Documentation', DFCG_DOMAIN); ?></a> | 
		<a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/faq/"><?php esc_html_e('FAQ', DFCG_DOMAIN); ?></a> | 
		<a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/error-messages/"><?php esc_html_e('Error Messages', DFCG_DOMAIN); ?></a>
	</p>
		
	<h3 class="not-top"><?php esc_html_e('Understanding the basics', DFCG_DOMAIN); ?></h3>
	<p><?php esc_html_e('Each image in the DCG gallery is linked to a Post or a Page, therefore providing a dynamic and visually appealing way of highlighting your featured content to your site vistors.', DFCG_DOMAIN); ?></p>
	<p><?php esc_html_e('Setting up the DCG involves 2 key settings, both of which are configured in the DCG Settings page:', DFCG_DOMAIN); ?></p>
	<ul>
		<li><?php _e('The <a class="dfcg-panel-gallery-link" href="#dfcg-panel-gallery">Gallery method</a> tab options determine which featured content, ie which Posts and Pages, you want to appear in the gallery. Four Gallery Methods are available:', DFCG_DOMAIN); ?>
			<ol>
				<li><?php esc_html_e('Multi Option - Posts from a mix of Categories.', DFCG_DOMAIN); ?></li>
				<li><?php esc_html_e('One Category - Posts from one selected Category.', DFCG_DOMAIN); ?></li>
				<li><?php esc_html_e('ID Method - Pages, or a mix of Post/Pages, selected by ID number.', DFCG_DOMAIN); ?></li>
				<li><?php esc_html_e('Custom Post Type - Posts from a selected Custom Post Type.', DFCG_DOMAIN); ?></li>
			</ol>
		</li>
		<li><?php _e('The <a class="dfcg-panel-image-link" href="#dfcg-panel-image">Image Management</a> tab options determine how the plugin associates images with the featured Posts/Pages:', DFCG_DOMAIN); ?>
			<ol>
				<li><?php esc_html_e('Auto - which means that the plugin will grab the first Image Attachment from the relevant Posts/Pages, or', DFCG_DOMAIN); ?>
				</li>
				<li><?php _e('Manually - which means you assign images to your posts or pages by entering an image URL in the Image URL field in the DCG Metabox which is shown in the Write Post/Page screen. Select either the Full or Partial <a class="dfcg-panel-image-link" href="#dfcg-panel-image">Image Management</a> options to determine the form of the URL that you enter in the DCG Metabox Image URL field.', DFCG_DOMAIN); ?></li>
			</ol>
		</li>
	</ul>
		
	<p><?php _e('The <a class="dfcg-panel-desc-link" href="#dfcg-panel-desc">Description</a> tab options determine the text which is displayed in the Slide Pane below the Post/Page title, and can be set as an automatically generated excerpt from the relevant Posts or Pages, or entered manually in the Slide Pane Description field in the DCG Metabox. You can also select None, if you don\'t want to display any text in the Slide Pane. ', DFCG_DOMAIN); ?></p>
	
	<p><?php _e('You can also create <a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/#default-images">default images</a> and specify their location on your server so that, in the event an Auto image or DCG Metabox Image URL is missing from a Post or Page, a default image will be shown in its place.', DFCG_DOMAIN); ?><?php _e('Note: The Custom Post Type gallery method does not currently support default images.', DFCG_DOMAIN); ?></p>

	<p><?php _e('There are lots of options for the <a class="dfcg-panel-css-link" href="#dfcg-panel-css">Gallery CSS</a>, as well as various <a class="dfcg-panel-javascript-link" href="#dfcg-panel-javascript">Javascript Options</a> which determine the behaviour of the gallery. There are also <a class="dfcg-panel-scripts-link" href="#dfcg-panel-scripts">Load Scripts</a> options for restricting the loading of the plugin\'s javascript files to reduce the impact of the plugin on page loading times. Finally, you have two choices of javascript framework, mootools or jquery, selected in the <a class="dfcg-panel-javascript-link" href="#dfcg-panel-javascript">Javascript Options</a>, which should help eliminate javascript conflicts with other plugins.', DFCG_DOMAIN); ?></p>
	<p><?php esc_attr_e('Still a bit lost? Find out more in the Configuration Guide =>', DFCG_DOMAIN); ?></p>
	<ul class="links">
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/#gallery-method-options"><?php esc_attr_e('How to choose the correct options for the Gallery Method', DFCG_DOMAIN); ?></a></li>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/#image-file-man-options"><?php esc_attr_e('How to select appropriate Image File management preferences', DFCG_DOMAIN); ?></a></li>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/#media-uploader"><?php esc_attr_e('How to use the Media Uploader to get image URLs for the DCG Metabox', DFCG_DOMAIN); ?></a></li>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/#template-code"><?php esc_attr_e('How to choose the correct theme template when adding the plugin code', DFCG_DOMAIN); ?></a></li>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/#restrict-script"><?php esc_attr_e('How to configure the Restrict Script loading options', DFCG_DOMAIN); ?></a></li>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/#external-link"><?php esc_attr_e('How to link gallery images to external URLs', DFCG_DOMAIN); ?></a></li>
		<li><a class="off-site" href="http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/#default-images"><?php esc_attr_e('How to organise your default images', DFCG_DOMAIN); ?></a></li>
	</ul>
	<p><?php esc_attr_e('Note for Wordpress Network (Multisite) users: Default images are not available in Network-enabled WP.', DFCG_DOMAIN); ?></p>
<?php }