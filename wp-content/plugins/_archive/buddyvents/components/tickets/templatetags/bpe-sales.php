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

class Buddyvents_Sale_Template
{
	var $current_sale = -1;
	var $sale_count;
	var $sales;
	var $sale;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_sale_count;

	/**
	 * PHP5 Constructor
	 * @since 2.0
	 */
	function __construct( $ids, $ticket_id, $seller_id, $buyer_id, $event_id, $single_price, $currency, $quantity, $gateway, $sales_id, $status, $sale_date, $month, $year, $requested, $page, $per_page, $max, $search_terms )
	{
		$this->pag_page = isset( $_REQUEST['spage'] ) ? intval( $_REQUEST['spage'] 	) : $page;
		$this->pag_num  = isset( $_REQUEST['num'] 	) ? intval( $_REQUEST['num'] 	) : $per_page;

		$this->sales = bpe_get_sales( array( 'ids' => $ids, 'ticket_id' => $ticket_id, 'seller_id' => $seller_id, 'buyer_id' => $buyer_id, 'event_id' => $event_id, 'single_price' => $single_price, 'currency' => $currency, 'quantity' => $quantity, 'gateway' => $gateway, 'sales_id' => $sales_id, 'status' => $status, 'sale_date' => $sale_date, 'month' => $month, 'year' => $year, 'requested' => $requested, 'per_page' => $this->pag_num, 'page' => $this->pag_page, 'search_terms' => $search_terms ) );

		if( ! $max || $max >= (int)$this->sales['total'] )
			$this->total_sale_count = (int)$this->sales['total'];
		else
			$this->total_sale_count = (int)$max;

		$this->sales = $this->sales['sales'];

		if( $max )
		{
			if( $max >= count( $this->sales ) )
				$this->sale_count = count( $this->sales );
			else
				$this->sale_count = (int)$max;
		}
		else
			$this->sale_count = count( $this->sales );
		
		$this->pag_links = paginate_links( array(
			'base' 		=> add_query_arg( array( 'spage' => '%#%' ) ),
			'format' 	=> '',
			'total' 	=> ceil( $this->total_sale_count / $this->pag_num ),
			'current' 	=> $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' 	=> 3
		));
	}

	/**
	 * Are there any sales
	 * @since 2.0
	 */
	function has_sales()
	{
		if( $this->sale_count )
			return true;

		return false;
	}

	/**
	 * Get the next sale
	 * @since 2.0
	 */
	function next_sale()
	{
		$this->current_sale++;
		$this->sale = $this->sales[$this->current_sale];

		return $this->sale;
	}

	/**
	 * Rewind all sales
	 * @since 2.0
	 */
	function rewind_sales()
	{
		$this->current_sale = -1;
		
		if ( $this->sale_count > 0 )
		{
			$this->sale = $this->sales[0];
		}
	}

	/**
	 * Check for sales
	 * @since 2.0
	 */
	function sales()
	{
		if ( $this->current_sale + 1 < $this->sale_count )
		{
			return true;
		}
		elseif( $this->current_sale + 1 == $this->sale_count )
		{
			do_action('loop_end');
			$this->rewind_sales();
		}

		$this->in_the_loop = false;
		return false;
	}

	/**
	 * Setup the sales data
	 * @since 2.0
	 */
	function the_sale()
	{
		$this->in_the_loop = true;
		$this->sale = $this->next_sale();

		if ( 0 == $this->current_sale )
			do_action('loop_start');
	}

}

/**
 * Check for sales
 * @since 2.0
 */
function bpe_has_sales( $args = '' )
{
	global $sale_template;

	$seller_id		= ( bpe_get_displayed_event( 'user_id' ) ) ? bpe_get_displayed_event( 'user_id' ) : false;
	$status			= ( bpe_get_displayed_event( 'user_id' ) ) ? 'completed' 						  : false;
	$event_id 		= ( bpe_get_displayed_event( 'id' )		 ) ? bpe_get_displayed_event( 'id' ) 	  : false;
	$per_page 		= ( bpe_get_displayed_event( 'id' )		 ) ? 99999 								  : 20;
	
	$defaults = array(
		'ids' 			=> false,
		'event_id' 		=> $event_id,
		'ticket_id' 	=> false,
		'seller_id' 	=> $seller_id,
		'buyer_id' 		=> false,
		'single_price' 	=> false,
		'currency' 		=> false,
		'quantity' 		=> false,
		'gateway' 		=> false,
		'sales_id' 		=> false,
		'status' 		=> $status,
		'sale_date' 	=> false,
		'month' 		=> false,
		'year' 			=> false,
		'requested' 	=> false,
		'page' 			=> 1,
		'per_page' 		=> $per_page,
		'max' 			=> false,
		'search_terms' 	=> false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$sale_template = new Buddyvents_Sale_Template( $ids, (int)$ticket_id, (int)$seller_id, (int)$buyer_id, (int)$event_id, $single_price, $currency, (int)$quantity, $gateway, (int)$sales_id, $status, $sale_date, $month, $year, $requested, (int)$page, (int)$per_page, (int)$max, $search_terms );
	return apply_filters( 'bpe_has_sales', $sale_template->has_sales(), &$sale_template );
}

/**
 * Get the sales
 * @since 2.0
 */
function bpe_sales()
{
	global $sale_template;

	return $sale_template->sales();
}

/**
 * Setup the sales data
 * @since 2.0
 */
function bpe_the_sale()
{
	global $sale_template;

	return $sale_template->the_sale();
}

/**
 * Get the sales count
 * @since 2.0
 */
function bpe_get_sales_count()
{
	global $sale_template;

	return $sale_template->sale_count;
}

/**
 * Get the total sales count
 * @since 2.0
 */
function bpe_get_total_sales_count()
{
	global $sale_template;

	return $sale_template->total_sale_count;
}

/**
 * Pagination links
 * @since 2.0
 */
function bpe_sales_pagination_links()
{
	echo bpe_get_sales_pagination_links();
}
	function bpe_get_sales_pagination_links()
	{
		global $sale_template;
	
		if( ! empty( $sale_template->pag_links ) )
			return sprintf( __( 'Page: %s', 'events' ), $sale_template->pag_links );
	}

/**
 * Pagination count
 * @since 2.0
 */
function bpe_sales_pagination_count()
{
	echo bpe_get_sales_pagination_count();
}
	function bpe_get_sales_pagination_count()
	{
		global $sale_template;
	
		$from_num = bp_core_number_format( intval( ( $sale_template->pag_page - 1 ) * $sale_template->pag_num ) + 1 );
		$to_num = bp_core_number_format( ( $from_num + ( $sale_template->pag_num - 1 ) > $sale_template->total_sale_count ) ? $sale_template->total_sale_count : $from_num + ( $sale_template->pag_num - 1 ) );
		$total = bp_core_number_format( $sale_template->total_sale_count );
	
		return apply_filters( 'bpe_get_sales_pagination_count', sprintf( __( 'Viewing sale %1$s to %2$s (of %3$s sales)', 'sales' ), $from_num, $to_num, $total ) );
	}

/**
 * Sale id
 * @since 2.0
 */
function bpe_sale_id( $s = false )
{
	echo bpe_get_sale_id( $s );
}
	function bpe_get_sale_id( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->id ) )
			return false;

		return apply_filters( 'bpe_get_sale_id', $sale->id, $sale );
	}
	
/**
 * Sale ticket_id
 * @since 2.0
 */
function bpe_sale_ticket_id( $s = false )
{
	echo bpe_get_sale_ticket_id( $s );
}
	function bpe_get_sale_ticket_id( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket_id ) )
			return false;

		return apply_filters( 'bpe_get_sale_ticket_id', $sale->ticket_id, $sale );
	}

/**
 * Sale seller_id
 * @since 2.0
 */
function bpe_sale_seller_id( $s = false )
{
	echo bpe_get_sale_seller_id( $s );
}
	function bpe_get_sale_seller_id( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->seller_id ) )
			return false;

		return apply_filters( 'bpe_get_sale_seller_id', $sale->seller_id, $sale );
	}

/**
 * Sale buyer_id
 * @since 2.0
 */
function bpe_sale_buyer_id( $s = false )
{
	echo bpe_get_sale_buyer_id( $s );
}
	function bpe_get_sale_buyer_id( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->buyer_id ) )
			return false;

		return apply_filters( 'bpe_get_sale_buyer_id', $sale->buyer_id, $sale );
	}

/**
 * Sale single_price
 * @since 2.0
 */
function bpe_sale_single_price( $s = false )
{
	echo bpe_get_sale_single_price( $s );
}
	function bpe_get_sale_single_price( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->single_price ) )
			return false;

		return apply_filters( 'bpe_get_sale_single_price', $sale->single_price, $sale );
	}

/**
 * Sale currency
 * @since 2.0
 */
function bpe_sale_currency( $s = false )
{
	echo bpe_get_sale_currency( $s );
}
	function bpe_get_sale_currency( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->currency ) )
			return false;

		return apply_filters( 'bpe_get_sale_currency', $sale->currency, $sale );
	}

/**
 * Sale quantity
 * @since 2.0
 */
function bpe_sale_quantity( $s = false )
{
	echo bpe_get_sale_quantity( $s );
}
	function bpe_get_sale_quantity( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->quantity ) )
			return false;

		return apply_filters( 'bpe_get_sale_quantity', $sale->quantity, $sale );
	}

/**
 * Sale gateway
 * @since 2.0
 */
function bpe_sale_gateway( $s = false )
{
	echo bpe_get_sale_gateway( $s );
}
	function bpe_get_sale_gateway( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->gateway ) )
			return false;

		return apply_filters( 'bpe_get_sale_gateway', $sale->gateway, $sale );
	}

/**
 * Sale sales_id
 * @since 2.0
 */
function bpe_sale_sales_id( $s = false )
{
	echo bpe_get_sale_sales_id( $s );
}
	function bpe_get_sale_sales_id( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->sales_id ) )
			return false;

		return apply_filters( 'bpe_get_sale_sales_id', $sale->sales_id, $sale );
	}

/**
 * Sale status
 * @since 2.0
 */
function bpe_sale_status( $s = false )
{
	echo bpe_get_sale_status( $s );
}
	function bpe_get_sale_status( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->status ) )
			return false;

		return apply_filters( 'bpe_get_sale_status', $sale->status, $sale );
	}

/**
 * Sale sale_date
 * @since 2.0
 */
function bpe_sale_sale_date( $s = false )
{
	echo bpe_get_sale_sale_date( $s );
}
	function bpe_get_sale_sale_date( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->sale_date ) )
			return false;

		return apply_filters( 'bpe_get_sale_sale_date', $sale->sale_date, $sale );
	}

/**
 * Raw sale commission
 * @since 2.0
 */
function bpe_sale_commission_raw( $s = false )
{
	echo bpe_get_sale_commission_raw( $s );
}
	function bpe_get_sale_commission_raw( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->commission ) )
			return false;

		return apply_filters( 'bpe_get_raw_sale_commission', $sale->commission, $sale );
	}

/**
 * Sale commission
 * @since 2.0
 */
function bpe_sale_commission( $s = false, $format = false )
{
	 echo bpe_sale_get_commission( $s, $format );
}
	function bpe_sale_get_commission( $s = false, $format = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->quantity ) || ! isset( $sale->single_price ) || ! isset( $sale->commission ) )
			return 0;
	
		$commission = (float)( ( ( $sale->quantity * $sale->single_price ) / 100 ) * $sale->commission );
		
		return apply_filters( 'bpe_get_sale_commission', ( ( $format ) ? number_format( $commission, 2 ) : $commission ), $sale );
	}

/**
 * Sale total
 * @since 2.0
 */
function bpe_sale_total( $s = false, $currency = false )
{
	 echo bpe_sale_get_total( $s, $currency );
}
	function bpe_sale_get_total( $s = false, $currency = false )
	{
		global $sale_template;
		
		$total = 0;
		foreach( $sale_template->sales as $sale ) :
			if( $sale->currency == $currency )
				$total += bpe_sale_get_subtotal( $sale );
		endforeach;
	
		return apply_filters( 'bpe_get_sale_total', number_format( (float)$total, 2 ), $sale );
	}

/**
 * Sale event_commission
 * @since 2.0
 */
function bpe_sale_event_commission( $s = false, $currency = false )
{
	 echo bpe_sale_get_event_commission( $s, $currency );
}
	function bpe_sale_get_event_commission( $s = false, $currency = false )
	{
		global $sale_template;
		
		$commission = 0;
		foreach( $sale_template->sales as $sale ) :
			if( $sale->currency == $currency )
				$commission += bpe_sale_get_commission( $sale );
		endforeach;
	
		return apply_filters( 'bpe_get_sale_event_commission', number_format( (float)$commission, 2 ), $sale );
	}

/**
 * Sale sub total
 * @since 2.0
 */
function bpe_sale_subtotal( $s = false )
{
	 echo bpe_sale_get_subtotal( $s );
}
	function bpe_sale_get_subtotal( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->quantity ) || ! isset( $sale->single_price ) )
			return 0;
	
		return apply_filters( 'bpe_get_sale_subtotal', number_format( (float)( $sale->quantity * $sale->single_price ), 2 ), $sale );
	}

/**
 * Sale buyer avatar
 * @since 2.0
 */
function bpe_sale_buyer_avatar( $s = false, $w = 25, $h = 25 )
{
	 echo bpe_sale_get_buyer_avatar( $s, $w, $h );
}
	function bpe_sale_get_buyer_avatar( $s = false, $w = 25, $h = 25 )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->buyer_id ) )
			return false;
	
		return bp_core_fetch_avatar( array( 'item_id' => $sale->buyer_id, 'type' => 'thumb', 'width' => $w, 'height' => $h ) );
	}

/**
 * Sale buyer link
 * @since 2.0
 */
function bpe_sale_buyer_link( $s = false )
{
	 echo bpe_sale_get_buyer_link( $s );
}
	function bpe_sale_get_buyer_link( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->buyer_id ) )
			return false;
	
		return bp_core_get_userlink( $sale->buyer_id );
	}

/**
 * Sale class
 * @since 2.0
 */
function bpe_sale_css_class( $s = false )
{
	 echo bpe_sale_get_css_class( $s );
}
	function bpe_sale_get_css_class( $s = false )
	{
		global $sale_template;
		
		$class = false;

		if ( $sale_template->current_sale % 2 == 1 )
			$class = 'alt';

		return apply_filters( 'bpe_sale_get_css_class', trim( $class ) );
	}

/**
 * Get all currencies for an event
 * @since 2.0
 */
function bpe_sale_get_currencies( $s = false )
{
	global $sale_template;

	$currencies = array();
	foreach( $sale_template->sales as $sale ) :
		$currencies[] = $sale->currency;
	endforeach;
	
	return apply_filters( 'bpe_get_sale_currencies', array_unique( $currencies ) );
}

/**
 * Ticket event_id
 * @since 2.0
 */
function bpe_sale_ticket_event_id( $s = false )
{
	echo bpe_sale_get_ticket_event_id( $t );
}
	function bpe_sale_get_ticket_event_id( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->event_id ) )
			return false;

		return apply_filters( 'bpe_get_sale_ticket_event_id', $sale->ticket->event_id, $sale );
	}

/**
 * Sale ticket name
 * @since 2.0
 */
function bpe_sale_ticket_name( $s = false )
{
	 echo bpe_sale_get_ticket_name( $s );
}
	function bpe_sale_get_ticket_name( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->name ) )
			return false;

		return apply_filters( 'bpe_sales_get_ticket_name', $sale->ticket->name, $sale );		
	}

/**
 * Ticket name raw
 * @since 2.0
 */
function bpe_sale_ticket_name_raw( $s = false )
{
	echo bpe_sale_get_ticket_name_raw( $t );
}
	function bpe_sale_get_ticket_name_raw( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->name ) )
			return false;

		return apply_filters( 'bpe_get_sale_ticket_name', $sale->ticket->name, $sale );
	}

/**
 * Ticket description
 * @since 2.0
 */
function bpe_sale_ticket_description( $s = false )
{
	echo bpe_sale_get_ticket_description( $t );
}
	function bpe_sale_get_ticket_description( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->description ) )
			return false;

		return apply_filters( 'bpe_sales_get_ticket_description', $sale->ticket->description, $sale );
	}

/**
 * Ticket description raw
 * @since 2.0
 */
function bpe_sale_ticket_description_raw( $s = false )
{
	echo bpe_sale_get_ticket_description_raw( $t );
}
	function bpe_sale_get_ticket_description_raw( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->description ) )
			return false;

		return apply_filters( 'bpe_get_raw_sale_ticket_description', $sale->ticket->description, $sale );
	}

/**
 * Ticket price
 * @since 2.0
 */
function bpe_sale_ticket_price( $s = false )
{
	echo bpe_sale_get_ticket_price( $t );
}
	function bpe_sale_get_ticket_price( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->price ) )
			return false;

		return apply_filters( 'bpe_get_sale_ticket_price', $sale->ticket->price, $sale );
	}

/**
 * Ticket currency
 * @since 2.0
 */
function bpe_sale_ticket_currency( $s = false )
{
	echo bpe_sale_get_ticket_currency( $t );
}
	function bpe_sale_get_ticket_currency( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->currency ) )
			return false;

		return apply_filters( 'bpe_get_sale_ticket_currency', $sale->ticket->currency, $sale );
	}

/**
 * Ticket quantity
 * @since 2.0
 */
function bpe_sale_ticket_quantity( $s = false )
{
	echo bpe_sale_get_ticket_quantity( $t );
}
	function bpe_sale_get_ticket_quantity( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->quantity ) )
			return false;

		return apply_filters( 'bpe_get_sale_ticket_quantity', $sale->ticket->quantity, $sale );
	}

/**
 * Ticket start_sales
 * @since 2.0
 */
function bpe_sale_ticket_start_sales( $s = false )
{
	echo bpe_sale_get_ticket_start_sales( $t );
}
	function bpe_sale_get_ticket_start_sales( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->start_sales ) )
			return false;

		return apply_filters( 'bpe_get_sale_ticket_start_sales', $sale->ticket->start_sales, $sale );
	}

/**
 * Ticket end_sales
 * @since 2.0
 */
function bpe_sale_ticket_end_sales( $s = false )
{
	echo bpe_sale_get_ticket_end_sales( $t );
}
	function bpe_sale_get_ticket_end_sales( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->end_sales ) )
			return false;

		return apply_filters( 'bpe_get_sale_ticket_end_sales', $sale->ticket->end_sales, $sale );
	}

/**
 * Ticket min_tickets
 * @since 2.0
 */
function bpe_sale_ticket_min_tickets( $s = false )
{
	echo bpe_sale_get_ticket_min_tickets( $t );
}
	function bpe_sale_get_ticket_min_tickets( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->min_tickets ) )
			return false;

		return apply_filters( 'bpe_get_sale_ticket_min_tickets', $sale->ticket->min_tickets, $sale );
	}
/**
 * Ticket max_tickets
 * @since 2.0
 */
function bpe_sale_ticket_max_tickets( $s = false )
{
	echo bpe_sale_get_ticket_max_tickets( $t );
}
	function bpe_sale_get_ticket_max_tickets( $s = false )
	{
		global $sale_template;
		
		$sale = ( isset( $sale_template->sale ) && empty( $s ) ) ? $sale_template->sale : $s;

		if( ! isset( $sale->ticket->max_tickets ) )
			return false;

		return apply_filters( 'bpe_get_sale_ticket_max_tickets', $sale->ticket->max_tickets, $sale );
	}
?>