<?php

/**
 * @author time.ly
 *
 * This class handles all the possible actions of a collection of Facebook Graph Objects (users, pages or groups)
 */

class Ai1ec_Facebook_Graph_Object_Collection {
	// Identifyes users.
	const FB_USER     = 'user';
	// Identifies Pages
	const FB_PAGE     = 'page';
	// Identifies groups
	const FB_GROUP    = 'group';
	// Array are not allowed for class constants.
	static public $fb_all_types = array( self::FB_USER, self::FB_PAGE, self::FB_GROUP );
	/**
	 * The Type of the collection ( it's one of the predefined constants)
	 *
	 * @var string
	 */
	private $_type;
	/**
	 *
	 * @var Query_Events_Strategy_Interface
	 */
	private $_query_events_strategy;
	/**
	 * The strategy object to help syncing with facebook
	 *
	 * @var Sync_Objects_From_Facebook_Strategy_Interface
	 */
	private $_sync_objects_strategy;
	/**
	 *
	 * @var array
	 */
	private $_ids;
	/**
	 *
	 * @var int
	 */
	private $_category;
	/**
	 *
	 * @var string
	 */
	private $_tag;
	/**
	 *
	 * @var Ai1ec_Facebook_Current_User
	 */
	private $_facebook_user;
	/**
	 * An instance of the facebook class
	 *
	 * @var Facebook
	 */
	private $_facebook;

	/**
	 * @param Sync_Objects_From_Facebook_Strategy_Interface $_sync_objects_strategy
	 */
	public function set_sync_objects_strategy( Sync_Objects_From_Facebook_Strategy_Interface $_sync_objects_strategy ) {
		$this->_sync_objects_strategy = $_sync_objects_strategy;
	}

	/**
	 * @param Facebook $_facebook
	 */
	public function set_facebook( Facebook_WP_Extend_Ai1ec $_facebook ) {
		$this->_facebook = $_facebook;
	}

	/**
	 * @param Ai1ec_Facebook_Current_User $_facebook_user
	 */
	public function set_facebook_user( Ai1ec_Facebook_Current_User $_facebook_user ) {
		$this->_facebook_user = $_facebook_user;
	}
	/**
	 * @param string $_type
	 */
	public function set_type( $_type ) {
		$this->_type = $_type;
		// Set the strategy object for getting facebook events
		$this->set_query_events_strategy( Ai1ec_Facebook_Factory::generate_strategy_for_querying_events( $_type ) );
		// Set the strategy object for doing a sync.
		$this->set_sync_objects_strategy( Ai1ec_Facebook_Factory::generate_sync_object_strategy( $_type ) );
	}

	/**
	 * @return Query_Events_Strategy_Interface $_query_events_strategy
	 */
	public function get_query_events_strategy() {
		return $this->_query_events_strategy;
	}

	/**
	 * @param Query_Events_Strategy_Interface $_query_events_strategy
	 */
	public function set_query_events_strategy( Query_Events_Strategy_Interface $_query_events_strategy ) {
		$this->_query_events_strategy = $_query_events_strategy;
	}

	/**
	 * @param int $_category
	 */
	public function set_category( $_category ) {
		$this->_category = $_category;
	}

	/**
	 * @param string $_tag
	 */
	public function set_tag( $_tag ) {
		$this->_tag = $_tag;
	}

	public function __construct( array $ids ) {
		$this->_ids = $ids;
	}
	/**
	 * Get the escaped and pretty version of the type
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	static public function get_type_printable_text( $type ) {
		$text = '';
		switch ( $type ) {
			case self::FB_USER: $text = esc_html__( 'Friends', AI1EC_PLUGIN_NAME );
				break;
			case self::FB_PAGE: $text = esc_html__( 'Pages', AI1EC_PLUGIN_NAME );
				break;
			case self::FB_GROUP: $text = esc_html__( 'Groups', AI1EC_PLUGIN_NAME );
				break;
		}
		return $text;
	}
	/**
	 * Update the subscription status for the user passed.
	 *
	 * @param boolean $subscribe if TRUE the user is subscribed, if fals it's unsubscribed.
	 *
	 * @param boolean $remove_events if TRUE and you are unsubscribing, the user events are deleted
	 *
	 * @throws Ai1ec_Facebook_Db_Exception
	 *
	 * @return The number of deleted Post ( when you unsubscribe) or 0
	 */
	public function update_subscription_status( $subscribe = TRUE, $remove_events = FALSE ) {
		global $wpdb;
		$data = $this->_ids;
		$status = ( $subscribe === TRUE ) ? 1 : 0;
		$table_name = Ai1ec_Facebook_Factory::get_plugin_table();
		// Set the category and tag. The default for the category is 0, for tha tag an empty string.
		$category = isset( $this->_category ) ? $this->_category : 0;
		$tag = isset( $this->_tag ) ? $this->_tag : '';
		$user = array(
			'category'   => $this->_category,
			'tag'        => $this->_tag,
			'subscribed' => $status,
		);
		$error_messages = array();
		foreach( $data as $id ) {
			$result = $wpdb->update( $table_name, $user, array(
					'user_id' => $id,
			),
			array(
				'%d',
				'%s',
				'%d'
				 ),
			array( '%s' ) );
			if ( $result === FALSE ) {
				// Select the correct message
				$message = ( $subscribe === TRUE ) ? "An error occurred while subscribing to %s" : "An error occurred while subscribing from %s" ;
				// Create a new Facebook graph Object
				$fgo = Ai1ec_Facebook_Factory::get_facebook_graph_object( $id );
				// Add the error message
				$error_messages[] = sprintf( __( $message, AI1EC_PLUGIN_NAME ), $fgo->get_user_name() );
			} elseif ( $subscribe === FALSE && $remove_events === TRUE ) {
				// If no errors occured and if the user decided to delete events, do it now
				$how_many = $this->delete_events_for_user( $id );
			} elseif ( $subscribe === FALSE && $remove_events === FALSE ) {
				$this->delete_events_for_user( $id, FALSE );
			}
		}
		// If there are error messages, throw the appropriate Exception.
		if( ! empty( $error_messages ) ) {
			$e = new Ai1ec_Facebook_Db_Exception();
			$e->set_error_messages( $error_messages );
			throw $e;
		}
		// Return the number of deleted posts
		return isset( $how_many ) ? $how_many : 0;
	}
	/**
	 * Deletes the posts that are not referenced anymore in the user_events table.
	 *
	 * @param array $event_ids
	 *
	 * @return number the number of deleted post
	 */
	private function delete_events_not_referenced_by_facebook_users( array $event_ids ) {
		$count = 0;
		foreach ( $event_ids as $event_id ) {
			$facebook_event = Ai1ec_Facebook_Factory::get_facebook_event_instance();
			$facebook_event->set_id( $event_id );
			$referenced = $facebook_event->check_if_someone_refers_the_event_in_the_user_events_table();
			if( ! $referenced ) {
				$wp_id = $facebook_event->get_wordpress_post_id_from_facebook_event_id();
				$deleted = wp_delete_post( $wp_id );
				if( $deleted !== FALSE ) {
					$count++;
				}
			}
		}
		return $count;
	}
	/**
	 * Deletes the events for the user from the user_events table.
	 * Then it removes the events imported from facebook (They have a facebook_eid) which are not associated to a user.
	 *
	 * @param bigint $id facebook user id
	 *
	 * @param boolean whether to delete also the posts or only the events from the user_events table
	 *
	 * @return number|void Number of events deleted if the secound argument was FALSE
	 */
	private function delete_events_for_user( $id, $delete_posts = TRUE ) {
		global $wpdb;
		// Delete events from the user_events table

		$fgo = Ai1ec_Facebook_Factory::get_facebook_graph_object( $id );
		// Get the ids of the events that are associated with the user
		$event_ids = $fgo->get_events_from_user_event_table();
		// Delete the events.
		$num_rows_deleted = $fgo->delete_events_from_user_event_table();
		// If we must delete also POSTS, remove the wp posts and events will be removed automatically.
		if( $delete_posts ) {
			$count = $this->delete_events_not_referenced_by_facebook_users( $event_ids );
			return $count;
		}
	}
	/**
	 * Refreshes the events from facebook for the currently loaded ids
	 *
	 * @throws WP_FacebookApiException if something goes wrong with the Facebook calls
	 *
	 * @return array an array with the results.
	 */
	public function refresh_events() {
		$timestamp = strtotime( 'now' );
		// I use the strategy pattern for this, the common interface assure us that this method is present.
		try {
			$events = $this->_query_events_strategy->query_events( $this->_facebook, $this->_ids, $timestamp );
		} catch ( WP_FacebookApiException $e ) {
			throw $e;
		}
		$result = $this->save_events( $events, $timestamp );
		return $result;
	}
	/**
	 * Syncs data with facebook for the current type.
	 *
	 * @throws WP_FacebookApiException if something goes wrong when making Facebook api calls
	 *
	 * @throws Ai1ec_Facebook_Friends_Sync_Exception if something goes wrong while writing to the db
	 */
	public function sync_facebook_users() {
		$error_messages = array();
		try {
			$items = $this->_sync_objects_strategy->get_users_from_facebook( $this->_facebook );
			$this->update_facebook_users_db( $items );
		}
		catch ( WP_FacebookApiException $e ) {
			throw $e;
		}
		catch ( Ai1ec_Facebook_Db_Exception $e ) {
			// since both are indexed arrays, the result will include all errors;
			$error_messages = array_merge( $error_messages, $e->get_error_messages() );
		}
		// If there are error messages, throw the appropriate Exception.
		if( ! empty( $error_messages ) ) {
			$e = new Ai1ec_Facebook_Friends_Sync_Exception();
			$e->set_error_messages( $error_messages );
			throw $e;
		}
	}
	/**
	 * Updates / Insert data about users / pages / groups to the db when syncing data with Facebook.
	 *
	 * @param array $users The user to save
	 *
	 * @throws Ai1ec_Facebook_Db_Exception the exception is thrown if any of the inserts / updates returns an error
	 *
	 */
	private function update_facebook_users_db( array $users ) {
		global $wpdb;
		// We create an array that holds the error messages
		$error_messages = array();

		$table_name = Ai1ec_Facebook_Factory::get_plugin_table();
		// We get the ids that are already present in the db so that we now if we need to update or insert
		$table_ids = self::get_users_already_in_the_db( $users );
		// We iterate over the user that come from the FQL call.
		foreach ( $users as $id => $user ) {
			$result = 0;
			// If the user was already present in the db.
			if ( in_array( $id, $table_ids ) === TRUE ) {
				// Update it
				//unset($user['user_id']);
				$result = $wpdb->update( $table_name, $user, array(
						'user_id' => $id,
				), array(
						'%s',
						'%s',
						'%s',
						'%s',
				), array( '%s' ) );
			} else {
				// Else insert it.
				$result = $wpdb->insert( $table_name, $user, array(
						'%s',
						'%s',
						'%s',
						'%s',
				));
			}
			// Both update and insert return FALSE on error
			if( $result === FALSE ) {
				// Add an error message to the array.
				$error_messages[] = sprintf( __( "An error message occurred when saving %s data to the db", AI1EC_PLUGIN_NAME ), $user['user_name'] );
			}
		}
		// If there are error messages, throw the appropriate Exception.
		if( ! empty( $error_messages ) ) {
			$e = new Ai1ec_Facebook_Db_Exception();
			$e->set_error_messages( $error_messages );
			throw $e;
		}
	}
	/**
	 * Generate a variable string that can be used inside a prepared statement.
	 *
	 * @param int $how_many how many type descriptors we should have
	 *
	 * @param string $type the type descriptor
	 *
	 * @return string a string that can be used inside a prepared statement.
	 */
	private function generate_string_for_prepared_statement( $how_many, $type = '%s' ) {
		return "( " . str_repeat( "$type,", $how_many - 1 ) . " $type )";
	}
	/**
	 * Check which of the passed users are already present in the db.
	 *
	 * @param array $users the users to check
	 *
	 * @return array an array of user already present in the db
	 */
	private function get_users_already_in_the_db( array $users ) {
		global $wpdb;
		// The keys of the array are the ids
		$array_of_ids = array_keys( $users );
		$table_name = Ai1ec_Facebook_Factory::get_plugin_table();
		// Generate the string for the prepared statement
		$generated_escape_sequence = $this->generate_string_for_prepared_statement( count( $array_of_ids) );
		$query = $wpdb->prepare( "SELECT user_id
									FROM $table_name
									WHERE user_id IN $generated_escape_sequence ", $array_of_ids );
		return $wpdb->get_col( $query );
	}
	/**
	 * Get the events for the current user of the collection
	 *
	 * @param int $timestamp the time that was used to retrieve events from Facebook
	 *
	 * @return array the events present in the user_event table that start after the $timestamp
	 */
	private function get_events_from_user_events_table_for_collection( $timestamp ) {
		global $wpdb;
		$table_user_events = Ai1ec_Facebook_Factory::get_user_events_table();
		$generated_statement = $this->generate_string_for_prepared_statement( count( $this->_ids ) );
		// We need only future events.
		$query = $wpdb->prepare( "SELECT DISTINCT eid FROM $table_user_events WHERE user_id IN $generated_statement AND start > FROM_UNIXTIME( $timestamp )", $this->_ids );
		$event_ids = $wpdb->get_col( $query );
		return $event_ids;
	}
	/**
	 * Delete the events for the current ids that start after the passed timestamp
	 *
	 * @param int $timestamp the time that was used to retrieve events from Facebook
	 */
	private function delete_events_from_user_events_table_for_collection( $timestamp ) {
		global $wpdb;
		$table_user_events = Ai1ec_Facebook_Factory::get_user_events_table();
		$generated_statement = $this->generate_string_for_prepared_statement( count( $this->_ids ) );
		$query = $wpdb->prepare( "DELETE FROM $table_user_events WHERE user_id IN $generated_statement AND start > FROM_UNIXTIME( $timestamp ) ", $this->_ids );
		$wpdb->query( $query );
	}
	/**
	 * Handle saving / updating multiple events;
	 *
	 * @param array $events the Facebook events to save.
	 *
	 * @param int $timestamp the timestamp used to query events.
	 *
	 * @return array the result of the operation.
	 */
	private function save_events( array $events, $timestamp ) {
		$return = array(
				'type'            => '',
				'errors'          => FALSE,
				'events_inserted' => 0,
				'events_updated'  => 0,
				'events_deleted'  => 0,
		);
		// We set the type that it's either the nice name of the tape or the name of the user if we have only one id.
		if( count( $this->_ids ) > 1 ) {
			$return['type'] = $this->get_type_printable_text( $this->_type );
		} elseif( count( $this->_ids ) === 1 ) {
			$fgo = Ai1ec_Facebook_Factory::get_facebook_graph_object( $this->_ids[0] );
			$return['type'] = $fgo->get_user_name();
		}
		// Get the id of the event which are already in the db.
		$events_in_the_db = $this->get_events_from_user_events_table_for_collection( $timestamp );
		// Flip the events. We are only interested in knowing if an event we fetched from facebook was already in the db. If we flip we can do the check with isset().
		$events_in_the_db = array_flip( $events_in_the_db );
		// Delete th events so that later we can check if the one we just get are referenced.
		$this->delete_events_from_user_events_table_for_collection( $timestamp );
		foreach ( $events as $event ) {
			// Check if the event is already present in the db.
			$post_id = $this->get_post_id_from_eid( $event['eid'] );
			// Save data in the user_events table
			$table_user_update_ok = $this->handle_saving_in_user_events_table( $event['facebook_user'], $event['eid'], Ai1ec_Facebook_Event::convert_facebook_time_to_gmt_timestamp( $event['start_time'], $this->_facebook_user->get_timezone() ) );
			// Unset the event if it was present from the event which were already in the db.
			if( isset( $events_in_the_db[$event['eid']] ) ) {
				unset( $events_in_the_db[$event['eid']] );
			}
			// If something goes wrong signal the errors.
			if( ! $table_user_update_ok ) {
				$return['errors'] = TRUE;
			}
			try {
				// If the post id wasn't found, create a new event.
				if ( $post_id === NULL ) {
					$this->save_event( $event, $this->_facebook_user->get_timezone() );
					$return['events_inserted']++;
					// Otherwise update it.
				} else {
					$this->update_event( $event, $this->_facebook_user->get_timezone(), $post_id );
					$return['events_updated']++;
				}
			} catch (Exception $e) {
				// There is nothing we can do. Something must be wrong with the db. Maybe it's just related to a certain event while other went well.
				$return['errors'] = TRUE;
			}
		}
		// If not every event has been unset, we should delete them.
		if( count( $events_in_the_db ) ) {
			$deleted = $this->delete_events_not_referenced_by_facebook_users( array_keys($events_in_the_db) );
			$return['events_deleted'] = $deleted;
		}
		return $return;
	}
	/**
	 * Does nothing if the event is already present in the table, otherwis it insert it.
	 *
	 * @param bigint $user_id the id of the Facebook user
	 *
	 * @param bigint $eid the id of the Facebook event
	 *
	 * @param int $timestamp The starting time of the event
	 *
	 * @return boolean TRUE if no errors happened, FALSE otherwise.
	 */
	private function handle_saving_in_user_events_table( $user_id, $eid, $timestamp ) {
		global $wpdb;
		$table_name = Ai1ec_Facebook_Factory::get_user_events_table();
		// Check if we already have the event in the DB
		$query = $wpdb->prepare( "SELECT 'exist' FROM $table_name where user_id = %s AND eid = %s", $user_id, $eid );
		$exist = $wpdb->get_var( $query );
		// If the query returned something just return true
		if( $exist !== NULL ) {
			return TRUE;
		}
		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO $table_name VALUES ( %s, %s, FROM_UNIXTIME( %d ) )", $user_id, $eid, $timestamp ) );
		if( $result === FALSE ) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * Insert a new Ai1ec_Event into the db
	 *
	 * @param array $event The event coming from Facebook
	 *
	 * @param int $user_timezone the timezone of the currently logged on user
	 *
	 * @throws Exception If there was an error in saving the event
	 */
	private function save_event( array $event, $user_timezone ) {
		global $ai1ec_events_helper;
		// Convert the Facebook event to Ai1ec_Event.
		$ai1ec_event = $this->convert_facebook_event_to_ai1ec_event( $event, $user_timezone );
		// Save the event.
		$new_post_id = $ai1ec_event->save( FALSE );
		// Throw an Exception if something bad happened.
		if( $new_post_id === 0 ) {
			throw new Exception();
		}
		// Cache the new event if everything went well.
		$ai1ec_events_helper->cache_event( $ai1ec_event );
	}
	/**
	 * Convert a Facebook event into a Ai1ec Event.
	 *
	 * @param array $facebook_event The event to convert
	 *
	 * @param int $user_timezone the timezone of the user which is used to convert time
	 *
	 * @param int $post_id the id of the wp_post if we are updating
	 *
	 * @return Ai1ec_Event|array an Ai1ec object if we are performing an insert or an array if we ar updating
	 */
	private function convert_facebook_event_to_ai1ec_event( array $facebook_event, $user_timezone, $post_id = NULL ) {
		global $ai1ec_events_helper;
		// Create a new calendar event.
		$event = new Ai1ec_Event();
		// Set start time and end time after converting it to GMT
		if ( isset( $facebook_event['start_time'] ) ) {
			$event->start           = Ai1ec_Facebook_Event::convert_facebook_time_to_gmt_timestamp( $facebook_event['start_time'], $user_timezone );
		}
		if ( isset( $facebook_event['end_time'] ) && $facebook_event['end_time'] !== NULL ) {
			$event->end             = Ai1ec_Facebook_Event::convert_facebook_time_to_gmt_timestamp( $facebook_event['end_time'], $user_timezone );
		}
		// Check if the coordinates are set.
		$coordinates_set = isset( $facebook_event['venue']['longitude'] ) && isset( $facebook_event['venue']['latitude'] );

		// Convert the venue and location, setting an empty string if no value is set.
		$event->venue               = Ai1ec_Facebook_Data_Converter::return_empty_or_value_if_set( $facebook_event, 'location' );
		$event->address             = Ai1ec_Facebook_Data_Converter::return_empty_or_value_if_set( $facebook_event['venue'], 'street' );
		$event->city                = Ai1ec_Facebook_Data_Converter::return_empty_or_value_if_set( $facebook_event['venue'], 'city' );
		$event->province            = Ai1ec_Facebook_Data_Converter::return_empty_or_value_if_set( $facebook_event['venue'], 'state' );
		$event->postal_code         = Ai1ec_Facebook_Data_Converter::return_empty_or_value_if_set( $facebook_event['venue'], 'zip' );
		$event->country             = Ai1ec_Facebook_Data_Converter::return_empty_or_value_if_set( $facebook_event['venue'], 'country' );
		// We show the map if we have a valid address or we have coordinates. This is arbitrary.
		$event->show_map            = $coordinates_set || isset( $facebook_event['venue']['street'] );
		$event->show_coordinates    = $coordinates_set ? 1 : 0;
		if ( $coordinates_set ) {
			$event->longitude       = $facebook_event['venue']['longitude'];
			$event->latitude        = $facebook_event['venue']['latitude'];
		}
		// We save the user and eid.
		$event->facebook_user       = $facebook_event['facebook_user'];
		$event->facebook_eid        = $facebook_event['eid'];
		$event->facebook_status     = Ai1ecFacebookConnectorPlugin::FB_IMPORTED_EVENT;
		// We create the post array. This will create / update the wp_post.
		$post = array();
		$post['post_content']       = $facebook_event['description'];
		$post['post_title']         = $facebook_event['name'];
		$post['post_status']        = 'publish';
		$post['post_type']          = AI1EC_POST_TYPE;
		// If we are simply returning a new event to save .
		if ( $post_id === NULL ) {
			$fgo = Ai1ec_Facebook_Factory::get_facebook_graph_object( $facebook_event['facebook_user'] );
			$data = $fgo->get_category_and_tag();
			$event->categories      = $data['category'];
			$event->tags            = $data['tag'];
			// We just save the post to the variable post and the class will use it.
			$event->post            = $post;
			return $event;
		} else {
			// Otherwise we return an array.
			$post['ID'] = $post_id;
			$event->post_id = $post_id;
			$return_array = array(
					"post"  => $post,
					"event" => $event,
			);
			return $return_array;
		}
	}
	/**
	 * Loads the ids of the user that are subscribed for the current type. This is used by the CRON
	 *
	 * @return TRUE if at least one fgo was loaded, false otherwise
	 */
	public function load_subscribers_for_type() {
		global $wpdb;
		$table_name = Ai1ec_Facebook_Factory::get_plugin_table();
		$query = $wpdb->prepare( "SELECT user_id
									FROM $table_name
									WHERE subscribed = 1 AND type = %s", $this->_type );
		$ids = $wpdb->get_col( $query );
		$this->_ids = $ids;
		if( count( $ids ) > 0 ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	/**
	 * Update a Ai1ec event with data coming from Facebook
	 *
	 * @param array $event The event coming from Facebook
	 *
	 * @param int $user_timezone The timezone of the currently logged user.
	 *
	 * @param int $post_id the id of the post to update
	 *
	 * @throws Exception If something goes wrong while updating.
	 */
	private function update_event( array $event, $user_timezone, $post_id ) {
		// Create the event to be sure that it's not an event we exported to Facebook
		$event_to_check = new Ai1ec_Event( $post_id );

		// IF the event is not an imported event, do not touch it
		if( $event_to_check->facebook_status !== Ai1ecFacebookConnectorPlugin::FB_IMPORTED_EVENT ) {
			return;
		}
		global $ai1ec_events_helper;
		// Convert the data coming from Facebook into a Ai1ec_Event and a $post array
		$return_array = $this->convert_facebook_event_to_ai1ec_event( $event, $user_timezone, $post_id );
		// Update the event.
		$ai1ec_event = $return_array['event'];
		$ai1ec_event->save( TRUE );
		// Update the post.
		$return = wp_update_post( $return_array['post'] );
		if( $return === 0 ) {
			throw new Exception();
		}
		$ai1ec_events_helper->delete_event_cache( $post_id );
		$ai1ec_events_helper->cache_event( $ai1ec_event );
	}
	/**
	 * Get the post id from the event id
	 *
	 * @param int $eid the event id
	 *
	 * @return int|NULL the post id or NULL if nothing is found.
	 */
	private function get_post_id_from_eid( $eid ) {
		global $wpdb;
		$table_name = Ai1ec_Facebook_Factory::get_events_table();
		$query = $wpdb->prepare( "SELECT post_id FROM $table_name WHERE facebook_eid = %s", $eid );
		return $wpdb->get_var( $query );
	}
}
