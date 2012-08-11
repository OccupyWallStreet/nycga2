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

class Buddyvents_Webhooks
{
	public $id;
	public $user_id;
	public $event;
	public $url;
	public $verifier;
	public $verified;
	
	/**
	 * PHP5 Constructor
	 * @since 1.5
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
	 * @since 1.5
	 */
	public function populate()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->webhooks} WHERE id = %d", $this->id ) );

		$this->user_id	= $table->user_id;
		$this->event	= $table->event;
		$this->url		= $table->url;
		$this->verifier	= $table->verifier;
		$this->verified	= $table->verified;
	}

	/**
	 * Save or uptimestamp a row
	 * @since 1.5
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->user_id 	= apply_filters( 'bpe_events_before_save_webhooks_user_id', $this->user_id, $this->id );
		$this->event	= apply_filters( 'bpe_events_before_save_webhooks_event', $this->event, $this->id );
		$this->url		= apply_filters( 'bpe_events_before_save_webhooks_url', $this->url, $this->id );
		$this->verifier	= apply_filters( 'bpe_events_before_save_webhooks_verifier', $this->verifier, $this->id );
		$this->verified	= apply_filters( 'bpe_events_before_save_webhooks_verified', $this->verified, $this->id );
		
		/* Call a before save action here */
		do_action( 'bpe_events_webhooks_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->webhooks} SET
													user_id = %d,
													event = %s,
													url = %s,
													verifier = %s,
													verified = %d
											WHERE id = %d",
													$this->user_id,
													$this->event,
													$this->url,
													$this->verifier,
													$this->verified,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->webhooks} (
													user_id,
													event,
													url,
													verifier,
													verified
											) VALUES ( 
													%d, %s, %s, %s, %d
											)",
													$this->user_id,
													$this->event,
													$this->url,
													$this->verifier,
													$this->verified ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'bpe_events_webhooks_after_save', $this ); 
		
		return $this->id;
	}
	
	/**
	 * Delete a row
	 * @since 1.5
	 */
	public function delete()
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->webhooks} WHERE id = %d", $this->id ) );
	}

	/**
	 * Delete for a user
	 * @since 2.0
	 */
	static function delete_by_user( $user_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->webhooks} WHERE user_id = %d", $user_id ) );
	}
	
	/**
	 * Delete a row
	 * @since 1.5
	 */
	static function get_hook_for_user( $user_id, $event )
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_row( $wpdb->prepare( "SELECT event, url FROM {$bpe->tables->webhooks} WHERE user_id = %d AND verified = 1 AND event = %s", $user_id, $event ) );
	}
	
	/**
	 * Delete a row
	 * @since 1.7
	 */
	static function check_verifier( $key )
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->webhooks} WHERE verifier = %s", $key ) );
	}
	
	/**
	 * Change a webhook status
	 * @since 1.7
	 */
	static function unverify_webhook( $id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->webhooks} SET verified = 0 WHERE id = %d", $id ) );
	}

	/**
	 * Bulk unverify webhooks
	 * @since 2.0
	 */
	static function bulk_unverify_webhooks( $ids )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->webhooks} SET verified = 0 WHERE id IN ({$ids})" ) );
	}

	/**
	 * Bulk verify webhooks
	 * @since 2.0
	 */
	static function bulk_verify_webhooks( $ids )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->webhooks} SET verified = 1 WHERE id IN ({$ids})" ) );
	}

	/**
	 * Bulk delete webhooks
	 * @since 2.0
	 */
	static function bulk_delete_webhooks( $ids )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->webhooks} WHERE id IN ({$ids})" ) );
	}
	
	/**
	 * Get all webhooks for a user
	 * @since 1.7
	 */
	static function get_all_webhooks_for_user()
	{
		global $wpdb, $bpe, $bp;
		
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bpe->tables->webhooks} WHERE user_id = %d", bp_loggedin_user_id() ) );
	}
	
	/**
	 * Check for existing hook
	 * @since 1.7
	 */
	static function check_existing_hook( $event, $url )
	{
		global $wpdb, $bpe, $bp;
		
		$event = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->webhooks} WHERE event = %s AND url = %s AND user_id = %d", $event, $url, bp_loggedin_user_id() ) );

		if( $event )
			return true;
		
		return false;
	}
	
	/**
	 * Verify
	 * @since 1.7
	 */
	static function verify_webhooks( $verifier )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->webhooks} SET verified = 1 WHERE verifier = %s", $verifier ) );
	}
}
?>