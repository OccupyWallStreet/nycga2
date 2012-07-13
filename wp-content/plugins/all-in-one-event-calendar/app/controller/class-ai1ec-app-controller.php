<?php
//
//  class-ai1ec-app-controller.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_App_Controller class
 *
 * @package Controllers
 * @author The Seed Studio
 **/
class Ai1ec_App_Controller {
	/**
	 * _instance class variable
	 *
	 * Class instance
	 *
	 * @var null | object
	 **/
	private static $_instance = NULL;

	/**
	 * _load_domain class variable
	 *
	 * Load domain
	 *
	 * @var bool
	 **/
	private static $_load_domain = FALSE;

	/**
	 * page_content class variable
	 *
	 * String storing page content for output by the_content()
	 *
	 * @var null | string
	 **/
	private $page_content = NULL;

	/**
	 * get_instance function
	 *
	 * Return singleton instance
	 *
	 * @return object
	 **/
	static function get_instance() {
		if( self::$_instance === NULL ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * Default constructor - application initialization
	 **/
	private function __construct()
	{
		global $wpdb,
		       $ai1ec_app_helper,
		       $ai1ec_events_controller,
		       $ai1ec_events_helper,
		       $ai1ec_importer_controller,
		       $ai1ec_exporter_controller,
		       $ai1ec_settings_controller,
		       $ai1ec_settings,
		       $ai1ec_themes_controller;

		// register_activation_hook
		register_activation_hook( AI1EC_PLUGIN_NAME . '/' . AI1EC_PLUGIN_NAME . '.php', array( &$this, 'activation_hook' ) );

		// Configure MySQL to operate in GMT time
		$wpdb->query( "SET time_zone = '+0:00'" );

		// Load plugin text domain
		$this->load_textdomain();

		// Install/update database schema as necessary
		$this->install_schema();

		// Install/update cron as necessary
		$this->install_cron();

		// Enable stats collection
		$this->install_n_cron();

		// Continue loading hooks only if themes are installed. Otherwise display a
		// notification on the backend with instructions how to install themes.
		if( ! $ai1ec_themes_controller->are_themes_available() ) {
			add_action( 'admin_notices', array( &$ai1ec_app_helper, 'admin_notices' ) );
			return;
		}

		// ===========
		// = ACTIONS =
		// ===========
		// Calendar theme initialization
		add_action( 'after_setup_theme',                        array( &$ai1ec_themes_controller, 'setup_theme' ) );
		// Create custom post type
		add_action( 'init',                                     array( &$ai1ec_app_helper, 'create_post_type' ) );
		// Handle ICS export requests
		add_action( 'init',                                     array( &$this, 'parse_standalone_request' ) );
		// General initialization
		add_action( 'init',                                     array( &$ai1ec_events_controller, 'init' ) );
		// Load plugin text domain
		add_action( 'init',                                     array( &$this, 'load_textdomain' ) );
		// Register The Event Calendar importer
		add_action( 'admin_init',                               array( &$ai1ec_importer_controller, 'register_importer' ) );
		// Install admin menu items.
		add_action( 'admin_menu',                               array( &$this, 'admin_menu' ), 9 );
		// Enable theme updater page if last version of core themes is older than
		// current version.
		if ( $ai1ec_themes_controller->are_themes_outdated() ) {
			add_action( 'admin_menu',                             array( &$ai1ec_themes_controller, 'register_theme_updater' ) );
		}
		// Add Event counts to dashboard.
		add_action( 'right_now_content_table_end',              array( &$ai1ec_app_helper, 'right_now_content_table_end' ) );
		// add content for our custom columns
		add_action( 'manage_posts_custom_column',               array( &$ai1ec_app_helper, 'custom_columns' ), 10, 2 );
		// Add filtering dropdowns for event categories and tags
		add_action( 'restrict_manage_posts',                    array( &$ai1ec_app_helper, 'taxonomy_filter_restrict_manage_posts' ) );
		// Trigger display of page in front-end depending on request
		add_action( 'template_redirect',                        array( &$this, 'route_request' ) );
		// Add meta boxes to event creation/edit form.
		add_action( 'add_meta_boxes',                           array( &$ai1ec_app_helper, 'add_meta_boxes' ) );
		add_filter( 'screen_layout_columns',                    array( &$ai1ec_app_helper, 'screen_layout_columns' ), 10, 2 );
		// Save event data when post is saved
		add_action( 'save_post',                                array( &$ai1ec_events_controller, 'save_post' ), 10, 2 );
		// Delete event data when post is deleted
		add_action( 'delete_post',                              array( &$ai1ec_events_controller, 'delete_post' ) );
		// Cron job hook
		add_action( 'ai1ec_cron',                               array( &$ai1ec_importer_controller, 'cron' ) );
		// Notification cron job hook
		add_action( 'ai1ec_n_cron',                             array( &$ai1ec_exporter_controller, 'n_cron' ) );
		// Category colors
		add_action( 'events_categories_add_form_fields',        array( &$ai1ec_events_controller, 'events_categories_add_form_fields' ) );
		add_action( 'events_categories_edit_form_fields',       array( &$ai1ec_events_controller, 'events_categories_edit_form_fields' ) );
		add_action( 'created_events_categories',                array( &$ai1ec_events_controller, 'created_events_categories' ) );
		add_action( 'edited_events_categories',                 array( &$ai1ec_events_controller, 'edited_events_categories' ) );
		add_action( 'admin_notices',                            array( &$ai1ec_app_helper, 'admin_notices' ) );
		// Scripts/styles for settings and widget admin screens.
		add_action( 'admin_enqueue_scripts',                    array( &$ai1ec_app_helper, 'admin_enqueue_scripts' ) );
		// Widgets
		add_action( 'widgets_init', create_function( '', "return register_widget( 'Ai1ec_Agenda_Widget' );" ) );

		// ===========
		// = FILTERS =
		// ===========
		add_filter( 'posts_orderby',                            array( &$ai1ec_app_helper, 'orderby' ), 10, 2 );
		// add custom column names and change existing columns
		add_filter( 'manage_ai1ec_event_posts_columns',         array( &$ai1ec_app_helper, 'change_columns' ) );
		// filter the post lists by custom filters
		add_filter( 'parse_query',                              array( &$ai1ec_app_helper, 'taxonomy_filter_post_type_request' ) );
		// Filter event post content, in single- and multi-post views
		add_filter( 'the_content',                              array( &$ai1ec_events_controller, 'event_content' ), PHP_INT_MAX - 1 );
		// Override excerpt filters for proper event display in excerpt form
		add_filter( 'get_the_excerpt',                          array( &$ai1ec_events_controller, 'event_excerpt' ), 11 );
		add_filter( 'the_excerpt',                              array( &$ai1ec_events_controller, 'event_excerpt_noautop' ), 11 );
		remove_filter( 'the_excerpt',                           'wpautop', 10 );
		// Update event post update messages
		add_filter( 'post_updated_messages',                    array( &$ai1ec_events_controller, 'post_updated_messages' ) );
		// Sort the custom columns
		add_filter( 'manage_edit-ai1ec_event_sortable_columns', array( &$ai1ec_app_helper, 'sortable_columns' ) );
		add_filter( 'map_meta_cap',                             array( &$ai1ec_app_helper, 'map_meta_cap' ), 10, 4 );
		// Inject event categories, only in front-end, depending on setting
		if( $ai1ec_settings->inject_categories && ! is_admin() ) {
			add_filter( 'get_terms',                              array( &$ai1ec_app_helper, 'inject_categories' ), 10, 3 );
			add_filter( 'wp_list_categories',                     array( &$ai1ec_app_helper, 'selected_category_link' ), 10, 2 );
		}
		// Rewrite event category URLs to point to calendar page.
		add_filter( 'term_link',                                array( &$ai1ec_app_helper, 'calendar_term_link' ), 10, 3 );
		// Add a link to settings page on the plugin list page.
		add_filter( 'plugin_action_links_' . AI1EC_PLUGIN_BASENAME, array( &$ai1ec_settings_controller, 'plugin_action_links' ) );
		// Add a link to donate page on plugin list page.
		add_filter( 'plugin_row_meta',                          array( &$ai1ec_settings_controller, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'post_type_link',                           array( &$ai1ec_events_helper, 'post_type_link' ), 10, 3 );
		add_filter( 'ai1ec_template_root_path',                 array( &$ai1ec_themes_controller, 'template_root_path' ) );
		add_filter( 'ai1ec_template_root_url',                  array( &$ai1ec_themes_controller, 'template_root_url' ) );

		// ========
		// = AJAX =
		// ========
		// Add iCalendar feed
		add_action( 'wp_ajax_ai1ec_add_ics',    array( &$ai1ec_settings_controller, 'add_ics_feed' ) );
		// Delete iCalendar feed
		add_action( 'wp_ajax_ai1ec_delete_ics', array( &$ai1ec_settings_controller, 'delete_ics_feed' ) );
		// Flush iCalendar feed
		add_action( 'wp_ajax_ai1ec_flush_ics',  array( &$ai1ec_settings_controller, 'flush_ics_feed' ) );
		// Update iCalendar feed
		add_action( 'wp_ajax_ai1ec_update_ics', array( &$ai1ec_settings_controller, 'update_ics_feed' ) );

		// RRule to Text
		add_action( 'wp_ajax_ai1ec_rrule_to_text', array( &$ai1ec_events_helper, 'convert_rrule_to_text' ) );

		// Display Repeat Box
		add_action( 'wp_ajax_ai1ec_get_repeat_box', array( &$ai1ec_events_helper, 'get_repeat_box' ) );
		add_action( 'wp_ajax_ai1ec_get_date_picker_box', array( &$ai1ec_events_helper, 'get_date_picker_box' ) );

		// Disable notification
		add_action( 'wp_ajax_ai1ec_disable_notification', array( &$ai1ec_settings_controller, 'disable_notification' ) );

		// ==============
		// = Shortcodes =
		// ==============
		add_shortcode( 'ai1ec', array( &$ai1ec_events_helper, 'shortcode' ) );

	}

	/**
	 * activation_hook function
	 *
	 * This function is called when activating the plugin
	 *
	 * @return void
	 **/
	function activation_hook() {
		// Load plugin text domain.
		$this->load_textdomain();

		// Flush rewrite rules.
		$this->rewrite_flush();
	}

	/**
	 * load_textdomain function
	 *
	 * Loads plugin text domain
	 *
	 * @return void
	 **/
	function load_textdomain() {
		if( self::$_load_domain === FALSE ) {
			load_plugin_textdomain( AI1EC_PLUGIN_NAME, false, AI1EC_LANGUAGE_PATH );
			self::$_load_domain = TRUE;
		}
	}

	/**
	 * rewrite_flush function
	 *
	 * Get permalinks to work when activating the plugin
	 *
	 * @return void
	 **/
	function rewrite_flush() {
		global $ai1ec_app_helper;
		$ai1ec_app_helper->create_post_type();
		flush_rewrite_rules( true );
	}

	/**
	 * install_schema function
	 *
	 * This function sets up the database, and upgrades it if it is out of date.
	 *
	 * @return void
	 **/
	function install_schema() {
		global $wpdb;

		// If existing DB version is not consistent with current plugin's version,
		// or does not exist, then create/update table structure using dbDelta().
		if( get_option( 'ai1ec_db_version' ) != AI1EC_DB_VERSION )
		{
			// =======================
			// = Create table events =
			// =======================
			$table_name = $wpdb->prefix . 'ai1ec_events';
			$sql = "CREATE TABLE $table_name (
					post_id           bigint(20) NOT NULL,
					start             datetime NOT NULL,
					end               datetime,
					allday            tinyint(1) NOT NULL,
					recurrence_rules  longtext,
					exception_rules   longtext,
					recurrence_dates  longtext,
					exception_dates   longtext,
					venue             varchar(255),
					country           varchar(255),
					address           varchar(255),
					city              varchar(255),
					province          varchar(255),
					postal_code       varchar(32),
					show_map          tinyint(1),
					contact_name      varchar(255),
					contact_phone     varchar(32),
					contact_email     varchar(128),
					cost              varchar(255),
					ical_feed_url     varchar(255),
					ical_source_url   varchar(255),
					ical_organizer    varchar(255),
					ical_contact      varchar(255),
					ical_uid          varchar(255),
					PRIMARY KEY  (post_id)
				) CHARACTER SET utf8;";

			// ==========================
			// = Create table instances =
			// ==========================
			$table_name = $wpdb->prefix . 'ai1ec_event_instances';
			$sql .= "CREATE TABLE $table_name (
					id      bigint(20) NOT NULL AUTO_INCREMENT,
					post_id bigint(20) NOT NULL,
					start   datetime NOT NULL,
					end     datetime NOT NULL,
					PRIMARY KEY  (id)
				) CHARACTER SET utf8;";

			// ======================
			// = Create table feeds =
			// ======================
			$table_name = $wpdb->prefix . 'ai1ec_event_feeds';
			$sql .= "CREATE TABLE $table_name (
					feed_id       bigint(20) NOT NULL AUTO_INCREMENT,
					feed_url      varchar(255) NOT NULL,
					feed_category bigint(20) NOT NULL,
					feed_tags     varchar(255) NOT NULL,
					PRIMARY KEY  (feed_id)
				) CHARACTER SET utf8;";

			// ================================
			// = Create table category colors =
			// ================================
			$table_name = $wpdb->prefix . 'ai1ec_event_category_colors';
			$sql .= "CREATE TABLE $table_name (
					term_id       bigint(20) NOT NULL,
					term_color    varchar(255) NOT NULL,
					PRIMARY KEY  (term_id)
				) CHARACTER SET utf8;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option( 'ai1ec_db_version', AI1EC_DB_VERSION );
		}
	}

	/**
	 * install_cron function
	 *
	 * This function sets up the cron job for updating the events, and upgrades it if it is out of date.
	 *
	 * @return void
	 **/
	function install_cron() {
		// If existing CRON version is not consistent with current plugin's version,
		// or does not exist, then create/update cron using
		if( get_option( 'ai1ec_cron_version' ) != AI1EC_CRON_VERSION ) {
			global $ai1ec_settings;
			// delete our scheduled crons
			wp_clear_scheduled_hook( 'ai1ec_cron' );
			// set the new cron
			wp_schedule_event( time(), $ai1ec_settings->cron_freq, 'ai1ec_cron' );
			// update the cron version
			update_option( 'ai1ec_cron_version', AI1EC_CRON_VERSION );
		}
	}

	/**
	 * install_notification_cron function
	 *
	 * This function sets up the cron job for collecting stats
	 *
	 * @return void
	 **/
	function install_n_cron() {
		global $ai1ec_settings;

		// if stats are disabled, cancel the cron
		if( $ai1ec_settings->allow_statistics == false ) {
			// delete our scheduled crons
			wp_clear_scheduled_hook( 'ai1ec_n_cron_version' );

			// remove the cron version
			delete_option( 'ai1ec_n_cron_version' );

			// prevent the execution of the code below
			return;
		}

		// If existing CRON version is not consistent with current plugin's version,
		// or does not exist, then create/update cron using
		if( get_option( 'ai1ec_n_cron_version' ) != AI1EC_N_CRON_VERSION ) {
			// delete our scheduled crons
			wp_clear_scheduled_hook( 'ai1ec_n_cron_version' );
			// set the new cron
			wp_schedule_event( time(), AI1EC_N_CRON_FREQ, 'ai1ec_n_cron' );
			// update the cron version
			update_option( 'ai1ec_n_cron_version', AI1EC_N_CRON_VERSION );
		}
	}

	/**
	 * admin_menu function
	 * Display the admin menu items using the add_menu_page WP function.
	 *
	 * @return void
	 */
	function admin_menu() {
		global $ai1ec_settings_controller,
           $ai1ec_settings_helper,
           $ai1ec_settings,
           $ai1ec_themes_controller;

		// =======================
		// = Calendar Feeds Page =
		// =======================
		$ai1ec_settings->feeds_page = add_submenu_page(
			'edit.php?post_type=' . AI1EC_POST_TYPE,
			__( 'Calendar Feeds', AI1EC_PLUGIN_NAME ),
			__( 'Calendar Feeds', AI1EC_PLUGIN_NAME ),
			'manage_ai1ec_feeds',
			AI1EC_PLUGIN_NAME . '-feeds',
			array( &$ai1ec_settings_controller, 'view_feeds' )
		);
		// Allow feeds page to have additional meta boxes added to it.
		add_action( "load-{$ai1ec_settings->feeds_page}", array( &$ai1ec_settings_helper, 'add_feeds_meta_boxes') );
		// Load our plugin's meta boxes.
		add_action( "load-{$ai1ec_settings->feeds_page}", array( &$ai1ec_settings_controller, 'add_feeds_meta_boxes' ) );

		// =================
		// = Settings Page =
		// =================
		$ai1ec_settings->settings_page = add_submenu_page(
			'options-general.php',
			__( 'Calendar', AI1EC_PLUGIN_NAME ),
			__( 'Calendar', AI1EC_PLUGIN_NAME ),
			'manage_ai1ec_options',
			AI1EC_PLUGIN_NAME . '-settings',
			array( &$ai1ec_settings_controller, 'view_settings' )
		);
		// Allow settings page to have additional meta boxes added to it.
		add_action( "load-{$ai1ec_settings->settings_page}", array( &$ai1ec_settings_helper, 'add_settings_meta_boxes') );
		// Load our plugin's meta boxes.
		add_action( "load-{$ai1ec_settings->settings_page}", array( &$ai1ec_settings_controller, 'add_settings_meta_boxes' ) );
		// ========================
		// = Calendar Update Page =
		// ========================
		add_submenu_page(
			'edit.php?post_type=' . AI1EC_POST_TYPE,
			__( 'Upgrade', AI1EC_PLUGIN_NAME ),
			__( 'Upgrade', AI1EC_PLUGIN_NAME ),
			'update_plugins',
			AI1EC_PLUGIN_NAME . '-upgrade',
			array( &$this, 'upgrade' )
		);
		remove_submenu_page( 'edit.php?post_type=' . AI1EC_POST_TYPE, AI1EC_PLUGIN_NAME . '-upgrade' );
	}

	/**
	 * route_request function
	 *
	 * Determines if the page viewed should be handled by this plugin, and if so
	 * schedule new content to be displayed.
	 *
	 * @return void
	 **/
	function route_request() {
		global $ai1ec_settings,
					 $ai1ec_calendar_controller,
					 $ai1ec_events_controller,
					 $ai1ec_events_helper,
					 $post;

		// regex pattern to match our shortcode [ai1ec]
		// \[(\[?)(ai1ec)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)
		$out = array();
		if( isset( $post->post_content ) ) {
			preg_match( "/\[(\[?)(ai1ec)\b([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s", $post->post_content, $out );
		}

		// Find out if the calendar page ID is defined, and we're on it
		if( $ai1ec_settings->calendar_page_id &&
				is_page( $ai1ec_settings->calendar_page_id ) )
		{
			// Proceed only if the page password is correctly entered OR
			// the page doesn't require a password
			if( ! post_password_required( $ai1ec_settings->calendar_page_id ) ) {
				ob_start();
				// Render view
				$ai1ec_calendar_controller->view();
				// Save page content to local variable
				$this->page_content = ob_get_contents();
				ob_end_clean();

				// Replace page content - make sure it happens at (almost) the very end of
				// page content filters (some themes are overly ambitious here)
				add_filter( 'the_content', array( &$this, 'append_content' ), PHP_INT_MAX - 1 );
			}
		} else if( isset( $out[2] ) && $out[2] == 'ai1ec' ) {
			// if content has [ai1ec] shortcode, display the calendar page
			$attr = shortcode_parse_atts( $out[3] );
			// Proceed only if the page password is correctly entered OR
			// the page doesn't require a password
			if( ! post_password_required( $post->ID ) ) {
				ob_start();
				if( isset( $attr["view"] ) && ! empty( $attr["view"] ) ) {
					switch( $attr["view"] ) {
						case "monthly":
							$_REQUEST["action"] = "ai1ec_month";
							break;
						case "weekly":
							$_REQUEST["action"] = "ai1ec_week";
							break;
						case "agenda":
							$_REQUEST["action"] = "ai1ec_agenda";
							break;
					}
				}

				// Parse categories by name
				if( isset( $attr["cat_name"] ) && ! empty( $attr["cat_name"] ) ) {
					foreach( explode( ',', $attr["cat_name"] ) as $c ) {
						$cid = get_term_by( "name", $c, "events_categories" );
						if( $cid !== false ) {
							// if term was found, include it
							$_REQUEST["ai1ec_cat_ids"] = $cid->term_id . ',';
						}
					}
					// remove last comma only if there is some content in the var
					if( isset( $_REQUEST["ai1ec_cat_ids"] ) && strlen( $_REQUEST["ai1ec_cat_ids"] ) > 2 ) {
						$_REQUEST["ai1ec_cat_ids"] = substr( $_REQUEST["ai1ec_cat_ids"], 0, -1 );
					}
				}

				// Parse categories by id
				if( isset( $attr["cat_id"] ) && ! empty( $attr["cat_id"] ) ) {
					// append cat_id to the ai1ec_cat_ids array
					$_REQUEST["ai1ec_cat_ids"] = ( isset( $_REQUEST["ai1ec_cat_ids"] ) && strlen( $_REQUEST["ai1ec_cat_ids"] ) > 0 )
																				 ? $_REQUEST["ai1ec_cat_ids"] . ',' . $attr["cat_id"]
																				 : $attr["cat_id"];
				}

				// Parse tags by name
				if( isset( $attr["tag_name"] ) && ! empty( $attr["tag_name"] ) ) {
					foreach( explode( ',', $attr["tag_name"] ) as $t ) {
						$tid = get_term_by( "name", $t, "events_tags" );
						if( $tid !== false ) {
							// if term was found, include it
							$_REQUEST["ai1ec_tag_ids"] = $tid->term_id . ',';
						}
					}
					// remove last comma only if there is some content in the var
					if( isset( $_REQUEST["ai1ec_tag_ids"] ) && strlen( $_REQUEST["ai1ec_tag_ids"] ) > 2 ) {
						$_REQUEST["ai1ec_tag_ids"] = substr( $_REQUEST["ai1ec_tag_ids"], 0, -1 );
					}
				}

				// Parse tags by id
				if( isset( $attr["tag_id"] ) && ! empty( $attr["tag_id"] ) ) {
					// append tag_id to the ai1ec_tag_ids array
					$_REQUEST["ai1ec_tag_ids"] = ( isset( $_REQUEST["ai1ec_tag_ids"] ) && strlen( $_REQUEST["ai1ec_tag_ids"] ) > 0 )
																				 ? $_REQUEST["ai1ec_tag_ids"] . ',' . $attr["tag_id"]
																				 : $attr["tag_id"];
				}

				// Parse posts by id
				if( isset( $attr["post_id"] ) && ! empty( $attr["post_id"] ) ) {
					$_REQUEST["ai1ec_post_ids"] = $attr["post_id"];
				}

				// Render view
				$ai1ec_calendar_controller->view();
				// Save page content to local variable
				$this->page_content = ob_get_contents();
				ob_end_clean();

				// Replace page content - make sure it happens at (almost) the very end of
				// page content filters (some themes are overly ambitious here)
				add_filter( 'the_content', array( &$this, 'append_content' ), PHP_INT_MAX - 1 );
			}
		}
	}

	/**
	 * parse_standalone_request function
	 *
	 * @return void
	 **/
	function parse_standalone_request() {
		global $ai1ec_exporter_controller,
					 $ai1ec_app_helper;

		$plugin     = $ai1ec_app_helper->get_param('plugin');
		$action     = $ai1ec_app_helper->get_param('action');
		$controller = $ai1ec_app_helper->get_param('controller');

		if( ! empty( $plugin ) && $plugin == AI1EC_PLUGIN_NAME && ! empty( $controller ) && ! empty( $action ) ) {
			if( $controller == "ai1ec_exporter_controller" ) :
				switch( $action ) :
					case 'export_events':
						$ai1ec_exporter_controller->export_events();
						break;
				endswitch;
			endif; // ai1ec_exporter_controller
		}
	}

	/**
	 * append_content function
	 *
	 * Append locally generated content to normal page content (if in the loop;
	 * don't want to do it for all instances of the_content() on the page!)
	 *
	 * @param string $content Post/Page content
	 * @return string         Post/Page content
	 **/
	function append_content( $content )
	{
		// Enclose entire content (including any admin-provided page content) in
		// the calendar container div
		if( in_the_loop() )
			$content =
				'<div id="ai1ec-container" class="ai1ec-container thenly">' .
				$content . $this->page_content .
				'</div>';

		return $content;
	}

	/**
	 * upgrade function
	 *
	 * @return void
	 **/
	function upgrade() {
		// continue only if user can update plugins
		if ( ! current_user_can( 'update_plugins' ) )
			wp_die( __( 'You do not have sufficient permissions to update plugins for this site.' ) );
		// use our custom class
		$upgrader = new Ai1ec_Updater();
		// update the plugin
		$upgrader->upgrade( 'all-in-one-event-calendar' );
		// give user a way out of the page
		echo '<a href="' . admin_url( 'edit.php?post_type=' . AI1EC_POST_TYPE ) . '">Continue here</a>';
	}
}
// END class
