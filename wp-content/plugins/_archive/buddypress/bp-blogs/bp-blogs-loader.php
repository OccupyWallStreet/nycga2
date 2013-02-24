<?php
/**
 * BuddyPress Blogs Streams Loader
 *
 * An blogs stream component, for users, groups, and blog tracking.
 *
 * @package BuddyPress
 * @subpackage Blogs Core
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class BP_Blogs_Component extends BP_Component {

	/**
	 * Start the blogs component creation process
	 *
	 * @since 1.5
	 */
	function __construct() {
		parent::start(
			'blogs',
			__( 'Site Tracking', 'buddypress' ),
			BP_PLUGIN_DIR
		);
	}

	/**
	 * Setup globals
	 *
	 * The BP_BLOGS_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since 1.5
	 * @global obj $bp
	 */
	function setup_globals() {
		global $bp;

		if ( !defined( 'BP_BLOGS_SLUG' ) )
			define ( 'BP_BLOGS_SLUG', $this->id );

		// Global tables for messaging component
		$global_tables = array(
			'table_name'          => $bp->table_prefix . 'bp_user_blogs',
			'table_name_blogmeta' => $bp->table_prefix . 'bp_user_blogs_blogmeta',
		);

		// All globals for messaging component.
		// Note that global_tables is included in this array.
		$globals = array(
			'path'                  => BP_PLUGIN_DIR,
			'slug'                  => BP_BLOGS_SLUG,
			'root_slug'             => isset( $bp->pages->blogs->slug ) ? $bp->pages->blogs->slug : BP_BLOGS_SLUG,
			'has_directory'         => is_multisite(), // Non-multisite installs don't need a top-level Sites directory, since there's only one site
			'notification_callback' => 'bp_blogs_format_notifications',
			'search_string'         => __( 'Search sites...', 'buddypress' ),
			'autocomplete_all'      => defined( 'BP_MESSAGES_AUTOCOMPLETE_ALL' ),
			'global_tables'         => $global_tables,
		);

		// Setup the globals
		parent::setup_globals( $globals );
	}

	/**
	 * Include files
	 */
	function includes() {
		// Files to include
		$includes = array(
			'cache',
			'actions',
			'screens',
			'classes',
			'template',
			'activity',
			'functions',
			'buddybar'
		);

		if ( is_multisite() )
			$includes[] = 'widgets';

		// Include the files
		parent::includes( $includes );
	}

	/**
	 * Setup BuddyBar navigation
	 *
	 * @global obj $bp
	 */
	function setup_nav() {
		global $bp;

		/**
		 * Blog/post/comment menus should not appear on single WordPress setups.
		 * Although comments and posts made by users will still show on their
		 * activity stream.
		 */
		if ( !is_multisite() )
			return false;

		// Add 'Sites' to the main navigation
		$main_nav =  array(
			'name'                => sprintf( __( 'Sites <span>%d</span>', 'buddypress' ), bp_blogs_total_blogs_for_user() ),
			'slug'                => $this->slug,
			'position'            => 30,
			'screen_function'     => 'bp_blogs_screen_my_blogs',
			'default_subnav_slug' => 'my-blogs',
			'item_css_id'         => $this->id
		);

		// Setup navigation
		parent::setup_nav( $main_nav );
	}

	/**
	 * Set up the admin bar
	 *
	 * @global obj $bp
	 */
	function setup_admin_bar() {
		global $bp;

		/**
		 * Blog/post/comment menus should not appear on single WordPress setups.
		 * Although comments and posts made by users will still show on their
		 * activity stream.
		 */
		if ( !is_multisite() )
			return false;

		// Prevent debug notices
		$wp_admin_nav = array();

		// Menus for logged in user
		if ( is_user_logged_in() ) {

			$blogs_link = trailingslashit( $bp->loggedin_user->domain . $this->slug );

			// Add the "Blogs" sub menu
			$wp_admin_nav[] = array(
				'parent' => $bp->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => __( 'Sites', 'buddypress' ),
				'href'   => trailingslashit( $blogs_link )
			);

			// My Blogs
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-my-sites',
				'title'  => __( 'My Sites', 'buddypress' ),
				'href'   => trailingslashit( $blogs_link )
			);

		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Sets up the title for pages and <title>
	 *
	 * @global obj $bp
	 */
	function setup_title() {
		global $bp;

		// Set up the component options navigation for Blog
		if ( bp_is_blogs_component() ) {
			if ( bp_is_my_profile() ) {
				if ( bp_is_active( 'xprofile' ) ) {
					$bp->bp_options_title = __( 'My Sites', 'buddypress' );
				}

			// If we are not viewing the logged in user, set up the current
			// users avatar and name
			} else {
				$bp->bp_options_avatar = bp_core_fetch_avatar( array(
					'item_id' => $bp->displayed_user->id,
					'type'    => 'thumb'
				) );
				$bp->bp_options_title = $bp->displayed_user->fullname;
			}
		}

		parent::setup_title();
	}
}
// Create the blogs component
$bp->blogs = new BP_Blogs_Component();

?>