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
 * PayPal return handler
 * @since 2.0
 */
function bpe_paypal_return_handler()
{
	global $bp, $bpe;
			
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( 'return' ) )
	{
		list( $user_id, $hash, $sales_id, $event_id, $context ) = explode( '|', $_GET['cm'] );
		
		$ticket_id = (int)$_GET['item_number'];
		$txn_id = wp_filter_kses( $_GET['tx'] );
		$status = wp_filter_kses( $_GET['st'] );
		$amount = (float)$_GET['amt'];
		$currency = wp_filter_kses( $_GET['cc'] );
		
		do_action( 'bpe_paypal_'. $context .'_return_handler', $user_id, $hash, $sales_id, $event_id, $ticket_id, $txn_id, $status, $amount, $currency );
		
		switch( $context )
		{
			case 'invoice' :
				bp_core_redirect( bpe_get_invoice_link( $user_id ) . bpe_get_option( 'success_slug' ) .'/' );
				break;
				
			case 'ticket' :
				bp_core_redirect( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'checkout_slug' ) .'/'. bpe_get_option( 'success_slug' ) .'/'. $event_id .'/' );
				break;
		}
		
		exit;
	}
}
add_action( 'wp', 'bpe_paypal_return_handler', 0 );

/**
 * PayPal IPN handler
 * @since 2.0
 */
function bpe_paypal_ipn_handler()
{
	global $bp, $bpe;
			
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( 'ipn' ) )
	{
		// check the IPN
		if( ! bpe_paypal_verify_ipn() )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( 'IPN could not get verified.', 'paypal' );
			
			return false;
		}
			
		list( $user_id, $hash, $sale_id, $event_id ) = explode( '|', $_POST['custom'] );

		$buyer = new WP_User( $user_id );

		// ignore IPN if invalid user
		if( empty( $buyer->ID ) )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG == true )
				bpe_log( $buyer, 'paypal', 'No buyer data.' );
			
			return false;
		}
		
		// check the ticket id
		$ticket_id = (int)$_POST['item_number'];
		$control_hash = wp_hash( $ticket_id );

		if( $hash != $control_hash )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( array( $hash, $control_hash ), 'paypal', 'Invalid hash data.' );
			
			return false;
		}
		
		// get the ticket
		$ticket = new Buddyvents_Tickets( $ticket_id );
		
		// ignore IPN if invalid ticket
		if( empty( $ticket->id ) )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( $ticket, 'paypal', 'No ticket data.' );
	
			return false;
		}
		
		$sale = new Buddyvents_Sales( $sale_id );

		// ignore IPN if invalid sale
		if( empty( $sale->id ) )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( $sale, 'paypal', 'No sales data.' );
		
			return false;
		}

		$amount = ( empty( $_POST['payment_gross'] ) ) ? $_POST['mc_gross'] : $_POST['payment_gross'];
		
		// check price
        if( $amount != ( $sale->quantity * $sale->single_price ) )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( array( $amount, ( $sale->quantity * $sale->single_price ) ), 'paypal', 'Price does not match.' );
	
			return false;
		}
			
		// check currency
		if( $_POST['mc_currency'] != $sale->currency )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( array( $_POST['mc_currency'], $sale->currency ), 'paypal', 'Currency does not match.' );
	
			return false;
		}
			
		$event = new Buddyvents_Events( $event_id );

		// ignore IPN if invalid event
		if( ! bpe_get_event_id( $event ) )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( $event, 'paypal', 'No event data.' );
	
			return false;
		}
			
		// check email
		if( strtolower( $_POST['business'] ) != strtolower( bp_get_user_meta( bpe_get_event_user_id( $event ), 'bpe_paypal_email', true ) ) )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( array( strtolower( $_POST['business'] ), strtolower( bp_get_user_meta( bpe_get_event_user_id( $event ), 'bpe_paypal_email', true ) ) ), 'paypal', 'Email does not match.' );
	
			return false;
		}
			
		do_action( 'bpe_before_paypal_ipn_handler', $event, $ticket, $sale, $buyer );

		bpe_set_paypal_status( $event, $ticket, $sale, $buyer );

		do_action( 'bpe_after_paypal_ipn_handler', $event, $ticket, $sale, $buyer );
		
		exit();
	}
}
add_action( 'wp', 'bpe_paypal_ipn_handler', 0 );

/**
 * Set the status
 * @since 2.0
 */
function bpe_set_paypal_status( $event, $ticket, $sale, $buyer )
{
	if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
		bpe_log( $_POST, 'paypal' );
		
	$status = strtolower( $_POST['payment_status'] );

	do_action( 'bpe_'. $status .'_paypal_status', $event, $ticket, $sale, $buyer );

	switch( $status )
	{
		case 'completed' :
			// check tansaction id isn't a duplicate
			$txn_ids = bpe_get_all_txn_ids();
			if( in_array( $_POST['txn_id'], (array)$txn_ids ) )
			{
				if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
					bpe_log( array( $_POST['txn_id'], $txn_ids ), 'paypal', 'Transaction ID already exists.' );
		
				return false;
			}

			bpe_sales_set_status( 'completed', $sale->id );
			bpe_sales_set_sales_id( $_POST['txn_id'], $sale->id );

			$existing_id = bpe_is_user_member_already( bpe_get_event_id( $event ), $buyer->ID );
			
			if( ! $existing_id )
				$existing_id = bpe_was_user_member( bpe_get_event_id( $event ), $buyer->ID );
			
			bpe_add_member( $existing_id, bpe_get_event_id( $event ), $buyer->ID, 1, bp_core_current_time(), 'attendee' );

			do_action( 'bpe_attend_event', $event, $buyer->ID ); 

			// send the tickets
			bpe_sales_send_tickets( $event, $ticket, $sale, $buyer );
			break;
	
		case 'reversed' :
			bpe_sales_set_status( 'reversed', $sale->id );
			break;
	
		case 'canceled_reversal' :
			bpe_sales_set_status( 'canceled_reversal', $sale->id );
			break;
	
		case 'denied' :
			bpe_sales_set_status( 'denied', $sale->id );
			break;
	
		case 'pending' :
			bpe_sales_set_status( 'pending', $sale->id );
			break;
	
		case 'refunded' :
			bpe_sales_set_status( 'refunded', $sale->id );
			break;
	
		case 'voided' :
			bpe_sales_set_status( 'voided', $sale->id );
			break;
	
		case 'processed' :
			bpe_sales_set_status( 'processed', $sale->id );
			break;
	
		case 'failed' :
			bpe_sales_set_status( 'failed', $sale->id );
			break;
	}

	do_action( 'bpe_after_'. $status .'_paypal_status', $event, $ticket, $sale, $buyer  );
}

/**
 * Verify an IPN call
 * @since 2.0
 */
function bpe_paypal_verify_ipn()
{
	if( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV )
		return false;

	$url = ( bpe_get_option( 'enable_sandbox' ) === true ) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr?' : 'https://www.paypal.com/cgi-bin/webscr?';
	
	$result = wp_remote_post( $url, array( 'ssl' => true, 'body' => 'cmd=_notify-validate&'. http_build_query( $_POST, '', '&' ) ) );
	
	if( wp_remote_retrieve_body( $result ) == 'VERIFIED' )
		return true;
		
	return false;
}

/**
 * Redirect to PayPal
 * @since 2.0
 */
function bpe_redirect_to_paypal()
{
	global $bp, $bpe;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'checkout_slug' ), 1 ) && bp_is_action_variable( 'paypal', 2 ) )
	{
		check_admin_referer( 'bpe_purchase_ticket_'. $_POST['event_id'] );

		$ticket = new Buddyvents_Tickets( (int)$_POST['ticket_id'] );

		do_action( 'bpe_inside_redirect_to_paypal', bpe_get_displayed_event(), bp_loggedin_user_id(), $ticket );
		
		// any extra attendees
		$attendees = array();
		if( isset( $_POST['emails'] ) && isset( $_POST['names'] ) )
			$attendees = array_combine( (array)$_POST['emails'], (array)$_POST['names'] );
		
		$attendees = maybe_serialize( $attendees );

		// redirect for free ticket
		if( $ticket->price == 0.00 )
		{
			$existing_id = bpe_is_user_member_already( bpe_get_displayed_event( 'id' ), bp_loggedin_user_id() );
			
			if( ! $existing_id )
				$existing_id = bpe_was_user_member( bpe_get_displayed_event( 'id' ), bp_loggedin_user_id() );
			
			bpe_add_member( $existing_id, bpe_get_displayed_event( 'id' ), bp_loggedin_user_id(), 1, bp_core_current_time(), 'attendee' );

			do_action( 'bpe_attend_event', bpe_get_displayed_event(), bp_loggedin_user_id() ); 

			bp_core_add_message( __( 'You are attending this event on a free ticket.', 'events' ) );
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
		}

		// add the sale as pending
		$quantity = (int)$_POST['sale'][$ticket->id]['quantity'];
		$sale_id = bpe_add_sale( null, $ticket->id, bpe_get_displayed_event( 'user_id' ), bp_loggedin_user_id(), $ticket->price, $ticket->currency, $quantity, $attendees, 'paypal', '', 'pending', bp_core_current_time(), bpe_get_option( 'commission_percent' ), 0 );
		
		// construct the link
		$link  = ( bpe_get_option( 'enable_sandbox' ) === true ) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr?' : 'https://www.paypal.com/cgi-bin/webscr?';
		$link .= http_build_query( array(
			'cmd' => '_xclick',
			'business' => bp_get_user_meta( bpe_get_displayed_event( 'user_id' ), 'bpe_paypal_email', true ),
			'item_name' => $ticket->name,
			'currency_code' => $ticket->currency,
			'amount' => $ticket->price,
			'quantity' => $quantity,
			'custom' => bp_loggedin_user_id() .'|'. wp_hash( $ticket->id ) .'|'. $sale_id .'|'. bpe_get_displayed_event( 'id' ) .'|ticket',
			'item_number' => $ticket->id,
			'notify_url' => bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/ipn/',
			'cancel_return' => bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'checkout_slug' ) .'/'. bpe_get_option( 'cancel_slug' ) .'/',
			'return' => bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/return/',
			'rm' => 2
		), '', '&' );
		
		do_action( 'bpe_just_before_redirect_to_paypal', $sale_id, $ticket, bpe_get_displayed_event(), bp_loggedin_user_id() );

		// cannot use bp_core_redirect or wp_redirect due to paypal
		header( 'Location: '. apply_filters( 'bpe_redirect_to_paypal_url', $link, $sale_id, $ticket, bpe_get_displayed_event(), bp_loggedin_user_id() ) );
		exit;
	}
}
add_action( 'wp', 'bpe_redirect_to_paypal', 0 );

/**
 * Redirect an invoice payment to PayPal
 * @since 2.0
 */
function bpe_redirect_invoice_to_paypal()
{
	global $bp, $bpe;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'invoice_slug' ) ) && bp_is_action_variable( 'settle', 0 ) && is_numeric( $bp->action_variables[1] ) )
	{
		check_admin_referer( 'bpe_settle_invoice-'. bp_action_variable( 1 ) );
		
		if( ! bpe_get_option( 'paypal_email' ) )
			bp_core_redirect( bp_core_get_user_domain( bp_displayed_user_id() ) . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'invoice_slug' ) .'/' );

		$result = bpe_get_invoices( array( 'ids' => (int)bp_action_variable( 1 ) ) );
		$invoice = $result['invoices'][0];

		do_action( 'bpe_inside_redirect_invoice_to_paypal', $invoice, bp_displayed_user_id() );
		
		foreach( $invoice->datasets as $sale ) :
			$payment += bpe_sale_get_commission( $sale );
		endforeach;
		
		// construct the link
		$link  = ( bpe_get_option( 'enable_sandbox' ) === true ) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr?' : 'https://www.paypal.com/cgi-bin/webscr?';
		$link .= http_build_query( array(
			'cmd' => '_xclick',
			'business' => bpe_get_option( 'paypal_email' ),
			'item_name' => sprintf( __( 'Payment for invoice %s from %s', 'events' ), zeroise( $invoice->id, 10 ), mysql2date( bpe_get_option( 'date_format' ), $invoice->sent_date ) ),
			'currency_code' => $invoice->datasets[0]->currency,
			'amount' => $payment,
			'quantity' => 1,
			'custom' => bp_displayed_user_id() .'|'. wp_hash( $invoice->id ) .'|0|0|invoice',
			'item_number' => $invoice->id,
			'notify_url' => bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'invoice_slug' ) .'/ipn/',
			'cancel_return' => bpe_get_invoice_link() . bpe_get_option( 'cancel_slug' ) .'/',
			'return' => bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/return/',
			'rm' => 2
		), '', '&' );

		do_action( 'bpe_just_before_redirect_invoice_to_paypal', $invoice, bp_displayed_user_id() );

		// cannot use bp_core_redirect or wp_redirect due to paypal
		header( 'Location: '. apply_filters( 'bpe_redirect_invoice_to_paypal_url', $link, $invoice, bp_displayed_user_id() ) );
		exit;
	}
}
add_action( 'wp', 'bpe_redirect_invoice_to_paypal', 0 );

/**
 * PayPal invoice IPN handler
 * @since 2.0
 */
function bpe_paypal_invoice_ipn_handler()
{
	global $bp;
			
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'invoice_slug' ) ) && bp_is_action_variable( 'ipn', 0 ) )
	{
		// check the IPN
		if( ! bpe_paypal_verify_ipn() )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( 'IPN could not get verified.', 'paypal' );
			
			return false;
		}
			
		list( $user_id, $hash ) = explode( '|', $_POST['custom'] );
		
		// check the ticket id
		$invoice_id = (int)$_POST['item_number'];
		$control_hash = wp_hash( $ticket_id );

		if( $hash != $control_hash )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( array( $hash, $control_hash ), 'paypal', 'Invalid hash data.' );
	
			return false;
		}

		// get the invoice
		$result = bpe_get_invoices( array( 'ids' => (int)$invoice_id ) );
		$invoice = $result['invoices'][0];
		
		// ignore IPN if invalid ticket
		if( empty( $invoice->id ) )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( $ticket, 'paypal', 'No invoice data.' );
	
			return false;
		}

		$amount = ( empty( $_POST['payment_gross'] ) ) ? $_POST['mc_gross'] : $_POST['payment_gross'];
		
		foreach( $invoice->datasets as $sale )
			$payment += bpe_sale_get_commission( $sale );
		
		// check price
        if( $amount != $payment )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( array( $amount, $payment ), 'paypal', 'Price does not match.' );
	
			return false;
		}
			
		// check currency
		if( $_POST['mc_currency'] != $invoice->datasets[0]->currency )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( array( $_POST['mc_currency'], $invoice->datasets[0]->currency ), 'paypal', 'Currency does not match.' );
	
			return false;
		}

		// check email
		if( strtolower( $_POST['business'] ) != strtolower( bpe_get_option( 'paypal_email' ) ) )
		{
			if( defined( 'EVENT_DEBUG' ) && EVENT_DEBUG === true )
				bpe_log( array( strtolower( $_POST['business'] ), strtolower( bpe_get_option( 'paypal_email' ) ) ), 'paypal', 'Email does not match.' );
	
			return false;
		}
			
		do_action( 'bpe_before_paypal_invoice_ipn_handler', $invoice);

		bpe_set_invoice_paypal_status( $invoice );

		do_action( 'bpe_after_paypal_invoice_ipn_handler', $invoice );
		
		exit();
	}
}
add_action( 'wp', 'bpe_paypal_invoice_ipn_handler', 0 );

/**
 * Set the status
 * @since 2.0
 */
function bpe_set_invoice_paypal_status( $invoice )
{
	if( EVENT_DEBUG === true )
		bpe_log( $_POST, 'paypal' );
		
	$status = strtolower( $_POST['payment_status'] );

	do_action( 'bpe_before_'. $status .'_invoice_paypal_status', $invoice );

	switch( $status )
	{
		case 'completed' :
			// check tansaction id isn't a duplicate
			$txn_ids = bpe_get_all_invoice_txn_ids();
			if( in_array( $_POST['txn_id'], (array)$txn_ids ) )
			{
				if( EVENT_DEBUG === true )
					bpe_log( array( $_POST['txn_id'], $txn_ids ), 'paypal', 'Transaction ID already exists.' );
		
				return false;
			}
			
			bpe_invoice_change_settled( 1, $invoice->id );
			break;
	
		case 'reversed' :
		case 'canceled_reversal' :
		case 'denied' :
		case 'pending' :
		case 'refunded' :
		case 'voided' :
		case 'processed' :
		case 'failed' :
			break;
	}

	do_action( 'bpe_after_'. $status .'_invoice_paypal_status', $invoice  );
}
?>