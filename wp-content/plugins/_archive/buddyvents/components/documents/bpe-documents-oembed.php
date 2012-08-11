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
 * Setup oEmbed for schedule descriptions
 *
 * @package Schedules
 * @since 	2.0
 */
function bpe_setup_documents_oembed( $data )
{
	add_filter( 'bpe_get_document_description', 			 array( &$data, 'run_shortcode' ), 	7 );
	add_filter( 'bpe_get_document_description', 			 array( &$data, 'autoembed' 	), 	8 );
}
add_action( 'bp_core_setup_oembed', 'bpe_setup_documents_oembed' );

/**
 * Setup the oEmbed cache
 * 
 * @TODO	Videos don't display if the caching function is enabled. Needs fixing!
 *  *
 * @package Documents
 * @since 	2.1
 */
function bpe_event_document_embed()
{
	add_filter( 'embed_post_id', 		 'bpe_get_document_event_id' 		  );
	add_filter( 'bp_embed_get_cache', 	 'bpe_embed_event_cache', 		10, 2 );
	add_action( 'bp_embed_update_cache', 'bpe_embed_event_save_cache', 	10, 3 );
}
//add_action( 'bpe_documents_loop_start', 'bpe_event_document_embed' );
?>