<?php 
$button_text = __('Book Now', 'dbem');
$button_booking = __('Booking...','dbem');
$button_success = __('Booking Submitted','dbem');
$button_fail = __('Booking Error. Try again?','dbem');
?>
<?php if( count($EM_Event->get_bookings()->get_available_tickets()->tickets) == 1 && is_user_logged_in() ):  ?>
	<?php ob_start(); ?>
	<a id="em-booking-button-<?php echo $EM_Event->id; ?>" class="button"><?php echo $button_text; ?></a>
	<?php echo apply_filters( 'em_booking_button', ob_get_clean(), $EM_Event ); ?>
	<?php ob_start(); ?>
	<script type="text/javascript">
		jQuery(document).ready( function($){
			$('#em-booking-button-<?php echo $EM_Event->id; ?>').click(function(){
				if( $(this).text() != '<?php echo $button_success; ?>' && $(this).text() != '<?php echo $button_booking; ?>'){
					$(this).text('<?php echo $button_booking; ?>');
					$.ajax({
						url: EM.ajaxurl,
						dataType: 'jsonp',
						beforeSubmit: function(formData, jqForm, options) {
							$('.em-booking-message').remove();
							$('#em-booking-form').append('<div id="em-loading"></div>');
						},
						data: {
							event_id : '<?php echo $EM_Event->id; ?>',
							_wpnonce : '<?php echo wp_create_nonce('booking_add_one') ?>',
							action : 'booking_add_one'
						},
						success : function(response, statusText, xhr, $form) {
							if(response.result){
								$('#em-booking-button-<?php echo $EM_Event->id; ?>').text('<?php echo $button_success; ?>');
							}else{
								$('#em-booking-button-<?php echo $EM_Event->id; ?>').text('<?php echo $button_text; ?>');					
							}
							alert(response.message);
						}
					});
				}
			});								
		});
	</script>
	<?php echo apply_filters( 'em_booking_button_js', ob_get_clean(), $EM_Event ); ?>
<?php endif; ?>