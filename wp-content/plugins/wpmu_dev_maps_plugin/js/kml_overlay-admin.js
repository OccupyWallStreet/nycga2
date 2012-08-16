(function ($) {
$(function () {

// Add options
$(document).bind("agm_google_maps-admin-markup_created", function (e, el, data) {
	var url = '';
	try { url = data.kml_url ? data.kml_url : ''; } catch (e) { url = ''; }
	el.find("#agm_mh_options").append(
		'<fieldset id="agm-kml_url_overlay">' +
			'<legend>KML Overlay</legend>' +
			'<label for="agm-kml_url">KML file URL</label>' +
			'<input type="text" id="agm-kml_url" class="widefat" value="' + url + '" />' +
		'</fieldset>'
	);
});

// Save KML URL
$(document).bind("agm_google_maps-admin-save_request", function (e, request) {
	request.kml_url = $("#agm-kml_url").val();
});

// Load KML overlay
$(document).bind("agm_google_maps-admin-map_initialized", function (e, map, data) {
	var url = '';
	try { url = data.kml_url ? data.kml_url : ''; } catch (e) { url = ''; }
	if (!url) return false;
	
	var kml = new google.maps.KmlLayer(url);
	$(document).data("kml_overlay", kml);
	kml.setMap(map);
});

$(document).bind("agm_google_maps-admin-options_dialog-closed", function (e, map) {
	var url = $("#agm-kml_url").val();
	if (!url) return false;
	
	var oldKml = $(document).data("kml_overlay");
	if (oldKml) oldKml.setMap(null);

	var kml = new google.maps.KmlLayer(url);
	kml.setMap(map);
});

});
})(jQuery);