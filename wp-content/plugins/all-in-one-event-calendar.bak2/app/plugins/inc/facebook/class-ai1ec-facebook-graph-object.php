<?php
/**
 * This class represent a simple Facebook Graph Object ( page, group, user).
 *
 * @author The Seed Network
 *
 *
 */
class Ai1ec_Facebook_Graph_Object {
	private $_id;
	
	public function __construct( $id ) {
		$this->_id = $id;
	}
	/**
	 * Returns the Facebook name from the id
	 *
	 * @return string the Facebook name
	 */
	public function get_user_name() {
		global $wpdb;
		$table_name = Ai1ec_Facebook_Factory::get_plugin_table();
		$query = $wpdb->prepare( "SELECT user_name FROM $table_name WHERE user_id = %s", $this->_id );
		return $wpdb->get_var( $query );
	}
	/**
	 * Returns the category and tag for the current object
	 * 
	 * @return array an associativ array with the category and tag
	 */
	public function get_category_and_tag() {
		global $wpdb;
		$table_name = Ai1ec_Facebook_Factory::get_plugin_table();
		$query = $wpdb->prepare( "SELECT category, tag FROM $table_name WHERE user_id = %s", $this->_id );
		return $wpdb->get_row( $query, ARRAY_A  );
	}
	/**
	 * Retrieves the id of the events currently associated with the user.
	 * 
	 * @return array an array of post id
	 */
	public function get_events_from_user_event_table() {
		global $wpdb;
		$user_event_table = Ai1ec_Facebook_Factory::get_user_events_table();
		$query = $wpdb->prepare( "SELECT eid FROM $user_event_table WHERE user_id = %s", $this->_id );
		return $wpdb->get_col( $query );
	}
	/**
	 * Deletes the rows from the user_events table which are associated to the current object.
	 * 
	 * @return number
	 */
	public function delete_events_from_user_event_table() {
		global $wpdb;
		$user_event_table = Ai1ec_Facebook_Factory::get_user_events_table();
		$query = $wpdb->prepare( "DELETE FROM $user_event_table WHERE user_id = %s", $this->_id );
		$num_rows_deleted = $wpdb->query( $query );
		return $num_rows_deleted;
	}
}

?>