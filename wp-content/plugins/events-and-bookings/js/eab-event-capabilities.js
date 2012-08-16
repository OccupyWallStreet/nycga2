(function ($) {


function switch_role () {
	var role = $("#eab-event-capabilities-switch_hub").val();
	if (!role) return;
	
	var $role = $("#eab-events-capabilities-editor-"+role);
	if (!$role.length) return;
	
	$(".eab-events-capabilities-per_role").hide();
	$role.show();
}

function reset_roles () {
	$(".eab-events-capabilities-per_role input").attr("disabled", true);
	$("#eab-event-capabilities-switch_hub")
		.val('')
		.attr("disabled", true)
	;
	$(".eab-events-capabilities-per_role").hide();
}

// Init
$(function () {

$("#eab-event-capabilities-switch_hub").change(switch_role);
switch_role();

$("#eab-event-capabilities-reset").click(reset_roles);

});
})(jQuery);
