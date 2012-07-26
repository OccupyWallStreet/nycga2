<?php
/**
* The Events Calendar Advanced Functions for the Venue Post Type
 *
 * These functions can be used to manipulate Venue data. These functions may be useful for integration with other WordPress plugins and extended functionality.
 */

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

if( class_exists( 'TribeEvents' ) ) {

	/**
	 * Create a Venue
	 *
	 * $args accepts all the args that can be passed to wp_insert_post(). 
	 * In addition to that, the following args can be passed specifically 
	 * for the process of creating a Venue:
	 *
	 * - Venue string - Title of the Venue. (required)
	 * - Country string - Country code for the Venue country.
	 * - Address string - Street address of the Venue.
	 * - City string - City of the Venue.
	 * - State string - Two letter state abbreviation.
	 * - Province string - Province of the Venue.
	 * - Zip string - Zip code of the Venue.
	 * - Phone string - Phone number for the Venue.
	 * 
	 * @param array $args Elements that make up post to insert.
	 * @return int ID of the Venue that was created. False if insert failed.
	 * @link http://codex.wordpress.org/Function_Reference/wp_insert_post
	 * @see wp_insert_post()
	 * @category Venue Functions
	 * @since 2.0.1
	 */
	function tribe_create_venue($args) {
		$postId = TribeEventsAPI::createVenue($args);
		return $postId;
	}

	/**
	 * Update a Venue
	 *
	 * @param int $postId ID of the Venue to be modified.
	 * @param array $args Args for updating the post. See {@link tribe_create_venue()} for more info.
	 * @return int ID of the Venue that was created. False if update failed.
	 * @link http://codex.wordpress.org/Function_Reference/wp_update_post
	 * @see wp_update_post()
	 * @see tribe_create_venue()
	 * @category Venue Functions
	 * @since 2.0.1
	 */
	function tribe_update_venue($postId, $args) {
		$postId = TribeEventsAPI::updateVenue($postId, $args);
		return $postId;
	}

	/**
	 * Delete a Venue
	 *
	 * @param int $postId ID of the Venue to be deleted.
	 * @param bool $force_delete Whether to bypass trash and force deletion. Defaults to false.
	 * @return bool false if delete failed.
	 * @link http://codex.wordpress.org/Function_Reference/wp_delete_post
	 * @see wp_delete_post()
	 * @category Venue Functions
	 * @since 2.0.1
	 */
	function tribe_delete_venue($postId, $force_delete = false) {
		$success = TribeEventsAPI::deleteVenue($postId, $args);
		return $success;
	}

}
?>