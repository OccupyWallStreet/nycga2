<?php

/**
 * @author time.ly
 *
 * This class handles the strategy used by a Facebook_Graph_Object_Collection to query for group events.
 */
require_once 'interfaces/Query_Events_Strategy_Interface.php';
class Ai1ec_Facebook_Group_Query_Events_Strategy extends Ai1ec_Facebook_Query_Abstract implements Query_Events_Strategy_Interface {
	/**
	 * (non-PHPdoc)
	 * @see Query_Events_Strategy_Interface::query_events()
	 */
	public function query_events( Facebook_WP_Extend_Ai1ec $facebook, array $groups, $timestamp ) {
		$events = array();
		try {
			// Groups behave differently. First you need to get the id of events through the API
			$events = $this->get_events_eid_from_groups( $facebook, $groups );
			// Normalize the result by getting the eids.
			$eids = $this->get_eids_from_groups_events_result( $events );
			// Get the actual data for events
			$events = $this->get_events_from_facebook_from_eids( $facebook, $eids, $timestamp );
			// Convert the result of the multi-query
			$events = $this->convert_multi_query_resultset( $events );
		} catch ( WP_FacebookApiException $e ) {
			throw $e;
		}
		// When an event has a page as venue, we must get the data from the page.
		$events = $this->update_events_with_page_id_as_venues( $facebook, $events );
		return $events;
	}
	/**
	 * Creates a multy-query to get events for groups
	 *
	 * @param array $grouped_eids an array where the keys are the id of the groups and the values are an array of events id
	 *
	 * @param int $timestamp the timestamp that needs to be passed for checking only events that start after it
	 *
	 * @return array the FQL mutly-query array.
	 */
	private function generate_multi_query_for_event_details( array $grouped_eids, $timestamp ) {
		$fql = array();
		foreach ( $grouped_eids as $id => $eids ) {
			$imploded_eids = implode( ',', $eids );
			$time = Ai1ec_Facebook_Event::get_facebook_actual_time( $timestamp );
			$fql[$id] = "
			SELECT
				eid,
				name,
				description,
				start_time,
				end_time,
				venue,
				location,
				update_time
			FROM
				event
			WHERE
				eid IN ($imploded_eids) AND start_time > $time";
		}
		return $fql;
	}
	/**
	 * Get's the group events by making calls to the Facebook API
	 *
	 * @param Facebook_WP_Extend_Ai1ec $facebook a Facebook instance
	 *
	 * @param array $groups the groups for which we must retrieve the events
	 *
	 * @throws WP_FacebookApiException Something went wrong with Facebook
	 *
	 * @return array an associative array where keys are the id of the groups and the values are an array of events
	 */
	private function get_events_eid_from_groups( Facebook_WP_Extend_Ai1ec $facebook, array $groups ) {
		$group_events = array();
		foreach( $groups as $id ) {
			try {
				$events = $facebook->api( "/$id/events/" );
				$group_events[$id] = $events['data'];
			} catch (WP_FacebookApiException $e) {
				throw $e;
			}
		}
		return $group_events;
	}
	/**
	 * Returns an array of event ids for an array of group events returned from an api call
	 *
	 * @param array $results
	 *
	 * @return array $eids array where the keys are the id of the groups and the values are an array of events id
	 */
	private function get_eids_from_groups_events_result( array $results ) {
		$eids = array();
		foreach( $results as $id => $events ) {
			$eids[$id] = array();
			foreach ( $events as $event ) {
				$eids[$id][] = $event['id'];
			}
		}
		return $eids;
	}
	/**
	 * Gets the details for the events for the groups so that they can be saved.
	 *
	 * @param Facebook_WP_Extend_Ai1ec $facebook a Facebook instance
	 *
	 * @param array $grouped_eids array where the keys are the id of the groups and the values are an array of events id
	 *
	 * @param int $timestamp the timestamp that needs to be passed for checking only events that start after it
	 *
	 * @throws WP_FacebookApiException
	 *
	 * @return array the result of the FQL query
	 */
	private function get_events_from_facebook_from_eids( Facebook_WP_Extend_Ai1ec $facebook, array $grouped_eids, $timestamp ) {
		$fql = $this->generate_multi_query_for_event_details( $grouped_eids, $timestamp );
		try {
			$events = $facebook->api( array(
					'method' => 'fql.multiquery',
					'queries' => $fql,
			) );
		} catch ( WP_FacebookApiException $e ) {
			throw $e;
		}
		return $events;
	}
}

?>
