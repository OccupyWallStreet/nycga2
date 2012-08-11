<?php
/**
 * @package WordPress
 * @subpackage BuddyPress
 * @author Boris Glumpler
 * @copyright 2010, ShabuShabu Webdesign
 * @link http://shabushabu.eu
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL License
 
 rsvp = 0 = not attending
 rsvp = 1 = attending
 rsvp = 2 = maybe
 rsvp = 3 = banned (not used yet)
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

class Buddyvents_Members
{
	public $id;
	public $event_id;
	public $user_id;
	public $rsvp;
	public $rsvp_date;
	public $role;
	
	/**
	 * PHP5 Constructor
	 * @since 1.0
	 */
	public function __construct( $id = null )
	{
		global $bpe, $wpdb;

		if( $id )
		{
			$this->id = $id;
			$this->populate();
		}
	}

	/**
	 * Get a row from the database
	 * @since 1.0
	 */
	public function populate()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->members} WHERE id = %d", $this->id ) );

		$this->event_id		= $table->event_id;
		$this->user_id		= $table->user_id;
		$this->rsvp	 		= $table->rsvp;
		$this->rsvp_date	= $table->rsvp_date;
		$this->role			= $table->role;
	}

	/**
	 * Save or uptimestamp a row
	 * @since 1.0
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->event_id		= apply_filters( 'bpe_events_before_save_members_event_id',  $this->event_id, 	$this->id );
		$this->user_id		= apply_filters( 'bpe_events_before_save_members_user_id',   $this->user_id,  	$this->id );
		$this->rsvp	 		= apply_filters( 'bpe_events_before_save_members_rsvp', 	 $this->rsvp,		$this->id );
		$this->rsvp_date	= apply_filters( 'bpe_events_before_save_members_rsvp_date', $this->rsvp_date, 	$this->id );
		$this->role			= apply_filters( 'bpe_events_before_save_members_role', 	 $this->role, 		$this->id );
		
		/* Call a before save action here */
		do_action( 'bpe_events_members_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->members} SET
													event_id = %d,
													user_id = %d,
													rsvp = %d,
													rsvp_date = %s,
													role = %s
											WHERE id = %d",
													$this->event_id,
													$this->user_id,
													$this->rsvp,
													$this->rsvp_date,
													$this->role,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->members} (
													event_id,
													user_id,
													rsvp,
													rsvp_date,
													role
											) VALUES ( 
													%d, %d, %d, %s, %s
											)",
													$this->event_id,
													$this->user_id,
													$this->rsvp,
													$this->rsvp_date,
													$this->role ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'bpe_events_members_after_save', $this ); 
		
		return $this->id;
	}
	
	/**
	 * Delete a row
	 * @since 1.0
	 */
	public function delete()
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->members} WHERE id = %d", $this->id ) );
	}

	/**
	 * Delete everything for a member
	 * @since 1.0
	 */
	static function delete_for_user( $user_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->members} WHERE user_id = %d", $user_id ) );
	}
	
	/**
	 * Delete everything for an event
	 * @since 1.0
	 */
	static function delete_members_for_event( $event_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->members} WHERE event_id = %d", $event_id ) );
	}
	
	/**
	 * Get all events for a user (maybe/attending)
	 * @since 1.0
	 */
	static function get_user_events( $user_id = false, $count = false )
	{
		global $wpdb, $bpe, $bp;
		
		if( ! $user_id )
			$user_id = bp_displayed_user_id();
		
		$events = $wpdb->get_results( $wpdb->prepare( "SELECT m.*, e.user_id as e_user FROM {$bpe->tables->members} m LEFT JOIN {$bpe->tables->events} e ON m.event_id = e.id WHERE m.user_id = %d AND m.rsvp != 0", $user_id ) );
			
		$ids = array();
		foreach( $events as $k => $event )
		{
			if( bpe_get_event_user_id( $event ) != $event->e_user )
				$ids[] = $event->event_id;
		}

		return ( $count === true ) ? count( $ids ) : $wpdb->escape( join( ',', (array)$ids ) );
	}
	
	/**
	 * Check if a user is a member already
	 * @since 1.0
	 */
	static function is_user_member_already( $event_id, $user_id = false )
	{
		global $wpdb, $bpe, $bp;
		
		if( ! $user_id )
			$user_id = bp_loggedin_user_id();
		
		$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->members} WHERE user_id = %d AND rsvp != 0 AND event_id = %d", $user_id, $event_id ) );
		
		if( $id )
			return $id;
			
		return false;
	}

	/**
	 * Check if a user has refused before
	 * @since 1.0
	 */
	static function was_user_member( $event_id, $user_id = false )
	{
		global $wpdb, $bpe, $bp;

		if( ! $user_id )
			$user_id = bp_loggedin_user_id();
		
		$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->members} WHERE user_id = %d AND rsvp = 0 AND event_id = %d", $user_id, $event_id ) );
		
		if( $id )
			return $id;
			
		return null;
	}

	/**
	 * Get user_ids for all attendees
	 * @since 1.3
	 */
	static function get_attendee_ids( $event_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_col( $wpdb->prepare( "SELECT m.user_id FROM {$bpe->tables->members} m RIGHT JOIN {$bpe->tables->notifications} n ON n.user_id = m.user_id AND n.remind = 1 WHERE m.rsvp != 0 AND m.event_id = %d", $event_id ) );
	}
	
	/**
	 * Set rsvp for a member
	 * @since 1.3
	 */
	static function set_rsvp_for_user( $rsvp, $user_id, $event_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->members} SET rsvp = %d WHERE user_id = %d AND event_id = %d", $rsvp, $user_id, $event_id ) );
	}

	/**
	 * Remove user from event
	 * @since 1.3
	 */
	static function remove_user_from_event( $user_id, $event_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->members} WHERE user_id = %d AND event_id = %d", $user_id, $event_id ) );
	}

	/**
	 * Update the role of an event member
	 * @since 1.7
	 */
	static function set_event_member_role( $user_id, $event_id, $role )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->members} SET role = %s WHERE user_id = %d AND event_id = %d", $role, $user_id, $event_id ) );
	}
}
?>