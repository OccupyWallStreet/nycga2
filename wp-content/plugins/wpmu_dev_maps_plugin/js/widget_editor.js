(function($){
$(function() {
	
/**
 * Individual (currently active) map handler.
 */
var _mapHandler = false;

/**
 * Parent (widget form) container for the clicked link.
 */
var $parent = false;
	
/**
 * Opens a fresh map.
 */
function createNewMap () {
	$parent = $(this).parents('p.agm_widget_query_options');
	var height = $(window).height(), adminbar_height = 0;
	if ($('body.admin-bar').length) adminbar_height = 28;
	height = height - 85 - adminbar_height;
	tb_show(l10nEditor.add_map, '#TB_inline?width=640&height=' + height + '&inlineId=map_container');
	$.post(ajaxurl, {"action": 'agm_new_map'}, function (data) {
		if (!"status" in data) return false;
		if ("upgrade" == data.status) {
			$("#map_upgrade_notice").dialog({
				"modal": true,
				"title": "Please upgrade"
			});
			$('#map_preview_container').trigger('agm_map_close');
			return false;
		}
		if (!"defaults" in data) return false;
		if (_mapHandler) _mapHandler.destroy();
		_mapHandler = new AgmMapHandler("#map_preview_container", data, false);
	});
	return false;
}

//Create the needed editor container HTML
$('body').append(
	'<div id="map_container" style="display:none">' + 
	(_agm_is_multisite ? '' : '<p class="agm_less_important">For more detailed instructions on how to use refer to <a target="_blank" href="http://premium.wpmudev.org/project/wordpress-google-maps-plugin/installation/">Google Maps Installation and Use instructions</a>.</p>') +
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

// Create a new map 
$('a.agm_create_new_map').live('click', createNewMap);

// Map saved; update the list and set selection
$('#map_preview_container').bind('agm_map_saved', function (e, mapId) {
	if (!$parent) return false;
	$.post(ajaxurl, {"action": "agm_list_maps"}, function (data) {
		if (!data.length) return false;
		var opts = '';
		$.each(data, function (idx, el) {
			opts += '<option value="' + el.id + '" ' + 
				(el.id == mapId ? 'selected="selected"' : '') + 
				'>' + el.title + '</option>';
		});
		$parent.find('.map_id_switch').attr('checked', true);
		$parent.find('.map_id_switch').click();
		$parent.find('select.map_id_target').html(opts);
	});
});

// Map closed; remove Thickbox
$('#map_preview_container').bind('agm_map_close', function () {
	tb_remove();
});
	
});
})(jQuery);