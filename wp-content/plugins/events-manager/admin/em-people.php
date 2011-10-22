<?php
function em_printable_booking_report() {
	global $EM_Event;
	//check that user can access this page
	if( isset($_GET['page']) && $_GET['page']=='events-manager-bookings' && isset($_GET['action']) && $_GET['action'] == 'bookings_report' && is_object($EM_Event)){
		if( is_object($EM_Event) && !$EM_Event->can_manage('edit_events','edit_others_events') ){
			?>
			<div class="wrap"><h2><?php _e('Unauthorized Access','dbem'); ?></h2><p><?php _e('You do not have the rights to manage this event.','dbem'); ?></p></div>
			<?php
			return false;
		}
		em_locate_template('templates/bookings-event-printable.php', true);
		die();
	}
} 
add_action('admin_init', 'em_printable_booking_report');

/**
 * Adds phone number to contact info of users, compatible with previous phone field method
 * @param $array
 * @return array
 */
function em_contact_methods($array){
	$array['dbem_phone'] = __('Phone','dbem') . ' <span class="description">('. __('Events Manager','dbem') .')</span>';
	return $array;
}
add_filter( 'user_contactmethods' , 'em_contact_methods' , 10 , 1 );

?>