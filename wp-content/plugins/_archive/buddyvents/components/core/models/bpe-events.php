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

class Buddyvents_Events
{
	public $id;
	public $user_id;
	public $group_id;
	public $name;
	public $slug;
	public $description;
	public $category;
	public $url;
	public $location;
	public $venue_name;
	public $longitude;
	public $latitude;
	public $start_date;
	public $start_time;
	public $end_date;
	public $end_time;
	public $date_created;
	public $public;
	public $limit_members;
	public $recurrent;
	public $is_spam;
	public $approved;
	public $rsvp;
	public $all_day;
	public $timezone;
	public $group_approved;
	
	/**
	 * PHP5 Constructor
	 * @since 1.0
	 */
	public function __construct( $id = null, $slug = null )
	{
		global $bpe, $wpdb;

		if( $id )
		{
			$this->id = $id;
			$this->populate();
		}
		elseif( $slug )
		{
			$this->slug = $slug;
			$this->populate_by_slug();
		}
	}

	/**
	 * Get a row from the database
	 * @since 1.0
	 */
	public function populate()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->events} WHERE id = %d", $this->id ) );
		
		if( empty( $table ) )
			return false;

		$this->user_id	 	 = $table->user_id;
		$this->group_id	 	 = $table->group_id;
		$this->name 		 = $table->name;
		$this->slug 		 = $table->slug;
		$this->description	 = $table->description;
		$this->category		 = $table->category;
		$this->url			 = $table->url;
		$this->location		 = $table->location;
		$this->venue_name	 = $table->venue_name;
		$this->longitude	 = $table->longitude;
		$this->latitude		 = $table->latitude;
		$this->start_date	 = $table->start_date;
		$this->start_time	 = $table->start_time;
		$this->end_date		 = $table->end_date;
		$this->end_time		 = $table->end_time;
		$this->date_created	 = $table->date_created;
		$this->public		 = $table->public;
		$this->limit_members = $table->limit_members;
		$this->recurrent	 = $table->recurrent;
		$this->is_spam		 = $table->is_spam;
		$this->approved		 = $table->approved;
		$this->rsvp			 = $table->rsvp;
		$this->all_day		 = $table->all_day;
		$this->timezone		 = $table->timezone;
		$this->group_approved= $table->group_approved;
	}

	/**
	 * Get a row from the database
	 * @since 1.0
	 */
	public function populate_by_slug()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->events} WHERE slug = %s", $this->slug ) );

		$this->id	 		 = $table->id;
		$this->user_id	 	 = $table->user_id;
		$this->group_id	 	 = $table->group_id;
		$this->name 		 = $table->name;
		$this->description	 = $table->description;
		$this->category		 = $table->category;
		$this->url			 = $table->url;
		$this->location		 = $table->location;
		$this->venue_name	 = $table->venue_name;
		$this->longitude	 = $table->longitude;
		$this->latitude		 = $table->latitude;
		$this->start_date	 = $table->start_date;
		$this->start_time	 = $table->start_time;
		$this->end_date		 = $table->end_date;
		$this->end_time		 = $table->end_time;
		$this->date_created	 = $table->date_created;
		$this->public		 = $table->public;
		$this->limit_members = $table->limit_members;
		$this->recurrent	 = $table->recurrent;
		$this->is_spam		 = $table->is_spam;
		$this->approved		 = $table->approved;
		$this->rsvp			 = $table->rsvp;
		$this->all_day		 = $table->all_day;
		$this->timezone		 = $table->timezone;
		$this->group_approved= $table->group_approved;
	}

	/**
	 * Save or uptimestamp a row
	 * @since 1.0
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->user_id	 	 = apply_filters( 'bpe_events_before_save_events_user_id', 			$this->user_id,			$this->id );
		$this->group_id	 	 = apply_filters( 'bpe_events_before_save_events_group_id', 		$this->group_id, 		$this->id );
		$this->name			 = apply_filters( 'bpe_events_before_save_events_name', 			$this->name, 			$this->id );
		$this->slug			 = apply_filters( 'bpe_events_before_save_events_slug', 			$this->slug, 			$this->id );
		$this->description	 = apply_filters( 'bpe_events_before_save_events_description', 		$this->description, 	$this->id );
		$this->category		 = apply_filters( 'bpe_events_before_save_events_category', 		$this->category, 		$this->id );
		$this->url			 = apply_filters( 'bpe_events_before_save_events_url', 				$this->url, 			$this->id );
		$this->location		 = apply_filters( 'bpe_events_before_save_events_location', 		$this->location, 		$this->id );
		$this->venue_name	 = apply_filters( 'bpe_events_before_save_events_venue_name', 		$this->venue_name, 		$this->id );
		$this->longitude	 = apply_filters( 'bpe_events_before_save_events_longitude', 		$this->longitude, 		$this->id );
		$this->latitude		 = apply_filters( 'bpe_events_before_save_events_latitude', 		$this->latitude, 		$this->id );
		$this->start_date	 = apply_filters( 'bpe_events_before_save_events_start_date', 		$this->start_date, 		$this->id );
		$this->start_time	 = apply_filters( 'bpe_events_before_save_events_start_time', 		$this->start_time, 		$this->id );
		$this->end_date		 = apply_filters( 'bpe_events_before_save_events_end_date', 		$this->end_date, 		$this->id );
		$this->end_time		 = apply_filters( 'bpe_events_before_save_events_end_time', 		$this->end_time, 		$this->id );
		$this->date_created	 = apply_filters( 'bpe_events_before_save_events_date_created', 	$this->date_created,	$this->id );
		$this->public		 = apply_filters( 'bpe_events_before_save_events_public', 			$this->public, 			$this->id );
		$this->limit_members = apply_filters( 'bpe_events_before_save_events_limit_members',	$this->limit_members, 	$this->id );
		$this->recurrent	 = apply_filters( 'bpe_events_before_save_events_recurrent', 		$this->recurrent, 		$this->id );
		$this->is_spam		 = apply_filters( 'bpe_events_before_save_events_is_spam', 			$this->is_spam, 		$this->id );
		$this->approved		 = apply_filters( 'bpe_events_before_save_events_approved', 		$this->approved, 		$this->id );
		$this->rsvp			 = apply_filters( 'bpe_events_before_save_events_rsvp', 			$this->rsvp, 			$this->id );
		$this->all_day		 = apply_filters( 'bpe_events_before_save_events_all_day', 			$this->all_day, 		$this->id );
		$this->timezone		 = apply_filters( 'bpe_events_before_save_events_timezone', 		$this->timezone, 		$this->id );
		$this->group_approved= apply_filters( 'bpe_events_before_save_events_group_approved',	$this->group_approved, 	$this->id );
		
		/* Call a before save action here */
		do_action( 'bpe_events_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->events} SET
													user_id = %d,
													group_id = %d,
													name = %s,
													slug = %s,
													description = %s,
													category = %d,
													url = %s,
													location = %s,
													venue_name = %s,
													longitude = %s,
													latitude = %s,
													start_date = %s,
													start_time = %s,
													end_date = %s,
													end_time = %s,
													date_created = %s,
													public = %d,
													limit_members = %d,
													recurrent = %s,
													is_spam = %d,
													approved = %d,
													rsvp = %d,
													all_day = %d,
													timezone = %s,
													group_approved = %d
											WHERE id = %d",
													$this->user_id,
													$this->group_id,
													$this->name,
													$this->slug,
													$this->description,
													$this->category,
													$this->url,
													$this->location,
													$this->venue_name,
													$this->longitude,
													$this->latitude,
													$this->start_date,
													$this->start_time,
													$this->end_date,
													$this->end_time,
													$this->date_created,
													$this->public,
													$this->limit_members,
													$this->recurrent,
													$this->is_spam,
													$this->approved,
													$this->rsvp,
													$this->all_day,
													$this->timezone,
													$this->group_approved,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->events} (
													user_id,
													group_id,
													name,
													slug,
													description,
													category,
													url,
													location,
													venue_name,
													longitude,
													latitude,
													start_date,
													start_time,
													end_date,
													end_time,
													date_created,
													public,
													limit_members,
													recurrent,
													is_spam,
													approved,
													rsvp,
													all_day,
													timezone,
													group_approved
											) VALUES ( 
													%d, %d, %s, %s, %s, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %d, %s, %d, %d, %d, %d, %s, %d
											)",
													$this->user_id,
													$this->group_id,
													$this->name,
													$this->slug,
													$this->description,
													$this->category,
													$this->url,
													$this->location,
													$this->venue_name,
													$this->longitude,
													$this->latitude,
													$this->start_date,
													$this->start_time,
													$this->end_date,
													$this->end_time,
													$this->date_created,
													$this->public,
													$this->limit_members,
													$this->recurrent,
													$this->is_spam,
													$this->approved,
													$this->rsvp,
													$this->all_day,
													$this->timezone,
													$this->group_approved ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'bpe_events_after_save', $this ); 
		
		return $this->id;
	}
	
	/**
	 * Delete a row
	 * @since 1.0
	 */
	public function delete()
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->events} WHERE id = %d", $this->id ) );
	}

	/**
	 * Delete everything for a user
	 * @since 1.0
	 */
	static function delete_for_user( $user_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->events} WHERE user_id = %d", $user_id ) );
	}
	
	/**
	 * Get it from the db
	 * @since 1.0
	 */
	static function get( $ids = false, $user_id = 0, $group_id = false, $name = false, $slug = false, $start_date = false, $start_time = false, $end_date = false, $end_time = false, $timezone = false, $day = false, $month = false, $year = false, $future = true, $past = false, $location = false, $venue_name = false, $venue = false, $map = false, $radius = false, $longitude = false, $latitude = false, $public = false, $rsvp = false, $category = false, $meta = false, $meta_key = false, $operator = '=', $begin = false, $end = false, $page = null, $per_page = null, $search_terms = false, $populate_extras = true, $sort = 'start_date_asc', $restrict = true, $spam = 0, $approved = true, $group_approved = true, $plus_days = 0 )
	{
		global $wpdb, $bpe, $bp;
		
		$paged_sql = array();

		if( $ids == -1 )
			return false;

		$paged_sql['select'][] = apply_filters( 'bpe_select_query_base', "SELECT SQL_CALC_FOUND_ROWS DISTINCT e.*, c.id as cat_id, c.name as cat_name, c.slug as cat_slug, em.meta_value as invitations" );
		
		if( bp_is_active( 'groups' ) )
			$paged_sql['select'][] = apply_filters( 'bpe_select_query_base_group', ", g.id as group_id, g.name as group_name, g.description as group_desc, g.status as group_status, g.slug as group_slug" );

		if( bpe_get_option( 'enable_address' ) === true && bp_is_active( 'groups' ) )
			$paged_sql['select'][] = apply_filters( 'bpe_select_query_base_address', ", gm.meta_value as address" );

		if( bpe_get_option( 'enable_tickets' ) === true )
			$paged_sql['select'][] = apply_filters( 'bpe_select_query_base_tickets', ", t.event_id as has_tickets" );

		if( in_array( bpe_get_option( 'enable_schedules' ), array( 1, 2, 3 ) ) )
			$paged_sql['select'][] = apply_filters( 'bpe_select_query_base_schedule', ", sh.event_id as has_schedule" );

		if( in_array( bpe_get_option( 'enable_documents' ), array( 1, 2, 3 ) ) )
			$paged_sql['select'][] = apply_filters( 'bpe_select_query_base_documents', ", d.event_id as has_document" );

		if( ! empty( $radius ) ) :
			if( empty( $latitude ) || empty( $longitude ) ) :
				$coords = bpe_get_search_coords( $location );
				$lat 	= $coords['lat'];
				$lng 	= $coords['lng'];
			else :
				$lat 	= $latitude;
				$lng 	= $longitude;
			endif;
			
			if( ! empty( $lat ) && ! empty( $lng ) ) :
				$dist 	= ( bpe_get_option( 'system' )  == 'm' ) ? 3959 : 6371;
				$sort 	= 'distance';
				$future = true;
				
				$paged_sql['select'][] = apply_filters( 'bpe_select_query_base_radius', $wpdb->prepare( ", ( {$dist} * acos( cos( radians( {$lat} ) ) * cos( radians( e.latitude ) ) * cos( radians( e.longitude ) - radians( {$lng} ) ) + sin( radians( {$lat} ) ) * sin( radians( e.latitude ) ) ) ) as distance" ), $dist, $lat, $lng );
			endif;
		endif;

		if( ! empty( $meta ) && ! empty( $meta_key ) )
			$paged_sql['select'][] = apply_filters( 'bpe_select_query_base_meta', ", em2.meta_value, em2.meta_key" );
		
		$paged_sql['select'][] = apply_filters( 'bpe_select_query_from', "FROM {$bpe->tables->events} e LEFT JOIN {$bpe->tables->categories} c ON c.id = e.category LEFT JOIN {$bpe->tables->events_meta} em ON em.event_id = e.id AND em.meta_key = 'invitations'" );

		if( bp_is_active( 'groups' ) )
			$paged_sql['select'][] = apply_filters( 'bpe_select_query_join_groups', "LEFT JOIN {$bp->groups->table_name} g ON g.id = e.group_id" );

		if( bpe_get_option( 'enable_tickets' ) === true )
			$paged_sql['select'][] = apply_filters( 'bpe_select_query_join_tickets', "LEFT JOIN {$bpe->tables->tickets} t ON e.id = t.event_id" );

		if( in_array( bpe_get_option( 'enable_schedules' ), array( 1, 2, 3 ) ) )
			$paged_sql['select'][] = apply_filters( 'bpe_select_query_join_schedules', "LEFT JOIN {$bpe->tables->schedules} sh ON e.id = sh.event_id" );
			
		if( in_array( bpe_get_option( 'enable_documents' ), array( 1, 2, 3 ) ) )
			$paged_sql['select'][] = apply_filters( 'bpe_select_query_join_documents', "LEFT JOIN {$bpe->tables->documents} d ON e.id = d.event_id" );
			
		if( bpe_get_option( 'enable_address' ) === true  && bp_is_active( 'groups' ) )
			$paged_sql['select'][] = apply_filters( 'bpe_select_query_join_address', $wpdb->prepare( "LEFT JOIN {$bp->groups->table_name_groupmeta} gm ON gm.group_id = e.group_id AND gm.meta_key = 'group_address'" ) );

		if( ! empty( $meta ) && ! empty( $meta_key ) )
		{
			$operator = ( ! in_array( $operator, array( '=', '!=', 'LIKE', 'NOT LIKE' ) ) ) ? '=' : $operator;
			
			if( $operator == 'LIKE' || $operator == 'NOT LIKE' )
			{
				$meta = like_escape( $wpdb->escape( $meta ) );
				$meta = "'%%{$meta}%%'";
			}
			else
				$meta = "'{$meta}'";
				
			if( is_array( $meta_key ) )
			{
				$meta_keys = array();
				foreach( $meta_key as $key )
					$meta_keys[] = "'{$key}'";
					
				$meta_keys = join( ',', $meta_keys );
				
				$meta_sql = "em2.meta_key IN ({$meta_keys})";
			}
			else
				$meta_sql = "em2.meta_key = '{$meta_key}'";
				
			$paged_sql['select'][] = apply_filters( 'bpe_core_select_query_join_meta', $wpdb->prepare( "RIGHT JOIN {$bpe->tables->events_meta} em2 ON em2.event_id = e.id AND em2.meta_value {$operator} {$meta} AND {$meta_sql}" ), $meta_sql, $meta, $operator );
		}

		if( ! empty( $ids ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_ids', $wpdb->prepare( "e.id in ({$ids})" ), $ids );

		if( ! empty( $group_id ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_group_id', $wpdb->prepare( "e.group_id = %d", (int)$group_id ), $group_id );

		if( ! empty( $user_id ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_user_id', $wpdb->prepare( "e.user_id = %d", (int)$user_id ), $user_id );

		if( ! empty( $name ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_name', $wpdb->prepare( "e.name = %s", $name ), $name );
			
		if( ! empty( $slug ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_slug', $wpdb->prepare( "e.slug = %s", $slug ), $slug );

		if( ! empty( $start_date ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_start_date', $wpdb->prepare( "e.start_date = %s", $start_date ), $start_date );

		if( ! empty( $start_time ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_start_time', $wpdb->prepare( "e.start_time = %s", $start_time ), $start_time );

		if( ! empty( $end_date ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_end_date', $wpdb->prepare( "e.end_date = %s", $end_date ), $end_date );

		if( ! empty( $end_time ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_end_time', $wpdb->prepare( "e.end_time = %s", $end_time ), $end_time );

		if( ! empty( $timezone ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_timezone', $wpdb->prepare( "e.timezone = %s", $timezone ), $timezone );

		if( $spam == 0 || $spam == 1 )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_is_spam_0_1', $wpdb->prepare( "e.is_spam = %d", $spam ), $spam );
			
		elseif( $spam == 2 )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_is_spam_2', $wpdb->prepare( "( e.is_spam = 0 OR e.is_spam = 1 )" ) );

		if( ! empty( $month ) && ! empty( $year ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_month_year', $wpdb->prepare( "( ( month(e.start_date) = '{$month}' AND year(e.start_date) = '{$year}' ) OR ( month(e.end_date) = '{$month}' AND year(e.end_date) = '{$year}' ) OR ( '{$year}-{$month}-15' BETWEEN e.start_date AND e.end_date ) )" ), $month, $year );
		
		if( ! empty( $begin ) && ! empty( $end ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_begin_end', $wpdb->prepare( "e.start_date >= '{$begin}' AND e.start_date <= '{$end}'" ), $begin, $end );

		if( ! empty( $day ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_day', $wpdb->prepare( "'{$day}' BETWEEN e.start_date AND e.end_date" ), $day );
		
		if( ! empty( $location ) && empty( $radius ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_location_no_radius', $wpdb->prepare( "e.location = %s", $location ), $location );

		if( ! empty( $venue_name ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_venue_name', $wpdb->prepare( "e.venue_name = %s", $venue_name ), $venue_name );

		if( ! empty( $venue ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_venue', $wpdb->prepare( "e.venue_name = %s OR e.location = %s", $venue, $venue ), $venue );

		if( ! empty( $map ) && empty( $latitude ) && empty( $longitude ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_map_no_coordinates', $wpdb->prepare( "e.latitude != 0.00000000000000 AND e.longitude != 0.00000000000000" ) );

		if( ! empty( $longitude ) && ! $map )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_longitude_no_map', $wpdb->prepare( "e.longitude = %s", $longitude ), $longitude );

		if( ! empty( $latitude ) && ! $map )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_latitude_no_map', $wpdb->prepare( "e.latitude = %s", $latitude ), $latitude );

		if( ! empty( $public ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_public', $wpdb->prepare( "e.public = %d", (int)$public ), $public );

		if( ! empty( $rsvp ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_rsvp', $wpdb->prepare( "e.rsvp = %d", (int)$rsvp ), $rsvp );

		if( ! empty( $category ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_category', $wpdb->prepare( "e.category = %d", (int)$category ), $category );

		if( $approved )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_approved_1', $wpdb->prepare( "e.approved = 1" ) );
		else
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_approved_0', $wpdb->prepare( "e.approved = 0" ) );

		if( $group_approved )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_group_approved_1', $wpdb->prepare( "e.group_approved = 1" ) );
		else
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_group_approved_0', $wpdb->prepare( "e.group_approved = 0" ) );

		if( ! empty( $future ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_future', $wpdb->prepare( "CONCAT( e.end_date, ' ', e.end_time ) >= NOW()" ) );

		if( ! empty( $past ) )
			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_past', $wpdb->prepare( "CONCAT( e.end_date, ' ', e.end_time ) < NOW()" ) );

		if( ! empty( $plus_days ) ) :
			$date = new DateTime();
			
			$date->modify( '+'. absint( $plus_days ) .' days' );
			
			$plus_days_date = $date->format( 'Y-m-d H:i:s' );

			$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_max_days', $wpdb->prepare( "CONCAT( e.start_date, ' ', e.start_time ) >= NOW() AND CONCAT( e.start_date, ' ', e.start_time ) <= '{$plus_days_date}'" ), $plus_days_date );
		endif;

				if( $search_terms )
		{
			$search_terms = bpe_sanitize_for_keywords( $search_terms, false );
			preg_match_all( '/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $search_terms, $matches );
			$sts = array_map( '_search_terms_tidy', $matches[0] );
			
			$term_sql = array();
			
			$sts = apply_filters( 'bpe_core_where_query_search_terms', array_unique( $sts ) );

			foreach( $sts as $st )
			{
				$st = like_escape( $wpdb->escape( $st ) );
				$term_sql[] = "( e.name LIKE '%%{$st}%%' OR e.description LIKE '%%{$st}%%' OR e.location LIKE '%%{$st}%%' OR e.start_date LIKE '%%{$st}%%' OR e.end_date LIKE '%%{$st}%%' )";
			}
			
			if( count( $term_sql ) > 0 )
				$paged_sql['where'][] = apply_filters( 'bpe_core_where_query_term_sql', '( '. join( ' OR ', (array)$term_sql ) .' )', $term_sql );
		}
		
		if( ! empty( $radius ) )
			$paged_sql['having'] = apply_filters( 'bpe_core_having_query_radius', $wpdb->prepare( "HAVING distance < {$radius}" ), $radius );
		
		switch( $sort )
		{
			case 'end_date_asc':
				$paged_sql['orderby'] = $wpdb->prepare( "ORDER BY CONCAT( e.end_date, ' ', e.end_time ) ASC" );
				break;

			case 'end_date_desc':
				$paged_sql['orderby'] = $wpdb->prepare( "ORDER BY CONCAT( e.end_date, ' ', e.end_time ) DESC" );
				break;

			case 'start_date_asc': default:
				$paged_sql['orderby'] = $wpdb->prepare( "ORDER BY CONCAT( e.start_date, ' ', e.start_time ) ASC" );
				break;

			case 'start_date_desc':
				$paged_sql['orderby'] = $wpdb->prepare( "ORDER BY CONCAT( e.start_date, ' ', e.start_time ) DESC" );
				break;
				
			case 'date_created_asc':
				$paged_sql['orderby'] = $wpdb->prepare( "ORDER BY e.date_created ASC" );
				break;

			case 'date_created_desc':
				$paged_sql['orderby'] = $wpdb->prepare( "ORDER BY e.date_created DESC" );
				break;
				
			case 'calendar':
				$paged_sql['orderby'] = $wpdb->prepare( "ORDER BY CONCAT( e.start_date, ' ', e.start_time ) ASC" );
				break;

			case 'random':
				$paged_sql['orderby'] = $wpdb->prepare( "ORDER BY RAND()" );
				break;

			case 'distance':
				$paged_sql['orderby'] = $wpdb->prepare( "ORDER BY distance" );
				break;
				
			default:
				$paged_sql['orderby'] = '';
				break;
		}
		
		if( $per_page && $page )
			$paged_sql['pagination'] = $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $per_page), intval( $per_page ) );

		// put it all together
		$p_sql[] = join( ' ', (array)$paged_sql['select'] );

		if( ! empty( $paged_sql['where'] ) )
			$p_sql[] = "WHERE " . join( ' AND ', (array)$paged_sql['where'] );

		if( ! empty( $paged_sql['having'] ) )
			$p_sql[] = $paged_sql['having'];
			
		$p_sql[] = apply_filters( 'bpe_core_sort_query_orderby', $paged_sql['orderby'], $sort );
		
		if( $per_page && $page )
			$p_sql[] = apply_filters( 'bpe_core_pagination_query', $paged_sql['pagination'], $per_page, $page );
		
		$query = join( ' ', (array)$p_sql );

		/* Get paginated results */
		$paged_events = $wpdb->get_results( apply_filters( 'bpe_core_main_query', $query ) );

		/* Get total events results */
		$total_events = intval( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );

		if( ! empty( $populate_extras ) )
		{
			$eids = array();
			foreach( (array)$paged_events as $event )
				$eids[] = $event->id;
				
			$ids = $wpdb->escape( join( ',', (array)$eids ) );
			 
			$paged = self::get_event_extras( &$paged_events, $total_events, $restrict, $ids );
			
			$paged_events = $paged['events'];
			$total_events = $paged['total'];
		}

		unset( $paged_sql );
		
		return array( 'events' => $paged_events, 'total' => $total_events );
	}
	
	/**
	 * Get some extras
	 * @since 1.0
	 */
	static function get_event_extras( $paged_events, $total_events = false, $restrict = true, $ids )
	{
		global $wpdb, $bp, $bpe;
		
		if( empty( $ids ) )
			$ids = 0;
		
		$members = $wpdb->get_results( $wpdb->prepare( "
			SELECT event_id, COUNT(id) as attendees 
			FROM {$bpe->tables->members} 
			WHERE rsvp != 0 
			AND role != 'admin' 
			AND event_id IN ({$ids}) 
			GROUP BY event_id
		" ) );
		
		$users 	 = $wpdb->get_results( $wpdb->prepare( "
			SELECT event_id, user_id, rsvp, role 
			FROM {$bpe->tables->members} 
			WHERE rsvp != 0 
			AND event_id IN ({$ids})
		" ) );
		
		$metas 	 = $wpdb->get_results( $wpdb->prepare( "
			SELECT event_id, meta_key, meta_value 
			FROM {$bpe->tables->events_meta} 
			WHERE event_id IN ({$ids})
		" ) );

		for( $i = 0; $i < count( $paged_events ); $i++ )
		{
			$paged_events[$i]->attendees 		  = 0;
			$paged_events[$i]->attending_status   = array();
			$paged_events[$i]->attendee_ids 	  = array();
			$paged_events[$i]->not_attendee_ids   = array();
			$paged_events[$i]->maybe_attendee_ids = array();
			$paged_events[$i]->organizer_ids 	  = array();
			$paged_events[$i]->admin_ids 		  = array();
			
			$paged_events[$i]->has_tickets	= ( is_null( $paged_events[$i]->has_tickets  ) ) ? false : true;
			$paged_events[$i]->has_schedule	= ( is_null( $paged_events[$i]->has_schedule ) ) ? false : true;
			$paged_events[$i]->has_document	= ( is_null( $paged_events[$i]->has_document ) ) ? false : true;
			
			foreach( (array)$members as $member )
			{
				if( $member->event_id == $paged_events[$i]->id )
					$paged_events[$i]->attendees = apply_filters( 'bpe_filter_event_attendee_count', $member->attendees, $member->event_id );
			}

			foreach( (array)$users as $user )
			{
				if( $user->event_id == $paged_events[$i]->id )
				{
					$key = $user->role .'_ids';

					$paged_events[$i]->{$key}[] = $user->user_id;
					$paged_events[$i]->attending_status[$user->user_id] = $user->rsvp;
				}
			}

			foreach( (array)$metas as $meta )
			{
				if( $meta->event_id == $paged_events[$i]->id )
					$paged_events[$i]->meta->{$meta->meta_key} = maybe_unserialize( stripslashes( $meta->meta_value ) );
			}
		}
		
		// do some access checks after we have all the data
		if( $restrict )
		{
			foreach( $paged_events as $k => $event )
			{
				if( bpe_restrict_event_access( $event ) )
				{
					// remove from array
					unset( $paged_events[$k] );
					
					// adjust the total number
					if( $total_events )
						$total_events--;
				}
			}
		
			// reset the array keys
			$paged_events = array_values( $paged_events );
		}

		return array( 'events' => $paged_events, 'total' => $total_events );
	}
	
	/**
	 * Get event id from the slug
	 * @since 1.0
	 */
	static function get_id_from_slug( $slug )
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->events} WHERE slug = %s", $slug ) );
	}

	/**
	 * Reset a group id
	 * @since 1.0
	 */
	static function remove_group_id( $group_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->events} SET group_id = '' WHERE group_id = %d", $group_id ) );
	}

	/**
	 * Set events to spam
	 * @since 1.2.4
	 */
	static function set_events_to_spam( $user_id, $is_spam )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->events} SET is_spam = %d WHERE user_id = %d", $is_spam, $user_id ) );
	}

	/**
	 * Approve event
	 * @since 1.4
	 */
	static function approve_event( $event_id, $approved = 1 )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->events} SET approved = %d WHERE id = %d", $approved, $event_id ) );
	}

	/**
	 * Group approve event
	 * @since 2.0
	 */
	static function group_approve_event( $event_id, $approved = 1 )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->events} SET group_approved = %d WHERE id = %d", $approved, $event_id ) );
	}

	/**
	 * Number of events to be approved
	 * @since 1.5
	 */
	static function get_approvable_events()
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bpe->tables->events} WHERE approved = 0" ) );
	}

	/**
	 * Set events to spam
	 * @since 1.2.4
	 */
	static function change_spam_by_ids( $ids, $is_spam )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->events} SET is_spam = %d WHERE id IN ({$ids})", $is_spam ) );
	}

	/**
	 * Delete by ids
	 * @since 1.2.4
	 */
	static function delete_by_ids( $ids )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->events} WHERE id IN ({$ids})" ) );
	}
	
	/**
	 * Is the current user an admin
	 * @since 1.0
	 */
	static function is_user_event_admin( $event_id )
	{
		global $wpdb, $bpe, $bp;
		
		$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->events} WHERE user_id = %d AND id = %d", bp_loggedin_user_id(), $event_id ) );
		
		if( $id )
			return true;
			
		return false;
	}
	
	/**
	 * Checks if the event is over
	 * @since 1.0
	 */
	static function is_event_over( $event_id )
	{
		global $wpdb, $bpe, $bp;
		
		$date = $wpdb->get_var( $wpdb->prepare( "SELECT CONCAT( end_date, ' ', end_time ) as date FROM {$bpe->tables->events} WHERE id = %d", $event_id ) );
		
		if( $date <= bp_core_current_time() )
			return true;
			
		return false;
	}

	/**
	 * Get groups last published date
	 * @since 1.0
	 */
	static function group_get_last_published( $cat_id = false, $tz = false, $venue = false )
	{
		global $bp, $bpe, $wpdb;
		
		if( $cat_id )
			$sql = $wpdb->prepare( "AND category = %d", $cat_id );
			
		if( $tz )
			$sql = $wpdb->prepare( "AND timezone = %s", $tz );

		if( $venue )
			$sql = $wpdb->prepare( "AND location = %s", $venue );
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT date_created FROM {$bpe->tables->events} WHERE group_id = %d {$sql} ORDER BY date_created ASC LIMIT 1", bp_get_current_group_id() ) );
	}

	/**
	 * Get users last published date
	 * @since 1.0
	 */
	static function user_get_last_published( $cat_id = false, $tz = false, $venue = false )
	{
		global $bp, $bpe, $wpdb;

		if( $cat_id )
			$sql = $wpdb->prepare( "AND category = %d", $cat_id );

		if( $tz )
			$sql = $wpdb->prepare( "AND timezone = %s", $tz );

		if( $venue )
			$sql = $wpdb->prepare( "AND location = %s", $venue );
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT date_created FROM {$bpe->tables->events} WHERE user_id = %d {$sql} ORDER BY date_created ASC LIMIT 1", bp_displayed_user_id() ) );
	}

	/**
	 * Get category last published date
	 * @since 1.1
	 */
	static function category_get_last_published( $cat )
	{
		global $bp, $bpe, $wpdb;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT date_created FROM {$bpe->tables->events} WHERE category = %d ORDER BY date_created ASC LIMIT 1", $cat ) );
	}

	/**
	 * Get timezone last published date
	 * @since 1.7
	 */
	static function timezone_get_last_published( $zone )
	{
		global $bp, $bpe, $wpdb;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT date_created FROM {$bpe->tables->events} WHERE timezone = %s ORDER BY date_created ASC LIMIT 1", $zone ) );
	}

	/**
	 * Get venue last published date
	 * @since 1.7
	 */
	static function venue_get_last_published( $venue )
	{
		global $bp, $bpe, $wpdb;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT date_created FROM {$bpe->tables->events} WHERE location = %s ORDER BY date_created ASC LIMIT 1", $venue ) );
	}
	
	/**
	 * Get global last published date
	 * @since 1.0
	 */
	static function get_last_published()
	{
		global $bpe, $wpdb;
	
		return $wpdb->get_var( $wpdb->prepare( "SELECT date_created FROM {$bpe->tables->events} ORDER BY date_created ASC LIMIT 1" ) );
	}
	
	/**
	 * Get all event users
	 * @since 1.2.4
	 */
	static function get_event_users()
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT e.user_id FROM {$bpe->tables->events} e" ) );
	}

	/**
	 * Get all event groups
	 * @since 1.2.4
	 */
	static function get_event_groups()
	{
		global $wpdb, $bpe, $bp;
		
		if( ! bp_is_active( 'groups' ) )
			return false;
		
		return $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT e.group_id, g.name FROM {$bpe->tables->events} e LEFT JOIN {$bp->groups->table_name} g ON g.id = e.group_id WHERE e.group_id != 0" ) );
	}

	/**
	* Get all timezones
	* @since 1.7.10
	*/
	static function get_timezones()
	{
		global $wpdb, $bpe, $bp;
		
		$timezones = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT timezone FROM {$bpe->tables->events} WHERE public = 1 AND approved = 1 AND group_approved = 1" ) );

		$tzs = array();
		foreach( $timezones as $tz )
		{
			if( empty( $tz ) )
				continue;
			
			$slug = sanitize_title_with_dashes( str_replace( '/', '-', $tz ) );
			$tzs[$slug] = $tz;
		}
		
		asort( $tzs );
		
		return $tzs;
	}
	
	/**
	* Get all venues
	* @since 1.7.10
	*/
	static function get_venues()
	{
		global $wpdb, $bpe, $bp;
		
		$venues = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT venue_name, location FROM {$bpe->tables->events} WHERE public = 1 AND approved = 1 AND group_approved = 1" ) );

		$vs = array();
		foreach( $venues as $val )
		{
			if( empty( $val->venue_name ) && empty( $val->location ) )
				continue;
				
			$venue = ( ! empty( $val->venue_name ) ) ? $val->venue_name : $val->location;
			$slug = sanitize_title_with_dashes( $venue );
			
			$vs[$slug] = $venue;
		}
		
		asort( $vs );
		
		return $vs;
	}
} 
?>