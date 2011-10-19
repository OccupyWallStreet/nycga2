<?php
/**
* Functions for the version 3.2 wp_postmeta meta_key name upgrade
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info Functions to upgrade wp_postmeta, and display upgrade admin screens
*
* @info The upgrade converts existing custom fields: dfcg-image, dfcg-desc, dfcg-link to _dfcg-image, _dfcg-desc, _dfcg-link.
* @info From 3.2+ DCG custom fields will be handled by DCG metabox, and won't appear in custom field edit boxes
*
* This is all quite complicated thanks to different functions/behaviour when upgrading in WPMU.
* Upgrade screens are handled in dfcg-admin-ui-upgrade-screen.php
*
* Decision to run upgrade or not is based on existence of $dfcg_postmeta_upgrade['upgraded'] == 'completed'
* This decision is made in dfcg_options_page() via dfcg_add_to_options_menu() which is hooked to 'admin_menu'
* If !== 'completed', dfcg-admin-ui-upgrade-screen.php is loaded, which calls the functions in this file.
* In WP - upgrade is run via 2 Settings screens
* In WPMU - upgrade is run using Site Admin Upgrade screen (Settings screen is info only, and doesn't do anything)
*
* Database postmeta function
* WPMU Site Admin Upgrade function
* WPMU Site Admin Upgrade page function
* Admin Notices function
* Functions for displaying upgrade screen contents
*
* @since 3.2
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit("Sorry, you are not allowed to access this file directly.");
}



/* Admin - Adds Admin Notice to prompt postmeta upgrade */
// Function defined in dfcg-admin-postmeta-upgrade.php
add_action('admin_notices', 'dfcg_admin_notice_postmeta');

/* Admin - Hooks the WPMU Site Upgrade function */
// Function defined in dfcg-admin-postmeta-upgrade.php
add_action('wpmu_upgrade_site', 'dfcg_admin_postmeta_upgrade_wpmu', 10, 1);

/* Admin - Adds Info to WPMU Site Admin Upgrade screen */
// Function defined in dfcg-admin-postmeta-upgrade.php
add_action('wpmu_upgrade_page', 'dfcg_admin_postmeta_wpmu_page');



/**
* Converts wp_postmeta meta_key names from x to _x
*
* Converts dfcg-image, dfcg-desc, dfcg-link
*
* @global array $wpdb WP database object
* @global int $id WPMU blog ID
* @return array $output Array of database upgrade results
* @since 3.2
*/
function dfcg_update_postmeta() {

	global $wpdb;
	
	// Get the postmeta table depending if WP or WPMU
	if( function_exists('wpmu_create_blog') ) {
		// Blog ID
		global $id;
		$db_table = $wpdb->get_blog_prefix( $id ) . "postmeta";
	} else {
		$db_table = $wpdb->postmeta;
	}

	$metas = $wpdb->get_results(
		$wpdb->prepare("SELECT * FROM $db_table WHERE meta_key = %s OR meta_key = %s OR meta_key = %s", 'dfcg-desc', 'dfcg-image', 'dfcg-link')
		);
		
	if( $metas ) {
		
		// Initialise counters
		$metas_count = count($metas);
		$update_counter = 0;
		$delete_counter = 0;
		$img_counter = 0;
		$desc_counter = 0;
		$link_counter = 0;
		
		// Update loop
		foreach ($metas as $meta) {
			
			$meta_key = $meta->meta_key;
			$new_meta_key = '_' . $meta->meta_key;
			$meta_value = $meta->meta_value;
			
			// Grab some stats
			if( $meta_key == 'dfcg-image' ) {
				$img_counter++;
			}
			if( $meta_key == 'dfcg-desc' ) {
				$desc_counter++;
			}
			if( $meta_key == 'dfcg-link' ) {
				$link_counter++;
			}
			
			// Add new postmeta
			update_post_meta($meta->post_id, $new_meta_key, $meta_value);
			// Increment loop counter
			$update_counter++;
		}
		
		// Delete loop
		foreach ($metas as $stuff) {
			
			// Delete old postmeta
			delete_post_meta($stuff->post_id, $stuff->meta_key, $stuff->meta_value);
			// Increment loop counter
			$delete_counter++;
		}
		
		// Build results array
		$output['postmetas'] = $metas_count;
		$output['modified'] = $update_counter;
		$output['deleted'] = $delete_counter;
		$output['dfcg-image'] = $img_counter;
		$output['dfcg-desc'] = $desc_counter;
		$output['dfcg-link'] = $link_counter;
		
	} else {
		// There are no records but we need a full array
		$output['postmetas'] = esc_attr('None found');
		$output['modified'] = esc_attr('None');
		$output['deleted'] = esc_attr('None');
		$output['dfcg-image'] = esc_attr('None');
		$output['dfcg-desc'] = esc_attr('None');
		$output['dfcg-link'] = esc_attr('None');
	}
	// Not sure we need this...
	$wpdb->flush();
	
	// Return results array
	return $output;
}


/**
* WPMU postmeta upgrade function
*
* Hooked to 'wpmu_upgrade_site'
*
* This function is run when Site Admin performs Site Admin Upgrade
* Hooked into 'wpmu_upgrade_site', this is run once per Blog
* Tests if Blog needs upgrading and, if so, runs the postmeta upgrade function
*
* @param int $id WPMU Blog ID
* @uses dfcg_update_postmeta()
* @since 3.2
*/
function dfcg_admin_postmeta_upgrade_wpmu($id) {
	$upgrade = get_blog_option($id, 'dfcg_plugin_postmeta_upgrade');
	if( $upgrade['upgraded'] !== 'completed' ) {
		$data = dfcg_update_postmeta();
		$data['upgraded'] = 'completed';
		update_blog_option($id, 'dfcg_plugin_postmeta_upgrade', $data, false);
		echo 'Dynamic Content Gallery v3.2 postmeta upgrade completed for blog ID: ' . $id;
	} else {
		echo 'Dynamic Content Gallery v3.2 postmeta upgrade not required for blog ID: ' . $id;
	}
}


/**
* WPMU Site Admin Upgrade screen text
*
* Hooked to 'wpmu_upgrade_page'
*
* Shows info text for Site Admin when on Site Admin Upgrade screen
*
* @global array $dfcg_postmeta_upgrade plugin upgrade options from db
* @since 3.2
*/
function dfcg_admin_postmeta_wpmu_page() {
	
	global $dfcg_postmeta_upgrade;
	//$upgrade = get_option('dfcg_plugin_postmeta_upgrade');
	if( $dfcg_postmeta_upgrade['upgraded'] !== 'completed' ) {
	?>
		<div style="float:left;width:690px;">
			<div class="dfcg-postmeta-db-info" style="margin-top:20px;">
			<p><strong><?php _e("Information for Site Administrator(s):", DFCG_DOMAIN); ?></strong></p>
			<p><?php _e('To complete the installation of the Dynamic Content Gallery v3.2 please run a Site Admin Upgrade. Click the "Upgrade Site" button above to perform this Site Admin Upgrade.', DFCG_DOMAIN); ?></p>
			<p><?php _e("Note: The plugin will continue to display the gallery correctly without this Site Admin Upgrade, but Blog Owners will not be able to administer the plugin's Settings page until this is run.", DFCG_DOMAIN); ?></p>
			<p><?php _e("This upgrade routine makes permanent changes to the postmeta tables for all Blogs. It is advisable to backup the database before proceeding!", DFCG_DOMAIN); ?></p>
			<p><?php _e('Further information about this upgrade can be found', DFCG_DOMAIN); ?> <a href="http://www.studiograsshopper.ch/wordpress-plugins/dynamic-content-gallery-v3-2-released/"><?php _e('here', DFCG_DOMAIN); ?></a>.</p>
			</div>
		</div>
<?php
	}
}


/**
* Function to display Admin Notices related to Postmeta upgrade
*
* Displays Admin Notices re postmeta upgrade
*
* Hooked to 'admin_notices' action
*
* @since 3.2
*/	
function dfcg_admin_notice_postmeta() {
	
	// This may have been updated since last page load
	$dfcg_postmeta_upgrade = get_option('dfcg_plugin_postmeta_upgrade');
	
	// Upgrade has finished, or this was a new install, let's get outta here
	if( $dfcg_postmeta_upgrade['upgraded'] == 'completed' ) {
		return;
	}
	
	// OK, we have some Admin Notices to sort out
	$red_msg_start = '<div id="message" class="error"><p style="font-size:11px;line-height:20px;"><strong>';
	$green_msg_start = '<div class="updated fade" style="background-color:#ecfcde; border:1px solid #a7c886;"><p><strong>';
	$msg_end = '</p></div>';
	
	$current_page_raw = basename($_SERVER['PHP_SELF']);
	
	
	// We're in WP
	if( !function_exists('wpmu_create_blog') ) {
	
		$get_var_page = '';
		$get_var_upgrade = '';
		
		if( !empty($_GET['upgraded']) ) {
			$get_var_upgrade = $_GET['upgraded'];
		}
		
		if( !empty($_GET['page']) ) {
			$get_var_page = $_GET['page'];
		}
		
		$current_page = $current_page_raw . '?page=' . $get_var_page;
		
		// We're on DCG Settings page and halfway through upgrade process
		if( $current_page == 'options-general.php?page=' . DFCG_FILE_HOOK && $get_var_upgrade == 'started' ) {
			
			echo $green_msg_start . __('Congratulations!', DFCG_DOMAIN) .'</strong> ' . __('Dynamic Content Gallery Custom Field data has been successfully updated.', DFCG_DOMAIN) . $msg_end;
		
		// We're on DCG Settings page and 'upgraded' doesn't exist
		} elseif( $current_page == 'options-general.php?page=' . DFCG_FILE_HOOK && empty($dfcg_postmeta_upgrade['upgraded']) ) {
			
			echo $red_msg_start . __('Important!', DFCG_DOMAIN) . '</strong> ' . __('Dynamic Content Gallery Custom Field data must be upgraded. Please follow the instructions on this page.', DFCG_DOMAIN) . $msg_end;
		
		// We're not on DCG Settings page and 'upgraded' doesn't exist
		} elseif( empty($dfcg_postmeta_upgrade['upgraded']) ) {
		
			echo $red_msg_start . __('Important!', DFCG_DOMAIN) . '</strong> ' . __('To complete the installation of Dynamic Content Gallery v3.2 your gallery Custom Field data must be upgraded.', DFCG_DOMAIN) .'&nbsp;'. __('Go to the', DFCG_DOMAIN) . ' <a href="./options-general.php?page=dynamic_content_gallery">' . __('Settings Page', DFCG_DOMAIN) . '</a> ' . __('to run this upgrade.', DFCG_DOMAIN) . $msg_end;
	
		}
	
	// We're in WPMU
	} else {
		
		if( is_site_admin() == true && $current_page_raw == 'wpmu-upgrade-site.php' ) {
			// No need to show the Admin Notice
			return;
		
		} elseif( is_site_admin() == true ) {
			// WPMU - For Site Admin
			echo $red_msg_start . __('Important message for Site Admin!', DFCG_DOMAIN) . '</strong> ' . __('To complete the installation of the Dynamic Content Gallery v3.2 please run a', DFCG_DOMAIN) .'&nbsp;<a href="./wp-admin/wpmu-upgrade-site.php">' . __('Site Admin Upgrade.', DFCG_DOMAIN) . '</a> ' . __('The gallery will not work until this upgrade is performed.', DFCG_DOMAIN) . $msg_end;
		
		} else {
			// WPMU - What non Site Admins see
			echo $red_msg_start . __('Important message for Site Admin!', DFCG_DOMAIN) . '</strong> ' . __('A new version of the Dynamic Content Gallery has been installed which requires your Site Administrator to run a Site Admin Upgrade.', DFCG_DOMAIN) .'&nbsp;'. __('Please contact your Site Administrator.', DFCG_DOMAIN) . ' <a href="http://www.studiograsshopper.ch/dynamic-content-gallery/">' . __('Read more', DFCG_DOMAIN) . '</a>' . $msg_end;
		}
	}
}



/**
* Displays first upgrade Settings page - WP and WPMU
*
* Helper function to separate WP from WPMU upgrade screen content
*
* Called by dfcg-admin-ui-upgrade-screen.php
*
* @uses dfcg_ui_upgrade_page1_wp_content()
* @uses dfcg_ui_upgrade_page1_wpmu_content()
*
* @since 3.2
*/
function dfcg_ui_upgrade_page1() {
	
	if( !function_exists('wpmu_create_blog') ) {
		dfcg_ui_upgrade_page1_wp_content();
	} else {
		dfcg_ui_upgrade_page1_wpmu_content();
	}
}


/**
* Displays contents of first Upgrade Settings page - WP only
*
* Called by dfcg_ui_upgrade_page1()
*
* The form uses $_POST rather than Settings API, due to problems with db update in Settings API callback function
*
* On Submit, creates array of input called $dfcg_plugin_postmeta_upgrade
* This array includes a hidden field 'upgraded' with a value of 'started', the latter being used
* by dfcg-admin-ui-upgrade-screen.php to determine whether to run the db upgrade function.
* Form Action also adds &upgraded=started to query string, which is needed by dfcg_admin_notice_postmeta()
* to display the Congratulations admin notice.
*
* @since 3.2
*/
function dfcg_ui_upgrade_page1_wp_content() {	
	?>
<div class="postbox">
	<h3><?php _e("Custom Field Upgrade", DFCG_DOMAIN); ?></h3>
	<div class="inside">
		<div style="float:left;width:690px;">
			<h4><?php _e('Step 1 of 2', DFCG_DOMAIN); ?></h4>
		
			<form method="post" action="<?php echo htmlspecialchars( add_query_arg( 'upgraded', 'started' ) ); ?>">
			
			<?php wp_nonce_field('dfcg_plugin_postmeta_upgrade'); // Set nonce... ?>
			
			<p><?php _e("Although you have successfully upgraded to version 3.2 of the Dynamic Content Gallery, you now need to run this upgrade routine for the plugin to work.", DFCG_DOMAIN); ?></p>
			<ul class="upgrade">
				<li><strong><?php _e('What does it do?', DFCG_DOMAIN); ?></strong> <?php _e("This upgrade routine converts the existing dfcg-desc, dfcg-image and dfcg-link custom field names, which are currently stored in your database, to the new format introduced in version 3.2 of the plugin.", DFCG_DOMAIN); ?></li>
				<li><strong><?php _e('What does it do exactly?', DFCG_DOMAIN); ?></strong> <?php _e("When you click the Run Upgrade button, the plugin searches your database postmeta table (where your custom fields are stored), finds all existing references to dfcg-desc, dfcg-image and dfcg-link, and renames these custom field names to _dfcg-desc, _dfcg-image and _dfcg-link.", DFCG_DOMAIN); ?></li>
				<li><strong><?php _e('What about the data stored in these custom fields?', DFCG_DOMAIN); ?></strong> <?php _e("Don't worry, your custom field values (URLs, descriptions, external links), stored with these custom field names, are not modified during this process. The Dynamic Content Gallery should work normally once the upgrade has been performed.", DFCG_DOMAIN); ?></li>
			</ul>
			<div class="dfcg-postmeta-db-info">
				<p><strong><?php _e("ATTENTION!", DFCG_DOMAIN); ?></strong> <strong><?php _e("This upgrade routine makes permanent changes to your database. It is advisable to backup your database before proceeding!", DFCG_DOMAIN); ?></strong></p>
			</div>
			<p><?php _e('To learn more about this upgrade please visit the', DFCG_DOMAIN); ?> <a href="http://www.studiograsshopper.ch/dynamic-content-gallery/postmeta-upgrade/"><?php _e('Custom field postmeta upgrade', DFCG_DOMAIN); ?></a> <?php _e('page.', DFCG_DOMAIN); ?></p>
			<p><?php _e("Please note that if you are using the dfcg-desc, dfcg-image and dfcg-link custom fields for purposes unrelated to the DCG, you will need to update any references to these custom fields in your theme's template files.", DFCG_DOMAIN); ?></p>
			
			<input name="dfcg_plugin_postmeta_upgrade[modified]" id="dfcg-modified" type="hidden" value="0" />
			<input name="dfcg_plugin_postmeta_upgrade[postmetas]" id="dfcg-postmetas" type="hidden" value="0" />
			<input name="dfcg_plugin_postmeta_upgrade[upgraded]" id="dfcg-upgraded" type="hidden" value="started" />
			<div style="float:left;width:400px;margin:0;padding:0;">
				<p class="submit"><input class="button-primary" name="dfcg_upgrade_1" type="submit" value="<?php _e('Run Upgrade'); ?>" /></p>
			</div>
			</form>
			
		</div>
						
		<?php dfcg_ui_upgrade_sgr_info(); ?>
										
		<div style="clear:both;"></div>
	</div><!-- end Postbox inside -->
</div><!-- end Postbox -->
<?php }


/**
* Displays contents of Upgrade Settings page - WPMU only
*
* Called by dfcg_ui_upgrade_page1()
*
* Site Admin gets additional info - see conditional
*
* @since 3.2
*/
function dfcg_ui_upgrade_page1_wpmu_content() {	
	?>
<div class="postbox">
	<h3><?php _e("Custom Field Upgrade Required", DFCG_DOMAIN); ?></h3>
	<div class="inside">
		<div style="float:left;width:690px;">
			<?php if( is_site_admin() ) : ?>
			<div class="dfcg-postmeta-db-info" style="margin-top:20px;">
				<p><strong><?php _e("Information for Site Administrator(s):", DFCG_DOMAIN); ?></strong></p>
				<p><?php _e('To complete the installation of the Dynamic Content Gallery v3.2, please run a', DFCG_DOMAIN); ?> <a href="./wp-admin/wpmu-upgrade-site.php"><?php _e('Site Admin Upgrade', DFCG_DOMAIN); ?></a></p>
				<p><?php _e("Note: The plugin will continue to display the gallery correctly without this Site Admin Upgrade, but Blog Owners will not be able to administer the plugin's Settings page until this is run.", DFCG_DOMAIN); ?></p>
				<p><?php _e("This upgrade routine makes permanent changes to the postmeta tables for all Blogs. It is advisable to backup the database before proceeding!", DFCG_DOMAIN); ?></p>
				<p><?php _e('The information shown below is displayed to Blog Admins in place of the normal Dynamic Content Gallery Settings page. Please note that the gallery will not work properly until the Site Admin Upgrade is performed.', DFCG_DOMAIN); ?></p>
			</div>
			<?php endif; ?>
			
			<h4><?php _e('Information for Wordpress Multisite (formerly Wordpress Mu)', DFCG_DOMAIN); ?></h4>
			<p><?php _e("The Dynamic Content Gallery plugin installed on this site has been upgraded to version 3.2.", DFCG_DOMAIN); ?></p>
			<p><?php _e("In order to complete the installation your Site Administrator needs to perform a Site Admin Upgrade. If you can see this screen, it means that this Site Admin Upgrade has not yet been performed. Please contact your Site Administrator.", DFCG_DOMAIN); ?></p>
			<h4><?php _e("About the Dynamic Content Gallery Custom Field Upgrade", DFCG_DOMAIN); ?></h4>
			<ul class="upgrade">
				<li><strong><?php _e('What does it do?', DFCG_DOMAIN); ?></strong> <?php _e("This upgrade routine converts the existing dfcg-desc, dfcg-image and dfcg-link custom field names, which are currently stored in your database, to the new format introduced in version 3.2 of the plugin.", DFCG_DOMAIN); ?></li>
				<li><strong><?php _e('What does it do exactly?', DFCG_DOMAIN); ?></strong> <?php _e("When the Site Admin Upgrade is performed, the plugin searches your database postmeta table (where your custom fields are stored), finds all existing references to dfcg-desc, dfcg-image and dfcg-link, and renames these custom field names to _dfcg-desc, _dfcg-image and _dfcg-link.", DFCG_DOMAIN); ?></li>
				<li><strong><?php _e('What about the data stored in these custom fields?', DFCG_DOMAIN); ?></strong> <?php _e("Don't worry, your custom field values (URLs, descriptions, external links), stored with these custom field names, are not modified during this process. The Dynamic Content Gallery should work normally once the upgrade has been performed.", DFCG_DOMAIN); ?></li>
			</ul>
			<p><?php _e('To learn more about this upgrade please visit the', DFCG_DOMAIN); ?> <a href="http://www.studiograsshopper.ch/dynamic-content-gallery/postmeta-upgrade/"><?php _e('Custom field postmeta upgrade', DFCG_DOMAIN); ?></a> <?php _e('page.', DFCG_DOMAIN); ?></p>
					
		</div>
						
		<?php dfcg_ui_upgrade_sgr_info(); ?>
										
		<div style="clear:both;"></div>
	</div><!-- end Postbox inside -->
</div><!-- end Postbox -->
<?php }


/**
* Displays second upgrade Settings page - WP only
*
* This function is run if $dfcg_plugin_postmeta_upgrade['upgraded'] == 'started'
* See dfcg-admin-ui-upgrade-screen.php
* Switches $dfcg_plugin_postmeta_upgrade['upgraded'] to 'completed' and updates db options
*
* @param $dfcg_postmeta_upgrade array db options
* @since 3.2
*/
function dfcg_ui_upgrade_page2($dfcg_postmeta_upgrade) {
	
	// Update database option to "completed" status
	$dfcg_postmeta_upgrade['upgraded'] = esc_attr('completed');
	update_option( 'dfcg_plugin_postmeta_upgrade', $dfcg_postmeta_upgrade );
	?>
<div class="postbox">
	<h3><?php _e("Upgrade Completed", DFCG_DOMAIN); ?></h3>
	<div class="inside">
		<div style="float:left;width:690px;">
			<h4><?php _e('Step 2 of 2', DFCG_DOMAIN); ?></h4>
			
			<p><?php _e("Results of custom field upgrade are shown below.", DFCG_DOMAIN); ?></p>
			<div class="sgr-postmeta" style="background-color:#ecfcde; border:1px solid #a7c886;padding:0px 15px;">
				<p><strong><?php _e('Gallery custom field names found in _postmeta table:', DFCG_DOMAIN); ?></strong></p>
				<table class="optiontable form-table">
				<tbody>
				<tr valign="top">
				<th scope="row"><strong>dfcg-image :</strong></th>
				<td><?php echo $dfcg_postmeta_upgrade['dfcg-image']; ?></td></tr>
				<tr valign="top">
				<th scope="row"><strong>dfcg-desc :</strong></th>
				<td><?php echo $dfcg_postmeta_upgrade['dfcg-desc']; ?></td></tr>
				<tr valign="top">
				<th scope="row"><strong>dfcg-link :</strong></th>
				<td><?php echo $dfcg_postmeta_upgrade['dfcg-link']; ?></td></tr>
				<tr valign="top">
				<th scope="row"><strong><?php _e('Total', DFCG_DOMAIN); ?> :</strong></th>
				<td><?php echo $dfcg_postmeta_upgrade['postmetas']; ?></td></tr>
				<tr valign="top">
				<th scope="row"><strong><?php _e('Number of records updated:', DFCG_DOMAIN); ?></th>
				<td><?php echo $dfcg_postmeta_upgrade['modified']; ?></td></tr>
				</tbody>
				</table>
				
			</div>
			<p><?php _e("The upgrade is completed. Click Finish to continue.", DFCG_DOMAIN); ?></p>
					
			<div style="width:300px;margin:0;padding:0;margin:25px 0 0 0;">
				<p class="submit"><a class="button-primary" href="../wp-admin/options-general.php?page=dynamic_content_gallery&upgrade=completed" title="Finish"><?php _e('Finish', DFCG_DOMAIN); ?></a></p>
			</div>
			</form>			

		</div>
						
		<?php dfcg_ui_upgrade_sgr_info(); ?>
										
		<div style="clear:both;"></div>
	</div><!-- end Postbox inside -->
</div><!-- end Postbox -->
<?php }


/**
* Displays SGR info box on postmeta upgrade Settings pages - WP and WPMU
*
* @since 3.2
*/
function dfcg_ui_upgrade_sgr_info() {
?>
<div class="postbox" id="sgr-info">	
	<h4><?php _e('Resources &amp; Support', DFCG_DOMAIN); ?></h4>
	<p><a href="http://www.studiograsshopper.ch"><img src="<?php echo DFCG_URL . '/admin-assets/sgr_icon_75.jpg'; ?>" alt="studiograsshopper" /></a><strong><?php _e('Dynamic Content Gallery for WP and WPMU', DFCG_DOMAIN); ?></strong>.<br /><?php _e('Version', DFCG_DOMAIN); ?> <?php echo DFCG_VER; ?><br /><?php _e('Author', DFCG_DOMAIN); ?>: <a href="http://www.studiograsshopper.ch/">Ade Walker</a></p>
	<ul>
		<li><a href="http://www.studiograsshopper.ch/dynamic-content-gallery/"><?php _e('Plugin Home page', DFCG_DOMAIN); ?></a></li>
		<li><a href="http://www.studiograsshopper.ch/forum/"><?php _e('Support Forum', DFCG_DOMAIN); ?></a></li>
	</ul>
</div><!-- end sgr-info -->
<?php }