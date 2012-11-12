var em_booking_doing_ajax = false;		
$('input#em-booking-submit').click(function(e){
	$('.em-booking-message').remove();
	var booking_gateway_handler = function(event, response){
		if(response.result){
			$('<div class="em-booking-message-success em-booking-message">'+response.message+'</div>').insertBefore('#em-booking-form');
			$('#em-booking-form').remove();
			$('.em-booking-login').remove();
		}else{
			if( response.errors != null ){
				if( $.isArray(response.errors) && response.errors.length > 0 ){
					var error_msg;
					response.errors.each(function(i, el){ 
						error_msg = error_msg + el;
					});
					$('<div class="em-booking-message-error em-booking-message">'+error_msg.errors+'</div>').insertBefore('#em-booking-form');
				}else{
					$('<div class="em-booking-message-error em-booking-message">'+response.errors+'</div>').insertBefore('#em-booking-form');							
				}
			}else{
				$('<div class="em-booking-message-error em-booking-message">'+response.message+'</div>').insertBefore('#em-booking-form');
			}
		}
		$('#em-booking-form input[name=gateway]').remove();
		$(document).unbind('em_booking_gateway_add',booking_gateway_handler);	
	};
	$(document).bind('em_booking_gateway_add',booking_gateway_handler);
});
$('#em-booking-form').submit( function(e){
	e.preventDefault();
	$.ajax({
		url: EM.ajaxurl,
		data:$('#em-booking-form').serializeArray(),
		dataType: 'jsonp',
		type:'post',
		beforeSend: function(formData, jqForm, options) {
			if(em_booking_doing_ajax){
				alert(EM.bookingInProgress);
				return false;
			}
			em_booking_doing_ajax = true;
			$('.em-booking-message').remove();
			$('#em-booking').append('<div id="em-loading"></div>');
		},
		success : function(response, statusText, xhr, $form) {
			$('#em-loading').remove();
			$('.em-booking-message').remove();
			$(document).trigger('em_booking_gateway_add', [response]);
			if(response.result && typeof Recaptcha != 'undefined'){
				Recaptcha.reload();
			}
			em_booking_doing_ajax = false;
		}
	});
	return false;	
});