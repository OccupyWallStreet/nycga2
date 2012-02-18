<?php  
/* @var $EM_Event EM_Event */   
global $EM_Notices;
$EM_Tickets = $EM_Event->get_bookings()->get_tickets();
$EM_Ticket = $EM_Tickets->get_first();
?>
<div id="em-booking">
	<a name="em-booking"></a>
	<?php 
		// We are firstly checking if the user has already booked a ticket at this event, if so offer a link to view their bookings.
		$EM_Booking = $EM_Event->get_bookings()->has_booking();
	?>
	<?php if( is_object($EM_Booking) && !get_option('dbem_bookings_double') ): ?>
		<p><?php echo apply_filters('em_my_bookings_booked_message', sprintf(__('You are currently attending this event. <a href="%s">Manage my bookings</a>','dbem'), em_get_my_bookings_url()), $EM_Booking); ?></p>
	<?php elseif( !$EM_Event->rsvp ): ?>
		<p><?php _e('Online bookings are not available for this event.','dbem'); ?></p>
	<?php elseif( $EM_Event->start < current_time('timestamp') ): ?>
		<p><?php _e('Bookings are closed for this event.','dbem'); ?></p>
	<?php elseif( $EM_Event->get_bookings()->get_available_spaces() <= 0 || (count($EM_Tickets->tickets) == 1 && !get_option('dbem_bookings_tickets_single_form') && $EM_Ticket->get_available_spaces() <= 0) ): ?>
		<p><?php _e('This event is fully booked.','dbem'); ?></p>
	<?php else: ?>
		<?php echo $EM_Notices; ?>
		<?php if( count($EM_Tickets->tickets) > 0) : ?>
			<?php //Tickets exist, so we show a booking form. ?>
			<form id='em-booking-form' name='booking-form' method='post' action=''>
				<?php do_action('em_booking_form_before_tickets'); ?>
				<?php 
					/* Show Tickets
					 * If there's more than one ticket, we show them in a list. 
					 * If not, we'll only show one ddm for the number of seats and maybe a price indicator if this event entrance has a price. 
					 * If for some reason you have more than one free ticket and no paid ones, the price collumn will be ommited.
					 */
					//we may show the tickets if user is logged out, so test this condition here and save result for later
					$can_book = is_user_logged_in() || (get_option('dbem_bookings_anonymous') && !is_user_logged_in());
					if( ($can_book || get_option('dbem_bookings_tickets_show_loggedout')) && (count($EM_Tickets->tickets) > 1 || get_option('dbem_bookings_tickets_single_form')) ){ //show if more than 1 ticket, or if in forced ticket list view mode
						em_locate_template('forms/bookingform/tickets-list.php',true, array('EM_Event'=>$EM_Event));
					}
				?>
				<?php if( $can_book ): ?>
					<?php do_action('em_booking_form_after_tickets'); ?>
					<div class='em-booking-form-details'>
						<?php 
							if( is_object($EM_Ticket) && count($EM_Tickets->tickets) == 1 && !get_option('dbem_bookings_tickets_single_form') ){
								em_locate_template('forms/bookingform/ticket-single.php',true, array('EM_Event'=>$EM_Event, 'EM_Ticket'=>$EM_Ticket));
							} 
						?>						
						<?php 
							if( get_option('em_booking_form_custom') ){ 
								do_action('em_booking_form_custom'); 
							}else{
								em_locate_template('forms/bookingform/booking-fields.php',true, array('EM_Event'=>$EM_Event, 'EM_Ticket'=>$EM_Ticket));
							}
						?>
						<div class="em-booking-buttons">
							<?php echo apply_filters('em_booking_form_buttons', '<input type="submit" class="em-booking-submit" id="em-booking-submit" value="'.__('Send your booking', 'dbem').'" />', $EM_Event); ?>
						 	<input type='hidden' name='action' value='booking_add'/>
						 	<input type='hidden' name='event_id' value='<?php echo $EM_Event->event_id; ?>'/>
						 	<input type='hidden' name='_wpnonce' value='<?php echo wp_create_nonce('booking_add'); ?>'/>
						</div>
					</div>
				<?php else: ?>
					<p class="em-booking-form-details"><?php echo get_option('dbem_booking_feedback_log_in'); ?></p>
				<?php endif; ?>
			</form>	
			<?php 
			if( !is_user_logged_in() && get_option('dbem_bookings_login_form') ){
				em_locate_template('forms/bookingform/login.php',true, array('EM_Event'=>$EM_Event));
			}
			?>
			<br class="clear" style="clear:left;" />
		<?php elseif( count($EM_Tickets->tickets) == 0 ): ?>
			<div><?php _e('No more tickets available at this time.','dbem'); ?></div>
		<?php endif; ?>  
	<?php endif; ?>
</div>