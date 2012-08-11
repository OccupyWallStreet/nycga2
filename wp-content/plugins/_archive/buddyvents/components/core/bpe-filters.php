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

add_filter( 'bpe_events_before_save_webhooks_user_id', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_webhooks_event', 		'wp_filter_kses',  		1 );

add_filter( 'bpe_before_save_notifications_user_id', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_before_save_notifications_keywords', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_before_save_notifications_email', 			'wp_filter_kses',  		1 );
add_filter( 'bpe_before_save_notifications_screen', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_before_save_notifications_remind', 		'wp_filter_kses',  		1 );

add_filter( 'bpe_events_before_save_members_event_id', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_members_user_id', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_members_rsvp', 			'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_members_rsvp_date', 	'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_members_role', 			'wp_filter_kses',  		1 );

add_filter( 'bpe_events_before_save_events_user_id', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_group_id', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_name', 			'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_slug', 			'wp_filter_kses',		1 );

add_filter( 'bpe_events_before_save_events_description',	'wp_filter_post_kses',  1 );
add_filter( 'bpe_events_before_save_events_description',	'force_balance_tags' 	  );

add_filter( 'bpe_events_before_save_events_category', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_url', 			'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_location', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_venue_name', 	'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_longitude', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_latitude', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_start_date', 	'wp_filter_kses',		1 );
add_filter( 'bpe_events_before_save_events_start_time', 	'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_end_date', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_end_time', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_date_created', 	'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_public', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_limit_members', 	'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_recurrent', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_is_spam', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_approved', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_rsvp', 			'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_all_day', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_events_timezone', 		'wp_filter_kses',  		1 );

add_filter( 'bpe_before_save_coords_user_id', 				'wp_filter_kses',  		1 );
add_filter( 'bpe_before_save_coords_group_id', 				'wp_filter_kses',  		1 );
add_filter( 'bpe_before_save_coords_lat', 					'wp_filter_kses',  		1 );
add_filter( 'bpe_before_save_coords_lng', 					'wp_filter_kses',  		1 );
 
add_filter( 'bpe_events_before_save_categories_name', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_categories_slug', 		'wp_filter_kses',  		1 );

add_filter( 'bpe_events_get_events_description', 			'wptexturize' 		 	  );
add_filter( 'bpe_events_get_events_description', 			'make_clickable'		  );
add_filter( 'bpe_events_get_events_description', 			'wpautop' 			 	  );
add_filter( 'bpe_events_get_events_description', 			'convert_chars' 	 	  );
add_filter( 'bpe_events_get_events_description', 			'stripslashes' 		 	  );

add_filter( 'bpe_events_get_events_name', 					'wptexturize'			  );
add_filter( 'bpe_events_get_events_name', 					'stripslashes'			  );
add_filter( 'bpe_events_get_events_name', 					'convert_chars'			  );

add_filter( 'bpe_events_get_events_cat_name', 				'wptexturize' 			  );
add_filter( 'bpe_events_get_events_cat_name', 				'convert_chars'			  );
add_filter( 'bpe_events_get_events_cat_name', 				'stripslashes'			  );

add_filter( 'bpe_events_get_events_venue_name', 			'wptexturize' 			  );
add_filter( 'bpe_events_get_events_venue_name', 			'convert_chars'			  );
add_filter( 'bpe_events_get_events_venue_name', 			'stripslashes'			  );

add_filter( 'bpe_events_get_events_location', 				'wptexturize'			  );
add_filter( 'bpe_events_get_events_location', 				'convert_chars'			  );
add_filter( 'bpe_events_get_events_location', 				'stripslashes'			  );

add_filter( 'bpe_events_get_events_description_excerpt', 	'wptexturize'			  );
add_filter( 'bpe_events_get_events_description_excerpt', 	'make_clickable'		  );
add_filter( 'bpe_events_get_events_description_excerpt', 	'wp_filter_kses',  		1 );
add_filter( 'bpe_events_get_events_description_excerpt', 	'wpautop'				  );
add_filter( 'bpe_events_get_events_description_excerpt', 	'convert_chars'			  );
add_filter( 'bpe_events_get_events_description_excerpt', 	'stripslashes'			  );

add_filter( 'bpe_events_get_raw_events_description_excerpt', 'wptexturize'			  );
add_filter( 'bpe_events_get_raw_events_description_excerpt', 'make_clickable'		  );
add_filter( 'bpe_events_get_raw_events_description_excerpt', 'wp_filter_kses', 		1 );
add_filter( 'bpe_events_get_raw_events_description_excerpt', 'wpautop'				  );
add_filter( 'bpe_events_get_raw_events_description_excerpt', 'convert_chars'		  );
add_filter( 'bpe_events_get_raw_events_description_excerpt', 'stripslashes'			  );

add_filter( 'bpe_events_get_events_url', 					'make_clickable'		  );

add_filter( 'bpe_event_get_group_address_website', 			'make_clickable'		  );
add_filter( 'bpe_event_get_group_address_website', 			'stripslashes'			  );

add_filter( 'bpe_get_event_group_address_fax', 				'stripslashes'			  );
add_filter( 'bpe_get_event_group_address_mobile', 			'stripslashes'			  );
add_filter( 'bpe_get_event_group_address_country', 			'stripslashes'			  );
add_filter( 'bpe_get_event_group_address_phone', 			'stripslashes'			  );
add_filter( 'bpe_get_event_group_address_postcode', 		'stripslashes'			  );
add_filter( 'bpe_get_event_group_address_city', 			'stripslashes'			  );
add_filter( 'bpe_get_event_group_address_street', 			'stripslashes'			  );
add_filter( 'bpe_get_event_group_name', 					'stripslashes'			  );
add_filter( 'bpe_get_event_group_description', 				'stripslashes'			  );
add_filter( 'bpe_get_raw_event_timezone', 					'stripslashes'			  );
add_filter( 'bpe_get_raw_event_description', 				'stripslashes'			  );
?>