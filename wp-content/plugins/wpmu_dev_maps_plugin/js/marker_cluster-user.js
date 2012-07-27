(function ($) {
$(function () {

$(document).bind("agm_google_maps-user-map_initialized", function (e, map, data, markers) {
	if (!markers || !markers.length) return;
	var markerCluster = new MarkerClusterer(map, markers, {"zoomOnClick": false, "gridSize": 30});

	google.maps.event.addListener(markerCluster, "click", function (c) {
		var clustered = c.getMarkers();
		var contents = '';
		$.each(clustered, function () {
			if ("_agmInfo" in this) {
				contents += this._agmInfo.getContent();
				contents += "<hr style='clear:both' />";
			}
		});
		var info = new google.maps.InfoWindow({
		    content: contents 
		});
		info.open(map, clustered[0]);
	});
});

});
})(jQuery);