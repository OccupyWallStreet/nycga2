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

class Buddyvents_Documents
{
	public $id;
	public $event_id;
	public $name;
	public $description;
	public $url;
	public $type;
	
	/**
	 * PHP5 Constructor
	 * @since 1.7
	 */
	public function __construct( $id = null, $event_id = null )
	{
		global $bpe, $wpdb;

		if( $id )
		{
			$this->id = $id;
			$this->populate();
		}
	}

	/**
	 * Get a row from the database
	 * @since 1.7
	 */
	public function populate()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->documents} WHERE id = %d", $this->id ) );

		$this->event_id		= $table->event_id;
		$this->name			= $table->name;
		$this->description	= $table->description;
		$this->url			= $table->url;
		$this->type			= $table->type;
	}

	/**
	 * Save or uptimestamp a row
	 * @since 1.7
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->event_id		= apply_filters( 'bpe_events_before_save_documents_event_id', $this->event_id, $this->id );
		$this->name	 	 	= apply_filters( 'bpe_events_before_save_documents_name', $this->name, $this->id );
		$this->description	= apply_filters( 'bpe_events_before_save_documents_description', $this->description, $this->id );
		$this->url			= apply_filters( 'bpe_events_before_save_documents_url', $this->url, $this->id );
		$this->type			= apply_filters( 'bpe_events_before_save_documents_type', $this->type, $this->id );
		
		/* Call a before save action here */
		do_action( 'bpe_events_before_schedule_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->documents} SET
													event_id = %d,
													name = %s,
													description = %s,
													url = %s,
													type = %s
											WHERE id = %d",
													$this->event_id,
													$this->name,
													$this->description,
													$this->url,
													$this->type,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->documents} (
													event_id,
													name,
													description,
													url,
													type
											) VALUES ( 
													%d, %s, %s, %s, %s
											)",
													$this->event_id,
													$this->name,
													$this->description,
													$this->url,
													$this->type ) );
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
	 * @since 1.7
	 */
	public function delete()
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->documents} WHERE id = %d", $this->id ) );
	}
	
	/**
	 * Get documents from the database
	 * @since 1.7
	 */
	static function get( $event_id, $name, $type, $page, $per_page, $search_terms )
	{
		global $wpdb, $bpe, $bp;
		
		$paged_sql = array();
		
		$paged_sql['select'][] = apply_filters( 'bpe_docs_select_base', "SELECT SQL_CALC_FOUND_ROWS s.* FROM {$bpe->tables->documents} s" );

		if( ! empty( $event_id ) )
			$paged_sql['where'][] = apply_filters( 'bpe_docs_where_event_id', $wpdb->prepare( "s.event_id = %d", (int)$event_id ), $event_id );

		if( ! empty( $name ) )
			$paged_sql['where'][] = apply_filters( 'bpe_docs_where_name', $wpdb->prepare( "s.name = %s", $name ), $name );

		if( ! empty( $type ) )
			$paged_sql['where'][] = apply_filters( 'bpe_docs_where_type', $wpdb->prepare( "s.type = %s", $type ), $type );

		if( $search_terms )
		{
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$paged_sql['where'][] = apply_filters( 'bpe_docs_where_search_terms', "( s.type LIKE '%%{$search_terms}%%' OR s.name LIKE '%%{$search_terms}%%' OR s.description LIKE '%%{$search_terms}%%' )", $search_terms );
		}

		$paged_sql['orderby'] = apply_filters( 'bpe_docs_where_orderby', $wpdb->prepare( "ORDER BY s.name ASC" ) );
		
		if( $per_page && $page )
			$paged_sql['pagination'] = apply_filters( 'bpe_docs_pagination', $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $per_page), intval( $per_page ) ), $per_page, $page );

		// put it all together
		$p_sql[] = join( ' ', (array)$paged_sql['select'] );

		if( ! empty( $paged_sql['where'] ) )
			$p_sql[] = "WHERE " . join( ' AND ', (array)$paged_sql['where'] );
		
		$p_sql[] = $paged_sql['orderby'];
		
		if( $per_page && $page )
			$p_sql[] = $paged_sql['pagination'];

		/* Get paginated results */
		$paged_documents = $wpdb->get_results( apply_filters( 'bpe_documents_main_query', join( ' ', (array)$p_sql ) ) );

		/* Get total events results */
		$total_documents = intval( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );

		unset( $paged_sql );
		
		return array( 'documents' => $paged_documents, 'total' => $total_documents );
	}
	
	/**
	 * Has an event at least 1 document
	 * @since 1.7
	 */
	static function has_event_document( $id )
	{
		global $wpdb, $bpe;
		
		if( $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->documents} WHERE event_id = %d LIMIT 1", $id ) ) )
			return true;
			
		return false;
	}
	
	/**
	 * Delete all documents for an event
	 * @since 1.7
	 */
	static function delete_documents_for_event( $id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->documents} WHERE event_id = %d", $id ) );
	}

	/**
	 * Delete all documents for an event
	 * @since 1.7
	 */
	static function get_file_links( $ids )
	{
		global $wpdb, $bpe;

		$ids = $wpdb->escape( implode( ',', (array)$ids ) );
		
		if( empty( $ids ) )
			$ids = 0;
		
		return $wpdb->get_col( $wpdb->prepare( "SELECT url FROM {$bpe->tables->documents} WHERE id IN ({$ids})" ) );
	}
	
	/**
	 * Delete documents by ids
	 * @since 1.7
	 */
	static function delete_documents_by_ids( $ids )
	{
		global $wpdb, $bpe;
		
		if( empty( $ids ) )
			return false;
		
		$ids = $wpdb->escape( implode( ',', (array)$ids ) );

		if( empty( $ids ) )
			$ids = 0;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->documents} WHERE id IN ({$ids})" ) );
	}

	/**
	 * Get all for an event
	 * @since 1.7
	 */
	static function get_docs_for_event( $event_id )
	{
		global $bpe, $wpdb;
		
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bpe->tables->documents} WHERE event_id = %d", $event_id ) );
	}
} 
?>