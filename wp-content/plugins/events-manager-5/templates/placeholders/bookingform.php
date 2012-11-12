<?php  
/* 
 * This is where the booking form is generated.
 * For non-advanced users, It's not recommended you edit this form directly if avoidable, as you can change booking form settings in various less obtrusive and upgrade-safe ways:
 * - check your booking form options panel in the Booking Options tab in your settings.
 * - use CSS or jQuery to change the look of your booking forms
 * - edit the files in the forms/bookingform folder individually instead of this file, to make it more upgrade-safe
 * - hook into WP action/filters below to modify/generate information 
 */

/* @var $EM_Event EM_Event */   
global $EM_Notices;
$EM_Tickets = $EM_Event->get_bookings()->get_tickets();
$EM_Ticket = $EM_Tickets->get_first();
$can_book = is_user_logged_in() || (get_option('dbem_bookings_anonymous') && !is_user_logged_in());
?>
<div id="em-booking" class="em-booking">
	<a name="em-booking"></a>
	<?php 
		// We are firstly checking if the user has already booked a ticket at this event, if so offer a link to view their bookings.
		$EM_Booking = $EM_Event->get_bookings()->has_booking();
	?>
	<?php if( is_object($EM_Booking) && !get_option('dbem_bookings_double') ): //Double bookings not allowed ?>
		<p>
			<?php echo get_option('dbem_bookings_form_msg_attending'); ?>
			<a href="<?php echo em_get_my_bookings_url(); ?>"><?php echo get_option('dbem_bookings_form_msg_bookings_link'); ?></a>
		</p>
	<?php elseif( !$EM_Event->rsvp ): //bookings not enabled ?>
		<p><?php echo get_option('dbem_bookings_form_msg_disabled'); ?></p>
	<?php elseif( $EM_Event->get_bookings()->get_available_spaces() <= 0 ): ?>
		<p><?php echo get_option('dbem_bookings_form_msg_full'); ?></p>
	<?php elseif( !$EM_Event->get_bookings()->is_open() ): //event has started ?>
		<p><?php echo get_option('dbem_bookings_form_msg_closed');  ?></p>
	<?php else: ?>
		<?php echo $EM_Notices; ?>
		<?php if( count($EM_Tickets->tickets) > 0) : ?>
			<?php //Tickets exist, so we show a booking form. ?>
			<form id='em-booking-form' class="em-booking-form" name='booking-form' method='post' action='<?php echo apply_filters('em_booking_form_action_url',$_SERVER['REQUEST_URI']); ?>'>
			 	<input type='hidden' name='action' value='booking_add'/>
			 	<input type='hidden' name='event_id' value='<?php echo $EM_Event->event_id; ?>'/>
			 	<input type='hidden' name='_wpnonce' value='<?php echo wp_create_nonce('booking_add'); ?>'/>
				<?php 
					// Tickets Form
					if( ($can_book || get_option('dbem_bookings_tickets_show_loggedout')) && (count($EM_Tickets->tickets) > 1 || get_option('dbem_bookings_tickets_single_form')) ){ //show if more than 1 ticket, or if in forced ticket list view mode
						do_action('em_booking_form_before_tickets'); //do not delete
						//Show multiple tickets form to user, or single ticket list if settings enable this
						//If logged out, can be allowed to see this in settings witout the register form 
						em_locate_template('forms/bookingform/tickets-list.php',true, array('EM_Event'=>$EM_Event));
						do_action('em_booking_form_after_tickets'); //do not delete
					}
				?>
				<?php if( $can_book ): ?>
					<div class='em-booking-form-details'>
						<?php 
							if( is_object($EM_Ticket) && count($EM_Tickets->tickets) == 1 && !get_option('dbem_bookings_tickets_single_form') ){
								do_action('em_booking_form_before_tickets'); //do not delete
								//show single ticket form, only necessary to show to users able to book (or guests if enabled)
								em_locate_template('forms/bookingform/ticket-single.php',true, array('EM_Event'=>$EM_Event, 'EM_Ticket'=>$EM_Ticket));
								do_action('em_booking_form_after_tickets'); //do not delete
							} 
						?>	
						<?php 
							do_action('em_booking_form_before_user_details');
							if( has_action('em_booking_form_custom') ){ 
								//Pro Custom Booking Form. You can create your own custom form by hooking into this action and setting the option above to true
								do_action('em_booking_form_custom', $EM_Event); //do not delete
							}else{
								//If you just want to modify booking form fields, you could do so here
								em_locate_template('forms/bookingform/booking-fields.php',true, array('EM_Event'=>$EM_Event, 'EM_Ticket'=>$EM_Ticket));
							}
							do_action('em_booking_form_after_user_details');
						?>
						<?php do_action('em_booking_form_footer', $EM_Event); //do not delete ?>
						<div class="em-booking-buttons">
							<?php if( preg_match('/https?:\/\//',get_option('dbem_bookings_submit_button')) ): //Settings have an image url (we assume). Use it here as the button.?>
							<input type="image" src="<?php echo get_option('dbem_bookings_submit_button'); ?>" class="em-booking-submit" id="em-booking-submit" />
							<?php else: //Display normal submit button ?>
							<input type="submit" class="em-booking-submit" id="em-booking-submit" value="<?php echo get_option('dbem_bookings_submit_button'); ?>" />
							<?php endif; ?>
						</div>
						<?php do_action('em_booking_form_footer_after_buttons', $EM_Event); //do not delete ?>
					</div>
				<?php else: ?>
					<p class="em-booking-form-details"><?php echo get_option('dbem_booking_feedback_log_in'); ?></p>
				<?php endif; ?>
			</form>	
			<?php 
			if( !is_user_logged_in() && get_option('dbem_bookings_login_form') ){
				//User is not logged in, show login form (enabled on settings page)
				em_locate_template('forms/bookingform/login.php',true, array('EM_Event'=>$EM_Event));
			}
			?>
			<br class="clear" style="clear:left;" />
		<?php endif; ?>  
	<?php endif; ?>
</div>