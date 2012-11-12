<?php
/**
 *
 * @author time.ly
 *
 * The interface that strategy objects must implement to query for Events for the collection.
 *
 */
interface Query_Events_Strategy_Interface {
	/**
	 * Get the events for the current Facebook Graph Object collection. This is the only method that must be implemented.
	 *
	 * @param Facebook_WP_Extend_Ai1ec $facebook an instance of the Facebook class
	 *
	 * @param array $ids an array of Facebook Graph Objects ids for which we must gather the events
	 *
	 * @param int $timestamp the starting time of the events to refresh: only events that start after this timestamp will be retrieved.
	 */
	public function query_events( Facebook_WP_Extend_Ai1ec $facebook, array $ids, $timestamp );

}

?>
