(function ($) {
$(function () {

var _linkToCheck = false;
var _oldSentToEditor = false;
var _isPublicPage = false;
	
function publishPostData (e) {
	e.preventDefault(); e.stopPropagation();
	var text = $("#wdqs-status").val();
	if (!text.length) return false;
	
	var title = $("#wdqs-post-title").length ? $("#wdqs-post-title").val() : '';
	var thumbnail = ($(".wdqs-image-list li:visible:first").length) ? $(".wdqs-image-list li:visible:first img").attr("src") : '';
	var no_thumbnail = ($("#wdqs-no-thumbnail").length && $("#wdqs-no-thumbnail").is(":checked")) ? 1 : 0;
	var height = $("#wdqs-height").length ? $("#wdqs-height").val() : 0;
	var width = $("#wdqs-width").length ? $("#wdqs-width").val() : 0;
	
	var link_title = $(".wdqs-title-container h3 a").text();
	var link_text = $(".wdqs-text-container p").html();
	
	var is_draft = $(e.target).is("#wdqs-draft") ? 1 : 0;
	
	var $root = $("#wdqs-dashboard-widget").parent();
	$root.html('<div class="wdqs-waiting-for-response"></div>');
	
	$.post(_wdqs_ajaxurl, {
		"action": "wdqs_post",
		"is_draft": is_draft,
		"data": text,
		"title": title,
		"height": height,
		"width": width,
		"thumbnail": thumbnail,
		"no_thumbnail": no_thumbnail,
		"link_title": link_title,
		"link_text": link_text
	}, function (data) {
		if (_isPublicPage) { // Refresh if it's a public page
			window.location.reload(true);
		} else { // Be ready for next input otherwise
			$root.html(data.form);
			setTimeout(function () { 
				$(".wdqs-update-notice").hide('slow');
			}, 6000);
			init();
		}
	});
	
	return false;
}
	
function updatePreviewMarkup (data) {
	var type = $("#wdqs-link-type").val();
	if ($("#wdqs-" + type + "-switch").length) {
		switchContentType($("#wdqs-" + type + "-switch"), false);
	}
	setPreviewTitle(data.preview.title);
	
	if (type != 'link') return updateVideoOrImagePreview(data);
	return updateLinkPreview(data);
}

function setPreviewTitle (title) {
	if (!title) return false;
	if ($("#wdqs-post-title").length) {
		$("#wdqs-preview-root .wdqs-title-container h3 a").text(title);
		$("#wdqs-post-title").val(title);
	} else {
		$("#wdqs-preview-root").prepend(
			"<div class='wdqs-title-container'>" +
				"<h3 class='entry-title'><a href='#'>" + title + "</a></h3>" +
				"<input type='hidden' id='wdqs-post-title' value='" + title + "' />" +
			"</div>"
		);
	}
}

function updateVideoOrImagePreview (data) {
	var height = false, width = false;
	try {
		height = data.preview.height; width = data.preview.width;
	} catch (e) {}
	height = parseInt(height) ? parseInt(height) : '';
	width = parseInt(width) ? parseInt(width) : '';
	$("#wdqs-preview-root .wdqs:last").append(
		'<div id="wdqs-size-container">' +
			'<label for="wdqs-height">' + l10nWdqs.height + '<input size="3" type="text" id="wdqs-height" value="' + height + '" /></label>' +
			'&nbsp;' +
			'<label for="wdqs-width">' + l10nWdqs.width + '<input size="3" type="text" id="wdqs-width" value="' + width + '" /></label>' +
			'&nbsp;' + '<small>' + l10nWdqs.leave_empty_for_defaults + '</small>' +
		'</div>'
	);
}

function updateLinkPreview (data) {
	$(".wdqs-image-list:last li")
		.hide()
		.first().show()
		.parents('.wdqs-thumbnail-container').append(
			'<div id="wdqs-thumbnail-switcher">' +
				'<a id="wdqs-previous" href="#"><span>&lt;</span></a>' +
				'<a>&nbsp;</a>' +
				'<a id="wdqs-next" href="#"><span>&gt;</span></a>' +
				'<br style="clear:both"/>' +
				'<label for="wdqs-no-thumbnail"><input type="checkbox" id="wdqs-no-thumbnail" /> ' + l10nWdqs.no_thumbnail + '</label>' +
			'</div>'
		)
	;
	// Do we even have multiple thubms to switch?
	if ($(".wdqs-image-list li").length <= 1) $("#wdqs-thumbnail-switcher a").remove();
	else $("#wdqs-thumbnail-switcher").append(
		'<div><small>' +
			'<span id="wdqs-img-count">1</span>' +
			'&nbsp;' + l10nWdqs.of + '&nbsp;' +
			$(".wdqs-image-list li").length + '&nbsp;' + 
			l10nWdqs.images_found + 
		'</small></div>' 
	);
}

function callPreviewUpdate () {
	var title = $("#wdqs-post-title").length ? $("#wdqs-post-title").val() : '';
	var text = $("#wdqs-status").val();
	var height = $("#wdqs-height").length ? $("#wdqs-height").val() : 0;
	var width = $("#wdqs-width").length ? $("#wdqs-width").val() : 0;
	
	$("#wdqs-preview-root").html('<div class="wdqs-waiting-for-response"></div>');
	$.post(_wdqs_ajaxurl, {
		"action": "wdqs_generate_preview",
		"text": text,
		"title": title,
		"height": height,
		"width": width
	}, function (data) {
		if (!parseInt(data.status)) {
			$("#wdqs-preview-root").html('');
			return false;
		}
		$("#wdqs-link-type").val(data.preview.type);
		$("#wdqs-preview-root").html(data.preview.markup);
		updatePreviewMarkup(data);
	});
}

/**
 * Auto-resizing the textarea.
 * Inspired by (and yes, adapted from :) this awesome article: 
 * http://www.sitepoint.com/build-auto-expanding-textarea-1/
 * by Craig Buckler
 * Thanks!
 */
function attemptTextareaResize () {
	var $status = $("#wdqs-status");
	if (!$status.length) return false;
	var scroll = $status.get(0).scrollHeight;

	$status.css({
		'padding-top': ($.browser.msie ? '3px' : '0'),
		'padding-bottom': ($.browser.msie ? '3px' : '0'),
		'overflow': 'hidden'
	});

	_isPublicPage ? $status.height(scroll) : $status.height(scroll+2);
	//$status.height(scroll);
}

function checkPreviewUpdate () {
	var text = $("#wdqs-status").val();
	
	if (!text.length) return true;
	
	if (!text.match(/https?:[^\s]+\b/)) return true;

	if ($("#wdqs-post-title").length && $("#wdqs-post-title").is(":visible")) {
		$("#wdqs-post-title-switch").click();
	}
	callPreviewUpdate();
}

function handleKeyEvent (e) {
	attemptTextareaResize();
	if (32 != e.which && 13 != e.which) return true;
	
	if ($("#wdqs-preview-root").children().length) return true;

	var text = $("#wdqs-status").val();	
	if (!text.length) return true;

	if (_linkToCheck && text.substr(0, _linkToCheck.length) == _linkToCheck) return true; // Already checked this
	if ($.trim(text).indexOf(' ') != -1) _linkToCheck = $.trim(text).substr(0, text.indexOf(' '));
	else _linkToCheck = $.trim(text);
	
	checkPreviewUpdate();
}

function handlePasteEvent (e) {
	setTimeout(function () {
		attemptTextareaResize();
		var text = $("#wdqs-status").val();
		if (!text.length) return true;
	
		if (_linkToCheck && text.substr(0, _linkToCheck.length) == _linkToCheck) return true; // Already checked this
		if ($.trim(text).indexOf(' ') != -1) _linkToCheck = $.trim(text).substr(0, text.indexOf(' '));
		else _linkToCheck = $.trim(text);
	
		checkPreviewUpdate();
	}, 100);
}

function handlePreviewRequest () {
	var text = $("#wdqs-status").val();
	//if (!text.match(/https?:[^\s]+\b/)) $("#wdqs-preview-root").html(text);
	//else callPreviewUpdate();
	callPreviewUpdate();
}

function resetData () {
	$("#wdqs-status").val('');
	$("#wdqs-preview-root").html('');
	_linkToCheck = false;
}

function changeLinkTitle () {
	alert("Upgrade to Pro to allow editing title");
	return false;
}

function changeTextContent () {
	alert("Upgrade to Pro to allow editing contents");
	return false;
}

function imageToEditor (html) {
	var $img = $('img', html);
	mediaToEditor($img.attr('src'));
	setPreviewTitle($img.attr('title'));
	callPreviewUpdate();
}

function videoToEditor (html) {
	mediaToEditor(html);
	$("#wdqs-preview-root").html(html);
	setPreviewTitle($(html).text());
}

function mediaToEditor (src) {
	$("#wdqs-status").val(src);
	tb_remove();
	window.send_to_editor = _oldSentToEditor;
	$("#wdqs-status").show();
}

function showVideoUpload () {
	var pfx = _isPublicPage ? _wdqs_adminurl + '/' : '';
	var height = $(window).height*0.35;
	tb_show("Upload Video", pfx + "media-upload.php?type=video&TB_iframe=1&width=640&height="+height);
	_oldSentToEditor = window.send_to_editor;
	window.send_to_editor = videoToEditor;
}

function showImageUpload () {
	var pfx = _isPublicPage ? _wdqs_adminurl + '/' : '';
	var height = $(window).height*0.35;
	tb_show("Upload Image", pfx + "media-upload.php?type=image&TB_iframe=1&width=640&height="+height);
	_oldSentToEditor = window.send_to_editor;
	window.send_to_editor = imageToEditor;
}

function switchContentType ($a, showUploads) {
	$a.siblings('a').removeClass('wdqs-active').end().addClass('wdqs-active');
	
	// Change UI
	$("#wdqs-status-arrow-container").css(
		"margin", 
		"0 0 0 " + Math.floor(($a.offset().left - $("#wdqs-status").offset().left) + ($a.outerWidth()/2)) + "px"
	);

	if (!showUploads) return false;

	$("#wdqs-status").val('').focus();
	$("#wdqs-preview-root").empty();
	
	switch ($a.attr('id')) {
		case "wdqs-video-switch":
			showVideoUpload();
			break;
		case "wdqs-image-switch":
			showImageUpload();
			break;
	}
	return false;
}

/*** Textarea handlers ***/
$("#wdqs-status").live('focus', attemptTextareaResize);
$("#wdqs-status").live('keyup', handleKeyEvent);
$("#wdqs-status").live('paste', handlePasteEvent);

/*** Control handlers ***/
$("#wdqs-preview").live('click', handlePreviewRequest);
$("#wdqs-reset").live('click', resetData);
$("#wdqs-post, #wdqs-draft").live('click', publishPostData);

/*** Tabs handlers ***/
$(".wdqs-type-switch").live('click', function () {switchContentType($(this), true); return false;});

/*** Link type editing events ***/
$("#wdqs-preview-root .wdqs-title-container").live('mouseenter', function(){$(this).addClass("wdqs-hover");});
$("#wdqs-preview-root .wdqs-title-container").live('mouseleave', function(){$(this).removeClass("wdqs-hover");}); 
$("#wdqs-preview-root .wdqs-title-container").live('click', changeLinkTitle);

$("#wdqs-preview-root .wdqs-text-container").live('mouseenter', function(){$(this).addClass("wdqs-hover");}); 
$("#wdqs-preview-root .wdqs-text-container").live('mouseleave', function(){$(this).removeClass("wdqs-hover");});
$("#wdqs-preview-root .wdqs-text-container").live('click', changeTextContent);

/*** Prev/Next handlers ***/
$("#wdqs-previous").live('click', function () {
	var $current = $(".wdqs-image-list li:visible:first");
	if ($current.get(0) == $(".wdqs-image-list li:first").get(0)) return false; // First image
	$current
		.prev().show()
		.end().hide()
	;
	$("#wdqs-img-count").text($current.prevAll().length);
	return false;
});
$("#wdqs-next").live('click', function () {
	var $current = $(".wdqs-image-list li:visible:first");
	if ($current.get(0) == $(".wdqs-image-list li:last").get(0)) return false; // Last image
	$current
		.next().show()
		.end().hide()
	;
	$("#wdqs-img-count").text($current.prevAll().length+2);
	return false;
});

/*** Initialize ***/
function init () {
	if (typeof window.send_to_editor != 'function') {
		_isPublicPage = true;
		// Neutralize wpautop
		$("#wdqs-dashboard-widget p").each(function () {
			if (!$.trim($(this).html())) $(this).remove();
		});		
	}
	if ($.browser.msie) $("#wdqs-status").css("padding", "3px");
	attemptTextareaResize();
	switchContentType($("#wdqs-generic-switch"));
	$("#wdqs-status-arrow-container").css("display", "block");
}
init();
});
})(jQuery);