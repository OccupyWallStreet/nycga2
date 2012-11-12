<?php

/**
 * @author time.ly
 *
 * This class implements some common methods for syncing objects with facebook.
 */
abstract class Ai1ec_Facebook_Sync_Object_Abstract {
	/**
	 * Unpack the results of a fql multy-query
	 *
	 * @param array $events the events coming from a fql multi query
	 *
	 * @return array an array where the keys are the ids and the values are the number of events
	 */
	protected function unpack_events_array( array $events ) {
		$results = array();
		foreach ( $events as $fql_result ) {
			$result[$fql_result['name']] = count( $fql_result['fql_result_set'] );
		}
		return $result;
	}
	/**
	 * Fetches data for the object to get missing icons.
	 *
	 * @param Facebook_WP_Extend_Ai1ec $facebook
	 *
	 * @param array $items
	 *
	 * @throws WP_FacebookApiException
	 *
	 * @return array the Facebook Graph Objects with the corrected icons
	 */
	protected function get_images( Facebook_WP_Extend_Ai1ec $facebook, array $items ) {
		$idx = 0;
		foreach ( $items as $item ) {
			$id = $item['id'];
			try {
				$details = $facebook->api( "/$id/" );
				// Applications have an icon url
				if ( isset( $details['icon_url'] ) && ! isset( $details['icon'] ) ) {
					$details['icon'] = $details['icon_url'];
				}
				//Since things change fast in Facebook, i add this extra check to avoid notices
				if ( ! isset( $details['icon'] ) ) {
					$details['icon'] = '';
				}
				$items[$idx]['user_pic'] = isset( $details['picture'] ) ? $details['picture'] : $details['icon'] ;
				$idx++;
			} catch ( WP_FacebookApiException $e ) {
				throw $e;
			}

		}
		return $items;
	}
}

?>
