<?php

/**
 * BuddyPress Activity Functions
 *
 * Functions for the Activity Streams component
 *
 * @package BuddyPress
 * @subpackage ActivityFunctions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks $bp pages global and looks for directory page
 *
 * @since 1.5.0
 *
 * @global object $bp BuddyPress global settings
 *
 * @return bool True if set, False if empty
 */
function bp_activity_has_directory() {
	global $bp;

	return (bool) !empty( $bp->pages->activity->id );
}

/**
 * Searches through the content of an activity item to locate usernames, designated by an @ sign
 *
 * @since 1.5.0
 *
 * @param string $content The content of the activity, usually found in $activity->content
 *
 * @return bool|array $usernames Array of the found usernames that match existing users. False if no matches
 */
function bp_activity_find_mentions( $content ) {
	$pattern = '/[@]+([A-Za-z0-9-_\.@]+)/';
	preg_match_all( $pattern, $content, $usernames );

	// Make sure there's only one instance of each username
	if ( !$usernames = array_unique( $usernames[1] ) )
		return false;

	return $usernames;
}

/**
 * Resets a user's unread mentions list and count
 *
 * @since 1.5.0
 *
 * @param int $user_id The id of the user whose unread mentions are being reset
 * @uses bp_delete_user_meta()
 */
function bp_activity_clear_new_mentions( $user_id ) {
	bp_delete_user_meta( $user_id, 'bp_new_mention_count' );
	bp_delete_user_meta( $user_id, 'bp_new_mentions' );
}

/**
 * Adjusts new mention count for mentioned users when activity items are deleted or created
 *
 * @since 1.5.0
 *
 * @param int $activity_id The unique id for the activity item
 * @param string $action Can be 'delete' or 'add'. Defaults to 'add'
 *
 * @uses BP_Activity_Activity() {@link BP_Activity_Activity}
 * @uses bp_activity_find_mentions()
 * @uses bp_is_username_compatibility_mode()
 * @uses bp_core_get_userid_from_nicename()
 * @uses bp_get_user_meta()
 * @uses bp_update_user_meta()
 */
function bp_activity_adjust_mention_count( $activity_id, $action = 'add' ) {
	$activity = new BP_Activity_Activity( $activity_id );

	if ( $usernames = bp_activity_find_mentions( strip_tags( $activity->content ) ) ) {
		foreach( (array)$usernames as $username ) {
			if ( bp_is_username_compatibility_mode() )
				$user_id = username_exists( $username );
			else
				$user_id = bp_core_get_userid_from_nicename( $username );

			if ( empty( $user_id ) )
				continue;

			// Adjust the mention list and count for the member
			$new_mention_count = (int)bp_get_user_meta( $user_id, 'bp_new_mention_count', true );
			if ( !$new_mentions = bp_get_user_meta( $user_id, 'bp_new_mentions', true ) )
				$new_mentions = array();

			switch ( $action ) {
				case 'delete' :
					$key = array_search( $activity_id, $new_mentions );
					if ( $key !== false ) {
						unset( $new_mentions[$key] );
					}
					break;

				case 'add' :
				default :
					if ( !in_array( $activity_id, $new_mentions ) ) {
						$new_mentions[] = (int)$activity_id;
					}
					break;
			}

			// Get an updated mention count
			$new_mention_count = count( $new_mentions );

			// Resave the user_meta
			bp_update_user_meta( $user_id, 'bp_new_mention_count', $new_mention_count );
			bp_update_user_meta( $user_id, 'bp_new_mentions', $new_mentions );
		}
	}
}

/**
 * Formats notifications related to activity
 *
 * @since 1.5.0
 *
 * @param string $action The type of activity item. Just 'new_at_mention' for now
 * @param int $item_id The activity id
 * @param int $secondary_item_id In the case of at-mentions, this is the mentioner's id
 * @param int $total_items The total number of notifications to format
 * @param string $format 'string' to get a BuddyBar-compatible notification, 'array' otherwise
 *
 * @global object $bp BuddyPress global settings
 * @uses bp_loggedin_user_domain()
 * @uses bp_get_activity_slug()
 * @uses bp_core_get_user_displayname()
 * @uses apply_filters() To call the 'bp_activity_multiple_at_mentions_notification' hook
 * @uses apply_filters() To call the 'bp_activity_single_at_mentions_notification' hook
 * @uses do_action() To call 'activity_format_notifications' hook
 *
 * @return string $return Formatted @mention notification
 */
function bp_activity_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {
	global $bp;

	switch ( $action ) {
		case 'new_at_mention':
			$activity_id      = $item_id;
			$poster_user_id   = $secondary_item_id;
			$at_mention_link  = bp_loggedin_user_domain() . bp_get_activity_slug() . '/mentions/';
			$at_mention_title = sprintf( __( '@%s Mentions', 'buddypress' ), $bp->loggedin_user->userdata->user_nicename );

			if ( (int)$total_items > 1 ) {
				$text = sprintf( __( 'You have %1$d new activity mentions', 'buddypress' ), (int)$total_items );
				$filter = 'bp_activity_multiple_at_mentions_notification';
			} else {
				$user_fullname = bp_core_get_user_displayname( $poster_user_id );
				$text =  sprintf( __( '%1$s mentioned you in an activity update', 'buddypress' ), $user_fullname );
				$filter = 'bp_activity_single_at_mentions_notification';
			}
		break;
	}

	if ( 'string' == $format ) {
		$return = apply_filters( $filter, '<a href="' . $at_mention_link . '" title="' . $at_mention_title . '">' . $text . '</a>', $at_mention_link, (int)$total_items, $activity_id, $poster_user_id );
	} else {
		$return = apply_filters( $filter, array(
			'text' => $text,
			'link' => $at_mention_link
		), $at_mention_link, (int)$total_items, $activity_id, $poster_user_id );
	}

	do_action( 'activity_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return $return;
}

/** Actions ******************************************************************/

/**
 * Sets the current action for a given activity stream location
 *
 * @since 1.1.0
 *
 * @param string $component_id
 * @param string $key
 * @param string $value
 *
 * @global object $bp BuddyPress global settings
 * @uses apply_filters() To call the 'bp_activity_set_action' hook
 *
 * @return bool False if any param is empty, otherwise true
 */
function bp_activity_set_action( $component_id, $key, $value ) {
	global $bp;

	// Return false if any of the above values are not set
	if ( empty( $component_id ) || empty( $key ) || empty( $value ) )
		return false;

	// Set activity action
	$bp->activity->actions->{$component_id}->{$key} = apply_filters( 'bp_activity_set_action', array(
		'key'   => $key,
		'value' => $value
	), $component_id, $key, $value );

	return true;
}

/**
 * Retreives the current action from a component and key
 *
 * @since 1.1.0
 *
 * @param string $component_id
 * @param string $key
 *
 * @global object $bp BuddyPress global settings
 * @uses apply_filters() To call the 'bp_activity_get_action' hook
 *
 * @return mixed False on error, action on success
 */
function bp_activity_get_action( $component_id, $key ) {
	global $bp;

	// Return false if any of the above values are not set
	if ( empty( $component_id ) || empty( $key ) )
		return false;

	return apply_filters( 'bp_activity_get_action', $bp->activity->actions->{$component_id}->{$key}, $component_id, $key );
}

/** Favorites ****************************************************************/

/**
 * Get a users favorite activity stream items
 *
 * @since 1.2.0
 *
 * @param int $user_id
 *
 * @global object $bp BuddyPress global settings
 * @uses bp_get_user_meta()
 * @uses apply_filters() To call the 'bp_activity_get_user_favorites' hook
 *
 * @return array Array of users favorite activity stream ID's
 */
function bp_activity_get_user_favorites( $user_id = 0 ) {
	global $bp;

	// Fallback to logged in user if no user_id is passed
	if ( empty( $user_id ) )
		$user_id = $bp->displayed_user->id;

	// Get favorites for user
	$favs = bp_get_user_meta( $user_id, 'bp_favorite_activities', true );

	return apply_filters( 'bp_activity_get_user_favorites', $favs );
}

/**
 * Add an activity stream item as a favorite for a user
 *
 * @since 1.2.0
 *
 * @param int $activity_id
 * @param int $user_id
 *
 * @global object $bp BuddyPress global settings
 * @uses is_user_logged_in()
 * @uses bp_get_user_meta()
 * @uses bp_activity_get_meta()
 * @uses bp_update_user_meta()
 * @uses bp_activity_update_meta()
 * @uses do_action() To call the 'bp_activity_add_user_favorite' hook
 * @uses do_action() To call the 'bp_activity_add_user_favorite_fail' hook
 *
 * @return bool True on success, false on failure
 */
function bp_activity_add_user_favorite( $activity_id, $user_id = 0 ) {
	global $bp;

	// Favorite activity stream items are for logged in users only
	if ( !is_user_logged_in() )
		return false;

	// Fallback to logged in user if no user_id is passed
	if ( empty( $user_id ) )
		$user_id = $bp->loggedin_user->id;

	// Update the user's personal favorites
	$my_favs   = bp_get_user_meta( $bp->loggedin_user->id, 'bp_favorite_activities', true );
	$my_favs[] = $activity_id;

	// Update the total number of users who have favorited this activity
	$fav_count = bp_activity_get_meta( $activity_id, 'favorite_count' );
	$fav_count = !empty( $fav_count ) ? (int)$fav_count + 1 : 1;

	// Update user meta
	bp_update_user_meta( $bp->loggedin_user->id, 'bp_favorite_activities', $my_favs );

	// Update activity meta counts
	if ( true === bp_activity_update_meta( $activity_id, 'favorite_count', $fav_count ) ) {

		// Execute additional code
		do_action( 'bp_activity_add_user_favorite', $activity_id, $user_id );

		// Success
		return true;

	// Saving meta was unsuccessful for an unknown reason
	} else {
		// Execute additional code
		do_action( 'bp_activity_add_user_favorite_fail', $activity_id, $user_id );

		return false;
	}
}

/**
 * Remove an activity stream item as a favorite for a user
 *
 * @since 1.2.0
 *
 * @param int $activity_id
 * @param int $user_id
 *
 * @global object $bp BuddyPress global settings
 * @uses is_user_logged_in()
 * @uses bp_get_user_meta()
 * @uses bp_activity_get_meta()
 * @uses bp_activity_update_meta()
 * @uses bp_update_user_meta()
 * @uses do_action() To call the 'bp_activity_remove_user_favorite' hook
 *
 * @return bool True on success, false on failure
 */
function bp_activity_remove_user_favorite( $activity_id, $user_id = 0 ) {
	global $bp;

	// Favorite activity stream items are for logged in users only
	if ( !is_user_logged_in() )
		return false;

	// Fallback to logged in user if no user_id is passed
	if ( empty( $user_id ) )
		$user_id = $bp->loggedin_user->id;

	// Remove the fav from the user's favs
	$my_favs = bp_get_user_meta( $user_id, 'bp_favorite_activities', true );
	$my_favs = array_flip( (array) $my_favs );
	unset( $my_favs[$activity_id] );
	$my_favs = array_unique( array_flip( $my_favs ) );

	// Update the total number of users who have favorited this activity
	if ( $fav_count = bp_activity_get_meta( $activity_id, 'favorite_count' ) ) {

		// Deduct from total favorites
		if ( bp_activity_update_meta( $activity_id, 'favorite_count', (int)$fav_count - 1 ) ) {

			// Update users favorites
			if ( bp_update_user_meta( $user_id, 'bp_favorite_activities', $my_favs ) ) {

				// Execute additional code
				do_action( 'bp_activity_remove_user_favorite', $activity_id, $user_id );

				// Success
				return true;

			// Error updating
			} else {
				return false;
			}

		// Error updating favorite count
		} else {
			return false;
		}

	// Error getting favorite count
	} else {
		return false;
	}
}

/**
 * Check if activity exists by scanning content
 *
 * @since 1.1.0
 *
 * @param string $content
 *
 * @uses BP_Activity_Activity::check_exists_by_content() {@link BP_Activity_Activity}
 * @uses apply_filters() To call the 'bp_activity_check_exists_by_content' hook
 *
 * @return bool
 */
function bp_activity_check_exists_by_content( $content ) {
	return apply_filters( 'bp_activity_check_exists_by_content', BP_Activity_Activity::check_exists_by_content( $content ) );
}

/**
 * Retrieve the last time activity was updated
 *
 * @since 1.0.0
 *
 * @uses BP_Activity_Activity::get_last_updated() {@link BP_Activity_Activity}
 * @uses apply_filters() To call the 'bp_activity_get_last_updated' hook
 *
 * @return string Date last updated
 */
function bp_activity_get_last_updated() {
	return apply_filters( 'bp_activity_get_last_updated', BP_Activity_Activity::get_last_updated() );
}

/**
 * Retrieve the number of favorite activity stream items a user has
 *
 * @since 1.2.0
 *
 * @param int $user_id
 *
 * @global object $bp BuddyPress global settings
 * @uses BP_Activity_Activity::total_favorite_count() {@link BP_Activity_Activity}
 *
 * @return int Total favorite count
 */
function bp_activity_total_favorites_for_user( $user_id = 0 ) {
	global $bp;

	// Fallback on displayed user, and then logged in user
	if ( empty( $user_id ) )
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;

	return BP_Activity_Activity::total_favorite_count( $user_id );
}

/** Meta *********************************************************************/

/**
 * Delete a meta entry from the DB for an activity stream item
 *
 * @since 1.2.0
 *
 * @param int $activity_id
 * @param string $meta_key
 * @param string $meta_value
 *
 * @global object $wpdb
 * @global object $bp BuddyPress global settings
 * @uses wp_cache_delete()
 * @uses is_wp_error()
 *
 * @return bool True on success, false on failure
 */
function bp_activity_delete_meta( $activity_id, $meta_key = '', $meta_value = '' ) {
	global $wpdb, $bp;

	// Return false if any of the above values are not set
	if ( !is_numeric( $activity_id ) )
		return false;

	// Sanitize key
	$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );

	if ( is_array( $meta_value ) || is_object( $meta_value ) )
		$meta_value = serialize( $meta_value );

	// Trim off whitespace
	$meta_value = trim( $meta_value );

	// Delete all for activity_id
	if ( empty( $meta_key ) )
		$retval = $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->activity->table_name_meta} WHERE activity_id = %d", $activity_id ) );

	// Delete only when all match
	else if ( $meta_value )
		$retval = $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->activity->table_name_meta} WHERE activity_id = %d AND meta_key = %s AND meta_value = %s", $activity_id, $meta_key, $meta_value ) );

	// Delete only when activity_id and meta_key match
	else
		$retval = $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->activity->table_name_meta} WHERE activity_id = %d AND meta_key = %s", $activity_id, $meta_key ) );

	// Delete cache entry
	wp_cache_delete( 'bp_activity_meta_' . $meta_key . '_' . $activity_id, 'bp' );

	// Success
	if ( !is_wp_error( $retval ) )
		return true;

	// Fail
	else
		return false;
}

/**
 * Get activity meta
 *
 * @since 1.2.0
 *
 * @param int $activity_id
 * @param string $meta_key
 *
 * @global object $wpdb
 * @global object $bp BuddyPress global settings
 * @uses wp_cache_get()
 * @uses wp_cache_set()
 * @uses apply_filters() To call the 'bp_activity_get_meta' hook
 *
 * @return bool
 */
function bp_activity_get_meta( $activity_id = 0, $meta_key = '' ) {
	global $wpdb, $bp;

	// Make sure activity_id is valid
	if ( empty( $activity_id ) || !is_numeric( $activity_id ) )
		return false;

	// We have a key to look for
	if ( !empty( $meta_key ) ) {

		// Sanitize key
		$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );

		// Check cache
		if ( !$metas = wp_cache_get( 'bp_activity_meta_' . $meta_key . '_' . $activity_id, 'bp' ) ) {

			// No cache so hit the DB
			$metas = $wpdb->get_col( $wpdb->prepare("SELECT meta_value FROM {$bp->activity->table_name_meta} WHERE activity_id = %d AND meta_key = %s", $activity_id, $meta_key ) );

			// Set cache
			wp_cache_set( 'bp_activity_meta_' . $meta_key . '_' . $activity_id, $metas, 'bp' );
		}

	// No key so get all for activity_id
	} else {
		$metas = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$bp->activity->table_name_meta} WHERE activity_id = %d", $activity_id ) );
	}

	// No result so return false
	if ( empty( $metas ) )
		return false;

	// Maybe, just maybe... unserialize
	$metas = array_map( 'maybe_unserialize', (array)$metas );

	// Return first item in array if only 1, else return all metas found
	$retval = ( 1 == count( $metas ) ? $metas[0] : $metas );

	// Filter result before returning
	return apply_filters( 'bp_activity_get_meta', $retval, $activity_id, $meta_key );
}

/**
 * Update activity meta
 *
 * @since 1.2.0
 *
 * @param int $activity_id
 * @param string $meta_key
 * @param string $meta_value
 *
 * @global object $wpdb
 * @global object $bp BuddyPress global settings
 * @uses maybe_serialize()
 * @uses bp_activity_delete_meta()
 * @uses wp_cache_set()
 *
 * @return bool True on success, false on failure
 */
function bp_activity_update_meta( $activity_id, $meta_key, $meta_value ) {
	global $wpdb, $bp;

	// Make sure activity_id is valid
	if ( !is_numeric( $activity_id ) )
		return false;

	// Sanitize key
	$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );

	// Sanitize value
	if ( is_string( $meta_value ) )
		$meta_value = stripslashes( $wpdb->escape( $meta_value ) );

	// Maybe, just maybe... serialize
	$meta_value = maybe_serialize( $meta_value );

	// If value is empty, delete the meta key
	if ( empty( $meta_value ) )
		return bp_activity_delete_meta( $activity_id, $meta_key );

	// See if meta key exists for activity_id
	$cur = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->activity->table_name_meta} WHERE activity_id = %d AND meta_key = %s", $activity_id, $meta_key ) );

	// Meta key does not exist so INSERT
	if ( empty( $cur ) )
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->activity->table_name_meta} ( activity_id, meta_key, meta_value ) VALUES ( %d, %s, %s )", $activity_id, $meta_key, $meta_value ) );

	// Meta key exists, so UPDATE
	else if ( $cur->meta_value != $meta_value )
		$wpdb->query( $wpdb->prepare( "UPDATE {$bp->activity->table_name_meta} SET meta_value = %s WHERE activity_id = %d AND meta_key = %s", $meta_value, $activity_id, $meta_key ) );

	// Weirdness, so return false
	else
		return false;

	// Set cache
	wp_cache_set( 'bp_activity_meta_' . $meta_key . '_' . $activity_id, $meta_value, 'bp' );

	// Victory is ours!
	return true;
}

/** Clean up *****************************************************************/

/**
 * Completely remove a user's activity data
 *
 * @since 1.5.0
 *
 * @param int $user_id
 *
 * @uses is_user_logged_in()
 * @uses bp_activity_delete()
 * @uses bp_delete_user_meta()
 * @uses do_action() To call the 'bp_activity_remove_data' hook
 * @uses do_action() To call the 'bp_activity_remove_all_user_data' hook
 */
function bp_activity_remove_all_user_data( $user_id = 0 ) {

	// Do not delete user data unless a logged in user says so
	if ( empty( $user_id ) || !is_user_logged_in() )
		return false;

	// Clear the user's activity from the sitewide stream and clear their activity tables
	bp_activity_delete( array( 'user_id' => $user_id ) );

	// Remove any usermeta
	bp_delete_user_meta( $user_id, 'bp_latest_update' );
	bp_delete_user_meta( $user_id, 'bp_favorite_activities' );

	// Execute additional code
	do_action( 'bp_activity_remove_data', $user_id ); // Deprecated! Do not use!

	// Use this going forward
	do_action( 'bp_activity_remove_all_user_data', $user_id );
}
add_action( 'wpmu_delete_user',  'bp_activity_remove_all_user_data' );
add_action( 'delete_user',       'bp_activity_remove_all_user_data' );
add_action( 'bp_make_spam_user', 'bp_activity_remove_all_user_data' );

/**
 * Register the activity stream actions for updates
 *
 * @since 1.2.0
 *
 * @global object $bp BuddyPress global settings
 * @uses bp_activity_set_action()
 * @uses do_action() To call the 'updates_register_activity_actions' hook
 */
function updates_register_activity_actions() {
	global $bp;

	bp_activity_set_action( $bp->activity->id, 'activity_update', __( 'Posted an update', 'buddypress' ) );

	do_action( 'updates_register_activity_actions' );
}
add_action( 'bp_register_activity_actions', 'updates_register_activity_actions' );

/******************************************************************************
 * Business functions are where all the magic happens in BuddyPress. They will
 * handle the actual saving or manipulation of information. Usually they will
 * hand off to a database class for data access, then return
 * true or false on success or failure.
 */

/**
 * Retrieve an activity or activities
 *
 * @since 1.2.0
 *
 * @param array $args
 *
 * @uses wp_parse_args()
 * @uses wp_cache_get()
 * @uses wp_cache_set()
 * @uses BP_Activity_Activity::get() {@link BP_Activity_Activity}
 * @uses apply_filters_ref_array() To call the 'bp_activity_get' hook
 *
 * @return object $activity The activity/activities object
 */
function bp_activity_get( $args = '' ) {
	$defaults = array(
		'max'              => false,  // Maximum number of results to return
		'page'             => 1,      // page 1 without a per_page will result in no pagination.
		'per_page'         => false,  // results per page
		'sort'             => 'DESC', // sort ASC or DESC
		'display_comments' => false,  // false for no comments. 'stream' for within stream display, 'threaded' for below each activity item

		'search_terms'     => false,  // Pass search terms as a string
		'show_hidden'      => false,  // Show activity items that are hidden site-wide?
		'exclude'          => false,  // Comma-separated list of activity IDs to exclude
		'in'               => false,  // Comma-separated list or array of activity IDs to which you want to limit the query

		/**
		 * Pass filters as an array -- all filter items can be multiple values comma separated:
		 * array(
		 * 	'user_id'      => false, // user_id to filter on
		 *	'object'       => false, // object to filter on e.g. groups, profile, status, friends
		 *	'action'       => false, // action to filter on e.g. activity_update, profile_updated
		 *	'primary_id'   => false, // object ID to filter on e.g. a group_id or forum_id or blog_id etc.
		 *	'secondary_id' => false, // secondary object ID to filter on e.g. a post_id
		 * );
		 */
		'filter' => array()
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	// Attempt to return a cached copy of the first page of sitewide activity.
	if ( 1 == (int)$page && empty( $max ) && empty( $search_terms ) && empty( $filter ) && 'DESC' == $sort && empty( $exclude ) ) {
		if ( !$activity = wp_cache_get( 'bp_activity_sitewide_front', 'bp' ) ) {
			$activity = BP_Activity_Activity::get( $max, $page, $per_page, $sort, $search_terms, $filter, $display_comments, $show_hidden );
			wp_cache_set( 'bp_activity_sitewide_front', $activity, 'bp' );
		}
	} else
		$activity = BP_Activity_Activity::get( $max, $page, $per_page, $sort, $search_terms, $filter, $display_comments, $show_hidden, $exclude, $in );

	return apply_filters_ref_array( 'bp_activity_get', array( &$activity, &$r ) );
}

/**
 * Fetch specific activity items
 *
 * @since 1.2.0
 *
 * @param array $args See docs for $defaults for details
 *
 * @uses wp_parse_args()
 * @uses apply_filters() To call the 'bp_activity_get_specific' hook
 * @uses BP_Activity_Activity::get() {@link BP_Activity_Activity}
 *
 * @return array The array returned by BP_Activity_Activity::get()
 */
function bp_activity_get_specific( $args = '' ) {
	$defaults = array(
		'activity_ids'     => false,  // A single activity_id or array of IDs.
		'page'             => 1,      // page 1 without a per_page will result in no pagination.
		'per_page'         => false,  // results per page
		'max'              => false,  // Maximum number of results to return
		'sort'             => 'DESC', // sort ASC or DESC
		'display_comments' => false,  // true or false to display threaded comments for these specific activity items
		'show_hidden'      => true    // When fetching specific items, show all
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	return apply_filters( 'bp_activity_get_specific', BP_Activity_Activity::get( $max, $page, $per_page, $sort, false, false, $display_comments, $show_hidden, false, $activity_ids ) );
}

/**
 * Add an activity item
 *
 * @since 1.1.0
 *
 * @param array $args See docs for $defaults for details
 *
 * @global object $bp BuddyPress global settings
 * @uses wp_parse_args()
 * @uses BP_Activity_Activity::save() {@link BP_Activity_Activity}
 * @uses BP_Activity_Activity::rebuild_activity_comment_tree() {@link BP_Activity_Activity}
 * @uses wp_cache_delete()
 * @uses do_action() To call the 'bp_activity_add' hook
 *
 * @return int The activity id
 */
function bp_activity_add( $args = '' ) {
	global $bp;

	$defaults = array(
		'id'                => false, // Pass an existing activity ID to update an existing entry.

		'action'            => '',    // The activity action - e.g. "Jon Doe posted an update"
		'content'           => '',    // Optional: The content of the activity item e.g. "BuddyPress is awesome guys!"

		'component'         => false, // The name/ID of the component e.g. groups, profile, mycomponent
		'type'              => false, // The activity type e.g. activity_update, profile_updated
		'primary_link'      => '',    // Optional: The primary URL for this item in RSS feeds (defaults to activity permalink)

		'user_id'           => $bp->loggedin_user->id, // Optional: The user to record the activity for, can be false if this activity is not for a user.
		'item_id'           => false, // Optional: The ID of the specific item being recorded, e.g. a blog_id
		'secondary_item_id' => false, // Optional: A second ID used to further filter e.g. a comment_id
		'recorded_time'     => bp_core_current_time(), // The GMT time that this activity was recorded
		'hide_sitewide'     => false  // Should this be hidden on the sitewide activity stream?
	);
	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	// Make sure we are backwards compatible
	if ( empty( $component ) && !empty( $component_name ) )
		$component = $component_name;

	if ( empty( $type ) && !empty( $component_action ) )
		$type = $component_action;

	// Setup activity to be added
	$activity                    = new BP_Activity_Activity( $id );
	$activity->user_id           = $user_id;
	$activity->component         = $component;
	$activity->type              = $type;
	$activity->action            = $action;
	$activity->content           = $content;
	$activity->primary_link      = $primary_link;
	$activity->item_id           = $item_id;
	$activity->secondary_item_id = $secondary_item_id;
	$activity->date_recorded     = $recorded_time;
	$activity->hide_sitewide     = $hide_sitewide;

	if ( !$activity->save() )
		return false;

	// If this is an activity comment, rebuild the tree
	if ( 'activity_comment' == $activity->type )
		BP_Activity_Activity::rebuild_activity_comment_tree( $activity->item_id );

	wp_cache_delete( 'bp_activity_sitewide_front', 'bp' );
	do_action( 'bp_activity_add', $params );

	return $activity->id;
}

/**
 * Post an activity update
 *
 * @since 1.2.0
 *
 * @param array $args See docs for $defaults for details
 *
 * @global object $bp BuddyPress global settings
 * @uses wp_parse_args()
 * @uses bp_core_is_user_spammer()
 * @uses bp_core_is_user_deleted()
 * @uses bp_core_get_userlink()
 * @uses bp_activity_add()
 * @uses apply_filters() To call the 'bp_activity_new_update_action' hook
 * @uses apply_filters() To call the 'bp_activity_new_update_content' hook
 * @uses apply_filters() To call the 'bp_activity_new_update_primary_link' hook
 * @uses bp_update_user_meta()
 * @uses wp_filter_kses()
 * @uses do_action() To call the 'bp_activity_posted_update' hook
 *
 * @return int $activity_id The activity id
 */
function bp_activity_post_update( $args = '' ) {
	global $bp;

	$defaults = array(
		'content' => false,
		'user_id' => $bp->loggedin_user->id
	);
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( empty( $content ) || !strlen( trim( $content ) ) )
		return false;

	if ( bp_core_is_user_spammer( $user_id ) || bp_core_is_user_deleted( $user_id ) )
		return false;

	// Record this on the user's profile
	$from_user_link   = bp_core_get_userlink( $user_id );
	$activity_action  = sprintf( __( '%s posted an update', 'buddypress' ), $from_user_link );
	$activity_content = $content;
	$primary_link     = bp_core_get_userlink( $user_id, false, true );

	// Now write the values
	$activity_id = bp_activity_add( array(
		'user_id'      => $user_id,
		'action'       => apply_filters( 'bp_activity_new_update_action', $activity_action ),
		'content'      => apply_filters( 'bp_activity_new_update_content', $activity_content ),
		'primary_link' => apply_filters( 'bp_activity_new_update_primary_link', $primary_link ),
		'component'    => $bp->activity->id,
		'type'         => 'activity_update'
	) );

	// Add this update to the "latest update" usermeta so it can be fetched anywhere.
	bp_update_user_meta( $bp->loggedin_user->id, 'bp_latest_update', array( 'id' => $activity_id, 'content' => wp_filter_kses( $content ) ) );

	do_action( 'bp_activity_posted_update', $content, $user_id, $activity_id );

	return $activity_id;
}

/**
 * Add an activity comment
 *
 * @since 1.2.0
 *
 * @param array $args See docs for $defaults for details
 *
 * @global object $bp BuddyPress global settings
 * @uses wp_parse_args()
 * @uses bp_activity_add()
 * @uses apply_filters() To call the 'bp_activity_comment_action' hook
 * @uses apply_filters() To call the 'bp_activity_comment_content' hook
 * @uses bp_activity_new_comment_notification()
 * @uses wp_cache_delete()
 * @uses do_action() To call the 'bp_activity_comment_posted' hook
 *
 * @return int $comment_id The comment id
 */
function bp_activity_new_comment( $args = '' ) {
	global $bp;

	$defaults = array(
		'id'          => false,
		'content'     => false,
		'user_id'     => $bp->loggedin_user->id,
		'activity_id' => false, // ID of the root activity item
		'parent_id'   => false  // ID of a parent comment (optional)
	);

	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	if ( empty($content) || empty($user_id) || empty($activity_id) )
		return false;

	if ( empty($parent_id) )
		$parent_id = $activity_id;

	// Check to see if the parent activity is hidden, and if so, hide this comment publically.
	$activity = new BP_Activity_Activity( $activity_id );
	$is_hidden = ( (int)$activity->hide_sitewide ) ? 1 : 0;

	// Insert the activity comment
	$comment_id = bp_activity_add( array(
		'id' => $id,
		'action' => apply_filters( 'bp_activity_comment_action', sprintf( __( '%s posted a new activity comment', 'buddypress' ), bp_core_get_userlink( $user_id ) ) ),
		'content' => apply_filters( 'bp_activity_comment_content', $content ),
		'component' => $bp->activity->id,
		'type' => 'activity_comment',
		'user_id' => $user_id,
		'item_id' => $activity_id,
		'secondary_item_id' => $parent_id,
		'hide_sitewide' => $is_hidden
	) );

	// Send an email notification if settings allow
	bp_activity_new_comment_notification( $comment_id, $user_id, $params );

	// Clear the comment cache for this activity
	wp_cache_delete( 'bp_activity_comments_' . $parent_id );

	do_action( 'bp_activity_comment_posted', $comment_id, $params );

	return $comment_id;
}

/**
 * Fetch the activity_id for an existing activity entry in the DB.
 *
 * @since 1.2.0
 *
 * @param array $args See docs for $defaults for details
 *
 * @uses wp_parse_args()
 * @uses apply_filters() To call the 'bp_activity_get_activity_id' hook
 * @uses BP_Activity_Activity::save() {@link BP_Activity_Activity}
 *
 * @return int $activity_id The activity id
 */
function bp_activity_get_activity_id( $args = '' ) {
	$defaults = array(
		'user_id'           => false,
		'component'         => false,
		'type'              => false,
		'item_id'           => false,
		'secondary_item_id' => false,
		'action'            => false,
		'content'           => false,
		'date_recorded'     => false,
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

 	return apply_filters( 'bp_activity_get_activity_id', BP_Activity_Activity::get_id( $user_id, $component, $type, $item_id, $secondary_item_id, $action, $content, $date_recorded ) );
}

/**
 * Deleting Activity
 *
 * If you're looking to hook into one action that provides the ID(s) of
 * the activity/activities deleted, then use:
 *
 * add_action( 'bp_activity_deleted_activities', 'my_function' );
 *
 * The action passes one parameter that is a single activity ID or an
 * array of activity IDs depending on the number deleted.
 *
 * If you are deleting an activity comment please use bp_activity_delete_comment();
 *
 * @since 1.0.0
 *
 * @param array $args See docs for $defaults for details
 *
 * @global object $bp BuddyPress global settings
 * @uses wp_parse_args()
 * @uses bp_activity_adjust_mention_count()
 * @uses BP_Activity_Activity::delete() {@link BP_Activity_Activity}
 * @uses do_action() To call the 'bp_before_activity_delete' hook
 * @uses bp_get_user_meta()
 * @uses bp_delete_user_meta()
 * @uses do_action() To call the 'bp_activity_delete' hook
 * @uses do_action() To call the 'bp_activity_deleted_activities' hook
 * @uses wp_cache_delete()
 *
 * @return bool True on success, false on failure
 */
function bp_activity_delete( $args = '' ) {
	global $bp;

	// Pass one or more the of following variables to delete by those variables
	$defaults = array(
		'id'                => false,
		'action'            => false,
		'content'           => false,
		'component'         => false,
		'type'              => false,
		'primary_link'      => false,
		'user_id'           => false,
		'item_id'           => false,
		'secondary_item_id' => false,
		'date_recorded'     => false,
		'hide_sitewide'     => false
	);

	$args = wp_parse_args( $args, $defaults );

	// Adjust the new mention count of any mentioned member
	bp_activity_adjust_mention_count( $args['id'], 'delete' );

	if ( !$activity_ids_deleted = BP_Activity_Activity::delete( $args ) )
		return false;

	// Check if the user's latest update has been deleted
	if ( empty( $args['user_id'] ) )
		$user_id = $bp->loggedin_user->id;
	else
		$user_id = $args['user_id'];

	do_action( 'bp_before_activity_delete', $args );

	$latest_update = bp_get_user_meta( $user_id, 'bp_latest_update', true );
	if ( !empty( $latest_update ) ) {
		if ( in_array( (int)$latest_update['id'], (array)$activity_ids_deleted ) )
			bp_delete_user_meta( $user_id, 'bp_latest_update' );
	}

	do_action( 'bp_activity_delete', $args );
	do_action( 'bp_activity_deleted_activities', $activity_ids_deleted );

	wp_cache_delete( 'bp_activity_sitewide_front', 'bp' );

	return true;
}

	/**
	 * Delete an activity item by activity id
	 *
	 * You should use bp_activity_delete() instead
	 *
	 * @since 1.1.0
	 * @deprecated 1.2.0
	 *
	 * @param array $args See docs for $defaults for details
	 *
	 * @global object $bp BuddyPress global settings
	 * @uses wp_parse_args()
	 * @uses bp_activity_delete()
	 *
	 * @return bool True on success, false on failure
	 */
	function bp_activity_delete_by_item_id( $args = '' ) {
		global $bp;

		$defaults = array( 'item_id' => false, 'component' => false, 'type' => false, 'user_id' => false, 'secondary_item_id' => false );
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		return bp_activity_delete( array( 'item_id' => $item_id, 'component' => $component, 'type' => $type, 'user_id' => $user_id, 'secondary_item_id' => $secondary_item_id ) );
	}

	/**
	 * Delete an activity item by activity id
	 *
	 * You should use bp_activity_delete() instead
	 *
	 * @since 1.1.0
	 * @deprecated 1.2.0
	 *
	 * @param int $activity_id The activity id
	 *
	 * @uses bp_activity_delete()
	 *
	 * @return bool True on success, false on failure
	 */
	function bp_activity_delete_by_activity_id( $activity_id ) {
		return bp_activity_delete( array( 'id' => $activity_id ) );
	}

	/**
	 * Delete an activity item by it's content
	 *
	 * You should use bp_activity_delete() instead
	 *
	 * @since 1.1.0
	 * @deprecated 1.2.0
	 *
	 * @param int $user_id The user id
	 * @param string $content The activity id
	 * @param string $component The activity component
	 * @param string $type The activity type
	 *
	 * @uses bp_activity_delete()
	 *
	 * @return bool True on success, false on failure
	 */
	function bp_activity_delete_by_content( $user_id, $content, $component, $type ) {
		return bp_activity_delete( array( 'user_id' => $user_id, 'content' => $content, 'component' => $component, 'type' => $type ) );
	}

	/**
	 * Delete a user's activity for a component
	 *
	 * You should use bp_activity_delete() instead
	 *
	 * @since 1.1.0
	 * @deprecated 1.2.0
	 *
	 * @param int $user_id The user id
	 * @param string $component The activity component
	 *
	 * @uses bp_activity_delete()
	 *
	 * @return bool True on success, false on failure
	 */
	function bp_activity_delete_for_user_by_component( $user_id, $component ) {
		return bp_activity_delete( array( 'user_id' => $user_id, 'component' => $component ) );
	}

/**
 * Delete an activity comment
 *
 * @since 1.2.0
 *
 * @param int $activity_id The activity id
 * @param int $comment_id The activity comment id
 *
 * @uses apply_filters() To call the 'bp_activity_delete_comment_pre' hook
 * @uses bp_activity_delete_children()
 * @uses bp_activity_delete()
 * @uses BP_Activity_Activity::rebuild_activity_comment_tree() {@link BP_Activity_Activity}
 * @uses do_action() To call the 'bp_activity_delete_comment' hook
 *
 * @return bool True on success, false on failure
 */
function bp_activity_delete_comment( $activity_id, $comment_id ) {
	/***
	 * You may want to hook into this filter if you want to override this function and
	 * handle the deletion of child comments differently. Make sure you return false.
	 */
	if ( !apply_filters( 'bp_activity_delete_comment_pre', true, $activity_id, $comment_id ) )
		return false;

	// Delete any children of this comment.
	bp_activity_delete_children( $activity_id, $comment_id );

	// Delete the actual comment
	if ( !bp_activity_delete( array( 'id' => $comment_id, 'type' => 'activity_comment' ) ) )
		return false;

	// Recalculate the comment tree
	BP_Activity_Activity::rebuild_activity_comment_tree( $activity_id );

	do_action( 'bp_activity_delete_comment', $activity_id, $comment_id );

	return true;
}

	/**
	 * Delete an activity comment's children
	 *
	 * @since 1.2.0
	 *
	 * @param int $activity_id The activity id
	 * @param int $comment_id The activity comment id
	 *
	 * @uses BP_Activity_Activity::get_child_comments() {@link BP_Activity_Activity}
	 * @uses bp_activity_delete_children()
	 * @uses bp_activity_delete()
	 */
	function bp_activity_delete_children( $activity_id, $comment_id) {
		// Recursively delete all children of this comment.
		if ( $children = BP_Activity_Activity::get_child_comments( $comment_id ) ) {
			foreach( (array)$children as $child )
				bp_activity_delete_children( $activity_id, $child->id );
		}
		bp_activity_delete( array( 'secondary_item_id' => $comment_id, 'type' => 'activity_comment', 'item_id' => $activity_id ) );
	}

/**
 * Get the permalink for a single activity item
 *
 * When only the $activity_id param is passed, BP has to instantiate a new BP_Activity_Activity
 * object. To save yourself some processing overhead, be sure to pass the full $activity_obj param
 * as well, if you already have it available.
 *
 * @since 1.2.0
 *
 * @param int $activity_id The unique id of the activity object
 * @param object $activity_obj (optional) The activity object
 *
 * @global object $bp BuddyPress global settings
 * @uses bp_get_root_domain()
 * @uses bp_get_activity_root_slug()
 * @uses apply_filters_ref_array() To call the 'bp_activity_get_permalink' hook
 *
 * @return string $link Permalink for the activity item
 */
function bp_activity_get_permalink( $activity_id, $activity_obj = false ) {
	global $bp;

	if ( !$activity_obj )
		$activity_obj = new BP_Activity_Activity( $activity_id );

	if ( isset( $activity_obj->current_comment ) ) {
		$activity_obj = $activity_obj->current_comment;
	}

	if ( 'new_blog_post' == $activity_obj->type || 'new_blog_comment' == $activity_obj->type || 'new_forum_topic' == $activity_obj->type || 'new_forum_post' == $activity_obj->type )
		$link = $activity_obj->primary_link;
	else {
		if ( 'activity_comment' == $activity_obj->type )
			$link = bp_get_root_domain() . '/' . bp_get_activity_root_slug() . '/p/' . $activity_obj->item_id . '/';
		else
			$link = bp_get_root_domain() . '/' . bp_get_activity_root_slug() . '/p/' . $activity_obj->id . '/';
	}

	return apply_filters_ref_array( 'bp_activity_get_permalink', array( $link, &$activity_obj ) );
}

/**
 * Hide a user's activity
 *
 * @since 1.2.0
 *
 * @param int $user_id The user id
 *
 * @uses BP_Activity_Activity::hide_all_for_user() {@link BP_Activity_Activity}
 *
 * @return bool True on success, false on failure
 */
function bp_activity_hide_user_activity( $user_id ) {
	return BP_Activity_Activity::hide_all_for_user( $user_id );
}

/**
 * Take content, remove all images and replace them with one thumbnail image.
 *
 * @since 1.2.0
 *
 * @param string $content The content to work with
 * @param string $link Optional. The URL that the image should link to
 *
 * @uses esc_attr()
 * @uses apply_filters() To call the 'bp_activity_thumbnail_content_images' hook
 *
 * @return string $content The content with images stripped and replaced with a single thumb.
 */
function bp_activity_thumbnail_content_images( $content, $link = false ) {
	global $post;

	preg_match_all( '/<img[^>]*>/Ui', $content, $matches );
	$content = preg_replace('/<img[^>]*>/Ui', '', $content );

	if ( !empty( $matches ) && !empty( $matches[0] ) ) {
		// Get the SRC value
		preg_match( '/<img.*?(src\=[\'|"]{0,1}.*?[\'|"]{0,1})[\s|>]{1}/i', $matches[0][0], $src );

		// Get the width and height
		preg_match( '/<img.*?(height\=[\'|"]{0,1}.*?[\'|"]{0,1})[\s|>]{1}/i', $matches[0][0], $height );
		preg_match( '/<img.*?(width\=[\'|"]{0,1}.*?[\'|"]{0,1})[\s|>]{1}/i', $matches[0][0], $width );

		if ( !empty( $src ) ) {
			$src = substr( substr( str_replace( 'src=', '', $src[1] ), 0, -1 ), 1 );
			$height = substr( substr( str_replace( 'height=', '', $height[1] ), 0, -1 ), 1 );
			$width = substr( substr( str_replace( 'width=', '', $width[1] ), 0, -1 ), 1 );

			if ( empty( $width ) || empty( $height ) ) {
				$width = 100;
				$height = 100;
			}

			$ratio = (int)$width / (int)$height;
			$new_height = (int)$height >= 100 ? 100 : $height;
			$new_width = $new_height * $ratio;

			$image = '<img src="' . esc_attr( $src ) . '" width="' . $new_width . '" height="' . $new_height . '" alt="' . __( 'Thumbnail', 'buddypress' ) . '" class="align-left thumbnail" />';

			if ( !empty( $link ) ) {
				$image = '<a href="' . $link . '">' . $image . '</a>';
			}

			$content = $image . $content;
		}
	}

	return apply_filters( 'bp_activity_thumbnail_content_images', $content, $matches );
}

/** Embeds *******************************************************************/

/**
 * Grabs the activity update ID and attempts to retrieve the oEmbed cache (if it exists)
 * during the activity loop.  If no cache and link is embeddable, cache it.
 *
 * This does not cover recursive activity comments, as they do not use a real loop.
 * For that, see {@link bp_activity_comment_embed()}.
 *
 * @since 1.5
 *
 * @see BP_Embed
 * @see bp_embed_activity_cache()
 * @see bp_embed_activity_save_cache()
 *
 * @uses add_filter() To attach 'bp_get_activity_id' to 'embed_post_id'
 * @uses add_filter() To attach 'bp_embed_activity_cache' to 'bp_embed_get_cache'
 * @uses add_action() To attach 'bp_embed_activity_save_cache' to 'bp_embed_update_cache'
 */
function bp_activity_embed() {
	add_filter( 'embed_post_id',         'bp_get_activity_id'                  );
	add_filter( 'bp_embed_get_cache',    'bp_embed_activity_cache',      10, 3 );
	add_action( 'bp_embed_update_cache', 'bp_embed_activity_save_cache', 10, 3 );
}
add_action( 'activity_loop_start', 'bp_activity_embed' );

/**
 * Grabs the activity comment ID and attempts to retrieve the oEmbed cache (if it exists)
 * when BP is recursing through activity comments {@link bp_activity_recurse_comments()}.
 * If no cache and link is embeddable, cache it.
 *
 * @since 1.5
 *
 * @see BP_Embed
 * @see bp_embed_activity_cache()
 * @see bp_embed_activity_save_cache()
 *
 * @uses add_filter() To attach 'bp_get_activity_comment_id' to 'embed_post_id'
 * @uses add_filter() To attach 'bp_embed_activity_cache' to 'bp_embed_get_cache'
 * @uses add_action() To attach 'bp_embed_activity_save_cache' to 'bp_embed_update_cache'
 */
function bp_activity_comment_embed() {
	add_filter( 'embed_post_id',         'bp_get_activity_comment_id'          );
	add_filter( 'bp_embed_get_cache',    'bp_embed_activity_cache',      10, 3 );
	add_action( 'bp_embed_update_cache', 'bp_embed_activity_save_cache', 10, 3 );
}
add_action( 'bp_before_activity_comment', 'bp_activity_comment_embed' );

/**
 * When a user clicks on a "Read More" item, make sure embeds are correctly parsed and shown for the expanded content.
 *
 * @since 1.5
 *
 * @see BP_Embed
 *
 * @param object $activity The activity that is being expanded
 *
 * @global object $bp BuddyPress global settings
 * @uses add_filter() To attach create_function() to 'embed_post_id'
 * @uses add_filter() To attach 'bp_embed_activity_cache' to 'bp_embed_get_cache'
 * @uses add_action() To attach 'bp_embed_activity_save_cache' to 'bp_embed_update_cache'
 */
function bp_dtheme_embed_read_more( $activity ) {
	global $bp;

	$bp->activity->read_more_id = $activity->id;

	add_filter( 'embed_post_id',            create_function( '', 'global $bp; return $bp->activity->read_more_id;' ) );
	add_filter( 'bp_embed_get_cache',       'bp_embed_activity_cache',      10, 3 );
	add_action( 'bp_embed_update_cache',    'bp_embed_activity_save_cache', 10, 3 );
}
add_action( 'bp_dtheme_get_single_activity_content', 'bp_dtheme_embed_read_more' );

/**
 * Removes the 'embed_post_id' filter after {@link bp_activity_recurse_comments()}
 * is rendered to avoid conflict with the 'embed_post_id' filter in
 * {@link bp_activity_embed()} or any other component embeds.
 *
 * @since 1.5
 *
 * @see bp_activity_comment_embed()
 *
 * @uses remove_filter() To remove 'bp_get_activity_comment_id' from 'embed_post_id'
 */
function bp_activity_comment_embed_after_recurse() {
	remove_filter( 'embed_post_id', 'bp_get_activity_comment_id' );
}
add_action( 'bp_after_activity_comment', 'bp_activity_comment_embed_after_recurse' );

/**
 * Wrapper function for {@link bp_activity_get_meta()}.
 * Used during {@link BP_Embed::parse_oembed()} via {@link bp_activity_embed()}.
 *
 * @since 1.5
 *
 * @uses bp_activity_get_meta()
 *
 * @return mixed The activity meta
 */
function bp_embed_activity_cache( $cache, $id, $cachekey ) {
	return bp_activity_get_meta( $id, $cachekey );
}

/**
 * Wrapper function for {@link bp_activity_update_meta()}.
 * Used during {@link BP_Embed::parse_oembed()} via {@link bp_activity_embed()}.
 *
 * @since 1.5
 *
 * @uses bp_activity_update_meta()
 */
function bp_embed_activity_save_cache( $cache, $cachekey, $id ) {
	bp_activity_update_meta( $id, $cachekey, $cache );
}
?>