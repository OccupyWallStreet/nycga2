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

add_filter( 'bpe_events_before_save_documents_event_id', 	'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_documents_name', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_documents_description', 'wp_filter_post_kses',  1 );
add_filter( 'bpe_events_before_save_documents_url', 		'wp_filter_kses',  		1 );
add_filter( 'bpe_events_before_save_documents_type', 		'wp_filter_kses',  		1 );

add_filter( 'bpe_get_document_description', 				'wptexturize' 		 	  );
add_filter( 'bpe_get_document_description', 				'make_clickable' 	 	  );
add_filter( 'bpe_get_document_description', 				'wpautop' 				  );
add_filter( 'bpe_get_document_description', 				'convert_chars'			  );
add_filter( 'bpe_get_document_description', 				'stripslashes'			  );

add_filter( 'bpe_get_document_name', 						'wptexturize'			  );
add_filter( 'bpe_get_document_name', 						'convert_chars'			  );
add_filter( 'bpe_get_document_name', 						'stripslashes'			  );

add_filter( 'bpe_get_raw_document_description', 			'stripslashes'			  );
?>