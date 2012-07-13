jQuery(document).ready( function() {	
	jQuery('.add-schedule').live( 'click', function() {	
		var link = jQuery(this);
		link.addClass('loading');
		
		var counter = jQuery('#schedule-counter').val();
		var firstCalDay = jQuery('#first-cal-day').val();
		
		var minDate = jQuery("#start_date").val();
		minDate = minDate.split('-');
		
		var maxDate = jQuery("#end_date").val();
		maxDate = maxDate.split('-');
		
		jQuery.post( ajaxurl, {
			'action': 'bpe_get_schedule_form_html',
			'cookie': encodeURIComponent(document.cookie),
			'id': counter
		},
		function(response) {
			response = jQuery.parseJSON(response);
			
			if(response.type == 'success') {
				jQuery('#schedule-wrapper').append(response.content);

				counter++;
				jQuery('#schedule-counter').val(counter);
				
				jQuery(".schedule-date:not(.hasDatepicker)").datepicker({
					firstDay: firstCalDay,
					minDate: new Date( minDate[0], minDate[1] - 1, minDate[2] ),
					maxDate: new Date( maxDate[0], maxDate[1] - 1, maxDate[2] ),
					changeMonth: false,
					changeYear: false,
					dateFormat: "yy-mm-dd"
				});
				jQuery(".time-input:not(.hasDatepicker)").timepicker({});
			}
			link.removeClass('loading');
		});
		
		return false;
	});
	
	jQuery('.del-schedule').live( 'click', function() {
		jQuery(this).parent('fieldset.event-schedule').remove();
		return false;
	});
});