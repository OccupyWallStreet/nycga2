<?php

require_once( 'interfaces/Sync_Objects_From_Facebook_Strategy_Interface.php' );

/**
 * @author time.ly
 *
 * This class handles the strategy used by a Facebook_Graph_Object_Collection to sync groups with facebook.
 */
class Ai1ec_Facebook_Group_Sync_Object_From_Facebook_Strategy extends Ai1ec_Facebook_Sync_Object_Abstract implements Sync_Objects_From_Facebook_Strategy_Interface {
	/**
	 * (non-PHPdoc)
	 * @see Sync_Objects_From_Facebook_Strategy_Interface::get_users_from_facebook()
	 */
	public function get_users_from_facebook( Facebook_WP_Extend_Ai1ec $facebook ) {
		$user = $facebook->getUser();
		try {
			$items = $facebook->api( "/$user/groups/" );
			$items = $items['data'];
			$items = $this->get_images( $facebook, $items );
		} catch ( WP_FacebookApiException $e) {
			throw $e;
		}
		return $this->convert_group_data_for_saving( $items );
	}


	/**
	 * Merges groups and events in a way that is consistent with user and pages
	 *
	 * @param array $groups an array of groups that the logged on users has subscribed to
	 *
	 * @return array $groups_to_return an array of groups that can be saved to the db
	 */
	public static function convert_group_data_for_saving( array $groups ) {
		$groups_to_return = array();
		foreach ( $groups as $group ) {
			$groups_to_return[$group['id']] = array(
					'user_id'         => $group['id'],
					'user_name'       => $group['name'],
					'user_pic'        => $group['user_pic'],
					'type'            => Ai1ec_Facebook_Graph_Object_Collection::FB_GROUP,
			);
		}
		return $groups_to_return;
	}

}

?>
