/**
 * Responsible for hooking Maps to the WP editor interface. 
 */


function openMapEditor () {
	jQuery(document).trigger('agm_map_editor_open');
	return false;
}
function closeMapEditor () {
	tb_remove();
	jQuery(document).trigger('agm_map_editor_close');
	return false;
}


(function($){
$(function() {
	
/**
 * Individual (currently active) map handler.
 */
var _mapHandler = false;
	
/**
 * Requests a fresh list of existing maps from the server.
 */
function requestMapList () {
	var data = {
		action: 'agm_list_maps'
	};
	$.post(ajaxurl, data, loadMaps);
}

/**
 * Renders the HTML for list of maps from server JSON response.
 */
function loadMaps (data) {
	var html = '<ul>';
	$.each(data, function (idx, el) {
		html += '<li class="existing_map_item">' + 
			'<div class="map_item_title">' + el.title + ' ' + '</div>' + 
			'<input type="hidden" value="' + el.id + '" />' + 
			'<div class="map_item_actions">' +
				'<a href="#" class="add_map_item">' + l10nEditor.use_this_map + '</a>' + 
				'&nbsp;|&nbsp;' +
				'<a href="#" class="edit_map_item">' + l10nEditor.preview_or_edit + '</a>' +
				'&nbsp;|&nbsp;' +
				'<a href="#" class="delete_map_item">' + l10nEditor.delete_map + '</a>' +
			'</div>' +
		'</li>';
	});
	html += '</ul>';
	//if (!data.length) html = '<div class="agm_info_box"><div class="agm_less_important">' + l10nEditor.no_existing_maps + '</div></div>';
		
	$('#maps_existing_result').html(html);
	if (!data.length) $('#maps_new_switch').click();
}

/**
 * Requests deleting of a map.
 */
function deleteMap () {
	var mapId = $(this).parents('li').find('input:hidden').val();
	$.post(ajaxurl, {"action": "agm_delete_map", "id": mapId}, function (data) {
		requestMapList();
	});
}

/**
 * Creates tag markup.
 */
function createMapIdMarkerMarkup (id) {
	if (!id) return '';
	return ' [map id="' + id + '"] ';
}

/**
 * Handles map list item insert click.
 */
function insertMapItem () {
	var $me = $(this);
	var mapMarker = createMapIdMarkerMarkup($me.parents('li').find('input:hidden').val());
	updateEditorContents(mapMarker);
	closeMapEditor();
	return false;
}

/**
 * Inserts the map marker into editor.
 * Supports TinyMCE and regular editor (textarea).
 */
function updateEditorContents (mapMarker) {	
	if (window.tinyMCE && ! $('#content').is(':visible')) window.tinyMCE.execCommand("mceInsertContent", true, mapMarker);
	else insertAtCursor($("#content").get(0), mapMarker);
}

/**
 * Inserts map marker into regular (textarea) editor.
 */
function insertAtCursor(fld, text) {
    // IE
    if (document.selection && !window.opera) {
    	fld.focus();
        sel = window.opener.document.selection.createRange();
        sel.text = text;
    }
    // Rest
    else if (fld.selectionStart || fld.selectionStart == '0') {
        var startPos = fld.selectionStart;
        var endPos = fld.selectionEnd;
        fld.value = fld.value.substring(0, startPos)
        + text
        + fld.value.substring(endPos, fld.value.length);
    } else {
    	fld.value += text;
    }
}

/**
 * Loads a map and opens it for preview/editing.
 */
function updateMapPreview () {
	var id = $(this).parents('li').find('input').val();
	$.post(ajaxurl, {"action": 'agm_load_map', "id": id}, function (data) {
		if (_mapHandler) _mapHandler.destroy();
		_mapHandler = new AgmMapHandler("#map_preview_container", data);
	});
	$('#maps_existing').hide();
	$('#maps_new').hide();
}

/**
 * Opens a fresh map.
 */
function createMap () {
	$.post(ajaxurl, {"action": 'agm_new_map'}, function (data) {
		if (!"status" in data) return false;
		if ("upgrade" == data.status) {
			$("#map_upgrade_notice").dialog({
				"modal": true,
				"title": "Please upgrade"
			});
			$('#maps_existing_switch').click();
			return false;
		}
		if (!"defaults" in data) return false;
		if (_mapHandler) _mapHandler.destroy();
		_mapHandler = new AgmMapHandler("#map_preview_container", data);
	});
	$('#maps_existing').hide();
	$('#maps_new').hide();
}

function advancedModeOn () {
	$('li.existing_map_item').each(function () {
		var $me = $(this);
		var mid = $me.find('input:hidden').val();
		var html = '<div class="map_advanced_checkbox_container">' +
			'<input type="checkbox" class="map_advanced_checkbox" value="' + mid + '" />' +
			'<div style="clear:both"></div>' +
		'</div>';
		$me.prepend(html);
	});
	var html = '<span id="advanced_mode_buttons">' + 
		'<input type="button" class="button-secondary action" id="maps_merge_locations" value="' + l10nEditor.merge_locations + '" />' +
		'<input type="button" class="button-secondary action" id="maps_batch_delete" value="' + l10nEditor.batch_delete + '" />' +
	'</span>';
	$('#maps_advanced_container').append(html);
	$('#maps_advanced_mode_help_container').html(l10nEditor.advanced_mode_help);
	$('#maps_advanced_switch').val(l10nEditor.advanced_off);
	
	// Bind events
	$('#maps_merge_locations').click(function () {
		var mapIds = [];
		$('li.existing_map_item .map_advanced_checkbox:checked').each(function () {
			mapIds[mapIds.length] = $(this).val();
		});
		$.post(ajaxurl, {"action": "agm_merge_maps", "ids": mapIds}, function (data) {
			$('#maps_advanced_switch').click(); // Turn off advanced mode
			if (_mapHandler) _mapHandler.destroy();
			_mapHandler = new AgmMapHandler("#map_preview_container", data);
			$('#maps_existing').hide();
			$('#maps_new').hide();
		});
	});
	$('#maps_batch_delete').click(function () {
		var mapIds = [];
		$('li.existing_map_item .map_advanced_checkbox:checked').each(function () {
			mapIds[mapIds.length] = $(this).val();
		});
		$.post(ajaxurl, {"action": "agm_batch_delete", "ids": mapIds}, function (data) {
			$('#maps_advanced_switch').click(); // Turn off advanced mode
			requestMapList();
		});
	});
	
}

function advancedModeOff () {
	$('.map_advanced_checkbox_container').remove();
	$('#advanced_mode_buttons').remove();
	$('#maps_merge_locations').unbind('click');
	$('#maps_batch_delete').unbind('click');
	$('#maps_advanced_mode_help_container').html(l10nEditor.advanced_mode_activate_help);
	$('#maps_advanced_switch').val(l10nEditor.advanced);
}


// Find Media Buttons strip and add the new one
var mbuttons_container = $('#media-buttons').length ? /*3.2*/ $('#media-buttons') : /*3.3*/ $("#wp-content-media-buttons");
if (!mbuttons_container.length) return;

mbuttons_container.append('' + 
	'<a onclick="return openMapEditor();" title="' + l10nEditor.add_map + '" class="thickbox" id="add_map" href="#TB_inline?width=640&height=594&inlineId=map_container">' +
		'<img onclick="return false;" alt="' + l10nEditor.add_map + '" src="' + _agm_root_url + '/img/system/globe-button.gif">' +
	'</a>'
);

// Create the needed editor container HTML
$('body').append(
	'<div id="map_container" style="display:none">' + 
	(_agm_is_multisite ? '' : '<p class="agm_less_important">For more detailed instructions on how to use refer to <a target="_blank" href="http://premium.wpmudev.org/project/wordpress-google-maps-plugin/installation/">Google Maps Installation and Use instructions</a>.</p>') +
		'<a href="#" id="maps_existing_switch">' + l10nEditor.existing_map + '</a>' +
		'<div id="maps_new_switch_container">' +
			'<p><input type="button" class="button-secondary action" id="maps_new_switch" value="' + l10nEditor.new_map + '" /></p>' +
			'<div class="agm_less_important">' + l10nEditor.new_map_intro + '</div>' +
		'</div>' +
		'<div class="agm_container" id="maps_existing">' +
			'<h3>' + l10nEditor.existing_map + '</h3>' +
			'<div id="maps_existing_result"><img src="' + _agm_root_url + '/img/system/loading.gif" />' + l10nEditor.loading + '</div>' +
			'<p id="maps_advanced_container"><input type="button" class="button-secondary action" id="maps_advanced_switch" value="' + l10nEditor.advanced + '" /></p>' +
			'<p id="maps_advanced_mode_help_container" class="agm_less_important">' + l10nEditor.advanced_mode_activate_help + '</p>' +
		'</div>' +
		'<div class="agm_container" id="maps_new">' +
			'<h3>' + l10nEditor.new_map + '</h3>' +
		'</div>' +
		'<div id="map_preview_container"><div id="map_preview"></div></div>' +
	'</div>' +
	'<div id="map_upgrade_notice" style="display:none">' +
		'<div class="error below-h2"><p><a title="Upgrade Now" href="http://premium.wpmudev.org/project/wordpress-google-maps-plugin">Upgrade to Google Maps Pro to enable additional features</a></p></div>' +
		l10nEditor.please_upgrade + 
	'</div>'
);


// --- Bind events ---



// Link switching
$('#maps_existing_switch').click(function () {
	if (_mapHandler) _mapHandler.destroy();
	$('#maps_existing_switch').hide();
	$('#maps_new').hide();
	$('#maps_existing').show();
	$('#maps_new_switch_container').show();
	
	if ($.browser.webkit) {
		$('#map_preview_container')
			.css('height', 0)
			.css('width', 0)
		;
	}
	
	// Load fresh map list on Existing Maps tab selection
	requestMapList();
});
$('#maps_new_switch').click(function () {
	if (_mapHandler) _mapHandler.destroy();
	$('#maps_new_switch_container').hide();
	$('#maps_existing').hide();
	$('#maps_new').show();
	$('#maps_existing_switch').show();
	
	if ($.browser.webkit) {
		$('#map_preview_container')
			.css('height', 0)
			.css('width', 0)
		;
	}
	
	createMap();
	
});

$(document).bind('agm_map_editor_open', function () {
	$('#maps_existing_switch').click();
});

// On map addition, update editor. 
$('li.existing_map_item a.add_map_item').live('click', insertMapItem);
$('li.existing_map_item a.edit_map_item').live('click', updateMapPreview);
$('li.existing_map_item a.delete_map_item').live('click', deleteMap);

// Bind map closing event to list toggling
$('#map_preview_container').bind('agm_map_close', function () {
	$('#maps_existing_switch').click();
});

// Bind map editor insert event to map insert
$('#map_preview_container').bind('agm_map_insert', function (e, id) { 
	var mapMarker = createMapIdMarkerMarkup(id);
	updateEditorContents(mapMarker);
	closeMapEditor();
});

// Bind advanced mode switching
$('#maps_advanced_switch').toggle(advancedModeOn, advancedModeOff);
	
// Highlight the active existing map item
$('li.existing_map_item')
	.live('mouseover', function () { $(this).addClass('agm_active_item'); })
	.live('mouseout', function () { $(this).removeClass('agm_active_item'); })
;
	
});
})(jQuery);