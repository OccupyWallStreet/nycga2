function resetNewsletterServices(){
	var hide = jQuery('input.show_service').filter(':not(:checked)');

	jQuery(hide).each( function(){
		var el = jQuery(this).parent('.show_service_wrap').next('.news-right');

		el.css({ opacity: 0.3 });
		el.find('input,select').prop('disabled', 'disabled');
		el.find('a.button').hide();
	});
}

jQuery(document).ready(function() {
	// get lists from AWeber
	jQuery('#aweber_fetch_lists').click( function() {
		var link = jQuery(this);
		link.addClass('loading');
		
		jQuery.post( ajaxurl, {
			'aweber_api_key': jQuery('#aweber_api_key').val(),
			'action': 'bpe_aweber_get_lists',
			'nl_event_id' : jQuery('#nl_event_id').val(),
			'_bpe_aweber_nonce': jQuery('#_bpe_aweber_nonce').val(),
			'cookie': encodeURIComponent(document.cookie)
		},
		function(response) {
			response = jQuery.parseJSON(response);
			
			if(response.message != ''){
				jQuery('#aweber_list_response').empty().html('<div id="message" class="'+ response.status +'"><p>'+ response.message +'</p></div>');
			}
			
			jQuery('#aweber_lists').empty().html(response.html);
			link.removeClass('loading');
			
			if(response.remove == 'yes')
				link.remove();
		});
		
		return false;
	});

	// get lists from Mailchimp
	jQuery('#mailchimp_fetch_lists').click( function() {
		var link = jQuery(this);
		link.addClass('loading');
		
		jQuery.post( ajaxurl, {
			'mailchimp_api_key': jQuery('#mailchimp_api_key').val(),
			'action': 'bpe_mailchimp_get_lists',
			'_bpe_mailchimp_nonce': jQuery('#_bpe_mailchimp_nonce').val(),
			'cookie': encodeURIComponent(document.cookie)
		},
		function(response) {
			response = jQuery.parseJSON(response);
			
			if(response.message != ''){
				jQuery('#mailchimp_list_response').empty().html('<div id="message" class="'+ response.status +'"><p>'+ response.message +'</p></div>');
			}
			
			jQuery('#mailchimp_lists').empty().html(response.html);
			link.removeClass('loading');
			
			if(response.remove == 'yes')
				link.remove();
		});
		
		return false;
	});

	// get clients from Campaign Monitor
	jQuery('#cmonitor_fetch_clients').click( function() {
		var link = jQuery(this);
		link.addClass('loading');
		
		jQuery.post( ajaxurl, {
			'cmonitor_api_key': jQuery('#cmonitor_api_key').val(),
			'action': 'bpe_cmonitor_get_clients',
			'_bpe_cmonitor_nonce': jQuery('#_bpe_cmonitor_nonce').val(),
			'cookie': encodeURIComponent(document.cookie)
		},
		function(response) {
			response = jQuery.parseJSON(response);
			
			if(response.message != ''){
				jQuery('#cmonitor_list_response').empty().html('<div id="message" class="'+ response.status +'"><p>'+ response.message +'</p></div>');
			}
			
			jQuery('#cmonitor_response').empty().html(response.html);
			link.removeClass('loading');
			
			if(response.remove == 'yes')
				link.remove();
		});
		
		return false;
	});

	// get lists from Campaign Monitor
	jQuery('#cmonitor_fetch_lists').live( 'click', function() {
		var link = jQuery(this);
		link.addClass('loading');
		
		jQuery.post( ajaxurl, {
			'cmonitor_api_key': jQuery('#cmonitor_api_key').val(),
			'cmonitor_client_id': jQuery('#cmonitor_client_id').val(),
			'_bpe_cmonitor_nonce': jQuery('#_bpe_cmonitor_nonce').val(),
			'action': 'bpe_cmonitor_get_lists',
			'cookie': encodeURIComponent(document.cookie)
		},
		function(response) {
			response = jQuery.parseJSON(response);
			
			if(response.message != ''){
				jQuery('#cmonitor_list_response').empty().html('<div id="message" class="'+ response.status +'"><p>'+ response.message +'</p></div>');
			}
			
			jQuery('#cmonitor_response').empty().html(response.html);
			link.removeClass('loading');
			
			if(response.remove == 'yes')
				link.remove();
		});
		
		return false;
	});

	if(jQuery('.news-wrap').length > 1){
		resetNewsletterServices();
	}
	
	// highlight a checked service
	jQuery('.show_service').click( function() {
		var service = jQuery(this).parent('.show_service_wrap').next('.news-right');
		
		resetNewsletterServices();
		jQuery('.news-right #message').remove();
		
		service.find('input,select').removeAttr('disabled');
		service.find('a.button').show();
		service.animate({opacity: 1},1500);
	});
});