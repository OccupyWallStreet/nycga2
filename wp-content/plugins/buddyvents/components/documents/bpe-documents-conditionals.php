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

/**
 * Is current page a documents page
 *
 * @package	 	Core
 * @since 	 	1.7
 * @deprecated	since version 2.1.1
 */
function bpe_is_event_documents()
{
	if( ! bpe_get_option( 'enable_documents' ) )
		return false;
	
	if( ! bpe_are_documents_enabled( bpe_get_displayed_event() ) )
		return false;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'active_slug' ) ) && bp_action_variable( 0 ) && bp_is_action_variable( bpe_get_option( 'documents_slug' ), 1 ) )
		return true;
	
	return false;
}

/**
 * Check if documents are enabled
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_are_documents_enabled( $event )
{
	if( bpe_get_option( 'enable_documents' ) == 4 )
		return false;
	
	if( bpe_get_option( 'enable_documents' ) == 1 )
		return true;
	
	if( bpe_get_option( 'enable_documents' ) == 3 && bpe_is_member( $event ) )
		return true;
	
	if( bpe_get_option( 'enable_documents' ) == 2 && is_user_logged_in() )
		return true;
		
	return false;	
}
?>