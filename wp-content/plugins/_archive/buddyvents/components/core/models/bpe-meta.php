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
 * Delete eventmeta
 * @since 1.0
 */
function bpe_delete_eventmeta( $event_id, $meta_key = false, $meta_value = false )
{
	global $wpdb, $bpe;

	if( ! is_numeric( $event_id ) )
		return false;

	$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );

	if( is_array( $meta_value ) || is_object( $meta_value ) )
		$meta_value = serialize( $meta_value );

	$meta_value = trim( $meta_value );

	if( ! $meta_key )
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->events_meta} WHERE event_id = %d", $event_id ) );
		
	elseif( $meta_value )
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->events_meta} WHERE event_id = %d AND meta_key = %s AND meta_value = %s", $event_id, $meta_key, $meta_value ) );

	else
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->events_meta} WHERE event_id = %d AND meta_key = %s", $event_id, $meta_key ) );

	/* Delete the cached object */
	wp_cache_delete( 'bpe_events_eventmeta_' . $event_id . '_' . $meta_key, 'bp' );

	return true;
}

/**
 * Get eventmeta
 * @since 1.0
 */
function bpe_get_eventmeta( $event_id, $meta_key = '' )
{
	global $wpdb, $bpe;

	$event_id = absint( $event_id );

	if( ! $event_id )
		return false;

	if( ! empty( $meta_key ) )
	{
		$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );

		if( ! $metas = wp_cache_get( 'bpe_events_eventmeta_' . $event_id . '_' . $meta_key, 'bp' ) )
		{
			$metas = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$bpe->tables->events_meta} WHERE event_id = %d AND meta_key = %s", $event_id, $meta_key ) );
			wp_cache_set( 'bpe_events_eventmeta_' . $event_id . '_' . $meta_key, $metas, 'bp' );
		}
	}
	else
	{
		$result = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM {$bpe->tables->events_meta} WHERE event_id = %d", $event_id ) );
		foreach( $result as $k => $v )
			$metas[$v->meta_key] = $v->meta_value;
	}

	if( empty( $metas ) )
	{
		if( empty( $meta_key ) )
			return array();
		else
			return '';
	}

	$metas = array_map( 'maybe_unserialize', (array)$metas );

	if( count( $metas ) == 1 )
		return $metas[0];
	else
		return $metas;
}

/**
 * Update eventmeta
 * @since 1.0
 */
function bpe_update_eventmeta( $event_id, $meta_key, $meta_value )
{
	global $wpdb, $bpe;

	if ( ! is_numeric( $event_id ) )
		return false;

	$meta_key = preg_replace( '|[^a-z0-9_]|i', '', $meta_key );

	if ( is_string( $meta_value ) )
		$meta_value = stripslashes( $wpdb->escape( $meta_value ) );

	$meta_value = maybe_serialize( $meta_value );

	if( empty( $meta_value ) )
		return bpe_delete_eventmeta( $event_id, $meta_key );

	if ( ! $cur = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->events_meta} WHERE event_id = %d AND meta_key = %s", $event_id, $meta_key ) ) )
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->events_meta} ( event_id, meta_key, meta_value ) VALUES ( %d, %s, %s )", $event_id, $meta_key, $meta_value ) );

	elseif ( $cur->meta_value != $meta_value )
		$wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->events_meta} SET meta_value = %s WHERE event_id = %d AND meta_key = %s", $meta_value, $event_id, $meta_key ) );

	else
		return false;

	/* Update the cached object and recache */
	wp_cache_set( 'bpe_events_eventmeta_' . $event_id . '_' . $meta_key, $meta_value, 'bp' );

	return true;
}
?>