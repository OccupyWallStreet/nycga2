(function($){
$(function() {
	
var $parent;
	
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
	var html = '<ul>';
	$.each(data.albums.data, function (idx, album) {
		album.count = ("count" in album) ? album.count : 0;
		html += '<li>';
		
		html += album.name + ' (' + album.count + ') <br />';
		html += '<a class="wdfb_insert_album" href="#' + album.id + '">' + l10nWdfbEditor.insert_album + '</a>';
		
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

function insertAlbum ($me) {
	var albumId = parseAlbumIdHref($me.attr('href'));
	$parent.find('input:text').val(albumId);
	tb_remove();
	return false;
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

function openWidgetEditor () {
	var height = $(window).height(), adminbar_height = 0;
	if ($('body.admin-bar').length) adminbar_height = 28;
	height = height - 85 - adminbar_height;
	tb_show(l10nWdfbEditor.add_fb_photo, '#TB_inline?width=640&height=' + height + '&inlineId=wdfb_album_root_container');
	loadAlbums();
	return false;
}

function init_ui () {
	// Create the needed editor container HTML
	$('body').append('<div id="wdfb_album_root_container" style="display:none"><div id="wdfb_album_container"></div></div>');
	
	// --- Bind events ---
	
	$('a.wdfb_widget_open_editor').live('click', function () {
		$parent = $(this).parents('.wdfb_album_widget_select_album');
		openWidgetEditor();
		return false;
	});
	
	$('a.wdfb_insert_album').live('click', function () {
		insertAlbum($(this));
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
		} else {
			$('.wdfb_album_widget_select_album').html(
				'<div class="error below-h2">' + l10nWdfbEditor.insuficient_perms + '<br />' + 
					'<a class="wdfb_grant_albums_perms" href="#" >' + l10nWdfbEditor.grant_perms + '</a>' +
				'</div>'
			);
			$(".wdfb_grant_albums_perms").live("click", function () { 
				var $me = $(this);
				var locale = $me.attr("wdfb:locale");
				/*
				FB.ui({ 
					"method": "permissions.request", 
					"perms": 'user_photos',
					"display": "iframe"
				}, function () {
					window.location.reload(true);
				});
				*/
				FB.login(function () {
					window.location.reload(true);
				}, {
					"scope": 'user_photos'
				}); 
				return false; 
			}); 
		}
	});
}
FB.getLoginStatus(function (resp) {
	init();
})
	
});
})(jQuery);