<?php
//
//  class-ai1ec-exporter-helper.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Exporter_Helper class
 *
 * @package Helpers
 * @author time.ly
 **/
class Ai1ec_Exporter_Helper {
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
	 * insert_event_in_calendar function
	 *
	 * Add event to the calendar
	 *
	 * @param object $event Event object
	 * @param object $c Calendar object
	 * @param bool $export States whether events are created for export
	 *
	 * @return void
	 **/
	function insert_event_in_calendar( $event, &$c, $export = false )
	{
		global $ai1ec_events_helper;

		$tz = get_option( 'timezone_string' );

		$e = & $c->newComponent( 'vevent' );
		$uid = $event->ical_uid ? $event->ical_uid : $event->post->guid;
		$e->setProperty( 'uid', $uid );
		$e->setProperty( 'url', get_permalink( $event->post_id ) );
		$e->setProperty( 'summary', html_entity_decode( apply_filters( 'the_title', $event->post->post_title ), ENT_QUOTES, 'UTF-8' ) );
		$content = apply_filters( 'the_content', $event->post->post_content );
		$content = str_replace(']]>', ']]&gt;', $content);
		$e->setProperty( 'description', html_entity_decode( $content, ENT_QUOTES, 'UTF-8' ) );
		if( $event->allday ) {
			$dtstart = $dtend = array();
			$dtstart["VALUE"] = $dtend["VALUE"] = 'DATE';
			// For exporting all day events, don't set a timezone
			if( $tz && !$export )
				$dtstart["TZID"] = $dtend["TZID"] = $tz;

			// For exporting all day events, only set the date not the time
			if( $export ) {
				$e->setProperty( 'dtstart', gmdate( "Ymd", $ai1ec_events_helper->gmt_to_local( $event->start ) ), $dtstart );
				$e->setProperty( 'dtend', gmdate( "Ymd", $ai1ec_events_helper->gmt_to_local( $event->end ) ), $dtend );
			} else {
				$e->setProperty( 'dtstart', gmdate( "Ymd\T", $ai1ec_events_helper->gmt_to_local( $event->start ) ), $dtstart );
				$e->setProperty( 'dtend', gmdate( "Ymd\T", $ai1ec_events_helper->gmt_to_local( $event->end ) ), $dtend );
			}
		} else {
			$dtstart = $dtend = array();
			if( $tz )
				$dtstart["TZID"] = $dtend["TZID"] = $tz;

			$e->setProperty( 'dtstart', gmdate( "Ymd\THis\Z", $ai1ec_events_helper->gmt_to_local( $event->start ) ), $dtstart );

			$e->setProperty( 'dtend', gmdate( "Ymd\THis\Z", $ai1ec_events_helper->gmt_to_local( $event->end ) ), $dtend );
		}
		$e->setProperty( 'location', $event->address );

		$contact = ! empty( $event->contact_name ) ? $event->contact_name : '';
		$contact .= ! empty( $event->contact_phone ) ? " ($event->contact_phone)" : '';
		$contact .= ! empty( $event->contact_email ) ? " <$event->contact_email>" : '';
		$e->setProperty( 'contact', $contact );

		$rrule = array();
		if( ! empty( $event->recurrence_rules ) ) {
			$rules = array();
			foreach( explode( ';', $event->recurrence_rules ) AS $v) {
				if( strpos( $v, '=' ) === false ) continue;

				list($k, $v) = explode( '=', $v );
				// If $v is a comma-separated list, turn it into array for iCalcreator
				switch( $k ) {
					case 'BYSECOND':
          case 'BYMINUTE':
          case 'BYHOUR':
          case 'BYDAY':
          case 'BYMONTHDAY':
          case 'BYYEARDAY':
          case 'BYWEEKNO':
          case 'BYMONTH':
          case 'BYSETPOS':
						$exploded = explode( ',', $v );
						break;
					default:
						$exploded = $v;
						break;
				}
				// iCalcreator requires a more complex array structure for BYDAY...
				if( $k == 'BYDAY' ) {
					$v = array();
					foreach( $exploded as $day ) {
						$v[] = array( 'DAY' => $day );
					}
				} else {
					$v = $exploded;
				}
				$rrule[ $k ] = $v;
			}
		}

		$exrule = array();
		if( ! empty( $event->exception_rules ) ) {
			$rules = array();
			foreach( explode( ';', $event->exception_rules ) AS $v) {
				if( strpos( $v, '=' ) === false ) continue;

				list($k, $v) = explode( '=', $v );
				// If $v is a comma-separated list, turn it into array for iCalcreator
				switch( $k ) {
					case 'BYSECOND':
          case 'BYMINUTE':
          case 'BYHOUR':
          case 'BYDAY':
          case 'BYMONTHDAY':
          case 'BYYEARDAY':
          case 'BYWEEKNO':
          case 'BYMONTH':
          case 'BYSETPOS':
						$exploded = explode( ',', $v );
						break;
					default:
						$exploded = $v;
						break;
				}
				// iCalcreator requires a more complex array structure for BYDAY...
				if( $k == 'BYDAY' ) {
					$v = array();
					foreach( $exploded as $day ) {
						$v[] = array( 'DAY' => $day );
					}
				} else {
					$v = $exploded;
				}
				$exrule[ $k ] = $v;
			}
		}

		// add rrule to exported calendar
		if( ! empty( $rrule ) )  $e->setProperty( 'rrule', $rrule );
		// add exrule to exported calendar
		if( ! empty( $exrule ) ) $e->setProperty( 'exrule', $exrule );
		// add exdates to exported calendar
		if( ! empty( $event->exception_dates ) )
			$e->setProperty( 'exdate', explode( ',', $event->exception_dates ) );
	}
}
