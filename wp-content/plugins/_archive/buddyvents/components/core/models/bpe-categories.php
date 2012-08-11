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

class Buddyvents_Categories
{
	public $id;
	public $name;
	public $slug;
	
	/**
	 * PHP5 Constructor
	 * @since 1.1
	 */
	public function __construct( $id = null )
	{
		if( $id )
		{
			$this->id = $id;
			$this->populate();
		}
	}

	/**
	 * Get a row from the database
	 * @since 1.1
	 */
	public function populate()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->categories} WHERE id = %d", $this->id ) );
		
		if( empty( $table ) )
			return false;

		$this->name	= $table->name;
		$this->slug	= $table->slug;
	}

	/**
	 * Save or uptimestamp a row
	 * @since 1.1
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->name	= apply_filters( 'bpe_events_before_save_categories_name', $this->name, $this->id );
		$this->slug	= apply_filters( 'bpe_events_before_save_categories_slug', $this->slug, $this->id );
		
		/* Call a before save action here */
		do_action( 'bpe_events_categories_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->categories} SET
													name = %s,
													slug = %s
											WHERE id = %d",
													$this->name,
													$this->slug,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->categories} (
													name,
													slug
											) VALUES ( 
													%s, %s
											)",
													$this->name,
													$this->slug ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'bpe_events_categories_after_save', $this ); 
		
		return $this->id;
	}
	
	/**
	 * Delete a row
	 * @since 1.1
	 */
	public function delete()
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->categories} WHERE id = %d", $this->id ) );
	}

	/**
	 * Get all categories
	 * @since 1.1
	 */
	static function get_event_categories( $count = false )
	{
		global $wpdb, $bpe;
		
		$cats =  $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bpe->tables->categories} ORDER BY name ASC" ) );

		if( $count )
		{
			$events = $wpdb->get_col( $wpdb->prepare( "SELECT category FROM {$bpe->tables->events}" ) );
			
			foreach( $cats as $k => $cat )
			{
				$category[$k]->id = $cat->id;
				$category[$k]->name = $cat->name;
				$category[$k]->slug = $cat->slug;
				
				$amount = 0;
				foreach( $events as $v )
					if( $v == $cat->id )
						$amount++;
					
				$category[$k]->amount = $amount;
			}
		}
		else
			$category = $cats;
		
		return $category;
	}
	
	/**
	 * Get id from the slug
	 * @since 1.1
	 */
	static function get_id_from_slug( $slug )
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bpe->tables->categories} WHERE slug = %s", $slug ) );
	}

	/**
	 * Get name from the slug
	 * @since 1.1
	 */
	static function get_name_from_slug( $slug )
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$bpe->tables->categories} WHERE slug = %s", $slug ) );
	}
}
?>