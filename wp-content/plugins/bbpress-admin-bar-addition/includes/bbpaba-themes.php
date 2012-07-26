<?php
/**
 * Display links to active bbPress 2.x compatible/specific themes settings' pages
 *
 * @package    bbPress Admin Bar Addition
 * @subpackage Theme Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright 2012, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://genesisthemes.de/en/wp-plugins/bbpress-admin-bar-addition/
 * @link       http://twitter.com/#!/deckerweb
 *
 * @since 1.5
 */

/**
 * Get current stylesheet name logic - compatible up to WordPress 3.4+!
 *
 * @since 1.5
 *
 * @global mixed $stylesheet
 * @param $bbpaba_stylesheet_name
 */
global $stylesheet;

if ( function_exists( 'wp_get_theme' ) ) {			// First, check for WP 3.4+ function wp_get_theme()
	$bbpaba_stylesheet_name = wp_get_theme( $stylesheet );
} elseif ( function_exists( 'get_current_theme' ) ) {		// Otherwise fall back to prior WP 3.4 default get_current_theme()
	$bbpaba_stylesheet_name = get_current_theme();
} // end-if stylesheet check


/**
 * "Theme Settings" String for all Themes/Child Themes
 *
 * @since 1.5
 *
 * @param $bbpaba_themesettings
 */
$bbpaba_themesettings = '&nbsp;' . __( 'Theme Settings', 'bbpaba' );


/**
 * Display link to active Genesis Framework theme settings page (premium, by StudioPress)
 *
 * @since 1.5
 */
if ( ! function_exists( 'ddw_gtbe_admin_bar_menu' ) && class_exists( 'BBP_Genesis' ) && defined( 'GENESIS_SETTINGS_FIELD' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['genesis-settings'] = array(
		'parent' => $bbpressbar,
		'title'  => 'Genesis' . $bbpaba_themesettings,
		'href'   => admin_url( 'admin.php?page=genesis' ),
		'meta'   => array( 'target' => '', 'title' => 'Genesis' . $bbpaba_themesettings )
	);
}  // end-if Genesis


/**
 * Display link to active Skeleton theme settings page (free, by Simple Themes)
 *
 * @since 1.5
 */
if ( ( $bbpaba_stylesheet_name == 'Skeleton' || get_template() == 'skeleton' ) && defined( 'OPTIONS_FRAMEWORK_VERSION' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['skeleton-settings'] = array(
		'parent' => $bbpressbar,
		'title'  => 'Skeleton' . $bbpaba_themesettings,
		'href'   => admin_url( 'themes.php?page=options-framework' ),
		'meta'   => array( 'target' => '', 'title' => 'Skeleton' . $bbpaba_themesettings )
	);
}  // end-if Skeleton


/**
 * Display link to active Fanwood theme settings page (free, by DevPress)
 * Requires Fanwood version 0.1.6 or higher!
 *
 * @since 1.5
 */
if ( ( function_exists( 'fanwood_resources' ) || $bbpaba_stylesheet_name == 'Fanwood' || get_template() == 'fanwood' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['fanwood-settings'] = array(
		'parent' => $bbpressbar,
		'title'  => 'Fanwood' . $bbpaba_themesettings,
		'href'   => admin_url( 'themes.php?page=theme-settings' ),
		'meta'   => array( 'target' => '', 'title' => 'Fanwood' . $bbpaba_themesettings )
	);
}  // end-if Fanwood


/**
 * Display link to active Elbee Elgee theme settings page (free, by Doug Stewart)
 *
 * @since 1.5
 */
if ( ( defined( 'LBLG_FUNCTIONS_DIR' ) || $bbpaba_stylesheet_name == 'Elbee Elgee' || get_template() == 'elbee-elgee' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['lblg-settings'] = array(
		'parent' => $bbpressbar,
		'title'  => 'Elbee Elgee' . $bbpaba_themesettings,
		'href'   => admin_url( 'themes.php?page=lblg_options_page' ),
		'meta'   => array( 'target' => '', 'title' => 'Elbee Elgee' . $bbpaba_themesettings )
	);
}  // end-if Elbee Elgee


/**
 * Display link to active Gratitude theme settings page (premium, by Chris Paul/ZenThemes)
 *
 * @since 1.5
 */
if ( ( $bbpaba_stylesheet_name == 'Gratitude' || get_template() == 'gratitude' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['graditude-settings'] = array(
		'parent' => $bbpressbar,
		'title'  => 'Gratitude' . $bbpaba_themesettings,
		'href'   => admin_url( 'themes.php?page=zen' ),
		'meta'   => array( 'target' => '', 'title' => 'Gratitude' . $bbpaba_themesettings )
	);
}  // end-if Gratitude


/**
 * Display link to active Buddies theme settings page (premium, by Chris Paul/ZenThemes)
 *
 * @since 1.5
 */
if ( ( $bbpaba_stylesheet_name == 'Buddies' || get_template() == 'buddies' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['buddies-settings'] = array(
		'parent' => $bbpressbar,
		'title'  => 'Buddies' . $bbpaba_themesettings,
		'href'   => admin_url( 'themes.php?page=zen' ),
		'meta'   => array( 'target' => '', 'title' => 'Buddies' . $bbpaba_themesettings )
	);
}  // end-if Buddies


/**
 * Display link to active WP Sharp theme settings page (premium, by PrimaThemes at ThemeForest)
 *
 * @since 1.5
 */
if ( ( $bbpaba_stylesheet_name == 'WP Sharp' || get_template() == 'wpsharp' ) && ( current_user_can( 'edit_theme_options' ) || current_user_can( 'manage_options' ) ) ) {
	$menu_items['wpsharp-settings'] = array(
		'parent' => $bbpressbar,
		'title'  => 'WP Sharp' . $bbpaba_themesettings,
		'href'   => admin_url( 'themes.php?page=primathemes' ),
		'meta'   => array( 'target' => '', 'title' => 'WP Sharp' . $bbpaba_themesettings )
	);
}  // end-if WP Sharp


/**
 * Display link to active Infinity (Anti-) Framework Theme settings page (premium, by PressCrew)
 *
 * @since 1.5
 */
if ( ( defined( 'INFINITY_VERSION' ) || get_template() == 'infinity' || $bbpaba_stylesheet_name == 'Infinity' ) && current_user_can( 'manage_options' ) ) {
	$menu_items['infinity-settings'] = array(
		'parent' => $bbpressbar,
		'title'  => 'Infinity' . $bbpaba_themesettings,
		'href'   => admin_url( 'admin.php?page=infinity-theme' ),
		'meta'   => array( 'target' => '', 'title' => 'Infinity' . $bbpaba_themesettings )
	);
}  // end-if Infinity
