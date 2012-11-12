/**
 * Global handler object variable.
 * Initiated as global, will be bound as object on document.load.
 */
var AgmMapHandler;

(function($){
$(function() {
	
/**
 * Public side map handler.
 * Responsible for rendering maps on public facing pages.
 * 
 * @param selector Container element selector string.
 * @param data Map data object.
 */
AgmMapHandler = function (selector, data) {
	var map;
	var directionsDisplay;
	var directionsService;
	var panoramioImages;
	var travelType;
	var mapId = 'map_' + Math.floor(Math.random()* new Date().getTime()) + '_preview';
	var $container = $(selector);
	var $alignmentContainer;
	var _markers = [];
	var _panoramioLayer = false;
	
	var closeDirections = function () {
		$(selector + ' .agm_mh_directions_container').remove();
		return false;
	};
	
	var createDirectionsMarkup = function () {
		var html = '<div class="agm_mh_directions_container agm_mh_container">';
		html += '<div style="width:300px">' +
					'<span style="float:right"><input type="button" class="agm_mh_close_directions" value="' + l10nStrings.close + '" /> </span>' +
					'<div>' +
						'<a href="#" class="agm_mh_travel_type"><img src="' + _agm_root_url + '/img/system/car_on.png"></a>' +
						'<a href="#" class="agm_mh_travel_type"><img src="' + _agm_root_url + '/img/system/bike_off.png"></a>' + 
						'<a href="#" class="agm_mh_travel_type"><img src="' + _agm_root_url + '/img/system/walk_off.png"></a>' +
					'</div>' +
				'</div>' +
			'<div>' +
				'<img src="' + _agm_root_url + '/img/system/a.png">' +
				'&nbsp;' +
				'<input size="32" type="text" class="agm_waypoint_a" />' +
			'</div>' +
			'<div><a href="#" class="agm_mh_swap_direction_waypoints"><img src="' + _agm_root_url + '/img/system/swap.png"></a></div>' +
			'<div>' +
				'<img src="' + _agm_root_url + '/img/system/b.png">' +
				'&nbsp;' +
				'<input size="32" type="text"  class="agm_waypoint_b" />' +
			'</div>' +
			'<div>' +
				'<input type="button" class="agm_mh_get_directions" value="' + l10nStrings.get_directions + '" />' +
			'</div>' +
			'<div class="agm_mh_directions_panel agm_mh_container">' +
			'</div>' 
		;
		html += '</div>';
		$container.append(html);
	};
	
	var togglePanoramioLayer = function () {
		if (data.show_panoramio_overlay && parseInt(data.show_panoramio_overlay)) {
			var tag = data.panoramio_overlay_tag;
			_panoramioLayer = new google.maps.panoramio.PanoramioLayer();
			if (tag) _panoramioLayer.setTag(tag);
			_panoramioLayer.setMap(map);
		} 
	};
	
	var switchTravelType = function () {
		var $me = $(this);
		var $meImg = $me.find('img');
		var $allImg = $(selector + ' .agm_mh_travel_type img');
		$allImg.each(function () {
			$(this).attr('src', $(this).attr('src').replace(/_on\./, '_off.'));
		});
		if ($meImg.attr('src').match(/car_off\.png/)) {
			travelType = google.maps.DirectionsTravelMode.DRIVING;
		} else if ($meImg.attr('src').match(/bike_off\.png/)) {
			travelType = google.maps.DirectionsTravelMode.BICYCLING;
		} else if ($meImg.attr('src').match(/walk_off\.png/)) {
			travelType = google.maps.DirectionsTravelMode.WALKING;
		}
		$meImg.attr('src', $meImg.attr('src').replace(/_off\./, '_on.'));
		return false;
	};
	
	var setDirectionWaypoint = function () {
		if (!$(selector + ' .agm_mh_directions_container').is(':visible')) createDirectionsMarkup();
		var id = extractMarkerId($(this).attr('href'));
		var marker = _markers[id];
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				$(selector + ' .agm_waypoint_a').val(results[0].formatted_address);
			}
			else alert(l10nStrings.geocoding_error);
		});
		return false;
	};
	
	var swapWaypoints = function () {
		var tmpA = $(selector + ' .agm_waypoint_a').val();
		$(selector + ' .agm_waypoint_a').val($(selector + ' .agm_waypoint_b').val());
		$(selector + ' .agm_waypoint_b').val(tmpA);
		return false;
	};
	
	var getDirections = function () {
		var locA = $(selector + ' .agm_waypoint_a').val();
		var locB = $(selector + ' .agm_waypoint_b').val();
		if (!locA || !locB) {
			alert(l10nStrings.missing_waypoint);
			return false;
		}
		var request = {
			"origin": locA, 
    		"destination": locB,
    		"unitSystem": data.defaults.units,
    		"travelMode": travelType
		};
		directionsDisplay.setPanel($(selector + ' .agm_mh_directions_panel').get(0));
		directionsService.route(request, function(result, status) {
			if (status == google.maps.DirectionsStatus.OK) directionsDisplay.setDirections(result);
			else alert(l10nStrings.oops_no_directions); 
		});
		return false;
	};
	
	var plotRoutes = function () {
		var old = false;
		$.each(_markers, function (idx, mark) {
			if (!old) {
				old = mark;
				return true; // Skip if no previous marker
			}
			var request = {
				"origin": old.getPosition(), 
	    		"destination": mark.getPosition(),
	    		"travelMode": google.maps.DirectionsTravelMode.DRIVING
			};
			var dd = new google.maps.DirectionsRenderer({
				"draggable": true
			});
			var ds = new google.maps.DirectionsService();
			dd.setMap(map);
			ds.route(request, function(result, status) {
				if (status == google.maps.DirectionsStatus.OK) dd.setDirections(result);
			});
			old = mark;
			
		});
	};
	
	var addNewMarker = function (title, pos, body, icon) {
		var idx = _markers.length;
		map.setCenter(pos);
		var marker = new google.maps.Marker({
			title: title,
            map: map, 
            icon: _agm_root_url + '/img/' + icon,
            draggable: false,
            clickable: true,
            position: pos
        });
		var infoContent = '<div class="agm_mh_info_content">' +
			'<div class="agm_mh_info_title">' + title + '</div>' + 
    		'<img class="agm_mh_info_icon" src="' + _agm_root_url + '/img/' + icon + '" />' +
    		'<div class="agm_mh_info_body">' + body + '</div>' +
    		createDirectionsLink(idx) +
    		createLinksToPostsMarkup(idx,1) +
    	'</div>';
		var info = new google.maps.InfoWindow({
		    content: infoContent 
		});
		google.maps.event.addListener(marker, 'click', function() {
			info.open(map, marker);
		});
		marker._agmBody = body;
		marker._agmInfo = info;
		_markers[idx] = marker;
		updateMarkersListDisplay();
	};
	
	var addMarkers = function () {
		if (!data.markers) return;
		$.each(data.markers, function (idx, marker) {
			addNewMarker(marker.title, new google.maps.LatLng(marker.position[0], marker.position[1]), marker.body, marker.icon);
		});
	};
	
	var extractMarkerId = function (href) {
		var id = href.replace(/[^0-9]+/, '');
		return parseInt(id);
	};
	
	var centerToMarker = function () {
		var $me = $(this);
		var id = extractMarkerId($me.attr('href'));
		var m = _markers[id];
		map.setCenter(m.getPosition());
		
		if (parseInt(data.street_view)) {
			var panorama = map.getStreetView();
			panorama.setPosition(map.getCenter());
			panorama.setVisible(true);
		}
		
		return false;
	};
	
	var createDirectionsLink = function (idx) {
		return '<a href="#agm_mh_marker-' + idx + '" class="agm_mh_marker_item_directions">' + l10nStrings.directions + '</a>';
	};
	
	var createLinksToPostsMarkup = function (idx,i) {
		if (!"show_posts" in data || !data.show_posts || !parseInt(data.show_posts)) return '';
		return '<div class="agm_post_links_container"><input type="hidden" value="' + idx + '" /></div>';
	};
	
	var updateMarkersListDisplay = function () {
		if (!data.show_markers || !parseInt(data.show_markers)) return false;
		var html = '<ul class="agm_mh_marker_list">';
		$.each(_markers, function (idx, mark) {
			html += '<li style="clear:both">' +
				'<a href="#agm_mh_marker-' + idx + '" class="agm_mh_marker_item">' + 
					'<img src="' + mark.getIcon() + '" />' +
					'<div class="agm_mh_marker_item_content">' +
						'<div><b>'+ mark.getTitle() + '</b></div>' +
						'<div>' + mark._agmBody + '</div>' +
					'</div>' +
				'</a>' +
				createDirectionsLink(idx) +
				createLinksToPostsMarkup(idx) +
				'<div style="clear:both"></div>' +
			'</li>';
		});
		html += '</ul>';
		$('#agm_mh_markers_' + mapId).html(html);
	};
	
	var populateLinksToPostsMarkup = function () {
		if (!"show_posts" in data || !data.show_posts || !parseInt(data.show_posts)) return false;
		$(selector + ' .agm_post_links_container').each(function () {
			var $me = $(this);
			var mid = $me.find('input:hidden').val();
			if (!mid) return true;
			var marker = data.markers[mid];
			if (!marker) return true;
			var post_ids = false;
			if (!"post_ids" in marker || !marker.post_ids || !marker.post_ids.length) post_ids = data.post_ids;
			else post_ids = marker.post_ids;
			if (!post_ids) return true;
			$.post(_agm_ajax_url, {"action": "agm_get_post_titles", "post_ids": post_ids}, function (data) {
				if (!data.posts) return true;
				var html = '<div class="agm_associated_posts_list_title">' + l10nStrings.posts + '</div>'; 
				html += '<ul class="agm_associated_posts_list_items">';
				var style = '';
				$.each(data.posts, function (idx, post) {
					if (idx > 2) style = 'style="display:none;"';
					html += '<li ' + style + '><a href="' + post.permalink + '">' + post.post_title + '</a></li>';
				});
				html += '</ul>';
				if (style) {
					html += '<a href="#" class="agm_toggle_hidden_post_links">' + l10nStrings.showAll + '</a>';
				}
				// Update marker in the list
				$me.html(html);
				// Update Info Popup
				var mapMarker = _markers[mid];
				var $old = $(mapMarker._agmInfo.getContent());
				$old.find('.agm_post_links_container').html(html);
				var markup = '<div class="agm_mh_info_content">' + $old.html() + '</div>';
				mapMarker._agmInfo.setContent(markup);
				
				$('.agm_toggle_hidden_post_links').live('click', function () {
					var $me = $(this).parents('.agm_post_links_container').first();
					if ($me.find('.agm_associated_posts_list_items li:hidden').length) {
						$me.find('.agm_associated_posts_list_items li:hidden').show();
						$(this).text(l10nStrings.hide);
					} else {
						$me.find('.agm_associated_posts_list_items li:gt(2)').hide();
						$(this).text(l10nStrings.showAll);
					}
					return false;
				});
			});
		});
	};
	
	var init = function () {
		try {
			var width = (parseInt(data.width) > 0) ? data.width : data.defaults.width;
			var height = (parseInt(data.height) > 0) ? data.height : data.defaults.height;
		} catch (e) {
			var width = (parseInt(data.width) > 0) ? data.width : 200;
			var height = (parseInt(data.height) > 0) ? data.height : 200;
		}
		width = (width.toString().indexOf('%')) ? width : parseInt(width); // Support percentages
		try {
			data.defaults.units = ("units" in data.defaults) ? google.maps.UnitSystem[data.defaults.units] : google.maps.UnitSystem.METRIC;
		} catch (e) {
			data.defaults.units = google.maps.UnitSystem.METRIC;
		}

		data.zoom = parseInt(data.zoom) ? parseInt(data.zoom) : 1;
		data.map_type = (data.map_type) ? data.map_type : 'ROADMAP';
		data.map_alignment = data.map_alignment || data.defaults.map_alignment;
		data.image_size = data.image_size || data.defaults.image_size;
		data.image_limit = data.image_limit || data.defaults.image_limit;
		
		data.show_panoramio_overlay = ("show_panoramio_overlay" in data) ? data.show_panoramio_overlay : 0; 
		data.panoramio_overlay_tag = ("panoramio_overlay_tag" in data) ? data.panoramio_overlay_tag : ''; 
		
		data.street_view = ("street_view" in data) ? data.street_view : 0;
		
		$container.wrap('<div id="map_' + mapId + '_alignment_container"></div>');
		$alignmentContainer =  $('#map_' + mapId + '_alignment_container');
		
		$container.html('<div id="' + mapId + '"></div>');
		$('#' + mapId)
			//.width(parseInt(width))
			.width(width)
			.height(parseInt(height))
		;
		$container
			//.width(parseInt(width))
			.width(width)
		;
		if (!data.show_map || !parseInt(data.show_map)) $('#' + mapId).css({
			"position": "absolute",
			"left": "-120000px"
		});
		map = new google.maps.Map($('#' + mapId).get(0), {
			"zoom": parseInt(data.zoom),
			"center": new google.maps.LatLng(40.7171, -74.0039), // New York
			"mapTypeId": google.maps.MapTypeId[data.map_type]
		});
		directionsDisplay = new google.maps.DirectionsRenderer({
			"draggable": true
		});
		directionsService = new google.maps.DirectionsService();
		directionsDisplay.setMap(map);
		travelType = google.maps.DirectionsTravelMode.DRIVING;
		$container.append(
			'<div id="agm_mh_footer">' +
				'<div class="agm_mh_container" id="agm_mh_markers_' + mapId + '">' +
				'</div>' +
			'</div>'
		);
		if ("show_links" in data && parseInt(data.show_links)) {
			$container.append('<div class="agm_upgrade_link"><small><a href="http://premium.wpmudev.org/project/wordpress-google-maps-plugin">Created by the WordPress Google Maps plugin</a></small></div>');
		}
		addMarkers();
		togglePanoramioLayer();
		populateLinksToPostsMarkup();
		
		if (parseInt(data.street_view)) {
			var panorama = map.getStreetView();
			var pos = data.street_view_pos ? new google.maps.LatLng(data.street_view_pos[0], data.street_view_pos[1]) : map.getCenter();
			panorama.setPosition(pos);
			if (data.street_view_pov) {
				panorama.setPov({
					"heading": parseInt(data.street_view_pov.heading),
					"pitch": parseInt(data.street_view_pov.pitch),
					"zoom": parseInt(data.street_view_pov.zoom)
				});
			}
			panorama.setVisible(true);
		}

		if(data.show_images && parseInt(data.show_images)) {
			panoramioImages = new AgmPanoramioHandler(map, $container, data.image_limit, data.image_size);
			$container.append(panoramioImages.createMarkup());
		}
		var plot_routes = false;
		try { if (data.plot_routes) plot_routes = true; } catch (e) {}
		if (plot_routes) plotRoutes();
		
		// Set alignment
		switch(data.map_alignment) {
			case "right":
				$container.css({"float": "right"});
				break;
			case "center":
				$container.css({"margin": "0 auto"});
				break;
			case "left":
			default:
				$container.css({"float": "left"});
				break;
		}
		$alignmentContainer.append('<div style="clear:both"></div>');
		
		$(selector + ' .agm_mh_travel_type').live('click', switchTravelType);
		$(selector + ' .agm_mh_swap_direction_waypoints').live('click', swapWaypoints);
		$(selector + ' .agm_mh_close_directions').live('click', closeDirections);
		$(selector + ' .agm_mh_get_directions').live('click', getDirections);
		$(selector + ' .agm_mh_marker_item').live('click', centerToMarker);
		$(selector + ' .agm_mh_marker_item_directions').live('click', setDirectionWaypoint);
	};
	
	init();
	
};

/**
 * Local Panoramio handler.
 * If not enabled per map, Panoramio images won't be loaded and this
 * won't be executed.
 * Since it's optional, the handler is not global.
 */
var AgmPanoramioHandler = function (map, $container, limit, size) {
	var containerId = 'agm_panoramio_' + Math.floor(Math.random() * new Date().getTime()) + '_container';
	var images = [];
	var height = 200;
	
	var loadPanoramioScript = function () {
		var bounds = map.getBounds();
		if (!bounds) return setTimeout(loadPanoramioScript, 100);
		var callback = 'func_' + containerId + '_image_handler';
		window[callback] = function (data) {
			images = data.photos;
		};
		var script = document.createElement("script");
		var src = 'http://www.panoramio.com/map/get_panoramas.php?set=full&from=0&to=' + limit +
			'&miny=' + bounds.getSouthWest().lat() + '&minx=' + bounds.getSouthWest().lng() + 
			'&maxy=' + bounds.getNorthEast().lat() + '&maxx=' + bounds.getNorthEast().lng() +
			'&size=' + size +
		'&callback=' + callback;
		script.type = "text/javascript";
		script.src = src;
		document.body.appendChild(script);
	};
	
	var loadGalleriaScript = function () {
		if (window.Galleria && typeof(window.Galleria) == 'function') return true;
		var script = document.createElement("script");
		var src = _agm_root_url + '/js/external/galleria/galleria-1.2.2.min.js';
		script.type = "text/javascript";
		script.src = src;
		document.body.appendChild(script);
	};
	
	var loadGalleriaTheme = function () {
		if (!window.Galleria || typeof(window.Galleria) != 'function') setTimeout(loadGalleriaTheme, 300);
		else {
			if (Galleria && Galleria.theme) return true;
			var script = document.createElement("script");
			var src = _agm_root_url + '/js/external/galleria/themes/classic/galleria.classic.min.js';
			script.type = "text/javascript";
			script.src = src;
			document.body.appendChild(script);
			waitForGalleriaTheme();
		}
	};
	
	var waitForImages = function () {
		if (!images.length) setTimeout(waitForImages, 1000);
		else {
			loadGalleriaTheme();
		}
	};
	var waitForGalleriaTheme = function () {
		if (!window.Galleria || !window.Galleria.theme || typeof(window.Galleria) != 'function' || typeof($('#' + containerId + ' div.agm_panoramio_image_list_container').galleria) != 'function') setTimeout(waitForGalleriaTheme, 300);
		else {
			populateContainer();
			var gheight = height ? height : 200;
			$('#' + containerId + ' div.agm_panoramio_image_list_container').galleria({
		        width: $container.width(),
		        height: gheight
		    });
		}
	};
	
	var createMarkup = function () {
		return '<div class="agm_panoramio_container" id="' + containerId + '">' +
		'</div>';
	};
	
	var populateContainer = function () {
		if (!$('#' + containerId).length) return false;
		var html = '<div class="agm_panoramio_image_list_container"><ul class="agm_panoramio_image_list">';
		var totalImageWidth = 0;
		$.each(images, function(idx, img) {
			var imgh = parseInt(img.height);
			height = (imgh > height) ? imgh : height;
			html += '<li>' +
				'<img src="' + img.photo_file_url + '" title="' + img.photo_title + '" />' +
			'</li>';
			totalImageWidth += img.width;
		});
		html += '</ul></div>';
		$('#'+containerId).html(html);
	};
	
	var init = function () {
		loadPanoramioScript();
		loadGalleriaScript();
		waitForImages();
	};
	
	init();
	
	return {
		"createMarkup": createMarkup
	};
};
	
/**
 * Uses global _agmMaps array to create the needed map objects.
 * Deferres AgmMapHandler creation until Google Maps API is available.
 */
function createMaps () {
	if (!_agmMapIsLoaded) {
		setTimeout(createMaps, 100);
	} else {
		$.each(_agmMaps, function (idx, map) {
			new AgmMapHandler(map.selector, map.data);
		});
	}
}

// Create map objects on document.load,
// or as soon as we're able to
createMaps();


});
})(jQuery);