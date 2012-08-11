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

class Buddyvents_Cubepoints
{
	/**
	 * Start your engines...
	 * 
	 * @package Cubepoints
	 * @since 	2.1.1
	 * @uses 	add_action()
	 */
	public static function init()
	{
		add_action( 'cp_logs_description', 				array( __CLASS__, 'add_description' ), 10, 3 );
		add_action( 'bpe_maybe_attend_event', 			array( __CLASS__, 'maybe_attend_action' ) );
		add_action( 'bpe_not_attending_event_general', 	array( __CLASS__, 'not_attending_event_action' ), 10, 2 );
		add_action( 'bpe_attend_event', 				array( __CLASS__, 'attend_event_action' ), 10, 2 );
		add_action( 'bpe_delete_event_action', 			array( __CLASS__, 'delete_event_action' ) );
		add_action( 'bpe_saved_new_event', 				array( __CLASS__, 'new_event_action' ) );
		add_action( 'bpe_approved_created_event', 		array( __CLASS__, 'new_event_action' ) );
	}
	
	/**
	 * Points awarded for creating an event
	 * 
	 * Attached to both bpe_saved_new_event and bpe_approved_created_event
	 * 
	 * @package Cubepoints
	 * @since 	1.7
	 * 
	 * @param 	object	$event	Buddyvents event settings
	 * 
	 * @uses 	cp_alterPoints()
	 * @uses 	cp_log()
	 * @uses 	bpe_get_event_user_id()
	 * @uses 	bpe_get_option()
	 * @uses 	bpe_get_event_user_id()
	 * @uses 	bpe_get_event_id()
	 */
	public static function new_event_action( $event )
	{
		if( ! function_exists( 'cp_alterPoints' ) )
			return false;
	
		cp_alterPoints( bpe_get_event_user_id( $event ), bpe_get_option( 'cp_create_event' ) );
		cp_log( 'Created an event', bpe_get_event_user_id( $event ), bpe_get_option( 'cp_create_event' ), bpe_get_event_id( $event ) );
	}
	
	/**
	 * Points deducted for deleting an event
	 * 
	 * Attached to the <code>bpe_delete_event_action</code> action hook
	 * 
	 * @package Cubepoints
	 * @since 	1.7
	 * 
	 * @param 	object	$event Buddyvents event settings
	 * 
	 * @uses 	cp_alterPoints()
	 * @uses 	cp_log()
	 * @uses 	bpe_get_event_user_id()
	 * @uses 	bpe_get_option()
	 * @uses 	bpe_get_event_user_id()
	 * @uses 	bpe_get_event_id()
	 */
	public static function delete_event_action( $event )
	{
		if( ! function_exists( 'cp_alterPoints' ) )
			return false;
	
		cp_alterPoints( bpe_get_event_user_id( $event ), bpe_get_option( 'cp_delete_event' ) );
		cp_log( 'Deleted an event', bpe_get_event_user_id( $event ), bpe_get_option( 'cp_delete_event' ), bpe_get_event_id( $event ) );
	}
	
	/**
	 * Points awarded for attending an event
	 * 
	 * Attached to the <code>bpe_attend_event</code> action hook
	 *
	 * @package Cubepoints
	 * @since 	1.7
	 * 
	 * @param 	object 	$event		Buddyvents event settings
	 * @param 	int 	$user_id 	The current user
	 * 
	 * @uses 	cp_alterPoints()
	 * @uses 	cp_log()
	 * @uses 	bpe_get_event_user_id()
	 * @uses 	bpe_get_option()
	 * @uses 	bpe_get_event_user_id()
	 * @uses 	bpe_get_event_id()
	 */
	public static function attend_event_action( $event, $user_id )
	{
		if( ! function_exists( 'cp_alterPoints' ) )
			return false;
	
		cp_alterPoints( $user_id, bpe_get_option( 'cp_attend_event' ) );
		cp_log( 'Attends an event', $user_id, bpe_get_option( 'cp_attend_event' ), bpe_get_event_id( $event ) );
	}
	
	/**
	 * Points deducted for not attending an event anymore
	 * 
	 * Attached to the <code>bpe_not_attending_event_general</code> action hook
	 * 
	 * @package Cubepoints
	 * @since 	1.7
	 * 
	 * @param 	object	$event 		Buddyvents event settings
	 * @param 	int		$user_id 	The current user
	 * 
	 * @uses 	cp_alterPoints()
	 * @uses 	cp_log()
	 * @uses 	bpe_get_event_user_id()
	 * @uses 	bpe_get_option()
	 * @uses 	bpe_get_event_user_id()
	 * @uses 	bpe_get_event_id()
	 */
	public static function not_attending_event_action( $event, $user_id )
	{
		if( ! function_exists( 'cp_alterPoints' ) )
			return false;
	
		if( $event->attending_status[$user_id] == 1 )
		{
			$points = bpe_get_option( 'cp_remove_event' );
			$type = 'Does not attend an event anymore';
		}		
		elseif( $event->attending_status[$user_id] == 2 )
		{
			$points = bpe_get_option( 'cp_maybe_remove_event' );
			$type = 'Does not attend an event anymore (maybe)';
		}
	
		if( ! $points )
			return false;
	
		cp_alterPoints( $user_id, $points );
		cp_log( $type, $user_id, $points, bpe_get_event_id( $event ) );
	}
	
	/**
	 * Points awarded for maybe attending an event
	 * 
	 * Attached to the <code>bpe_maybe_attend_event</code> action hook
	 * 
	 * @package Cubepoints
	 * @since 	1.7
	 * 
	 * @param 	object 	$event		Buddyvents event settings
	 * @param 	int 	$user_id 	The current user
	 * 
	 * @uses 	cp_alterPoints()
	 * @uses 	cp_log()
	 * @uses 	bpe_get_event_user_id()
	 * @uses 	bpe_get_option()
	 * @uses 	bpe_get_event_user_id()
	 * @uses 	bpe_get_event_id()
	 */
	public static function maybe_attend_action( $event, $user_id )
	{
		if( ! function_exists( 'cp_alterPoints' ) )
			return false;
		
		cp_alterPoints( $user_id, bpe_get_option( 'cp_maybe_attend_event' ) );
		cp_log( 'Might attend an event', $user_id, bpe_get_option( 'cp_maybe_attend_event' ), bpe_get_event_id( $event ) );
	}
	
	/**
	 * Add a description on points overview pages
	 * 
	 * Attached to the <code>cp_logs_description</code> action hook
	 * 
	 * TODO: Ask plugin author why this is attached to an action rather than a filter...
	 * 
	 * @package Cubepoints
	 * @since 	2.0.6
	 * 
	 * @param 	string 	$type	The type of points given
	 * @param 	int 	$uid 	The current user
	 * @param	string	$points	The amount of points given/deducted
	 */
	public static function add_description( $type, $uid, $points )
	{
		echo $type;
	}
}
Buddyvents_Cubepoints::init();
?>