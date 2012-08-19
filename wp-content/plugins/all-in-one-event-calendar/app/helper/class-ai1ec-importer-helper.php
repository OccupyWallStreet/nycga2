<?php
//
//  class-ai1ec-importer-helper.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Importer_Helper class
 *
 * @package Helpers
 * @author time.ly
 **/
class Ai1ec_Importer_Helper {
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
	 * time_array_to_timestamp function
	 *
	 * Converts time array to time string.
	 * Passed array: Array( 'year', 'month', 'day', ['hour', 'min', 'sec', ['tz']] )
	 * Return int: UNIX timestamp in GMT
	 *
	 * @param array $t iCalcreator's time property array (*full* format expected)
	 * @param string $def_timezone Default time zone in case not defined in $t
	 *
	 * @return int UNIX timestamp
	 **/
	function time_array_to_timestamp( $t, $def_timezone ) {
		$ret = $t['value']['year'] .
			'-' . $t['value']['month'] .
			'-' . $t['value']['day'];
		if( isset( $t['value']['hour'] ) )
			$ret .= ' ' . $t['value']['hour'] .
				':' . $t['value']['min'] .
				':' . $t['value']['sec'];
		$timezone = '';
		if( isset( $t['value']['tz'] ) && $t['value']['tz'] == 'Z' )
			$timezone = 'Z';
		elseif( isset( $t['params']['TZID'] ) )
			$timezone = $t['params']['TZID'];

		if( empty( $timezone ) ) $timezone = $def_timezone;
		if( $timezone )
			$ret .= ' ' . $timezone;
		return strtotime( $ret );
	}

	/**
	 * Gets and parses an iCalendar feed into an array of Ai1ec_Event objects
	 *
	 * @param object $feed Row from the ai1ec_event_feeds table
	 *
	 * @return int Number of events imported
	 */
	function parse_ics_feed( &$feed )
	{
		global $ai1ec_events_helper;

		$count = 0;

		// include ical parser
		if( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
			// Parser that requires PHP v5.3.0 or up
			require_once( AI1EC_LIB_PATH . '/iCalcreator-2.12/iCalcreator.class.php' );
		} else {
			// Parser that works on PHP versions below 5.3.0
			require_once( AI1EC_LIB_PATH . '/iCalcreator-2.10/iCalcreator.class.php' );
			require_once( AI1EC_LIB_PATH . '/iCalcreator-2.10/iCalUtilityFunctions.class.php' );
		}

		// set unique id, required if any component UID is missing
		$config = array( 'unique_id' => 'ai1ec' );

		// create new instance
		$v = new vcalendar( array(
			'unique_id' => $feed->feed_url,
			'url' => $feed->feed_url,
		) );

		// actual parse of the feed
		if( $v->parse() )
		{
			$v->sort();
			// Reverse the sort order, so that RECURRENCE-IDs are listed before the
			// defining recurrence events, and therefore take precedence during
			// caching.
			$v->components = array_reverse( $v->components );

			// TODO: select only VEVENT components that occur after, say, 1 month ago.
			// Maybe use $v->selectComponents(), which takes into account recurrence

			// Fetch default timezone in case individual properties don't define it
			$timezone = $v->getProperty( 'X-WR-TIMEZONE' );
			$timezone = $timezone[1];

			// go over each event
			while( $e = $v->getComponent( 'vevent' ) )
			{
				$start = $e->getProperty( 'dtstart', 1, true );
				$end   = $e->getProperty( 'dtend', 1, true );
				// For cases where a "VEVENT" calendar component
				// specifies a "DTSTART" property with a DATE value type but no
				// "DTEND" nor "DURATION" property, the event's duration is taken to
				// be one day.  For cases where a "VEVENT" calendar component
				// specifies a "DTSTART" property with a DATE-TIME value type but no
				// "DTEND" property, the event ends on the same calendar date and
				// time of day specified by the "DTSTART" property.
				if( empty( $end ) )  {
					// #1 if duration is present, assign it to end time
					$end = $e->getProperty( 'duration', 1, true, true );
					if( empty( $end ) ) {
						// #2 if only DATE value is set for start, set duration to 1 day
						if( ! isset( $start['value']['hour'] ) ) {
							$end = array(
								'year'  => $start['value']['year'],
								'month' => $start['value']['month'],
								'day'   => $start['value']['day'],
								'hour'  => 23,
								'min'   => 59,
								'sec'   => 59,
								'tz'    => $start['value']['tz']
							);
						} else {
							// #3 set end date to start time
							$end = $start;
						}
					}
				}

				// Event is all-day if no time components are defined
				$allday = ! isset( $start['value']['hour'] );

				// convert times to GMT UNIX timestamps
				$start = $this->time_array_to_timestamp( $start, $timezone );
				$end   = $this->time_array_to_timestamp( $end, $timezone );

				// If all-day, and start and end times are equal, then this event has
				// invalid end time (happens sometimes with poorly implemented iCalendar
				// exports, such as in The Event Calendar), so set end time to 1 day
				// after start time.
				if( $allday && $start === $end )
					$end += 24 * 60 * 60;

				// Due to potential time zone differences (WP time zone vs. feed time
				// zone), must convert all-day event start/end dates to date only (the
				// *intended* local date, non-GMT-ified)
				if( $allday ) {
					$start = $ai1ec_events_helper->gmt_to_local( $start );
					$start = $ai1ec_events_helper->gmgetdate( $start );
					$start = gmmktime( 0, 0, 0, $start['mon'], $start['mday'], $start['year'] );
					$start = $ai1ec_events_helper->local_to_gmt( $start );
					$end = $ai1ec_events_helper->gmt_to_local( $end );
					$end = $ai1ec_events_helper->gmgetdate( $end );
					$end = gmmktime( 0, 0, 0, $end['mon'], $end['mday'], $end['year'] );
					$end = $ai1ec_events_helper->local_to_gmt( $end );
				}

				if( $rrule = $e->createRrule() )
					$rrule = trim( end( explode( ':', $rrule ) ) );
				if( $exrule = $e->createExrule() )
					$exrule = trim( end( explode( ':', $exrule ) ) );
				if( $rdate = $e->createRdate() )
					$rdate = trim( end( explode( ':', $rdate ) ) );
				if( $exdate = $e->createExdate() )
					$exdate = trim( end( explode( ':', $exdate ) ) );

				$data = array(
					'start' 						=> $start,
					'end'								=> $end,
					'allday' 						=> $allday,
					'recurrence_rules'	=> $rrule,
					'exception_rules'		=> $exrule,
					'recurrence_dates'	=> $rdate,
					'exception_dates' 	=> $exdate,
					'venue' 						=> $e->getProperty( 'location' ),
					'ical_feed_url' 		=> $feed->feed_url,
					'ical_source_url' 	=> $e->getProperty( 'url' ),
					'ical_organizer' 		=> $e->getProperty( 'organizer' ),
					'ical_contact' 			=> $e->getProperty( 'contact' ),
					'ical_uid'          => $e->getProperty( 'uid' ),
					'categories'				=> $feed->feed_category,
					'tags'							=> $feed->feed_tags,
					'feed'              => $feed,
					'post'							=> array(
						'post_status'		=> 'publish',
						'post_type'			=> AI1EC_POST_TYPE,
						'post_author'		=> 1,
						'post_title'		=> $e->getProperty( 'summary' ),
						'post_content'	=> stripslashes( str_replace( '\n', "\n", $e->getProperty( 'description' ) ) ),
					),
				);

				$event = new Ai1ec_Event( $data );

				// TODO: when singular events change their times in an ICS feed from one
				// import to another, the matching_event_id is null, which is wrong. We
				// want to match that event that previously had a different time.
				// However, we also want the function to NOT return a matching event in
				// the case of recurring events, and different events with different
				// RECURRENCE-IDs... ponder how to solve this.. may require saving the
				// RECURRENCE-ID as another field in the database.
				$matching_event_id = $ai1ec_events_helper->get_matching_event_id(
					$event->ical_uid,
					$event->ical_feed_url,
					$event->start,
					! empty( $event->recurrence_rules )
				);

				if( is_null( $matching_event_id ) )
				{
					// =================================================
					// = Event was not found, so store it and the post =
					// =================================================
					$event->save();
				}
				else
				{
					// ======================================================
					// = Event was found, let's store the new event details =
					// ======================================================

					// Update the post
					$post               = get_post( $matching_event_id );
					$post->post_title   = $event->post->post_title;
					$post->post_content = $event->post->post_content;
					wp_update_post( $post );

					// Update the event
					$event->post_id = $matching_event_id;
					$event->post    = $post;
					$event->save( true );

					// Delete event's cache
					$ai1ec_events_helper->delete_event_cache( $matching_event_id );
				}

				// Regenerate event's cache
				$ai1ec_events_helper->cache_event( $event );

				$count++;
			}
		}

		return $count;
	}
}
// END class
