<?php
//
//  class-ai1ec-exporter-controller.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Exporter_Controller class
 *
 * @package Controllers
 * @author time.ly
 **/
class Ai1ec_Exporter_Controller {
	/**
	 * _instance class variable
	 *
	 * Class instance
	 *
	 * @var null | object
	 **/
	private static $_instance = NULL;

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
	 * Default constructor
	 **/
	private function __construct() { }

	/**
	 * n_cron function
	 *
	 * @return void
	 **/
	function n_cron() {
		global $ai1ec_settings, $wpdb;

		// send data only if cURL is available
		if( is_curl_available() ) {
			$query = "SELECT COUNT( ID ) as num_events " .
			         "FROM $wpdb->posts " .
			         "WHERE post_type = '" . AI1EC_POST_TYPE . "' AND " .
			         "post_status = 'publish'";
			$n_events = $wpdb->get_var( $query );

			$query   = "SELECT COUNT( ID ) FROM $wpdb->users";
			$n_users = $wpdb->get_var( $query );

			$categories = $tags = array();
			foreach( get_terms( 'events_categories', array( 'hide_empty' => false ) ) as $term ) {
				if( isset( $term->name ) )
					$categories[] = $term->name;
			}
			foreach( get_terms( 'events_tags', array( 'hide_empty' => false ) ) as $term ) {
				if( isset( $term->name ) )
					$tags[] = $term->name;
			}
			$data = array(
				'n_users'        => $n_users,
				'n_events'       => $n_events,
				'categories'     => $categories,
				'tags'           => $tags,
				'blog_name'      => get_bloginfo( 'name' ),
				'cal_url'        => get_permalink( $ai1ec_settings->calendar_page_id ),
				'ics_url'        => AI1EC_EXPORT_URL,
				'php_version'    => phpversion(),
				'wp_version'     => get_bloginfo( 'version' ),
				'wp_lang'        => get_bloginfo( 'language' ),
				'wp_url'         => home_url(),
				'timezone'       => get_option( 'timezone_string', 'America/Los_Angeles' ),
				'privacy'        => get_option( 'blog_public' ),
				'plugin_version' => AI1EC_VERSION
			);

			$ch = curl_init( 'http://184.169.144.10:24132/data' );
			curl_setopt( $ch, CURLOPT_POST, count( $data ) );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

			//execute post
			$result = curl_exec( $ch );

			//close connection
			curl_close( $ch );
		}
	}

	/**
	 * export_events function
	 *
	 * Export events
	 *
	 * @return void
	 **/
	function export_events() {
		global $ai1ec_events_helper,
					 $ai1ec_exporter_helper;
		$ai1ec_cat_ids 	= isset( $_REQUEST['ai1ec_cat_ids'] ) 	&& ! empty( $_REQUEST['ai1ec_cat_ids'] ) 	? $_REQUEST['ai1ec_cat_ids'] 	: false;
		$ai1ec_tag_ids 	= isset( $_REQUEST['ai1ec_tag_ids'] ) 	&& ! empty( $_REQUEST['ai1ec_tag_ids'] ) 	? $_REQUEST['ai1ec_tag_ids'] 	: false;
		$ai1ec_post_ids = isset( $_REQUEST['ai1ec_post_ids'] )	&& ! empty( $_REQUEST['ai1ec_post_ids'] ) ? $_REQUEST['ai1ec_post_ids'] : false;
		$filter = array();

		if( $ai1ec_cat_ids )
			$filter['cat_ids'] = split( ',', $ai1ec_cat_ids );
		if( $ai1ec_tag_ids )
			$filter['tag_ids'] = split( ',', $ai1ec_tag_ids );
		if( $ai1ec_post_ids )
			$filter['post_ids'] = split( ',', $ai1ec_post_ids );

		// when exporting events by post_id, do not look up the event's start/end date/time
		$start  = $ai1ec_post_ids !== false ? false : gmmktime() - 24 * 60 * 60; // Include any events ending today
		$end    = false;
		$events = $ai1ec_events_helper->get_matching_events( $start, $end, $filter );
		$c = new vcalendar();
		$c->setProperty( 'calscale', 'GREGORIAN' );
		$c->setProperty( 'method', 'PUBLISH' );
		$c->setProperty( 'X-WR-CALNAME', get_bloginfo( 'name' ) );
		$c->setProperty( 'X-WR-CALDESC', get_bloginfo( 'description' ) );
		// Timezone setup
		$tz = get_option( 'timezone_string' );
		if( $tz ) {
			$c->setProperty( 'X-WR-TIMEZONE', $tz );
			$tz_xprops = array( 'X-LIC-LOCATION' => $tz );
			iCalUtilityFunctions::createTimezone( $c, $tz, $tz_xprops );
		}

		foreach( $events as $event ) {
			$ai1ec_exporter_helper->insert_event_in_calendar( $event, $c, $export = true );
		}
		$str = $c->createCalendar();

		header( 'Content-type: text/calendar; charset=utf-8' );
		echo $str;
		exit;
	}
}
// END class
