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
 * Add a ticket
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_add_ticket( $id = null, $event_id, $name, $description, $price, $currency, $quantity, $start_sales, $end_sales, $min_tickets, $max_tickets )
{
	$ticket = new Buddyvents_Tickets( $id );

	$ticket->event_id 		= $event_id;
	$ticket->name 			= $name;
	$ticket->description 	= $description;
	$ticket->price 			= $price;
	$ticket->currency 		= $currency;
	$ticket->quantity 		= $quantity;
	$ticket->start_sales 	= $start_sales;
	$ticket->end_sales 		= $end_sales;
	$ticket->min_tickets 	= $min_tickets;
	$ticket->max_tickets 	= $max_tickets;

	if( $new_id = $ticket->save() )
		return $new_id;
		
	return false;
}

/**
 * Add a sale
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_add_sale( $id = null, $ticket_id, $seller_id, $buyer_id, $single_price, $currency, $quantity, $attendees, $gateway, $sales_id, $status, $sale_date, $commission, $requested )
{
	$sale = new Buddyvents_Sales( $id );

	$sale->ticket_id 	= $ticket_id;
	$sale->seller_id 	= $seller_id;
	$sale->buyer_id 	= $buyer_id;
	$sale->single_price = $single_price;
	$sale->currency 	= $currency;
	$sale->quantity 	= $quantity;
	$sale->attendees 	= $attendees;
	$sale->gateway		= $gateway;
	$sale->sales_id 	= $sales_id;
	$sale->status 		= $status;
	$sale->sale_date 	= $sale_date;
	$sale->commission 	= $commission;
	$sale->requested 	= $requested;
	
	if( $new_id = $sale->save() )
		return $new_id;
		
	return false;
}

/**
 * Add an invoice
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_add_invoice( $id = null, $user_id, $sales, $month, $sent_date, $settled, $transaction_id )
{
	$invoice = new Buddyvents_Invoices( $id );

	$invoice->user_id 			= $user_id;
	$invoice->sales 			= $sales;
	$invoice->month 			= $month;
	$invoice->sent_date 		= $sent_date;
	$invoice->settled 			= $settled;
	$invoice->transaction_id 	= $transaction_id;
	
	if( $new_id = $invoice->save() )
		return $new_id;
		
	return false;
}

/**
 * Get sales
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_get_sales( $args = '' )
{
	global $bp;
	
	$defaults = array(
		'ids' 			=> false,
		'ticket_id' 	=> false,
		'seller_id' 	=> false,
		'buyer_id' 		=> false,
		'event_id' 		=> false,
		'single_price' 	=> false,
		'currency' 		=> false,
		'quantity' 		=> false,
		'gateway' 		=> false,
		'sales_id' 		=> false,
		'status' 		=> false,
		'sale_date' 	=> false,
		'month' 		=> false,
		'year' 			=> false,
		'requested' 	=> false,
		'per_page' 		=> 20,
		'page' 			=> 1,
		'search_terms' 	=> false
	);	

	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	$sales = Buddyvents_Sales::get( $ids, (int)$ticket_id, (int)$seller_id, (int)$buyer_id, (int)$event_id, $single_price, $currency, (int)$quantity, $gateway, (int)$sales_id, $status, $sale_date, $month, $year, $requested, (int)$page, (int)$per_page, $search_terms );

	return apply_filters( 'bpe_get_sales', $sales, &$params );
}

/**
 * Get tickets
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_get_tickets( $args = '' )
{
	global $bp;
	
	$defaults = array(
		'event_id' 		=> false,
		'name' 			=> false,
		'price' 		=> false,
		'currency' 		=> false,
		'quantity' 		=> false,
		'start_sales' 	=> false,
		'end_sales' 	=> false,
		'min_tickets' 	=> false,
		'max_tickets' 	=> false,
		'available' 	=> false,
		'per_page' 		=> 20,
		'page' 			=> 1,
		'search_terms' 	=> false
	);	

	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	$tickets = Buddyvents_Tickets::get( (int)$event_id, $name, $price, $currency, (int)$quantity, $start_sales, $end_sales, (int)$min_tickets, (int)$max_tickets, (bool)$available, (int)$page, (int)$per_page, $search_terms );

	return apply_filters( 'bpe_get_tickets', $tickets, &$params );
}

/**
 * Get invoices
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_get_invoices( $args = '' )
{
	global $bp;
	
	$defaults = array(
		'ids' 			=> false,
		'user_id' 		=> false,
		'month' 		=> false,
		'sent_date' 	=> false,
		'sent' 			=> false,
		'settled' 		=> false,
		'per_page' 		=> 20,
		'page' 			=> 1,
		'search_terms' 	=> false
	);	

	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	$tickets = Buddyvents_Invoices::get( $ids, (int)$user_id, $month, $sent_date, $sent, $settled, (int)$page, (int)$per_page, $search_terms );

	return apply_filters( 'bpe_get_invoices', $tickets, &$params );
}

/**
 * Delete tickets by id
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_delete_tickets_by_ids( $ids )
{
	return Buddyvents_Tickets::delete_tickets_by_ids( $ids );
}

/**
 * Delete tickets by event
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_delete_tickets_by_event( $event_id )
{
	return Buddyvents_Tickets::delete_tickets_by_event( $event_id );
}

/**
 * Get the number of schedules for an event
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_ticket_amount( $id )
{
	return Buddyvents_Tickets::ticket_amount( $id );
}

/**
 * Get the start date of the next ticket sale
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_get_next_ticket_sale_date( $id )
{
	return Buddyvents_Tickets::get_next_ticket_sale_date( $id );
}

/**
 * Save a status
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_sales_set_status( $status = false, $id = false )
{
	return Buddyvents_Sales::set_status( $status, $id );
}

/**
 * Save a sales id
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_sales_set_sales_id( $sales_id = false, $id = false )
{
	return Buddyvents_Sales::set_sales_id( $sales_id, $id );
}

/**
 * Set requested
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_sales_set_requested( $ids = false, $requested = false )
{
	return Buddyvents_Sales::set_requested( $ids, $requested );
}

/**
 * Get all PayPal tax_ids
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_get_all_txn_ids()
{
	return Buddyvents_Sales::get_all_txn_ids();
}

/**
 * Get all invoice PayPal tax_ids
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_get_all_invoice_txn_ids()
{
	return Buddyvents_Invoices::get_all_txn_ids();
}

/**
 * Get the event for a sale
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_ticket_sale_get_event( $sale_id = false )
{
	return Buddyvents_Sales::get_event( $sale_id );
}

/**
 * Update an invoice date
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_ticket_update_date( $date = false, $ids = false )
{
	return Buddyvents_Invoices::update_date( $date, $ids );
}

/**
 * Bulk delete invoices
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_tickets_delete_invoices( $ids = false )
{
	return Buddyvents_Invoices::delete_invoices( $ids );
}

/**
 * Change the settled status of an invoice
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_invoice_change_settled( $settled = false, $ids = false )
{
	return Buddyvents_Invoices::change_settled( $settled, $ids );
}
?>