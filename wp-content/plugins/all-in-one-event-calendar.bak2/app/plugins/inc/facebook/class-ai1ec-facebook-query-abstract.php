<?php


/**
 *
 * @author time.ly
 *
 * The class implements some common methods for retrieving Facebook events
 */

abstract class Ai1ec_Facebook_Query_Abstract {
	/**
	 * This function normalizes the result of a fql multyquery
	 *
	 * @param array $events the result of the fql multy query
	 *
	 * @return array
	 */
	protected function convert_multi_query_resultset( array $events ) {
		$events_to_return = array();
		foreach( $events as $event_array ) {
			$id = $event_array['name'];
			foreach( $event_array['fql_result_set'] as $event ) {
				$event['facebook_user'] = $id;
				$events_to_return[] = $event;
			}

		}
		return $events_to_return;
	}
	/**
	 * Unpack the results of a fql multy-query for venues updating the events accordingly
	 *
	 * @param array $events The events array
	 *
	 * @param array $venues the result of the fql multi query
	 *
	 * @param array $indexes an array that stores the index in $events of the events eid
	 *
	 * @return array the events arrey with the added venues
	 */
	protected function merge_events_and_venues( array $events, array $venues, array $indexes ) {
		foreach( $venues as $venue ) {
			$eid = $venue['name'];
			$index = $indexes[$eid];
			$events[$index]['venue'] = Ai1ec_Facebook_Data_Converter::return_empty_or_value_if_set( $venue['fql_result_set'][0] , 'location' );
		}
		return $events;
	}
	/**
	 *
	 * @param Facebook_WP_Extend_Ai1ec $facebook an instance of the facebook class
	 *
	 * @param array $events the events to check
	 *
	 * @throws WP_FacebookApiException if something goes wrong in the facebook call
	 *
	 * @return array the events with the page_ids converted into venues
	 */
	protected function update_events_with_page_id_as_venues( Facebook_WP_Extend_Ai1ec $facebook, array $events ) {
		// This array holds the fql queries
		$fql = array();
		// This array will hold the Venues
		$venues = array();
		// This array keeps track of what events need a venue so that we save cycles afterwards
		$indexes = array();
		$index = 0;
		foreach ( $events as $event ) {
			// If the venues is a id
			if( isset( $event['venue']['id'] ) ) {
				$pid = $event['venue']['id'];
				// Save the index of this event
				$indexes[$event['eid']] = $index;
				// Create the query.
				$fql[$event['eid']] = "SELECT location FROM page WHERE page_id = $pid";
			}
			$index++;
		}
		if( ! empty( $fql ) ) {
			try {
				$venues = $facebook->api( array(
						'method' => 'fql.multiquery',
						'queries' => $fql,
				) );
				// Merge the results if no exception is thrown.
				$events = $this->merge_events_and_venues( $events, $venues, $indexes );
			}
			catch ( WP_FacebookApiException $e ) {
				throw $e;
			}
		}
		return $events;
	}
}

?>
