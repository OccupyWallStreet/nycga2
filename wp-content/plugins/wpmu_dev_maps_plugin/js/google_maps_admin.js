/**
 * Global handler object variable.
 * Initiated as global, will be bound as object on document.load.
 */
var AgmMapHandler;

(function($){
$(function() {

/**
 * Admin-side map handler object.
 * Responsible for rendering markup for a particular map,
 * and for requests related to a particular map editing.
 * 
 * Has one public method: destroy(), which is used in Editor interface.
 * 
 * @param selector Container element selector string.
 * @param data Map data object.
 */
AgmMapHandler = function (selector, data, allowInsertion) {
	var _imageSizes = ['medium', 'small', 'thumbnail', 'square', 'mini_square'];
	var map;
	var originalData;
	var $container = $(selector);
	var _markers = [];
	var _panoramioLayer = false;
	// Allow map insertion by default
	allowInsertion = typeof(allowInsertion) != 'undefined' ? allowInsertion : true;
	
	var populateDefaults = function () {
		originalData = data;
		data.zoom = data.zoom || data.defaults.zoom;
		data.zoom = parseInt(data.zoom) ? parseInt(data.zoom) : 1;
		data.show_map = ("show_map" in data) ? data.show_map : 1; 
		data.show_posts = ("show_posts" in data) ? data.show_posts : 0; 
		data.show_markers = ("show_markers" in data) ? data.show_markers : 1; 
		data.show_images = ("show_images" in data) ? data.show_images : 0; 
		data.show_links = ("show_links" in data) ? data.show_links : 1; 

		data.snapping = ("snapping" in data.defaults) ? parseInt(data.defaults.snapping) : 1; 

		data.show_panoramio_overlay = ("show_panoramio_overlay" in data) ? parseInt(data.show_panoramio_overlay) : 0; 
		data.panoramio_overlay_tag = ("panoramio_overlay_tag" in data) ? data.panoramio_overlay_tag : ''; 
		
		data.street_view = ("street_view" in data) ? data.street_view : 0;
		data.street_view_pos = ("street_view_pos" in data) ? data.street_view_pos : 0;
		data.street_view_pov = ("street_view_pov" in data) ? data.street_view_pov : 0;
		
		data.map_type = data.map_type || data.defaults.map_type;
		data.map_alignment = data.map_alignment || data.defaults.map_alignment;
		data.post_ids = data.post_ids || [];
		
		data.image_size = data.image_size || data.defaults.image_size;
		data.image_limit = data.image_limit || data.defaults.image_limit;
	};
	
	var insertMap = function () {
		var id = $('#agm_mh_map_id').val();
		if (!id || $(this).is(':disabled')) {
			alert(l10nStrings.please_save_map);
			return false;
		}
		destroyMap();
		$container.trigger('agm_map_insert', [id]);
	};
	
	var saveMap = function () {
		var title = $('#agm_map_title').val();
		if (!title) {
			alert(l10nStrings.map_name_missing);
			return false;
		}
		var streetView = map.getStreetView();
		
		var width = $('#agm_map_size_x').is(':enabled') ? $('#agm_map_size_x').val() : 0;
		var height = $('#agm_map_size_y').is(':enabled') ? $('#agm_map_size_y').val() : 0;
		var alignment = $('input.agm_map_alignment_element:checked').val();
		var request = {
			"action": "agm_save_map",
			"id": $('#agm_mh_map_id').val(),
			"title": title,
			"height": height,
			"width": width,
			"zoom": map.getZoom(),
			"map_type": map.getMapTypeId(),
			"show_map": $('#agm_map_show_map').is(':checked') ? 1 : 0,
			"show_posts": $('#agm_map_show_posts').is(':checked') ? 1 : 0,
			"map_alignment": alignment,
			"show_markers": $('#agm_map_show_markers').is(':checked') ? 1 : 0,
			"show_images": $('#agm_map_show_images').is(':checked') ? 1 : 0,
			"show_links": $('#agm_map_show_links').is(':checked') ? 1 : 0,
			"image_size": $('#agm_map_image_size').val(),
			"image_limit": $('#agm_map_image_limit').val(),
			"show_panoramio_overlay": $('#agm_map_show_panoramio_overlay').is(':checked') ? 1 : 0,
			"panoramio_overlay_tag": $('#agm_map_panoramio_overlay_tag').val(),
			"street_view": streetView.getVisible() ? 1 : 0,
			"street_view_pos": streetView.getVisible() ? [streetView.getPosition().lat(), streetView.getPosition().lng()] : 0,
			"street_view_pov": streetView.getVisible() ? streetView.getPov() : 0,
			"post_ids": [],
			"markers": []
		};

		$.each($('input.agm_mh_associated_id'), function (idx, el) {
			request.post_ids[request.post_ids.length] = $(el).val();
		});
		if ($('#agm_map_size_associate').is(':checked')) {
			request.post_ids[request.post_ids.length] = $('#post_ID').val();
		}
		$.each(_markers, function (idx, marker) {
			var position = marker.getPosition();
			if (!position) return true;
			var data = {
				"title": marker.getTitle(),
				"body": marker._agmBody,
				"icon": marker.getIcon(),
				"position": [position.lat(), position.lng()]
			};
			request.markers[request.markers.length] = data;
		});
		$.post(ajaxurl, request, function (data) {
			if (!data.status) alert(l10nStrings.map_not_saved);
			else {
				$('#agm_mh_map_id').val(parseInt(data.id));
				$('#agm_insert_map').attr('disabled', false);
				$container.trigger('agm_map_saved', [parseInt(data.id)]);
			}
		});
	};
	
	var loadAssociatedPosts = function () {
		if (!data.post_ids.length) {
			$('#agm_map_associated_posts').html('');
			$('#agm_map_size_associate').attr('checked', false); // Not associated, default to false
			return false;
		}
		$.post(ajaxurl, {"action": "agm_get_post_titles", "post_ids": data.post_ids}, function (data) {
			if (!data.posts) return false;
			var html = '<div class="agm_less_important">' +
				l10nStrings.already_associated_width +
			'<ul class="agm_associated_list">';
			var postId = $('#post_ID').val();
			$.each(data.posts, function (idx, post) {
				html +=  '<li>' + post.post_title + '</li>';
			});
			html += '</ul></div>';
			$('#agm_map_associated_posts').html(html);
		});
		return false;
	};
	
	var createMarkup = function () {
		var ids = '';
		var sizes = '';
		$.each(data.post_ids, function (idx, id) {
			ids += '<input type="hidden" class="agm_mh_associated_id" value="' + id + '" />';
		});
		$.each(_imageSizes, function(idx, size) {
			var selected = (size == data.image_size) ? 'selected="selected"' : '';
			sizes += '<option value="' + size + '" ' + selected + '>' + size + '</option>';
		});
		$container.html(
			'<div id="agm_mh_header">' +
				'<input type="hidden" id="agm_mh_map_id">' +
				'<div class="agm_mh_container">' +
					'<label>' + l10nStrings.add_location + ' </label>' +
					'<input type="text" id="agm_mh_location" />' +
					'<input type="button" class="button-secondary action" id="agm_mh_location_trigger" value="' + l10nStrings.add +'" />' +
					'<div class="agm_less_important">' + l10nStrings.add_location_info + '</div>' +
				'</div>' +
				'<div class="agm_mh_container">' + 
					l10nStrings.map_title + ' <input type="text" size="32" id="agm_map_title" />' + 
				'</div>' +
				'<div class="agm_mh_container">' + 
					'<div id="agm_map_options_controls_container">' +
						'<input type="button" class="button-secondary action" id="agm_map_options" value="' + l10nStrings.options + '" />' +
						'<div id="agm_map_options_help" class="agm_less_important">' + l10nStrings.options_help + '</div>' +
					'</div>' +
					'<div id="agm_map_drop_marker_controls_container">' +
						'<input type="button" class="button-secondary action" id="agm_map_drop_marker" value="' + l10nStrings.drop_marker + '" />' +
						'<div id="agm_map_zoom_in_help" class="agm_less_important">' + l10nStrings.zoom_in_help + '</div>' +
					'</div>' +
				'</div>' +
			'</div>' + 
			'<div id="agm_mh_options" style="display:none">' +
			'<div class="error below-h2"><p><a title="Upgrade Now" href="http://premium.wpmudev.org/project/wordpress-google-maps-plugin">Upgrade to Google Maps Pro to enable additional features and permanently disable links</a></p></div>' +
				'<div>' +
					'<input type="checkbox" id="agm_map_size_associate" value="' + $('#post_ID').val() + '" /> <label for="agm_map_size_associate">' + l10nStrings.map_associate + '</label>' +
					'<div class="agm_less_important">' + l10nStrings.association_help + '</div>' + 
					ids + 
					'<div id="agm_map_associated_posts"></div>' +
				'</div>' +
				'<fieldset><legend>' + l10nStrings.map_size + '</legend>' +
					'<input type="text" size="3" id="agm_map_size_x" />' +
					'&nbsp;x&nbsp;' +
					'<input type="text" size="3" id="agm_map_size_y" />' +
					'<div><input type="checkbox" id="agm_map_size_default" /> <label for="agm_map_size_default">' + l10nStrings.use_default_size + '</label></div>' +
				'</fieldset>' +
				'<fieldset><legend>' + l10nStrings.map_appearance + '</legend>' +
					'<div><input type="checkbox" id="agm_map_show_links" /> <label for="agm_map_show_links">Show links</label></div>' +
					'<div><input type="checkbox" disabled="disabled" id="agm_map_show_posts" /> <label for="agm_map_show_posts">' + l10nStrings.show_posts + '</label></div>' +
					'<div><input type="checkbox" disabled="disabled" id="agm_map_show_map" /> <label for="agm_map_show_map">' + l10nStrings.show_map + '</label></div>' +
					'<div><input type="checkbox" disabled="disabled" id="agm_map_show_markers" /> <label for="agm_map_show_markers">' + l10nStrings.show_markers + '</label></div>' +
					'<div><fieldset>' +
						'<legend>' + l10nStrings.images_strip + '</legend>' +
						'<input type="checkbox" disabled="disabled" id="agm_map_show_images" /> <label for="agm_map_show_images">' + l10nStrings.show_images + '</label>' +
						'<br />' +
						'<label for="agm_map_image_size">' + l10nStrings.image_size + '</label> ' +
						'<select disabled="disabled" id="agm_map_image_size">' +
							sizes +
						'</select>' +
						'<br />' +
						'<label for="agm_map_image_limit">' + l10nStrings.image_limit + '</label> ' +
						'<input type="text" disabled="disabled" size="2" id="agm_map_image_limit" value="' + data.image_limit + '" />' +
					'</fieldset></div>' +
					'<fieldset><legend>' + l10nStrings.panoramio_overlay + '</legend>' +
						'<div><input type="checkbox" disabled="disabled" id="agm_map_show_panoramio_overlay" /> <label for="agm_map_show_panoramio_overlay">' + l10nStrings.show_panoramio_overlay + '</label></div>' +
						'<div><label for="agm_map_panoramio_overlay_tag">' + l10nStrings.panoramio_overlay_tag + '</label> <input type="text" disabled="disabled" size="12" id="agm_map_panoramio_overlay_tag" /></div>' +
						'<div><small>' + l10nStrings.panoramio_overlay_tag_help + '</small></div>' +
					'</fieldset>' +
					'<div><fieldset>' +
						'<legend>' + l10nStrings.map_alignment + '</legend>' +
						'<input type="radio" id="agm_map_alignment_left" name="ama" class="agm_map_alignment_element" value="left" />' +
							'<label for="agm_map_alignment_left"><img src="' + _agm_root_url + '/img/system/left.png" />' + l10nStrings.map_alignment_left + '</label><br/>' +
						'<input type="radio" id="agm_map_alignment_center" name="ama" class="agm_map_alignment_element" value="center" />' +
							'<label for="agm_map_alignment_center"><img src="' + _agm_root_url + '/img/system/center.png" />' + l10nStrings.map_alignment_center + '</label><br/>' +
						'<input type="radio" id="agm_map_alignment_right" name="ama" class="agm_map_alignment_element" value="right" />' +
							'<label for="agm_map_alignment_right"><img src="' + _agm_root_url + '/img/system/right.png" />' + l10nStrings.map_alignment_right + '</label><br/>' +
					'</fieldset></div>' +
				'</fieldset>' +
				'<p class="agm_less_important">Global defaults are configured in Settings &gt; Google Maps</p>' +
			'</div>' +
			'<div id="map_preview"></div>'
		);
		$container.append(
				'<div id="agm_mh_footer">' +
					'<div class="agm_mh_container">' + 
						(allowInsertion ? '<input type="button" class="button-secondary action" id="agm_insert_map" value="' + l10nStrings.insert + '" />' : '') +
						'<input type="button" class="button-primary" id="agm_save_map" value="' + l10nStrings.save + '" />' +
						'<input type="button" class="button-secondary action" id="agm_go_back" value="' + l10nStrings.go_back + '" />' +
					'</div>' +
					'<div class="agm_mh_container" id="agm_mh_markers">' +
					'</div>' +
				'</div>'
		);
		$('#map_preview')
			.width(640)
			//.height(400)
			.height($(window).height() * 0.35) // Using a dynamic map height
		;
		$container.css({
			"position": "absolute",
			"width": "640px",
			"height": "740px",
			"overflow-y": "auto"
		});
		$('#agm_mh_options').dialog({
			"autoOpen": false,
			"title": l10nStrings.options,
			"width": 600,
			"modal": true,
			"buttons": { 
				"OK": function() { $(this).dialog("close"); } 
			}
		});
		
		$('#agm_mh_map_id').val(data.id);
		if (!data.id) $('#agm_insert_map').attr('disabled', true);
		$('#agm_map_title').val(data.title);
		
		var show_map = (data.show_map && parseInt(data.show_map)) ? true : false;
		var show_posts = (data.show_posts && parseInt(data.show_posts)) ? true : false;
		var show_markers = (data.show_markers && parseInt(data.show_markers)) ? true : false;
		var show_images = (data.show_images && parseInt(data.show_images)) ? true : false;
		var show_panoramio_overlay = data.show_panoramio_overlay ? true : false;
		
		var show_links = (data.show_links && parseInt(data.show_links)) ? true : false;
		$('#agm_map_show_links').attr('checked', show_links);
		
		$('#agm_map_show_map').attr('checked', show_map);   
		$('#agm_map_show_posts').attr('checked', show_posts);   
		$('#agm_map_show_markers').attr('checked', show_markers);
		$('#agm_map_show_images')
			.click(function () {
				$('#agm_map_image_size').attr('disabled', !$(this).is(":checked"));
				$('#agm_map_image_limit').attr('disabled', !$(this).is(":checked"));
			})
			.attr('checked', show_images); 
		$('#agm_map_image_size').attr('disabled', !$('#agm_map_show_images').is(":checked"));
		$('#agm_map_image_limit').attr('disabled', !$('#agm_map_show_images').is(":checked"));
		
	
		// agm_map_show_panoramio_overlay
		$('#agm_map_show_panoramio_overlay')
			.click(togglePanoramioLayer)
			.attr('checked', show_panoramio_overlay)
		;
		$('#agm_map_panoramio_overlay_tag')
			.change(function () {
				if (!_panoramioLayer) return true;
				_panoramioLayer.setTag($(this).val());
			})
			.val(data.panoramio_overlay_tag)
		;
		
		switch (data.map_alignment) {
			case "right":
				$('#agm_map_alignment_right').attr('checked', true);
				break;
			case "center":
				$('#agm_map_alignment_center').attr('checked', true);
				break;
			case "left":
			default:
				$('#agm_map_alignment_left').attr('checked', true);
				break;
		}
		
		if (!data.width || !parseInt(data.width)) {
			$('#agm_map_size_default').attr('checked', true);
		}
		toggleDefaultSize();
		$.each(data.post_ids, function (idx, el) {
			if ($('#post_ID').val() == el) $('#agm_map_size_associate').attr('checked', true);
		});
		// Toggle post association/disassociation
		$('#agm_map_size_associate').click(function () {
			var val = $('#agm_map_size_associate').val();
			
			if ($('#agm_map_size_associate').is(':checked')) {
				if (data.post_ids) data.post_ids.push(val);
				else data.post_ids = [val];
			} else {
				$('input.agm_mh_associated_id[value="' + val + '"]').remove();
				if (data.post_ids) data.post_ids = $.grep(data.post_ids, function (el) { return el != val; });
			}
			
			loadAssociatedPosts();
			return true;
		});
	};
	
	var togglePanoramioLayer = function () {
		if ($('#agm_map_show_panoramio_overlay').is(':checked')) {
			var tag = $('#agm_map_panoramio_overlay_tag').val();
			_panoramioLayer = new google.maps.panoramio.PanoramioLayer();
			if (tag) _panoramioLayer.setTag(tag);
			_panoramioLayer.setMap(map);
		} else if (_panoramioLayer) {
			_panoramioLayer.setMap(null);
			_panoramioLayer = false;
		}
	};
	
	var toggleOptions = function () {
		var $opts = $('#agm_mh_options');
		if ($opts.dialog('isOpen')) $opts.dialog('close');
		else $opts.dialog('open');
		return false;
	};
	
	var toggleDefaultSize = function () {
		if ($('#agm_map_size_default').is(':checked')) {
			$('#agm_map_size_x').val(data.defaults.width).attr('disabled', true);
			$('#agm_map_size_y').val(data.defaults.height).attr('disabled', true);
		} else {
			var width = (parseInt(data.width) > 0) ? data.width : data.defaults.width;
			var height = (parseInt(data.height) > 0) ? data.height : data.defaults.height;
			$('#agm_map_size_x').val(width).attr('disabled', false);
			$('#agm_map_size_y').val(height).attr('disabled', false);			
		}
	};
	
	var addMarkers = function () {
		if (!data.markers) return;
		$.each(data.markers, function (idx, marker) {
			addNewMarker(marker.title, new google.maps.LatLng(marker.position[0], marker.position[1]), marker.body, marker.icon);
		});
	};
	
	var dropNewMarker = function () {
		addNewMarker('Untitled marker', map.getCenter());
		return false;
	};
	
	var addNewMarker = function (title, pos, body, icon) {
		body = body || '';
		icon = icon ? (_agm_root_url + '/img/' + icon) : (_agm_root_url + '/img/system/marker.png');
		var markerPosition = _markers.length;
		map.setCenter(pos);
		var marker = new google.maps.Marker({
			title: title,
            map: map, 
            icon: icon,
            draggable: true,
            clickable: true,
            position: pos
        });
		var info = new google.maps.InfoWindow({
		    "content": createInfoContent(title, body, icon, markerPosition),
		    "maxWidth": 400
		});
		google.maps.event.addListener(marker, 'click', function() {
			info.open(map, marker);
		});
		marker._agmInfo = info; 
		if (data.snapping) {
			google.maps.event.addListener(marker, 'dragend', function() {
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						marker.setPosition(results[0].geometry.location);
						marker.setTitle(results[0].formatted_address);
						info.setContent(createInfoContent(results[0].formatted_address, '', marker.getIcon(), markerPosition));
						updateMarkersListDisplay();
					} else alert(l10nStrings.geocoding_error);
				});
			});	
		}	
		marker._agmBody = body;
		_markers[markerPosition] = marker;
		updateMarkersListDisplay();
	};
	
	var createInfoContent = function (title, body, icon, markerPosition) {
		return '<div class="agm_mh_info_content">' +
			'<a href="#" class="agm_mh_info_icon_switch"><img agm:marker_id="' + markerPosition + '" src="' + icon + '" /><br /><small>Icon</small></a>' +
			'<div class="agm_mh_info_text">' +
				'<div><label for="">' + l10nStrings.title + '</label><br /><input type="text" agm:marker_id="' + markerPosition + '" class="agm_mh_info_title" value="' + title + '" /></div>' + 
				'<label for="">' + l10nStrings.body + '</label><textarea class="agm_mh_info_body" agm:marker_id="' + markerPosition + '">' + body + '</textarea>' +
			'</div>' +
		'</div>';
	};
	
	var extractMarkerId = function (href) {
		var id = href.replace(/[^0-9]+/, '');
		return parseInt(id);
	};
	
	var removeMarker = function () {
		var $me = $(this);
		var id = extractMarkerId($me.attr('href'));
		marker = _markers.splice(id, 1);
		marker[0].setMap(null);
		updateMarkersListDisplay();
		return false;
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
	
	var updateMarkersListDisplay = function () {
		var html = '<ul>';
		$.each(_markers, function (idx, mark) {
			html += '<li>' +
				'<a href="#agm_mh_marker-' + idx + '" class="agm_mh_marker_item">' + mark.getTitle() + '</a>' +
				'&nbsp;' +
				'<span><a href="#agm_mh_marker-' + idx + '" class="button-secondary action agm_mh_marker_delete_item">' + l10nStrings.delete_item + '</a></span>' +
			'</li>';
		});
		html += '</ul>';
		$('#agm_mh_markers').html(html);
	};
	
	var showMarkerIconsList = function () {
		var markerId = $(this).find('img').attr('agm:marker_id');
		var $parent = $(this).parent('.agm_mh_info_content');
		var oldContent = $parent.html();
		
		$.post(ajaxurl, {"action": "agm_list_icons"}, function (data) {
			var html = '';
			$.each(data, function (idx, el) {
				html += '<a class="agm_new_icon" href="#"><img src="' + _agm_root_url + '/img/' + el + '" /></a> ';
			});
			$parent.html(html);
			$(".agm_new_icon").click(function () {
				var src = $(this).find('img').attr('src');
				$parent.html(oldContent);
				$parent.find('a.agm_mh_info_icon_switch img').attr('src', src);
				var marker = _markers[markerId];
				marker.setIcon(src);
				updateMarkersListDisplay();
			});
		});
	};
	
	var updateMarkerTitle = function () {
		var $me = $(this);
		var markerId = $me.attr('agm:marker_id');
		var marker = _markers[markerId];
		marker.setTitle($me.val());
		marker._agmInfo.setContent(createInfoContent (marker.getTitle(), marker._agmBody, marker.getIcon(), markerId));
		updateMarkersListDisplay();
	};

	var updateMarkerBody = function () {
		var $me = $(this);
		var markerId = $me.attr('agm:marker_id');
		var marker = _markers[markerId];
		marker._agmBody = $me.val();
		marker._agmInfo.setContent(createInfoContent (marker.getTitle(), marker._agmBody, marker.getIcon(), markerId));
		updateMarkersListDisplay();
	};
	
	var searchNewLocation = function () {
		var loc = $('#agm_mh_location').val();
		if (!loc) {
			alert(l10nStrings.type_in_location);
			return false;
		}
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({'address': loc}, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) addNewMarker(results[0].formatted_address, results[0].geometry.location);
			else alert(l10nStrings.geocoding_error);
		});
	};
	
	var init = function () {
		populateDefaults();
		createMarkup();
		map = new google.maps.Map($("#map_preview").get(0), {
			"zoom": parseInt(data.zoom),
			"center": new google.maps.LatLng(40.7171, -74.0039), // New York
			"mapTypeId": google.maps.MapTypeId[data.map_type]
		});

		// Set initial location, if possible 
		// and if not already queued to be set by markers
		if(navigator.geolocation && !data.markers) {
			navigator.geolocation.getCurrentPosition(function(position) {
				map.setCenter(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));
			});
		}
		
		addMarkers();
		togglePanoramioLayer();
		loadAssociatedPosts();

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
		
		$('#agm_map_drop_marker').click(dropNewMarker);
		$('#agm_go_back').click(destroyMap);
		$('#agm_insert_map').click(insertMap);
		$('#agm_map_options').click(toggleOptions);
		$('#agm_save_map').click(saveMap);
		$('#agm_mh_location_trigger').click(searchNewLocation);
		$('#agm_map_size_default').click(toggleDefaultSize);
		
		$('.agm_mh_marker_item').live('click', centerToMarker);
		$('.agm_mh_marker_delete_item').live('click', removeMarker);
		$('.agm_mh_info_icon_switch').live('click', showMarkerIconsList);
		$('.agm_mh_info_title').live('change', updateMarkerTitle);
		$('.agm_mh_info_body').live('change', updateMarkerBody);
	};
	
	var destroyMap = function () {
		destroy();
		$container.trigger('agm_map_close');
	};
	
	var destroy = function () {
		$container.empty();
		$('.agm_mh_marker_item').die('click');
		$('.agm_mh_marker_delete_item').die('click');
		$('.agm_mh_info_icon_switch').die('click');
		$('.agm_mh_info_title').die('change');
		$('.agm_mh_info_body').die('change');
	};
	
	/**
	 * Uses global _agmMaps array to create the needed map objects.
	 * Deferres AgmMapHandler creation until Google Maps API is available.
	 */
	var waitForMaps = function () {
		if (!_agmMapIsLoaded) {
			setTimeout(waitForMaps, 100);
		} else {
			init();
		}
	};
	
	waitForMaps();
		
	return {
		"destroy": destroy
	};
	
};

});
})(jQuery);