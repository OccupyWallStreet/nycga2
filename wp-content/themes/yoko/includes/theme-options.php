<?php
/**
 * Yoko Theme Options
 *
 * @package WordPress
 * @subpackage Yoko
 */
 
function yoko_admin_enqueue_scripts( $hook_suffix ) {
	if ( $hook_suffix != 'appearance_page_theme_options' )
		return;

	wp_enqueue_style( 'yoko-theme-options', get_template_directory_uri() . '/includes/theme-options.css', false );
	wp_enqueue_script( 'yoko-theme-options', get_template_directory_uri() . '/includes/theme-options.js', array( 'farbtastic' ) );
	wp_enqueue_style( 'farbtastic' );
}
add_action( 'admin_enqueue_scripts', 'yoko_admin_enqueue_scripts' );
 
// Default options values
$yoko_options = array(
	'custom_color' => '#009BC2',
	'custom_logo' => ''
);

if ( is_admin() ) : // Load only if we are viewing an admin page

function yoko_register_settings() {
	// Register the settings
	register_setting( 'yoko_theme_options', 'yoko_options', 'yoko_validate_options' );
}

add_action( 'admin_init', 'yoko_register_settings' );


function yoko_theme_options() {
	// Add theme options page to the addmin menu
	add_theme_page( __( 'Theme Options', 'yoko'), __( 'Theme Options', 'yoko'), 'edit_theme_options', 'theme_options', 'yoko_theme_options_page');
}

add_action( 'admin_menu', 'yoko_theme_options' );

// Function to generate options page
function yoko_theme_options_page() {
	global $yoko_options, $yoko_categories, $yoko_layouts;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. ?>

	<div class="wrap">

	<?php screen_icon(); echo "<h2>" . get_current_theme() . __( ' Theme Options', 'yoko' ) . "</h2>";
	// This shows the page's name and an icon if one has been provided ?>

	<?php if ( false !== $_REQUEST['updated'] ) : ?>
	<div class="updated fade"><p><strong><?php _e( 'Options saved', 'yoko' ); ?></strong></p></div>
	<?php endif; // If the form has just been submitted, this shows the notification ?>

	<form method="post" action="options.php">

	<?php $settings = get_option( 'yoko_options', $yoko_options ); ?>
	
	<?php settings_fields( 'yoko_theme_options' );
	/* This function outputs some hidden fields required by the form,
	including a nonce, a unique number used to ensure the form has been submitted from the admin page
	and not somewhere else, very important for security */ ?>

	<table class="form-table">

	<tr valign="top"><th scope="row"><label for="custom_color"><?php _e('Custom Link Color', 'yoko'); ?></label></th>
	<td>
	<input id="custom_color" name="yoko_options[custom_color]" type="text" value="<?php  esc_attr_e($settings['custom_color']); ?>" />
	<a href="#" class="pickcolor hide-if-no-js" id="custom_color-example"></a>
	<input type="button" class="pickcolor button hide-if-no-js" value="<?php esc_attr_e( 'Select a Color', 'yoko' ); ?>">
	<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
	<br />
	<small class="description"><?php _e('e.g. #0000FF or blue (default link color: #009BC2)', 'yoko'); ?></small>
	</td>
	</tr>
	
	<tr valign="top"><th scope="row"><label for="custom_logo"><?php _e('Custom Logo Image URL', 'yoko'); ?></label></th>
	<td>
	<input id="custom_logo" name="yoko_options[custom_logo]" type="text" value="<?php  esc_attr_e($settings['custom_logo']); ?>" />
	<span class="description"> <a href="<?php echo home_url(); ?>/wp-admin/media-new.php" target="_blank"><?php _e('Upload your own logo image', 'yoko'); ?></a> <?php _e(' using the WordPress Media Library and insert the URL here', 'yoko'); ?> </span>
	<br/><img style="margin-top: 10px;" src="<?php echo (get_option('custom_logo')) ? get_option('custom_logo') : get_template_directory_uri() . '/images/logo.png' ?>" />
	</td>
	</tr>
	</table>

	<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Options', 'yoko'); ?>" /></p>

	</form>

	</div>

	<?php
}

function yoko_validate_options( $input ) {
	global $yoko_options, $yoko_categories, $yoko_layouts;

	$settings = get_option( 'yoko_options', $yoko_options );
	
	// We strip all tags from the text field, to avoid vulnerablilties like XSS
	$input['custom_color'] = wp_filter_nohtml_kses( $input['custom_color'] );
	
	return $input;
}

endif;  // EndIf is_admin()


// Custom CSS for Link Colors
function insert_custom_color(){
?>

<?php 
	global $yoko_options;
	$yoko_settings = get_option( 'yoko_options', $yoko_options );
?>
<?php if( $yoko_settings['custom_color'] != '' ) : ?>
<style type="text/css">
a {color: <?php echo $yoko_settings['custom_color'] ; ?>!important;}
#content .single-entry-header h1.entry-title {color: <?php echo $yoko_settings['custom_color'] ; ?>!important;}
input#submit:hover {background-color: <?php echo $yoko_settings['custom_color'] ; ?>!important;}
#content .page-entry-header h1.entry-title {color: <?php echo $yoko_settings['custom_color'] ; ?>!important;}
.searchsubmit:hover {background-color: <?php echo $yoko_settings['custom_color'] ; ?>!important;}
</style>
<?php endif; ?>
<?php
}

add_action('wp_head', 'insert_custom_color');
