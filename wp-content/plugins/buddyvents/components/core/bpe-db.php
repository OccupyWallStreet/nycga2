<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add an event
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_save_event( $id = null, $user_id, $group_id, $name, $slug, $description, $category, $url, $location, $venue_name, $longitude, $latitude, $start_date, $start_time, $end_date, $end_time, $date_created, $public, $limit_members, $recurrent, $is_spam, $approved, $rsvp, $all_day, $timezone, $group_approved )
{
	$event = new Buddyvents_Events( $id );

	$event->user_id 		= $user_id;
	$event->group_id 		= $group_id;
	$event->name 			= $name;
	$event->slug 			= $slug;
	$event->description 	= $description;
	$event->category 		= $category;
	$event->url 			= $url;
	$event->location 		= $location;
	$event->venue_name 		= $venue_name;
	$event->longitude 		= $longitude;
	$event->latitude 		= $latitude;
	$event->start_date 		= $start_date;
	$event->start_time 		= $start_time;
	$event->end_date 		= $end_date;
	$event->end_time 		= $end_time;
	$event->date_created 	= $date_created;
	$event->public 			= $public;
	$event->limit_members 	= $limit_members;
	$event->recurrent 		= $recurrent;
	$event->is_spam 		= $is_spam;
	$event->approved 		= $approved;
	$event->rsvp 			= $rsvp;
	$event->all_day 		= $all_day;
	$event->timezone 		= $timezone;
	$event->group_approved 	= $group_approved;
	
	if( $new_id = $event->save() )
		return $new_id;
		
	return false;
}

/**
 * Add a member
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_add_member( $id = null, $event_id, $user_id, $rsvp, $rsvp_date, $role )
{
	$member = new Buddyvents_Members( $id );
	
	$member->event_id 	= $event_id;
	$member->user_id 	= $user_id;
	$member->rsvp 		= $rsvp;
	$member->rsvp_date 	= $rsvp_date;
	$member->role 		= $role;

	if( $new_id = $member->save() )
		return $new_id;
		
	return false;
}

/**
 * Add a notification
 *
 * @package	 Core
 * @since 	 1.2.5
 */
function bpe_add_notification( $id = null, $user_id, $keywords, $email, $screen, $remind )
{
	$note = new Buddyvents_Notifications( $id );
	
	$note->user_id 	= $user_id;
	$note->keywords = $keywords;
	$note->email 	= $email;
	$note->screen 	= $screen;
	$note->remind 	= $remind;

	if( $new_id = $note->save() )
		return $new_id;
		
	return false;
}

/**
 * Add a category
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_add_category( $id = null, $name, $slug )
{
	$cat = new Buddyvents_Categories( $id );
	
	$cat->name = $name;
	$cat->slug = $slug;

	if( $new_id = $cat->save() )
		return $new_id;
		
	return false;
}

/**
 * Loop function: get all events
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_get_events( $args = '' )
{
	global $bp;
	
	$defaults = array(
		'ids' 				=> false,
		'user_id' 			=> false,
		'group_id' 			=> false,
		'name' 				=> false,
		'slug' 				=> false,
		'start_date' 		=> false,
		'start_time' 		=> false,
		'end_date' 			=> false,
		'end_time' 			=> false,
		'timezone' 			=> false,
		'day' 				=> false,
		'month' 			=> false,
		'year' 				=> false,
		'future' 			=> true,
		'past' 				=> false,
		'location' 			=> false,
		'venue_name' 		=> false,
		'venue' 			=> false,
		'map' 				=> false,
		'radius' 			=> false,
		'longitude' 		=> false,
		'latitude' 			=> false,
		'public' 			=> false,
		'rsvp' 				=> false,
		'category' 			=> false,
		'meta' 				=> false,
		'meta_key' 			=> false,
		'operator' 			=> '=',
		'begin' 			=> false,
		'end' 				=> false,
		'per_page' 			=> 10,
		'page' 				=> 1,
		'search_terms' 		=> false,
		'populate_extras' 	=> true,
		'sort' 				=> 'start_date_asc',
		'restrict' 			=> true,
		'spam' 				=> 0,
		'approved' 			=> 1,
		'group_approved' 	=> 1,
		'plus_days'			=> 0
	);
	
	$params = wp_parse_args( $args, $defaults );

	extract( $params, EXTR_SKIP );

	$events = Buddyvents_Events::get( $ids, (int)$user_id, (int)$group_id, $name, $slug, $start_date, $start_time, $end_date, $end_time, $timezone, $day, $month, $year, $future, $past, $location, $venue_name, $venue, (bool)$map, $radius, $longitude, $latitude, (int)$public, (int)$rsvp, $category, $meta, $meta_key, $operator, $begin, $end, (int)$page, (int)$per_page, $search_terms, (bool)$populate_extras, $sort, (bool)$restrict, $spam, (int)$approved, (int)$group_approved, (int)$plus_days );

	return apply_filters( 'bpe_get_events', $events, &$params );
}

/**
 * Get the adjacent event
 * Takes into account access rights to an event
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_get_adjacent_event( $previous = true )
{
	global $wpdb, $bpe, $event_template, $bp;
	
	$displayed_event_date = apply_filters( 'bpe_get_adjacent_event_date', $event_template->event->start_date .' '. $event_template->event->start_time, $event_template->event );
		
	$op = $previous ? '<' : '>';
	$order = $previous ? 'DESC' : 'ASC';
	
	$group_status = '';
	$group_join   = '';
	
	if( bp_is_active( 'groups' ) ) :
		$group_status = ', g.status';
		$group_join	  = 'LEFT JOIN {$bp->groups->table_name} g ON g.id = e.group_id';
	endif;	
	
	$query = $wpdb->prepare( "SELECT e.*{$group_status}, m.meta_value as invitations FROM {$bpe->tables->events} e{$group_join} LEFT JOIN {$bpe->tables->events_meta} m ON e.id = m.event_id AND meta_key = 'invitations' WHERE e.approved = 1 AND CONCAT( e.start_date, ' ', e.start_time ) {$op} %s ORDER BY CONCAT( e.start_date, ' ', e.start_time ) {$order} LIMIT 1", $displayed_event_date );
	$query_key = 'adjacent_event_'. md5( $query );
	
	$result = wp_cache_get( $query_key, 'events' );
	
	if( $result === false )
	{
		$result = $wpdb->get_row( $query );
		if ( $result === null )
			$result = '';
	
		wp_cache_set( $query_key, $result, 'events' );
	}

	$check = bpe_restrict_event_access( $result );

	if( $check )
	{
		do {
			$displayed_event_date = $result->start_date .' '. $result->start_time;

			$query = $wpdb->prepare( "SELECT e.*, g.status, m.meta_value as invitations FROM {$bpe->tables->events} e LEFT JOIN {$bp->groups->table_name} g ON g.id = e.group_id LEFT JOIN {$bpe->tables->events_meta} m ON e.id = m.event_id AND meta_key = 'invitations' WHERE e.approved = 1 AND CONCAT( e.start_date, ' ', e.start_time ) {$op} %s ORDER BY CONCAT( e.start_date, ' ', e.start_time ) {$order} LIMIT 1", $displayed_event_date );
			$query_key = 'adjacent_event_'. md5( $query );

			$result = wp_cache_get( $query_key, 'events' );

			if( $result === false )
			{
				$result = $wpdb->get_row( $query );
				if( $result === null )
					$result = '';
			
				wp_cache_set( $query_key, $result, 'events' );
			}
			
			$check = bpe_restrict_event_access( $result );
		} while ( $check );
	}
	
	return apply_filters( 'bpe_get_adjacent_event', $result, $event_template->event );
}

/**
 * Get the event coordinates
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_get_event_coordinates( $location )
{
	global $wpdb, $bpe;
	
	if( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV )
		return false;
	
	// get the coordinates
	$xml = wp_remote_get( 'http://maps.google.com/maps/api/geocode/xml?address='. urlencode( $location ) .'&sensor=false' );
	
	$data = new SimpleXMLElement( wp_remote_retrieve_body( $xml ) );
	
	if( $data->status == 'OK' )
	{
		$lat = (array)$data->result->geometry->location->lat;
		$lng = (array)$data->result->geometry->location->lng;
		
		$latitude = $original_lat = $lat[0];
		$longitude = $original_lng = $lng[0];
	
		// make sure we don't get the same coordinates twice
		$check = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->events} WHERE longitude = {$longitude} AND latitude = {$latitude}" ) );
		if( $check )
		{
			$angle = 36;
			$radius = 0.0001;
			do {
				if( $angle % 360 == 0 )
					$radius = $radius + 0.0001;
					
				$coords = bpe_next_coords( $original_lat, $original_lng, $radius, $angle );
				
				$latitude = $coords['lat'];
				$longitude = $coords['lng'];
				
				$check = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->events} WHERE longitude = {$longitude} AND latitude = {$latitude}" ) );
	
				$angle = $angle + 36;
			} while ( $check );
		}
	}
	else
	{
		$latitude = '';
		$longitude = '';
	}
	
	$coordinates = array( 'lat' => $latitude, 'lng' => $longitude );
	
	return apply_filters( 'bpe_get_event_coordinates', $coordinates );
}

/**
 * Make sure we have a unique slug
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_check_unique_slug( $slug, $table = 'events', $id = false )
{
	global $wpdb, $bpe;
	
	if( $table == 'events' )
		$sql = "SELECT slug FROM {$bpe->tables->events} WHERE slug = %s LIMIT 1";
		
	elseif( $table == 'categories' )
		$sql = "SELECT slug FROM {$bpe->tables->categories} WHERE slug = %s LIMIT 1";

	$check = $wpdb->get_var( $wpdb->prepare( $sql, $slug ) );

	if( $check )
	{
		$suffix = 2;
		do {
			$alt_title = substr( $slug, 0, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
			$check = $wpdb->get_var( $wpdb->prepare( $sql, $alt_title ) );
			$suffix++;
		} while ( $check );
		$slug = $alt_title;
	}
	
	return $slug;	
}

/**
 * Get the event ids
 * http://code.google.com/apis/maps/articles/phpsqlsearch.html#findnearsql
 *
 * @package	 Core
 * @since 	 1.2.3
 */
function bpe_proximity_event_ids( $filter )
{
	global $wpdb, $bpe, $bp;
	
	if( ! $filter )
		return false;
	
	$dist = ( bpe_get_option( 'system' )  == 'm' ) ? 3959 : 6371;
	
	$dist = apply_filters( 'bpe_proximity_system', $dist );
	
	$coords = new MAPO_Coords( null, bp_loggedin_user_id() );
	
	if( empty( $coords ) )
		return false;
	
	$events = $wpdb->get_results( $wpdb->prepare( "SELECT *, ( {$dist} * acos( cos( radians( {$coords->lat} ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( {$coords->lng} ) ) + sin( radians( {$coords->lat} ) ) * sin( radians( latitude ) ) ) ) as distance FROM {$bpe->tables->events} HAVING distance < {$filter} ORDER BY distance" ) );

	foreach( $events as $event  )
	{
		if( ! empty( $event->distance ) )
			$eids[] = bpe_get_event_id( $event );
	}
		
	$eids = $wpdb->escape( join( ',', (array)$eids ) );

	return $eids;
}

/**
 * Get the coordinates
 *
 * @package	 Core
 * @since 	 1.6
 */
function bpe_get_search_coords( $location = false )
{
	global $bp;
	
	if( ! $location )
	{
		$coords = new MAPO_Coords( null, bp_loggedin_user_id() );
		$lat = $coords->lat;
		$lng = $coords->lng;
	}
	else
	{
		$coords = bpe_get_event_coordinates( $location );
		$lat = $coords['lat'];
		$lng = $coords['lng'];
	}

	return array( 'lat' => $lat, 'lng' => $lng );
}

/**
 * Get the total events count
 *
 * @package	 Core
 * @since 	 1.3
 */
function bpe_get_event_count( $type = 'active', $id = 0, $type_id = 'none' )
{
	global $wpdb, $bpe, $bp;

	if( ! $count = wp_cache_get( 'bpe_get_total_events_count_'. $type .'_'. $type_id .'_'. $id, 'bpe' ) )
	{
		switch( $type_id )
		{
			case 'none': default:
				break;
	
			case 'group':
				$sql_bits[] = $wpdb->prepare( "e.group_id = %d", $id );
				break;
	
			case 'user':
				$sql_bits[] = $wpdb->prepare( "e.user_id = %d", $id );
				break;
		}

		switch( $type )
		{
			case 'active':
				$sql_bits[] = $wpdb->prepare( "CONCAT( e.end_date, ' ', e.end_time ) >= NOW()" );
				break;

			case 'archive':
				$sql_bits[] = $wpdb->prepare( "CONCAT( e.end_date, ' ', e.end_time ) < NOW()" );
				break;
		}

		$sql_bits[] = $wpdb->prepare( "e.is_spam = 0" );
		$sql_bits[] = $wpdb->prepare( "e.approved = 1" );
		$sql_bits[] = $wpdb->prepare( "e.group_approved = 1" );

		$sql = " WHERE " . implode( ' AND ', (array)$sql_bits );
		
		$group_sql 	  = '';
		$group_select = '';
		if( bp_is_active( 'groups' ) ) :
			$group_sql 	  = "LEFT JOIN {$bp->groups->table_name} g ON g.id = e.group_id";
			$group_select = ", g.status as group_status";
		endif;
		
		$events = $wpdb->get_results( $wpdb->prepare( "
			SELECT e.*, em.meta_value as invitations {$group_select}
			FROM {$bpe->tables->events} e
			{$group_sql}
			LEFT JOIN {$bpe->tables->events_meta} em ON em.event_id = e.id AND em.meta_key = 'invitations'
			{$sql}
		" ) );
		
		foreach( $events as $key => $event )
		{
			if( bpe_restrict_event_access( $event ) )
				unset( $events[$key] );
		}
		
		$count = count( $events );

		wp_cache_set( 'bpe_get_total_events_count_'. $type .'_'. $type_id .'_'. $id, $count, 'bpe' );
	}

	return apply_filters( 'bpe_get_event_count', $count );
}

/**
 * Get all groups where loggedin user is a member
 *
 * @package	 Core
 * @since 	 1.2
 */
function bpe_get_groups_for_user( $user_id = false )
{
	global $wpdb, $bp;
	
	if( ! bp_is_active( 'groups' ) )
		return false;
	
	if( ! $user_id )
		$user_id = bp_loggedin_user_id();

	$groups = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT m.group_id, g.name FROM {$bp->groups->table_name_members} m LEFT JOIN {$bp->groups->table_name} g ON m.group_id = g.id WHERE m.user_id = %d AND m.is_banned = 0", $user_id ) );

	$new_groups = array();
	foreach( $groups as $g )
		$new_groups[$g->group_id] = $g->name; 
		
	return apply_filters( 'bpe_get_groups_for_user', array_unique( $new_groups ) );
}

/**
 * Get the activity ids for an event that has a forum attached
 *
 * @package	 Core
 * @since 	 2.1
 */
function bpe_get_activity_ids_with_forum( $forum_id = false, $event_id = false )
{
	global $wpdb, $bp;
	
	if( ! $forum_id || ! $event_id )
		return false;
	
	$ids = $wpdb->get_col( $wpdb->prepare( "
		SELECT id 
		FROM {$bp->activity->table_name} 
		WHERE (component = 'events' AND item_id = %d) 
		OR (component = 'bbpress' AND secondary_item_id = %d)
	", $event_id, $forum_id ) );
	
	return apply_filters( 'bpe_get_activity_ids_with_forum', join( ',', array_unique( $ids ) ) );
}

/**
 * Is the user a member
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_user_member_already( $event_id, $user_id = false )
{
	return Buddyvents_Members::is_user_member_already( $event_id, $user_id );
}

/**
 * Was the user a member
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_was_user_member( $event_id, $user_id = false )
{
	return Buddyvents_Members::was_user_member( $event_id, $user_id );
}

/**
 * Get all events for the current user
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_get_user_events( $user_id = false, $count = false )
{
	return Buddyvents_Members::get_user_events( $user_id, $count );
}

/**
 * Set rsvp for a member
 *
 * @package	 Core
 * @since 	 1.3
 */
function bpe_set_rsvp_for_user( $rsvp, $user_id, $event_id )
{
	return Buddyvents_Members::set_rsvp_for_user( $rsvp, $user_id, $event_id );
}

/**
 * Remove user from event
 *
 * @package	 Core
 * @since 	 1.3
 */
function bpe_remove_user_from_event( $user_id, $event_id )
{
	return Buddyvents_Members::remove_user_from_event( $user_id, $event_id );
}

/**
 * Empty members for an event
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_delete_members_for_event( $event_id )
{
	return Buddyvents_Members::delete_members_for_event( $event_id );
}

/**
 * Get all attendees for an event
 *
 * @package	 Core
 * @since 	 1.3
 */
function bpe_get_attendee_ids( $event_id )
{
	return Buddyvents_Members::get_attendee_ids( $event_id );
}

/**
 * Set the role of an event member
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_set_event_member_role( $user_id, $event_id, $role )
{
	return Buddyvents_Members::set_event_member_role( $user_id, $event_id, $role );
}

/**
 * Get an event id from the slug
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_get_id_from_slug( $slug )
{
	return Buddyvents_Events::get_id_from_slug( $slug );	
}

/**
 * Approve an event
 *
 * @package	 Core
 * @since 	 1.4
 */
function bpe_approve_event( $event_id, $approved = 1 )
{
	return Buddyvents_Events::approve_event( $event_id, $approved );	
}

/**
 * Group approve an event
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_group_approve_event( $event_id, $approved = 1 )
{
	return Buddyvents_Events::group_approve_event( $event_id, $approved );	
}

/**
 * Get approvable events
 *
 * @package	 Core
 * @since 	 1.5
 */
function bpe_get_approvable_events()
{
	return Buddyvents_Events::get_approvable_events();
}

/**
 * Change spam by ids
 *
 * @package	 Core
 * @since 	 1.2.4
 */
function bpe_change_spam_by_ids( $ids, $is_spam )
{
	return Buddyvents_Events::change_spam_by_ids( $ids, $is_spam );	
}

/**
 * Delete by ids
 *
 * @package	 Core
 * @since 	 1.2.4
 */
function bpe_delete_by_ids( $ids )
{
	return Buddyvents_Events::delete_by_ids( $ids );	
}

/**
 * Checks if the event has expired
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_event_over( $event_id )
{
	return Buddyvents_Events::is_event_over( $event_id );	
}

/**
 * Is the current user the event creator
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_is_user_event_admin( $event_id )
{
	return Buddyvents_Events::is_user_event_admin( $event_id );	
}

/**
 * Last published event date for a group
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_group_get_last_published( $cat_id = false, $tz = false, $venue = false )
{
	return Buddyvents_Events::group_get_last_published( $cat_id, $tz, $venue );
}

/**
 * Last published event date for a user
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_user_get_last_published( $cat_id = false, $tz = false, $venue = false )
{
	return Buddyvents_Events::user_get_last_published( $cat_id, $tz, $venue );
}

/**
 * Last global event date
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_get_last_published()
{
	return Buddyvents_Events::get_last_published();
}

/**
 * Last category event date
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_category_get_last_published( $cat )
{
	return Buddyvents_Events::category_get_last_published( $cat );
}

/**
 * Last timezone event date
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_timezone_get_last_published( $zone )
{
	return Buddyvents_Events::timezone_get_last_published( $zone );
}

/**
 * Last venue event date
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_venue_get_last_published( $venue )
{
	return Buddyvents_Events::venue_get_last_published( $venue );
}

/**
 * Set events to spam for a user
 *
 * @package	 Core
 * @since 	 1.2.4
 */
function bpe_set_events_to_spam( $user_id, $is_spam )
{
	return Buddyvents_Events::set_events_to_spam( $user_id, $is_spam );
}
add_action( 'bp_core_action_set_spammer_status', 'bpe_set_events_to_spam', 10, 2 );

/**
 * Get all event categories
 *
 * @package	 Core
 * @since 	 1.0
 */
function bpe_get_event_categories( $count = false )
{
	return Buddyvents_Categories::get_event_categories( $count );
}

/**
 * Get all event users
 *
 * @package	 Core
 * @since 	 1.2.4
 */
function bpe_get_event_users()
{
	return Buddyvents_Events::get_event_users();
}

/**
 * Get all event groups
 *
 * @package	 Core
 * @since 	 1.2.4
 */
function bpe_get_event_groups()
{
	return Buddyvents_Events::get_event_groups();
}

/**
 * Get category id from slug
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_get_catid_from_slug( $slug )
{
	return Buddyvents_Categories::get_id_from_slug( $slug );
}

/**
 * Get category name from slug
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_get_category_name_by_slug( $slug )
{
	return Buddyvents_Categories::get_name_from_slug( $slug );
}

/**
 * Get user_ids for event keywords
 *
 * @package	 Core
 * @since 	 1.2.5
 */
function bpe_get_uids_for_keywords( $string, $uid )
{
	return Buddyvents_Notifications::get_uids_for_keywords( $string, $uid );
}

/**
 * Get all group members
 *
 * @package	 Core
 * @since 	 1.4
 */
function bpe_get_group_members( $group_id )
{
	global $wpdb, $bp;

	return $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$bp->groups->table_name_members} WHERE group_id = %d", $group_id ) );
}

/**
 * Get all groups for the site admin
 *
 * @package	 Core
 * @since 	 1.5.3
 */
function bpe_get_admin_groups()
{
	global $bpe, $wpdb, $bp;
	
	return $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$bp->groups->table_name}" ) );	
}

/**
 * Get all timezones
 *
 * @package	 Core
 * @since 	 1.7.10
 */
function bpe_get_timezones()
{
	return Buddyvents_Events::get_timezones();
}

/**
 * Get all venues
 *
 * @package	 Core
 * @since 	 1.7.10
 */
function bpe_get_venues()
{
	return Buddyvents_Events::get_venues();
}
?>