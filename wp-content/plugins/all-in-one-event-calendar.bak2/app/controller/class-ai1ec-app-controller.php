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
 * @author time.ly
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
	 * The scripts that must be echoed in the footer for the admin pages
	 * 
	 * @var string
	 */
	private $scripts_in_footer = '';
	/**
	 * The scripts that must be echoed in the footer for the frontend
	 * 
	 * @var string
	 */
	private $scripts_in_footer_frontend = '';

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
		       $ai1ec_themes_controller,
		       $ai1ec_importer_plugin_helper;

		// register_activation_hook
		register_activation_hook( AI1EC_PLUGIN_NAME . '/' . AI1EC_PLUGIN_NAME . '.php', array( &$this, 'activation_hook' ) );

		// Configure MySQL to operate in GMT time
		$wpdb->query( "SET time_zone = '+0:00'" );

		// Load plugin text domain
		$this->load_textdomain();

		// Install/update database schema as necessary
		$this->install_schema();

		// Enable stats collection
		$this->install_n_cron();


		// Enable plugins for importing events from external sources
		$this->install_plugins();


		// Enable checking for cron updates
		$this->install_u_cron();
		

		// Continue loading hooks only if themes are installed. Otherwise display a
		// notification on the backend with instructions how to install themes.
		if( ! $ai1ec_themes_controller->are_themes_available() ) {
			// Enables the hidden themes installer page
			add_action( 'admin_menu', array( &$ai1ec_themes_controller, 'register_theme_installer' ), 1 );
			// Redirects the user to install theme page
			add_action( 'admin_menu', array( &$this, 'check_themes' ), 2 );
			return;
		}


		// ===========
		// = ACTIONS =
		// ===========
		// Very early on in WP bootstrap, prepare to do any requested theme preview.
		add_action( 'setup_theme',                              array( &$ai1ec_themes_controller, 'preview_theme' ) );
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
		// Load back-end javascript files
		add_action( 'init',                                     array( &$this, 'load_admin_js' ) );
		// Load the scripts for the backend for wordpress version < 3.3
		add_action( 'admin_footer',                             array( $this, 'print_admin_script_footer_for_wordpress_32' ) );
		// Load the scripts for the frontend for wordpress version < 3.3
		add_action( 'wp_footer',                                array( $this, 'print_frontend_script_footer_for_wordpress_32' ) );
		// Set an action to load front-end javascript
		add_action( 'ai1ec_load_frontend_js',                   array( &$this, 'load_frontend_js' ), 10, 1 );
		// Check if themes are installed
		add_action( 'init',                                     array( &$ai1ec_themes_controller, 'check_themes' ) );
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
		// Notification cron job hook
		add_action( 'ai1ec_n_cron',                             array( &$ai1ec_exporter_controller, 'n_cron' ) );
		// Updates cron job hook
		add_action( 'ai1ec_u_cron',                             array( &$ai1ec_settings_controller, 'u_cron' ) );
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

		// RRule to Text
		add_action( 'wp_ajax_ai1ec_rrule_to_text', array( &$ai1ec_events_helper, 'convert_rrule_to_text' ) );

		// Display Repeat Box
		add_action( 'wp_ajax_ai1ec_get_repeat_box', array( &$ai1ec_events_helper, 'get_repeat_box' ) );
		add_action( 'wp_ajax_ai1ec_get_date_picker_box', array( &$ai1ec_events_helper, 'get_date_picker_box' ) );

		// Disable notifications
		add_action( 'wp_ajax_ai1ec_disable_notification', array( &$ai1ec_settings_controller, 'disable_notification' ) );
		add_action( 'wp_ajax_ai1ec_disable_intro_video', array( &$ai1ec_settings_controller, 'disable_intro_video' ) );

		// ==============
		// = Shortcodes =
		// ==============
		add_shortcode( 'ai1ec', array( &$ai1ec_events_helper, 'shortcode' ) );

	}
	/**
	 * Load javascript files for frontend pages.
	 *
	 * @param $is_calendar_page boolean Wheter we are displaying the calendar or not
	 */
	public function load_frontend_js( $is_calendar_page ) {
		// Get translation data
		$data = $this->get_translation_data();
		global $ai1ec_settings,
		       $ai1ec_view_helper,
		       $ai1ec_calendar_controller;
		// Load requirejs
		$ai1ec_view_helper->admin_enqueue_script( 'ai1ec_requirejs', 'require.js' );

		// We need to specify the location of the main.js file.
		add_filter( 'clean_url', array( $this, 'add_data_main' ), 11, 1 );
		// ======
		// = JS =
		// ======
		if( $this->check_if_single_event_page() === TRUE ) {
			$ai1ec_view_helper->admin_enqueue_script( 'ai1ec_event_category', 'pages/event.js', array( 'ai1ec_requirejs' ), true );
			// This is needed by gmaps.
			$this->localize_script_for_requirejs( 'ai1ec_event_category', 'ai1ec_config', $data, true );
		}
		if( $is_calendar_page === TRUE ) {
			// Require the correct script to load
			$ai1ec_view_helper->admin_enqueue_script( 'ai1ec_calendar_requirejs', 'pages/calendar.js', array( 'ai1ec_requirejs' ), true );
			// now it's time to load custom functions from the themes
			try {
				$ai1ec_view_helper->theme_enqueue_script( 'ai1ec_add_new_event_require', "pages/calendar.js", array( 'ai1ec_requirejs' ), true );
			}
			catch ( Ai1ec_File_Not_Found $e ) {
				// There is no custom file to load.
			}
			$ai1ec_calendar_controller->load_js_translations();
		}
		$this->load_require_js_config();
	}
	/**
	 * Add the data-main attribute
	 *
	 * @param string $url
	 * @return string
	 */
	public function add_data_main( $url ) {
		if ( FALSE === strpos( $url, 'require.js' ) ) {
			return $url;
		}
		$data_main = AI1EC_ADMIN_THEME_JS_URL . '/main.js';
		//Must be a ', not "!
		return "$url' data-main='$data_main";
	}
	/**
	*	Check if we are in the calendar feeds page
	*
	* @return boolean TRUE if we are in the calendar feeds page FALSE otherwise
	*/
	private function check_if_calendar_feeds_page() {
		$path_details = pathinfo( $_SERVER["SCRIPT_NAME"] );
		$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : FALSE;
		$page = isset( $_GET['page'] ) ? $_GET['page'] : FALSE;
		if( $post_type === FALSE || $page === FALSE ) {
			return FALSE;
		}
		$is_calendar_feed_page = $path_details['basename'] === 'edit.php' &&
		                         $post_type                === 'ai1ec_event' &&
		                         $page                     === 'all-in-one-event-calendar-feeds';
		return $is_calendar_feed_page;
	}
	/**
	* check if we are editing an event
	*
	* @return boolean TRUE if we are editing an event FALSE otherwise
	*/
	private function check_if_editing_event() {
		$path_details = pathinfo( $_SERVER["SCRIPT_NAME"] );
		$post_id = isset( $_GET['post'] ) ? $_GET['post'] : FALSE;
		$action = isset( $_GET['action'] ) ? $_GET['action'] : FALSE;
		if( $post_id === FALSE || $action === FALSE ) {
			return FALSE;
		}
		$editing = $path_details['basename'] === 'post.php' &&
		           $action                   === 'edit' &&
		           get_post_type( $post_id ) === AI1EC_POST_TYPE;
		return $editing;
	}
	/**
	 * check if we are creating a new event
	 *
	 * @return boolean TRUE if we are creating a new event FALSE otherwise
	 */
	private function check_if_creating_new_event() {
		$path_details = pathinfo( $_SERVER["SCRIPT_NAME"] );
		$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
		return $path_details['basename'] === 'post-new.php' && $post_type === AI1EC_POST_TYPE;
	}
	/**
	* Check if we are accessing the settings page
	*
	* @return boolean TRUE if we are accessing the settings page FALSE otherwise
	*/
	private function check_if_calendar_settings_page() {
		$path_details = pathinfo( $_SERVER["SCRIPT_NAME"] );
		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';
		return $path_details['basename'] === 'options-general.php' && $page === AI1EC_PLUGIN_NAME . '-settings';
	}
	/**
	* Check if we are accessing the events category page
	*
	* @return boolean TRUE if we are accessing the events category page FALSE otherwise
	*/
	private function check_if_edit_event_categories() {
		$path_details = pathinfo( $_SERVER["SCRIPT_NAME"] );
		$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
		return $path_details['basename'] === 'edit-tags.php' && $post_type === AI1EC_POST_TYPE;
	}
	/**
	 * Check if we are accessing a single event page
	 *
	 * @return boolean TRUE if we are accessing a single event page FALSE otherwise
	 */
	private function check_if_single_event_page() {
		return get_post_type() === AI1EC_POST_TYPE;
	}
	/**
	 * create the array that's needed for translation and passing data
	 *
	 * @return $data array the dynamic data array
	 */
	private function get_translation_data() {
		global $ai1ec_events_helper,
		       $ai1ec_settings,
		       $wp_locale,
		       $ai1ec_view_helper,
		       $ai1ec_importer_plugin_helper;
		$data = array(
				'select_one_option'              => __( 'Select at least one user  group / page to subscribe', AI1EC_PLUGIN_NAME ),
				'error_no_response'              => __( 'An unexpected error occurred, try reloading the page', AI1EC_PLUGIN_NAME ),
				'no_more_subscription'           => __( 'No subscriptions yet.', AI1EC_PLUGIN_NAME ),
				'no_more_than_ten'               => __( 'Please select no more than ten users / groups / pages at a time to avoid overloading Facebook Requests', AI1EC_PLUGIN_NAME ),
				// ICS feed error messages
				'duplicate_feed_message'         => esc_html__( 'This feed is already being imported.', AI1EC_PLUGIN_NAME ),
				'invalid_url_message'            => esc_html__( 'Please enter a valid iCalendar URL.', AI1EC_PLUGIN_NAME ),
				// Current time, used for date/time pickers
				'now'                            => $ai1ec_events_helper->gmt_to_local( time() ),
				// Date format for date pickers
				'date_format'                    => $ai1ec_settings->input_date_format,
				// Names for months in date picker header (escaping is done in wp_localize_script)
				'month_names'                    => implode( ',', $wp_locale->month ),
				// Names for days in date picker header (escaping is done in wp_localize_script)
				'day_names'                      => implode( ',', $wp_locale->weekday_initial ),
				// Start the week on this day in the date picker
				'week_start_day'                 => $ai1ec_settings->week_start_day,
				// 24h time format for time pickers
				'twentyfour_hour'                => $ai1ec_settings->input_24h_time,
				// Set region biasing for geo_autocomplete plugin
				'region'                         => ( $ai1ec_settings->geo_region_biasing ) ? $ai1ec_events_helper->get_region() : '',
				'disable_autocompletion'         => $ai1ec_settings->disable_autocompletion,
				'error_message_not_valid_lat'    => __( 'Please enter a valid latitude. A valid latitude is comprised between +90 and -90.', AI1EC_PLUGIN_NAME ),
				'error_message_not_valid_long'   => __( 'Please enter a valid longitude. A valid longitude is comprised between +180 and -180.', AI1EC_PLUGIN_NAME ),
				'error_message_not_entered_lat'  => __( 'When the "Input coordinates" checkbox is checked, "Latitude" is a required field.', AI1EC_PLUGIN_NAME ),
				'error_message_not_entered_long' => __( 'When the "Input coordinates" checkbox is checked, "Longitude" is a required field.', AI1EC_PLUGIN_NAME ),
				'gmaps_language'                 => $ai1ec_events_helper->get_lang(),
				// This function will be set later if needed
				'page'                           => '',
				'page_on_front_description'      => __( 'This setting cannot be changed in All-in-One Event Calendar Platform mode.', AI1EC_PLUGIN_NAME ),
				// if the user is the super admin we disable this later
				'strict_mode'                    => $ai1ec_settings->event_platform_strict,
				'platform_active'                => $ai1ec_settings->event_platform_active,
				'facebook_logged_in'             => $ai1ec_importer_plugin_helper->check_if_we_have_a_valid_facebook_access_token(),
				'app_id_and_secret_are_required' => __( "You must specify both an app id and app secret to connect to Facebook", AI1EC_PLUGIN_NAME ),
		);
		return $data;
	}
	/**
	 * Load the required javascript files
	 *
	 */
	public function load_admin_js() {
		global $ai1ec_view_helper,
		       $ai1ec_settings;
		// Initialize dashboard view
		$data = $this->get_translation_data();
		if( is_admin() ) {
			// Load requirejs
			$ai1ec_view_helper->admin_enqueue_script( 'ai1ec_requirejs', 'require.js', array( 'postbox' ) );

			// We need to specify the location of the main.js file.
			add_filter( 'clean_url', array( $this, 'add_data_main' ), 11, 1 );
			// Load common backend scripts
			$ai1ec_view_helper->admin_enqueue_script( 'ai1ec_common_backend', 'pages/common_backend.js', array( 'ai1ec_requirejs' ), true );

			// Do not further modify UI for super admins.
			if( is_super_admin() ) {
				$data['strict_mode'] = FALSE;
			}
			$script_to_load = FALSE;

			// Start the scripts for the Calendar feeds pages
			if( $this->check_if_calendar_feeds_page() === TRUE ) {

				// Load script for the importer plugins
				$script_to_load = 'calendar_feeds.js';
				// Set the page
				$data['page'] = $ai1ec_settings->settings_page;
			}
			// Start the scripts for the event category page
			if( $this->check_if_edit_event_categories() === TRUE ) {
				// Load script required when editing categories
				$script_to_load = 'event_category.js';
			}
			// Load the js needed when you edit an event / add a new event
			if( $this->check_if_creating_new_event() === TRUE || $this->check_if_editing_event() === TRUE ) {
				// Load script for adding / modifying events
				$script_to_load = 'add_new_event.js';
			}
			// Set a variable if you are in the calendar settings page
			if( $this->check_if_calendar_settings_page() === TRUE ) {
				// Set the page
				$data['page'] = $ai1ec_settings->settings_page;
				$script_to_load = 'admin_settings.js';
			}
			if( $script_to_load !== FALSE ) {
				$ai1ec_view_helper->admin_enqueue_script( 'ai1ec_add_new_event_require', "pages/$script_to_load", array( 'ai1ec_requirejs' ), true );
			}
		}
		// Load the config module. Loading it before the common back_end script assure us that it's available for all the other scripts.
		$this->localize_script_for_requirejs( 'ai1ec_common_backend' , 'ai1ec_config', $data);
		$this->load_require_js_config();
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
					show_coordinates  tinyint(1),
					latitude          decimal(20,15),
					longitude         decimal(20,15),
					facebook_eid      bigint(20),
					facebook_user     bigint(20),
					facebook_status   varchar(1) NOT NULL DEFAULT '',
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
	 *  This function scans the connector plugins directory and adds the plugin to the plugin helper
	 *
	 */
	function install_plugins() {
		global $ai1ec_importer_plugin_helper;

		// Scan the plugin directory for php files
		foreach ( glob( AI1EC_IMPORT_PLUGIN_PATH . "/*.php" ) as $filename ) {
			// Require the file
			require_once $filename;
			// The class name should be the same as the php file
			$class_name = str_replace( '.php', '', $filename );
			$class_name = str_replace( AI1EC_IMPORT_PLUGIN_PATH . '/', '', $class_name );
			// If the class exist
			if ( class_exists( $class_name ) &&  is_subclass_of( $class_name, 'Ai1ec_Connector_Plugin' ) ) {
				// Instantiate a new object and add it as a plugin. In the constructor the plugin will add his hooks.
				$ai1ec_importer_plugin_helper->add_plugin( new $class_name() );
			}
		}
		$ai1ec_importer_plugin_helper->sort_plugins();
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
			wp_clear_scheduled_hook( 'ai1ec_n_cron' );

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
	 * install_u_cron function
	 *
	 * This function sets up the cron job that checks for available updates
	 *
	 * @return void
	 **/
	function install_u_cron() {
		// If existing CRON version is not consistent with current plugin's version,
		// or does not exist, then create/update cron using
		if( get_option( 'ai1ec_u_cron_version' ) != AI1EC_U_CRON_VERSION ) {
			// delete our scheduled crons
			wp_clear_scheduled_hook( 'ai1ec_u_cron' );
			// reset flags
			update_option( 'ai1ec_update_available', 0 );
			update_option( 'ai1ec_update_message', '' );
			update_option( 'ai1ec_package_url', '' );
			// set the new cron
			wp_schedule_event( time(), AI1EC_U_CRON_FREQ, 'ai1ec_u_cron' );
			// update the cron version
			update_option( 'ai1ec_u_cron_version', AI1EC_U_CRON_VERSION );
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

		// ===============
		// = Themes Page =
		// ===============
		$themes_page = add_submenu_page(
			'themes.php',
			__( 'Calendar Themes', AI1EC_PLUGIN_NAME ),
			__( 'Calendar Themes', AI1EC_PLUGIN_NAME ),
			'switch_ai1ec_themes',
			AI1EC_PLUGIN_NAME . '-themes',
			array( &$ai1ec_themes_controller, 'view' )
		);

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
				'edit.php?post_type=' . AI1EC_POST_TYPE . '',
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
		// This is needed to load the correct javascript
		$is_calendar_page = FALSE;

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
				// Tell the javascript loader to load the js for the calendar
				$is_calendar_page = TRUE;
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
						case "posterboard":
							$_REQUEST["action"] = "ai1ec_posterboard";
							break;
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
						$cid = get_term_by( "name", trim( $c ), "events_categories" );
						if( $cid !== false ) {
							// if term was found, include it
							$_REQUEST["ai1ec_cat_ids"] = isset( $_REQUEST["ai1ec_cat_ids"] ) ?
							                             $_REQUEST["ai1ec_cat_ids"] . $cid->term_id . ',' :
							                             $cid->term_id . ',';
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
						$tid = get_term_by( "name", trim( $t ), "events_tags" );
						if( $tid !== false ) {
							// if term was found, include it
							$_REQUEST["ai1ec_tag_ids"] = isset( $_REQUEST["ai1ec_tag_ids"] ) ?
							                             $_REQUEST["ai1ec_tag_ids"] . $tid->term_id . ',' :
							                             $tid->term_id . ',';
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
				// Tell the javascript loader to load the js for the calendar
				$is_calendar_page = TRUE;
			}
		}
		// Load the correct javascript
		do_action( 'ai1ec_load_frontend_js', $is_calendar_page );
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
				'<div id="ai1ec-container" class="ai1ec-container timely">' .
				$content . $this->page_content .
				'</div>';

		return $content;
	}

	/**
	 * Defines a simple module that can be later imported by require js. Useful for translations and so on.
	 *
	 * @param string $handle The script handle that was registered or used in script-loader
	 * @param string $object_name Name for the created requirejs module. This is passed directly so it should be qualified JS variable /[a-zA-Z0-9_]+/
	 * @param array $l10n Associative PHP array containing the translated strings. HTML entities will be converted and the array will be JSON encoded.
	 * @param boolean $frontend Whether the localization is for frontend scripts or backend. Used only in wordpress < 3.3
	 * @return bool Whether the localization was added successfully.
	 */
	function localize_script_for_requirejs( $handle, $object_name, $l10n, $frontend = false ) {
		global $wp_scripts;
		if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
			if ( ! did_action( 'init' ) )
				_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
						'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>init</code>' ), '3.3' );
			return false;
		}
		foreach ( (array) $l10n as $key => $value ) {
			if ( !is_scalar($value) )
				continue;

			$l10n[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8');
		}
		$json_data = json_encode( $l10n );
		$script = "define( '$object_name', $json_data );";
		// Check if the get_data method exist
		if( method_exists( $wp_scripts, 'get_data' ) ) {
			// we are >= 3.3
			$data = $wp_scripts->get_data( $handle, 'data' );
			
			if ( !empty( $data ) )
				$script = "$data\n$script";
			return $wp_scripts->add_data( $handle, 'data', $script );
		} else {
			// we are < 3.3
			$script_to_print = '';
			$script_to_print .= "<script type='text/javascript'>\n";
			$script_to_print .= "/* <![CDATA[ */\n";
			$script_to_print .= $script;
			$script_to_print .= "/* ]]> */\n";
			$script_to_print .= "</script>\n";
			if( $frontend === true ) {
				$this->scripts_in_footer_frontend .= $script_to_print;
			} else {
				$this->scripts_in_footer .= $script_to_print;
			}
			return true;
		}
	}
	/**
	 * echo the scripts in the footer for the admin. Since this action is enqueued with priority 10
	 * this will happen before the script that requires javascript for the page is loaded as 
	 * scripts are loaded with priority = 20 and i need this script before that ( because that script use this as a dependency )
	 */
	public function print_admin_script_footer_for_wordpress_32() {
		echo $this->scripts_in_footer;
	}
	/**
	 * echo the scripts in the footer for the frontend. Since this action is enqueued with priority 10
	 * this will happen before the script that requires javascript for the page is loaded as
	 * scripts are loaded with priority = 20 and i need this script before that ( because that script use this as a dependency )
	 */
	public function print_frontend_script_footer_for_wordpress_32() {
		echo $this->scripts_in_footer_frontend;
	}
	/**
	 * Loads requirejs configuration before loading requirejs. This should assure that when we load the page script requirejs has a config
	 *
	 */
	private function load_require_js_config() {
		$handle = 'ai1ec_requirejs';
		global $wp_scripts;
		if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
			if ( ! did_action( 'init' ) )
				_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
						'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>init</code>' ), '3.3' );
			return false;
		}

		$script = <<<SCRIPT
var require = {
	waitSeconds : 15,
	paths : {
		"jquery" : "require_jquery"
	}
};
SCRIPT;
		if( method_exists( $wp_scripts, 'get_data' ) ) {
			$data = $wp_scripts->get_data( $handle, 'data' );
			
			if ( !empty( $data ) )
				$script = "$data\n$script";
			return $wp_scripts->add_data( $handle, 'data', $script );
		} else {
			// We are in version < 3.3. First of all do not echo this if we are in an ajax call ( otherwise it gets echoed by all calls )
			if( basename( $_SERVER['SCRIPT_NAME'] ) !== 'admin-ajax.php' ) {
				// otherwise echo this ASAP, i need this to be present before i load require.js as it's his config
				echo "<script type='text/javascript'>\n";
				echo "/* <![CDATA[ */\n";
				echo $script;
				echo "/* ]]> */\n";
				echo "</script>\n";
				return true;
			}
		}

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

		if(
			! isset( $_REQUEST["package"] ) ||
			empty( $_REQUEST["package"] ) ||
			! isset( $_REQUEST["plugin_name"] ) ||
			empty( $_REQUEST["plugin_name"] )
		) {
			wp_die( __( 'Download package is needed and was not supplied. Visit <a href="http://time.ly/" target="_blank">time.ly</a> to download the newest version of the plugin.' ) );
		}

		// use our custom class
		$upgrader = new Ai1ec_Updater();
		// update the plugin
		$upgrader->upgrade( $_REQUEST["plugin_name"], $_REQUEST["package"] );
		// clear update notification
		update_option( 'ai1ec_update_available', 0 );
		update_option( 'ai1ec_update_message', '' );
		update_option( 'ai1ec_package_url', '' );
		update_option( 'ai1ec_plugin_name', '' );
	}

	/**
	 * check_themes function
	 *
	 * This function checks if the user is not on install themes page
	 * and redirects the user to that page
	 *
	 * @return void
	 **/
	function check_themes() {
		if( ! isset( $_REQUEST["page"] ) || $_REQUEST["page"] != AI1EC_PLUGIN_NAME . '-install-themes' )
			wp_redirect( admin_url( AI1EC_INSTALL_THEMES_BASE_URL ) );
	}
}
// END class
