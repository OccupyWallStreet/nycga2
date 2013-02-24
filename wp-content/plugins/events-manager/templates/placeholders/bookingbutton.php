<?php
/*
 * You can override this by copying this file to wp-content/themesyourthemefolder/plugins/events-manager/placeholders/ and modifying it however you need.
 * There are a few variables made available to you:
 * 
 * $EM_Event - EM_Event object 
 */
$notice_full = get_option('dbem_booking_button_msg_full');
$button_text = get_option('dbem_booking_button_msg_book');
$button_booking = get_option('dbem_booking_button_msg_booking');
$button_success = get_option('dbem_booking_button_msg_booked');
$button_fail = get_option('dbem_booking_button_msg_error');
$button_cancel = get_option('dbem_booking_button_msg_cancel');
$button_canceling = get_option('dbem_booking_button_msg_canceling');
$button_cancel_success = get_option('dbem_booking_button_msg_cancelled');
$button_cancel_fail = get_option('dbem_booking_button_msg_cancel_error');
/* @var $EM_Event EM_Event */
?>
<?php if( $EM_Event->get_bookings()->is_open() && is_user_logged_in() ):  ?>
	<?php
		$EM_Booking = $EM_Event->get_bookings()->has_booking(); 
		ob_start(); 
	?>
	<?php if( is_object($EM_Booking) && !get_option('dbem_bookings_double') && $EM_Booking->status != 3 && get_option('dbem_bookings_user_cancellation') ) : $show_js = true; ?>
		<a id="em-cancel-button_<?php echo $EM_Booking->booking_id; ?>_<?php echo wp_create_nonce('booking_cancel'); ?>" class="button em-cancel-button"><?php echo $button_cancel; ?></a>
	<?php elseif( !is_object($EM_Booking) || get_option('dbem_bookings_double') ) : $show_js = true; ?>
		<a id="em-booking-button_<?php echo $EM_Event->event_id; ?>_<?php echo wp_create_nonce('booking_add_one'); ?>" class="button em-booking-button"><?php echo $button_text; ?></a>
	<?php endif; ?>	
	<?php echo apply_filters( 'em_booking_button', ob_get_clean(), $EM_Event ); ?>
<?php elseif( $EM_Event->get_bookings()->get_available_spaces() <= 0 ): ?>
	<span class="em-full-button"><?php echo $notice_full ?></span>
<?php endif; ?>