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

class Buddyvents_Invoices
{
	public $id;
	public $user_id;
	public $sales;
	public $month;
	public $sent_date;
	public $settled;
	public $transaction_id;
	
	/**
	 * PHP5 Constructor
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * @param 	int 	$id 	Invoice id
	 * 
	 * @access 	public
	 */
	public function __construct( $id = null )
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
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * 
	 * @access 	public
	 */
	public function populate()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->invoices} WHERE id = %d", $this->id ) );

		$this->user_id		= $table->user_id;
		$this->sales		= $table->sales;
		$this->month		= $table->month;
		$this->sent_date 	= $table->sent_date;
		$this->settled		= $table->settled;
	}

	/**
	 * Save a database row
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * @return 	int 	$id 	Invoice Id
	 * 
	 * @access 	public
	 * @uses 	apply_filters()
	 * @uses 	do_action()
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->user_id			= apply_filters( 'bpe_before_save_invoice_user_id', $this->user_id, $this->id );
		$this->sales			= apply_filters( 'bpe_before_save_invoice_sales', $this->sales, $this->id );
		$this->month			= apply_filters( 'bpe_before_save_invoice_month', $this->month, $this->id );
		$this->sent_date		= apply_filters( 'bpe_before_save_invoice_sent_date', $this->sent_date, $this->id );
		$this->settled			= apply_filters( 'bpe_before_save_invoice_settled', $this->settled, $this->id );
		$this->transaction_id 	= apply_filters( 'bpe_before_save_invoice_transaction_id', $this->transaction_id, $this->id );
		
		/* Call a before save action here */
		do_action( 'bpe_invoice_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->invoices} SET
													user_id = %d,
													sales = %s,
													month = %s,
													sent_date = %s,
													settled = %d,
													transaction_id = %s
											WHERE id = %d",
													$this->user_id,
													$this->sales,
													$this->month,
													$this->sent_date,
													$this->settled,
													$this->transaction_id,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->invoices} (
													user_id,
													sales,
													month,
													sent_date,
													settled,
													transaction_id
											) VALUES ( 
													%d, %s, %s, %s, %d, %s
											)",
													$this->user_id,
													$this->sales,
													$this->month,
													$this->sent_date,
													$this->settled,
													$this->transaction_id ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'bpe_invoice_after_save', $this ); 
		
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
	 * 
	 * @access 	public
	 * @return 	boolean
	 */
	public function delete()
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->invoices} WHERE id = %d", $this->id ) );
	}
	
	/**
	 * Mass delete invoices
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * @param 	mixed 	$ids 	Array or string
	 * @return 	boolean
	 */
	static public function delete_invoices( $ids )
	{
		global $wpdb, $bpe;
		
		if( is_array( $ids ) )
			$ids = $wpdb->escape( join( ',', $ids ) );
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->invoices} WHERE id IN ({$ids})" ) );
	}

	/**
	 * Get invoices
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 			WordPress database object
	 * @global 	object 	$bpe			Buddyvents settings
	 * @global 	object 	$bp				BuddyPress settings
	 * @param	string 	$ids 			optional
	 * @param 	int		$user_id		optional
	 * @param 	string 	$month			optional
	 * @param 	string 	$sent_date		optional
	 * @param 	bool 	$sent			optional
	 * @param 	bool 	$settled		optional
	 * @param 	int 	$page			optional
	 * @param 	int 	$per_page		optional
	 * @param 	string	$search_terms	optional
	 * @return 	array
	 * 
	 * @uses 	like_escape()
	 * @uses	maybe_unserialize()
	 */
	static public function get( $ids = false, $user_id = false, $month = false, $sent_date = false, $sent = false, $settled = false, $page = false, $per_page = false, $search_terms = false )
	{
		global $wpdb, $bpe, $bp;
		
		if( $ids == -1 )
			return false;

		$paged_sql = array();
		
		$paged_sql['select'][] = apply_filters( 'bpe_invoices_select_query_base', "SELECT SQL_CALC_FOUND_ROWS i.* FROM {$bpe->tables->invoices} i" );

		if( ! empty( $ids ) )
		{
			if( is_array( $ids ) )
				$ids = $wpdb->escape( join( ',', $ids ) );
				
			$paged_sql['where'][] = apply_filters( 'bpe_invoices_where_query_ids', $wpdb->prepare( "i.id IN ({$ids})" ), $ids );
		}

		if( ! empty( $user_id ) )
			$paged_sql['where'][] = apply_filters( 'bpe_invoices_where_query_user_id', $wpdb->prepare( "i.user_id = %d", (int)$user_id ), $user_id );

		if( ! empty( $month ) )
			$paged_sql['where'][] = apply_filters( 'bpe_invoices_where_query_month', $wpdb->prepare( "i.month = %s", $month ), $month );

		if( ! empty( $sent_date ) )
			$paged_sql['where'][] = apply_filters( 'bpe_invoices_where_query_sent_date', $wpdb->prepare( "i.sent_date = %s", $sent_date ), $sent_date );

		if( ! empty( $settled ) )
		{
			$new_settled = ( $settled == 'yes' ) ? 1 : 0;
			$paged_sql['where'][] = apply_filters( 'bpe_invoices_where_query_settled', $wpdb->prepare( "i.settled = %s", (int)$new_settled ), $new_settled );
		}

		if( ! empty( $sent ) )
		{
			$operator = ( $sent == 'yes' ) ? '!=' : '=';
			$paged_sql['where'][] = apply_filters( 'bpe_invoices_where_query_sent', $wpdb->prepare( "i.sent_date {$operator} '0000-00-00 00:00:00'" ), $operator );
		}

		if( $search_terms )
		{
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$paged_sql['where'][] = apply_filters( 'bpe_invoices_where_query_search_terms', "( i.currency LIKE '%%{$search_terms}%%' OR i.month LIKE '%%{$search_terms}%%' )", $search_terms );
		}

		$paged_sql['orderby'] = apply_filters( 'bpe_invoices_orderby_query', $wpdb->prepare( "ORDER BY i.sent_date ASC" ) );
		
		if( $per_page && $page )
			$paged_sql['pagination'] = apply_filters( 'bpe_invoices_pagination_query', $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $per_page), intval( $per_page ) ), $per_page, $page );

		// put it all together
		$p_sql[] = join( ' ', (array)$paged_sql['select'] );

		if( ! empty( $paged_sql['where'] ) )
			$p_sql[] = "WHERE " . join( ' AND ', (array)$paged_sql['where'] );
		
		$p_sql[] = $paged_sql['orderby'];
		
		if( $per_page && $page )
			$p_sql[] = $paged_sql['pagination'];
			
		$query = join( ' ', (array)$p_sql );

		/* Get paginated results */
		$paged_invoices = $wpdb->get_results( apply_filters( 'bpe_invoices_main_query', $query ) );
		/* Get total sales results */
		$total_invoices = intval( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );

		$iids = array();
		foreach( (array)$paged_invoices as $invoice )
		{
			$sale_ids = maybe_unserialize( stripslashes( $invoice->sales ) );
			
			foreach( (array)$sale_ids as $id )
				$iids[] = $id;
		}
			
		$iids = $wpdb->escape( join( ',', $iids ) );
		 
		$paged_invoices = self::get_invoice_sales( &$paged_invoices, $iids );

		unset( $paged_sql );
		
		return array( 'invoices' => $paged_invoices, 'total' => $total_invoices );
	}
	
	/**
	 * Get invoices sales
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 			WordPress database object
	 * @global 	object 	$bpe			Buddyvents settings
	 * @param	array 	$paged_invoices	An array of invoice objects
	 * @param	string	$ids			Comma seperated invoice ids
	 * @return 	array 	$paged_invoices	Modified array of invoice objects
	 */
	static public function get_invoice_sales( $paged_invoices, $ids )
	{
		global $wpdb, $bpe;
		
		if( empty( $ids ) )
			$ids = 0;
		
		$sales = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bpe->tables->sales} WHERE id IN ({$ids})" ) );
		
		for( $i = 0; $i < count( $paged_invoices ); $i++ )
		{
			foreach( (array)$sales as $sale )
			{
				$invoice_sales = maybe_unserialize( stripslashes( $paged_invoices[$i]->sales ) );
				if( in_array( $sale->id, (array)$invoice_sales ) )
					$paged_invoices[$i]->datasets[] = $sale;
			}
		}
		
		return $paged_invoices;
	}

	/**
	 * Delete a row
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 	WordPress database object
	 * @global 	object 	$bpe	Buddyvents settings
	 * @param	string	$date
	 * @param	mixed	$ids	An array or a string of comma seperated invoice ids
	 * @return	boolean
	 */
	static public function update_date( $date, $ids )
	{
		global $wpdb, $bpe;
		
		if( is_array( $ids ) )
			$ids = $wpdb->escape( join( ',', $ids ) );
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->invoices} SET sent_date = %s WHERE id IN ({$ids})", $date ) );
	}

	/**
	 * Change the settled status of an invoice
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object 	$wpdb 		WordPress database object
	 * @global 	object 	$bpe		Buddyvents settings
	 * @param	bool	$settled
	 * @param	mixed	$ids		Array or comma seperated list of invoice ids
	 * @return	boolean
	 */
	static public function change_settled( $settled = false, $ids = false )
	{
		global $wpdb, $bpe;
		
		if( is_array( $ids ) )
			$ids = $wpdb->escape( join( ',', $ids ) );

		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->invoices} SET settled = %d WHERE id IN ({$ids})", $settled ) );
	}

	/**
	 * Get all PayPal transaction ids
	 * 
	 * @package Tickets
	 * @since 	2.0
	 * 
	 * @global 	object $wpdb WordPress database object
	 * @global 	object $bpe	Buddyvents settings
	 * @return	object A column of invoice transaction ids
	 */
	static public function get_all_txn_ids()
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_col( $wpdb->prepare( "SELECT transaction_id FROM {$bpe->tables->invoices}" ) );
	}
}
?>