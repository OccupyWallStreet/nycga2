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

class Buddyvents_Notifications
{
	public $id;
	public $user_id;
	public $keywords;
	public $email;
	public $screen;
	public $remind;
	
	/**
	 * PHP5 Constructor
	 * @since 1.2.5
	 */
	public function __construct( $id = null, $user_id = null )
	{
		global $bpe, $wpdb;

		if( $id )
		{
			$this->id = $id;
			$this->populate();
		}
		elseif( $user_id )
		{
			$this->user_id = $user_id;
			$this->populate_by_user();
		}
	}

	/**
	 * Get a row from the database
	 * @since 1.2.5
	 */
	public function populate()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->notifications} WHERE id = %d", $this->id ) );

		$this->user_id	= $table->user_id;
		$this->keywords	= $table->keywords;
		$this->email	= $table->email;
		$this->screen	= $table->screen;
		$this->remind	= $table->remind;
	}

	/**
	 * Get a row from the database
	 * @since 1.2.5
	 */
	public function populate_by_user()
	{
		global $bpe, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->notifications} WHERE user_id = %d", $this->user_id ) );
		
		if( ! isset( $table ) )
			return false;

		$this->id		= $table->id;
		$this->keywords	= $table->keywords;
		$this->email	= $table->email;
		$this->screen	= $table->screen;
		$this->remind	= $table->remind;
	}

	/**
	 * Save or uptimestamp a row
	 * @since 1.2.5
	 */
	public function save()
	{
		global $wpdb, $bpe;
		
		$this->user_id	= apply_filters( 'bpe_before_save_notifications_user_id', $this->user_id, $this->id );
		$this->keywords	= apply_filters( 'bpe_before_save_notifications_keywords', $this->keywords, $this->id );
		$this->email	= apply_filters( 'bpe_before_save_notifications_email', $this->email, $this->id );
		$this->screen	= apply_filters( 'bpe_before_save_notifications_screen', $this->screen, $this->id );
		$this->remind	= apply_filters( 'bpe_before_save_notifications_remind', $this->remind, $this->id );
		
		/* Call a before save action here */
		do_action( 'bpe_notifications_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->notifications} SET
													user_id = %d,
													keywords = %s,
													email = %d,
													screen = %d,
													remind = %d
											WHERE id = %d",
													$this->user_id,
													$this->keywords,
													$this->email,
													$this->screen,
													$this->remind,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$bpe->tables->notifications} (
													user_id,
													keywords,
													email,
													screen,
													remind
											) VALUES ( 
													%d, %s, %d, %d, %d
											)",
													$this->user_id,
													$this->keywords,
													$this->email,
													$this->screen,
													$this->remind ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'bpe_notifications_after_save', $this ); 
		
		return $this->id;
	}
	
	/**
	 * Delete a row
	 * @since 1.2.5
	 */
	public function delete()
	{
		global $wpdb, $bpe;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bpe->tables->notifications} WHERE id = %d", $this->id ) );
	}
	
	/**
	 * Return user_ids for a keyword search
	 * Strips the event creator out
	 * @since 1.2.5
	 */
	static function get_uids_for_keywords( $string, $uid )
	{
		global $wpdb, $bpe;
		
		$string = like_escape( $wpdb->escape( bpe_sanitize_for_keywords( $string ) ) );

		$ids = $wpdb->get_results( $wpdb->prepare( "SELECT user_id, screen, email FROM {$bpe->tables->notifications} WHERE MATCH(keywords) AGAINST('{$string}' IN BOOLEAN MODE)" ) );
		
		$email = $screen = array();
		foreach( (array)$ids as $user )
		{
			if( $user->email == 1 )
				$email[] = $user->user_id;
			
			if( $user->screen == 1 )
				$screen[] = $user->user_id;
		}
		
		if ( is_numeric( $e_key = array_search( $uid, (array)$email ) ) )
			unset( $email[$e_key] );

		if ( is_numeric( $s_key = array_search( $uid, (array)$screen ) ) )
			unset( $screen[$s_key] );
		
		return array( 'email' => array_unique( (array) $email ), 'screen' => array_unique( (array)$screen ) );
	}
}
?>