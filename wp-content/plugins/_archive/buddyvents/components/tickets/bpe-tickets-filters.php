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

add_filter( 'bpe_tickets_before_save_tickets_event_id', 	'wp_filter_kses', 		 1 );
add_filter( 'bpe_tickets_before_save_tickets_name', 		'wp_filter_kses', 		 1 );
add_filter( 'bpe_tickets_before_save_tickets_description', 	'wp_filter_kses', 		 1 );
add_filter( 'bpe_tickets_before_save_tickets_price', 		'wp_filter_kses', 		 1 );
add_filter( 'bpe_tickets_before_save_tickets_currency', 	'wp_filter_kses', 		 1 );
add_filter( 'bpe_tickets_before_save_tickets_quantity', 	'wp_filter_kses', 		 1 );
add_filter( 'bpe_tickets_before_save_tickets_start_sales',	'wp_filter_kses', 		 1 );
add_filter( 'bpe_tickets_before_save_tickets_end_sales', 	'wp_filter_kses', 		 1 );
add_filter( 'bpe_tickets_before_save_tickets_min_tickets', 	'wp_filter_kses', 		 1 );
add_filter( 'bpe_tickets_before_save_tickets_max_tickets', 	'wp_filter_kses', 		 1 );

add_filter( 'bpe_events_get_ticket_description', 			'wptexturize' 			   );
add_filter( 'bpe_events_get_ticket_description', 			'make_clickable'		   );
add_filter( 'bpe_events_get_ticket_description', 			'bp_groups_filter_kses', 1 );
add_filter( 'bpe_events_get_ticket_description', 			'wpautop' 				   );
add_filter( 'bpe_events_get_ticket_description', 			'convert_chars' 		   );
add_filter( 'bpe_events_get_ticket_description', 			'stripslashes' 			   );

add_filter( 'bpe_events_get_ticket_name', 					'wptexturize' 			   );
add_filter( 'bpe_events_get_ticket_name', 					'stripslashes'    		   );
add_filter( 'bpe_events_get_ticket_name', 					'convert_chars'			   );

add_filter( 'bpe_sales_before_save_sales_ticket_id', 		'wp_filter_kses', 		 1 );
add_filter( 'bpe_sales_before_save_sales_seller_id', 		'wp_filter_kses', 		 1 );
add_filter( 'bpe_sales_before_save_sales_buyer_id', 		'wp_filter_kses', 		 1 );
add_filter( 'bpe_sales_before_save_sales_single_price', 	'wp_filter_kses',		 1 );
add_filter( 'bpe_sales_before_save_sales_currency', 		'wp_filter_kses',		 1 );
add_filter( 'bpe_sales_before_save_sales_quantity', 		'wp_filter_kses',		 1 );
add_filter( 'bpe_sales_before_save_sales_attendees', 		'wp_filter_kses',		 1 );
add_filter( 'bpe_sales_before_save_sales_gateway', 			'wp_filter_kses',		 1 );
add_filter( 'bpe_sales_before_save_sales_sales_id', 		'wp_filter_kses',		 1 );
add_filter( 'bpe_sales_before_save_sales_status', 			'wp_filter_kses',		 1 );
add_filter( 'bpe_sales_before_save_sales_commission', 		'wp_filter_kses',		 1 );
add_filter( 'bpe_sales_before_save_sales_requested', 		'wp_filter_kses',		 1 );

add_filter( 'bpe_before_save_invoice_user_id', 				'wp_filter_kses',		 1 );
add_filter( 'bpe_before_save_invoice_sales', 				'wp_filter_kses',		 1 );
add_filter( 'bpe_before_save_invoice_month', 				'wp_filter_kses',		 1 );
add_filter( 'bpe_before_save_invoice_sent_date', 			'wp_filter_kses',		 1 );
add_filter( 'bpe_before_save_invoice_settled', 				'wp_filter_kses',		 1 );
add_filter( 'bpe_before_save_invoice_transaction_id', 		'wp_filter_kses',		 1 );

add_filter( 'bpe_sales_get_ticket_name', 					'wptexturize'			   );
add_filter( 'bpe_sales_get_ticket_name', 					'stripslashes'			   );
add_filter( 'bpe_sales_get_ticket_name', 					'convert_chars'			   );

add_filter( 'bpe_sales_get_ticket_description', 			'wptexturize'			   );
add_filter( 'bpe_sales_get_ticket_description', 			'make_clickable' 		   );
add_filter( 'bpe_sales_get_ticket_description', 			'wpautop'				   );
add_filter( 'bpe_sales_get_ticket_description', 			'convert_chars'			   );
add_filter( 'bpe_sales_get_ticket_description', 			'stripslashes'			   );

add_filter( 'bpe_get_sale_ticket_name', 					'stripslashes'			   );
add_filter( 'bpe_get_raw_sale_ticket_description', 			'stripslashes'			   );
add_filter( 'bpe_get_raw_ticket_name', 						'stripslashes'			   );
add_filter( 'bpe_get_raw_ticket_description', 				'stripslashes'			   );
?>