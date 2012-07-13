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

abstract class Buddyvents_NL_Services
{
	/**
	 * The name of the newsletter service
	 * Has to be set in a child class
	 */
	protected $service;
	
	public function __construct()
	{
		if( ! bpe_get_option( 'enable_'. $this->service ) )
			return false;

		add_action( 'bpe_newsletter_service_create_screen', 	 			  array( &$this, 'create_screen'  ) );
		add_action( 'bpe_newsletter_service_edit_screen', 		 			  array( &$this, 'edit_screen'	  ) );
		add_action( 'bpe_newsletter_service_signup_form',		 			  array( &$this, 'signup_form'	  ) );
		add_action( 'bpe_newsletter_service_edit_screen_save',   			  array( &$this, 'save_data' 	  ) );
		add_action( 'bpe_newsletter_service_create_screen_save', 			  array( &$this, 'save_data'	  ) );
		add_action( 'wp_ajax_bpe_'. $this->service .'_new_subscriber', 	   	  array( &$this, 'new_subscriber' ) );
		add_action( 'wp_ajax_nopriv_bpe_'. $this->service .'_new_subscriber', array( &$this, 'new_subscriber' ) );
		add_action( 'wp_ajax_bpe_'. $this->service .'_get_lists', 			  array( &$this, 'get_lists' 	  ) );
	}

	/**
	 * Handles the actual saving of data
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	public function save_data()
	{
		if( ! empty( $_POST[$this->service .'_api_key'] ) && ! empty( $_POST[$this->service .'_list_id'] ) && $_POST['show_service'] == $this->service ) :
			bpe_update_eventmeta( bpe_get_displayed_event( 'id' ), $this->service .'_api_key', $_POST[$this->service .'_api_key'] );
			bpe_update_eventmeta( bpe_get_displayed_event( 'id' ), $this->service .'_list_id', $_POST[$this->service .'_list_id'] );
			bpe_update_eventmeta( bpe_get_displayed_event( 'id' ), 'newsletter_service', $this->service );

			bp_update_user_meta( bp_loggedin_user_id(), $this->service .'_api_key', $_POST[$this->service .'_api_key'] );
		else :
			bpe_delete_eventmeta( bpe_get_displayed_event( 'id' ), $this->service .'_api_key' );
			bpe_delete_eventmeta( bpe_get_displayed_event( 'id' ), $this->service .'_list_id' );
		endif;
	}


	/**
	 * The newsletter create screen
	 * 
	 * @package	Newsletter
	 * @since 	2.1
 	 */
	public function create_screen()
	{
		$this->create_edit_form( 'create' );
	}

	/**
	 * The newsletter edit screen
	 * 
	 * @package	Newsletter
	 * @since 	2.1
 	 */
	public function edit_screen()
	{
		$this->create_edit_form( 'edit' );
	}
	
	/**
	 * The edit or create form
	 * 
	 * Has to be overridden in a child class
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	abstract protected function create_edit_form( $context );

	/**
	 * HTML of the signup form
	 * 
	 * Has to be overridden in a child class
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	abstract function signup_form();
	
	/**
	 * Add a new subscriber
	 * 
	 * Has to be overridden in a child class
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	abstract function new_subscriber();
	
	/**
	 * Get the lists
	 * 
	 * Has to be overridden in a child class
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	abstract function get_lists();
}
?>