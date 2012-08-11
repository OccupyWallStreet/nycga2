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

class Buddyvents_Invoices_Template
{
	var $current_invoice = -1;
	var $invoice_count;
	var $invoices;
	var $invoice;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_invoice_count;

	/**
	 * PHP5 Constructor
	 * @since 2.0
	 */
	function __construct( $ids, $user_id, $month, $sent_date, $sent, $settled, $page, $per_page, $max, $search_terms )
	{
		$this->pag_page = isset( $_REQUEST['ipage'] ) ? intval( $_REQUEST['ipage'] 	) : $page;
		$this->pag_num  = isset( $_REQUEST['num'] 	) ? intval( $_REQUEST['num'] 	) : $per_page;

		$this->invoices = bpe_get_invoices( array( 'ids' => $ids, 'user_id' => $user_id, 'month' => $month, 'sent_date' => $sent_date, 'sent' => $sent, 'settled' => $settled, 'per_page' => $this->pag_num, 'page' => $this->pag_page, 'search_terms' => $search_terms ) );

		if( ! $max || $max >= (int)$this->invoices['total'] )
			$this->total_invoice_count = (int)$this->invoices['total'];
		else
			$this->total_invoice_count = (int)$max;

		$this->invoices = $this->invoices['invoices'];

		if( $max )
		{
			if( $max >= count( $this->invoices ) )
				$this->invoice_count = count( $this->invoices );
			else
				$this->invoice_count = (int)$max;
		}
		else
			$this->invoice_count = count( $this->invoices );
		
		$this->pag_links = paginate_links( array(
			'base' 		=> add_query_arg( array( 'ipage' => '%#%' ) ),
			'format' 	=> '',
			'total' 	=> ceil( $this->total_invoice_count / $this->pag_num ),
			'current' 	=> $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' 	=> 3
		));
	}

	/**
	 * Have invoices been returned
	 * @since 2.0
	 */
	function has_invoices()
	{
		if( $this->invoice_count )
			return true;

		return false;
	}

	/**
	 * Get the next invoice
	 * @since 2.0
	 */
	function next_invoice()
	{
		$this->current_invoice++;
		$this->invoice = $this->invoices[$this->current_invoice];

		return $this->invoice;
	}

	/**
	 * Rewind invoices
	 * @since 2.0
	 */
	function rewind_invoices()
	{
		$this->current_invoice = -1;
		
		if ( $this->invoice_count > 0 )
		{
			$this->invoice = $this->invoices[0];
		}
	}

	/**
	 * Check for invoices
	 * @since 2.0
	 */
	function invoices()
	{
		if ( $this->current_invoice + 1 < $this->invoice_count )
		{
			return true;
		}
		elseif( $this->current_invoice + 1 == $this->invoice_count )
		{
			do_action('loop_end');
			$this->rewind_invoices();
		}

		$this->in_the_loop = false;
		return false;
	}

	/**
	 * Setup the invoice data
	 * @since 2.0
	 */
	function the_invoice()
	{
		$this->in_the_loop = true;
		$this->invoice = $this->next_invoice();

		if ( 0 == $this->current_invoice )
			do_action('loop_start');
	}

}

/**
 * Check for invoices with predefined parameters
 * @since 2.0
 */
function bpe_has_invoices( $args = '' )
{
	global $invoice_template;

	$search_terms = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
	$user_id = bp_displayed_user_id();
	
	$defaults = array(
		'ids' 			=> false,
		'user_id' 		=> $user_id,
		'month' 		=> false,
		'sent_date' 	=> false,
		'sent' 			=> false,
		'settled' 		=> false,
		'page' 			=> 1,
		'per_page' 		=> 20,
		'max' 			=> false,
		'search_terms' 	=> $search_terms
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$invoice_template = new Buddyvents_Invoices_Template( $ids, (int)$user_id, $month, $sent_date, $sent, $settled, (int)$page, (int)$per_page, (int)$max, $search_terms );
	return apply_filters( 'bpe_has_invoices', $invoice_template->has_invoices(), &$invoice_template );
}

/**
 * Get invoices
 * @since 2.0
 */
function bpe_invoices()
{
	global $invoice_template;

	return $invoice_template->invoices();
}

/**
 * Setup the invoice data
 * @since 2.0
 */
function bpe_the_invoice()
{
	global $invoice_template;

	return $invoice_template->the_invoice();
}

/**
 * Get invoice count
 * @since 2.0
 */
function bpe_get_invoices_count()
{
	global $invoice_template;

	return $invoice_template->invoice_count;
}

/**
 * Get total invoice count
 * @since 2.0
 */
function bpe_get_total_invoices_count()
{
	global $invoice_template;

	return $invoice_template->total_invoice_count;
}

/**
 * Pagination links
 * @since 2.0
 */
function bpe_invoices_pagination_links()
{
	echo bpe_get_invoices_pagination_links();
}
	function bpe_get_invoices_pagination_links()
	{
		global $invoice_template;
	
		if( ! empty( $invoice_template->pag_links ) )
			return sprintf( __( 'Page: %s', 'events' ), $invoice_template->pag_links );
	}

/**
 * Pagination count
 * @since 2.0
 */
function bpe_invoices_pagination_count()
{
	echo bpe_get_invoices_pagination_count();
}
	function bpe_get_invoices_pagination_count()
	{
		global $bp, $invoice_template;
	
		$from_num = bp_core_number_format( intval( ( $invoice_template->pag_page - 1 ) * $invoice_template->pag_num ) + 1 );
		$to_num = bp_core_number_format( ( $from_num + ( $invoice_template->pag_num - 1 ) > $invoice_template->total_invoice_count ) ? $invoice_template->total_invoice_count : $from_num + ( $invoice_template->pag_num - 1 ) );
		$total = bp_core_number_format( $invoice_template->total_invoice_count );
	
		return apply_filters( 'bpe_get_invoices_pagination_count', sprintf( __( 'Viewing invoice %1$s to %2$s (of %3$s invoices)', 'invoices' ), $from_num, $to_num, $total ) );
	}

/**
 * Invoice id
 * @since 2.0
 */
function bpe_invoice_id( $i = false )
{
	echo bpe_get_invoice_id( $i );
}
	function bpe_get_invoice_id( $i = false )
	{
		global $invoice_template;
		
		$invoice = ( isset( $invoice_template->invoice ) && empty( $i ) ) ? $invoice_template->invoice : $i;

		if( ! isset( $invoice->id ) )
			return false;

		return apply_filters( 'bpe_get_invoice_id', $invoice->id, $invoice );
	}

/**
 * Invoice user_id
 * @since 2.0
 */
function bpe_invoice_user_id( $i = false )
{
	echo bpe_get_invoice_user_id( $i );
}
	function bpe_get_invoice_user_id( $i = false )
	{
		global $invoice_template;
		
		$invoice = ( isset( $invoice_template->invoice ) && empty( $i ) ) ? $invoice_template->invoice : $i;

		if( ! isset( $invoice->user_id ) )
			return false;

		return apply_filters( 'bpe_get_invoice_user_id', $invoice->user_id, $invoice );
	}

/**
 * Invoice sales
 * @since 2.0
 */
function bpe_invoice_sales( $i = false )
{
	echo bpe_get_invoice_sales( $i );
}
	function bpe_get_invoice_sales( $i = false )
	{
		global $invoice_template;
		
		$invoice = ( isset( $invoice_template->invoice ) && empty( $i ) ) ? $invoice_template->invoice : $i;

		if( ! isset( $invoice->sales ) )
			return false;

		return apply_filters( 'bpe_get_invoice_sales', $invoice->sales, $invoice );
	}

/**
 * Invoice month
 * @since 2.0
 */
function bpe_invoice_month( $i = false )
{
	echo bpe_get_invoice_month( $i );
}
	function bpe_get_invoice_month( $i = false )
	{
		global $invoice_template;
		
		$invoice = ( isset( $invoice_template->invoice ) && empty( $i ) ) ? $invoice_template->invoice : $i;

		if( ! isset( $invoice->month ) )
			return false;

		return apply_filters( 'bpe_get_invoice_month', $invoice->month, $invoice );
	}

/**
 * Invoice sent_date
 * @since 2.0
 */
function bpe_invoice_sent_date( $i = false )
{
	echo bpe_get_invoice_sent_date( $i );
}
	function bpe_get_invoice_sent_date( $i = false )
	{
		global $invoice_template;
		
		$invoice = ( isset( $invoice_template->invoice ) && empty( $i ) ) ? $invoice_template->invoice : $i;

		if( ! isset( $invoice->send_date ) )
			return false;

		return apply_filters( 'bpe_get_invoice_sent_date', $invoice->sent_date, $invoice );
	}

/**
 * Invoice settled
 * @since 2.0
 */
function bpe_invoice_settled( $i = false )
{
	echo bpe_get_invoice_settled( $i );
}
	function bpe_get_invoice_settled( $i = false )
	{
		global $invoice_template;
		
		$invoice = ( isset( $invoice_template->invoice ) && empty( $i ) ) ? $invoice_template->invoice : $i;

		if( ! isset( $invoice->settled ) )
			return false;

		return apply_filters( 'bpe_get_invoice_settled', $invoice->settled, $invoice );
	}

/**
 * Invoice transaction_id
 * @since 2.0
 */
function bpe_invoice_transaction_id( $i = false )
{
	echo bpe_get_invoice_transaction_id( $i );
}
	function bpe_get_invoice_transaction_id( $i = false )
	{
		global $invoice_template;
		
		$invoice = ( isset( $invoice_template->invoice ) && empty( $i ) ) ? $invoice_template->invoice : $i;

		if( ! isset( $invoice->transaction_id ) )
			return false;

		return apply_filters( 'bpe_get_invoice_transaction_id', $invoice->transaction_id, $invoice );
	}

/**
 * Invoice link
 * @since 2.0
 */
function bpe_invoice_link( $user_id = false )
{
	echo bpe_get_invoice_link( $user_id );
}
	function bpe_get_invoice_link( $user_id = false )
	{
		if( ! $user_id )
			$user_id = bp_displayed_user_id();
		
		return apply_filters( 'bpe_get_invoice_link', esc_url( bp_core_get_user_domain( $user_id ) . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'invoice_slug' ) .'/' ), $invoice );
	}
	
/**
 * Invoice view link
 * @since 2.0
 */
function bpe_invoice_view_link( $i = false )
{
	echo bpe_get_invoice_view_link( $i );
}
	function bpe_get_invoice_view_link( $i )
	{
		global $invoice_template;
		
		$invoice = ( isset( $invoice_template->invoice ) && empty( $i ) ) ? $invoice_template->invoice : $i;

		return apply_filters( 'bpe_get_invoice_view_link', esc_url( wp_nonce_url( bpe_get_invoice_link( $invoice ) .'view/'. $invoice->id .'/', 'bpe_invoice_member-'. $invoice->id ) ), $invoice );
	}
	
/**
 * Invoice settle link
 * @since 2.0
 */
function bpe_invoice_settle_link( $i = false )
{
	echo bpe_get_invoice_settle_link( $i );
}
	function bpe_get_invoice_settle_link( $i )
	{
		global $invoice_template;
		
		$invoice = ( isset( $invoice_template->invoice ) && empty( $i ) ) ? $invoice_template->invoice : $i;

		return apply_filters( 'bpe_get_invoice_settle_link', esc_url( wp_nonce_url( bpe_get_invoice_link( $invoice ) .'settle/'. $invoice->id .'/', 'bpe_settle_invoice-'. $invoice->id ) ), $invoice );
	}
	
/**
 * Is invoice settled
 * @since 2.0
 */
function bpe_invoice_is_unsettled( $i = false )
{
	global $invoice_template;
	
	// without an email address, no button
	if( bpe_get_option( 'paypal_email' ) )
		return false;
	
	$invoice = ( isset( $invoice_template->invoice ) && empty( $i ) ) ? $invoice_template->invoice : $i;

	if( $invoice->settled == 1 )
		return true;
		
	return false;
}
?>