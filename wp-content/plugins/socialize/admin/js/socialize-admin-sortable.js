jQuery(document).ready(function($) {
	$( "#inline-sortable" ).sortable({
		placeholder: "ui-socialize-highlight"
	});
	$( "#alert-sortable" ).sortable({
		placeholder: "ui-socialize-highlight"
	});
	$( "#inline-sortable" ).disableSelection();
	$( "#alert-sortable" ).disableSelection();
});