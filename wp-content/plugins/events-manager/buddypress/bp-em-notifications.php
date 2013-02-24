<?php
//This file handles hooks requiring notifications

/**
 * bp_em_format_notifications()
 *
 * The format notification function will take DB entries for notifications and format them
 * so that they can be displayed and read on the screen.
 *
 * Notifications are "screen" notifications, that is, they appear on the notifications menu
 * in the site wide navigation bar. They are not for email notifications.
 *
 *
 * The recording is done by using bp_core_add_notification() which you can search for in this file for
 * ems of usage.
 */
function bp_em_format_notifications( $action, $item_id, $secondary_item_id, $total_items ) {
	global $bp;
	switch ( $action ) {
		case 'pending_booking':
			//Count pending bookings
			if( get_option('dbem_bookings_approval')){ 
				if ( $total_items > 1 ) {
					return '<a href="' . $bp->loggedin_user->domain . $bp->events->slug . '/my-bookings/" title="' . __( 'My Bookings', 'dbem' ) . '">' . __('You have a pending booking','dbem'). '</a>';
				} else {
					return apply_filters( 'bp_em_format_new_booking_notification', '<a href="' . $bp->loggedin_user->domain . $bp->events->slug . '/my-bookings/" title="' . __( 'My Bookings', 'dbem' ) . '">' . sprintf(__('You have %s pending bookings','dbem'), $total_items). '</a>' );
				}
			}
		break;
		case 'confirmed_booking':
			//Count pending bookings
			if ( $total_items > 1 ) {
				return apply_filters( 'bp_em_format_confirmed_booking_notifications', '<a href="' . $bp->loggedin_user->domain . $bp->events->slug . '/my-bookings/" title="' . __( 'My Bookings', 'dbem' ) . '">' . __('You have a confirmed booking','dbem'). '</a>' );
			} else {
				return apply_filters( 'bp_em_format_confirmed_booking_notification', '<a href="' . $bp->loggedin_user->domain . $bp->events->slug . '/my-bookings/" title="' . __( 'My Bookings', 'dbem' ) . '">' . sprintf(__('You have %s confirmed bookings','dbem'), $total_items). '</a>' );
			}
		break;
		case 'cancelled_booking':
			//Count pending bookings
			if ( $total_items > 1 ) {
				return apply_filters( 'bp_em_format_cancelled_booking_notifications', '<a href="' . $bp->loggedin_user->domain . $bp->events->slug . '/my-bookings/" title="' . __( 'My Bookings', 'dbem' ) . '">' . __('A user cancelled a booking','dbem'). '</a>' );
			} else {
				return apply_filters( 'bp_em_format_cancelled_booking_notification', '<a href="' . $bp->loggedin_user->domain . $bp->events->slug . '/my-bookings/" title="' . __( 'My Bookings', 'dbem' ) . '">' . sprintf(__('%s users cancelled bookings.','dbem'), $total_items). '</a>' );
			}
		break;
	}
	do_action( 'bp_em_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return false;
}

/**
 * Remove a screen notification for a user.
 */
function bp_em_remove_screen_notifications() {
	global $bp;
	bp_core_delete_notifications_by_type( $bp->loggedin_user->id, $bp->events->slug, 'attending' );
}
add_action( 'bp_em_my_events', 'bp_em_remove_screen_notifications' );
add_action( 'xprofile_screen_display_profile', 'bp_em_remove_screen_notifications' );

/**
 * Catch booking saves and add a BP notification.
 * @param boolean $result
 * @param EM_Booking $EM_Booking
 * @return boolean
 */
function bp_em_add_booking_notification($result, $EM_Booking){
	global $bp;
	if( get_option('dbem_bookings_approval') && $EM_Booking->get_status() == 0 ){
		$action = 'pending_booking';
	}elseif( $EM_Booking->get_status() == 1 || (get_option('dbem_bookings_approval') && $EM_Booking->get_status() == 0) ){
		$action = 'confirmed_booking';
	}elseif( $EM_Booking->get_status() == 3 ){
		$action = 'cancelled_booking';
	}
	if( !empty($action) ){
		bp_core_add_notification( $EM_Booking->booking_id, $EM_Booking->get_event()->get_contact()->ID, 'events', $action );
	}
	return $result;
}
add_filter('em_booking_save','bp_em_add_booking_notification',1,2);