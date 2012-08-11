<?php
/**
 * @param EM_Event $EM_Event
 * @return string
 */
function em_event_added_email($EM_Event){
	if( !$EM_Event->get_status() && get_option('dbem_bookings_approval') && get_option('dbem_event_submitted_email_admin') != '' ){
		$admin_emails = explode(',', get_option('dbem_event_submitted_email_admin')); //admin emails are in an array, single or multiple
		$subject = $EM_Event->output(get_option('dbem_event_submitted_email_subject'));
		$message = $EM_Event->output(get_option('dbem_event_submitted_email_body'));
		//Send email to admins
		$EM_Event->email_send( $subject,$message, $admin_emails);
	}
}
add_action('em_event_added','em_event_added_email', 10, 1);