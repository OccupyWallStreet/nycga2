var map;
var mark;
var markersArray = [];

function checkAll(form) {
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].name == "be[]") {
				if(form.elements[i].checked == true)
					form.elements[i].checked = false;
				else
					form.elements[i].checked = true;
			}
		}
	}
	i = null;
}

function def_map_initialize() {
	var latlng = new google.maps.LatLng(mapLat, mapLng);
	
	var mapOptions = {
		zoom: mapZoom,
		center: latlng,
		mapTypeId: google.maps.MapTypeId[mapType]
	}
	map = new google.maps.Map(document.getElementById("default-loc-map"), mapOptions);

	mark = new google.maps.Marker({
		position: latlng, 
		map: map
	});

	google.maps.event.addListener(map, 'click', function(event) {
		var coords = event.latLng;
		mark.setMap(null);
		placeMarker(coords);
		
		coords = String(coords);
		coords = coords.split(', ');
		
		jQuery('#map_location_lat').val(coords[0].substr( 1, coords[0].length ));
		jQuery('#map_location_lng').val(coords[1].substr( 0, coords[1].length - 1 ));
	});
}

function placeMarker(location) {
	deleteOverlays();
	var marker = new google.maps.Marker({
		position: location, 
		map: map
	});
	
	markersArray.push(marker);
	map.setCenter(location);
}

function deleteOverlays() {
	if (markersArray) {
		for (i in markersArray) {
			markersArray[i].setMap(null);
		}
		markersArray.length = 0;
	}
}

jQuery(document).ready(function(){
	jQuery('#bpe-slugs').hide();
	jQuery('#toggle-slugs').click(function() {
		jQuery('#bpe-slugs').toggle();
		return false;
	});
	
	jQuery('.enable_ext_api').each(function() {
		var api = jQuery(this).attr('id');
		api = api.split('_');
		api = api[1];
	
		if( jQuery(this).is(':checked') ){
			jQuery('.'+ api +'_hide').show();
		} else {
			jQuery('.'+ api +'_hide').hide();
		}
	});

	jQuery('.enable_ext_api').click(function() {
		var api = jQuery(this).attr('id');
		api = api.split('_');
		api = api[1];

		if( jQuery(this).is(':checked') ){
			jQuery('.'+ api +'_hide').show();
		} else {
			jQuery('.'+ api +'_hide').hide();
		}
	});
	
	def_map_initialize();

	var menutabs = jQuery( "ul#menu-tabs" ).sortable( {
		cursor: 'move',
		axis: 'y',
		items: 'li',
		update: function(e, ui){
			var tab_order = menutabs.sortable("toArray").join();
			jQuery('input#tab_order').val(tab_order);
			
			var first = tab_order.split(',');
			first = first[0];
			
			jQuery('.deactivate-tab').removeAttr('disabled');
			jQuery('#'+ first +'-tab').attr("disabled","disabled").removeAttr('checked');
		}
	});
});