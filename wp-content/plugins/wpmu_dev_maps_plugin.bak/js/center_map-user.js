(function ($) {
$(function () {

// Center map
$(document).bind("agm_google_maps-user-map_initialized", function (e, map, data) {
	var center = false;
	try { center = data.center; } catch (e) { center = false; }
	if (!center) return false;
	
	var lat = false, lng = false;
	try { lat = parseFloat(center.latitude); } catch (e) { lat = false; }
	try { lng = parseFloat(center.longitude); } catch (e) { lng = false; }
	if (!lat || !lng) return false;
	
	map.setCenter(new google.maps.LatLng(lat,lng));
});

});
})(jQuery);