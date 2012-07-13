<?php
/**
 * Helper functions for custom branding & capabilities
 *
 * @package    BuddyPress Toolbar
 * @subpackage Branding
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright 2012, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://genesisthemes.de/en/wp-plugins/buddypress-toolbar/
 * @link       http://twitter.com/#!/deckerweb
 *
 * @since 1.2
 */

/**
 * Helper functions for returning a few popular roles/capabilities.
 *
 * @since 1.2
 *
 * @return role/capability
 */
	/**
	 * Helper function for returning 'editor' role/capability.
	 *
	 * @since 1.2
	 *
	 * @return 'editor' role
	 */
	function __bptb_role_editor() {

		return 'editor';
	}

	/**
	 * Helper function for returning 'edit_theme_options' capability.
	 *
	 * @since 1.2
	 *
	 * @return 'edit_theme_options' capability
	 */
	function __bptb_cap_edit_theme_options() {

		return 'edit_theme_options';
	}

	/**
	 * Helper function for returning 'manage_options' capability.
	 *
	 * @since 1.2
	 *
	 * @return 'manage_options' capability
	 */
	function __bptb_cap_manage_options() {

		return 'manage_options';
	}

	/**
	 * Helper function for returning 'install_plugins' capability.
	 *
	 * @since 1.2
	 *
	 * @return 'install_plugins' capability
	 */
	function __bptb_cap_install_plugins() {

		return 'install_plugins';
	}

/** End of role/capability helper functions */


/**
 * Helper functions for returning colored icons.
 *
 * @since 1.2
 *
 * @return colored icon image
 */
	/**
	 * Helper function for returning the blue icon.
	 *
	 * @since 1.2
	 *
	 * @return blue icon
	 */
	function __bptb_blue_icon() {

		return plugins_url( 'images/icon-buddypress-blue.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the brown icon.
	 *
	 * @since 1.2
	 *
	 * @return brown icon
	 */
	function __bptb_brown_icon() {

		return plugins_url( 'images/icon-buddypress-brown.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the gray icon.
	 *
	 * @since 1.2
	 *
	 * @return gray icon
	 */
	function __bptb_gray_icon() {

		return plugins_url( 'images/icon-buddypress-gray.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the green icon.
	 *
	 * @since 1.2
	 *
	 * @return green icon
	 */
	function __bptb_green_icon() {

		return plugins_url( 'images/icon-buddypress-green.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the khaki icon.
	 *
	 * @since 1.2
	 *
	 * @return khaki icon
	 */
	function __bptb_khaki_icon() {

		return plugins_url( 'images/icon-buddypress-khaki.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the orange icon.
	 *
	 * @since 1.2
	 *
	 * @return orange icon
	 */
	function __bptb_orange_icon() {

		return plugins_url( 'images/icon-buddypress-orange.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the pink icon.
	 *
	 * @since 1.2
	 *
	 * @return pink icon
	 */
	function __bptb_pink_icon() {

		return plugins_url( 'images/icon-buddypress-pink.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the red icon.
	 *
	 * @since 1.2
	 *
	 * @return red icon
	 */
	function __bptb_red_icon() {

		return plugins_url( 'images/icon-buddypress-red.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the turquoise icon.
	 *
	 * @since 1.2
	 *
	 * @return turquoise icon
	 */
	function __bptb_turquoise_icon() {

		return plugins_url( 'images/icon-buddypress-turquoise.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the yellow icon.
	 *
	 * @since 1.2
	 *
	 * @return yellow icon
	 */
	function __bptb_yellow_icon() {

		return plugins_url( 'images/icon-buddypress-yellow.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the yellow2 icon.
	 *
	 * @since 1.2
	 *
	 * @return yellowtwo icon
	 */
	function __bptb_yellowtwo_icon() {

		return plugins_url( 'images/icon-buddypress-yellow2.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning the bp default icon.
	 *
	 * @since 1.2
	 *
	 * @return bp default icon
	 */
	function __bptb_default_icon() {

		return plugins_url( 'images/icon-buddypress.png', dirname( __FILE__ ) );
	}

	/**
	 * Helper function for returning a custom icon (icon-bptb.png) from stylesheet/theme "images" folder.
	 *
	 * @since 1.2
	 *
	 * @return bptb custom icon
	 */
	function __bptb_theme_images_icon() {

		return get_stylesheet_directory_uri() . '/images/icon-bptb.png';
	}

/** End of icon helper functions */


/**
 * Helper functions for returning icon class.
 *
 * @since 1.2
 *
 * @return icon class
 */
	/**
	 * Helper function for returning no icon class.
	 *
	 * @since 1.2
	 *
	 * @return int 0
	 */
	function __bptb_no_icon_display() {

		return NULL;
	}

/** End of icon class helper functions */
