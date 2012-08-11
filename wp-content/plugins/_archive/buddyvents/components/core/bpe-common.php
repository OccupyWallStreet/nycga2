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
 * Get base values
 *
 * @package	 Core
 * @since 	 2.0
 * 
 * @param	string	$value
 * @return	mixed
 */
function bpe_get_base( $value )
{
	global $bp;
	
	return ( isset( $bp->buddyvents->{$value} ) ) ? $bp->buddyvents->{$value} : false;
}

/**
 * Get an option
 *
 * @package	 Core
 * @since 	 2.0
 * 
 * @param	string	$value
 * @param	string	sub
 * @return	mixed
 */
function bpe_get_option( $value = '', $sub = false )
{
	global $bpe;
	
	$option = ( isset( $bpe->options->{$value} ) ) ? $bpe->options->{$value} : false;
	
	if( $sub && isset( $option[$sub] ) && is_array( $option ) )
		$option = $option[$sub];
	
	return apply_filters( 'bpe_get_option', $option, $value, $sub );
}

/**
 * Get the displayed event
 *
 * @package	 Core
 * @since 	 2.0
 * 
 * @param	string	$value
 * @return	mixed
 */
function bpe_get_displayed_event( $value = false )
{
	global $bpe;
	
	if( ! isset( $bpe->displayed_event ) )
		return false;
	
	return ( ! empty( $value ) && isset( $bpe->displayed_event->{$value} ) ) ? $bpe->displayed_event->{$value} : $bpe->displayed_event;
}

/**
 * Get the displayed event
 *
 * @package	 Core
 * @since 	 2.0
 * 
 * @param	string	$value
 * @return	mixed
 */
function bpe_get_displayed_event_meta( $value = false )
{
	global $bpe;
	
	if( ! isset( $bpe->displayed_event->meta->{$value} ) )
		return false;
	
	return $bpe->displayed_event->meta->{$value};
}

/**
 * Get a config option
 *
 * @package	 Core
 * @since 	 2.0
 * 
 * @param	string	$value
 * @param	string	sub
 * @return	mixed
 */
function bpe_get_config( $value = '', $sub = false )
{
	global $bpe;
	
	$config = ( isset( $bpe->config->{$value} ) ) ? $bpe->config->{$value} : false;
	
	if( $sub !== false && $config !== false && is_array( $config ) )
		$config = $config[$sub];
	
	return apply_filters( 'bpe_get_config', $config, $value, $sub );
}

/**
 * Get the plugin version
 *
 * @package	 Core
 * @since 	 2.1
 *
 * @return	string
 */
function bpe_get_version()
{
	return Buddyvents::VERSION;
}

/**
 * Get the plugin database version
 *
 * @package	 Core
 * @since 	 2.1
 *
 * @return	string
 */
function bpe_get_db_version()
{
	return Buddyvents::DBVERSION;
}

/**
 * Get the plugin status
 *
 * @package	 Core
 * @since 	 2.1
 *
 * @return	bool
 */
function bpe_is_buddyvents_active()
{
	return Buddyvents::$active;
}
?>