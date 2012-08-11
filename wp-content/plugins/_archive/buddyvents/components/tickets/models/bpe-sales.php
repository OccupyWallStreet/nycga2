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

class Buddyvents_Sales
{
	public $id;
	public $ticket_id;
	public $seller_id;
	public $buyer_id;
	public $single_price;
	public $currency;
	public $quantity;
	public $attendees;
	public $gateway;
	public $sales_id;
	public $status;
	public $sale_date;
	public $commission;
	public $requested;

	/**
	 * PHP5 Constructor
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @param	int		$id
	 * @param	int		$ticket_id
	 */
	public function __construct( $id = null, $ticket_id = null )
	{
		if( $id )
		{
			$this->id = $id;
			$this->populate();
		}
		elseif( $ticket_id )
		{
			$this->ticket_id = $ticket_id;
			$this->populate_by_ticket_id();
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
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->sales} WHERE id = %d", $this->id ) );

		$this->ticket_id	= $table->ticket_id;
		$this->seller_id 	= $table->seller_id;
		$this->buyer_id	 	= $table->buyer_id;
		$this->single_price = $table->single_price;
		$this->currency 	= $table->currency;
		$this->quantity	 	= $table->quantity;
		$this->attendees 	= $table->attendees;
		$this->gateway		= $table->gateway;
		$this->sales_id		= $table->sales_id;
		$this->status		= $table->status;
		$this->sale_date	= $table->sale_date;
		$this->commission	= $table->commission;
		$this->requested	= $table->requested;
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
	public function populate_by_ticket_id()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->sales} WHERE ticket_id = %d", $this->ticket_id ) );

		$this->id		 	= $table->id;
		$this->seller_id 	= $table->seller_id;
		$this->buyer_id	 	= $table->buyer_id;
		$this->single_price = $table->single_price;
		$this->currency 	= $table->currency;
		$this->quantity	 	= $table->quantity;
		$this->attendees 	= $table->attendees;
		$this->gateway		= $table->gateway;
		$this->sales_id		= $table->sales_id;
		$this->status		= $table->status;
		$this->commission	= $table->commission;
		$this->requested	= $table->requested;
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
	 * @uses 	apply_filters()
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->ticket_id	= apply_filters( 'bpe_sales_before_save_sales_ticket_id', $this->ticket_id, $this->id );
		$this->seller_id	= apply_filters( 'bpe_sales_before_save_sales_seller_id', $this->seller_id, $this->id );
		$this->buyer_id	 	= apply_filters( 'bpe_sales_before_save_sales_buyer_id', $this->buyer_id, $this->id );
		$this->single_price	= apply_filters( 'bpe_sales_before_save_sales_single_price', $this->single_price, $this->id );
		$this->currency		= apply_filters( 'bpe_sales_before_save_sales_currency', $this->currency, $this->id );
		$this->quantity	 	= apply_filters( 'bpe_sales_before_save_sales_quantity', $this->quantity, $this->id );
		$this->attendees 	= apply_filters( 'bpe_sales_before_save_sales_attendees', $this->attendees, $this->id );
		$this->gateway		= apply_filters( 'bpe_sales_before_save_sales_gateway', $this->gateway, $this->id );
		$this->sales_id		= apply_filters( 'bpe_sales_before_save_sales_sales_id', $this->sales_id, $this->id );
		$this->status		= apply_filters( 'bpe_sales_before_save_sales_status', $this->status, $this->id );
		$this->sale_date	= apply_filters( 'bpe_sales_before_save_sales_sale_date', $this->sale_date, $this->id );
		$this->commission	= apply_filters( 'bpe_sales_before_save_sales_commission', $this->commission, $this->id );
		$this->requested	= apply_filters( 'bpe_sales_before_save_sales_requested', $this->requested, $this->id );
		
		/* Call a before save action here */
		do_action( 'bpe_sales_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->sales} SET
													ticket_id = %d,
													seller_id = %d,
													buyer_id = %d,
													single_price = %s,
													currency = %s,
													quantity = %d,
													attendees = %s,
													gateway = %s,
													sales_id = %s,
													status = %s,
													sale_date = %s,
													commission = %s,
													requested = %d
											WHERE id = %d",
													$this->ticket_id,
													$this->seller_id,
													$this->buyer_id,
													$this->single_price,
													$this->currency,
													$this->quantity,
													$this->attendees,
													$this->gateway,
													$this->sales_id,
													$this->status,
													$this->sale_date,
													$this->commission,
													$this->requested,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->sales} (
													ticket_id,
													seller_id,
													buyer_id,
													single_price,
													currency,
													quantity,
													attendees,
													gateway,
													sales_id,
													status,
													sale_date,
													commission,
													requested
											) VALUES ( 
													%d, %d, %d, %s, %s, %d, %s, %s, %s, %s, %s, %s, %d
											)",
													$this->ticket_id,
													$this->seller_id,
													$this->buyer_id,
													$this->single_price,
													$this->currency,
													$this->quantity,
													$this->attendees,
													$this->gateway,
													$this->sales_id,
													$this->status,
													$this->sale_date,
													$this->commission,
													$this->requested ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'bpe_sales_after_save', $this ); 
		
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
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->sales} WHERE id = %d", $this->id ) );
	}
	
	/**
	 * Get sales from the database based on certain parameters
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * 
	 * @param	string	$ids			optional
	 * @param	int		$ticket_id		optional
	 * @param	int		$seller_id		optional
	 * @param	int		$buyer_id		optional
	 * @param	int		$event_id		optional
	 * @param	float	$single_price	optional
	 * @param	string	$currency		optional
	 * @param	int		$quantity		optional
	 * @param	string	$gateway		optional
	 * @param	int		$sales_id		optional
	 * @param	string	$status			optional
	 * @param	string	$sale_date		optional
	 * @param	string	$month			optional
	 * @param	int		$year			optional
	 * @param	bool	$requested		optional
	 * @param	int		$page			optional
	 * @param	int		$per_page		optional
	 * @param	string	$search_terms	optional
	 * 
	 * @uses	like_escape()
	 */
	static function get( $ids = false, $ticket_id = false, $seller_id = false, $buyer_id = false, $event_id = false, $single_price = false, $currency = false, $quantity = false, $gateway = false, $sales_id = false, $status = false, $sale_date = false, $month = false, $year = false, $requested = false, $page = false, $per_page = false, $search_terms = false )
	{
		global $wpdb, $bpe, $bp;

		$paged_sql = array();
		
		if( $ids == -1 )
			return false;
		
		$paged_sql['select'][] = apply_filters( 'bpe_sales_select_query_base', "SELECT SQL_CALC_FOUND_ROWS s.* FROM {$bpe->tables->sales} s" );

		if( ! empty( $event_id ) )
			$paged_sql['select'][] = apply_filters( 'bpe_sales_join_query_event_id', $wpdb->prepare( "RIGHT JOIN {$bpe->tables->tickets} t ON s.ticket_id = t.id AND t.event_id = %d", (int)$event_id ), $event_id );

		if( ! empty( $ids ) )
		{
			if( is_array( $ids ) )
				$ids = $wpdb->escape( join( ',', $ids ) );
			
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_ids', $wpdb->prepare( "s.id IN ({$ids})" ), $ids );
		}

		if( ! empty( $ticket_id ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_ticket_id', $wpdb->prepare( "s.ticket_id = %d", (int)$ticket_id ), $ticket_id );

		if( ! empty( $seller_id ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_seller_id', $wpdb->prepare( "s.seller_id = %d", (int)$seller_id ), $seller_id );

		if( ! empty( $buyer_id ) && empty( $search_terms ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_buyer_id', $wpdb->prepare( "s.buyer_id = %d", (int)$buyer_id ), $buyer_id );

		if( ! empty( $single_price ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_single_price', $wpdb->prepare( "s.single_price = %s", $single_price ), $single_price );

		if( ! empty( $currency ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_currency', $wpdb->prepare( "s.currency = %s", $currency ), $currency );

		if( ! empty( $quantity ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_quantity', $wpdb->prepare( "s.quantity = %d", (int)$quantity ), $quantity );

		if( ! empty( $gateway ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_gateway', $wpdb->prepare( "s.gateway = %s", $gateway ), $gateway );

		if( ! empty( $sales_id ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_sales_id', $wpdb->prepare( "s.sales_id = %s", $sales_id ), $sales_id );

		if( ! empty( $status ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_status', $wpdb->prepare( "s.status = %s", $status ), $status );

		if( ! empty( $sale_date ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_sale_date', $wpdb->prepare( "s.sale_date = %s", $sale_date ), $sale_date );
			
		if( ! empty( $month ) && ! empty( $year ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_month_year', $wpdb->prepare( "( month(s.sale_date) = '{$month}' AND year(s.sale_date) = '{$year}' )" ), $month, $year );

		elseif( ! empty( $year ) )
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_year', $wpdb->prepare( "( year(s.sale_date) = '{$year}' )" ), $year );

		if( ! empty( $requested ) )
		{
			$status_requested = ( $requested == 'open' ) ? false : true;
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_requested', $wpdb->prepare( "s.requested = %d", (int)$status_requested ), $status_requested );
		}

		if( $search_terms && $buyer_id )
		{
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$paged_sql['where'][] = apply_filters( 'bpe_sales_where_query_terms_buyer', "( s.attendees LIKE '%%{$search_terms}%%' ) OR ( s.buyer_id = {$buyer_id} )", $search_terms, $buyer_id );
		}

		$paged_sql['orderby'] = apply_filters( 'bpe_sales_orderby_query', $wpdb->prepare( "ORDER BY s.sale_date ASC" ) );
		
		if( $per_page && $page )
			$paged_sql['pagination'] = apply_filters( 'bpe_sales_pagination_query', $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $per_page), intval( $per_page ) ), $per_page. $page );

		// put it all together
		$p_sql[] = join( ' ', (array)$paged_sql['select'] );

		if( ! empty( $paged_sql['where'] ) )
			$p_sql[] = "WHERE " . join( ' AND ', (array)$paged_sql['where'] );
		
		$p_sql[] = $paged_sql['orderby'];
		
		if( $per_page && $page )
			$p_sql[] = $paged_sql['pagination'];
			
		$query = join( ' ', (array)$p_sql );

		/* Get paginated results */
		$paged_sales = $wpdb->get_results( apply_filters( 'bpe_sales_main_query', $query ) );

		/* Get total sales results */
		$total_sales = intval( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );

		$tids = array();
		foreach( (array)$paged_sales as $sale )
			$tids[] = $sale->ticket_id;
			
		$tids = $wpdb->escape( join( ',', (array)$tids ) );
		 
		$paged_sales = self::get_sale_tickets( &$paged_sales, $tids );

		unset( $paged_sql );
		
		return array( 'sales' => $paged_sales, 'total' => $total_sales );
	}

	/**
	 * Get invoices sales
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 			WordPress database object
	 * @global 	object 	$bpe			Buddyvents settings
	 * @param	array 	$paged_sales	An array of sales objects
	 * @param	string	$ids			A comma seperated list of sales ids
	 */
	static function get_sale_tickets( $paged_sales, $ids )
	{
		global $wpdb, $bpe;
		
		if( ! $ids )
			$ids = 0;
		
		$tickets = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bpe->tables->tickets} WHERE id IN ({$ids})" ) );
		
		for( $i = 0; $i < count( $paged_sales ); $i++ )
		{
			foreach( (array)$tickets as $ticket )
			{
				if( $ticket->id == $paged_sales[$i]->ticket_id )
					$paged_sales[$i]->ticket = $ticket;
			}
		}
		
		return $paged_sales;
	}
	
	/**
	 * Set the status
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * @param	string	$status
	 * @param	int		$id
	 */
	static function set_status( $status = false, $id = false )
	{
		global $wpdb, $bpe;
		
		if( ! $status || ! $id )
			return false;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->sales} SET status = %s WHERE id = %d", $status, $id ) );
	}
	
	/**
	 * Set sales_id
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * @param	int		$sales_id
	 * @param	int		$id
	 */
	static function set_sales_id( $sales_id = false, $id = false )
	{
		global $wpdb, $bpe;

		if( ! $sales_id || ! $id )
			return false;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->sales} SET sales_id = %s WHERE id = %d", $sales_id, $id ) );
	}

	/**
	 * Set requested
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * @param	string	$ids
	 * @param	int		$requested
	 */
	static function set_requested( $ids = false, $requested = 1 )
	{
		global $wpdb, $bpe;
			
		if( is_array( $ids ) )
			$ids = $wpdb->escape( join( ',', $ids ) );
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->sales} SET requested = %d WHERE id IN ({$ids})", (int)$requested ) );
	}
	
	/**
	 * Get all PayPal transaction ids
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 */
	static function get_all_txn_ids()
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_col( $wpdb->prepare( "SELECT sales_id FROM {$bpe->tables->sales} WHERE gateway = 'paypal'" ) );
	}
	
	/**
	 * Get an event based on a sales id
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 *
	 * @param	int		$sale_id
	 * @return	object	Sale data
	 */
	static function get_event( $sale_id )
	{
		global $wpdb, $bpe;

		return $wpdb->get_row( $wpdb->prepare( "SELECT e.*, s.id as sales_id FROM {$bpe->tables->sales} s LEFT OUTER JOIN {$bpe->tables->tickets} t ON t.id = s.ticket_id LEFT OUTER JOIN {$bpe->tables->events} e ON e.id = t.event_id WHERE s.id = %d", (int)$sale_id ) );
	}
} 
?>