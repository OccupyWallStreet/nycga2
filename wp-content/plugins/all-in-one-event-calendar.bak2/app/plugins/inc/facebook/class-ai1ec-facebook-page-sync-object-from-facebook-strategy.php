<?php

require_once( 'interfaces/Sync_Objects_From_Facebook_Strategy_Interface.php' );

/**
 * @author time.ly
 *
 * This class handles the strategy used by a Facebook_Graph_Object_Collection to sync pages from facebook.
 */
class Ai1ec_Facebook_Page_Sync_Object_From_Facebook_Strategy extends Ai1ec_Facebook_Sync_Object_Abstract implements Sync_Objects_From_Facebook_Strategy_Interface {
	/**
	 * (non-PHPdoc)
	 * @see Sync_Objects_From_Facebook_Strategy_Interface::get_users_from_facebook()
	 */
	public function get_users_from_facebook( Facebook_WP_Extend_Ai1ec $facebook ) {
		$user = $facebook->getUser();
		$items = array();
		try {
			$items = $facebook->api( "/$user/accounts/" );
			$items = $items['data'];
			$items = $this->get_images( $facebook, $items );
		} catch ( WP_FacebookApiException $e) {
			throw $e;
		}
		return $this->convert_page_data_for_saving( $items );
	}

	/**
	 * Merges pages and events in a way that is consistent with user and groups
	 *
	 * @param array $pages The pages retriev from facebook
	 *
	 * @return array an array of pages that can be saved to the db
	 */
	private function convert_page_data_for_saving( array $pages ) {
		$pages_to_return = array();
		foreach ( $pages as $page ) {
			$pages_to_return[$page['id']] = array(
					'user_id'         => $page['id'],
					'user_name'       => $page['name'],
					'user_pic'        => $page['user_pic'],
					'type'            => Ai1ec_Facebook_Graph_Object_Collection::FB_PAGE,
			);
		}
		return $pages_to_return;
	}

}

?>
