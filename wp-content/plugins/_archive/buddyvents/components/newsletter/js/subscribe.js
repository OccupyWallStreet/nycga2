jQuery(document).ready(function() {
	// subscribe an email address to Mailchimp
	jQuery('#mailchimp-subscription-form').submit( function() {
		var submit = jQuery('#mailchimp-subscribe');
		submit.addClass('loading');
		
		jQuery.post( ajaxurl, {
			'action': 'bpe_mailchimp_new_subscriber',
			'_bpe_mailchimp_nonce': jQuery('#_bpe_mailchimp_nonce').val(),
			'mailchimp_first_name': jQuery('#mailchimp_first_name').val(),
			'mailchimp_last_name': jQuery('#mailchimp_last_name').val(),
			'mailchimp_event_id': jQuery('#mailchimp_event_id').val(),
			'mailchimp_email': jQuery('#mailchimp_email').val(),
			'cookie': encodeURIComponent(document.cookie)
		},
		function(response) {
			response = jQuery.parseJSON(response);

			if(response.message != ''){
				jQuery('#message').remove();
				jQuery('#event-actions').before('<div id="message" class="'+ response.status +'"><p>'+ response.message +'</p></div>');
			}
			
			jQuery('#mailchimp_first_name,#mailchimp_last_name,#mailchimp_email').val('');
			
			submit.removeClass('loading');
		});
		
		return false;
	});

	// subscribe an email address to Campaign Monitor
	jQuery('#cmonitor-subscription-form').submit( function() {
		var submit = jQuery('#cmonitor-subscribe');
		submit.addClass('loading');
		
		jQuery.post( ajaxurl, {
			'action': 'bpe_cmonitor_new_subscriber',
			'_bpe_cmonitor_nonce': jQuery('#_bpe_cmonitor_nonce').val(),
			'cmonitor_name': jQuery('#cmonitor_name').val(),
			'cmonitor_email': jQuery('#cmonitor_email').val(),
			'cmonitor_event_id': jQuery('#cmonitor_event_id').val(),
			'cookie': encodeURIComponent(document.cookie)
		},
		function(response) {
			response = jQuery.parseJSON(response);

			if(response.message != ''){
				jQuery('#message').remove();
				jQuery('#event-actions').before('<div id="message" class="'+ response.status +'"><p>'+ response.message +'</p></div>');
			}
			
			jQuery('#cmonitor_name,#cmonitor_email').val('');
			
			submit.removeClass('loading');
		});
		
		return false;
	});

	// subscribe an email address to AWeber
	jQuery('#aweber-subscription-form').submit( function() {
		var submit = jQuery('#aweber-subscribe');
		submit.addClass('loading');
		
		jQuery.post( ajaxurl, {
			'action': 'bpe_aweber_new_subscriber',
			'_bpe_aweber_nonce': jQuery('#_bpe_aweber_nonce').val(),
			'aweber_name': jQuery('#aweber_name').val(),
			'aweber_email': jQuery('#aweber_email').val(),
			'aweber_event_id': jQuery('#aweber_event_id').val(),
			'cookie': encodeURIComponent(document.cookie)
		},
		function(response) {
			response = jQuery.parseJSON(response);

			if(response.message != ''){
				jQuery('#message').remove();
				jQuery('#event-actions').before('<div id="message" class="'+ response.status +'"><p>'+ response.message +'</p></div>');
			}
			
			jQuery('#aweber_name,#aweber_email').val('');
			
			submit.removeClass('loading');
		});
		
		return false;
	});});