/**
 * Responsible for hooking Maps to the WP editor interface. 
 */


function wdfbOpenAlbumEditor () {
	jQuery(document).trigger('wdfb_album_editor_open');
	return false;
}
function wdfbCloseAlbumEditor () {
	tb_remove();
	jQuery(document).trigger('wdfb_album_editor_close');
	return false;
}


(function($){
$(function() {
	
function parseAlbumIdHref (href) {
	return parseInt(href.substr(1));
}
	
function createAlbumsMarkup (data) {
	var status = parseInt(data.status);
	if (!status) {
		$("#wdfb_album_container").html(
			"Please log in to your FB account first"
		);
		return false;
	}
	var html = '<ul class="wdfb_albums">';
	$.each(data.albums.data, function (idx, album) {
		album.count = ("count" in album) ? album.count : 0;
		html += '<li>';
		
		html += album.name + ' (' + album.count + ') <br />';
		html += '<a class="wdfb_insert_album" href="#' + album.id + '">' + l10nWdfbEditor.insert_album + '</a>';
		html += '&nbsp;';
		html += '<a class="wdfb_show_album_photos" href="#' + album.id + '">' + l10nWdfbEditor.insert_album_photos + '</a>';
		
		html += '</li>';
	});
	html += '</ul>';
	$("#wdfb_album_container").html(html);
}

function createAlbumPhotosMarkup (data) {
	var status = parseInt(data.status);
	if (!status) {
		$("#wdfb_album_container").html(
				"Please log in to your FB account first"
		);
		return false;
	}
	var html = '<p>';
	html += '<input type="button" id="wdfb_insert_album_photo_items" value="' + l10nWdfbEditor.insert + '" />';
	html += '<input type="button" id="wdfb_back_to_albums" value="' + l10nWdfbEditor.go_back + '" />';
	html += '</p>';
	
	html += '<ul class="wdfb_album_photos">';
	$.each(data.photos.data, function (idx, photo) {
		var iconSrc = photo.images[photo.images.length-1].source;
		var imgSrc = photo.images[0].source;
		html += '<li>';
		
		html += '<img src="' + iconSrc+ '" width="90" /><br />';
		html += '<input type="checkbox" id="wdfb_image_item' + idx + '" class="wdfb_album_photo_item" value="' + imgSrc + '" /><label for="wdfb_image_item' + idx + '">' + l10nWdfbEditor.use_this_image + '</label>';
		
		html += '</li>';
	});
	html += '</ul>';
	
	$("#wdfb_album_container").html(html);
}

function loadAlbums () {
	$("#wdfb_album_container").html(l10nWdfbEditor.please_wait + ' <img src="' + _wdfb_root_url + '/img/waiting.gif">');
	$.post(ajaxurl, {"action": "wdfb_list_fb_albums"}, function (response) {
		createAlbumsMarkup(response);
	});
}

function loadAlbumPhotos ($me) {
	$("#wdfb_album_container").html(l10nWdfbEditor.please_wait + ' <img src="' + _wdfb_root_url + '/img/waiting.gif">');
	var albumId = parseAlbumIdHref($me.attr('href'));
	$.post(ajaxurl, {"action": "wdfb_list_fb_album_photos", "album_id": albumId}, function (response) {
		createAlbumPhotosMarkup(response);
	});
}

function insertAlbum ($me) {
	var albumId = parseAlbumIdHref($me.attr('href'));
	/*
	var markup = '';
	$("#wdfb_album_container").html(l10nWdfbEditor.please_wait + ' <img src="' + _wdfb_root_url + '/img/waiting.gif">');
	$.post(ajaxurl, {"action": "wdfb_list_fb_album_photos", "album_id": albumId}, function (response) {
		var status = parseInt(response.status);
		if (!status) return false;
		markup += '<div class="wdfb_fb_album">';
		markup += '<ul>';
		$.each(response.photos.data, function (idx, photo) {
			var iconSrc = photo.images[photo.images.length-1].source;
			var imgSrc = photo.images[0].source;
			markup += '<li>';
			markup += '<a href="' + imgSrc + '">';
			markup += '<img src="' + iconSrc + '" />';
			markup += '</a>';
			markup += '</li>';
		});
		markup += '</ul>';
		markup += '</div>';
		updateEditorContents(markup);
		wdfbCloseAlbumEditor();
	});
	*/
	updateEditorContents('[wdfb_album id="' + albumId + '"]');
	wdfbCloseAlbumEditor();
	return false;
}

function insertAlbumPhotos () {
	var markup = '';
	$('.wdfb_album_photo_item:checked').each(function () {
		markup += '<img src="' + $(this).val() + '" />';
	});
	updateEditorContents(markup);
	wdfbCloseAlbumEditor();
}


/**
 * Inserts the map marker into editor.
 * Supports TinyMCE and regular editor (textarea).
 */
function updateEditorContents (markup) {	
	if (window.tinyMCE && ! $('#content').is(':visible')) window.tinyMCE.execCommand("mceInsertContent", true, markup);
	else insertAtCursor($("#content").get(0), markup);
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

function init_ui () {
	
	// Find Media Buttons strip and add the new one
	var mbuttons_container = $('#media-buttons').length ? /*3.2*/ $('#media-buttons') : /*3.3*/ $("#wp-content-media-buttons");
	if (!mbuttons_container.length) return;
	
	mbuttons_container.append('' + 
			'<a onclick="return wdfbOpenAlbumEditor();" title="' + l10nWdfbEditor.add_fb_photo + '" class="thickbox" id="add_fb_photo" href="#TB_inline?width=640&height=594&inlineId=wdfb_album_root_container">' +
			'<img onclick="return false;" alt="' + l10nWdfbEditor.add_fb_photo + '" src="' + _wdfb_root_url + '/img/fb_photo.png">' +
			'</a>'
	);
	
	// Create the needed editor container HTML
	$('body').append('<div id="wdfb_album_root_container" style="display:none"><div id="wdfb_album_container"></div></div>');
	
	// --- Bind events ---
	
	$(document).bind('wdfb_album_editor_open', function () {
		loadAlbums();
	});
	
	$('a.wdfb_show_album_photos').live('click', function () {
		loadAlbumPhotos($(this));
	});
	$('a.wdfb_insert_album').live('click', function () {
		insertAlbum($(this));
	});
	$('#wdfb_back_to_albums').live('click', function () {
		loadAlbums();
	});
	$('#wdfb_insert_album_photo_items').live('click', function () {
		insertAlbumPhotos();
	});
	
}

function init () {
	if (typeof FB != 'object') return false; // Don't even bother
	FB.api({
		"method": "fql.query",
		"query": "SELECT user_photos FROM permissions WHERE uid=me()"
	}, function (resp) {
		var all_good = true;
		try {
			$.each(resp[0], function (idx, el) {
				if(el !== "1") all_good = false;
			});
		} catch (e) {
			all_good = false;
		}
		if (all_good) {
			init_ui();
		}
	});
}

if (typeof FB == 'object' && FB._apiKey) {
	FB.getLoginStatus(function (resp) {
		init();
	});
}

});
})(jQuery);