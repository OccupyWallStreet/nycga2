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
 * Delete a category
 * 
 * @package Admin
 * @since 	1.1
 */
function bpe_ajax_delete_category()
{
	check_admin_referer( 'bpe_delete_category' );
	
	global $wpdb, $bpe;
	
	// we need an id
	if( empty( $_POST['id'] ) || $_POST['id'] == 1 )
	{
		$message = '<div class="error"><p>' . __( 'The category could not be deleted.', 'events' ) . '</p></div>';
		echo json_encode( array( 'type' => 'error', 'message' => $message ) );
		die();
	}
	
	// delete the category and reassign categories
	$cat = new Buddyvents_Categories( (int)$_POST['id'] );
	if( $cat->delete() )
		$wpdb->query( $wpdb->prepare( "UPDATE {$bpe->tables->events} SET category = 1 WHERE category = %d", (int)$_POST['id'] ) );

	$message = '<div class="updated"><p>' . __( 'The category has been deleted.', 'events' ) . '</p></div>';
	echo json_encode( array( 'type' => 'success', 'message' => $message, 'id' => (int)$_POST['id'] ) );
	die();
}
add_action( 'wp_ajax_bpe_delete_category', 'bpe_ajax_delete_category' );

/**
 * Add a category
 * 
 * @package Admin
 * @since 	1.1
 */
function bpe_ajax_add_category()
{
	check_admin_referer( 'bpe_categories' );
	
	// we need a name
	if( empty( $_POST['name'] ) )
	{
		$message = '<div class="error"><p>' . __( 'Please specify a category name.', 'events' ) . '</p></div>';
		echo json_encode( array( 'type' => 'error', 'message' => $message ) );
		die();
	}
	
	$cat_id = ( empty( $_POST['cat_id'] ) ) ? null : (int)$_POST['cat_id'];
	
	// if no slug was specified, we use the name
	$slug = ( empty(  $_POST['slug'] ) ) ? $_POST['name'] : $_POST['slug'];
	// format the slug
	$slug = sanitize_title_with_dashes( $slug );
	// now we need to check the slug
	$slug = bpe_check_unique_slug( $slug, 'categories', $cat_id );
	
	// got everything, so try to insert the category
	if( ! $id = bpe_add_category( $cat_id, $_POST['name'], $slug ) )
	{
		$message =  '<div class="error"><p>' . __( 'There was a problem. The category could not be saved. Please try again.', 'events' ) . '</p></div>';
		echo json_encode( array( 'type' => 'error', 'message' => $message ) );
		die();
	}
	else
	{
		if( $cat_id  )
		{
			$message = '<div class="updated"><p>' . __( 'Your category was edited successfully.', 'events' ) . '</p></div>';
			$category = $cat_id .'§'. wp_filter_kses( $slug ) .'§'. wp_filter_kses( $_POST['name'] );
			$action = 'edit';
		}
		else
		{
			$message = '<div class="updated"><p>' . __( 'Your category was added successfully.', 'events' ) . '</p></div>';
	
			$category  = '<tr>';
			$category .= '<td class="check-column"><a id="cat-'. $id .'" class="bpe-delete-category" href="'. wp_nonce_url( admin_url( 'admin.php?page='. EVENT_FOLDER ), 'bpe_delete_category' ) .'"></a></td>';
			$category .= '<td><a id="editcat-'. $id .'" class="bpe-edit-category" href="'. admin_url( 'admin.php?page='. EVENT_FOLDER ) .'" title="'. __( 'Edit this category', 'events' ) .'">'. wp_filter_kses( $_POST['name'] ) .'</a></td>';
			$category .= '<td id="catslug-'. $id .'">'. wp_filter_kses( $slug ) .'</td>';
			$category .= '<td id="num-'. $id .'" class="num">0</td>';
			$category .= '</tr>';
			
			$action = 'add';
		}
				
		echo json_encode( array( 'type' => 'success', 'message' => $message, 'category' => $category, 'action' => $action ) );
		die();
	}
}
add_action( 'wp_ajax_bpe_add_category', 'bpe_ajax_add_category' );

/**
 * Send the quote request
 * 
 * @since 2.0
 */
function bpe_send_ajax_quote_request()
{
	check_admin_referer( 'shabu_contact-form' );

	if( empty( $_POST['message'] ) )
	{
		$message = '<div class="error"><p>' . __( 'Message needs to be filled in.', 'events' ) . '</p></div>';
		echo json_encode( array( 'type' => 'error', 'message' => $message ) );
		die();
	}
	
	$user = get_userdata( bp_loggedin_user_id() );
	
	$subject = wp_filter_kses( $_POST['subject'] );
	$message = wp_filter_kses( $_POST['message'] );
	
	$appendix  = 'Sent by:'. $user->display_name ."\n";
	$appendix .= 'Sent email:'. $user->user_email ."\n";
	$appendix .= 'Sent login:'. $user->user_login ."\n\n";
	
	wp_mail( 'mail@shabushabu.eu', $subject, $appendix . $message );

	$message = '<div class="updated"><p>' . __( 'Your message has been sent! We will get back to you asap, usually within 12 hours.', 'events' ) . '</p></div>';
	echo json_encode( array( 'type' => 'success', 'message' => $message ) );
	die();
}
add_action( 'wp_ajax_shabu_quote_request', 'bpe_send_ajax_quote_request' );
?>