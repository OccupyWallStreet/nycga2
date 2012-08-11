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

class MAPO_Coords
{
	public $id;
	public $user_id;
	public $group_id;
	public $lat;
	public $lng;
	
	/**
	 * PHP5 Constructor
	 * @since 1.0
	 */
	public function __construct( $id = null, $user_id = null, $group_id = null )
	{
		global $mapo, $wpdb;

		if( $id )
		{
			$this->id = $id;
			$this->populate();
		}
		elseif( $user_id )
		{
			$this->user_id = $user_id;
			$this->populate_by_user_id();
		}
		elseif( $group_id )
		{
			$this->group_id = $group_id;
			$this->populate_by_group_id();
		}
	}

	/**
	 * Get a row from the database
	 * @since 1.0
	 */
	public function populate()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->coords} WHERE id = %d", $this->id ) );

		$this->user_id	= $table->user_id;
		$this->group_id	= $table->group_id;
		$this->lat	 	= $table->lat;
		$this->lng		= $table->lng;
	}

	/**
	 * Get a row from the database
	 * @since 1.0
	 */
	public function populate_by_user_id()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->coords} WHERE user_id = %d", $this->user_id ) );
		
		if( ! $table )
			return false;

		$this->id		= $table->id;
		$this->group_id	= $table->group_id;
		$this->lat		= $table->lat;
		$this->lng		= $table->lng;
	}

	/**
	 * Get a row from the database
	 * @since 1.0
	 */
	public function populate_by_group_id()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->coords} WHERE group_id = %d", $this->group_id ) );

		$this->id		= $table->id;
		$this->user_id	= $table->user_id;
		$this->lat		= $table->lat;
		$this->lng		= $table->lng;
	}

	/**
	 * Save or uptimestamp a row
	 * @since 1.0
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->user_id	= apply_filters( 'bpe_before_save_coords_user_id', $this->user_id, $this->id );
		$this->group_id	= apply_filters( 'bpe_before_save_coords_group_id', $this->group_id, $this->id );
		$this->lat	 	= apply_filters( 'bpe_before_save_coords_lat', $this->lat, $this->id );
		$this->lng		= apply_filters( 'bpe_before_save_coords_lng', $this->lng, $this->id );
		
		/* Call a before save action here */
		do_action( 'bpe_coords_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->coords} SET
													user_id = %d,
													group_id = %d,
													lat = %s,
													lng = %s
											WHERE id = %d",
													$this->user_id,
													$this->group_id,
													$this->lat,
													$this->lng,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->coords} (
													user_id,
													group_id,
													lat,
													lng
											) VALUES ( 
													%d, %d, %s, %s
											)",
													$this->user_id,
													$this->group_id,
													$this->lat,
													$this->lng ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'bpe_coords_after_save', $this ); 
		
		return $this->id;
	}
	
	/**
	 * Delete a row
	 * @since 1.0
	 */
	public function delete()
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->coords} WHERE id = %d", $this->id ) );
	}

	/**
	 * Get an id
	 * @since 1.0
	 */
	static function get_id_by_user( $user_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->coords} WHERE user_id = %d", $user_id ) );
	}

	/**
	 * Get an id
	 * @since 1.0
	 */
	static function get_id_by_group( $group_id )
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->coords} WHERE group_id = %d", $group_id ) );
	}
}
?>