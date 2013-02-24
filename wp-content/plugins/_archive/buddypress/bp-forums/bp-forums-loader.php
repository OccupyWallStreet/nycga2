<?php
/**
 * BuddyPress Forums Loader
 *
 * A discussion forums component. Comes bundled with bbPress stand-alone.
 *
 * @package BuddyPress
 * @subpackage Forums Core
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class BP_Forums_Component extends BP_Component {

	/**
	 * Start the forums component creation process
	 *
	 * @since 1.5
	 */
	function __construct() {
		parent::start(
			'forums',
			__( 'Discussion Forums', 'buddypress' ),
			BP_PLUGIN_DIR
		);
	}

	/**
	 * Setup globals
	 *
	 * The BP_FORUMS_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since 1.5
	 * @global obj $bp
	 */
	function setup_globals() {
		global $bp;

		// Define the parent forum ID
		if ( !defined( 'BP_FORUMS_PARENT_FORUM_ID' ) )
			define( 'BP_FORUMS_PARENT_FORUM_ID', 1 );

		// Define a slug, if necessary
		if ( !defined( 'BP_FORUMS_SLUG' ) )
			define( 'BP_FORUMS_SLUG', $this->id );

		// The location of the bbPress stand-alone config file
		if ( isset( $bp->site_options['bb-config-location'] ) )
			$this->bbconfig = $bp->site_options['bb-config-location'];

		// All globals for messaging component.
		// Note that global_tables is included in this array.
		$globals = array(
			'path'                  => BP_PLUGIN_DIR,
			'slug'                  => BP_FORUMS_SLUG,
			'root_slug'             => isset( $bp->pages->forums->slug ) ? $bp->pages->forums->slug : BP_FORUMS_SLUG,
			'has_directory'         => true,
			'notification_callback' => 'messages_format_notifications',
			'search_string'         => __( 'Search Forums...', 'buddypress' ),
		);

		parent::setup_globals( $globals );
	}

	/**
	 * Include files
	 */
	function includes() {

		// Files to include
		$includes = array(
			'actions',
			'screens',
			'classes',
			'filters',
			'template',
			'functions',
		);

		// Admin area
		if ( is_admin() )
			$includes[] = 'admin';

		// bbPress stand-alone
		if ( !defined( 'BB_PATH' ) )
			$includes[] = 'bbpress-sa';

		parent::includes( $includes );
	}

	/**
	 * Setup BuddyBar navigation
	 *
	 * @global obj $bp
	 */
	function setup_nav() {
		global $bp;

		// Stop if forums haven't been set up yet
		if ( !bp_forums_is_installed_correctly() )
			return;

		// Stop if there is no user displayed or logged in
		if ( !is_user_logged_in() && !isset( $bp->displayed_user->id ) )
			return;

		// Add 'Forums' to the main navigation
		$main_nav = array(
			'name'                => __( 'Forums', 'buddypress' ),
			'slug'                => $this->slug,
			'position'            => 80,
			'screen_function'     => 'bp_member_forums_screen_topics',
			'default_subnav_slug' => 'topics',
			'item_css_id'         => $this->id
		);

		// Determine user to use
		if ( isset( $bp->displayed_user->domain ) ) {
			$user_domain = $bp->displayed_user->domain;
			$user_login  = $bp->displayed_user->userdata->user_login;
		} elseif ( isset( $bp->loggedin_user->domain ) ) {
			$user_domain = $bp->loggedin_user->domain;
			$user_login  = $bp->loggedin_user->userdata->user_login;
		} else {
			return;
		}

		// User link
		$forums_link = trailingslashit( $user_domain . $this->slug );

		// Additional menu if friends is active
		$sub_nav[] = array(
			'name'            => __( 'Topics Started', 'buddypress' ),
			'slug'            => 'topics',
			'parent_url'      => $forums_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'bp_member_forums_screen_topics',
			'position'        => 20,
			'item_css_id'     => 'topics'
		);

		// Additional menu if friends is active
		$sub_nav[] = array(
			'name'            => __( 'Replied To', 'buddypress' ),
			'slug'            => 'replies',
			'parent_url'      => $forums_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'bp_member_forums_screen_replies',
			'position'        => 40,
			'item_css_id'     => 'replies'
		);

		// Favorite forums items. Disabled until future release.
		/*
		$sub_nav[] = array(
			'name'            => __( 'Favorites', 'buddypress' ),
			'slug'            => 'favorites',
			'parent_url'      => $forums_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'bp_member_forums_screen_favorites',
			'position'        => 60,
			'item_css_id'     => 'favorites'
		);
		*/

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up the admin bar
	 *
	 * @global obj $bp
	 */
	function setup_admin_bar() {
		global $bp;

		// Prevent debug notices
		$wp_admin_nav = array();

		// Menus for logged in user
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables
			$user_domain = $bp->loggedin_user->domain;
			$user_login  = $bp->loggedin_user->userdata->user_login;
			$forums_link = trailingslashit( $user_domain . $this->slug );

			// Add the "My Account" sub menus
			$wp_admin_nav[] = array(
				'parent' => $bp->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => __( 'Forums', 'buddypress' ),
				'href'   => trailingslashit( $forums_link )
			);

			// Topics
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-topics-started',
				'title'  => __( 'Topics Started', 'buddypress' ),
				'href'   => trailingslashit( $forums_link . 'topics' )
			);

			// Replies
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-replies',
				'title'  => __( 'Replies', 'buddypress' ),
				'href'   => trailingslashit( $forums_link . 'replies' )
			);

			// Favorites
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'id'     => 'my-account-' . $this->id . '-favorite-topics',
				'title'  => __( 'Favorite Topics', 'buddypress' ),
				'href'   => trailingslashit( $forums_link . 'favorites' )
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

		// Adjust title based on view
		if ( bp_is_forums_component() ) {
			if ( bp_is_my_profile() ) {
				$bp->bp_options_title = __( 'Forums', 'buddypress' );
			} else {
				$bp->bp_options_avatar = bp_core_fetch_avatar( array(
					'item_id' => $bp->displayed_user->id,
					'type'    => 'thumb'
				) );
				$bp->bp_options_title  = $bp->displayed_user->fullname;
			}
		}

		parent::setup_title();
	}
}
// Create the forums component
$bp->forums = new BP_Forums_Component();

?>