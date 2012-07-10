<?php

// If BuddyPress is not activated, switch back to the default WP theme
if ( !defined( 'BP_VERSION' ) )
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	
// Set up theme. Taken mostly from bp-default theme. 
function bp_dtheme_setup() {
	global $bp;

	// Load the AJAX functions for the theme
	require( TEMPLATEPATH . '/_inc/ajax.php' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'buddypress' ),
	) );
	
	if ( !is_admin() ) {
		// Register buttons for the relevant component templates
		// Friends button
		if ( bp_is_active( 'friends' ) )
			add_action( 'bp_member_header_actions',    'bp_add_friend_button' );

		// Activity button
		if ( bp_is_active( 'activity' ) )
			add_action( 'bp_member_header_actions',    'bp_send_public_message_button' );

		// Messages button
		if ( bp_is_active( 'messages' ) )
			add_action( 'bp_member_header_actions',    'bp_send_private_message_button' );

		// Group buttons
		if ( bp_is_active( 'groups' ) ) {
			add_action( 'bp_group_header_actions',     'bp_group_join_button' );
			add_action( 'bp_group_header_actions',     'bp_group_new_topic_button' );
			add_action( 'bp_directory_groups_actions', 'bp_group_join_button' );
		}

		// Blog button
		if ( bp_is_active( 'blogs' ) )
			add_action( 'bp_directory_blogs_actions',  'bp_blogs_visit_blog_button' );
	}
}
add_action( 'after_setup_theme', 'bp_dtheme_setup' );

// Load up Frisco theme options
require_once( get_stylesheet_directory() . '/theme-options.php' );

// Add main CSS and Google Font CSS	
function bp_dtheme_enqueue_styles() {
	// Bump this when changes are made to bust cache
	$version = '20111003';
	// Register our main stylesheet
		wp_enqueue_style( 'bp-default-main', get_template_directory_uri() . '/_inc/css/default.css', array(), $version );
	// Main CSS
		wp_enqueue_style( 'frisco-main', get_stylesheet_directory_uri() . '/style.css', array(), $version );
	// Google Font CSS
	$options = get_option('frisco_theme_options');
		wp_enqueue_style( 'frisco-fonts', 'http://fonts.googleapis.com/css?family=' . str_replace(" ", "+", $options['googlefont'] ) );
}
add_action( 'wp_print_styles', 'bp_dtheme_enqueue_styles' );

// Add color choice CSS from theme options. 
add_action('wp_print_styles', 'frisco_add_colorcss');
function frisco_add_colorcss() {
	// If theme options are not saved in the database
	if( !get_option( 'frisco_theme_options' ) ) { 
		// Load default color stylesheet
		wp_register_style('frisco-color-css', get_stylesheet_directory_uri() . '/css/default.css');
	} else {
		// Load stylesheet for color choice
		$options = get_option('frisco_theme_options');
		wp_register_style('frisco-color-css', get_stylesheet_directory_uri() . '/css/' . $options['themecolor'] . '.css');
	}
		wp_enqueue_style( 'frisco-color-css');
}

// Add custom.css, if selected in theme options. 
add_action('wp_print_styles', 'frisco_add_customcss');
function frisco_add_customcss() {

	$options = get_option('frisco_theme_options');
	
	if ( $options['customcss'] == 1 ) {
    // Load custom.css
	    wp_register_style('customcss', get_stylesheet_directory_uri() . '/custom.css');
		wp_enqueue_style( 'customcss');
	} else {
    // Do nothing
	}
}

// Load up functions-custom.php, if the user has selected that option in theme options.
add_action( 'after_setup_theme', 'frisco_add_custom_functions' );
function frisco_add_custom_functions() {
	$options = get_option('frisco_theme_options');
	
	if ( $options['customphp'] == 1 ) {
		get_template_part('functions-custom');
	}
}

// Add Google font CSS to header 
function frisco_add_google_font_css() {
 $options = get_option('frisco_theme_options');
?>
	<style type="text/css">
		#header h1 a { font-family: "<?php echo $options['googlefont']; ?>","Helvetica Neue",Helvetica,Arial,sans-serif; }
	</style>
<?php
}
add_action ( 'wp_head', 'frisco_add_google_font_css' );

// Add viewport settings for mobile access. From Less Framework (http://lessframework.com/)
function frisco_add_responsive() {
	?>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
	<?php 
}
add_action ( 'bp_head', 'frisco_add_responsive' );

// Show friendly message upon theme activation
function bp_dtheme_show_notice() {
	global $pagenow;

	// Bail if Frisco theme was not just activated
	if ( empty( $_GET['activated'] ) || ( 'themes.php' != $pagenow ) || !is_admin() )
		return;
	?>

	<div id="message" class="updated fade">
		<p><?php printf( __( 'Theme activated! This theme contains <a href="%s">a few options</a> and <a href="%s">sidebar widgets</a>.', 'buddypress' ), admin_url( 'themes.php?page=theme_options' ), admin_url( 'widgets.php' ) ) ?></p>
	</div>

	<style type="text/css">#message2, #message0 { display: none; }</style>

	<?php
}
add_action( 'admin_notices', 'bp_dtheme_show_notice' );

// Use a better image than default mystery man
function frisco_core_set_avatar_constants() {
	define( 'BP_AVATAR_DEFAULT', get_stylesheet_directory_uri() . '/images/mystery-man.jpg' );
	define( 'BP_AVATAR_DEFAULT_THUMB', get_stylesheet_directory_uri() . '/images/mystery-man-50.jpg' );
}
add_action( 'bp_init', 'frisco_core_set_avatar_constants', 2 );

// Adding full width backgrounds requires some extra divs. We'll add those using actions starting below. 
function frisco_div_bp_before_header() {
	?>
		<div id="bp-before-header" class="fullwidth">
	<?php 
}
add_action ( 'bp_before_header', 'frisco_div_bp_before_header' );

// close the bp-before-header div
function frisco_div_bp_after_header() {
	?>
		</div> 
	<?php
}
add_action ( 'bp_after_header', 'frisco_div_bp_after_header' );

// Adding full width backgrounds requires some extra divs.
function frisco_div_bp_before_container() {
	?>
		<div id="bp-before-container" class="fullwidth">
	<?php 
}
add_action ( 'bp_before_container', 'frisco_div_bp_before_container' );

// close the bp-before-container div
function frisco_div_bp_after_container() {
	?>
		</div> 
	<?php
}
add_action ( 'bp_after_container', 'frisco_div_bp_after_container' );

// Adding full width backgrounds requires some extra divs.
function frisco_div_bp_before_footer() {
	?>
		<div id="bp-before-footer" class="fullwidth">
	<?php 
}
add_action ( 'bp_before_footer', 'frisco_div_bp_before_footer' );

// close the bp-before-footer div
function frisco_div_bp_after_footer() {
	?>
		</div> 
	<?php
}
add_action ( 'bp_after_footer', 'frisco_div_bp_after_footer' );


// Adding full width backgrounds requires some extra divs.
function frisco_div_bp_before_activity_post_form() {
	?>
		<div id="bp-before-activity-post-form">
	<?php 
}
add_action ( 'bp_before_activity_post_form', 'frisco_div_bp_before_activity_post_form' );

// close the bp-before-activity-post-form div
function frisco_div_bp_after_activity_post_form() {
	?>
		</div> 
	<?php
}
add_action ( 'bp_after_activity_post_form', 'frisco_div_bp_after_activity_post_form' );

// Adding full width backgrounds requires some extra divs.
function frisco_div_bp_before_member_header() {
	?>
		<div id="bp-before-member-header">
	<?php 
}
add_action ( 'bp_before_member_header', 'frisco_div_bp_before_member_header' );

// close the bp-before-member-header div
function frisco_div_bp_after_member_header() {
	?>
		</div> 
	<?php
}
add_action ( 'bp_after_member_header', 'frisco_div_bp_after_member_header' );


// Adding full width backgrounds requires some extra divs.
function frisco_div_bp_before_group_header() {
	?>
		<div id="bp-before-group-header">
	<?php 
}
add_action ( 'bp_before_group_header', 'frisco_div_bp_before_group_header' );

// close the bp-before-group-header div
function frisco_div_bp_after_group_header() {
	?>
		</div> 
	<?php
}
add_action ( 'bp_after_group_header', 'frisco_div_bp_after_group_header' );

// Add site credits by filtering exising text in footer.php from bp-default.
add_filter('gettext', 'frisco_sitecredits', 20, 3);
/**
 * Edit the default credits to add Frisco link. Remove it if you'd like or modify it to display whatever you want. 
 *
 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
 */
function frisco_sitecredits( $translated_text, $untranslated_text, $domain ) {
    $custom_field_text = 'Proudly powered by <a href="%1$s">WordPress</a> and <a href="%2$s">BuddyPress</a>.';
    if ( $untranslated_text === $custom_field_text ) {
        return 'Proudly powered by <a href="http://wordpress.org">WordPress</a>, <a href="http://buddypress.org">BuddyPress</a> and the <a href="http://friscotheme.com/">Frisco Theme</a>.';
    }
    return $translated_text;
}

?>