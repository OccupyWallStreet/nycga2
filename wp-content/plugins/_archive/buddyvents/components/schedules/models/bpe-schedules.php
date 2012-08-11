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

class Buddyvents_Schedules
{
	public $id;
	public $event_id;
	public $day;
	public $start;
	public $end;
	public $description;
	
	/**
	 * PHP5 Constructor
	 * @since 1.5
	 */
	public function __construct( $id = null, $event_id = null )
	{
		global $bpe, $wpdb;

		if( $id )
		{
			$this->id = $id;
			$this->populate();
		}
		elseif( $event_id )
		{
			$this->event_id = $event_id;
			$this->populate_by_event_id();
		}
	}

	/**
	 * Get a row from the database
	 * @since 1.5
	 */
	public function populate()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->schedules} WHERE id = %d", $this->id ) );

		$this->event_id		= $table->event_id;
		$this->day			= $table->day;
		$this->start		= $table->start;
		$this->end			= $table->end;
		$this->description	= $table->description;
	}

	/**
	 * Get a row from the database
	 * @since 1.5
	 */
	public function populate_by_event_id()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->schedules} WHERE event_id = %d", $this->event_id ) );

		$this->id	 		= $table->id;
		$this->end		 	= $table->end;
		$this->day	 	 	= $table->day;
		$this->start 		= $table->start;
		$this->description	= $table->description;
	}

	/**
	 * Save or uptimestamp a row
	 * @since 1.5
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->event_id		= apply_filters( 'bpe_events_before_save_schedules_event_id', $this->event_id, $this->id );
		$this->day	 	 	= apply_filters( 'bpe_events_before_save_schedules_day', $this->day, $this->id );
		$this->start		= apply_filters( 'bpe_events_before_save_schedules_start', $this->start, $this->id );
		$this->end			= apply_filters( 'bpe_events_before_save_schedules_end', $this->end, $this->id );
		$this->description	= apply_filters( 'bpe_events_before_save_schedules_description', $this->description, $this->id );
		
		/* Call a before save action here */
		do_action( 'bpe_events_before_schedule_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->schedules} SET
													event_id = %d,
													day = %s,
													start = %s,
													end = %s,
													description = %s
											WHERE id = %d",
													$this->event_id,
													$this->day,
													$this->start,
													$this->end,
													$this->description,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->schedules} (
													event_id,
													day,
													start,
													end,
													description
											) VALUES ( 
													%d, %s, %s, %s, %s
											)",
													$this->event_id,
													$this->day,
													$this->start,
													$this->end,
													$this->description ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'bpe_events_after_schedule_save', $this ); 
		
		return $this->id;
	}
	
	/**
	 * Delete a row
	 * @since 1.5
	 */
	public function delete()
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->schedules} WHERE id = %d", $this->id ) );
	}
	
	/**
	 * Get schedules from the database
	 * @since 1.5
	 */
	static function get( $event_id, $day, $start, $end, $page, $per_page, $search_terms )
	{
		global $wpdb, $bpe, $bp;
		
		$paged_sql = array();
		
		$paged_sql['select'][] = apply_filters( 'bpe_schedules_select_query_base', "SELECT SQL_CALC_FOUND_ROWS s.* FROM {$bpe->tables->schedules} s" );

		if( ! empty( $event_id ) )
			$paged_sql['where'][] = apply_filters( 'bpe_schedules_where_query_event_id', $wpdb->prepare( "s.event_id = %d", (int)$event_id ), $event_id );

		if( ! empty( $day ) )
			$paged_sql['where'][] = apply_filters( 'bpe_schedules_where_query_day', $wpdb->prepare( "s.day = %s", $day ), $day );

		if( ! empty( $start ) )
			$paged_sql['where'][] = apply_filters( 'bpe_schedules_where_query_start', $wpdb->prepare( "s.start = %s", $start ), $start );

		if( ! empty( $end ) )
			$paged_sql['where'][] = apply_filters( 'bpe_schedules_where_query_end', $wpdb->prepare( "s.end = %s", $end ), $end );

		if( $search_terms )
		{
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$paged_sql['where'][] = "( s.description LIKE '%%{$search_terms}%%' OR s.day LIKE '%%{$search_terms}%%' OR s.start LIKE '%%{$search_terms}%%' OR s.end LIKE '%%{$search_terms}%%' )";
		}

		$paged_sql['orderby'] = $wpdb->prepare( "ORDER BY CONCAT( s.day, ' ', s.start ) ASC" );
		
		if( $per_page && $page )
			$paged_sql['pagination'] = $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $per_page), intval( $per_page ) );

		// put it all together
		$p_sql[] = join( ' ', (array)$paged_sql['select'] );

		if( ! empty( $paged_sql['where'] ) )
			$p_sql[] = "WHERE " . join( ' AND ', (array)$paged_sql['where'] );
		
		$p_sql[] = $paged_sql['orderby'];
		
		if( $per_page && $page )
			$p_sql[] = $paged_sql['pagination'];

		/* Get paginated results */
		$paged_schedules = $wpdb->get_results( apply_filters( 'bpe_schedules_main_query', join( ' ', (array)$p_sql ) ) );

		/* Get total events results */
		$total_schedules = intval( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );

		unset( $paged_sql );
		
		return array( 'schedules' => $paged_schedules, 'total' => $total_schedules );
	}
	
	/**
	 * Has an event a schedule
	 * @since 1.5
	 */
	static function has_event_schedule( $id )
	{
		global $wpdb, $bpe;
		
		if( $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->schedules} WHERE event_id = %d LIMIT 1", $id ) ) )
			return true;
			
		return false;
	}
	
	/**
	 * Delete all schedules for an event
	 * @since 1.5
	 */
	static function delete_schedules_for_event( $id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->schedules} WHERE event_id = %d", $id ) );
	}
	
	/**
	 * Delete schedules by ids
	 * @since 1.5
	 */
	static function delete_schedules_by_ids( $ids )
	{
		global $wpdb, $bpe;
		
		$ids = $wpdb->escape( implode( ',', (array)$ids ) );

		if( empty( $ids ) )
			$ids = 0;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->schedules} WHERE id IN ({$ids})" ) );
	}

	/**
	 * Has an event a schedule
	 * @since 1.5
	 */
	static function schedule_amount( $id )
	{
		global $wpdb, $bpe;
		
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bpe->tables->schedules} WHERE event_id = %d", $id ) );
		
		if( ! $count )
			return 1;
			
		return $count + 1;
	}
} 
?>