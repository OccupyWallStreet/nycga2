<?php
//
//  class-ai1ec-calendar-helper.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Calendar_Helper class
 *
 * @package Helpers
 * @author time.ly
 **/
class Ai1ec_Calendar_Helper {
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
	 * get_events_for_month function
	 *
	 * Return an array of all dates for the given month as an associative
	 * array, with each element's value being another array of event objects
	 * representing the events occuring on that date.
	 *
	 * @param int $time         the UNIX timestamp of a date within the desired month
	 * @param array $filter     Array of filters for the events returned:
	 *                          ['cat_ids']   => non-associatative array of category IDs
	 *                          ['tag_ids']   => non-associatative array of tag IDs
	 *                          ['post_ids']  => non-associatative array of post IDs
	 *
	 * @return array            array of arrays as per function description
	 **/
	function get_events_for_month( $time, $filter = array() )
	{
		global $ai1ec_events_helper;

		$days_events = array();

		$bits = $ai1ec_events_helper->gmgetdate( $time );
		$last_day = gmdate( 't', $time );

		$start_time = $the_first = gmmktime( 0, 0, 0, $bits['mon'], 1, $bits['year'] );
		$end_time   = $the_last = gmmktime( 0, 0, 0, $bits['mon'], $last_day + 1, $bits['year'] );

		$month_events = $this->get_events_between( $start_time, $end_time, $filter, TRUE );

		// ==========================================
		// = Iterate through each date of the month =
		// ==========================================
		for ( $day = 1; $day <= $last_day; $day++ ) {
			$start_time = gmmktime( 0, 0, 0, $bits['mon'], $day, $bits['year'] );
			$end_time = gmmktime( 0, 0, 0, $bits['mon'], $day + 1, $bits['year'] );

			// Itemize events that fall under the current day
			$_events = array();
			$_allday_events = array();
			$_multiday_events = array();
			foreach ( $month_events as $event ) {
				$event_start = $ai1ec_events_helper->gmt_to_local( $event->start );
				$event_end = $ai1ec_events_helper->gmt_to_local( $event->end );
				// Add this event if:
				// 1. we are populating the 1st & this event starts before the 1st, or
				// 2. this event starts on the currently populated day
				if ( $day == 1 && $event_start < $the_first ||
				     $event_start >= $start_time && $event_start < $end_time ) {
					// Set multiday properties. TODO: Should these be made event object
					// properties? They probably shouldn't be saved to the DB, so I'm not
					// sure. Just creating properties dynamically for now.
					if ( $event_start < $the_first ) {
						$event->start_truncated = TRUE;
					}
					if ( $event_end >= $the_last ) {
						$event->end_truncated = TRUE;
					}
					// Categorize event.
					if ( $event->allday ) {
						$_allday_events[] = $event;
					}
					elseif ( $event->multiday ) {
						$_multiday_events[] = $event;
					}
					else {
						$_events[] = $event;
					}
				}
			}

			$days_events[$day] = array_merge( $_multiday_events, $_allday_events, $_events );
		}

		return apply_filters( 'ai1ec_get_events_for_month', $days_events, $time, $filter );
	}

	/**
	 * get_month_cell_array function
	 *
	 * Return an array of weeks, each containing an array of days, each
	 * containing the date for the day ['date'] (if inside the month) and
	 * the events ['events'] (if any) for the day, and a boolean ['today']
	 * indicating whether that day is today.
	 *
	 * @param int $timestamp	    UNIX timestamp of the 1st day of the desired
	 *                            month to display
	 * @param array $days_events  list of events for each day of the month in
	 *                            the format returned by get_events_for_month()
	 *
	 * @return void
	 **/
	function get_month_cell_array( $timestamp, $days_events )
	{
		global $ai1ec_settings, $ai1ec_events_helper;

		// Decompose date into components, used for calculations below
		$bits = $ai1ec_events_helper->gmgetdate( $timestamp );
		$today = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );	// Used to flag today's cell

		// Figure out index of first table cell
		$first_cell_index = gmdate( 'w', $timestamp );
		// Modify weekday based on start of week setting
		$first_cell_index = ( 7 + $first_cell_index - $ai1ec_settings->week_start_day ) % 7;

		// Get the last day of the month
		$last_day = gmdate( 't', $timestamp );
		$last_timestamp = gmmktime( 0, 0, 0, $bits['mon'], $last_day, $bits['year'] );
		// Figure out index of last table cell
		$last_cell_index = gmdate( 'w', $last_timestamp );
		// Modify weekday based on start of week setting
		$last_cell_index = ( 7 + $last_cell_index - $ai1ec_settings->week_start_day ) % 7;

		$weeks = array();
		$week = 0;
		$weeks[$week] = array();

		// Insert any needed blank cells into first week
		for( $i = 0; $i < $first_cell_index; $i++ ) {
			$weeks[$week][] = array( 'date' => null, 'events' => array() );
		}

		// Insert each month's day and associated events
		for( $i = 1; $i <= $last_day; $i++ ) {
			$weeks[$week][] = array(
				'date' => $i,
				'today' =>
					$bits['year'] == $today['year'] &&
					$bits['mon']  == $today['mon'] &&
					$i            == $today['mday'],
				'events' => $days_events[$i]
			);
			// If reached the end of the week, increment week
			if( count( $weeks[$week] ) == 7 )
				$week++;
		}

		// Insert any needed blank cells into last week
		for( $i = $last_cell_index + 1; $i < 7; $i++ ) {
			$weeks[$week][] = array( 'date' => null, 'events' => array() );
		}

		return $weeks;
	}

	/**
	 * get_week_cell_array function
	 *
	 * Return an associative array of weekdays, indexed by the day's date,
	 * starting the day given by $timestamp, each element an associative array
	 * containing three elements:
	 *   ['today']     => whether the day is today
	 *   ['allday']    => non-associative ordered array of events that are all-day
	 *   ['notallday'] => non-associative ordered array of non-all-day events to
	 *                    display for that day, each element another associative
	 *                    array like so:
	 *     ['top']       => how many minutes offset from the start of the day
	 *     ['height']    => how many minutes this event spans
	 *     ['indent']    => how much to indent this event to accommodate multiple
	 *                      events occurring at the same time (0, 1, 2, etc., to
	 *                      be multiplied by whatever desired px/em amount)
	 *     ['event']     => event data object
	 *
	 * @param int $timestamp    the UNIX timestamp of the first day of the week
	 * @param array $filter     Array of filters for the events returned:
	 *                          ['cat_ids']   => non-associatative array of category IDs
	 *                          ['tag_ids']   => non-associatative array of tag IDs
	 *                          ['post_ids']  => non-associatative array of post IDs
	 *
	 * @return array            array of arrays as per function description
	 **/
	function get_week_cell_array( $timestamp, $filter = array() )
	{
		global $ai1ec_events_helper, $ai1ec_settings;

		// Decompose given date and current time into components, used below
		$bits = $ai1ec_events_helper->gmgetdate( $timestamp );
		$now = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );

		// Do one SQL query to find all events for the week, including spanning
		$week_events = $this->get_events_between(
			$timestamp,
			gmmktime( 0, 0, 0, $bits['mon'], $bits['mday'] + 7, $bits['year'] ),
			$filter,
			true );

		// Split up events on a per-day basis
		$all_events = array();
		foreach( $week_events as $evt ) {
			$evt_start = $ai1ec_events_helper->gmt_to_local( $evt->start );
			$evt_end = $ai1ec_events_helper->gmt_to_local( $evt->end );

			// Iterate through each day of the week and generate new event object
			// based on this one for each day that it spans
			for( $day = $bits['mday']; $day < $bits['mday'] + 7; $day++ ) {
				$day_start = gmmktime( 0, 0, 0, $bits['mon'], $day, $bits['year'] );
				$day_end = gmmktime( 0, 0, 0, $bits['mon'], $day + 1, $bits['year'] );

				// If event falls on this day, make a copy.
				if( $evt_end > $day_start && $evt_start < $day_end ) {
					$_evt = clone $evt;
					if( $evt_start < $day_start ) {
						// If event starts before this day, adjust copy's start time
						$_evt->start = $ai1ec_events_helper->local_to_gmt( $day_start );
						$_evt->start_truncated = true;
					}
					if( $evt_end > $day_end ) {
						// If event ends after this day, adjust copy's end time
						$_evt->end = $ai1ec_events_helper->local_to_gmt( $day_end );
						$_evt->end_truncated = true;
					}

					// Place copy of event in appropriate category
					if( $_evt->allday || $_evt->multiday)
						$all_events[$day_start]['allday'][] = $_evt;
					else
						$all_events[$day_start]['notallday'][] = $_evt;
				}
			}
		}

		// This will store the returned array
		$days = array();
		// =========================================
		// = Iterate through each date of the week =
		// =========================================
		for( $day = $bits['mday']; $day < $bits['mday'] + 7; $day++ )
		{
			$day_date = gmmktime( 0, 0, 0, $bits['mon'], $day, $bits['year'] );
			// Re-fetch date bits, since $bits['mday'] + 7 might be in the next month
			$day_bits = $ai1ec_events_helper->gmgetdate( $day_date );

			// Initialize empty arrays for this day if no events to minimize warnings
			if( ! isset( $all_events[$day_date]['allday'] ) ) $all_events[$day_date]['allday'] = array();
			if( ! isset( $all_events[$day_date]['notallday'] ) ) $all_events[$day_date]['notallday'] = array();

			$notallday = array();
			$evt_stack = array( 0 ); // Stack to keep track of indentation
			foreach( $all_events[$day_date]['notallday'] as $evt )
			{
				$start_bits = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( $evt->start ) );

				// Calculate top and bottom edges of current event
				$top = $start_bits['hours'] * 60 + $start_bits['minutes'];
				$bottom = min( $top + $evt->getDuration() / 60, 1440 );

				// While there's more than one event in the stack and this event's top
				// position is beyond the last event's bottom, pop the stack
				while( count( $evt_stack ) > 1 && $top >= end( $evt_stack ) )
					array_pop( $evt_stack );
				// Indentation is number of stacked events minus 1
				$indent = count( $evt_stack ) - 1;
				// Push this event onto the top of the stack
				array_push( $evt_stack, $bottom );

				$notallday[] = array(
					'top'    => $top,
					'height' => $bottom - $top,
					'indent' => $indent,
					'event'  => $evt,
				);
			}

			$days[$day_date] = array(
				'today'     =>
					$day_bits['year'] == $now['year'] &&
					$day_bits['mon']  == $now['mon'] &&
					$day_bits['mday'] == $now['mday'],
				'allday'    => $all_events[$day_date]['allday'],
				'notallday' => $notallday,
			);
		}

		return apply_filters( 'ai1ec_get_week_cell_array', $days, $timestamp, $filter );
	}

	/**
	 * get_oneday_cell_array function
	 *
	 * Return an associative array of weekdays, indexed by the day's date,
	 * starting the day given by $timestamp, each element an associative array
	 * containing three elements:
	 *   ['today']     => whether the day is today
	 *   ['allday']    => non-associative ordered array of events that are all-day
	 *   ['notallday'] => non-associative ordered array of non-all-day events to
	 *                    display for that day, each element another associative
	 *                    array like so:
	 *     ['top']       => how many minutes offset from the start of the day
	 *     ['height']    => how many minutes this event spans
	 *     ['indent']    => how much to indent this event to accommodate multiple
	 *                      events occurring at the same time (0, 1, 2, etc., to
	 *                      be multiplied by whatever desired px/em amount)
	 *     ['event']     => event data object
	 *
	 * @param int $timestamp    the UNIX timestamp of the first day of the week
	 * @param array $filter     Array of filters for the events returned:
	 *                          ['cat_ids']   => non-associatative array of category IDs
	 *                          ['tag_ids']   => non-associatative array of tag IDs
	 *                          ['post_ids']  => non-associatative array of post IDs
	 *
	 * @return array            array of arrays as per function description
	 **/
	function get_oneday_cell_array( $timestamp, $filter = array() )
	{
		global $ai1ec_events_helper, $ai1ec_settings;

		// Decompose given date and current time into components, used below
		$bits = $ai1ec_events_helper->gmgetdate( $timestamp );
		$now  = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );
		$day_events = $this->get_events_between( $timestamp, gmmktime( 0, 0, 0, $bits['mon'], $bits['mday'] + 1, $bits['year'] ), $filter, true );

		// Split up events on a per-day basis
		$all_events = array();

		foreach( $day_events as $evt ) {
			$evt_start = $ai1ec_events_helper->gmt_to_local( $evt->start );
			$evt_end   = $ai1ec_events_helper->gmt_to_local( $evt->end );

			// generate new event object
			// based on this one day
			$day_start = gmmktime( 0, 0, 0, $bits['mon'], $bits['mday'], $bits['year'] );
			$day_end   = gmmktime( 0, 0, 0, $bits['mon'], $bits['mday']+1, $bits['year'] );

			// If event falls on this day, make a copy.
			if( $evt_end > $day_start && $evt_start < $day_end ) {
				$_evt = clone $evt;
				if( $evt_start < $day_start ) {
					// If event starts before this day, adjust copy's start time
					$_evt->start = $ai1ec_events_helper->local_to_gmt( $day_start );
					$_evt->start_truncated = true;
				}
				if( $evt_end > $day_end ) {
					// If event ends after this day, adjust copy's end time
					$_evt->end = $ai1ec_events_helper->local_to_gmt( $day_end );
					$_evt->end_truncated = true;
				}

				// Place copy of event in appropriate category
				if( $_evt->allday || $_evt->multiday)
					$all_events[$day_start]['allday'][] = $_evt;
				else
					$all_events[$day_start]['notallday'][] = $_evt;
			}
		}

		// This will store the returned array
		$days = array();
		$day = $bits['mday'];

		$day_date = gmmktime( 0, 0, 0, $bits['mon'], $day, $bits['year'] );
		// Re-fetch date bits, since $bits['mday'] + 1 might be in the next month
		$day_bits = $ai1ec_events_helper->gmgetdate( $day_date );

		// Initialize empty arrays for this day if no events to minimize warnings
		if( ! isset( $all_events[$day_date]['allday'] ) ) $all_events[$day_date]['allday'] = array();
		if( ! isset( $all_events[$day_date]['notallday'] ) ) $all_events[$day_date]['notallday'] = array();

		$notallday = array();
		$evt_stack = array( 0 ); // Stack to keep track of indentation
		foreach( $all_events[$day_date]['notallday'] as $evt )
		{
			$start_bits = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( $evt->start ) );

			// Calculate top and bottom edges of current event
			$top = $start_bits['hours'] * 60 + $start_bits['minutes'];
			$bottom = min( $top + $evt->getDuration() / 60, 1440 );

			// While there's more than one event in the stack and this event's top
			// position is beyond the last event's bottom, pop the stack
			while( count( $evt_stack ) > 1 && $top >= end( $evt_stack ) )
				array_pop( $evt_stack );
			// Indentation is number of stacked events minus 1
			$indent = count( $evt_stack ) - 1;
			// Push this event onto the top of the stack
			array_push( $evt_stack, $bottom );

			$notallday[] = array(
				'top'    => $top,
				'height' => $bottom - $top,
				'indent' => $indent,
				'event'  => $evt,
			);
		}

		$days[$day_date] = array(
			'today'     =>
				$day_bits['year'] == $now['year'] &&
				$day_bits['mon']  == $now['mon'] &&
				$day_bits['mday'] == $now['mday'],
			'allday'    => $all_events[$day_date]['allday'],
			'notallday' => $notallday,
		);

		// =========================================
		// = Set one oneday events =
		// =========================================

		return apply_filters( 'ai1ec_get_oneday_cell_array', $days, $timestamp, $filter );
	}

	/**
	 * get_events_between function
	 *
	 * Return all events starting after the given start time and before the
	 * given end time that the currently logged in user has permission to view.
	 * If $spanning is true, then also include events that span this
	 * period. All-day events are returned first.
	 *
	 * @param int $start_time   limit to events starting after this (local) UNIX time
	 * @param int $end_time     limit to events starting before this (local) UNIX time
	 * @param array $filter     Array of filters for the events returned:
	 *                          ['cat_ids']   => non-associatative array of category IDs
	 *                          ['tag_ids']   => non-associatative array of tag IDs
	 *                          ['post_ids']  => non-associatative array of post IDs
	 * @param bool $spanning    also include events that span this period
	 *
	 * @return array            list of matching event objects
	 **/
	function get_events_between( $start_time, $end_time, $filter, $spanning = false ) {

		global $wpdb, $ai1ec_events_helper;

		// Convert timestamps to MySQL format in GMT time
		$start_time = $ai1ec_events_helper->local_to_gmt( $start_time );
		$end_time = $ai1ec_events_helper->local_to_gmt( $end_time );

		// Query arguments
		$args = array( $start_time, $end_time );

		// Get post status Where snippet and associated SQL arguments
		$this->_get_post_status_sql( $post_status_where, $args );

		// Get the Join (filter_join) and Where (filter_where) statements based on
		// $filter elements specified
		$this->_get_filter_sql( $filter );

		$query = $wpdb->prepare(
			"SELECT p.*, e.post_id, i.id AS instance_id, " .
			"UNIX_TIMESTAMP( i.start ) AS start, " .
			"UNIX_TIMESTAMP( i.end ) AS end, " .
			// Treat event instances that span 24 hours as all-day
			"IF( e.allday, e.allday, i.end = DATE_ADD( i.start, INTERVAL 1 DAY ) ) AS allday, " .
			"e.recurrence_rules, e.exception_rules, e.recurrence_dates, e.exception_dates, " .
			"e.venue, e.country, e.address, e.city, e.province, e.postal_code, " .
			"e.show_map, e.contact_name, e.contact_phone, e.contact_email, e.cost, " .
			"e.ical_feed_url, e.ical_source_url, e.ical_organizer, e.ical_contact, e.ical_uid " .
			"FROM {$wpdb->prefix}ai1ec_events e " .
				"INNER JOIN $wpdb->posts p ON p.ID = e.post_id " .
				"INNER JOIN {$wpdb->prefix}ai1ec_event_instances i ON e.post_id = i.post_id " .
				$filter['filter_join'] .
			"WHERE post_type = '" . AI1EC_POST_TYPE . "' " .
			"AND " .
				( $spanning ? "i.end > FROM_UNIXTIME( %d ) AND i.start < FROM_UNIXTIME( %d ) "
										: "i.start >= FROM_UNIXTIME( %d ) AND i.start < FROM_UNIXTIME( %d ) " ) .
			$filter['filter_where'] .
			$post_status_where .
			"ORDER BY allday DESC, i.start ASC, post_title ASC",
			$args );

		$events = $wpdb->get_results( $query, ARRAY_A );
		foreach( $events as &$event ) {
			$event = new Ai1ec_Event( $event );
		}

		return $events;
	}

	/**
	 * get_events_relative_to function
	 *
	 * Return all events starting after the given reference time, limiting the
	 * result set to a maximum of $limit items, offset by $page_offset. A
	 * negative $page_offset can be provided, which will return events *before*
	 * the reference time, as expected.
	 *
	 * @param int $time	          limit to events starting after this (local) UNIX time
	 * @param int $limit          return a maximum of this number of items
	 * @param int $page_offset    offset the result set by $limit times this number
	 * @param array $filter       Array of filters for the events returned.
	 *		                        ['cat_ids']   => non-associatative array of category IDs
	 *		                        ['tag_ids']   => non-associatative array of tag IDs
	 *                            ['post_ids']  => non-associatative array of post IDs
	 *
	 * @return array              three-element array:
	 *                              ['events'] an array of matching event objects
	 *															['prev'] true if more previous events
	 *															['next'] true if more next events
	 **/
	function get_events_relative_to( $time, $limit = 0, $page_offset = 0, $filter = array() ) {

		global $wpdb, $ai1ec_events_helper;

		// Figure out what the beginning of the day is to properly query all-day
		// events; then convert to GMT time
		$bits = $ai1ec_events_helper->gmgetdate( $time );

		// Convert timestamp to GMT time
		$time = $ai1ec_events_helper->local_to_gmt( $time );

		// Query arguments
		$args = array( $time );

		if( $page_offset >= 0 )
			$first_record = $page_offset * $limit;
		else
			$first_record = ( -$page_offset - 1 ) * $limit;

		// Get post status Where snippet and associated SQL arguments
		$this->_get_post_status_sql( $post_status_where, $args );

		// Get the Join (filter_join) and Where (filter_where) statements based on
		// $filter elements specified
		$this->_get_filter_sql( $filter );

		$query = $wpdb->prepare(
			"SELECT SQL_CALC_FOUND_ROWS p.*, e.post_id, i.id AS instance_id, " .
			"UNIX_TIMESTAMP( i.start ) AS start, " .
			"UNIX_TIMESTAMP( i.end ) AS end, " .
			// Treat event instances that span 24 hours as all-day
			"IF( e.allday, e.allday, i.end = DATE_ADD( i.start, INTERVAL 1 DAY ) ) AS allday, " .
			"e.recurrence_rules, e.exception_rules, e.recurrence_dates, e.exception_dates, " .
			"e.venue, e.country, e.address, e.city, e.province, e.postal_code, " .
			"e.show_map, e.contact_name, e.contact_phone, e.contact_email, e.cost, " .
			"e.ical_feed_url, e.ical_source_url, e.ical_organizer, e.ical_contact, e.ical_uid " .
			"FROM {$wpdb->prefix}ai1ec_events e " .
				"INNER JOIN $wpdb->posts p ON e.post_id = p.ID " .
				"INNER JOIN {$wpdb->prefix}ai1ec_event_instances i ON e.post_id = i.post_id " .
				$filter['filter_join'] .
			"WHERE post_type = '" . AI1EC_POST_TYPE . "' " .
			"AND " .
				( $page_offset >= 0 ? "i.end >= FROM_UNIXTIME( %d ) "
					: "i.start < FROM_UNIXTIME( %d ) "
				) .
			$filter['filter_where'] .
			$post_status_where .
			// Reverse order when viewing negative pages, to get correct set of
			// records. Then reverse results later to order them properly.
			"ORDER BY i.start " . ( $page_offset >= 0 ? 'ASC' : 'DESC' ) .
				", post_title " . ( $page_offset >= 0 ? 'ASC' : 'DESC' ) .
			" LIMIT $first_record, $limit",
			$args );

		$events = $wpdb->get_results( $query, ARRAY_A );

		// Reorder records if in negative page offset
		if( $page_offset < 0 ) $events = array_reverse( $events );

		foreach( $events as &$event ) {
			$event = new Ai1ec_Event( $event );
		}

		// Find out if there are more records in the current nav direction
		$more = $wpdb->get_var( 'SELECT FOUND_ROWS()' ) > $first_record + $limit;

		// Navigating in the future
		if( $page_offset > 0 ) {
			$prev = true;
			$next = $more;
		}
		// Navigating in the past
		elseif( $page_offset < 0 ) {
			$prev = $more;
			$next = true;
		}
		// Navigating from the reference time
		else {
			$query = $wpdb->prepare(
				"SELECT COUNT(*) " .
				"FROM {$wpdb->prefix}ai1ec_events e " .
					"INNER JOIN {$wpdb->prefix}ai1ec_event_instances i ON e.post_id = i.post_id " .
					"INNER JOIN $wpdb->posts p ON e.post_id = p.ID " .
					$filter['filter_join'] .
				"WHERE post_type = '" . AI1EC_POST_TYPE . "' " .
				"AND i.start < FROM_UNIXTIME( %d ) " .
				$filter['filter_where'] .
				$post_status_where,
				$args );
			$prev = $wpdb->get_var( $query );
			$next = $more;
		}
		return array(
			'events' => $events,
			'prev' => $prev,
			'next' => $next,
		);
	}

	/**
	 * get_posterboard_date_array function
	 *
	 * Breaks down the given ordered array of event objects into dates, and
	 * outputs an ordered array of two-element associative arrays in the
	 * following format:
	 *	key: localized UNIX timestamp of date
	 *	value:
	 *		['events'] => two-element associatative array broken down thus:
	 *			['allday'] => all-day events occurring on this day
	 *			['notallday'] => all other events occurring on this day
	 *		['today'] => whether or not this date is today
	 *
	 * @param array $events
	 *
	 * @return array
	 **/
	function get_posterboard_date_array( $events ) {
		global $ai1ec_events_helper;

		$dates = array();

		// Classify each event into a date/allday category
		foreach( $events as $event ) {
			$date = $ai1ec_events_helper->gmt_to_local( $event->start );
			$date = $ai1ec_events_helper->gmgetdate( $date );
			$timestamp = gmmktime( 0, 0, 0, $date['mon'], $date['mday'], $date['year'] );
			$category = $event->allday||$event->multiday ? 'allday' : 'notallday';
			$dates[$timestamp]['events'][$category][] = $event;
		}

		// Flag today
		$today = $ai1ec_events_helper->gmt_to_local( time() );
		$today = $ai1ec_events_helper->gmgetdate( $today );
		$today = gmmktime( 0, 0, 0, $today['mon'], $today['mday'], $today['year'] );
		if( isset( $dates[$today] ) )
			$dates[$today]['today'] = true;

		return $dates;
	}

	/**
	 * get_agenda_date_array function
	 *
	 * Breaks down the given ordered array of event objects into dates, and
	 * outputs an ordered array of two-element associative arrays in the
	 * following format:
	 *	key: localized UNIX timestamp of date
	 *	value:
	 *		['events'] => two-element associatative array broken down thus:
	 *			['allday'] => all-day events occurring on this day
	 *			['notallday'] => all other events occurring on this day
	 *		['today'] => whether or not this date is today
	 *
	 * @param array $events
	 *
	 * @return array
	 **/
	function get_agenda_date_array( $events ) {
		global $ai1ec_events_helper;

		$dates = array();

		// Classify each event into a date/allday category
		foreach( $events as $event ) {
			$date = $ai1ec_events_helper->gmt_to_local( $event->start );
			$date = $ai1ec_events_helper->gmgetdate( $date );
			$timestamp = gmmktime( 0, 0, 0, $date['mon'], $date['mday'], $date['year'] );
			$category = $event->allday||$event->multiday ? 'allday' : 'notallday';
			$dates[$timestamp]['events'][$category][] = $event;
		}

		// Flag today
		$today = $ai1ec_events_helper->gmt_to_local( time() );
		$today = $ai1ec_events_helper->gmgetdate( $today );
		$today = gmmktime( 0, 0, 0, $today['mon'], $today['mday'], $today['year'] );
		if( isset( $dates[$today] ) )
			$dates[$today]['today'] = true;

		return $dates;
	}



	/**
	 * get_calendar_url function
	 *
	 * Returns the URL of the configured calendar page in the default view,
	 * optionally preloaded at the month containing the given event (rather than
	 * today's date), and optionally prefiltered by the given filters.
	 *
	 * @param object|null $event  The event to focus the calendar on
	 * @param array       $filter Array of filters for the events returned.
	 *		['cat_ids']   => non-associatative array of category IDs
	 *		['tag_ids']   => non-associatative array of tag IDs
	 *		['post_ids']  => non-associatative array of post IDs
	 *
	 * @return string The URL for this calendar
	 **/
	function get_calendar_url( $event = null, $filter = array() ) {
		global $ai1ec_settings, $ai1ec_events_helper, $ai1ec_app_helper, $wpdb;

		$url = get_permalink( $ai1ec_settings->calendar_page_id );

		if( $event )
		{
			$url .= $ai1ec_app_helper->get_param_delimiter_char( $url );

			switch( $ai1ec_settings->default_calendar_view )
			{
				case 'month':
					// Get components of localized timstamps and calculate month offset
					$today = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );
					$desired = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( $event->start ) );
					$month_offset =
						( $desired['year'] - $today['year'] ) * 12 +
						$desired['mon'] - $today['mon'];

					$url .= "ai1ec_month_offset=$month_offset";
					break;

				case 'week':
					// Get components of localized timstamps and calculate week offset
					/* TODO - code this; first need to find out first day of week based on week start day,
						 then calculate how many weeks off we are from that one
					$today = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );
					$desired = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( $event->start ) );
					$week_offset =
						( $desired['year'] - $today['year'] ) * 12 +
						$desired['mon'] - $today['mon'];

					$url .= "ai1ec_week_offset=$week_offset";*/
					break;

				case 'agenda':
					// Find out how many event instances are between today's first
					// instance and the desired event's instance
					$now = $ai1ec_events_helper->local_to_gmt( time() );
					$after_today = $event->end >= $now;
					$query = $wpdb->prepare(
						"SELECT COUNT(*) FROM {$wpdb->prefix}ai1ec_events e " .
							"INNER JOIN $wpdb->posts p ON e.post_id = p.ID " .
							"INNER JOIN {$wpdb->prefix}ai1ec_event_instances i ON e.post_id = i.post_id " .
						"WHERE post_type = '" . AI1EC_POST_TYPE . "' " .
						"AND post_status = 'publish' " .
						( $after_today
							? "AND i.end >= FROM_UNIXTIME( %d ) AND i.end < FROM_UNIXTIME( %d ) "
							: "AND i.start < FROM_UNIXTIME( %d ) AND i.start >= FROM_UNIXTIME( %d ) "
						) .
						"ORDER BY i.start ASC",
						array( $now, $after_today ? $event->end : $event->start ) );
					$count = $wpdb->get_var( $query );
					// ( $count - 1 ) below solves boundary case for first event of each agenda page
					$page_offset = intval( ( $count - 1 ) / $ai1ec_settings->agenda_events_per_page );
					if( ! $after_today ) $page_offset = -1 - $page_offset;

					$url .= "ai1ec_page_offset=$page_offset";
					break;

				case 'posterboard':
					// Find out how many event instances are between today's first
					// instance and the desired event's instance
					$now = $ai1ec_events_helper->local_to_gmt( time() );
					$after_today = $event->end >= $now;
					$query = $wpdb->prepare(
						"SELECT COUNT(*) FROM {$wpdb->prefix}ai1ec_events e " .
							"INNER JOIN $wpdb->posts p ON e.post_id = p.ID " .
							"INNER JOIN {$wpdb->prefix}ai1ec_event_instances i ON e.post_id = i.post_id " .
						"WHERE post_type = '" . AI1EC_POST_TYPE . "' " .
						"AND post_status = 'publish' " .
						( $after_today
							? "AND i.end >= FROM_UNIXTIME( %d ) AND i.end < FROM_UNIXTIME( %d ) "
							: "AND i.start < FROM_UNIXTIME( %d ) AND i.start >= FROM_UNIXTIME( %d ) "
						) .
						"ORDER BY i.start ASC",
						array( $now, $after_today ? $event->end : $event->start ) );
					$count = $wpdb->get_var( $query );
					// ( $count - 1 ) below solves boundary case for first event of each posterboard page
					$page_offset = intval( ( $count - 1 ) / $ai1ec_settings->posterboard_events_per_page );
					if( ! $after_today ) $page_offset = -1 - $page_offset;

					$url .= "ai1ec_page_offset=$page_offset";
					break;
			}

			$url .= "&ai1ec_active_event=$event->post_id";
		}

		// Add filter parameters
		foreach( $filter as $key => $val ) {
			if( $val ) {
				$url .= $ai1ec_app_helper->get_param_delimiter_char( $url ) .
					"ai1ec_$key=" . join( ',', $val );
			}
		}

		return $url;
	}

	/**
	 * get_weekdays function
	 *
	 * Returns a list of abbreviated weekday names starting on the configured
	 * week start day setting.
	 *
	 * @return array
	 **/
	function get_weekdays() {
		global $ai1ec_settings;
		static $weekdays;

		if( ! isset( $weekdays ) )
		{
			$time = strtotime( 'next Sunday' );
			$time = strtotime( "+{$ai1ec_settings->week_start_day} days", $time );

			$weekdays = array();
			for( $i = 0; $i < 7; $i++ ) {
				$weekdays[] = date_i18n( 'D', $time );
				$time += 60 * 60 * 24; // Add a day
			}
		}
		return $weekdays;
	}

	/**
	 * get_posterboard_pagination_links function
	 *
	 * Returns an associative array of two links for the posterboard view of the
	 * calendar: previous page (if previous events exist), next page (if next
	 * events exist), in that order.
	 * Each element' is an associative array containing the link ID ['id'],
	 * text ['text'] and value to assign to link's href ['href'].
	 *
	 * @param int $cur_offset page offset of posterboard view, needed for hrefs
	 * @param int $prev       whether there are more events before the current page
	 * @param int $next       whether there are more events after the current page
	 *
	 * @return array          array of link information as described above
	 **/
	function get_posterboard_pagination_links( $cur_offset, $prev = false, $next = false ) {
		global $ai1ec_settings;

		$links = array();

		if( $prev ) {
			$links['prev'] = array(
				'id' => 'ai1ec-prev-page',
				'text' => sprintf( __( '« Previous Events', AI1EC_PLUGIN_NAME ), $ai1ec_settings->posterboard_events_per_page ),
				'href' => '#action=ai1ec_posterboard&ai1ec_page_offset=' . ( $cur_offset - 1 ),
			);
		}
		if( $next ) {
			$links['next'] = array(
				'id' => 'ai1ec-next-page',
				'text' => sprintf( __( 'Next Events »', AI1EC_PLUGIN_NAME ), $ai1ec_settings->posterboard_events_per_page ),
				'href' => '#action=ai1ec_posterboard&ai1ec_page_offset=' . ( $cur_offset + 1 ),
			);
		}

		return $links;
	}

	/**
	 * get_day_pagination_links function
	 *
	 * Returns a non-associative array of four links for the day view of the
	 * calendar:
	 * previous day, next day, in that order.
	 * Each element's key is an associative array containing the link's ID
	 * ['id'], text ['text'] and value to assign to link's href ['href'].
	 *
	 * @param int $cur_offset day offset of current day, needed for hrefs
	 *
	 * @return array          array of link information as described above
	 **/
    function get_oneday_pagination_links( $cur_offset ) {
    	global $ai1ec_events_helper;

		$links = array();

		// Base timestamp on offset week
		$bits = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );
		$bits['mday'] += $cur_offset;

		/* translators: "%s" represents the week's starting date */
		$links[] = array(
			'id' => 'ai1ec-prev-day',
			'text' =>
				'‹ ' .
					date_i18n( __( 'j F Y', AI1EC_PLUGIN_NAME ), gmmktime( 0, 0, 0, $bits['mon'], $bits['mday'] - 1, $bits['year'] )
				),
			'href' => '#action=ai1ec_oneday&ai1ec_oneday_offset=' . ( $cur_offset - 1 ),
		);
		$links[] = array(
			'id' => 'ai1ec-prev-day',
			'text' =>
					date_i18n( __( 'j F Y', AI1EC_PLUGIN_NAME ), gmmktime( 0, 0, 0, $bits['mon'], $bits['mday'] + 1, $bits['year'] ))
                    .' ›',
			'href' => '#action=ai1ec_oneday&ai1ec_oneday_offset=' . ( $cur_offset + 1 ),
		);

		return $links;
     }

	/**
	 * get_month_pagination_links function
	 *
	 * Returns a non-associative array of four links for the month view of the
	 * calendar:
	 * previous year, previous month, next month, and next year, in that order.
	 * Each element's key is an associative array containing the link's ID
	 * ['id'], text ['text'] and value to assign to link's href ['href'].
	 *
	 * @param int $cur_offset month offset of current month, needed for hrefs
	 *
	 * @return array          array of link information as described above
	 **/
	function get_month_pagination_links( $cur_offset ) {
		global $ai1ec_events_helper;

		$links = array();

		// Base timestamp on offset month
		$bits = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );
		$bits['mon'] += $cur_offset;
		// 'mon' may now be out of range (< 1 or > 12), so recreate $bits to make sane
		$bits = $ai1ec_events_helper->gmgetdate( gmmktime( 0, 0, 0, $bits['mon'], 1, $bits['year'] ) );

		$links[] = array(
			'id' => 'ai1ec-prev-year',
			'text' => '« ' . ( $bits['year'] - 1 ),
			'href' => '#action=ai1ec_month&ai1ec_month_offset=' . ( $cur_offset - 12 ),
		);
		$links[] = array(
			'id' => 'ai1ec-prev-month',
			'text' => '‹ ' . date_i18n( 'M', gmmktime( 0, 0, 0, $bits['mon'] - 1, 1, $bits['year'] ), true ),
			'href' => '#action=ai1ec_month&ai1ec_month_offset=' . ( $cur_offset - 1 ),
		);
		$links[] = array(
			'id' => 'ai1ec-next-month',
			'text' => date_i18n( 'M', gmmktime( 0, 0, 0, $bits['mon'] + 1, 1, $bits['year'] ), true ) . ' ›',
			'href' => '#action=ai1ec_month&ai1ec_month_offset=' . ( $cur_offset + 1 ),
		);
		$links[] = array(
			'id' => 'ai1ec-next-year',
			'text' => ( $bits['year'] + 1 ) . ' »',
			'href' => '#action=ai1ec_month&ai1ec_month_offset=' . ( $cur_offset + 12 ),
		);

		return $links;
	}

	/**
	 * get_week_pagination_links function
	 *
	 * Returns a non-associative array of two links for the week view of the
	 * calendar:
	 * previous week, next week, in that order.
	 * Each element's key is an associative array containing the link's ID
	 * ['id'], text ['text'] and value to assign to link's href ['href'].
	 *
	 * @param int $cur_offset week offset of current week, needed for hrefs
	 *
	 * @return array          array of link information as described above
	 **/
	function get_week_pagination_links( $cur_offset ) {
		global $ai1ec_events_helper;

		$links = array();

		// Base timestamp on offset week
		$bits = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );
		$bits['mday'] += $ai1ec_events_helper->get_week_start_day_offset( $bits['wday'] );
		$bits['mday'] += $cur_offset * 7;

		/* translators: "%s" represents the week's starting date */
		$links[] = array(
			'id' => 'ai1ec-prev-week',
			'text' =>
				'‹ ' .
				sprintf(
					__( 'Week of %s', AI1EC_PLUGIN_NAME ),
					date_i18n( __( 'M j', AI1EC_PLUGIN_NAME ), gmmktime( 0, 0, 0, $bits['mon'], $bits['mday'] - 7, $bits['year'] ), true )
				),
			'href' => '#action=ai1ec_week&ai1ec_week_offset=' . ( $cur_offset - 1 ),
		);
		$links[] = array(
			'id' => 'ai1ec-next-week',
			'text' =>
				sprintf(
					__( 'Week of %s', AI1EC_PLUGIN_NAME ),
					date_i18n( __( 'M j', AI1EC_PLUGIN_NAME ), gmmktime( 0, 0, 0, $bits['mon'], $bits['mday'] + 7, $bits['year'] ), true )
				)
				. ' ›',
			'href' => '#action=ai1ec_week&ai1ec_week_offset=' . ( $cur_offset + 1 ),
		);

		return $links;
	}

	/**
	 * get_agenda_pagination_links function
	 *
	 * Returns an associative array of two links for the agenda view of the
	 * calendar: previous page (if previous events exist), next page (if next
	 * events exist), in that order.
	 * Each element' is an associative array containing the link ID ['id'],
	 * text ['text'] and value to assign to link's href ['href'].
	 *
	 * @param int $cur_offset page offset of agenda view, needed for hrefs
	 * @param int $prev       whether there are more events before the current page
	 * @param int $next       whether there are more events after the current page
	 *
	 * @return array          array of link information as described above
	 **/
	function get_agenda_pagination_links( $cur_offset, $prev = false, $next = false ) {
		global $ai1ec_settings;

		$links = array();

		if( $prev ) {
			$links['prev'] = array(
				'id' => 'ai1ec-prev-page',
				'text' => sprintf( __( '« Previous Events', AI1EC_PLUGIN_NAME ), $ai1ec_settings->agenda_events_per_page ),
				'href' => '#action=ai1ec_agenda&ai1ec_page_offset=' . ( $cur_offset - 1 ),
			);
		}
		if( $next ) {
			$links['next'] = array(
				'id' => 'ai1ec-next-page',
				'text' => sprintf( __( 'Next Events »', AI1EC_PLUGIN_NAME ), $ai1ec_settings->agenda_events_per_page ),
				'href' => '#action=ai1ec_agenda&ai1ec_page_offset=' . ( $cur_offset + 1 ),
			);
		}

		return $links;
	}

	/**
	 * _get_post_status_sql function
	 *
	 * Returns SQL snippet for properly matching event posts, as well as array
	 * of arguments to pass to $wpdb->prepare, in function argument references.
	 * Nothing is returned by the function.
	 *
	 * @param string &$sql  The variable to store the SQL snippet into
	 * @param array  &$args The variable to store the SQL arguments into
	 *
	 * @return void
	 */
	function _get_post_status_sql( &$post_status_where = '', &$args )
	{
		global $current_user;

		// Query the correct post status
		if( current_user_can( 'administrator' ) || current_user_can( 'editor' ) )
		{
			// User has privilege of seeing all published and private

			$post_status_where = "AND ( post_status = %s OR post_status = %s ) ";
			$args[]            = 'publish';
			$args[]            = 'private';
		}
		elseif( is_user_logged_in() )
		{
			// User has privilege of seeing all published and only their own private
			// posts.

			// get user info
			get_currentuserinfo();

			// include post_status = published
			//   OR
			// post_status = private AND post_author = userID
			$post_status_where =
				"AND ( " .
					"post_status = %s " .
					"OR ( post_status = %s AND post_author = %d ) " .
				") ";

			$args[] = 'publish';
			$args[] = 'private';
			$args[] = $current_user->ID;
		} else {
			// User can only see published posts.
			$post_status_where = "AND post_status = %s ";
			$args[]            = 'publish';
		}
	}

	/**
	 * _get_filter_sql function
	 *
	 * Takes an array of filtering options and turns it into JOIN and WHERE statements
	 * for running an SQL query limited to the specified options
	 *
	 * @param array &$filter      Array of filters for the events returned.
	 *		                        ['cat_ids']   => non-associatative array of category IDs
	 *		                        ['tag_ids']   => non-associatative array of tag IDs
	 *		                        ['post_ids']  => non-associatative array of event post IDs
	 *														This array is modified to have:
	 *                              ['filter_join']  the Join statements for the SQL
	 *                              ['filter_where'] the Where statements for the SQL
	 *
	 * @return void
	 */
	function _get_filter_sql( &$filter ) {
		global $wpdb;

		// Set up the filter join and where strings
		$filter['filter_join']  = '';
		$filter['filter_where'] = '';

		// By default open the Where with an AND ( .. ) to group all statements.
		// Later, set it to OR to join statements together.
		// TODO - make this cleaner by supporting the choice of AND/OR logic
		$where_logic = ' AND (';

		foreach( $filter as $filter_type => $filter_ids ) {
			// If no filter elements specified, don't do anything
			if( $filter_ids && is_array( $filter_ids ) ) {
				switch ( $filter_type ) {
					// Limit by Category IDs
					case 'cat_ids':
						$filter['filter_join']   .= " LEFT JOIN $wpdb->term_relationships AS trc ON e.post_id = trc.object_id ";
						$filter['filter_join']   .= " LEFT JOIN $wpdb->term_taxonomy ttc ON trc.term_taxonomy_id = ttc.term_taxonomy_id AND ttc.taxonomy = 'events_categories' ";
						$filter['filter_where']  .= $where_logic . " ttc.term_id IN ( " . join( ',', $filter_ids ) . " ) ";
						$where_logic = ' OR ';
						break;
					// Limit by Tag IDs
					case 'tag_ids':
						$filter['filter_join']   .= " LEFT JOIN $wpdb->term_relationships AS trt ON e.post_id = trt.object_id ";
						$filter['filter_join']   .= " LEFT JOIN $wpdb->term_taxonomy ttt ON trt.term_taxonomy_id = ttt.term_taxonomy_id AND ttt.taxonomy = 'events_tags' ";
						$filter['filter_where']  .= $where_logic . " ttt.term_id IN ( " . join( ',', $filter_ids ) . " ) ";
						$where_logic = ' OR ';
						break;
					// Limit by post IDs
					case 'post_ids':
						$filter['filter_where']  .= $where_logic . " e.post_id IN ( " . join( ',', $filter_ids ) . " ) ";
						$where_logic = ' OR ';
						break;
				}
			}
		}

		// Close the Where statement bracket if any Where statements were set
		if( $filter['filter_where'] != '' ) {
			$filter['filter_where'] .= ' ) ';
		}
	}
}
// END class
