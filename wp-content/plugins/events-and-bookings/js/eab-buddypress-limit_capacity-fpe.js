(function ($) {

function append_meta_data (e, request) {
	request['eab-elc_capacity'] = $("#eab_event_capacity").val();
}

function capacity_change () {
	var $capacity = $("#eab_event_capacity");
	var $unlimited = $("#eab_event_capacity-unlimited");
	var cap = parseInt($capacity.val());
	cap = cap ? cap : 0;
	
	if (cap > 0) {
		$unlimited.attr("checked", false);
	} else {
		$unlimited.attr("checked", true);
	}	
}

function unlimited_change () {
	var $capacity = $("#eab_event_capacity");
	var $unlimited = $("#eab_event_capacity-unlimited");
	
	if ($unlimited.is(":checked")) return $capacity.val(0);
	else return $capacity.focus();
}

$(document).bind('eab-events-fpe-save_request', append_meta_data);
$(function () {
	$("#eab_event_capacity").change(capacity_change);
	$("#eab_event_capacity-unlimited").change(unlimited_change);
});
	
})(jQuery);
