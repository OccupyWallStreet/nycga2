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
 * Add an api key
 * 
 * @package API
 * @since 	1.5
 */
function bpe_add_apikey( $id = null, $user_id, $api_key, $active, $date_generated, $hits, $hit_date, $hits_over )
{
	$api = new Buddyvents_API( $id );
	
	$api->user_id 			= $user_id;
	$api->api_key 			= $api_key;
	$api->active 			= $active;
	$api->date_generated 	= $date_generated;
	$api->hits 				= $hits;
	$api->hit_date 			= $hit_date;
	$api->hits_over			= $hits_over;
		
	if( $new_id = $api->save() )
		return $new_id;
		
	return false;
}

/**
 * Add a webhook
 * 
 * @package API
 * @since 	1.7
 */
function bpe_add_webhook( $id = null, $user_id, $event, $url, $verifier, $verified )
{
	$webhook = new Buddyvents_Webhooks( $id );
	
	$webhook->user_id 	= $user_id;
	$webhook->event 	= $event;
	$webhook->url 		= $url;
	$webhook->verifier 	= $verifier;
	$webhook->verified 	= $verified;
	
	if( $new_id = $webhook->save() )
		return $new_id;
		
	return false;
}

/**
 * Check a verifier
 * 
 * @package API
 * @since 	1.7
 */
function bpe_check_verifier( $key )
{
	return Buddyvents_Webhooks::check_verifier( $key );
}

/**
 * Change the status of a webhook
 * 
 * @package API
 * @since 	1.7
 */
function bpe_unverify_webhook( $id )
{
	return Buddyvents_Webhooks::unverify_webhook( $id );
}

/**
 * Change the status of webhooks
 * 
 * @package API
 * @since 	2.0
 */
function bpe_bulk_unverify_webhooks( $ids )
{
	return Buddyvents_Webhooks::bulk_unverify_webhooks( $ids );
}

/**
 * Change the status of webhooks
 * 
 * @package API
 * @since 	2.0
 */
function bpe_bulk_verify_webhooks( $ids )
{
	return Buddyvents_Webhooks::bulk_verify_webhooks( $ids );
}

/**
 * Change the status of webhooks
 * 
 * @package API
 * @since 	2.0
 */
function bpe_bulk_delete_webhooks( $ids )
{
	return Buddyvents_Webhooks::bulk_delete_webhooks( $ids );
}

/**
 * Check for an existing hook
 * 
 * @package API
 * @since 	1.7
 */
function bpe_api_check_existing_hook( $event, $url )
{
	return Buddyvents_Webhooks::check_existing_hook( $event, $url );
}

/**
 * Verify webhooks
 * 
 * @package API
 * @since 	1.7
 */
function bpe_verify_webhooks( $verifier )
{
	return Buddyvents_Webhooks::verify_webhooks( $verifier );
}

/**
 * Get webhooks
 * 
 * @package API
 * @since 	1.7
 */
function bpe_get_hook_for_user( $user_id, $event )
{
	return Buddyvents_Webhooks::get_hook_for_user( $user_id, $event );
}

/**
 * Get all webhooks for a user
 * 
 * @package API
 * @since 	1.7
 */
function bpe_get_all_webhooks_for_user()
{
	return Buddyvents_Webhooks::get_all_webhooks_for_user();
}

/**
 * Check an api key
 * 
 * @package API
 * @since 	1.5
 */
function bpe_check_apikey( $key, $check = false )
{
	return Buddyvents_API::check_apikey( $key, $check );
}

/**
 * Get all for a user
 * 
 * @package API
 * @since 	1.5.1
 */
function bpe_get_all_for_user( $user_id )
{
	return Buddyvents_API::get_all_for_user( $user_id );
}

/**
 * Get all for a user
 * 
 * @package API
 * @since 	1.5.1
 */
function bpe_set_api_access( $id, $type = 1 )
{
	return Buddyvents_API::set_api_access( $id, $type );
}

/**
 * Reset the time
 * 
 * @package API
 * @since 	1.5.1
 */
function bpe_reset_api_time_hits( $id )
{
	return Buddyvents_API::reset_api_time_hits( $id );
}

/**
 * Reset the time
 * 
 * @package API
 * @since 	1.5.1
 */
function bpe_incriment_api_hits( $id )
{
	return Buddyvents_API::incriment_api_hits( $id );
}

/**
 * Reset the time
 * 
 * @package API
 * @since 	2.0
 */
function bpe_reset_hits_over( $id )
{
	return Buddyvents_API::reset_hits_over( $id );
}

/**
 * Reset the time
 * 
 * @package API
 * @since 	2.0
 */
function bpe_incriment_hits_over( $id )
{
	return Buddyvents_API::incriment_hits_over( $id );
}
?>