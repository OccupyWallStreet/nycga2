<?php
/**
 * Display links to active BuddyPress compatible/specific themes settings' pages
 *
 * @package    BuddyPress Toolbar
 * @subpackage Theme Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright 2012, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://genesisthemes.de/en/wp-plugins/buddypress-toolbar/
 * @link       http://twitter.com/#!/deckerweb
 *
 * @since 1.0
 * @version 1.1
 */

/**
 * Get current stylesheet name logic - compatible up to WordPress 3.4+!
 *
 * @since 1.2
 *
 * @global mixed $stylesheet
 * @param $bptb_stylesheet_name
 */
global $stylesheet;

if ( function_exists( 'wp_get_theme' ) ) {			// First, check for WP 3.4+ function wp_get_theme()
	$bptb_stylesheet_name = wp_get_theme( $stylesheet );
} elseif ( function_exists( 'get_current_theme' ) ) {		// Otherwise fall back to prior WP 3.4 default get_current_theme()
	$bptb_stylesheet_name = get_current_theme();
} // end-if stylesheet check


/**
 * "Theme Settings" String for all Themes/Child Themes
 *
 * @since 1.2
 *
 * @param $bptb_themesettings
 */
$bptb_themesettings = '&nbsp;' . __( 'Theme Settings', 'buddypress-toolbar' );


/**
 * Display link to active Genesis Connect (Plugin) and Genesis Framework (Theme) settings pages (premium, by StudioPress)
 *
 * @since 1.0
 * @version 1.1
 */
if ( ! function_exists( 'ddw_gtbe_admin_bar_menu' ) && ( defined( 'GENESIS_SETTINGS_FIELD' ) || get_template() == 'genesis' ) && class_exists( 'GConnect_Theme' ) && current_user_can( 'edit_theme_options' ) ) {
	/** Genesis Connect Settings */
	$menu_items['gconnect-settings'] = array(
		'parent' => $extensions,
		'title'  => __( 'Genesis Connect Settings', 'buddypress-toolbar' ),
		'href'   => admin_url( 'admin.php?page=connect-settings' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Genesis Connect Settings', 'buddypress-toolbar' ) )
	);
	/** Genesis Theme Settings */
	$menu_items['genesis-settings'] = array(
		'parent' => $tgroup,
		'title'  => 'Genesis' . $bptb_themesettings,
		'href'   => admin_url( 'admin.php?page=genesis' ),
		'meta'   => array( 'target' => '', 'title' => 'Genesis' . $bptb_themesettings )
	);
}  // end-if Genesis & Genesis Connect


/**
 * Display link to active Custom Community theme settings page (free, by Themekraft)
 *
 * @since 1.0
 */
if ( ( defined( 'CC_VERSION' ) || $bptb_stylesheet_name == 'Custom Community' || get_template() == 'custom-community' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['customcommunity-settings'] = array(
		'parent' => $tgroup,
		'title'  => 'Custom Community' . $bptb_themesettings,
		'href'   => admin_url( 'themes.php?page=theme_settings' ),
		'meta'   => array( 'target' => '', 'title' => 'Custom Community' . $bptb_themesettings )
	);
}  // end-if Custom Community


/**
 * Display link to active Frisco for BuddyPress theme settings page (free, by David Carson)
 *
 * @since 1.0
 */
if ( ( $bptb_stylesheet_name == 'Frisco for BuddyPress' && get_template() == 'bp-default' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['frisco-settings'] = array(
		'parent' => $tgroup,
		'title'  => 'Frisco' . $bptb_themesettings,
		'href'   => admin_url( 'themes.php?page=theme_options' ),
		'meta'   => array( 'target' => '', 'title' => 'Frisco' . $bptb_themesettings )
	);
}  // end-if Frisco


/**
 * Display link to active Elbee Elgee theme settings page (free, by Doug Stewart)
 *
 * @since 1.0
 */
if ( ( defined( 'LBLG_FUNCTIONS_DIR' ) || $bptb_stylesheet_name == 'Elbee Elgee' || get_template() == 'elbee-elgee' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['lblg-settings'] = array(
		'parent' => $tgroup,
		'title'  => 'Elbee Elgee' . $bptb_themesettings,
		'href'   => admin_url( 'themes.php?page=lblg_options_page' ),
		'meta'   => array( 'target' => '', 'title' => 'Elbee Elgee' . $bptb_themesettings )
	);
}  // end-if Elbee Elgee


/**
 * Display link to active Gratitude theme settings page (premium, by Chris Paul/ZenThemes)
 *
 * @since 1.1
 */
if ( ( $bptb_stylesheet_name == 'Gratitude' || get_template() == 'gratitude' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['graditude-settings'] = array(
		'parent' => $tgroup,
		'title'  => 'Gratitude' . $bptb_themesettings,
		'href'   => admin_url( 'themes.php?page=zen' ),
		'meta'   => array( 'target' => '', 'title' => 'Gratitude' . $bptb_themesettings )
	);
}  // end-if Gratitude


/**
 * Display link to active Buddies theme settings page (premium, by Chris Paul/ZenThemes)
 *
 * @since 1.2
 */
if ( ( $bptb_stylesheet_name == 'Buddies' || get_template() == 'buddies' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['buddies-settings'] = array(
		'parent' => $tgroup,
		'title'  => 'Buddies' . $bptb_themesettings,
		'href'   => admin_url( 'themes.php?page=zen' ),
		'meta'   => array( 'target' => '', 'title' => 'Buddies' . $bptb_themesettings )
	);
}  // end-if Buddies


/**
 * Display link to active Visual theme settings page (premium, by DevPress)
 *
 * @since 1.2
 */
if ( ( function_exists( 'visual_register_shortcodes' ) || $bptb_stylesheet_name == 'Visual' || get_template() == 'visual' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['visual-settings'] = array(
		'parent' => $tgroup,
		'title'  => 'Visual' . $bptb_themesettings,
		'href'   => admin_url( 'themes.php?page=theme-settings' ),
		'meta'   => array( 'target' => '', 'title' => 'Visual' . $bptb_themesettings )
	);
}  // end-if Visual


/**
 * Display link to active Fanwood theme settings page (free, by DevPress)
 * Requires Fanwood version 0.1.6 or higher!
 *
 * @since 1.2
 */
if ( ( function_exists( 'fanwood_resources' ) || $bptb_stylesheet_name == 'Fanwood' || get_template() == 'fanwood' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['fanwood-settings'] = array(
		'parent' => $tgroup,
		'title'  => 'Fanwood' . $bptb_themesettings,
		'href'   => admin_url( 'themes.php?page=theme-settings' ),
		'meta'   => array( 'target' => '', 'title' => 'Fanwood' . $bptb_themesettings )
	);
}  // end-if Fanwood


/**
 * Display link to active Builder Framework Theme settings page (premium, by iThemes)
 *
 * @since 1.1
 */
if ( ( ( get_template() == 'builder' || get_template() == 'Builder' || $bptb_stylesheet_name == 'Builder' ) && current_user_can( 'switch_themes' ) ) && in_array( 'builder-buddypress/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	$menu_items['builder-settings'] = array(
		'parent' => $tgroup,
		'title'  => 'Builder' . $bptb_themesettings,
		'href'   => admin_url( 'admin.php?page=theme-settings' ),
		'meta'   => array( 'target' => '', 'title' => 'Builder' . $bptb_themesettings )
	);
}  // end-if Builder


/**
 * Display link to active Infinity (Anti-) Framework Theme settings page (premium, by PressCrew)
 *
 * @since 1.1
 */
if ( ( ( defined( 'INFINITY_VERSION' ) || get_template() == 'infinity' || $bptb_stylesheet_name == 'Infinity' ) && in_array( 'bp-template-pack/loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) && current_user_can( 'manage_options' ) ) {
	$menu_items['infinity-settings'] = array(
		'parent' => $tgroup,
		'title'  => 'Infinity' . $bptb_themesettings,
		'href'   => admin_url( 'admin.php?page=infinity-theme' ),
		'meta'   => array( 'target' => '', 'title' => 'Infinity' . $bptb_themesettings )
	);
}  // end-if Infinity


/**
 * Display link to active Business Services Theme settings pages (premium, by Tammie Lister - WPMU DEV)
 *
 * @since 1.1
 */
if ( ( get_template() == 'business-services' || $bptb_stylesheet_name == 'Business Services' ) && current_user_can( 'edit_theme_options' ) ) {
	/** Theme Settings */
	$menu_items['bservicessettings'] = array(
		'parent' => $tgroup,
		'title'  => 'Business Services' . $bptb_themesettings,
		'href'   => admin_url( 'themes.php?page=functions.php' ),
		'meta'   => array( 'target' => '', 'title' => 'Business Services' . $bptb_themesettings )
	);
	/** Custom Styling Settings */
	$menu_items['bservicessettings-styling'] = array(
		'parent' => $bservicessettings,
		'title'  => __( 'Custom Styling', 'buddypress-toolbar' ),
		'href'   => admin_url( 'themes.php?page=styling-functions.php' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Custom Styling', 'buddypress-toolbar' ) )
	);
}  // end-if Business Services


/**
 * Display link to active BuddyPress Corporate Theme settings pages (premium, by Richie KS - WPMU DEV)
 *
 * @since 1.1
 */
if ( ( get_template() == 'buddypress-corporate' || $bptb_stylesheet_name == 'BuddyPress Corporate' ) && current_user_can( 'edit_theme_options' ) ) {
	$menu_items['bpcorporate-settings'] = array(
		'parent' => $tgroup,
		'title'  => 'BP Corporate' . $bptb_themesettings,
		'href'   => admin_url( 'themes.php?page=options-functions.php' ),
		'meta'   => array( 'target' => '', 'title' => 'BuddyPress Corporate' . $bptb_themesettings )
	);
}  // end-if BP Corporate


/**
 * Display link to active theme settings pages by 3onseven.com (all free, by milo317)
 *
 * @since 1.2
 */
	/**
	 * 3colours Theme
	 *
	 * @since 1.2
	 */
	if ( ( $bptb_stylesheet_name == '3colours' || get_template() == '3colours' ) && current_user_can( 'edit_themes' ) ) {
		$menu_items['threeonesevensettings'] = array(
			'parent' => $tgroup,
			'title'  => '3colours' . $bptb_themesettings,
			'href'   => admin_url( 'themes.php?page=panel.php' ),
			'meta'   => array( 'target' => '', 'title' => '3colours' . $bptb_themesettings )
		);
		$menu_items['threeonesevensettings-more'] = array(
			'parent' => $threeonesevensettings,
			'title'  => __( 'More Settings', 'buddypress-toolbar' ),
			'href'   => admin_url( 'themes.php?page=controlpanel.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'More Settings', 'buddypress-toolbar' ) )
		);
	}  // end-if 3colours

	/**
	 * Niukita Theme
	 *
	 * @since 1.2
	 */
	if ( ( $bptb_stylesheet_name == 'Niukita' || get_template() == 'Niukita' ) && current_user_can( 'edit_themes' ) ) {
		$menu_items['threeonesevensettings'] = array(
			'parent' => $tgroup,
			'title'  => 'Niukita' . $bptb_themesettings,
			'href'   => admin_url( 'themes.php?page=panel.php' ),
			'meta'   => array( 'target' => '', 'title' => 'Niukita' . $bptb_themesettings )
		);
		$menu_items['threeonesevensettings-more'] = array(
			'parent' => $threeonesevensettings,
			'title'  => __( 'More Settings', 'buddypress-toolbar' ),
			'href'   => admin_url( 'themes.php?page=controlpanel.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'More Settings', 'buddypress-toolbar' ) )
		);
	}  // end-if Niukita

	/**
	 * Ice Theme
	 *
	 * @since 1.2
	 */
	if ( ( $bptb_stylesheet_name == 'Ice' || get_template() == 'Ice' ) && current_user_can( 'edit_themes' ) ) {
		$menu_items['threeonesevensettings'] = array(
			'parent' => $tgroup,
			'title'  => 'Ice' . $bptb_themesettings,
			'href'   => admin_url( 'themes.php?page=control.php' ),
			'meta'   => array( 'target' => '', 'title' => 'Ice' . $bptb_themesettings )
		);
	}  // end-if Ice

	/**
	 * Bruce Theme
	 *
	 * @since 1.2
	 */
	if ( ( $bptb_stylesheet_name == 'Bruce' || get_template() == 'Bruce' ) && current_user_can( 'edit_themes' ) ) {
		$menu_items['threeonesevensettings'] = array(
			'parent' => $tgroup,
			'title'  => 'Bruce' . $bptb_themesettings,
			'href'   => admin_url( 'themes.php?page=controlpanel.php' ),
			'meta'   => array( 'target' => '', 'title' => 'Bruce' . $bptb_themesettings )
		);
	}  // end-if Bruce

	/**
	 * Detox Theme
	 *
	 * @since 1.2
	 */
	if ( ( $bptb_stylesheet_name == 'Detox' || get_template() == 'Detox' ) && current_user_can( 'edit_themes' ) ) {
		$menu_items['threeonesevensettings'] = array(
			'parent' => $tgroup,
			'title'  => 'Detox' . $bptb_themesettings,
			'href'   => admin_url( 'themes.php?page=panel.php' ),
			'meta'   => array( 'target' => '', 'title' => 'Detox' . $bptb_themesettings )
		);
	}  // end-if Detox

	/**
	 * Ines Theme
	 *
	 * @since 1.2
	 */
	if ( ( $bptb_stylesheet_name == 'Ines' || get_template() == 'Ines' ) && current_user_can( 'edit_themes' ) ) {
		$menu_items['threeonesevensettings'] = array(
			'parent' => $tgroup,
			'title'  => 'Ines' . $bptb_themesettings,
			'href'   => admin_url( 'themes.php?page=control.php' ),
			'meta'   => array( 'target' => '', 'title' => 'Ines' . $bptb_themesettings )
		);
	}  // end-if Ines

	/**
	 * Soho Theme
	 *
	 * @since 1.2
	 */
	if ( ( $bptb_stylesheet_name == 'Soho' || get_template() == 'Soho' ) && current_user_can( 'edit_themes' ) ) {
		$menu_items['threeonesevensettings'] = array(
			'parent' => $tgroup,
			'title'  => 'Soho' . $bptb_themesettings,
			'href'   => admin_url( 'themes.php?page=panel.php' ),
			'meta'   => array( 'target' => '', 'title' => 'Soho' . $bptb_themesettings )
		);
	}  // end-if Soho

	/**
	 * Solitude Theme
	 *
	 * @since 1.2
	 */
	if ( ( $bptb_stylesheet_name == 'Solitude' || get_template() == 'Solitude' ) && current_user_can( 'edit_themes' ) ) {
		$menu_items['threeonesevensettings'] = array(
			'parent' => $tgroup,
			'title'  => 'Solitude' . $bptb_themesettings,
			'href'   => admin_url( 'themes.php?page=control.php' ),
			'meta'   => array( 'target' => '', 'title' => 'Solitude' . $bptb_themesettings )
		);
	}  // end-if Solitude

	/**
	 * Speed Theme
	 *
	 * @since 1.2
	 */
	if ( ( $bptb_stylesheet_name == 'Speed' || get_template() == 'Speed' ) && current_user_can( 'edit_themes' ) ) {
		$menu_items['threeonesevensettings'] = array(
			'parent' => $tgroup,
			'title'  => 'Speed' . $bptb_themesettings,
			'href'   => admin_url( 'themes.php?page=control.php' ),
			'meta'   => array( 'target' => '', 'title' => 'Speed' . $bptb_themesettings )
		);
	}  // end-if Speed

	/**
	 * Spyker Theme
	 *
	 * @since 1.2
	 */
	if ( ( $bptb_stylesheet_name == 'Spyker' || get_template() == 'Spyker' ) && current_user_can( 'edit_themes' ) ) {
		$menu_items['threeonesevensettings'] = array(
			'parent' => $tgroup,
			'title'  => 'Spyker' . $bptb_themesettings,
			'href'   => admin_url( 'themes.php?page=adcontrol.php' ),
			'meta'   => array( 'target' => '', 'title' => 'Spyker' . $bptb_themesettings )
		);
	}  // end-if Spyker

/** end of: 3oneseven.com themes */


/**
 * Last entry of "Theme Group" menu entry
 *
 * @since 1.1
 */

	/**
	 * BP Template Pack (free, by apeatling, boonebgorges, r-a-y)
	 *
	 * @since 1.1
	 */
	if ( ( in_array( 'bp-template-pack/loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) && current_user_can( 'switch_themes' ) ) {
		$menu_items['ext-bptemplatepack'] = array(
			'parent' => $tgroup,
			'title'  => __( 'BuddyPress Theme Compatibility', 'buddypress-toolbar' ),
			'href'   => admin_url( 'themes.php?page=bp-tpack-options' ),
			'meta'   => array( 'target' => '', 'title' => __( 'BuddyPress Theme Compatibility', 'buddypress-toolbar' ) )
		);
	}  // end-if BP Template Pack

/** end of: last entry of theme group */
