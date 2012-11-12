<?php
//
//  class-ai1ec-settings-controller.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Settings_Controller class
 *
 * @package Controllers
 * @author time.ly
 **/
class Ai1ec_Settings_Controller {
	/**
	 * _instance class variable
	 *
	 * Class instance
	 *
	 * @var null | object
	 **/
	private static $_instance = NULL;

	/**
	 * Constructor
	 *
	 * Default constructor
	 **/
	private function __construct() { }

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
	 * Display this plugin's settings page in the admin.
	 *
	 * @return void
	 */
	function view_settings() {
		global $ai1ec_view_helper,
		       $ai1ec_settings;

		if( isset( $_REQUEST['ai1ec_save_settings'] ) ) {
			$this->save( 'settings' );
		}
		$args = array(
			'title'             => __( 'All-in-One Event Calendar: Settings', AI1EC_PLUGIN_NAME ),
			'settings_page'     => $ai1ec_settings->settings_page,
			'calendar_settings' => TRUE,
		);
		$ai1ec_view_helper->display_admin( 'settings.php', $args );
	}

	/**
	 * Display this plugin's feeds page in the admin.
	 *
	 * @return void
	 */
	function view_feeds() {
		global $ai1ec_view_helper,
		       $ai1ec_settings,
		       $ai1ec_importer_plugin_helper;


		// Let the plugins handle POST
		$ai1ec_importer_plugin_helper->handle_feeds_page_post();
		$args = array(
			'title'             => __( 'All-in-One Event Calendar: Calendar Feeds', AI1EC_PLUGIN_NAME ),
			'settings_page'     => $ai1ec_settings->feeds_page,
			'calendar_settings' => FALSE,
		);
		$ai1ec_view_helper->display_admin( 'settings.php', $args );
	}

	/**
	 * Save the submitted settings form.
	 *
	 * @param string $settings_page Which settings page is being saved.
	 * @return void
	 */
	function save( $settings_page ) {
		global $ai1ec_settings,
		       $ai1ec_view_helper,
		       $ai1ec_importer_plugin_helper;

		$ai1ec_settings->update( $settings_page, $_REQUEST );
		do_action( 'ai1ec_save_settings', $settings_page, $_REQUEST );
		$ai1ec_settings->save();
		do_action( 'ai1ec-post-save-facebook-login' );

		$error_messages = $ai1ec_importer_plugin_helper->are_there_any_errors_to_show_on_calendar_settings_page();
		if( ! empty( $error_messages ) ) {
			$args = array(
					'msgs' => $error_messages
			);
			$ai1ec_view_helper->display_admin( "save_error.php", $args );
		}
		$args = array(
			'msg' => __( 'Settings Updated.', AI1EC_PLUGIN_NAME )
		);
		$ai1ec_view_helper->display_admin( "save_successful.php", $args );
	}

	/**
	 * Disable data notification (AJAX callback).
	 *
	 * @return void
	 **/
	function disable_notification() {
		global $ai1ec_view_helper, $ai1ec_settings;

		$ai1ec_settings->update_notification( false );
		$output = array(
			'error' 	=> false,
			'message'	=> 'Request successful.'
		);

		$ai1ec_view_helper->json_response( $output );
	}

	/**
	 * Disable intro video (AJAX callback).
	 *
	 * @return void
	 **/
	function disable_intro_video() {
		global $ai1ec_view_helper, $ai1ec_settings;

		$ai1ec_settings->update_intro_video( false );
		$output = array(
			'error' 	=> false,
			'message'	=> 'Request successful.'
		);

		$ai1ec_view_helper->json_response( $output );
	}

	/**
	 * Add meta boxes to settings screen.
	 *
	 * @return void
	 */
	function add_settings_meta_boxes() {
		global $ai1ec_settings_helper,
               $ai1ec_settings,
               $ai1ec_importer_plugin_helper;

		// Add the 'General Settings' meta box.
		add_meta_box(
			'ai1ec-general-settings',
			_x( 'General Settings', 'meta box', AI1EC_PLUGIN_NAME ),
			array( &$ai1ec_settings_helper, 'general_settings_meta_box' ),
			$ai1ec_settings->settings_page,
			'left-side',
			'default'
		);
		// Add the 'Advanced Settings' meta box.
		add_meta_box(
			'ai1ec-advanced-settings',
			_x( 'Advanced Settings', 'meta box', AI1EC_PLUGIN_NAME ),
			array( &$ai1ec_settings_helper, 'advanced_settings_meta_box' ),
			$ai1ec_settings->settings_page,
			'left-side',
			'default'
		);

		if( $ai1ec_importer_plugin_helper->is_settings_meta_box_required() ) {
			// Let the plugin helper add it's meta box.
			add_meta_box(
					'ai1ec-plugins-settings',
					_x( 'Connection Settings', 'meta box', AI1EC_PLUGIN_NAME ),
					array( &$ai1ec_importer_plugin_helper, 'plugins_settings_meta_box' ),
					$ai1ec_settings->settings_page,
					'left-side',
					'default'
			);
		}

		// Add the 'Timely Support' meta box.
		add_meta_box(
			'ai1ec-support',
			_x( 'Timely Support', 'meta box', AI1EC_PLUGIN_NAME ),
			array( &$ai1ec_settings_helper, 'support_meta_box' ),
			$ai1ec_settings->settings_page,
			'right-side',
			'default'
		);
	}

	/**
	 * Add meta boxes to feeds screen.
	 *
	 * @return void
	 */
	function add_feeds_meta_boxes() {
		global $ai1ec_settings_helper,
					 $ai1ec_settings;

		// Add the 'ICS Import Settings' meta box.
		add_meta_box(
			'ai1ec-feeds',
			_x( 'Feed Subscriptions', 'meta box', AI1EC_PLUGIN_NAME ),
			array( &$ai1ec_settings_helper, 'feeds_meta_box' ),
			$ai1ec_settings->feeds_page,
			'left-side',
			'default'
		);
		// Add the 'Timely Support' meta box.
		add_meta_box(
			'ai1ec-support',
			_x( 'Timely Support', 'meta box', AI1EC_PLUGIN_NAME ),
			array( &$ai1ec_settings_helper, 'support_meta_box' ),
			$ai1ec_settings->feeds_page,
			'right-side',
			'default'
		);
	}

	/**
	 * plugin_action_links function
	 *
	 * Adds a link to Settings page in plugin list page.
	 *
	 * @return array
	 **/
	function plugin_action_links( $links ) {
		$settings = sprintf( __( '<a href="%s">Settings</a>', AI1EC_PLUGIN_NAME ), admin_url( AI1EC_SETTINGS_BASE_URL ) );
		array_unshift( $links, $settings );
		return $links;
	}

	/**
	 * plugin_row_meta function
	 *
	 *
	 *
	 * @return void
	 **/
	function plugin_row_meta( $links, $file ) {
		if( $file == AI1EC_PLUGIN_BASENAME ) {
			$links[] = sprintf( __( '<a href="%s" target="_blank">Donate</a>', AI1EC_PLUGIN_NAME ), 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9JJMUW48W2ED8' );
			$links[] = sprintf( __( '<a href="%s" target="_blank">Get Support</a>', AI1EC_PLUGIN_NAME ), 'http://help.time.ly/' );
		}

		return $links;
	}

	/**
	 * u_cron function
	 *
	 * @return void
	 **/
	function u_cron() {
		update_option( 'ai1ec_update_available', 0 );
		update_option( 'ai1ec_update_message', '' );
		update_option( 'ai1ec_package_url', '' );
		// get current version
		$response = wp_remote_get( AI1EC_UPDATES_URL );
		if( ! is_wp_error( $response )             &&
		    isset( $response['response'] )         &&
		    isset( $response['response']['code'] ) &&
		    $response['response']['code'] == 200   &&
		    isset( $response['body'] )             &&
		    ! empty( $response['body'] ) ) {

			// continue only if there is a result
			$updater = json_decode( $response['body'] );
			$new = isset( $updater->version ) ? $updater->version : AI1EC_VERSION;
			$new = trim( str_replace( '-PREMIUM', '', strtoupper( $new ) ) );
			$old = str_replace( '-PREMIUM', '', strtoupper( AI1EC_VERSION ) );
			$old = str_replace( ' PREMIUM', '', $old );
			$old = trim( str_replace( 'PREMIUM', '', $old ) );
			if( ( version_compare( $old, $new ) == -1 ) ) {
				update_option( 'ai1ec_update_available', 1 );
				update_option( 'ai1ec_update_message', $updater->message );
				update_option( 'ai1ec_package_url', $updater->package );
				update_option( 'ai1ec_plugin_name', $updater->plugin_name );
			}
		}
	}
}
// END class
