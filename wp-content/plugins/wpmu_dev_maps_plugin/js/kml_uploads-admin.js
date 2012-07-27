(function ($) {
$(function () {

// Add options
$(document).bind("agm_google_maps-admin-markup_created", function (e, el, data) {
	var html = '<label for="agm-kml_uploaded_file">Select a KML file</label>';
	$.post(ajaxurl, {
		"action": "agm_list_kml_uploads"
	}, function (data) {
		html += '<select class="widefat" id="agm-kml_uploaded_file">';
		html += '<option value="">Select a KML file</option>';
		$.each(data, function (file, url) {
			html += '<option value="' + url + '">' + file + '</option>';
		});
		html += '</select>';
		if (!$("#agm-kml_uploader").length) $("#agm-kml_url_overlay").append('<div id="agm-kml_uploader"></div>');
		$("#agm-kml_uploader").html(html);
		$("#agm-kml_uploaded_file").change(function () {
			$("#agm-kml_url").val($(this).val());
		});
	});
});

});
})(jQuery);