(function ($) {

/**
 * Shows/hides attendance information.
 */
function toggle_rsvps () {
	var $rsvps = $("#eab-events-fpe-rsvps-wrapper");
	if ($rsvps.is(":visible")) $rsvps.slideUp('slow');
	else $rsvps.slideDown('slow');
	return false;
}

/**
 * Shows Fee box if the event is premium.
 */
function toggle_fee () {
	var $select = $("#eab-events-fpe-is_premium");
	if (!$select.val()) return false;
	
	var $fee = $("#eab-events-fpe-event_fee-wrapper");
	
	var is_premium = parseInt($select.val());
	if (is_premium) $fee.show();
	else $fee.hide(); 
}

/**
 * Shows missing date/time-specific error.
 */
function missing_datetime_error ($erroneous) {
	if ($("#eab-events-fpe-date_time-error").length) $("#eab-events-fpe-date_time-error").remove();
	$("#eab-events-fpe-date_time").append(
		'<div id="eab-events-fpe-date_time-error">' +
			l10nFpe.mising_time_date + 
		'</div>'
	);
	return false;
}

/**
 * Shows invalid date/time-specific error.
 */
function invalid_datetime_error ($erroneous) {
	if ($("#eab-events-fpe-date_time-error").length) $("#eab-events-fpe-date_time-error").remove();
	$("#eab-events-fpe-date_time").append(
		'<div id="eab-events-fpe-date_time-error">' +
			l10nFpe.check_time_date + 
		'</div>'
	);
	return false;
}

/**
 * Shows general purpose level messages.
 */
function show_message (msg, is_error) {
	var cls = is_error ? 'eab-events-fpe-error' : 'eab-events-fpe-success';
	$(".eab-events-fpe-notification").remove();
	$("#eab-events-fpe-ok_cancel").append(
		'<div class="eab-events-fpe-notification ' + cls + '"><p>' +
			msg +
		'</p></div>'
	);
	setTimeout(function () {
		$(".eab-events-fpe-notification").slideUp("slow");
	}, 2000);
}

/**
 * Normalizes a time string into 24-hours format array.
 * @param string Time string to normalilze
 * @return array [HH,mm] 
 */
function _time_string_to_array (time_string) {
	var time_parts = false;
	if (time_string.match(/[ap]m/i)) { // Yanks have been here
		var is_night = time_string.match(/pm/i);
		var normalized = time_string.replace(/[ap]m/i, '');
		time_parts = (normalized.indexOf(':') >= 0) 
			? normalized.split(':') 
			: [normalized, '00']
		;
		if (is_night) {
			time_parts[0] = 12 + parseInt(time_parts[0]);
		}
	} else { 	
		time_parts = (time_string.indexOf(':') >= 0) 
			? time_string.split(':') 
			: [time_string, '00']
		;
	}
	return time_parts ? time_parts : ['12', '00'];
}

/**
 * Sends save request and shows general message
 */
function send_save_request () {
	if ($("#eab-events-fpe-date_time-error").length) $("#eab-events-fpe-date_time-error").remove();

	var $start_date = $("#eab-events-fpe-start_date");
	if (!$start_date.val()) return missing_datetime_error($start_date);
	
	var $start_time = $("#eab-events-fpe-start_time");
	if (!$start_time.val()) return missing_datetime_error($start_time);
	
	var start = new Date($start_date.val());
	var start_time_parts = _time_string_to_array($start_time.val());

	start.setHours(start_time_parts[0]);
	start.setMinutes(start_time_parts[1]);
	
	var $end_date = $("#eab-events-fpe-end_date");
	if (!$end_date.val()) return missing_datetime_error($end_date);
	
	var $end_time = $("#eab-events-fpe-end_time");
	if (!$end_time.val()) return missing_datetime_error($end_time);
	
	var end = new Date($end_date.val());
	var end_time_parts = _time_string_to_array($end_time.val());
	end.setHours(end_time_parts[0]);
	end.setMinutes(end_time_parts[1]);
	
	if (start >= end) return invalid_datetime_error();  
	
	$("#eab-events-fpe-ok").after(
		'<img src="' + _eab_events_fpe_data.root_url + '/waiting.gif" id="eab-events-fpe-waiting_indicator" />'
	)
	var content = $("#eab-events-fpe-content").is(":visible") ? $("#eab-events-fpe-content").val() : tinyMCE.activeEditor.getContent();
	var data = {
		"id": $("#eab-events-fpe-event_id").val(),
		"title": $("#eab-events-fpe-event_title").val(),
		"content": content,
		"start": $start_date.val() + ' ' + start_time_parts.join(':'),
		"end": $end_date.val() + ' ' + end_time_parts.join(':'),
		"venue": $("#eab-events-fpe-venue").val(),
		"status": $("#eab-events-fpe-status").val(),
		"is_premium": $("#eab-events-fpe-is_premium").val(),
		"category": $("#eab-events-fpe-categories").val(),
		"fee": $("#eab-events-fpe-event_fee").val()
	};
	$(document).trigger('eab-events-fpe-save_request', [data]);

	// Start sending!!
	$.post(_eab_events_fpe_data.ajax_url, {
		"action": "eab_events_fpe-save_event",
		"data": data
	}, function (response) {
		$("#eab-events-fpe-waiting_indicator").remove();
		var status = false;
		var message = false;
		try { status = parseInt(response.status); } catch (e) { status = 0; }
		try { message = response.message; } catch (e) { message = false; }
		if (!status) return show_message((message ? message : l10nFpe.general_error), true);
		
		var post_id = false;
		try { post_id = parseInt(response.post_id); } catch (e) { post_id = 0; }
		if (!post_id) return show_message((message ? message : l10nFpe.missing_id), true);
		
		var link = false;
		try { link = response.permalink; } catch (e) { link = false; }
		if (link) {
			$("#eab-events-fpe-back_to_event").attr("href", link).show();
		}
		
		$("#eab-events-fpe-event_id").val(post_id);
		return show_message((message ? message : l10nFpe.all_good), false);
	});
	return false;
}
	

// Init
$(function () {
	
	$("#fpe-editor").append($("#fpe-editor-root"));
	$("#fpe-editor-root").show();
	
	// Toggle RSVPs
	$("#eab-events-fpe-toggle_rsvps").click(toggle_rsvps);
	
	// Init date pickers
	$("#eab-events-fpe-start_date, #eab-events-fpe-end_date").datepicker({
		minDate: 0,
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		changeYear: true
	});
	
	// Init Fee toggling
	$("#eab-events-fpe-is_premium").change(toggle_fee);
	toggle_fee();
	
	// Init save request processing
	$("#eab-events-fpe-ok").click(send_save_request);
});
	
	
})(jQuery);
