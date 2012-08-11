<?php
/*
 * This template needs updating, soon the JS will be removed from here, and most probably the button variables will be localized so that the JS isn't using PHP to output info.
 * Please keep an eye on this file when updating if you depend heavily on this button. We'll aim to make one big change and leave it at that (or at least for as long as possible!)
 */
$button_text = __('Book Now', 'dbem');
$button_booking = __('Booking...','dbem');
$button_success = sprintf(__('%s Submitted','dbem'), __('Booking','dbem'));
$button_fail = sprintf(__('%s Error. Try again?','dbem'), __('Booking','dbem'));
$button_cancel = __('Cancel', 'dbem');
$button_canceling = __('Canceling...','dbem');
$button_cancel_success = __('Cancelled','dbem');
$button_cancel_fail = sprintf(__('%s Error. Try again?','dbem'), __('Cancellation','dbem'));
?>
<?php if( count($EM_Event->get_bookings()->get_available_tickets()->tickets) == 1 && is_user_logged_in() ):  ?>
	<?php
		$EM_Booking = $EM_Event->get_bookings()->has_booking(); 
		ob_start(); 
	?>
	<?php if( is_object($EM_Booking) && !get_option('dbem_bookings_double') && $EM_Booking->status != 3 && get_option('dbem_bookings_user_cancellation') ) : $show_js = true; ?>
		<a id="em-cancel-button-<?php echo $EM_Event->event_id; ?>" class="button"><?php echo $button_cancel; ?></a>
	<?php elseif( !is_object($EM_Booking) || get_option('dbem_bookings_double') ) : $show_js = true; ?>
		<a id="em-booking-button-<?php echo $EM_Event->event_id; ?>" class="button"><?php echo $button_text; ?></a>
	<?php endif; ?>	
	<?php echo apply_filters( 'em_booking_button', ob_get_clean(), $EM_Event ); ?>
	<?php if( !empty($show_js) ): ob_start(); ?>
	<script type="text/javascript">
		jQuery(document).ready( function($){
			$('#em-booking-button-<?php echo $EM_Event->event_id; ?>').click(function(){
				if( $(this).text() != '<?php echo $button_success; ?>' && $(this).text() != '<?php echo $button_booking; ?>'){
					$(this).text('<?php echo $button_booking; ?>');
					$.ajax({
						url: EM.ajaxurl,
						dataType: 'jsonp',
						data: {
							event_id : '<?php echo $EM_Event->event_id; ?>',
							_wpnonce : '<?php echo wp_create_nonce('booking_add_one') ?>',
							action : 'booking_add_one'
						},
						success : function(response, statusText, xhr, $form) {
							if(response.result){
								$('#em-booking-button-<?php echo $EM_Event->event_id; ?>').text('<?php echo $button_success; ?>');
							}else{
								$('#em-booking-button-<?php echo $EM_Event->event_id; ?>').text('<?php echo $button_fail; ?>');					
							}
							if(response.message != '') alert(response.message);
						},
						error : function(){ $('#em-booking-button-<?php echo $EM_Event->event_id; ?>').text('<?php echo $button_fail; ?>'); }
					});
				}
			});								
		});
		jQuery(document).ready( function($){
			$('#em-cancel-button-<?php echo $EM_Event->event_id; ?>').click(function(){
				if( $(this).text() != '<?php echo $button_cancel_success; ?>' && $(this).text() != '<?php echo $button_canceling; ?>'){
					$(this).text('<?php echo $button_canceling; ?>');
					$.ajax({
						url: EM.ajaxurl,
						dataType: 'jsonp',
						data: {
							booking_id : '<?php echo $EM_Booking->booking_id; ?>',
							_wpnonce : '<?php echo wp_create_nonce('booking_cancel') ?>',
							action : 'booking_cancel'
						},
						success : function(response, statusText, xhr, $form) {
							if(response.result){
								$('#em-cancel-button-<?php echo $EM_Event->event_id; ?>').text('<?php echo $button_cancel_success; ?>');
							}else{
								$('#em-cancel-button-<?php echo $EM_Event->event_id; ?>').text('<?php echo $button_cancel_fail; ?>');
							}
						},
						error : function(){ $('#em-booking-button-<?php echo $EM_Event->event_id; ?>').text('<?php echo $button_cancel_fail; ?>'); }
					});
				}
			});  
		});
	</script>
	<?php echo apply_filters( 'em_booking_button_js', ob_get_clean(), $EM_Event ); endif; ?>
<?php endif; ?>