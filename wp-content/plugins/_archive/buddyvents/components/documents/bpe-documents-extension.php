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

class Buddyvents_Documents_Extension extends Buddyvents_Extension
{
	/**
	 * PHP5 Constructor
	 * 
	 * @package	Documents
	 * @since 	2.1.1
 	 */
	public function __construct()
	{
 		$this->name 		= __( 'Documents', 'events' );
		$this->display_name = sprintf( __( 'Documents: %s', 'events' ), bpe_get_displayed_event( 'name' ) );
		$this->slug 		= bpe_get_option( 'documents_slug' );

		$this->create_step_position = apply_filters( 'bpe_documents_create_step_position', 30 );
		$this->enable_create_step	= true;
		$this->enable_edit_item		= true;
		$this->enable_nav_item		= ( bpe_get_displayed_event( 'has_document' ) == true && bpe_are_documents_enabled( bpe_get_displayed_event() ) ) ? true : false;
	}

	/**
	 * Display create screen
	 * 
	 * @package	Documents
	 * @since 	2.1.1
	 */
	public function create_screen()
	{
		if( ! bpe_is_event_creation_step( $this->slug ) )
			return false;
		
		bpe_load_template( 'events/top-level/steps/documents' );

		wp_nonce_field( 'bpe_add_event_'. $this->slug );
	}

	/**
	 * Process create screen
	 * 
	 * @package	Documents
	 * @since 	2.1.1
	 */
	public function create_screen_save()
	{
		check_admin_referer( 'bpe_add_event_'. $this->slug );
		
		bpe_process_document( $_POST, $_FILES, bpe_get_displayed_event() );
	}

	/**
	 * Display edit screen
	 * 
	 * @package	Documents
	 * @since 	2.1.1
	 */
	public function edit_screen()
	{
		if( ! bpe_is_event_edit_screen( $this->slug ) )
			return false;
		
		bpe_load_template( 'events/single/steps/documents' );

		wp_nonce_field( 'bpe_edit_event_'. $this->slug );
	}

	/**
	 * Process edit screen
	 * 
	 * @package	Documents
	 * @since 	2.1.1
	 */
 	public function edit_screen_save()
	{
		if( ! isset( $_POST['edit-event'] ) )
			return false;

		check_admin_referer( 'bpe_edit_event_'. $this->slug );

		bpe_process_document( $_POST, $_FILES, bpe_get_displayed_event(), false, true );

		if( is_admin() )
			bp_core_redirect( admin_url( 'admin.php?page='. EVENT_FOLDER .'&paged=1&event='. bpe_get_displayed_event( 'id' ) .'&step='. $this->slug ) );
		else
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. $this->slug .'/' );
	}
	
	/**
	 * Display any signup forms
	 * 
	 * @package	Documents
	 * @since 	2.1.1
	 */
	public function display()
	{
		bpe_load_template( 'events/single/documents' );
	}
}
bpe_register_event_extension( 'Buddyvents_Documents_Extension' );
?>