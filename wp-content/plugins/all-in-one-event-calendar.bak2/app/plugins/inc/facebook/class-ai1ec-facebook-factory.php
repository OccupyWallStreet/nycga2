<?php
/**
 * This class handles the creation of objects.
 *
 * @author The Seed Network
 *
 *
 */
class Ai1ec_Facebook_Factory {
	const CURRENT_USER            = 'Ai1ec_Facebook_Current_User';
	const TAB                     = 'Ai1ec_Facebook_Tab';
	const GRAPH_OBJECT_COLLECTION = 'Ai1ec_Facebook_Graph_Object_Collection';
	const GRAPH_OBJECT            = 'Ai1ec_Facebook_Graph_Object';
	const FACEBOOK_APP            = 'Ai1ec_Facebook_Application';
	const EVENT                   = 'Ai1ec_Facebook_Event';
	const CUSTOM_BULK_ACTION      = 'Ai1ec_Facebook_Custom_Bulk_Action';
	// The name of the plugin table
	const FB_DB_TABLE             = 'ai1ec_facebook_users';
	// The name of the evnts table
	const AI1EC_EVENTS_TABLE      = 'ai1ec_events';
	// The name of the user-events table
	const FB_USER_EVENTS_TABLE    = 'ai1ec_facebook_users_events';
	/**
	 * 
	 * @param Facebook_WP_Extend_Ai1ec $facebook
	 * @return Ai1ec_Facebook_Current_User
	 */
	static public function get_facebook_user_instance( Facebook_WP_Extend_Ai1ec $facebook ) {
		$class = self::CURRENT_USER;
		return new $class( $facebook );
	}
	/**
	 * @return Ai1ec_Facebook_Tab
	 */
	static public function get_facebook_tab_instance() {
		$class = self::TAB;
		return new $class();
	}
	/**
	 * 
	 * @param array $ids the ids of the facebook graph object that will form the collection 
	 * @return Ai1ec_Facebook_Graph_Object_Collection
	 */
	static public function get_facebook_graph_object_collection( array $ids ) {
		$class = self::GRAPH_OBJECT_COLLECTION;
		return new $class( $ids );
	}
	/**
	 * 
	 * @param int $id the id of the Facebook Graph Object
	 * 
	 * @return Ai1ec_Facebook_Graph_Object
	 */
	static public function get_facebook_graph_object( $id ) {
		$class = self::GRAPH_OBJECT;
		return new $class( $id );
	}
	/**
	 * Returns the plugin table with Wordpress prefix
	 *
	 * @return string the plugin table
	 */
	static public function get_plugin_table() {
		global $wpdb;
		return $wpdb->prefix . self::FB_DB_TABLE;
	}
	/**
	* Returns the events table with Wordpress prefix
	*
	* @return string the plugin table
	*/
	static public function get_events_table() {
		global $wpdb;
		return $wpdb->prefix . self::AI1EC_EVENTS_TABLE;
	}
	/**
	 * Generate the correct strategy object for getting events.
	 * 
	 * @param string $type the type of Facebook Graph Object we are creating the strategy for
	 * 
	 * @return Query_Events_Strategy_Interface
	 */
	static public function generate_strategy_for_querying_events( $type ) {
		// Facebook pages and users implement the same strategy.
		if( $type === Ai1ec_Facebook_Graph_Object_Collection::FB_PAGE ) {
			$type = Ai1ec_Facebook_Graph_Object_Collection::FB_USER;
		}
		$type = ucfirst( $type );
		$class = "Ai1ec_Facebook_{$type}_Query_Events_Strategy";
		return new $class(); 
	}
	/**
	 * * Generate the correct strategy object for syncing objects.
	 * 
	 * @param string $type the type of Facebook Graph Object we are creating the strategy for
	 * 
	 * @return Sync_Objects_From_Facebook_Strategy_Interface an object that implement the interface
	 */
	static public function generate_sync_object_strategy( $type ) {
		$type = ucfirst( $type );
		$class = "Ai1ec_Facebook_{$type}_Sync_Object_From_Facebook_Strategy";
		return new $class();
	}
	/**
	 * Returns the user_events table with the wordpress prefix
	 * 
	 * @return string
	 */
	static public function get_user_events_table() {
		global $wpdb;
		return $wpdb->prefix . self::FB_USER_EVENTS_TABLE;
	}
	/**
	 * Return a Facebook object.
	 * 
	 * @return Ai1ec_Facebook_Event
	 */
	static public function get_facebook_event_instance() {
		$class = self::EVENT;
		return new $class();
	}
	/**
	 * Return a Facebook application instance
	 * 
	 * @param string $app_id
	 * 
	 * @param string $secret
	 * 
	 * @return Ai1ec_Facebook_Application
	 */
	static public function get_facebook_application_instance( $app_id, $secret ) {
		$class = self::FACEBOOK_APP;
		return new $class( $app_id, $secret );
	}
	/**
	 * Return a Facebook custom bulk action instance
	 * 
	 * @return Ai1ec_Facebook_Custom_Bulk_Action
	 */
	static public function get_facebook_custom_bulk_action_instance( Ai1ecFacebookConnectorPlugin $facebook_plugin ) {
		$class = self::CUSTOM_BULK_ACTION;
		return new $class( $facebook_plugin );
	}
}

?>