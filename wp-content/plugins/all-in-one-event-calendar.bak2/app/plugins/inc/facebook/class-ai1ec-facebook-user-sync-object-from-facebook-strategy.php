<?php

require_once( 'interfaces/Sync_Objects_From_Facebook_Strategy_Interface.php' );

/**
 * @author time.ly
 *
 * This class handles the strategy used by a Facebook_Graph_Object_Collection to sync users from facebook.
 */

class Ai1ec_Facebook_User_Sync_Object_From_Facebook_Strategy extends Ai1ec_Facebook_Sync_Object_Abstract implements Sync_Objects_From_Facebook_Strategy_Interface {

	public function get_users_from_facebook( Facebook_WP_Extend_Ai1ec $facebook ) {
		$fql = "SELECT name, id, pic FROM profile WHERE id = me() OR id IN (SELECT uid2 FROM friend WHERE uid1 = me() )";
		try {
			$items = $facebook->api( array(
					'method' => 'fql.query',
					'query' => $fql,
			) );
		} catch ( WP_FacebookApiException $e) {
			throw $e;
		}
		return $this->convert_user_data_for_saving( $items );
	}
	/**
	 * Merges the users and the event data so that we can save them to the db.
	 *
	 * @param array $users the user that were retrieved from Facebook
	 *
	 * @return array the users that can be saved to the db.
	 */
	public function convert_user_data_for_saving( array $users ) {
		$users_to_return = array();
		foreach ( $users as $user ) {
			$users_to_return[$user['id']] = array(
					'user_id'    => $user['id'],
					'user_name'  => $user['name'],
					'user_pic'   => $user['pic'],
					'type'       => Ai1ec_Facebook_Graph_Object_Collection::FB_USER,
			);
		}
		return $users_to_return;
	}
}

?>
