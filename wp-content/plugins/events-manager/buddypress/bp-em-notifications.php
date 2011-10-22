<?php

/********************************************************************************
 * Activity & Notification Functions
 *
 * These functions handle the recording, deleting and formatting of activity and
 * notifications for the user and for this specific component.
 */
/**
 * bp_em_screen_notification_settings()
 *
 * Adds notification settings for the component, so that a user can turn off email
 * notifications set on specific component actions.
 */
function bp_em_screen_notification_settings() {
	global $current_user;

	/**
	 * Under Settings > Notifications within a users profile page they will see
	 * settings to turn off notifications for each component.
	 *
	 * You can plug your custom notification settings into this page, so that when your
	 * component is active, the user will see options to turn off notifications that are
	 * specific to your component.
	 */

	 /**
	  * Each option is stored in a posted array notifications[SETTING_NAME]
	  * When saved, the SETTING_NAME is stored as usermeta for that user.
	  *
	  * For em, notifications[notification_friends_friendship_accepted] could be
	  * used like this:
	  *
	  * if ( 'no' == get_usermeta( $bp['loggedin_userid'], 'notification_friends_friendship_accepted' ) )
	  *		// don't send the email notification
	  *	else
	  *		// send the email notification.
      */

	?>
	<table class="notification-settings" id="bp-em-notification-settings">
		<tr>
			<th class="icon"></th>
			<th class="title"><?php _e( 'Events', 'dbem' ) ?></th>
			<th class="yes"><?php _e( 'Yes', 'dbem' ) ?></th>
			<th class="no"><?php _e( 'No', 'dbem' )?></th>
		</tr>
		<tr>
			<td></td>
			<td><?php _e( 'Action One', 'dbem' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_em_action_one]" value="yes" <?php if ( !get_usermeta( $current_user->id,'notification_em_action_one') || 'yes' == get_usermeta( $current_user->id,'notification_em_action_one') ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_em_action_one]" value="no" <?php if ( get_usermeta( $current_user->id,'notification_em_action_one') == 'no' ) { ?>checked="checked" <?php } ?>/></td>
		</tr>
		<tr>
			<td></td>
			<td><?php _e( 'Action Two', 'dbem' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_em_action_two]" value="yes" <?php if ( !get_usermeta( $current_user->id,'notification_em_action_two') || 'yes' == get_usermeta( $current_user->id,'notification_em_action_two') ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_em_action_two]" value="no" <?php if ( 'no' == get_usermeta( $current_user->id,'notification_em_action_two') ) { ?>checked="checked" <?php } ?>/></td>
		</tr>

		<?php do_action( 'bp_em_notification_settings' ); ?>
	</table>
<?php
}
//add_action( 'bp_notification_settings', 'bp_em_screen_notification_settings' );

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
 * Catch booking saves and add a BP notification.
 * @param boolean $result
 * @param EM_Booking $EM_Booking
 * @return boolean
 */
function bp_em_add_booking_notification($result, $EM_Booking){
	global $bp;
	if( get_option('dbem_bookings_approval') && $EM_Booking->status == 0 ){
		$action = 'pending_booking';
	}elseif( $EM_Booking->status == 1 || (get_option('dbem_bookings_approval') && $EM_Booking->status == 0) ){
		$action = 'confirmed_booking';
	}elseif( $EM_Booking->status == 3 ){
		$action = 'cancelled_booking';
	}
	if( !empty($action) ){
		bp_core_add_notification( $EM_Booking->id, $EM_Booking->get_event()->owner, 'events', $action );
	}
	return $result;
}
add_filter('em_booking_save','bp_em_add_booking_notification',1,2);