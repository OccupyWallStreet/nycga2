(function ($) {

function append_meta_data (e, request) {
	request['eab_event-bp-group_event'] = $("#eab_event-bp-group_event").val();
}

$(document).bind('eab-events-fpe-save_request', append_meta_data);
	
})(jQuery);
