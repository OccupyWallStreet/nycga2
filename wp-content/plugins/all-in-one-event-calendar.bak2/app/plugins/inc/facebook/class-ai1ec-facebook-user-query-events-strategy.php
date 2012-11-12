<?php

/**
 * @author time.ly
 *
 * This class handles the strategy used by the Facebook Graph Object Collection to  query for user events.
 */
require_once 'interfaces/Query_Events_Strategy_Interface.php';
class Ai1ec_Facebook_User_Query_Events_Strategy extends Ai1ec_Facebook_Query_Abstract implements Query_Events_Strategy_Interface {
	/**
	 * (non-PHPdoc)
	 * @see Query_Events_Strategy_Interface::query_events()
	 */
	public function query_events( Facebook_WP_Extend_Ai1ec $facebook, array $users, $timestamp ) {
		$events = array();
		// Create the fql query.
		$fql = $this->generate_fql_multiquery_to_get_events_details( $users, $timestamp );
		try {
			$events = $facebook->api( array(
					'method' => 'fql.multiquery',
					'queries' => $fql,
			) );
		} catch ( WP_FacebookApiException $e ) {
			throw $e;
		}
		// Normalize the events
		$events = $this->convert_multi_query_resultset( $events );
		// When an event has a page as venue, we must get the data from the page.
		$events = $this->update_events_with_page_id_as_venues( $facebook, $events );
		return $events;
	}

	/**
	 * Generate the fql query to get the details of user events
	 *
	 * @param array $users
	 *
	 * @param int $timestamp
	 *
	 * @return array an array of fql queries.
	 */
	private function generate_fql_multiquery_to_get_events_details( array $users, $timestamp ) {
		$fql = array();
		// When we make a query we must convert
		$time = Ai1ec_Facebook_Event::get_facebook_actual_time( $timestamp );
		foreach( $users as $id ) {
			$fql[$id] = "SELECT
							eid,
							name,
							description,
							start_time,
							end_time,
							venue,
							location,
							update_time,
							timezone
						FROM
							event
						WHERE
							eid IN (SELECT eid FROM event_member WHERE uid = $id) AND start_time > $time" ;
		}
		return $fql;
	}
}

?>
