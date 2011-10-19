<?php
/**
* Displays Settings Page
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info Settings page for Wordpress and Wordpress Mu.
*
* 	All UI functions on this page are defined in dfcg-admin-ui-functions.php
*	dfcg_load_textdomain()		- defined in dfcg-admin-core.php
*	dfcg_options_js()			- defined in dfcg-admin-ui-js.php
*	dfcg_on_load_validation()	- defined in dfcg-admin-ui-validation.php
*
* @since 3.0
* @updated 3.3.1
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', DFCG_DOMAIN) );
}


// Load text domain
dfcg_load_textdomain();

// Load Settings Page JS
dfcg_options_js();

// Pull options from db - added in 3.3.1 to solve new options issue
$dfcg_options = get_option( 'dfcg_plugin_settings');

 // Run Settings validation checks on page load
dfcg_on_load_validation($dfcg_options);
?>

<div class="wrap" id="sgr-style">

	<?php screen_icon('options-general');// Display icon next to title ?>
	
	<h2><?php _e('Dynamic Content Gallery Configuration', DFCG_DOMAIN); ?></h2>
	<p><strong><?php _e('Version: ', DFCG_DOMAIN); ?><?php echo DFCG_VER; ?></strong></p>
		
	<form method="post" action="options.php">

	<?php settings_fields('dfcg_plugin_settings_options'); // Settings API, nonces etc ?>
	
	<div id="tabs">
		<ul id="dfcg-tabs">
			<li id="dfcg-tab-general"><a href="#dfcg-panel-general"><?php _e('General', DFCG_DOMAIN) ?></a></li>
			<li id="dfcg-tab-image"><a href="#dfcg-panel-image"><?php _e('Image Management', DFCG_DOMAIN) ?></a></li>
			<li id="dfcg-tab-gallery"><a href="#dfcg-panel-gallery"><?php _e('Gallery Method', DFCG_DOMAIN) ?></a></li>
			<li id="dfcg-tab-desc"><a href="#dfcg-panel-desc"><?php _e('Descriptions', DFCG_DOMAIN) ?></a></li>
			<li id="dfcg-tab-css"><a href="#dfcg-panel-css"><?php _e('Gallery CSS', DFCG_DOMAIN) ?></a></li>
			<li id="dfcg-tab-javascript"><a href="#dfcg-panel-javascript"><?php _e('Javascript Options', DFCG_DOMAIN) ?></a></li>
			<li id="dfcg-tab-scripts"><a href="#dfcg-panel-scripts"><?php _e('Load Scripts', DFCG_DOMAIN) ?></a></li>
			<li id="dfcg-tab-tools"><a href="#dfcg-panel-tools"><?php _e('Tools', DFCG_DOMAIN) ?></a></li>
			<li id="dfcg-tab-help" class="last"><a href="#dfcg-panel-help"><?php _e('Help', DFCG_DOMAIN) ?></a></li>
		</ul>
	
		<div class="dfcg-panel form-table" id="dfcg-panel-general">
			<?php dfcg_ui_intro_menu(); ?>
		</div>
	
		<div class="dfcg-panel form-table" id="dfcg-panel-image">
			<?php dfcg_ui_1_image(); // Image File management ?>
		</div>
	
		<div class="dfcg-panel form-table" id="dfcg-panel-gallery">
			<?php dfcg_ui_2_method(); // Gallery Method ?>
			<?php dfcg_ui_multi(); // Multi-Option ?>
			<?php dfcg_ui_onecat(); // One Category ?>
			<?php dfcg_ui_id(); // Pages ?>
			<?php dfcg_ui_custom_post(); // Custom Post type ?>
		</div>
	
		<div class="dfcg-panel form-table" id="dfcg-panel-desc">
			<?php dfcg_ui_defdesc(); // Default Desc ?>
		</div>
	
		<div class="dfcg-panel form-table" id="dfcg-panel-css">
			<?php dfcg_ui_css(); // Gallery CSS ?>
		</div>
	
		<div class="dfcg-panel form-table" id="dfcg-panel-javascript">
			<?php dfcg_ui_js_framework(); // Javascript Framework options ?>
			<?php dfcg_ui_javascript(); // Javascript configuration options ?>
		</div>
	
		<div class="dfcg-panel form-table" id="dfcg-panel-scripts">
			<?php dfcg_ui_restrict_scripts(); // Restrict Scripts ?>
		</div>
	
		<div class="dfcg-panel form-table" id="dfcg-panel-tools">
			<?php dfcg_ui_errors(); // Error Messages ?>
			<?php dfcg_ui_columns(); // Add Edit Posts/Pages columns ?>
		</div>
	
		<div class="dfcg-panel form-table" id="dfcg-panel-help">
			<?php dfcg_ui_help(); // Help stuff ?>
		</div>
	
		<?php if ( function_exists('wpmu_create_blog') ) {
				// Hidden fields - WPMU ONLY
				dfcg_ui_hidden_wpmu();
			} else {
				// Hidden fields - WP ONLY
				dfcg_ui_hidden_wp();
			}
?>
	</div><!-- end #tabs -->
	
	<?php dfcg_ui_reset_end(); // Reset and End ?>
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" /></p>
	
	</form>
	
	<?php dfcg_ui_credits(); // Credits ?>	
	
</div><!-- end #sgr-style .wrap -->