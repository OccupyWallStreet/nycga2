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
 * Log an array, variable or object for debugging
 *
 * @package Core
 * @since 	1.7
 */
function bpe_log( $arr = false, $note = '', $component = 'general' )
{
	if( ! $arr )
		return false;
	
	$log  = sprintf( __( 'Component: %s', 'events' ), strtoupper( $component ) ) ."\r\n";

	if( ! empty( $note ) )
		$log .= sprintf( __( 'Note: %s', 'events' ), $note ) ."\r\n";

	$log .= sprintf( __( 'Date: %s', 'events' ), gmdate( 'Y-m-d H:i:s' ) ) ."\r\n";
	
	if( ! is_object( $arr ) && ! is_array( $arr ) )
	{
		$log .= "VAR = [\r\n";
		$log .= "\t". $arr ."\r\n";
		$log .= "]\r\n\r\n----------\r\n\r\n";
	}
	else
	{
		if( is_object( $arr ) )
			$arr = get_object_vars( $arr );
	
		$log .= "VARS = [\r\n";
		$log .= bpe_log_walker( $arr, 1 );
		$log .= "]\r\n\r\n----------\r\n\r\n";
	}

	file_put_contents( EVENT_ABSPATH . 'logs/debug.log', $log, FILE_APPEND );
}

/**
 * Traverse an array for logging purposes
 *
 * @package Core
 * @since 	2.0
 */
function bpe_log_walker( $arr, $tab )
{
	$tabs = '';
	for( $i = 1; $i <= $tab; $i++ )
		$tabs .= "\t";
	
	$log = '';	
	foreach( (array)$arr as $key => $value )
	{
		if( is_array( $value ) || is_object( $value ) )
		{
			if( is_object( $value ) )
				$value = get_object_vars( $value );
				
			$tab++;
			
			$log .= "$tabs'$key' = [\r\n";
			$log .= bpe_log_walker( $value, $tab );
			$log .= "$tabs]\r\n";
			
			$tab--;
		}
		else
			$log .= "$tabs'$key' = '$value'\r\n";
	}
	
	return $log;
}
?>