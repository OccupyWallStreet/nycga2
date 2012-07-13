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

class Buddyvents_Moderation
{
	/**
 	 * Start the moderation process
	 * 
	 * @package Moderation
	 * @since 	1.2
	 * 
	 * @access 	public
	 * @uses 	bpModeration::register_content_type()
	 */
 	public static function bootstrap()
	{
		$callbacks = array(
			'info'   => array( __CLASS__, 'info' ),
			'init'   => array( __CLASS__, 'init' ),
			'edit'   => array( __CLASS__, 'edit' ),
			'delete' => array( __CLASS__, 'delete' )
		);

		$activity_types = apply_filters( 'bpe_moderation_activity_types', array(
			'new_event',
			'edited_event',
			'attend_event',
			'maybe_attend_event',
			'remove_from_event',
			'new_event_logo',
			'updated_event_schedule',
			'updated_event_document'
		) );

		return bpModeration::register_content_type( 'bpe_events', __( 'Events', 'events' ), $callbacks, $activity_types );
	}

	/**
 	 * Append the moderation link to the content
	 * 
	 * @package Moderation
	 * @since 	1.2
	 * 
	 * @access 	public
	 * @uses 	add_action()
	 */
	public static function init()
	{
		add_action( 'bpe_end_single_event_action', array( __CLASS__, 'append_link' ), 10, 2 );
	}

	/**
 	 * Displays the moderation link
	 * 
	 * @package Moderation
	 * @since 	1.2
	 * 
	 * @access 	public
	 * 
	 * @param 	int 	$event_id	The current event id
	 * @param 	int 	$user_id 	The current user
	 */
	public static function append_link( $event_id, $user_id )
	{
		$link = bpModFrontend::get_link( array(
			'type' => 'bpe_events',
			'id' => $event_id,
			'id2' => 0,
			'author_id' => $user_id,
			'unflagged_text' => __( 'Flag this event as inappropriate', 'events' )
		));

		echo '<p class="bpe-mod">'. $link .'</p>';
	}

	/**
	 * Get some information about an event
	 * 
	 * @package Moderation
	 * @since 	1.2
	 * 
	 * @param 	int 	$id 	Event id
	 * @param 	int 	$id2 	Not used
	 * 
	 * @access 	public
	 * @uses 	bpe_get_event_user_id()
	 * @uses 	bp_get_root_domain()
	 * @uses 	bpe_get_event_slug()
	 * @uses 	bpe_get_event_date_created()
	 */
	public static function info( $id, $id2 )
	{
		$event = new Buddyvents_Events( $id );
		
		return array(
			'author' => bpe_get_event_user_id( $event ),
			'url'    => bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_event_slug( $event ) .'/',
			'date'   => bpe_get_event_date_created( $event ),
		);
	}

	/**
	 * Link to edit an event
	 *
	 * @package Moderation
	 * @since 	1.2
	 * 
	 * @param 	int 	$id 	Event id
	 * @param 	int 	$id2 	Not used
	 * 
	 * @access 	public
	 * @uses 	bp_get_root_domain()
	 * @uses 	bpe_get_base()
	 * @uses 	bpe_get_event_slug()
	 */
	public static function edit( $id, $id2 )
	{
		$event = new Buddyvents_Events( $id );

		$url = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_event_slug( $event ) .'/edit/';

		return $url;
	}

	/**
	 * Delete an event via the Moderation backend
	 *
	 * @package Moderation
	 * @since 	1.2
	 * 
	 * @access 	public
	 * 
	 * @param 	int		$id 	Event id
	 * @param 	int 	$id2 	Not used
	 */
	public static function delete( $id, $id2 )
	{
		$event = new Buddyvents_Events( $id );
		
		do_action( 'bpe_delete_event_action', $event );
	}
}
Buddyvents_Moderation::bootstrap();
?>