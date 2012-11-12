<?php
/**
 *
 * @author time.ly
 *
 * This class extend the base abstract class and handles all the task for setting-up the environment, handling ajax rquests, POST requests and rendering the appropriate html
 *
 */
class Ai1ecFacebookConnectorPlugin extends Ai1ec_Connector_Plugin {
	// The path of the Facebook SDK
	const FB_SDK_PATH             = '/facebook-php-sdk/class-facebook-wp.php';
	const FB_APP_ID               = 'facebook-app-id';
	const FB_APP_SECRET           = 'facebook-app-secret';
	const FB_TOKEN                = 'facebook-token';
	const FB_VALID_APP_ID         = 'valid-app-id';
	const FB_LOGGED_TIMEZONE      = 'facebook-logged-timezone';
	// The permissions requred for the app.
	const FB_SCOPE                          = 'user_events,friends_events,user_groups,manage_pages,create_event';
	// The name of the plugin table
	const FB_DB_TABLE                       = 'ai1ec_facebook_users';
	// The variable in session where we store the user.
	const FB_USER_SESSION                   = 'ai1ec_facebook_user';
	// The plugin cron version
	const FB_CRON_VERSION                   = 100;
	// The name of the CRON for pages
	const FB_CRON_PAGES                     = 'ai1ec_facebook_cron_pages';
	// The name of the CRON for users
	const FB_CRON_USERS                     = 'ai1ec_facebook_cron_users';
	// The name of the CRON for groups
	const FB_CRON_GROUPS                    = 'ai1ec_facebook_cron_groups';
	// The name of the CRON for pages
	const FB_CRON_EVENTS                    = 'ai1ec_facebook_cron_events';
	// CRON frequency
	const FB_CRON_FREQUENCY                 = 'twicedaily';
	// Option where saving the message that will be shown in the notice.
	const FB_OPTION_CRON_NOTICE             = 'ai1ec_facebook_admin_notice';
	// Option where saving the CRON version.
	const FB_OPTION_CRON_VERSION            = 'ai1ec_facebook_cron_version';
	// Option where saving the DB version
	const FB_OPTION_DB_VERSION              = 'ai1ec_facebook_db_version';
	// The error code for an Auth exception
	const FB_OAUTH_EXC_CODE                 = 190;
	// The plugin DB version
	const FB_DB_VERSION                     = 100;
	// The id of the checkbox that the user must check to export the PSOT to Facebook
	const FB_EXPORT_CHKBX_NAME              = 'ai1ec_facebook_export';
	// The value for the facebook_status column when an event is exported to facebook
	const FB_EXPORTED_EVENT                 = 'E';
	// The value for the facebook_status column when an event is imported from facebook
	const FB_IMPORTED_EVENT                 = 'I';
	// The value when an event is neither imported nor exported to Facebook
	const FB_STANDARD_EVENT                 = '';
	// THe name of the hidden field we set when we choose to delete an event we exported to FB
	const FB_HIDDEN_INPUT_REMOVE_EVENT_NAME = 'ai1ec-remove-event';
	// THe name of the hidden field we set in the add new event page when we have no active ticket.
	const FB_HIDDEN_INPUT_NO_ACTIVE_TOKEN   = 'ai1ec-no-active-token';
	// The name of the submit input that the facebook modal triggers to submit the form
	const FB_SUBMIT_MODAL_NAME              = 'ai1ec_facebook_modal_submit';
	// The info variable for this class
	const FB_INFO_VARIABLE                  = '<a href="http://help.time.ly/customer/portal/articles/616553-how-do-i-use-the-facebook-import-" target="_blank">How do I use Facebook import?</a>';
	// The FB_APP_ID description text
	const FB_APP_ID_DESCRIPTION_TEXT        = "Enter your Facebook App ID:";
	// The FB_APP_SECRET description text
	const FB_APP_SECRET_DESCRIPTION_TEXT    = "Enter your Facebook App Secret:";
	/**
	 * @var array $settings An Associative array that holds the settings for the plugin. this array is persisted in Ai1ec_Settings
	 *   facebook-app-id: The Facebook app-id assigned from Facebook
	 *   facebook-app-secret: The Facebook app secret assigned from Facebook
	 *   facebook-token: The token that Facebook returns after authorizing the app
	 *   valid-app-id: an app id that has validate succesfully
	 *   facebook-logged-timezone: the timezone offset of the current user
	 */
	protected $settings = array(
		array(
			"id"          => self::FB_APP_ID,
			"description" => self::FB_APP_ID_DESCRIPTION_TEXT,
			"admin-page"  => TRUE
		),
		array(
			"id"          => self::FB_APP_SECRET,
			"description" => self::FB_APP_SECRET_DESCRIPTION_TEXT,
			"admin-page"  => TRUE
		),
		array(
			"id"          => self::FB_TOKEN,
			"description" => "This is the token which is used internally.",
			"admin-page"  => FALSE
		),
		array(
			"id"          => self::FB_VALID_APP_ID,
			"description" => "This is an app ID that has been validated and is an app.",
			"admin-page"  => FALSE
		)
	);
	/**
	 *
	 * @var array
	 *   title: The title of the tab and the title of the configuration section
	 *   id: The id used in the generation of the tab
	 */
	protected $variables = array(
		"title" => "Facebook Feeds",
		"id"    => "facebook",
		"info"  => self::FB_INFO_VARIABLE
	);
	/**
	 * Stores messages that must be printed to the user.
	 *
	 * @var array
	 */
	private $_information_message = array();
	/**
	 * Stores any error messages that will be shown on top of the area.
	 *
	 * @var array
	 */
	private $_error_messages = array();
	/**
	 * If set to TRUE, try to perform a Login
	 *
	 * @var boolean
	 */
	private $do_a_facebook_login;
	/**
	 * An instance of the Facebook class. For cache purpose.
	 *
	 * @var Facebook
	 */
	private $facebook;
	/**
	 * Facebook has problems if you call getLoginUrl multiple times so i store the value here.
	 *
	 * @var string
	 */
	private $facebook_login_url;
	/**
	 *
	 * @var string
	 */
	public $error_message_after_validating_app_id_and_secret;
	/**
	 * In the constructor all action and filter hooks are declared as the constructor is called when the app initialize. We als handle the CROn and the DB Schema.
	 *
	 */
	public function __construct() {
		// Start the session if it's not started, the Plugin needs it.
		if ( ! session_id() ) {
			session_start();
		}
		// Set the AJAX action to dismiss the notice.
		add_action( 'wp_ajax_ai1ec_facebook_cron_dismiss'                , array( $this, 'dismiss_notice_ajax' ) );
		// Set the AJAX action to refresh Facebook Graph Objects
		add_action( 'wp_ajax_ai1ec_refresh_facebook_objects'             , array( $this, 'refresh_facebook_ajax' ) );
		// Set AJAX action to remove a subscribed user
		add_action( 'wp_ajax_ai1ec_remove_subscribed'                    , array( $this, 'remove_subscribed_ajax' ) );
		// Set AJAX action to refresh multiselect
		add_action( 'wp_ajax_ai1ec_refresh_multiselect'                  , array( $this, 'refresh_multiselect' ) );
		// Set AJAX action to refresh events
		add_action( 'wp_ajax_ai1ec_refresh_events'                       , array( $this, 'refresh_events_ajax' ) );
		// Add the "Export to facebook" widget.
		add_action( 'post_submitbox_misc_actions'                        , array( $this, 'render_export_box' ) );
		// Ad action to check if app-id / secret where changed
		add_action( 'ai1ec-Ai1ecFacebookConnectorPlugin-postsave-setting', array( $this, 'if_app_id_or_secret_changed_perform_logout' ), 10, 1 );
		// Add action to perform a login to Facebook if needed
		add_action( 'ai1ec-post-save-facebook-login'                     , array( $this, 'do_facebook_login_if_app_id_and_secret_were_changed' ) );
		//allow redirection, even if my theme starts to send output to the browser
		add_action('init'                                                , array( $this, 'start_output_buffering' ) );
		// I leave all add_action() call in one place since it's easier to understand and mantain
		$facebook_custom_bulk_action = Ai1ec_Facebook_Factory::get_facebook_custom_bulk_action_instance( $this );
		// Add the select to filter events in the "All events" page
		add_action( 'restrict_manage_posts'                              , array( $facebook_custom_bulk_action, 'facebook_filter_restrict_manage_posts' ) );
		// Add action to handle export to facebook
		add_action( 'load-edit.php'                                      , array( $facebook_custom_bulk_action , 'facebook_custom_bulk_action' ) );
		// Add action to filter data
		add_filter( 'posts_where'                                        , array( $facebook_custom_bulk_action , 'facebook_filter_posts_where' ) );
		// Install db
		$this->install_schema();
		// Install CRON
		$this->install_cron();
	}
	/**
	 * Start output buffering to allow redirection ( it's used maily by the automatic login )
	 */
	function start_output_buffering() {
		ob_start();
	}
	/**
	 * Login to facebook if the user changed app_id and secret and they are valid
	 *
	 */
	public function do_facebook_login_if_app_id_and_secret_were_changed() {
		if( $this->do_a_facebook_login === TRUE ) {
			unset( $this->facebook );
			$this->do_facebook_login();
		}
	}
	/**
	 * If the use has entered new settings for app-id or secret in the settings page, logout the user from fb.
	 *
	 * @param array $new_data
	 */
	public function if_app_id_or_secret_changed_perform_logout( array $old_data ) {
		if( $old_data['page'] === 'all-in-one-event-calendar-settings' ) {
			$new_data = $this->get_plugin_settings( get_class( $this ) );
			if( $this->has_app_id_or_secret_changed( $old_data, $new_data ) ) {
				$this->clear_facebook_data_from_session_and_db_and_disable_cron();
				if( $this->has_app_id_and_app_secret_not_empty( $new_data ) ) {
					$facebook_app = Ai1ec_Facebook_Factory::get_facebook_application_instance( $new_data[self::FB_APP_ID], $new_data[self::FB_APP_SECRET] );
					try {
						$facebook_app->get_back_an_access_token_from_facebook_for_the_app();
						$this->do_a_facebook_login = TRUE;
					}
					catch ( Ai1ec_Error_Validating_App_Id_And_Secret $e ) {
						$this->error_message_after_validating_app_id_and_secret = __( $e->getMessage(), AI1EC_PLUGIN_NAME );
						$this->reset_app_id_and_secret();
					}
					catch ( Exception $e ) {
						$this->error_message_after_validating_app_id_and_secret = __( $e->getMessage(), AI1EC_PLUGIN_NAME );
						$this->reset_app_id_and_secret();
					}
				}
			}
		}
	}
	/**
	 * Reset app id and secret if they are not valid.
	 *
	 */
	private function reset_app_id_and_secret() {
		$this->save_plugin_variable( self::FB_APP_ID, '' );
		$this->save_plugin_variable( self::FB_APP_SECRET, '' );
		// unset the facebook instance because it still has previou app id and secret
		unset( $this->facebook );
	}
	/**
	 * Check if we have both an app id and an app secret.
	 *
	 * @param array $new_data
	 *
	 * @return boolean
	 */
	private function has_app_id_and_app_secret_not_empty( array $new_data ) {
		return ! empty( $new_data[self::FB_APP_ID] ) && ! empty( $new_data[self::FB_APP_SECRET] );
	}
	/**
	 * Check if facebook app_id or secrets have been changed
	 *
	 * @param array $old_data
	 * @param array $new_data
	 * @return boolean
	 */
	private function has_app_id_or_secret_changed( array $old_data, array $new_data ) {
		return ( ( $old_data[self::FB_APP_ID] !== $new_data[self::FB_APP_ID] ) || ( $old_data[self::FB_APP_SECRET] !== $new_data[self::FB_APP_SECRET] ) );
	}
	/**
	 * Clears Facebook data from Session, clear the Access token and disable cron functions
	 *
	 */
	private function clear_facebook_data_from_session_and_db_and_disable_cron() {
		// Get an instance of the Facebook class
		$facebook = $this->facebook_instance_factory();
		// Destroy the session so that no Facebook data is held
		$facebook->destroySession();
		// Invalidate the Token
		$this->save_plugin_variable( self::FB_TOKEN, '' );
		// Delete the user saved in session
		unset( $_SESSION[self::FB_USER_SESSION] );
		// Disable the cron Functions
		$this->disable_cron_functions();
		// Delete the option that saves the cron version: when a new acces token is obtained the CRON will be set again
		delete_option( self::FB_OPTION_CRON_VERSION );
	}
	/**
	 * Handles all the required steps to install / update the schema
	 */
	private function install_schema() {
		// If existing DB version is not consistent with current plugin's version,
		// or does not exist, then create/update table structure using dbDelta().
		if( get_option( self::FB_OPTION_DB_VERSION ) != self::FB_DB_VERSION ) {
			$table_name = Ai1ec_Facebook_Factory::get_plugin_table();
			$sql = "CREATE TABLE $table_name (
			user_id      bigint(20) NOT NULL,
			user_name    varchar(255) NOT NULL,
			user_pic     varchar(255) NOT NULL,
			subscribed   tinyint(1) NOT NULL DEFAULT '0',
			type         varchar(255) NOT NULL,
			tag          varchar(255) NOT NULL DEFAULT '',
			category     int(11) NOT NULL DEFAULT '0',
			last_synced  timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (user_id)
			) DEFAULT CHARSET=utf8;";
			$table_users_events = Ai1ec_Facebook_Factory::get_user_events_table();
			$sql .= "CREATE TABLE $table_users_events (
			user_id      bigint(20) unsigned NOT NULL,
			eid          bigint(20) unsigned NOT NULL,
			start        datetime NOT NULL,
			PRIMARY KEY  (user_id,eid)
			) DEFAULT CHARSET=utf8;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			update_option( self::FB_OPTION_DB_VERSION, self::FB_DB_VERSION );
		}
	}
	/**
	 * Disables all cron functions. This is called when the users logs out or when the plugin is uninstalled.
	 */
	private function disable_cron_functions() {
		// delete our scheduled crons
		wp_clear_scheduled_hook( self::FB_CRON_GROUPS, array( Ai1ec_Facebook_Graph_Object_Collection::FB_GROUP ) );
		wp_clear_scheduled_hook( self::FB_CRON_PAGES, array( Ai1ec_Facebook_Graph_Object_Collection::FB_PAGE ) );
		wp_clear_scheduled_hook( self::FB_CRON_USERS, array( Ai1ec_Facebook_Graph_Object_Collection::FB_USER ) );
		wp_clear_scheduled_hook( self::FB_CRON_EVENTS );
	}
	/**
	 * (non-PHPdoc)
	 * @see Ai1ec_Connector_Plugin::run_uninstall_procedures()
	 */
	public function run_uninstall_procedures() {
		// delete our scheduled crons
		$this->disable_cron_functions();
		// Clean up opions
		delete_option( self::FB_OPTION_DB_VERSION );
		delete_option( self::FB_OPTION_CRON_VERSION );
		delete_option( self::FB_OPTION_CRON_NOTICE );
		// Delete tables
		global $wpdb;
		$plugin_table = Ai1ec_Facebook_Factory::get_plugin_table();
		$user_events_table = Ai1ec_Facebook_Factory::get_user_events_table();
		$wpdb->query( "DROP TABLE IF EXISTS $plugin_table" );
		$wpdb->query( "DROP TABLE IF EXISTS $user_events_table" );
	}
	/**
	 * Handles all required steps to install the CRON
	 */
	private function install_cron() {
		// Set CRON action for users
		add_action( self::FB_CRON_PAGES                     , array( &$this, 'do_facebook_sync_cron' ) );
		// Set CRON action for users
		add_action( self::FB_CRON_GROUPS                    , array( &$this, 'do_facebook_sync_cron' ) );
		// Set CRON action for users
		add_action( self::FB_CRON_USERS                     , array( &$this, 'do_facebook_sync_cron' ) );
		// Set the CRON for updating events
		add_action( self::FB_CRON_EVENTS                    , array( &$this, 'refresh_events_cron' ) );
		// Check if we have a token. This means that the user has logged into facebook, so start the cron
		$token = $this->get_plugin_variable( self::FB_TOKEN );
		// If existing CRON version is not consistent with current plugin's version,
		// or does not exist, then create/update cron
		if( $token && get_option( self::FB_OPTION_CRON_VERSION ) != self::FB_CRON_VERSION ) {
			// delete our scheduled crons
			wp_clear_scheduled_hook( self::FB_CRON_GROUPS, array( Ai1ec_Facebook_Graph_Object_Collection::FB_GROUP ) );
			wp_clear_scheduled_hook( self::FB_CRON_PAGES, array( Ai1ec_Facebook_Graph_Object_Collection::FB_PAGE ) );
			wp_clear_scheduled_hook( self::FB_CRON_USERS, array( Ai1ec_Facebook_Graph_Object_Collection::FB_USER ) );
			wp_clear_scheduled_hook( self::FB_CRON_EVENTS );
			// set the new crons ( use  current_time( 'timestamp' ) to be more consisted as suggest in the codex)
			// Set the CRON for pages
			wp_schedule_event( current_time( 'timestamp' ) + 1800, self::FB_CRON_FREQUENCY, self::FB_CRON_PAGES, array( Ai1ec_Facebook_Graph_Object_Collection::FB_PAGE ) );
			// Set the CRON for groups
			wp_schedule_event( current_time( 'timestamp' ) + 3600, self::FB_CRON_FREQUENCY, self::FB_CRON_GROUPS, array( Ai1ec_Facebook_Graph_Object_Collection::FB_GROUP ) );
			// Set the CRON for users
			wp_schedule_event( current_time( 'timestamp' ) + 5400, self::FB_CRON_FREQUENCY, self::FB_CRON_USERS, array( Ai1ec_Facebook_Graph_Object_Collection::FB_USER ) );
			// Set the CRON for events
			wp_schedule_event( current_time( 'timestamp' ), self::FB_CRON_FREQUENCY, self::FB_CRON_EVENTS );
			// update the cron version
			update_option( self::FB_OPTION_CRON_VERSION, self::FB_CRON_VERSION );
		}
	}
	/**
	 * This just delete the option when the user clicks on dismiss.
	 */
	public function dismiss_notice_ajax() {
		$response = array();
		$ok = delete_option( self::FB_OPTION_CRON_NOTICE );
		if( ! $ok ) {
			$response['message'] = __( "Something went wrong when deleting the option", AI1EC_PLUGIN_NAME );
		}
		echo json_encode( $response );
		die();
	}
	/**
	 * This is just a wrapper around the other function that allows me to return if the Token is not set
	 *
	 * @param string $type
	 */
	public function do_facebook_sync_cron( $type ) {
		// Set time limits so that the CRON doesn't stop
		@set_time_limit( 0 );
		@ini_set( 'memory_limit'  , '256M' );
		@ini_set( 'max_input_time', '-1' );
		// If there is no token, block the CRON.
		$token = $this->get_plugin_variable( self::FB_TOKEN );
		if( empty( $token ) ) {
			return;
		}
		// Call the standard function
		$this->do_facebook_sync( $type );
	}
	/**
	 * If at least one config option is set we need the settings meta box
	 */
	public function is_settings_meta_box_required() {
		return $this->at_least_one_config_field_is_set( $this->generate_settings_array_for_admin_view() );
	}
	/**
	 * Remove a Facebook graph object that the user had subscribed to. This also deletes the orphaned (not referred to by other subscribed users) events if the user choose to do so.
	 */
	public function remove_subscribed_ajax() {
		// Create the object that will be returned.
		$response = array(
			"errors" => FALSE,
			"id"     => $_POST['ai1ec_post_id'],
			"logged" => $_POST['ai1ec_logged_user'],
			"type"   =>  $_POST['type'],
		);
		if( isset( $_POST['ai1ec_post_id'] ) ) {
			try {
				$fgoc = Ai1ec_Facebook_Factory::get_facebook_graph_object_collection( (array) $_POST['ai1ec_post_id'] );
				$remove_events = $_POST['ai1ec_remove_events'] === 'true' ? TRUE : FALSE;
				// Try to unsubscribe.
				$how_many = $fgoc->update_subscription_status( FALSE, $remove_events );
				// Get an object
				$fgo = Ai1ec_Facebook_Factory::get_facebook_graph_object( $_POST['ai1ec_post_id'] );
				// No exception, prepare the message to return
				$user_name = $fgo->get_user_name();
				$response['message'] = sprintf( __( "You unsubscribed from %s. ", AI1EC_PLUGIN_NAME ), $user_name );
				if( $how_many > 0 ) {
					$response['message'] .= sprintf( _n( "%d event was deleted",  "%d events were deleted.",$how_many, AI1EC_PLUGIN_NAME ), $how_many );
				}
				// IF we are removing the logged user, update the session variable.
				if( $_POST['ai1ec_logged_user'] === 'true' ) {
					$current_user = $this->get_current_facebook_user_from_session();
					$current_user->set_subscribed( FALSE );
					$current_user->set_tag( '' );
					$current_user->set_category( '' );
					$this->save_current_facebook_user_in_session( $current_user );
				}
				// Get the data for the update of the multiselect
				$response['html'] = $this->refresh_multiselect( $_POST['type'] );
			} catch ( Ai1ec_Facebook_Db_Exception $e ) {
				$response['errors'] = TRUE;
				$error_messages = $e->get_error_messages();
				$response['error_message'] = $error_messages[0];
			}
		}
		echo( json_encode( $response ) );
		die();
	}
	/**
	 * Check if we have a valid access token. If we have, it saves the user in session.
	 *
	 * @return boolean TRUE if we have a valid token, FALSE if we don't
	 */
	public function check_if_we_have_a_valid_access_token() {
		if( $this->get_current_facebook_user_from_session() !== NULL ) {
			// If we have a seesion, we use it
			return TRUE;
		} else {
			$facebook = $this->facebook_instance_factory();
			$current_user = Ai1ec_Facebook_Factory::get_facebook_user_instance( $facebook );
			// Do login
			$logged_in = $current_user->do_login();
			if( $logged_in ) {
				$this->save_current_facebook_user_in_session( $current_user );
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see Ai1ec_Connector_Plugin::display_admin_notices()
	 */
	public function display_admin_notices(){
		// Let's check if the cron has set a message.
		$message = get_option( self::FB_OPTION_CRON_NOTICE, FALSE );
		if( $message === FALSE ) {
			return;
		}
		global $ai1ec_view_helper;
		$args = array(
				'label'        => $message['label'],
				'msg'          => $message['message'],
				'button'       => (object) array( 'class' => 'ai1ec-facebook-cron-dismiss-notification', 'value' => 'Dismiss' ),
				'message_type' => $message['message_type'],
		);
		$ai1ec_view_helper->display_admin( 'admin_notices.php', $args );
	}
	/**
	 * Sync the data of a multiselect with Facebook.
	 */
	public function refresh_facebook_ajax() {
		if ( isset( $_POST['ai1ec_type'] ) ) {
			$response = $this->do_facebook_sync( $_POST['ai1ec_type'] );
			if( $response['errors'] === FALSE ) {
				$facebook_tab = Ai1ec_Facebook_Factory::get_facebook_tab_instance();
				$facebook_user = $this->get_current_facebook_user_from_session();
				$response['html'] = $facebook_tab->render_multi_select( $_POST['ai1ec_type'], $facebook_user->get_id() );
				$response['type'] = $_POST['ai1ec_type'];
			}
			echo( json_encode( $response ) );
			die();
		}
	}
	/**
	 * Saves a value in the option so that a notice is visualized when there is an error
	 */
	private function set_error_message_for_cron( $exception_message = '' ) {
		$message = array(
			"label"        => __( 'All-in-One Event Calendar Facebook Import Notice', AI1EC_PLUGIN_NAME ),
			"message"      => __( "Something went wrong while syncing events from Facebook. Please check that your Access Token is still valid by visiting the <strong>Events</strong> &gt; <strong>Calendar Feeds</strong> screen.", AI1EC_PLUGIN_NAME ),
			"message_type" => "updated",
		);
		if( ! empty( $exception_message ) ) {
			$message["message"] .= "<br />" . __( "Facebook provided this error message: <em>$exception_message</em>", AI1EC_PLUGIN_NAME );
		}
		update_option( self::FB_OPTION_CRON_NOTICE, $message );
	}
	/**
	 * Private, used to log events to check if cron works
	 */
	private function log_number_of_events() {
		global $wpdb;
		$event_table = Ai1ec_Facebook_Factory::get_events_table();
		$event_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $event_table;" ) );
		error_log( "There are $event_count in the table $event_table" );
	}
	/**
	 * This is the function that is called by the cron and refreshes all the events of the user / groups /pages that are subscribed.
	 */
	public function refresh_events_cron() {
		@set_time_limit( 0 );
		@ini_set( 'memory_limit'  , '256M' );
		@ini_set( 'max_input_time', '-1' );
		// If there is no token, block the CRON.
		$token = $this->get_plugin_variable( self::FB_TOKEN );
		if( empty( $token ) ) {
			return;
		}
		$fgoc = Ai1ec_Facebook_Factory::get_facebook_graph_object_collection( array() );
		foreach( Ai1ec_Facebook_Graph_Object_Collection::$fb_all_types as $type ) {
			$fgoc->set_type( $type );
			$facebook = $this->facebook_instance_factory();
			// Set the facebook instance
			$fgoc->set_facebook( $facebook );
			// We don't have a session,get a new user
			$current_user = Ai1ec_Facebook_Factory::get_facebook_user_instance( $facebook );
			// Do login
			$logged_in = $current_user->do_login();
			if( $logged_in ) {
				$fgoc->set_facebook_user( $current_user );
			} else {
				$this->set_error_message_for_cron( $current_user->get_error_message() );
			}
			// Load the subcriber for the type and check that at least one subscriber is loaded
			$at_least_one_user = $fgoc->load_subscribers_for_type();
			if( $at_least_one_user === TRUE ) {
				try {
					$response = $fgoc->refresh_events();
				} catch ( WP_FacebookApiException $e ) {
					$this->set_error_message_for_cron( $e->getMessage() );
				}
			}
		}
	}
	/**
	 * Refreshes the events for the clicked user / group / page.
	 * 
	 * This function can become very slow if the official Facebook plugin is present.
	 * This is because Facebook attaches some very expensive call to the save_post action
	 * which we trigger recursively and time for a single wp_update_post or wp_save_post call
	 * goes up to 1.5 sec.
	 * Listed are the calls to remove if needs arise
	 * remove_action( 'save_post', 'fb_add_page_mention_box_save' );
	 * remove_action( 'save_post', 'fb_add_friend_mention_box_save' );
	 * 
	 */
	public function refresh_events_ajax() {
		$to_return = array();
		if( isset( $_POST['ai1ec_post_id'] ) ) {
			// Set the id
			$to_return['id'] = $_POST['ai1ec_post_id'];
			// Get a collection object
			$fgoc = Ai1ec_Facebook_Factory::get_facebook_graph_object_collection( array( $_POST['ai1ec_post_id'] ) );
			// Set the type. This loads the correct strategy object for querying events.
			$fgoc->set_type( $_POST['ai1ec_type'] );
			$facebook = $this->facebook_instance_factory();
			// Set the currently active user. This is neede to calculate starting times of events.
			$fgoc->set_facebook_user( $_SESSION[self::FB_USER_SESSION] );
			// Set the facebook instance
			$fgoc->set_facebook( $facebook );
			try {
				
				$response = $fgoc->refresh_events();
				$tab = Ai1ec_Facebook_Factory::get_facebook_tab_instance();
				// IF there are errors i show a warning message in the front-end, that say that something went wrong but not totally wrong.
				$to_return['errors'] = $response['errors'];
				$to_return['message'] = $tab->create_refresh_message( $response );
			}
			catch ( WP_FacebookApiException $e ) {
				$message = '';
				if( $e->getCode() === self::FB_OAUTH_EXC_CODE ) {
					$message = __( "Something went wrong with Facebook authentication, try to log out and then login again", AI1EC_PLUGIN_NAME );
					$facebook->destroySession();
				} else {
					$message = __( "Something went wrong when retrieving data from Facebook. Try subscribing to fewer users or try logging off and logging in again.", AI1EC_PLUGIN_NAME );
				}
				// An exception happened, set it so that an error alert can be visualized in the front-end.
				$to_return['exception'] = TRUE;
				$to_return['message'] = $message;
			}
		}
		echo json_encode( $to_return );
		die();
	}
	/**
	 * Logs out the user from Facebook
	 *
	 */
	private function do_facebook_logout() {
		$this->clear_facebook_data_from_session_and_db_and_disable_cron();
		// Get an instance of the Facebook class
		$facebook = $this->facebook_instance_factory();
		// Get the logout URL from the Facebook Class
		$logout = $facebook->getLogoutUrl();
		$this->facebook->setAccessToken('');
		// Redirect the user to the logout url, facebook will redirect him to our page
		wp_redirect( $logout );
	}
	/**
	 * Check if the plugin has been configured, i.e. the user has entered data in the settings page
	 *
	 * @return boolean TRUE if all settings have been set, FALSE otherwise
	 */
	private function is_plugin_configured() {
		// Get the plugin settings.
		$plugin_settings = $this->get_plugin_settings( get_class( $this ) );
		foreach ( $this->settings as $setting ) {
			// Check only settings that appear on the settings page
			if ( $setting['admin-page'] === TRUE && empty( $plugin_settings[$setting['id']] ) ) {
				return FALSE;
			}
		}
		return TRUE;
	}
	/**
	 * Checks if the user set a valid App Id in the settings page. If the id is valid, it's saved in the plugin settings, otherwise an appropriate error message is shown.
	 *
	 * @return boolean $valid Wheter a valid app id has been set or not
	 */
	private function has_valid_app_id() {
		$valid = TRUE;
		// Get plugin settings
		$plugin_settings = $this->get_plugin_settings( get_class( $this ) );
		// Let's check that app-id is a valid app id if we already did'n to this.
		// We also run this check if the user has changed the app_id in the settings page
		if ( empty( $plugin_settings[self::FB_VALID_APP_ID]) || ( (int) $plugin_settings[self::FB_VALID_APP_ID] !== (int) $plugin_settings[self::FB_APP_ID] ) ) {
			try {
				$this->is_valid_facebook_app_id( $plugin_settings[self::FB_APP_ID] );
				// If we arrive here, it's valid, let's save that as valid app id
				$this->save_plugin_settings( array( self::FB_VALID_APP_ID => $plugin_settings[self::FB_APP_ID] ), TRUE );
			}
			catch ( InvalidArgumentException $e ) {
				$this->render_error_page( $e->getMessage(), TRUE );
				$valid = FALSE;
			}
			catch ( Exception $e ) {
				// This is not the expected error so we log it
				$this->render_error_page( $e->getMessage(), TRUE );
				$valid = FALSE;
			}
		}
		return $valid;
	}
	/**
	 * Return an error message if set, FALSE otherwise
	 *
	 * @return mixed <boolean, string>
	 */
	public function are_there_any_errors_to_show_on_calendar_settings_page() {
		return isset( $this->error_message_after_validating_app_id_and_secret ) ? $this->error_message_after_validating_app_id_and_secret : FALSE;
	}
	/**
	 * Returns the currently logged on user from session if set
	 *
	 * @return Ai1ec_Facebook_Current_User or NULL if it is not set
	 */
	private function get_current_facebook_user_from_session() {
		return isset( $_SESSION[self::FB_USER_SESSION] ) ? $_SESSION[self::FB_USER_SESSION] : NULL;
	}
	/**
	 * Sets the user as the current user in session.
	 *
	 * @param Ai1ec_Facebook_Current_User $user
	 */
	private function save_current_facebook_user_in_session( Ai1ec_Facebook_Current_User $user ) {
		$_SESSION[self::FB_USER_SESSION] = $user;
	}
	/**
	 * (non-PHPdoc)
	 * @see Ai1ec_Connector_Plugin::render_tab_content()
	 */
	public function render_tab_content() {
		// Load CSS for the plugin
		$this->load_css();
		$this->render_opening_div_of_tab();

		$facebook = $this->facebook_instance_factory();
		$logged_in = FALSE;

		// Check if the user has been saved into the session.
		if( $this->get_current_facebook_user_from_session() !== NULL ) {
			$facebook_user = $this->get_current_facebook_user_from_session();
			$logged_in = TRUE;
		} else {
			$facebook_user = Ai1ec_Facebook_Factory::get_facebook_user_instance( $facebook );
			$logged_in = $facebook_user->do_login();
		}

		global $ai1ec_view_helper;

		// Login or logout url will be needed depending on current user state.
		if ( $logged_in ) {
			// We have a valid token, save it
			$this->save_plugin_variable( self::FB_TOKEN, $facebook_user->get_token() );
			// Save the current user in session
			$this->save_current_facebook_user_in_session( $facebook_user );
			// Create all multi selects
			$facebook_tab = Ai1ec_Facebook_Factory::get_facebook_tab_instance();
			// IF we have errors, set them in the class.
			if( isset( $this->_error_messages ) ) {
				$facebook_tab->set_error_messages( $this->_error_messages );
			}
			// If we have messages, set them in the class.
			if( isset( $this->_information_message ) ) {
				$facebook_tab->set_informational_messages( $this->_information_message );
			}
			// Render the tab.
			$facebook_tab->render_tab( $facebook_user, Ai1ec_Facebook_Graph_Object_Collection::$fb_all_types );
			// Close the div
			$this->render_closing_div_of_tab();
		} else {
			if( isset( $this->error_message_after_validating_app_id_and_secret ) ) {
				$this->render_error_page( $this->error_message_after_validating_app_id_and_secret );
			}
			$modal_html = '';
			$login_url = '#';
			$facebook_tab = Ai1ec_Facebook_Factory::get_facebook_tab_instance();
			$question_mark = $facebook_tab->render_question_mark_for_facebook();
			if( $this->at_least_one_config_field_is_set( $this->generate_settings_array_for_admin_view() ) ) {
				// Get login url.
				$login_url = $this->get_facebook_login_url();
			} else {

				$modal_html = $facebook_tab->render_modal_for_facebook_app_id_and_secret_and_return_html();
			}
			$args = array(
				'login_url'     => $login_url,
				'modal_html'    => $modal_html,
				'submit_name'   => self::FB_SUBMIT_MODAL_NAME,
				'question_mark' => $question_mark,
			);
			$ai1ec_view_helper->display_admin( 'plugins/facebook/user_login.php', $args );
			$this->render_closing_div_of_tab();
		}
	}
	/**
	 * Enqueues styles for the plugin
	 */
	private function load_css() {
		global $ai1ec_view_helper;
		$ai1ec_view_helper->admin_enqueue_style( 'ai1ec-facebook', 'plugins/facebook.css' );
	}
	/**
	 * Returns the facebook login url. It's cached to avoid invalidating the CRSF token
	 *
	 */
	private function get_facebook_login_url() {
		if( ! isset( $this->facebook_login_url ) ) {
			$facebook = $this->facebook_instance_factory();
			$params = array(
					'scope' => self::FB_SCOPE,
			);
			$this->facebook_login_url = $facebook->getLoginUrl( $params );
		}
		return $this->facebook_login_url;
	}
	/**
	 * Returns an instance of the facebook class. The token is added if present.
	 *
	 * @return Facebook_WP_Extend_Ai1ec
	 */
	public function facebook_instance_factory() {
		if( ! isset( $this->facebook ) ) {
			require_once AI1EC_LIB_PATH . self::FB_SDK_PATH;
			// Get plugin settings.
			$plugin_settings = $this->get_plugin_settings( get_class( $this ) );

			// Create our Application instance.
			$facebook = new Facebook_WP_Extend_Ai1ec( array(
					'appId'  => $plugin_settings[self::FB_APP_ID],
					'secret' => $plugin_settings[self::FB_APP_SECRET],
			) );

			// Retrieve the token from the configuration.
			$token = $this->get_plugin_variable( 'facebook-token' );

			// If the token was set, use it.
			if( $token ) {
				$facebook->setAccessToken( $token );
			}
			$this->facebook = $facebook;
		}
		return $this->facebook;
	}
	/**
	 * Refresh a multiselect through AJAX. This is called after a succesful remove.
	 *
	 */
	private function refresh_multiselect( $type ) {
		$facebook_tab = Ai1ec_Facebook_Factory::get_facebook_tab_instance();
		$facebook_user = $this->get_current_facebook_user_from_session();
		return $facebook_tab->render_multi_select( $type, $facebook_user->get_id() );
	}
	/**
	 * Fetches data from Facebook for the specified type and then updates the DB
	 *
	 * @param string $type the facebook graph object type to sync
	 *
	 * @return array
	 */
	public function do_facebook_sync( $type ) {
		$response = array(
			"errors"         => false,
			"error_messages" => array(),
		);

		$fgoc = Ai1ec_Facebook_Factory::get_facebook_graph_object_collection( array() );
		// Set the an instance of the Facebook class
		$facebook = $this->facebook_instance_factory();
		$fgoc->set_facebook( $facebook );
		// Set the currently active user.
		if( $this->get_current_facebook_user_from_session() !== NULL ) {
			// If we have a seesion, we use it
			$fgoc->set_facebook_user( $this->get_current_facebook_user_from_session() );
		} else {
			// We don't have a session, this is probably the cron. get a new user
			$current_user = Ai1ec_Facebook_Factory::get_facebook_user_instance( $facebook );
			// Do login
			$logged_in = $current_user->do_login();
			if( $logged_in ) {
				$fgoc->set_facebook_user( $current_user );
			} else {
				$this->set_error_message_for_cron( $current_user->get_error_message() );
			}
		}
		// Set the type of the collection.
		$fgoc->set_type( $type );
		try {
			$fgoc->sync_facebook_users();
			$response['message'] = sprintf( __( "Fetching data for %s has completed succesfully", AI1EC_PLUGIN_NAME ),
					                         Ai1ec_Facebook_Graph_Object_Collection::get_type_printable_text( $type ) );
		}
		catch ( WP_FacebookApiException $e ) {
			// The first FQL query failed and nothing has changed
			$response['errors'] = TRUE;
			$response['error_messages'][] = $e->getMessage();
		}
		catch ( Ai1ec_Facebook_Friends_Sync_Exception $e ) {
			// The first query failed but we have something to return
			$response['errors'] = TRUE;
			$response['error_messages'] = array_merge( $response['error_messages'], $e->get_error_messages() );
		}
		return $response;
	}
	/**
	 * (non-PHPdoc)
	 * @see Ai1ec_Connector_Plugin::handle_feeds_page_post()
	 */
	public function handle_feeds_page_post() {
		// Get tag and category here as they are needed in more than one function.
		$category = isset( $_POST['ai1ec_facebook_feed_category'] ) ? $_POST['ai1ec_facebook_feed_category'] : '';
		$tag      = isset( $_POST['ai1ec_facebook_feed_tags'] ) ? $_POST['ai1ec_facebook_feed_tags'] : '';
		// Handle when the user wants to subscribe to his events
		if( isset( $_POST['ai1ec_facebook_subscribe_yours'] ) ) {
			// Get the user from session
			$facebook_user = $this->get_current_facebook_user_from_session();
			// Get the id of the user
			$current_user_id = $facebook_user->get_id();
			// SUbscribe to it
			$this->subscribe_users( array( $current_user_id ), Ai1ec_Facebook_Graph_Object_Collection::FB_USER, $category, $tag );
			// Set the user as subscribed
			$facebook_user->set_subscribed( TRUE );
			// Set tag and category for the current user
			$facebook_user->set_category( $category );
			$facebook_user->set_tag( $tag );
			// Save the user back into the Session
			$this->save_current_facebook_user_in_session( $facebook_user );
		}
		// Handle when the user logs out
		if( isset( $_POST['ai1ec_logout_facebook'] ) ) {
			$this->do_facebook_logout();
		}
		// Handle when the user subscribe other users.
		if( isset( $_POST['ai1ec_subscribe_users'] ) ) {
			foreach( Ai1ec_Facebook_Graph_Object_Collection::$fb_all_types as $type ) {
				$name = Ai1ec_Facebook_Tab::FB_MULTISELECT_NAME . $type;
				if ( isset( $_POST[$name] ) ) {
					$this->subscribe_users( $_POST[$name], $type, $category, $tag );
				}
			}
		}
		// Handle when saving app_id_and secret from the modal
		if( isset( $_POST[self::FB_SUBMIT_MODAL_NAME] ) ) {
			$app_id = $_POST['ai1ec_facebook_app_id_modal'];
			$secret = $_POST['ai1ec_facebook_app_secret_modal'];
			$facebook_app = Ai1ec_Facebook_Factory::get_facebook_application_instance( $app_id, $secret );
			try {
				$facebook_app->get_back_an_access_token_from_facebook_for_the_app();
				$this->update_app_id_and_secret( $app_id, $secret );
				$this->do_facebook_login();
			}
			catch ( Ai1ec_Error_Validating_App_Id_And_Secret $e ) {
				$this->error_message_after_validating_app_id_and_secret = __( $e->getMessage(), AI1EC_PLUGIN_NAME );
			}
			catch ( Exception $e ) {
				$this->error_message_after_validating_app_id_and_secret =  __( $e->getMessage(), AI1EC_PLUGIN_NAME );
			}
		}
	}

	/**
	 * Save app id and secret and unset Facebook instance
	 *
	 * @param string $app_id
	 * @param string $secret
	 */

	private function update_app_id_and_secret( $app_id, $secret ) {
		$this->save_plugin_variable( self::FB_APP_ID , $app_id );
		$this->save_plugin_variable( self::FB_APP_SECRET , $secret );
		// unset the facebook instance because it still has previou app id and secret
		unset( $this->facebook );
	}

	/**
	 * Try to login the user to Facebook
	 *
	 */
	private function do_facebook_login() {
		$login_url = $this->get_facebook_login_url();
		wp_redirect( $login_url );
	}
	/**
	 * Subscribe the passed users and fetches from Facebook their events. The events and the users are tagged with the relative category and tag.
	 *
	 * @param array $users the users to subscribe to
	 *
	 * @param string $type the type of the users array (user / page / group)
	 *
	 * @param int $category the category
	 *
	 * @param string $tag the tags
	 */
	private function subscribe_users( array $users, $type, $category, $tag ) {
		// Get a collection object
		$fgoc = Ai1ec_Facebook_Factory::get_facebook_graph_object_collection( $users );
		// Set the category (needed to attach to the users, not to the events)
		$fgoc->set_category( $category );
		// Set the tags (needed to attach to the users, not to the events)
		$fgoc->set_tag( $tag );
		// Set the an instance of the Facebook class
		$facebook = $this->facebook_instance_factory();
		$fgoc->set_facebook( $facebook );
		// Set the currently active user.
		$fgoc->set_facebook_user( $this->get_current_facebook_user_from_session() );
		// Set the type of the collection.
		$fgoc->set_type( $type );
		try {
			// Update the subscription status.
			$fgoc->update_subscription_status( TRUE, $category, $tag );
			$this->_information_message[] = $fgoc->refresh_events();
		} catch ( Ai1ec_Facebook_Db_Exception $e ) {
			$this->_error_messages = array_merge( $this->_error_messages, $e->get_error_messages() );
		} catch ( WP_FacebookApiException $e ) {
			if ( $e->getCode() === self::FB_OAUTH_EXC_CODE ) {
				$message = __( "Something went wrong with Facebook authentication. Try to log out and then log in again.", AI1EC_PLUGIN_NAME );
				$this->_error_messages = array_merge( $this->_error_messages, array( $message ) );
				$facebook->destroySession();
			} else {
				$message = __( "Something went wrong when retrieving data from Facebook. Try subscribing to fewer calendars.", AI1EC_PLUGIN_NAME );
				$this->_error_messages = array_merge( $this->_error_messages, array( $message ) );
			}
		}
	}
	/**
	 * Creates the HTML to display on the "Add new event" page so that the user can choose to export the event to Facebook.
	 *
	 * @return string an empty string or the html to show
	 */
	public function render_export_box() {
		global $post;
		// We only want this for events.
		if( $post->post_type !== AI1EC_POST_TYPE ) {
			return;
		}
		try {
			$event = new Ai1ec_Event( $post->ID );
		} catch( Ai1ec_Event_Not_Found $e ) {
			// Post exists, but event data hasn't been saved yet. Create new event
			// object.
			$event = NULL;
		}
		// If we have an event end the event was imported from facebook, return, we can't export it.
		if( $event !== NULL && $event->facebook_status === Ai1ecFacebookConnectorPlugin::FB_IMPORTED_EVENT ) {
			return;
		}
		// Let's check if we have a user in session
		if( $this->get_current_facebook_user_from_session() === NULL ) {
			// No user in session, let's see if we have a token and the user can login.
			$facebook = $this->facebook_instance_factory();
			$current_user = Ai1ec_Facebook_Factory::get_facebook_user_instance( $facebook );
			$logged_in = $current_user->do_login();
			// If the user couldn't login, do not print anything but return an hidden input, in this way the
			// plugin save post handler simply returns.
			if( $logged_in === FALSE ) {
				$hidden_input_name = self::FB_HIDDEN_INPUT_NO_ACTIVE_TOKEN;
				echo "<input type='hidden' name='$hidden_input_name' value='1' />";
				return;
			}
			// Save it in session.
			$this->save_current_facebook_user_in_session( $current_user );
		}
		$checked = '';
		if( $event !== NULL && $event->facebook_status === Ai1ecFacebookConnectorPlugin::FB_EXPORTED_EVENT ) {
			$checked = 'checked';
		}
		$link = '';
		$modal_html = '';
		if( isset( $event->facebook_eid ) && ( int ) $event->facebook_eid !== 0 ) {
			$link_label = __( "Linked Facebook event", AI1EC_PLUGIN_NAME );
			$link = "<div id='ai1ec-facebook-linked-event'><a href='https://www.facebook.com/events/{$event->facebook_eid}'>$link_label</a></div>";
			// We include the modal only when the event has been exported to facebook
			$twitter_bootstrap_modal = new Ai1ec_Twitter_Bootstrap_Modal( __( "Would you like to delete the linked Facebook event or keep it? If you choose to keep it and later you export this event again, a new one will be created.", AI1EC_PLUGIN_NAME ) );
			$twitter_bootstrap_modal->set_header_text( __( "Unpublish Facebook event?", AI1EC_PLUGIN_NAME ) );
			$twitter_bootstrap_modal->set_id( "ai1ec-facebook-export-modal" );
			$twitter_bootstrap_modal->set_delete_button_text( __( "Delete event", AI1EC_PLUGIN_NAME ) );
			$twitter_bootstrap_modal->set_keep_button_text( __( "Keep event", AI1EC_PLUGIN_NAME ) );
			$modal_html = $twitter_bootstrap_modal->render_modal_and_return_html();
		}
		$label     = __( 'Export event to Facebook?', AI1EC_PLUGIN_NAME );
		$name      = self::FB_EXPORT_CHKBX_NAME;

		$html = <<<HTML
<div id="ai1ec-facebook-publish" class="misc-pub-section">
	<div id="ai1ec-facebook-small-logo"></div>
	<label for="$name">
		$label
	</label>
	<input type="checkbox" $checked name="$name" id="$name" value="1" />
	$link
</div>
<div class="timely">
$modal_html
</div>
HTML;
		echo $html;
	}
	/**
	 * Return a Facebook instance if we have a valid access token
	 *
	 * @throws Ai1ec_No_Valid_Facebook_Access_Token
	 * @return Facebook
	 */
	private function get_facebook_instance_if_there_is_a_valid_access_token() {
		if( $this->check_if_we_have_a_valid_access_token() ) {
			$facebook = $this->facebook_instance_factory();
			return $facebook;
		} else {
			$this->set_error_message_for_no_valid_access_token();
			throw new Ai1ec_No_Valid_Facebook_Access_Token();
		}
	}
	/**
	 * Set an error message because no valid access token was found
	 *
	 */
	private function set_error_message_for_no_valid_access_token() {
		$message = array(
				"label"        => __( 'All-in-One Event Calendar Facebook Import Error', AI1EC_PLUGIN_NAME ),
				"message"      => __( "We couldn't synchronize data with Facebook as we don't have a valid access token. Try to log out of Facebook from the <strong>Events</strong> &gt; <strong>Calendar Feeds</strong> screen, then log in again.", AI1EC_PLUGIN_NAME ),
				"message_type" => "error",
		);
		update_option( Ai1ecFacebookConnectorPlugin::FB_OPTION_CRON_NOTICE, $message );
	}
	/**
	 * If the checkbox for exporting events to facebook is checked, export the event to facebook
	 *
	 * @param Ai1ec_Event $event a referemce to the event which is being saved. We need a refernce as we are going to modify it
	 */
	public function handle_save_event( Ai1ec_Event &$event ) {
		// If the hidden input that states that no active token is present was set, just return;
		if( isset( $_POST[self::FB_HIDDEN_INPUT_NO_ACTIVE_TOKEN] ) ) {
			return;
		}
		// If the checkbox is not set, reset eid and status
		if( ! isset( $_POST[self::FB_EXPORT_CHKBX_NAME] ) ) {
			$facebook_eid = $event->facebook_eid;
			$event->facebook_eid = 0;
			$event->facebook_status = self::FB_STANDARD_EVENT;
			$event->facebook_user = 0;
			// If the hidden field was added, delete the event from Facebook
			if( isset( $_POST[self::FB_HIDDEN_INPUT_REMOVE_EVENT_NAME] ) ) {
				try {
					$facebook = $this->get_facebook_instance_if_there_is_a_valid_access_token();
					$facebook_event = Ai1ec_Facebook_Factory::get_facebook_event_instance();
					$facebook_event->set_id( $facebook_eid );
					$facebook_event->delete_from_facebook( $facebook );
				} catch ( Ai1ec_No_Valid_Facebook_Access_Token $e ) {
					// The message is set automatically
				}
			}
			return;
		}
		// Check if we have a valid access token, otherwise there is no sense in going on.
		try {
			$facebook = $this->get_facebook_instance_if_there_is_a_valid_access_token();
			$facebook_event = Ai1ec_Facebook_Factory::get_facebook_event_instance();
			$facebook_event->populate_event_from_post_and_ai1ec_event( $_POST, $event );
			$result = $facebook_event->save_to_facebook( $facebook );
			if( is_array( $result )  && ! empty( $result ) ) {
				$event->facebook_eid = $result['id'];
				$event->facebook_status = self::FB_EXPORTED_EVENT;
			}
		} catch ( Ai1ec_No_Valid_Facebook_Access_Token $e ) {
			// The message is set automatically
		}
	}
	/**
	 * This function is called when the user deletes a calendar event and we must delete the linked facebook event
	 *
	 * @param Ai1ec_Event $event
	 */
	public function handle_delete_event( Ai1ec_Event $event ) {
		if( isset( $event->facebook_eid ) &&
			( int ) $event->facebook_eid !== 0 &&
			$event->facebook_status !== self::FB_IMPORTED_EVENT ) {
			if( $this->check_if_we_have_a_valid_access_token() ) {
				$facebook_event = Ai1ec_Facebook_Factory::get_facebook_event_instance();
				$facebook_event->set_id( $event->facebook_eid );
				$facebook = $this->facebook_instance_factory();
				$facebook_event->delete_from_facebook( $facebook );
			} else {
				$message = array(
						"label"        => __( 'All-in-One Event Calendar Facebook Import Error', AI1EC_PLUGIN_NAME ),
						"message"      => __( "We couldn't delete data from Facebook as we don't have a valid access token. Try to log out of Facebook from the <strong>Events</strong> &gt; <strong>Calendar Feeds</strong> screen, then log in again.", AI1EC_PLUGIN_NAME ),
						"message_type" => "error",
				);
				update_option( Ai1ecFacebookConnectorPlugin::FB_OPTION_CRON_NOTICE, $message );
			}
		}
	}
}

?>
