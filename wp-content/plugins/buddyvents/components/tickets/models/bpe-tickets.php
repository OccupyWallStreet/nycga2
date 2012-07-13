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

class Buddyvents_Tickets
{
	public $id;
	public $event_id;
	public $name;
	public $description;
	public $price;
	public $currency;
	public $quantity;
	public $start_sales;
	public $end_sales;
	public $min_tickets;
	public $max_tickets;

	/**
	 * PHP5 Constructor
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @param	int		$id
	 * @param	int		$event_id
	 */
	public function __construct( $id = null, $event_id = null )
	{
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
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 */
	public function populate()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->tickets} WHERE id = %d", $this->id ) );

		$this->event_id	 	= $table->event_id;
		$this->name	 	 	= $table->name;
		$this->description 	= $table->description;
		$this->price 		= $table->price;
		$this->currency	 	= $table->currency;
		$this->quantity		= $table->quantity;
		$this->start_sales	= $table->start_sales;
		$this->end_sales	= $table->end_sales;
		$this->min_tickets	= $table->min_tickets;
		$this->max_tickets	= $table->max_tickets;
	}

	/**
	 * Get a row from the database
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 */
	public function populate_by_event_id()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->tickets} WHERE event_id = %d", $this->event_id ) );

		$this->id		 	= $table->id;
		$this->name	 	 	= $table->name;
		$this->description 	= $table->description;
		$this->price 		= $table->price;
		$this->currency	 	= $table->currency;
		$this->quantity		= $table->quantity;
		$this->start_sales	= $table->start_sales;
		$this->end_sales	= $table->end_sales;
		$this->min_tickets	= $table->min_tickets;
		$this->max_tickets	= $table->max_tickets;
	}

	/**
	 * Save or uptimestamp a row
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * 
	 * @uses	apply_filters()
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->event_id	 	= apply_filters( 'bpe_tickets_before_save_tickets_event_id', $this->event_id, $this->id );
		$this->name	 	 	= apply_filters( 'bpe_tickets_before_save_tickets_name', $this->name, $this->id );
		$this->description	= apply_filters( 'bpe_tickets_before_save_tickets_description', $this->description, $this->id );
		$this->price		= apply_filters( 'bpe_tickets_before_save_tickets_price', $this->price, $this->id );
		$this->currency	 	= apply_filters( 'bpe_tickets_before_save_tickets_currency', $this->currency, $this->id );
		$this->quantity		= apply_filters( 'bpe_tickets_before_save_tickets_quantity', $this->quantity, $this->id );
		$this->start_sales	= apply_filters( 'bpe_tickets_before_save_tickets_start_sales', $this->start_sales, $this->id );
		$this->end_sales	= apply_filters( 'bpe_tickets_before_save_tickets_end_sales', $this->end_sales, $this->id );
		$this->min_tickets	= apply_filters( 'bpe_tickets_before_save_tickets_min_tickets', $this->min_tickets, $this->id );
		$this->max_tickets	= apply_filters( 'bpe_tickets_before_save_tickets_max_tickets', $this->max_tickets, $this->id );
		
		/* Call a before save action here */
		do_action( 'bpe_tickets_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->tickets} SET
													event_id = %d,
													name = %s,
													description = %s,
													price = %s,
													currency = %s,
													quantity = %d,
													start_sales = %s,
													end_sales = %s,
													min_tickets = %d,
													max_tickets = %d
											WHERE id = %d",
													$this->event_id,
													$this->name,
													$this->description,
													$this->price,
													$this->currency,
													$this->quantity,
													$this->start_sales,
													$this->end_sales,
													$this->min_tickets,
													$this->max_tickets,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->tickets} (
													event_id,
													name,
													description,
													price,
													currency,
													quantity,
													start_sales,
													end_sales,
													min_tickets,
													max_tickets
											) VALUES ( 
													%d, %s, %s, %s, %s, %d, %s, %s, %d, %d
											)",
													$this->event_id,
													$this->name,
													$this->description,
													$this->price,
													$this->currency,
													$this->quantity,
													$this->start_sales,
													$this->end_sales,
													$this->min_tickets,
													$this->max_tickets ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'bpe_tickets_after_save', $this ); 
		
		return $this->id;
	}
	
	/**
	 * Delete a row
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 */
	public function delete()
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->tickets} WHERE id = %d", $this->id ) );
	}
	
	/**
	 * Get tickets
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * 
	 * @param	int		$event_id		optional
	 * @param	string	$name			optional
	 * @param	float	$price			optional
	 * @param	string	$currency		optional
	 * @param	int		$quantity		optional
	 * @param	string	$start_sales	optional
	 * @param	string	$end_sales		optional
	 * @param	int		$min_tickets	optional
	 * @param	int		$max_tickets	optional
	 * @param	bool	$available		optional
	 * @param	int		$page			optional
	 * @param	int		$per_page		optional
	 * @param	string	$search_terms	optional
	 * 
	 * @return 	array
	 * 
	 * @uses	like_escape()
	 * @uses	bp_core_current_time()
	 */
	static public function get( $event_id = false, $name = false, $price = false, $currency = false, $quantity = false, $start_sales = false, $end_sales = false, $min_tickets = false, $max_tickets = false, $available = false, $page = false, $per_page = false, $search_terms = false )
	{
		global $wpdb, $bpe, $bp;
		
		$paged_sql = array();
		
		$paged_sql['select'][] = apply_filters( 'bpe_tickets_select_query_base', "SELECT SQL_CALC_FOUND_ROWS t.* FROM {$bpe->tables->tickets} t" );

		if( ! empty( $event_id ) )
			$paged_sql['where'][] = apply_filters( 'bpe_tickets_where_query_event_id', $wpdb->prepare( "t.event_id = %d", (int)$event_id ), $event_id );

		if( ! empty( $name ) )
			$paged_sql['where'][] = apply_filters( 'bpe_tickets_where_query_name', $wpdb->prepare( "t.name = %s", $name ), $name );

		if( ! empty( $price ) )
			$paged_sql['where'][] = apply_filters( 'bpe_tickets_where_query_price', $wpdb->prepare( "t.price = %d", (int)$price ), $price );

		if( ! empty( $currency ) )
			$paged_sql['where'][] = apply_filters( 'bpe_tickets_where_query_currency', $wpdb->prepare( "t.currency = %s", $currency ), $currency );

		if( ! empty( $quantity ) )
			$paged_sql['where'][] = apply_filters( 'bpe_tickets_where_query_quantity', $wpdb->prepare( "t.quantity = %d", (int)$quantity ), $quantity );

		if( ! empty( $start_sales ) )
			$paged_sql['where'][] = apply_filters( 'bpe_tickets_where_query_start_sales', $wpdb->prepare( "t.start_sales = %s", $start_sales ), $start_sales );

		if( ! empty( $end_sales ) )
			$paged_sql['where'][] = apply_filters( 'bpe_tickets_where_query_end_sales', $wpdb->prepare( "t.end_sales = %s", $end_sales ), $end_sales );

		if( ! empty( $min_tickets ) )
			$paged_sql['where'][] = apply_filters( 'bpe_tickets_where_query_min_tickets', $wpdb->prepare( "t.min_tickets = %d", (int)$min_tickets ), $min_tickets );

		if( ! empty( $max_tickets ) )
			$paged_sql['where'][] = apply_filters( 'bpe_tickets_where_query_max_tickets', $wpdb->prepare( "t.max_tickets = %d", (int)$max_tickets ), $max_tickets );

		if( ! empty( $available ) )
		{
			$now = bp_core_current_time();
			$paged_sql['where'][] = apply_filters( 'bpe_tickets_where_query_available', $wpdb->prepare( "( t.start_sales <= '{$now}' AND t.end_sales >= '{$now}' )" ), $now );
		}

		if( $search_terms )
		{
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$paged_sql['where'][] = apply_filters( 'bpe_tickets_where_query_search_terms', "( t.description LIKE '%%{$search_terms}%%' OR t.name LIKE '%%{$search_terms}%%' )", $search_terms );
		}

		$paged_sql['orderby'] = apply_filters( 'bpe_tickets_orderby_query', $wpdb->prepare( "ORDER BY t.price ASC" ) );
		
		if( $per_page && $page )
			$paged_sql['pagination'] = apply_filters( 'bpe_tickets_pagination_query', $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $per_page), intval( $per_page ) ), $per_page, $page );

		// put it all together
		$p_sql[] = join( ' ', (array)$paged_sql['select'] );

		if( ! empty( $paged_sql['where'] ) )
			$p_sql[] = "WHERE " . join( ' AND ', (array)$paged_sql['where'] );
		
		$p_sql[] = $paged_sql['orderby'];
		
		if( $per_page && $page )
			$p_sql[] = $paged_sql['pagination'];
			
		$query = join( ' ', (array)$p_sql );

		/* Get paginated results */
		$paged_tickets = $wpdb->get_results( apply_filters( 'bpe_tickets_main_query', $query ) );

		/* Get total events results */
		$total_tickets = intval( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );

		unset( $paged_sql );
		
		return array( 'tickets' => $paged_tickets, 'total' => $total_tickets );
	}
	
	/**
	 * Delete tickets by id
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * @param	array 	$ids
	 * @return	boolean
	 */
	static public function delete_tickets_by_ids( $ids )
	{
		global $wpdb, $bpe;
		
		$ids = $wpdb->escape( implode( ',', (array)$ids ) );
		
		if( empty( $ids ) )
			return false;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->tickets} WHERE id IN ({$ids})" ) );
	}

	/**
	 * Delete tickets by event
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * @param	int		$event_id
	 * @return	boolean
	 */
	static public function delete_tickets_by_event( $event_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->tickets} WHERE event_id = %d", $event_id ) );
	}

	/**
	 * Has an event a ticket
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * @param	int		$id
	 * @return	int		$count
	 */
	static public function ticket_amount( $id )
	{
		global $wpdb, $bpe;
		
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bpe->tables->tickets} WHERE event_id = %d", $id ) );
		
		if( ! $count )
			return 1;
			
		return $count + 1;
	}

	/**
	 * Get the date of the next ticket sale
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * @param	int		$id
	 * @return	boolean
	 * 
	 * @uses	bp_core_current_time()
	 */
	static public function get_next_ticket_sale_date( $id )
	{
		global $wpdb, $bpe;
		
		$now = bp_core_current_time();
		return $wpdb->get_var( $wpdb->prepare( "SELECT start_sales FROM {$bpe->tables->tickets} WHERE event_id = %d AND start_sales > '{$now}' ORDER BY start_sales ASC LIMIT 1", $id ) );
	}
} 
?>