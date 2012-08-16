(function ($) {
$(function () {

// Load KML overlay
$(document).bind("agm_google_maps-user-map_initialized", function (e, map, data) {
	var url = '';
	try { url = data.kml_url ? data.kml_url : ''; } catch (e) { url = ''; }
	if (!url) return false;
	
	var kml = new google.maps.KmlLayer(url);
	$(document).data("kml_overlay", kml);
	kml.setMap(map);
});

});
})(jQuery);