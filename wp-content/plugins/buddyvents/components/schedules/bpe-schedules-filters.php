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

add_filter( 'bpe_events_before_save_schedules_event_id', 	'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_schedules_day', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_schedules_start', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_schedules_end', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_schedules_description', 'wp_filter_post_kses',  1 );

add_filter( 'bpe_events_get_schedule_description',			'wptexturize'			  );
add_filter( 'bpe_events_get_schedule_description', 			'make_clickable'		  );
add_filter( 'bpe_events_get_schedule_description', 			'wp_filter_kses',  		1 );
add_filter( 'bpe_events_get_schedule_description', 			'wpautop'				  );
add_filter( 'bpe_events_get_schedule_description', 			'convert_chars'			  );
add_filter( 'bpe_events_get_schedule_description', 			'stripslashes'			  );

add_filter( 'bpe_get_raw_schedule_description', 			'stripslashes'			  );
?>