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
 * Setup oEmbed for various descriptions
 *
 * @package Core
 * @since 	2.0
 */
function bpe_setup_oembed( $data )
{
	add_filter( 'bpe_events_get_events_description', 		 array( &$data, 'run_shortcode' ), 	7 );
	add_filter( 'bpe_events_get_events_description', 		 array( &$data, 'autoembed' 	), 	8 );

	add_filter( 'bpe_events_get_events_description_excerpt', array( &$data, 'run_shortcode' ),  7 );
	add_filter( 'bpe_events_get_events_description_excerpt', array( &$data, 'autoembed' 	), 	8 );
}
add_action( 'bp_core_setup_oembed', 'bpe_setup_oembed' );

/**
 * Setup the oEmbed cache
 * 
 * @todo	fix this cache function
 *
 * @package Core
 * @since 	2.0
 */
function bpe_event_embed()
{
	add_filter( 'embed_post_id', 		 'bpe_get_event_id'					  );
	add_filter( 'bp_embed_get_cache', 	 'bpe_embed_event_cache', 		10, 2 );
	add_action( 'bp_embed_update_cache', 'bpe_embed_event_save_cache', 	10, 3 );
}
add_action( 'bpe_events_loop_start', 'bpe_event_embed', 99 );

/**
 * Get the oEmbed cache
 *
 * @package Core
 * @since 	2.0
 */
function bpe_embed_event_cache( $id, $cachekey )
{
	return bpe_get_eventmeta( $id, $cachekey );
}

/**
 * Save the oEmbed cache
 *
 * @package Core
 * @since 	2.0
 */
function bpe_embed_event_save_cache( $cache, $cachekey, $id )
{
	bpe_update_eventmeta( $id, $cachekey, $cache );
}
?>