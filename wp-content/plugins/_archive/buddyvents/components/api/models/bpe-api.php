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

class Buddyvents_API
{
	public $id;
	public $user_id;
	public $api_key;
	public $active;
	public $date_generated;
	public $hits;
	public $hit_date;
	public $hits_over;
	
	/**
	 * PHP5 Constructor
	 * @since 1.5
	 */
	public function __construct( $id = null, $apikey = null, $username = null )
	{
		global $bpe, $wpdb;

		if( $id )
		{
			$this->id = $id;
			$this->populate();
		}
		elseif( $apikey )
		{
			$this->api_key = $apikey;
			$this->populate_by_key();
		}
		elseif( $username )
		{
			$this->populate_by_username( $username );
		}
	}

	/**
	 * Get a row from the database
	 * @since 1.5
	 */
	public function populate()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->api} WHERE id = %d", $this->id ) );

		$this->user_id			= $table->user_id;
		$this->api_key			= $table->api_key;
		$this->active			= $table->active;
		$this->date_generated	= $table->date_generated;
		$this->hits 			= $table->hits;
		$this->hit_date			= $table->hit_date;
		$this->hits_over		= $table->hits_over;
	}

	/**
	 * Get a row from the database
	 * @since 1.5
	 */
	public function populate_by_key()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->api} WHERE api_key = %s", $this->api_key ) );

		$this->id				= $table->id;
		$this->user_id			= $table->user_id;
		$this->active			= $table->active;
		$this->date_generated	= $table->date_generated;
		$this->hits 			= $table->hits;
		$this->hit_date			= $table->hit_date;
		$this->hits_over		= $table->hits_over;
	}
	
	/**
	 * Get a row from the database
	 * @since 1.5
	 */
	public function populate_by_username( $username )
	{
		global $bpe, $wpdb;
		
		$user_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->users} WHERE user_login = %s", $username ) );
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->api} WHERE user_id = %d", $user_id ) );

		$this->id				= $table->id;
		$this->user_id			= $table->user_id;
		$this->api_key			= $table->api_key;
		$this->active			= $table->active;
		$this->date_generated	= $table->date_generated;
		$this->hits 			= $table->hits;
		$this->hit_date			= $table->hit_date;
		$this->hits_over		= $table->hits_over;
	}

	/**
	 * Save or uptimestamp a row
	 * @since 1.5
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->user_id			= apply_filters( 'bpe_before_save_api_user_id', $this->user_id, $this->id );
		$this->api_key			= apply_filters( 'bpe_before_save_api_api_key', $this->api_key, $this->id );
		$this->active			= apply_filters( 'bpe_before_save_api_active', $this->active, $this->id );
		$this->date_generated	= apply_filters( 'bpe_before_save_api_date_generated', $this->date_generated, $this->id );
		$this->hits				= apply_filters( 'bpe_before_save_api_hits', $this->hits, $this->id );
		$this->hit_date			= apply_filters( 'bpe_before_save_api_hit_date', $this->hit_date, $this->id );
		$this->hits_over		= apply_filters( 'bpe_before_save_api_hits_over', $this->hits_over, $this->id );
				
		/* Call a before save action here */
		do_action( 'bpe_api_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->api} SET
													user_id = %d,
													api_key = %s,
													active = %d,
													date_generated = %s,
													hits = %d,
													hit_date = %s,
													hits_over = %d
											WHERE id = %d",
													$this->user_id,
													$this->api_key,
													$this->active,
													$this->date_generated,
													$this->hits,
													$this->hit_date,
													$this->hits_over,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->api} (
													user_id,
													api_key,
													active,
													date_generated,
													hits,
													hit_date,
													hits_over
											) VALUES ( 
													%d, %s, %d, %s, %d, %s, %d
											)",
													$this->user_id,
													$this->api_key,
													$this->active,
													$this->date_generated,
													$this->hits,
													$this->hit_date,
													$this->hits_over ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'bpe_api_after_save', $this ); 
		
		return $this->id;
	}
	
	/**
	 * Delete a row
	 * @since 1.5
	 */
	public function delete()
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->api} WHERE id = %d", $this->id ) );
	}

	/**
	 * Delete for a user
	 * @since 2.0
	 */
	static function delete_by_user( $user_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->api} WHERE user_id = %d", $user_id ) );
	}
	
	/**
	 * Check an api key
	 * @since 1.5
	 */
	static function check_apikey( $key )
	{
		global $wpdb, $bpe;
		
		$user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$bpe->tables->api} WHERE api_key = %s AND active = 1", $key ) );
		
		if( $user_id )
			return $user_id;
			
		return false;
	}

	/**
	 * Get all for a user
	 * @since 1.5.1
	 */
	static function get_all_for_user( $user_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bpe->tables->api} WHERE user_id = %d", $user_id ) );
	}
	
	/**
	 * Set the API access
	 * @since 1.5.1
	 */
	static function set_api_access( $ids, $type = 1 )
	{
		global $wpdb, $bpe;
		
		$ids = $wpdb->escape( join( ',', (array)$ids ) );
		
		if( empty( $ids ) )
			$ids = 0;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->api} SET active = %d WHERE id IN({$ids})", $type ) );
	}
	
	/**
	 * Reset the api time
	 * @since 1.5.1
	 */
	static function reset_api_time_hits( $id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->api} SET hit_date = %s, hits = 1 WHERE id = %d", bp_core_current_time(), $id ) );
	}

	/**
	 * Incriment the hits by 1
	 * @since 1.5.1
	 */
	static function incriment_api_hits( $id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->api} SET hits = hits+1 WHERE id = %d", $id ) );
	}

	/**
	 * Reset hits_over
	 * @since 2.0
	 */
	static function reset_hits_over( $id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->api} SET hits_over = 0 WHERE id = %d", $id ) );
	}

	/**
	 * Incriment hits_over by 1
	 * @since 2.0
	 */
	static function incriment_hits_over( $id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->api} SET hits_over = hits_over+1 WHERE id = %d", $id ) );
	}
}
?>