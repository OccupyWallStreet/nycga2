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
 * Get another schedule entry
 *
 * @package	 Core
 * @since 	 1.5
 */
function bpe_ajax_get_schedule_form_html()
{
	global $bp, $bpe;

	$id = (int)$_POST['id'];
	
	// make sure we have everything
	if( empty( $id ) )
	{
		echo json_encode( array( 'type' => 'error', 'content' => '' ) );
		die();
	}

	$content = bpe_event_schedule_form( $id );
	echo json_encode( array( 'type' => 'success', 'content' => $content ) );
	die();
}
add_action( 'wp_ajax_bpe_get_schedule_form_html', 		 'bpe_ajax_get_schedule_form_html' );
add_action( 'wp_ajax_nopriv_bpe_get_schedule_form_html', 'bpe_ajax_get_schedule_form_html' );

/**
 * Schedule form html
 *
 * @package	 Core
 * @since 1.5
 */
function bpe_event_schedule_form( $key, $schedule = false )
{
	$form = '<fieldset class="event-schedule" id="event-schedule-'. esc_attr( $key ) .'">';
		
		if( $schedule['id'] > 0 )
        	$form .= '<input type="hidden" name="schedule['. esc_attr( $key ) .'][id]" value="'. esc_attr( $schedule['id'] ) .'" />';
		
        $form .= '<a class="button del-schedule" href="#">X</a>';
        
		$form .= '<div class="date-schedule">';
            $form .= '<label for="date_schedule-'. esc_attr( $key ) .'">'. __( '* Date', 'events' ) .'</label>';
            $form .= '<input type="text" id="date_schedule-'. esc_attr( $key ) .'" class="schedule-date" name="schedule['. esc_attr( $key ) .'][day]" value="'. esc_attr( $schedule['day'] ) .'" />';
		$form .= '</div>';
        
		$form .= '<div class="date-schedule">';
            $form .= '<label for="start_schedule-'. esc_attr( $key ) .'">'. __( '* Start', 'events' ) .'</label>';
            $form .= '<input class="time-input" type="text" id="start_schedule-'. esc_attr( $key ) .'" name="schedule['. esc_attr( $key ) .'][start]" value="'. esc_attr( $schedule['start'] ) .'">';
        $form .= '</div>';
    
		$form .= '<div class="date-schedule">';
            $form .= '<label for="end_schedule-'. esc_attr( $key ) .'">'. __( 'End', 'events' ) .'</label>';
            $form .= '<input class="time-input" type="text" id="end_schedule-'. esc_attr( $key ) .'" name="schedule['. esc_attr( $key ) .'][end]" value="'. esc_attr( $schedule['end'] ) .'">';
      	$form .= '</div>';
        $form .= '<div class="clear"></div>';
    
        $form .= '<label for="schedule_description-'. esc_attr( $key ) .'">'. __( '* Description', 'events' ) .'</label>';
        $form .= '<textarea id="schedule_description-'. esc_attr( $key ) .'" name="schedule['. esc_attr( $key ) .'][description]">'. esc_textarea( $schedule['description'] ) .'</textarea>';

	$form .= '</fieldset>';
    
    return $form;
}
?>