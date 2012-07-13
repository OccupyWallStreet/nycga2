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
 * @author The Seed Studio
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
			'title' => __( 'All-in-One Calendar: Settings', AI1EC_PLUGIN_NAME ),
			'settings_page' => $ai1ec_settings->settings_page,
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
		       $ai1ec_settings;

		if( isset( $_REQUEST['ai1ec_save_settings'] ) ) {
			$this->save( 'feeds' );
		}
		$args = array(
			'title' => __( 'All-in-One Calendar: Calendar Feeds', AI1EC_PLUGIN_NAME ),
			'settings_page' => $ai1ec_settings->feeds_page,
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
		       $ai1ec_view_helper;

		$ai1ec_settings->update( $settings_page, $_REQUEST );
		do_action( 'ai1ec_save_settings', $settings_page, $_REQUEST );
		$ai1ec_settings->save();

		$args = array(
			'msg' => __( 'Settings Updated.', AI1EC_PLUGIN_NAME )
		);

		$ai1ec_view_helper->display_admin( "save_successful.php", $args );
	}

	/**
	 * add_ics_feed function
	 *
	 * Adds submitted ics feed to the database
	 *
	 * @return string JSON output
	 **/
	function add_ics_feed() {
		global $ai1ec_view_helper,
		       $wpdb;

		$table_name = $wpdb->prefix . 'ai1ec_event_feeds';

		$wpdb->insert(
			$table_name,
			array(
				'feed_url' 			=> $_REQUEST["feed_url"],    // convert webcal to http
				'feed_category' => $_REQUEST["feed_category"],
				'feed_tags'			=> $_REQUEST["feed_tags"],
			),
			array(
				'%s',
				'%d',
				'%s'
			)
		);
		$feed_id = $wpdb->insert_id;
		ob_start();
		$feed_category = get_term( $_REQUEST["feed_category"], 'events_categories' );
		$args = array(
			'feed_url' 			 => $_REQUEST["feed_url"],
			'event_category' => $feed_category->name,
			'tags'					 => $_REQUEST["feed_tags"],
			'feed_id'				 => $feed_id,
			'events'         => 0
		);
		// display added feed row
		$ai1ec_view_helper->display_admin( 'feed_row.php', $args );

		$output = ob_get_contents();
		ob_end_clean();

		$output = array(
			"error" 	=> 0,
			"message"	=> stripslashes( $output )
		);

		echo json_encode( $output );
		exit();
	}

	/**
	 * flush_ics_feed function
	 *
	 * Deletes all event posts that are from that selected feed
	 *
	 * @param bool $ajax When set to TRUE, the data is outputted using json_response
	 * @param bool|string $feed_url Feed URL
	 *
	 * @return void
	 **/
	function flush_ics_feed( $ajax = TRUE, $feed_url = FALSE ) {
		global $wpdb,
					 $ai1ec_view_helper;
		$ics_id = isset( $_REQUEST['ics_id'] ) ? (int) $_REQUEST['ics_id'] : 0;
		$table_name = $wpdb->prefix . 'ai1ec_event_feeds';

		if( $feed_url === FALSE ) {
			$feed_url = $wpdb->get_var( $wpdb->prepare( "SELECT feed_url FROM $table_name WHERE feed_id = %d", $ics_id ) );
		}

		if( $feed_url ) {
			$table_name = $wpdb->prefix . 'ai1ec_events';
			$sql = "SELECT post_id FROM {$table_name} WHERE ical_feed_url = '%s'";
			$events = $wpdb->get_results( $wpdb->prepare( $sql, $feed_url ) );
			$total = count( $events );

			foreach( $events as $event ) {
				// delete post (this will trigger deletion of cached events, and remove the event from events table)
				wp_delete_post( $event->post_id, 'true' );
			}

			$output = array(
				'error' 	=> FALSE,
				'message'	=> sprintf( __( 'Flushed %d events', AI1EC_PLUGIN_NAME ), $total ),
				'count'   => $total,
			);
		} else {
			$output = array(
				'error' 	=> TRUE,
				'message'	=> __( 'Invalid ICS feed ID', AI1EC_PLUGIN_NAME )
			);
		}

		if( $ajax ) {
			$ai1ec_view_helper->json_response( $output );
		}
	}

	/**
	 * update_ics_feed function
	 *
	 * Imports the selected iCalendar feed
	 *
	 * @return void
	 **/
	function update_ics_feed() {
		global $wpdb,
					 $ai1ec_view_helper,
					 $ai1ec_importer_helper;

		$feed_id = (int) $_REQUEST['ics_id'];
		$table_name = $wpdb->prefix . 'ai1ec_event_feeds';
		$feed = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE feed_id = %d", $feed_id ) );

		if( $feed ) {
			// flush the feed
			$this->flush_ics_feed( false, $feed->feed_url );
			// reimport the feed
			$count = @$ai1ec_importer_helper->parse_ics_feed( $feed );
			if ( $count == 0 ) {
				// If results are 0, it could be result of a bad URL or other error, send a specific message
				$output = array(
					'error' 	=> true,
					'message'	=> __( 'No events were found', AI1EC_PLUGIN_NAME )
				);
			} else {
				$output = array(
					'error'       => false,
					'message'     => sprintf( __( 'Imported %d events', AI1EC_PLUGIN_NAME ), $count ),
					'flush_label' => sprintf( _n( 'Flush 1 event', 'Flush %s events', $count, AI1EC_PLUGIN_NAME ), $count ),
					'count'       => $count,
				);
			}
		} else {
			$output = array(
				'error' 	=> true,
				'message'	=> __( 'Invalid ICS feed ID', AI1EC_PLUGIN_NAME )
			);
		}

		$ai1ec_view_helper->json_response( $output );
	}

	/**
	 * delete_ics_feed function
	 *
	 * Deletes submitted ics feed id from the database
	 *
	 * @return String JSON output
	 **/
	function delete_ics_feed() {
		global $wpdb,
					 $ai1ec_view_helper;

		$ics_id = (int) $_REQUEST['ics_id'];
		$table_name = $wpdb->prefix . 'ai1ec_event_feeds';
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$table_name} WHERE feed_id = %d", $ics_id ) );
		$output = array(
			'error' 	=> false,
			'message'	=> 'Request successful.'
		);

		$ai1ec_view_helper->json_response( $output );
	}

	/**
	 * disable_notification function
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
	 * Add meta boxes to settings screen.
	 *
	 * @return void
	 */
	function add_settings_meta_boxes() {
		global $ai1ec_settings_helper,
					 $ai1ec_settings;

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
		// Add the 'Then.ly Support' meta box.
		add_meta_box(
			'the-seed-studio-settings',
			_x( 'Then.ly Support', 'meta box', AI1EC_PLUGIN_NAME ),
			array( &$ai1ec_settings_helper, 'the_seed_studio_meta_box' ),
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
		// Add the 'Then.ly Support' meta box.
		add_meta_box(
			'the-seed-studio-settings',
			_x( 'Then.ly Support', 'meta box', AI1EC_PLUGIN_NAME ),
			array( &$ai1ec_settings_helper, 'the_seed_studio_meta_box' ),
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
			$links[] = sprintf( __( '<a href="%s" target="_blank">Get Support</a>', AI1EC_PLUGIN_NAME ), 'http://help.then.ly' );
		}

		return $links;
	}

}
// END class
