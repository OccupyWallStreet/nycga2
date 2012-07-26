jQuery(document).ready(function(){

	jQuery('#shabu-contact-form #send-contact-message').live( 'click', function() {

		var subject = jQuery( "#shabu-contact-form #contact-subject" ).val();
		var message = jQuery( "#shabu-contact-form textarea#contact-message" ).val();
		var nonce = jQuery( "#shabu-contact-form #_wpnonce" ).val();
		
		jQuery('#shabu-contact-form .ajax-loading').css('visibility', 'visible');
		
		jQuery.post( ajaxurl, {
			'action': 'shabu_quote_request',
			'cookie': encodeURIComponent(document.cookie),
			'subject': subject,
			'message': message,
			'_wpnonce': nonce
		},
		function(response) {
			response = jQuery.parseJSON(response);
			
			if(response.type == 'success' )
				jQuery('#contact-wrapper').empty().html(response.message);
			else
				jQuery('#ajax-response').empty().html(response.message);
			
			jQuery('#shabu-contact-form .ajax-loading').css('visibility', 'hidden');
		});
		
		return false;
	});
});