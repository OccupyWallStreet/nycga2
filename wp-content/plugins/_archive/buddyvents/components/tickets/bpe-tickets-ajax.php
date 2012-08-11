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
 * Get another ticket entry
 * 
 * Attached to the <code>wp_ajax_bpe_get_ticket_form_html</code> action hook
 * 
 * @package Tickets
 * @since 	2.0
 * @uses	bp_get_user_meta()
 * @uses	bpe_event_ticket_form()
 */
function bpe_ajax_get_ticket_form_html()
{
	$id = (int)$_POST['id'];
	$user_id = (int)$_POST['user_id'];
	
	// make sure we have everything
	if( empty( $id ) )
	{
		echo json_encode( array( 'type' => 'error', 'content' => '' ) );
		die();
	}
	
	$currency = bp_get_user_meta( $user_id, 'bpe_paypal_currency', true );
	$content = bpe_event_ticket_form( $id, false, $currency );
	echo json_encode( array( 'type' => 'success', 'content' => $content ) );
	exit;
}
add_action( 'wp_ajax_bpe_get_ticket_form_html', 'bpe_ajax_get_ticket_form_html' );
?>