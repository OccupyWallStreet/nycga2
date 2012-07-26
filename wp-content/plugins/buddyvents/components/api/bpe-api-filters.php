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

add_filter( 'bpe_before_save_api_user_id', 				'wp_filter_kses', 1 );
add_filter( 'bpe_before_save_api_api_key', 				'wp_filter_kses', 1 );
add_filter( 'bpe_before_save_api_active', 				'wp_filter_kses', 1 );
add_filter( 'bpe_before_save_api_hits', 				'wp_filter_kses', 1 );
add_filter( 'bpe_before_save_api_hit_date', 			'wp_filter_kses', 1 );
add_filter( 'bpe_before_save_api_hits_over', 			'wp_filter_kses', 1 );

add_filter( 'bpe_events_before_save_webhooks_user_id', 	'wp_filter_kses', 1 );
add_filter( 'bpe_events_before_save_webhooks_event', 	'wp_filter_kses', 1 );
add_filter( 'bpe_events_before_save_webhooks_url', 		'wp_filter_kses', 1 );
add_filter( 'bpe_events_before_save_webhooks_verifier', 'wp_filter_kses', 1 );
add_filter( 'bpe_events_before_save_webhooks_verified', 'wp_filter_kses', 1 );
?>