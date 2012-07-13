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

class Buddyvents_Ticket_Template
{
	var $current_ticket = -1;
	var $ticket_count;
	var $tickets;
	var $ticket;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_ticket_count;

	/**
	 * PHP5 Constructor
	 * @since 2.0
	 */
	function __construct( $event_id, $name, $price, $currency, $quantity, $start_sales, $end_sales, $min_tickets, $max_tickets, $available, $page, $per_page, $max, $search_terms )
	{
		$this->pag_page = isset( $_REQUEST['spage'] ) ? intval( $_REQUEST['spage'] 	) : $page;
		$this->pag_num  = isset( $_REQUEST['num'] 	) ? intval( $_REQUEST['num'] 	) : $per_page;

		$this->tickets = bpe_get_tickets( array( 'event_id' => $event_id, 'name' => $name, 'price' => $price, 'currency' => $currency, 'quantity' => $quantity, 'start_sales' => $start_sales, 'end_sales' => $end_sales, 'min_tickets' => $min_tickets, 'max_tickets' => $max_tickets, 'available' => $available, 'per_page' => $this->pag_num, 'page' => $this->pag_page, 'search_terms' => $search_terms ) );

		if( ! $max || $max >= (int)$this->tickets['total'] )
			$this->total_ticket_count = (int)$this->tickets['total'];
		else
			$this->total_ticket_count = (int)$max;

		$this->tickets = $this->tickets['tickets'];

		if( $max )
		{
			if( $max >= count( $this->tickets ) )
				$this->ticket_count = count( $this->tickets );
			else
				$this->ticket_count = (int)$max;
		}
		else
			$this->ticket_count = count( $this->tickets );
		
		$this->pag_links = paginate_links( array(
			'base' 		=> add_query_arg( array( 'spage' => '%#%' ) ),
			'format' 	=> '',
			'total' 	=> ceil( $this->total_ticket_count / $this->pag_num ),
			'current' 	=> $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' 	=> 3
		));
	}

	/**
	 * Check for tickets
	 * @since 2.0
	 */
	function has_tickets()
	{
		if( $this->ticket_count )
			return true;

		return false;
	}

	/**
	 * Get the next ticket
	 * @since 2.0
	 */
	function next_ticket()
	{
		$this->current_ticket++;
		$this->ticket = $this->tickets[$this->current_ticket];

		return $this->ticket;
	}

	/**
	 * Rewind all tickets
	 * @since 2.0
	 */
	function rewind_tickets()
	{
		$this->current_ticket = -1;
		
		if ( $this->ticket_count > 0 )
		{
			$this->ticket = $this->tickets[0];
		}
	}

	/**
	 * Get the tickets
	 * @since 2.0
	 */
	function tickets()
	{
		if ( $this->current_ticket + 1 < $this->ticket_count )
		{
			return true;
		}
		elseif( $this->current_ticket + 1 == $this->ticket_count )
		{
			do_action('loop_end');
			$this->rewind_tickets();
		}

		$this->in_the_loop = false;
		return false;
	}

	/**
	 * Setup the ticket data
	 * @since 2.0
	 */
	function the_ticket()
	{
		$this->in_the_loop = true;
		$this->ticket = $this->next_ticket();

		if ( 0 == $this->current_ticket )
			do_action('loop_start');
	}

}

/**
 * Are there any tickets
 * @since 2.0
 */
function bpe_has_tickets( $args = '' )
{
	global $ticket_template;

	$search_terms = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
	
	$defaults = array(
		'event_id' 		=> bpe_get_displayed_event( 'id' ),
		'name' 			=> false,
		'price' 		=> false,
		'currency' 		=> false,
		'quantity' 		=> false,
		'start_sales' 	=> false,
		'end_sales' 	=> false,
		'min_tickets' 	=> false,
		'max_tickets' 	=> false,
		'available' 	=> false,
		'page' 			=> 1,
		'per_page' 		=> 20,
		'max' 			=> false,
		'search_terms' 	=> $search_terms
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$ticket_template = new Buddyvents_Ticket_Template( (int)$event_id, $name, $price, $currency, (int)$quantity, $start_sales, $end_sales, (int)$min_tickets, (int)$max_tickets, (bool)$available, (int)$page, (int)$per_page, (int)$max, $search_terms );
	return apply_filters( 'bpe_has_tickets', $ticket_template->has_tickets(), &$ticket_template );
}

/**
 * Get the tickets
 * @since 2.0
 */
function bpe_tickets()
{
	global $ticket_template;

	return $ticket_template->tickets();
}

/**
 * Setup the ticket data
 * @since 2.0
 */
function bpe_the_ticket()
{
	global $ticket_template;

	return $ticket_template->the_ticket();
}

/**
 * Get the ticket count
 * @since 2.0
 */
function bpe_get_tickets_count()
{
	global $ticket_template;

	return $ticket_template->ticket_count;
}

/**
 * Get the total ticket count
 * @since 2.0
 */
function bpe_get_total_tickets_count()
{
	global $ticket_template;

	return $ticket_template->total_ticket_count;
}

/**
 * Pagination links
 * @since 2.0
 */
function bpe_tickets_pagination_links()
{
	echo bpe_get_tickets_pagination_links();
}
	function bpe_get_tickets_pagination_links()
	{
		global $ticket_template;
	
		if( ! empty( $ticket_template->pag_links ) )
			return sprintf( __( 'Page: %s', 'events' ), $ticket_template->pag_links );
	}

/**
 * Pagination count
 * @since 2.0
 */
function bpe_tickets_pagination_count()
{
	echo bpe_get_tickets_pagination_count();
}
	function bpe_get_tickets_pagination_count()
	{
		global $ticket_template;
	
		$from_num = bp_core_number_format( intval( ( $ticket_template->pag_page - 1 ) * $ticket_template->pag_num ) + 1 );
		$to_num = bp_core_number_format( ( $from_num + ( $ticket_template->pag_num - 1 ) > $ticket_template->total_ticket_count ) ? $ticket_template->total_ticket_count : $from_num + ( $ticket_template->pag_num - 1 ) );
		$total = bp_core_number_format( $ticket_template->total_ticket_count );
	
		return apply_filters( 'bpe_get_tickets_pagination_count', sprintf( __( 'Viewing ticket %1$s to %2$s (of %3$s tickets)', 'tickets' ), $from_num, $to_num, $total ) );
	}

/**
 * Ticket id
 * @since 2.0
 */
function bpe_ticket_id( $t = false )
{
	echo bpe_get_ticket_id( $t );
}
	function bpe_get_ticket_id( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->id ) )
			return false;

		return apply_filters( 'bpe_get_ticket_id', $ticket->id, $ticket );
	}

/**
 * Ticket event_id
 * @since 2.0
 */
function bpe_ticket_event_id( $t = false )
{
	echo bpe_get_ticket_event_id( $t );
}
	function bpe_get_ticket_event_id( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->event_id ) )
			return false;

		return apply_filters( 'bpe_get_ticket_event_id', $ticket->event_id, $ticket );
	}
	
/**
 * Ticket name
 * @since 2.0
 */
function bpe_ticket_name( $t = false )
{
	echo bpe_get_ticket_name( $t );
}
	function bpe_get_ticket_name( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->name ) )
			return false;

		return apply_filters( 'bpe_sales_get_ticket_name', $ticket->name, $ticket );
	}

/**
 * Ticket name raw
 * @since 2.0
 */
function bpe_ticket_name_raw( $t = false )
{
	echo bpe_get_ticket_name_raw( $t );
}
	function bpe_get_ticket_name_raw( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->name ) )
			return false;

		return apply_filters( 'bpe_get_raw_ticket_name', $ticket->name, $ticket );
	}

/**
 * Ticket description
 * @since 2.0
 */
function bpe_ticket_description( $t = false )
{
	echo bpe_get_ticket_description( $t );
}
	function bpe_get_ticket_description( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->description ) )
			return false;

		return apply_filters( 'bpe_sales_get_ticket_description', $ticket->description, $ticket );
	}

/**
 * Ticket description raw
 * @since 2.0
 */
function bpe_ticket_description_raw( $t = false )
{
	echo bpe_get_ticket_description_raw( $t );
}
	function bpe_get_ticket_description_raw( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->description ) )
			return false;

		return apply_filters( 'bpe_get_raw_ticket_description', $ticket->description, $ticket );
	}

/**
 * Ticket price
 * @since 2.0
 */
function bpe_ticket_price( $t = false )
{
	echo bpe_get_ticket_price( $t );
}
	function bpe_get_ticket_price( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->price ) )
			return false;

		return apply_filters( 'bpe_get_ticket_price', $ticket->price, $ticket );
	}

/**
 * Ticket currency
 * @since 2.0
 */
function bpe_ticket_currency( $t = false )
{
	echo bpe_get_ticket_currency( $t );
}
	function bpe_get_ticket_currency( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->currency ) )
			return false;

		return apply_filters( 'bpe_get_ticket_currency', $ticket->currency, $ticket );
	}

/**
 * Ticket quantity
 * @since 2.0
 */
function bpe_ticket_quantity( $t = false )
{
	echo bpe_get_ticket_quantity( $t );
}
	function bpe_get_ticket_quantity( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->quantity ) )
			return false;

		return apply_filters( 'bpe_get_ticket_quantity', $ticket->quantity, $ticket );
	}

/**
 * Ticket start_sales
 * @since 2.0
 */
function bpe_ticket_start_sales( $t = false )
{
	echo bpe_get_ticket_start_sales( $t );
}
	function bpe_get_ticket_start_sales( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->start_sales ) )
			return false;

		return apply_filters( 'bpe_get_ticket_start_sales', $ticket->start_sales, $ticket );
	}

/**
 * Ticket end_sales
 * @since 2.0
 */
function bpe_ticket_end_sales( $t = false )
{
	echo bpe_get_ticket_end_sales( $t );
}
	function bpe_get_ticket_end_sales( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->end_sales ) )
			return false;

		return apply_filters( 'bpe_get_ticket_end_sales', $ticket->end_sales, $ticket );
	}

/**
 * Ticket min_tickets
 * @since 2.0
 */
function bpe_ticket_min_tickets( $t = false )
{
	echo bpe_get_ticket_min_tickets( $t );
}
	function bpe_get_ticket_min_tickets( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->min_tickets ) )
			return false;

		return apply_filters( 'bpe_get_ticket_min_tickets', $ticket->min_tickets, $ticket );
	}

/**
 * Ticket max_tickets
 * @since 2.0
 */
function bpe_ticket_max_tickets( $t = false )
{
	echo bpe_get_ticket_max_tickets( $t );
}
	function bpe_get_ticket_max_tickets( $t = false )
	{
		global $ticket_template;
		
		$ticket = ( isset( $ticket_template->ticket ) && empty( $t ) ) ? $ticket_template->ticket : $t;

		if( ! isset( $ticket->max_tickets ) )
			return false;

		return apply_filters( 'bpe_get_ticket_max_tickets', $ticket->max_tickets, $ticket );
	}
	
/**
 * Event link after successful purchase
 * @since 2.0
 */
function bpe_ticket_event_link()
{
	echo bpe_get_ticket_event_link();
}
	function bpe_get_ticket_event_link()
	{
		$event = new Buddyvents_Events( bp_action_variable( 1 ) );
		
		return bpe_get_event_link( $event );
	}
?>