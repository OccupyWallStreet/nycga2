<?php
/**
* Displays the special Settings pages for the version 3.2 wp_postmeta meta_key name upgrade
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info Special upgrade screen for one-off upgrade of postmeta data.
* @info Included by dfcg_options_page()
*
*	All UI functions on this page are defined in dfcg-admin-postmeta_upgrade.php
*	dfcg_load_textdomain() is defined in dynamic-admin-core.php
*	dfcg_options_js() is defined in dfcg-admin-ui-js.php
*
*	This file is included if upgrade completed option !== completed
*
* @since 3.2
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', DFCG_DOMAIN) );
}


// Load text domain
dfcg_load_textdomain();

// Load Settings Page JS
dfcg_options_js();

// Handle the form submission Page 1
if( isset($_POST['dfcg_upgrade_1']) ) {
	
	// Is the user allowed to do this?
	if ( function_exists('current_user_can') && !current_user_can('manage_options') )
		die(__('Sorry. You do not have permission to do this.'));
	
	// check the nonce
	check_admin_referer( 'dfcg_plugin_postmeta_upgrade' );
	
	// build the array from input (1 item)
	$input = $_POST['dfcg_plugin_postmeta_upgrade'];
	
	// Make sure we only run this once
	if( $input['upgraded'] == 'started' ) {
		
		$data = dfcg_update_postmeta();
		
		if( $data ) {
		
			// Update the $input array with results from dfcg_update_postmeta() function
			$input['postmetas'] = $data['postmetas'];
			$input['modified'] = $data['modified'];
			$input['deleted'] = $data['deleted'];
			$input['dfcg-image'] = $data['dfcg-image'];
			$input['dfcg-desc'] = $data['dfcg-desc'];
			$input['dfcg-link'] = $data['dfcg-link'];
		}
		
		
		// Update the db options
		update_option( 'dfcg_plugin_postmeta_upgrade', $input);
	}
}
// Get the updated db options
$dfcg_postmeta_upgrade = get_option('dfcg_plugin_postmeta_upgrade');
 ?>

<div class="wrap" id="sgr-style">

	<?php screen_icon('options-general');// Display icon next to title ?>
	
	<h2><?php _e('Dynamic Content Gallery Custom Field Upgrade', DFCG_DOMAIN); ?></h2>
	
	<div class="metabox-holder">
		
<?php
if( $dfcg_postmeta_upgrade['upgraded'] == '' ) {

	/* Output pre-upgrade screen contents */
	dfcg_ui_upgrade_page1();
	
} elseif( $dfcg_postmeta_upgrade['upgraded'] == 'started' ) {
	
	/* Output post-upgrade screen contents */
	dfcg_ui_upgrade_page2($dfcg_postmeta_upgrade);
}

// Credits
dfcg_ui_credits();
?>
	</div><!-- end meta-box holder -->
	
</div><!-- end sgr-style wrap -->