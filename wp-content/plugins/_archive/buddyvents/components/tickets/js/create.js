jQuery(document).ready( function() {
	jQuery('.add-ticket').live( 'click', function() {
		var link = jQuery(this);
		link.addClass('loading');
		
		var userId = jQuery('#uid').val();
		
		var counter = jQuery('#ticket-counter').val();
		var firstCalDay = jQuery('#first-cal-day').val();

		var today = new Date();
		var maxDate = jQuery('#end_date').val();
		console.log(maxDate);
		maxDate = maxDate.split('-');
		
		jQuery.post( ajaxurl, {
			'action': 'bpe_get_ticket_form_html',
			'cookie': encodeURIComponent(document.cookie),
			'id': counter,
			'user_id': userId
		},
		function(response) {
			response = jQuery.parseJSON(response);
			if(response.type == 'success') {
				jQuery('#ticket-wrapper').append(response.content);

				counter++;
				jQuery('#ticket-counter').val(counter);
				
				jQuery(".ticket-date:not(.hasDatepicker)").datepicker({
					minDate: today,
					maxDate: new Date( maxDate[0], maxDate[1], maxDate[2] ),
					firstDay: firstCalDay,
					changeMonth: false,
					changeYear: false,
					dateFormat: "yy-mm-dd"
				});
			}
			link.removeClass('loading');
		});
		
		return false;
	});
	
	jQuery('.del-ticket').live( 'click', function() {
		jQuery(this).parent('fieldset.event-block').remove();
		return false;
	});
});