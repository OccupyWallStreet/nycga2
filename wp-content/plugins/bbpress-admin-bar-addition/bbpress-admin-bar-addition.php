<?php 
/**
 * Main plugin file.
 * This plugin adds useful admin links and resources for the bbPress 2.x Forum Plugin to the WordPress Toolbar / Admin Bar.
 *
 * @package   bbPress Admin Bar Addition
 * @author    David Decker
 * @link      http://twitter.com/#!/deckerweb
 * @copyright Copyright 2011-2012, David Decker - DECKERWEB
 *
 * @credits Inspired and based on the plugin "WooThemes Admin Bar Addition" by Remkus de Vries @defries.
 * @link    http://remkusdevries.com/
 * @link    http://twitter.com/#!/defries
 *
 * Plugin Name: bbPress Admin Bar Addition
 * Plugin URI: http://genesisthemes.de/en/wp-plugins/bbpress-admin-bar-addition/
 * Description: This plugin adds useful admin links and resources for the bbPress 2.x Forum Plugin to the WordPress Toolbar / Admin Bar.
 * Version: 1.6
 * Author: David Decker - DECKERWEB
 * Author URI: http://deckerweb.de/
 * License: GPLv2 or later
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: bbpaba
 * Domain Path: /languages/
 *
 * Copyright 2011-2012 David Decker - DECKERWEB
 *
 *     This file is part of bbPress Admin Bar Addition,
 *     a plugin for WordPress.
 *
 *     bbPress Admin Bar Addition is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 2 of the License, or (at your option)
 *     any later version.
 *
 *     bbPress Admin Bar Addition is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Setting constants
 *
 * @since 1.0
 * @version 1.1
 */
/** Plugin directory */
define( 'BBPABA_PLUGIN_DIR', dirname( __FILE__ ) );

/** Plugin base directory */
define( 'BBPABA_PLUGIN_BASEDIR', dirname( plugin_basename( __FILE__ ) ) );

/** Various link/content related helper constants */
define( 'BBPABA_VTUTORIALS_INSTALL', apply_filters( 'bbpaba_filter_video_installation', 'http://www.youtube.com/watch?v=EDAp8M7CuPk&hd=1' ) );
define( 'BBPABA_VTUTORIALS_THEMING', apply_filters( 'bbpaba_filter_video_theming', 'http://www.youtube.com/watch?v=lB2Oodx2GJw&hd=1' ) );
define( 'BBPABA_TUTORIALS_STARTING', apply_filters( 'bbpaba_filter_tutorial_starting', 'http://wp.smashingmagazine.com/2011/11/15/getting-started-with-bbpress/' ) );


add_action( 'init', 'ddw_bbpress_aba_init' );
/**
 * Load the text domain for translation of the plugin.
 * Load admin helper functions - only within 'wp-admin'.
 * 
 * @since 1.0
 * @version 1.2
 */
function ddw_bbpress_aba_init() {

	/** First look in WordPress' "languages" folder = custom & update-secure! */
	load_plugin_textdomain( 'bbpaba', false, BBPABA_PLUGIN_BASEDIR . '/../../languages/bbpress-admin-bar-addition/' );

	/** Then look in plugin's "languages" folder = default */
	load_plugin_textdomain( 'bbpaba', false, BBPABA_PLUGIN_BASEDIR . '/languages/' );

	/** If 'wp-admin' include admin helper functions */
	if ( is_admin() ) {
		require_once( BBPABA_PLUGIN_DIR . '/includes/bbpaba-admin.php' );
	}

	/** Define constants and set defaults for removing all or certain sections */
	if ( ! defined( 'BBPABA_DISPLAY' ) ) {
		define( 'BBPABA_DISPLAY', TRUE );
	}

	if ( ! defined( 'BBPABA_EXTENSIONS_DISPLAY' ) ) {
		define( 'BBPABA_EXTENSIONS_DISPLAY', TRUE );
	}

	if ( ! defined( 'BBPABA_THEMES_DISPLAY' ) ) {
		define( 'BBPABA_THEMES_DISPLAY', TRUE );
	}

	if ( ! defined( 'BBPABA_RESOURCES_DISPLAY' ) ) {
		define( 'BBPABA_RESOURCES_DISPLAY', TRUE );
	}

	if ( ! defined( 'BBPABA_DE_DISPLAY' ) ) {
		define( 'BBPABA_DE_DISPLAY', TRUE );
	}

	if ( ! defined( 'BBPABA_REMOVE_GDBBPRESSTOOLS_TOOLBAR' ) ) {
		define( 'BBPABA_REMOVE_GDBBPRESSTOOLS_TOOLBAR', FALSE );
	}

}  // end of function ddw_bbpress_aba_init


add_action( 'admin_bar_menu', 'ddw_bbpress_aba_admin_bar_menu', 98 );
/**
 * Add new menu items to the WordPress Toolbar / Admin Bar.
 * 
 * @since 1.0
 * @version 1.3
 *
 * @global mixed $wp_admin_bar 
 */
function ddw_bbpress_aba_admin_bar_menu() {

	global $wp_admin_bar;

	/**
	 * Allows for filtering the general user role/capability to see main & sub-level items
	 *
	 * Default role: 'moderate' (set by bbPress 2.x itself!)
	 *
	 * @since 1.5
	 */
	$bbpaba_filter_capability = apply_filters( 'bbpaba_filter_capability_all', 'moderate' );

	/**
	 * Required bbPress/ WordPress cabability to display new admin bar entry
	 * Only showing items if toolbar / admin bar is activated and user is logged in!
	 *
	 * @since 1.0
	 * @version 1.1
	 */
	if ( ! is_user_logged_in() || 
		! is_admin_bar_showing() || 
		! current_user_can( $bbpaba_filter_capability ) ||  // allows for custom filtering the required role/capability
		! BBPABA_DISPLAY  // allows for custom disabling
	)
		return;

	/** Set unique prefix */
	$prefix = 'ddw-bbpress-';
	
	/** Create parent menu item references/prefixes */
	$bbpressbar = $prefix . 'admin-bar';			// root level
		$bbpsupport = $prefix . 'bbpsupport';			// sub level: bbp support
		$bbpknowledgebase = $prefix . 'bbpknowledgebase';	// sub level: bbp knowledgebase
		$bbpsites = $prefix . 'bbpsites';			// sub level: bbp sites
		$bbpsettings = $prefix . 'bbpsettings';			// sub level: bbp settings
		$forums = $prefix . 'forums';				// sub level: forums
			$forumsfrontbase = $prefix . 'forumsfrontbase';		// third level: forums frontend view
			$f_gdtools = $prefix . 'f_gdtools';			// third level plugin extension: gd bbpress tools
			$f_gdtoolbox = $prefix . 'f_gdtoolbox';			// third level plugin extension: gd bbpress toolbox
		$topics = $prefix . 'topics';				// sub level: topics
		$replies = $prefix . 'replies';				// sub level: replies
		$users = $prefix . 'users';				// sub level: users
			$userswangguard = $prefix . 'userswangguard';		// third level plugin extension: wangguard
		$extensions = $prefix . 'extensions';			// sub level: support
			$extgdtools = $prefix . 'extgdtools';			// third level plugin extension: gd bbpress tools
			$extgdtoolbox = $prefix . 'extgdtoolbox';		// third level plugin extension: gd bbpress toolbox
			$extwangguard = $prefix . 'extwangguard';		// third level plugin extension: wangguard
			$extcptprivacy = $prefix . 'extcptprivacy';		// third level plugin extension: custom post type privacy
			$extbbpconvert = $prefix . 'extbbpconvert';		// third level plugin extension: bbconverter
			$exts2member = $prefix . 'exts2member';			// third level plugin extension: s2member
		$bbpgroup = $prefix . 'bbpgroup';			// sub level: bbp group (resources)


	/** Make the "bbPress" name filterable within menu items */
	$bbpaba_bbpress_name = apply_filters( 'bbpaba_filter_bbpress_name', __( 'bbPress', 'bbpaba' ) );

	/** Make the "bbPress" name's tooltip filterable within menu items */
	$bbpaba_bbpress_name_tooltip = apply_filters( 'bbpaba_filter_bbpress_name_tooltip', _x( 'bbPress', 'Translators: For the tooltip', 'bbpaba' ) );


	/**
	 * Check for WordPress version to add parent ids for resource links group
	 * Check against WP 3.3+ only function "wp_editor" - if true use "$bbpgroup" as parent (WP 3.3+ style)
	 * otherwise use "$bbpressbar" as parent (WP 3.1/3.2 style)
	 *
	 * @since 1.4
	 *
	 * @param $bbpgroup_check_item
	 */
	if ( function_exists( 'wp_editor' ) ) {
		$bbpgroup_check_item = $bbpgroup;
	} else {
		$bbpgroup_check_item = $bbpressbar;
	}


	/** Display these items also when bbPress plugin is not installed */
	if ( BBPABA_RESOURCES_DISPLAY ) {

		$menu_items = array(

			/** Support menu items */
			'bbpsupport' => array(
				'parent' => $bbpgroup_check_item,
				'title'  => __( 'bbPress Support Forum', 'bbpaba' ),
				'href'   => 'http://bbpress.org/forums/',
				'meta'   => array( 'title' => __( 'bbPress Support Forum', 'bbpaba' ) )
			),
			'bbpsupporttag' => array(
				'parent' => $bbpsupport,
				'title'  => __( 'Topic Tag: bbpress-plugin', 'bbpaba' ),
				'href'   => 'http://bbpress.org/forums/tags/bbpress-plugin',
				'meta'   => array( 'title' => _x( 'Topic Tag: bbpress-plugin', 'Translators: For the tooltip', 'bbpaba' ) )
			),
			'bbpsupportwporg' => array(
				'parent' => $bbpsupport,
				'title'  => __( 'Free Support Forum (WP.org)', 'bbpaba' ),
				'href'   => 'http://wordpress.org/tags/bbpress?forum_id=10',
				'meta'   => array( 'title' => __( 'Free Support Forum (WP.org)', 'bbpaba' ) )
			),

			/** Knowledge Base menu items */
			'bbpknowledgebase' => array(
				'parent' => $bbpgroup_check_item,
				'title'  => __( 'bbPress Docs & FAQ', 'bbpaba' ),
				'href'   => 'http://bbpress.org/forums/topic/bbpress-20-faq',
				'meta'   => array( 'title' => _x( 'bbPress Docs & FAQ', 'Translators: For the tooltip', 'bbpaba' ) )
			),
			'bbpshortcodes' => array(
				'parent' => $bbpknowledgebase,
				'title'  => __( 'bbPress Shortcodes', 'bbpaba' ),
				'href'   => 'http://bbpress.org/forums/topic/bbpress-20-shortcodes',
				'meta'   => array( 'title' => __( 'bbPress Shortcodes', 'bbpaba' ) )
			),
			'bbphooks' => array(
				'parent' => $bbpknowledgebase,
				'title'  => __( 'bbPress 2.0 Hooks &amp; Filters', 'bbpaba' ),
				'href'   => 'http://etivite.com/api-hooks/#bbpress',
				'meta'	 => array( 'title' => _x( 'bbPress 2.0 Hooks, Filters &amp; Components (Dev Docs)', 'Translators: For the tooltip', 'bbpaba' ) )
			),
			'bbpvideo-install' => array(
				'parent' => $bbpknowledgebase,
				'title'  => __( 'Video: bbPress Installation', 'bbpaba' ),
				'href'   => esc_url( BBPABA_VTUTORIALS_INSTALL ),
				'meta'	 => array( 'title' => _x( 'Video: bbPress Installation', 'Translators: For the tooltip', 'bbpaba' ) )
			),
			'bbpvideo-theming' => array(
				'parent' => $bbpknowledgebase,
				'title'  => __( 'Video: bbPress Theming', 'bbpaba' ),
				'href'   => esc_url( BBPABA_VTUTORIALS_INSTALL ),
				'meta'	 => array( 'title' => _x( 'Video: bbPress Theming', 'Translators: For the tooltip', 'bbpaba' ) )
			),
			'bbpgetstarted' => array(
				'parent' => $bbpknowledgebase,
				'title'  => __( 'Getting Started with bbPress', 'bbpaba' ),
				'href'   => esc_url( BBPABA_TUTORIALS_STARTING ),
				'meta'	 => array( 'title' => _x( 'Getting Started with bbPress (Smashing Magazine)', 'Translators: For the tooltip', 'bbpaba' ) )
			),

			/** bbPress HQ menu items */
			'bbpsites' => array(
				'parent' => $bbpgroup_check_item,
				'title'  => __( 'bbPress HQ', 'bbpaba' ),
				'href'   => 'http://bbpress.org/',
				'meta'   => array( 'title' => __( 'bbPress HQ', 'bbpaba' ) )
			),
			'bbpblog' => array(
				'parent' => $bbpsites,
				'title'  => __( 'Official Blog', 'bbpaba' ),
				'href'   => 'http://bbpress.org/blog/',
				'meta'   => array( 'title' => __( 'Official Blog', 'bbpaba' ) )
			),
			'bbpdevel' => array(
				'parent' => $bbpsites,
				'title'  => __( 'Development Updates', 'bbpaba' ),
				'href'   => 'http://bbpdevel.wordpress.com/',
				'meta'   => array( 'title' => __( 'Development Updates', 'bbpaba' ) )
			),
			'bbptrac' => array(
				'parent' => $bbpsites,
				'title'  => __( 'Trac: Tickets &amp; Bug Reports', 'bbpaba' ),
				'href'   => 'http://bbpress.trac.wordpress.org/roadmap',
				'meta'   => array( 'title' => __( 'Trac: Tickets &amp; Bug Reports', 'bbpaba' ) )
			),
			'bbpplugins' => array(
				'parent' => $bbpsites,
				'title'  => __( 'More free plugins/extensions at WP.org', 'bbpaba' ),
				'href'   => 'http://wordpress.org/extend/plugins/tags/bbpress/',
				'meta'   => array( 'title' => __( 'More free plugins/extensions at WP.org', 'bbpaba' ) )
			),
			'bbpffnews' => array(
				'parent' => $bbpsites,
				'title'  => __( 'bbPress News Planet', 'bbpaba' ),
				'href'   => 'http://friendfeed.com/bbpress-news',
				'meta'   => array( 'title' => _x( 'bbPress News Planet (official and community news via FriendFeed service)', 'Translators: For the tooltip', 'bbpaba' ) )
			),
		);

	}  // end-if constant check for displaying resources


	/** Display the following links only for these locales: de_DE, de_AT, de_CH, de_LU */
	if ( BBPABA_DE_DISPLAY && ( get_locale() == 'de_DE' || get_locale() == 'de_AT' || get_locale() == 'de_CH' || get_locale() == 'de_LU' ) ) {

		/** German public community support forum */
		$menu_items['bbp-forum-de'] = array(
			'parent' => $bbpgroup_check_item,
			'title'  => __( 'German Support Forum', 'bbpaba' ),
			'href'   => 'http://forum.wpde.org/bbpress/',
			'meta'   => array( 'title' => _x( 'Public German Community Support Forum', 'Translators: For the tooltip', 'bbpaba' ) )
		);

		/** German language packs */
		$menu_items['bbp-languages-de'] = array(
			'parent' => $bbpgroup_check_item,
			'title'  => __( 'German language files', 'bbpaba' ),
			'href'   => 'http://deckerweb.de/material/sprachdateien/bbpress-forum/',
			'meta'   => array( 'title' => _x( 'German language files for bbPress 2.x plus some extensions', 'Translators: For the tooltip', 'bbpaba' ) )
		);
	}  // end-if german locales


	/** Show these items only if bbPress plugin is actually installed */
	if ( class_exists( 'bbPress' ) ) {

		/** Main settings section */
		if ( current_user_can( 'manage_options' ) ) {
			$menu_items['bbpsettings'] = array(
				'parent' => $bbpressbar,
				'title'  => esc_attr__( $bbpaba_bbpress_name ) . ' ' . __( 'Main Settings', 'bbpaba' ),
				'href'   => admin_url( 'options-general.php?page=bbpress' ),
				'meta'   => array( 'target' => '', 'title' => esc_attr__( $bbpaba_bbpress_name_tooltip ) . ' ' . _x( 'Main Settings', 'Translators: For the tooltip', 'bbpaba' ) )
			);
		}  // end-if cap check

		/** Settings: Widgets & Menus */
		if ( current_user_can( 'edit_theme_options' ) ) {
			$menu_items['s-widgets'] = array(
				'parent' => $bbpsettings,
				'title'  => esc_attr__( $bbpaba_bbpress_name ) . ' ' . __( 'Widgets', 'bbpaba' ),
				'href'   => admin_url( 'widgets.php' ),
				'meta'   => array( 'target' => '', 'title' => esc_attr__( $bbpaba_bbpress_name_tooltip ) . ' ' . __( 'Widgets', 'bbpaba' ) )
			);
			$menu_items['s-menus'] = array(
				'parent' => $bbpsettings,
				'title'  => esc_attr__( $bbpaba_bbpress_name ) . ' ' . __( 'Menus', 'bbpaba' ),
				'href'   => admin_url( 'nav-menus.php' ),
				'meta'   => array( 'target' => '', 'title' => esc_attr__( $bbpaba_bbpress_name_tooltip ) . ' ' . __( 'Menus', 'bbpaba' ) )
			);
		}  // end-if cap check

		/** Settings: Forum Recount */
		if ( is_super_admin() && current_user_can( 'manage_options' ) ) {
			$menu_items['s-recount'] = array(
				'parent' => $bbpsettings,
				'title'  => __( 'Recount (Topics &amp; Replies)', 'bbpaba' ),
				'href'   => admin_url( 'tools.php?page=bbp-recount' ),
				'meta'   => array( 'target' => '', 'title' => _x( 'Recount (Topics &amp; Replies)', 'Translators: For the tooltip', 'bbpaba' ) )
			);
		}  // end-if cap check

		/** Settings: Update Forums in Multisite - only for super admins */
		if ( current_user_can( 'manage_network' ) ) {
			$menu_items['s-msupdate'] = array(
				'parent' => $bbpsettings,
				'title'  => __( 'Update Forums', 'bbpaba' ),
				'href'   => network_admin_url( 'update-core.php?page=bbpress-update' ),
				'meta'   => array( 'target' => '', 'title' => sprintf( _x( 'Update all %s Forums in Multisite', 'Translators: For the tooltip', 'bbpaba' ), esc_attr__( $bbpaba_bbpress_name_tooltip ) ) )
			);
		}  // end-if multisite check

		/** Settings: Converter */
		if ( class_exists( 'BBP_Converter' ) && current_user_can( 'manage_options' ) ) {
			$menu_items['s-converter'] = array(
				'parent' => $bbpsettings,
				'title'  => __( 'Forum Converter', 'bbpaba' ),
				'href'   => admin_url( 'tools.php?page=bbp-converter' ),
				'meta'   => array( 'target' => '', 'title' => sprintf( _x( 'bbPress Converter: Convert other forum systems to %s', 'Translators: For the tooltip', 'bbpaba' ), esc_attr__( $bbpaba_bbpress_name_tooltip ) ) )
			);
		}  // end-if cap check

		/** Forums section */
		if ( current_user_can( 'publish_forums' ) || current_user_can( 'edit_forum' ) ) {
			$menu_items['forums'] = array(
				'parent' => $bbpressbar,
				'title'  => __( 'Forums', 'bbpaba' ),
				'href'   => admin_url( 'edit.php?post_type=forum' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Forums', 'bbpaba' ) )
			);
			$menu_items['forumsfrontbase'] = array(
				'parent' => $forums,
				'title'  => __( 'View Forums', 'bbpaba' ),
				'href'   => get_post_type_archive_link( 'forum' ),
				'meta'   => array( 'target' => '', 'title' => __( 'View Forums', 'bbpaba' ) )
			);

			/**
			 * @TODO: Here re-add corrected code part for forums frontend-links.
			 * (was there in v1.5, removed because of errors with CPT edit screens!)
			 */

			/**
			 * Action Hook 'bbpaba_custom_forum_items'
			 * allows for hooking other custom forum items in
			 *
			 * @since 1.5
			 */
			do_action( 'bbpaba_custom_forum_items' );

			$menu_items['f-add-forum'] = array(
				'parent' => $forums,
				'title'  => __( 'Add new Forum', 'bbpaba' ),
				'href'   => admin_url( 'post-new.php?post_type=forum' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Add new Forum', 'bbpaba' ) )
			);
		}  // end-if cap check

		/** Topics section */
		if ( current_user_can( 'publish_topics' ) || current_user_can( 'edit_topic' ) ) {
			$menu_items['topics'] = array(
				'parent' => $bbpressbar,
				'title'  => __( 'Topics', 'bbpaba' ),
				'href'   => admin_url( 'edit.php?post_type=topic' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Topics', 'bbpaba' ) )
			);
			$menu_items['t-add-topic'] = array(
				'parent' => $topics,
				'title'  => __( 'Add new Topic', 'bbpaba' ),
				'href'   => admin_url( 'post-new.php?post_type=topic' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Add new Topic', 'bbpaba' ) )
			);
			$menu_items['t-topic-tags'] = array(
				'parent' => $topics,
				'title'  => __( 'Topic Tags', 'bbpaba' ),
				'href'   => admin_url( 'edit-tags.php?taxonomy=topic-tag&post_type=topic' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Topic Tags', 'bbpaba' ) )
			);
		}  // end-if cap check

		/** Replies section */
		if ( current_user_can( 'publish_replies' ) || current_user_can( 'edit_reply' ) ) {
			$menu_items['replies'] = array(
				'parent' => $bbpressbar,
				'title'  => __( 'Replies', 'bbpaba' ),
				'href'   => admin_url( 'edit.php?post_type=reply' ),
				'meta'   => array( 'target' => '', 'title' => _x( 'Replies', 'Translators: For the tooltip', 'bbpaba' ) )
			);
			$menu_items['r-add-reply'] = array(
				'parent' => $replies,
				'title'  => __( 'Add new Reply', 'bbpaba' ),
				'href'   => admin_url( 'post-new.php?post_type=reply' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Add new Reply', 'bbpaba' ) )
			);
		}  // end-if cap check

		/** Users section */
		if ( current_user_can( 'edit_users' ) ) {
			$menu_items['users'] = array(
				'parent' => $bbpressbar,
				'title'  => __( 'Users', 'bbpaba' ),
				'href'   => is_network_admin() ? network_admin_url( 'users.php' ) : admin_url( 'users.php' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Users', 'bbpaba' ) )
			);
		}  // end-if can edit_users
		if ( current_user_can( 'add_users' ) ) {
			$menu_items['u-add-user'] = array(
				'parent' => $users,
				'title'  => __( 'Add new User', 'bbpaba' ),
				'href'   => is_network_admin() ? network_admin_url( 'user-new.php' ) : admin_url( 'user-new.php' ),
				'meta'   => array( 'target' => '', 'title' => __( 'Add new User', 'bbpaba' ) )
			);
		}  // end-if can add_users

		/**
		 * Display last main item in the menu for active extensions/plugins
		 * ATTENTION: This is where plugins/extensions hook in on the sub-level hierarchy
		 *
		 * @since 1.0
		 * @version 1.1
		 */
		if ( BBPABA_EXTENSIONS_DISPLAY && current_user_can( 'activate_plugins' ) ) {

			$menu_items['extensions'] = array(
				'parent' => $bbpressbar,
				'title'  => __( 'Active Extensions', 'bbpaba' ),
				'href'   => is_network_admin() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' ),
				'meta'   => array( 'target' => '', 'title' => _x( 'Active Extensions', 'Translators: For the tooltip', 'bbpaba' ) )
			);

			/**
			 * Action Hook 'bbpaba_custom_extension_items'
			 * allows for hooking other extension-related items in
			 *
			 * @since 1.5
			 */
			do_action( 'bbpaba_custom_extension_items' );

		}  // end-if constant & cap check for displaying extensions

	}  // end-if bbPress conditional


	/**
	 * Display links to active bbPress 2.x plugins/extensions settings' pages
	 *
	 * @since 1.0
	 * @version 1.2
	 */
		/** Include plugin file with plugin support links */
		require_once( BBPABA_PLUGIN_DIR . '/includes/bbpaba-plugins.php' );


	/**
	 * Display links to active bbPress 2.x compatible/specific themes settings' pages
	 *
	 * @since 1.5
	 */
		/** Include plugin file with theme support links */
		if ( BBPABA_THEMES_DISPLAY ) {

			require_once( BBPABA_PLUGIN_DIR . '/includes/bbpaba-themes.php' );

			/**
			 * Action Hook 'bbpaba_custom_theme_items'
			 * allows for hooking other theme-related items in
			 *
			 * @since 1.5
			 */
			do_action( 'bbpaba_custom_theme_items' );

		}  // end-if constant check


	/** Allow menu items to be filtered, but pass in parent menu item IDs */
	$menu_items = (array) apply_filters( 'ddw_bbpress_aba_menu_items', $menu_items, $prefix, $bbpressbar, $bbpsupport, $bbpsites, 
						$bbpsettings, $forums, $forumsfrontbase, $f_gdtools, $f_gdtoolbox, $topics, $replies, 
							$users, $userswangguard, 
						$extensions, $extgdtools, $extgdtoolbox, $extwangguard, $extcptprivacy, $extbbpconvert, 
							$exts2member, $bbpgroup
	);  // end of array


	/**
	 * Add the bbPress top-level menu item
	 *
	 * @since 1.0
	 * @version 1.1
	 *
	 * @param $bbpaba_main_item_title
	 * @param $bbpaba_main_item_title_tooltip
	 * @param $bbpaba_main_item_icon_display
	 */
		/** Filter the main item name */
		$bbpaba_main_item_title = apply_filters( 'bbpaba_filter_main_item', __( 'bbPress', 'bbpaba' ) );

		/** Filter the main item name's tooltip */
		$bbpaba_main_item_title_tooltip = apply_filters( 'bbpaba_filter_main_item_tooltip', _x( 'bbPress Forums', 'Translators: For the tooltip', 'bbpaba' ) );

		/** Filter the main item icon's class/display */
		$bbpaba_main_item_icon_display = apply_filters( 'bbpaba_filter_main_item_icon_display', 'icon-bbpress' );

		$wp_admin_bar->add_menu( array(
			'id'    => $bbpressbar,
			'title' => esc_attr__( $bbpaba_main_item_title ),
			'href'  => admin_url( 'options-general.php?page=bbpress' ),
			'meta'  => array( 'class' => esc_attr( $bbpaba_main_item_icon_display ), 'title' => esc_attr__( $bbpaba_main_item_title_tooltip ) )
		) );


	/** Loop through the menu items */
	foreach ( $menu_items as $id => $menu_item ) {
		
		/** Add in the item ID */
		$menu_item['id'] = $prefix . $id;

		/** Add meta target to each item where it's not already set, so links open in new window/tab */
		if ( ! isset( $menu_item['meta']['target'] ) )		
			$menu_item['meta']['target'] = '_blank';

		/** Add class to links that open up in a new window/tab */
		if ( '_blank' === $menu_item['meta']['target'] ) {
			if ( ! isset( $menu_item['meta']['class'] ) )
				$menu_item['meta']['class'] = '';
			$menu_item['meta']['class'] .= $prefix . 'bbpaba-new-tab';
		}

		/** Add menu items */
		$wp_admin_bar->add_menu( $menu_item );

	}  // end foreach


	/**
	 * Action Hook 'bbpaba_custom_main_items'
	 * allows for hooking other main items in
	 *
	 * @since 1.5
	 */
	do_action( 'bbpaba_custom_main_items' );


	/**
	 * Check for WordPress version to add resource links group
	 * Check against WP 3.3+ only function "wp_editor" - if true display group styling
	 * otherwise display links in WP 3.1/3.2 style
	 *
	 * @since 1.4
	 */
	if ( function_exists( 'wp_editor' ) ) {
		$wp_admin_bar->add_group( array(
			'parent' => $bbpressbar,
			'id'     => $bbpgroup,
			'meta'   => array( 'class' => 'ab-sub-secondary' )
		) );
	} else {
		$wp_admin_bar->add_menu( array(
			'parent' => $bbpressbar,
			'id'     => $bbpgroup
		) );
	}  // end-if wp version check


	/**
	 * Action Hook 'bbpaba_custom_group_items'
	 * allows for hooking other bbPress Group items in
	 *
	 * @since 1.5
	 */
	do_action( 'bbpaba_custom_group_items' );

}  // end of main function


add_action( 'wp_head', 'ddw_bbpress_aba_admin_style' );
add_action( 'admin_head', 'ddw_bbpress_aba_admin_style' );
/**
 * Add the styles for new WordPress Toolbar / Admin Bar entry
 * 
 * @since 1.0
 * @version 1.3
 *
 * @param $bbpaba_main_icon
 */
function ddw_bbpress_aba_admin_style() {

	/** No styles if admin bar is disabled or user is not logged in or items are disabled via constant */
	if ( ! is_admin_bar_showing() || ! is_user_logged_in() || ! BBPABA_DISPLAY )
		return;

	/**
	 * Add CSS styles to wp_head/admin_head
	 * Check against WP 3.3+ only function "wp_editor"
	 */
	/** Add filter for main icon */
	$bbpaba_main_icon = apply_filters( 'bbpaba_filter_main_icon', plugins_url( 'bbpress-admin-bar-addition/images/bbpress-icon2.png',
dirname( __FILE__ ) ) );

	/** Styles for WordPress 3.3 or higher */
	if ( function_exists( 'wp_editor' ) ) {

		?>
		<style type="text/css">
			#wpadminbar.nojs .ab-top-menu > li.menupop.icon-bbpress:hover > .ab-item,
			#wpadminbar .ab-top-menu > li.menupop.icon-bbpress.hover > .ab-item,
			#wpadminbar.nojs .ab-top-menu > li.menupop.icon-bbpress > .ab-item,
			#wpadminbar .ab-top-menu > li.menupop.icon-bbpress > .ab-item {
	      			background-image: url(<?php echo esc_url_raw( $bbpaba_main_icon ); ?>);
				background-repeat: no-repeat;
				background-position: 0.85em 50%;
				padding-left: 30px;
			}
			#wp-admin-bar-ddw-bbpress-f-add-forum,
			#wp-admin-bar-ddw-bbpress-extensions {
	    			border-top: 1px solid;
				margin-bottom: -5px !important;
				padding-bottom: 3px !important;
				padding-top: 3px !important;
			}
			#wp-admin-bar-ddw-bbpress-bbp-forum-de > .ab-item:before,
			#wp-admin-bar-ddw-bbpress-bbp-languages-de > .ab-item:before {
				color: #ff9900;
				content: '• ';
			}
		</style>
		<?php

	/** Styles for WordPress prior 3.3 */
	} else {

		?>
		<style type="text/css">
			#wpadminbar .icon-bbpress > a {
				background: url(<?php echo $bbpaba_main_icon; ?>) no-repeat 0.85em 50% transparent;
				padding-left: 30px;
			}
			#wp-admin-bar-ddw-bbpress-f-add-forum,
			#wp-admin-bar-ddw-bbpress-bbpsettings,
			#wp-admin-bar-ddw-bbpress-extensions {
	    			border-top: 1px solid;
			}
			#wp-admin-bar-ddw-bbpress-bbp-forum-de > a:before,
			#wp-admin-bar-ddw-bbpress-bbp-languages-de > a:before {
				color: #ff9900;
				content: '• ';
			}
		</style>
		<?php

	}  // end if else

}  // end of function ddw_bbpress_aba_admin_style


/**
 * Helper functions for custom branding of the plugin
 *
 * @since 1.5
 */
	/** Include plugin file with special custom stuff */
	require_once( BBPABA_PLUGIN_DIR . '/includes/bbpaba-branding.php' );
