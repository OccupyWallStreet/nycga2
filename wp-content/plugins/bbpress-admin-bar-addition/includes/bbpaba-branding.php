<?php
/**
 * Helper functions for custom branding & capabilities
 *
 * @package    bbPress Admin Bar Addition
 * @subpackage Branding
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright 2012, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://genesisthemes.de/en/wp-plugins/bbpress-admin-bar-addition/
 * @link       http://twitter.com/#!/deckerweb
 *
 * @since 1.5
 */

/**
 * Helper functions for returning a few popular roles/capabilities.
 *
 * @since 1.5
 *
 * @return role/capability
 */
	/**
	 * Helper function for returning 'administrator' role/capability.
	 *
	 * @since 1.5
	 *
	 * @return 'administrator' role
	 */
	function __bbpaba_admin_only() {

		return 'administrator';
	}

	/**
	 * Helper function for returning 'editor' role/capability.
	 *
	 * @since 1.5
	 *
	 * @return 'editor' role
	 */
	function __bbpaba_role_editor() {

		return 'editor';
	}

	/**
	 * Helper function for returning bbPress 2.x specific 'bbp_moderator' role/capability.
	 *
	 * @since 1.5
	 *
	 * @return 'bbp_moderator' role
	 */
	function __bbpaba_role_bbp_moderator() {

		return 'bbp_moderator';
	}

	/**
	 * Helper function for returning bbPress 2.x specific 'moderate' capability.
	 *
	 * @since 1.5
	 *
	 * @return 'moderate' capability
	 */
	function __bbpaba_cap_moderate() {

		return 'moderate';
	}

	/**
	 * Helper function for returning 'manage_options' capability.
	 *
	 * @since 1.5
	 *
	 * @return 'manage_options' capability
	 */
	function __bbpaba_cap_manage_options() {

		return 'manage_options';
	}

	/**
	 * Helper function for returning 'install_plugins' capability.
	 *
	 * @since 1.5
	 *
	 * @return 'install_plugins' capability
	 */
	function __bbpaba_cap_install_plugins() {

		return 'install_plugins';
	}

	/**
	 * Helper function for returning 'edit_theme_options' capability.
	 *
	 * @since 1.5
	 *
	 * @return 'edit_theme_options' capability
	 */
	function __bbpaba_cap_edit_theme_options() {

		return 'edit_theme_options';
	}

/** End of role/capability helper functions */


/**
 * Helper functions for returning custom icons.
 *
 * @since 1.5
 *
 * @return string URL for custom icon image
 */
	/**
	 * Helper function for returning the blue icon.
	 *
	 * @since 1.5
	 *
	 * @return blue icon
	 */
	function __bbpaba_blue_icon() {

		return plugins_url( 'images/bbpaba-icon-blue.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the brown icon.
	 *
	 * @since 1.5
	 *
	 * @return brown icon
	 */
	function __bbpaba_brown_icon() {

		return plugins_url( 'images/bbpaba-icon-brown.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the gray icon.
	 *
	 * @since 1.5
	 *
	 * @return gray icon
	 */
	function __bbpaba_gray_icon() {

		return plugins_url( 'images/bbpaba-icon-gray.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the green icon.
	 *
	 * @since 1.5
	 *
	 * @return green icon
	 */
	function __bbpaba_green_icon() {

		return plugins_url( 'images/bbpaba-icon-green.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the khaki icon.
	 *
	 * @since 1.5
	 *
	 * @return khaki icon
	 */
	function __bbpaba_khaki_icon() {

		return plugins_url( 'images/bbpaba-icon-khaki.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the orange icon.
	 *
	 * @since 1.5
	 *
	 * @return orange icon
	 */
	function __bbpaba_orange_icon() {

		return plugins_url( 'images/bbpaba-icon-orange.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the pink icon.
	 *
	 * @since 1.5
	 *
	 * @return pink icon
	 */
	function __bbpaba_pink_icon() {

		return plugins_url( 'images/bbpaba-icon-pink.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the red icon.
	 *
	 * @since 1.5
	 *
	 * @return red icon
	 */
	function __bbpaba_red_icon() {

		return plugins_url( 'images/bbpaba-icon-red.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the turquoise icon.
	 *
	 * @since 1.5
	 *
	 * @return turquoise icon
	 */
	function __bbpaba_turquoise_icon() {

		return plugins_url( 'images/bbpaba-icon-turquoise.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the alternate (forums menu) icon.
	 *
	 * @since 1.5
	 *
	 * @return alternate icon
	 */
	function __bbpaba_alternate_icon() {

		return plugins_url( 'images/icon-bbpaba-alternate.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning a custom icon (icon-bbpaba.png) from stylesheet/child/theme "images" folder.
	 *
	 * @since 1.5
	 *
	 * @return bbpaba custom icon
	 */
	function __bbpaba_theme_images_icon() {

		return get_stylesheet_directory_uri() . '/images/icon-bbpaba.png';
	}

/** End of icon helper functions */


/**
 * Helper functions for returning icon class.
 *
 * @since 1.5
 *
 * @return icon class
 */
	/**
	 * Helper function for returning no icon class.
	 *
	 * @since 1.5
	 *
	 * @return no icon class
	 */
	function __bbpaba_no_icon_display() {

		return NULL;
	}

/** End of icon class helper functions */


/**
 * Misc. helper functions
 *
 * @since 1.5
 */
	add_action( 'wp_before_admin_bar_render', 'ddw_bbpaba_remove_gdbbpresstools_toolbar', 5 );
	/**
	 * Disable original toolbar items of "GD bbPress Tools"
	 *
	 * @since 1.5
	 */
	function ddw_bbpaba_remove_gdbbpresstools_toolbar() {

		if ( BBPABA_REMOVE_GDBBPRESSTOOLS_TOOLBAR ) {

			global $wp_admin_bar;

			$wp_admin_bar->remove_menu( 'gdbb-toolbar' );
		}

	}  // end of function

/** End of misc. helper functions */
