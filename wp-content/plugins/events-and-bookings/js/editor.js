/**
 * Responsible for hooking Maps to the Events interface. 
 */

(function($){
$(function() {

/**
 * Creates tag markup.
 */
function createMapIdMarkerMarkup (id) {
        if (!id) return '';
        return ' [map id="' + id + '"] ';
}

/**
 * Inserts the map marker into editor.
 * Supports TinyMCE and regular editor (textarea).
 */
function updateEditorContents (mapMarker) {	
	insertAtCursor($("#incsub_event_venue").get(0), mapMarker);
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

function insertMapItem () {
        var $me = $(this);
        var mapMarker = createMapIdMarkerMarkup($me.parents('li').find('input:hidden').val());
        updateEditorContents(mapMarker);
        closeMapEditor();
        return false;
}

// Find Media Buttons strip and add the new one
var eab_mbuttons_container = $('#eab_insert_map');
if (!eab_mbuttons_container.length) return;

eab_mbuttons_container.append('' + 
	'<a onclick="return openMapEditor();" title="' + eab_l10nEditor.add_map + '" class="thickbox" id="eab_add_map" href="#TB_inline?width=640&height=594&inlineId=map_container">' +
		'<img onclick="return false;" alt="' + eab_l10nEditor.add_map + '" src="' + _agm_root_url + '/img/system/globe-button.gif">' +
	'</a>'
);

$('li.existing_map_item a.add_map_item').die('click');
$('li.existing_map_item a.add_map_item').live('click', insertMapItem);

$('#map_preview_container').unbind('agm_map_insert');
$('#map_preview_container').bind('agm_map_insert', function (e, id) {
        var mapMarker = createMapIdMarkerMarkup(id);
        updateEditorContents(mapMarker);
        closeMapEditor();
});

$('#add_map').hide();
	
});
})(jQuery);