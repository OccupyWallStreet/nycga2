/*** Create/Admin form functionality ***/
jQuery(document).ready( function() {

	// element shortcuts
	var e_loader = jQuery(".ajax-loader");
	var e_url = jQuery("input#link-url");
	var e_url_ro = jQuery("input#link-url-readonly");
	var e_fields = jQuery("div#link-name-desc-fields");
	var e_name = e_fields.children("input#link-name");
	var e_desc = e_fields.children("textarea#link-desc");
	var e_clear = jQuery("span#link-url-embed-clear");
	var e_clear_a = e_clear.children("a");
	var e_embed = jQuery("div#link-url-embed");
	var e_avopt_p = jQuery("div.link-avatar-option p");
	var e_avimg_def = e_avopt_p.children("img.avatar-default");
	var e_avimg_cur = e_avopt_p.children("img.avatar-current");

	// bind to URL clear link click event
	function bindClearUrlClick()
	{
		e_clear_a.click( function() {
			e_clear.fadeOut(500, function() {
				// reset embed panel
				if (e_embed.is(':visible')) {
					e_embed.slideUp(500, function() {
						e_embed.html('');
						e_avimg_cur.attr("src", e_avimg_def.attr("src"));
						e_avimg_cur.attr("alt", e_avimg_def.attr("alt"));
						e_avimg_cur.attr("width", e_avimg_def.width());
						e_avimg_cur.attr("height", e_avimg_def.height());
					});
				}
				// hide fields
				if (e_fields.is(':visible')) {
					e_fields.fadeOut(500);
				}
				// reset all form values
				e_url.val('');
				e_url.removeAttr("readonly");
				e_url_ro.val(0);
				e_name.val('');
				e_desc.val('');
			});
		});
	}
	
	// bind to thumb picker click events
	function bindThumbPickerClick()
	{
		var e_thpick = jQuery("div#link-url-embed-thpick");
		var e_thpick_a = e_thpick.children("a");
		var e_thpick_curr = e_thpick.children("span#thcurrent");
		var e_thpick_idx = jQuery("input#link-url-embed-thidx");
		var e_thpick_skip = jQuery("input#link-url-embed-thskip");
		var e_avimg_cur = jQuery("div#link-url-embed-content img");

		var local_idx = null;
		var last_img_idx = e_thpick_idx.val();
		var last_img_src = e_avimg_cur.attr("src");

		e_thpick_a.click(function() {

			if (e_thpick_skip.attr('checked')) {
				return false;
			}

			var images = e_thpick.data("images");
			var images_idx = e_thpick.data("images_idx");

			if (local_idx == null) {
				local_idx = 0;
				for (i = 0; i < images.length; i = i + 1) {
					if(images[i][0] == images_idx) {
						local_idx = i;
						break;
					}
				}
			}

			switch (jQuery(this).attr('id')) {
				case 'thprev':
					if (local_idx >= 1) {
						local_idx = local_idx - 1;
						break;
					}
					return false;
				case 'thnext':
					if (local_idx < (images.length - 1)) {
						local_idx = local_idx + 1;
						break;
					}
					return false;
				default:
					return false;
			}

			// swap out the image
			e_avimg_cur.fadeTo(200, 0.3, function() {
				// update idx locally and in form
				e_thpick_idx.val(images[local_idx][0]);
				e_thpick_curr.html(local_idx + 1);
				e_avimg_cur.attr("src", images[local_idx][1]);
				e_avimg_cur.fadeTo(200, 1);
			});

			return false;
		});

		e_thpick_skip.click(function() {

			// must grab this here in case not available on page load
			var images = e_thpick.data("images");

			// swap out image with default if checked
			switch (jQuery(this).attr('checked')) {
				case true:
					last_img_idx = e_thpick_idx.val()
					last_img_src = e_avimg_cur.attr("src");
					e_thpick_idx.val(null);
					e_avimg_cur.attr("src", e_avimg_def.attr("src"));
					break;
				default:
					if ((last_img_idx) && (last_img_src)) {
						e_thpick_idx.val(last_img_idx);
						e_avimg_cur.attr("src", last_img_src);
					} else {
						// use values from first element
						e_thpick_idx.val(images[0][0]);
						e_avimg_cur.attr("src", images[0][1]);
					}
					break;
			}
		});
	}

	// bind to edit check box click event
	function bindEditTextClick()
	{
		jQuery("input#link-url-embed-edit-text").click( function() {
			if (jQuery(this).attr('checked')) {
				e_fields.fadeIn(750);
			} else {
				e_fields.fadeOut(750, function() {
					e_name.val(e_name.data('default_value'));
					e_desc.val(e_desc.data('default_value'));
				});
			}
		});
	}

	// detect if url is embeddable
	function detectUrl()
	{
		var url = jQuery.trim(e_url.val());
		
		// only try to match if url has some meat AND has changed
		if (url.length >= 15 && !e_url.attr("readonly")) {
			// make sure embed content is blank
			e_embed.html(''); e_name.val(''); e_desc.val('');
			return true;
		}
		return false;
	}

	// need to bind these if visible on page load
	if (e_clear.is(':visible')) {
		bindClearUrlClick();
	}
	if (e_embed.is(':visible')) {
		bindEditTextClick();
		if (jQuery("div#link-url-embed-thpick").is(':visible')) {
			bindThumbPickerClick();
		}
	}

	// try to locate an auto embed service for the URL entered
	e_url.blur( function()
	{
		e_loader.toggle();

		if (detectUrl()) {
			e_url.attr("readonly", "readonly");
			e_url_ro.val(1);
			e_clear.fadeIn(500, bindClearUrlClick);
		} else {
			e_loader.toggle();
			return;
		}

		jQuery.post(ajaxurl, {
			action: 'link_auto_embed_url',
			'cookie': encodeURIComponent(document.cookie),
			'_wpnonce': jQuery("input#_wpnonce-link-auto-embed").val(),
			'url': e_url.val()
		},
		function(response) {

			var response_split = response.split('[[split]]');
			var err_num = response_split[0];

			jQuery('#message').remove();

			if (err_num < 1 ) {
				jQuery('form#link-details-form').before('<div id="message" class="error fade"><p>' + response_split[1] + '</p></div>')
				e_fields.fadeIn(750);
			} else {

				e_name.val(response_split[1]);
				e_desc.val(response_split[2]);

				if (err_num == 1) {

					e_name.data('default_value', response_split[1]);
					e_desc.data('default_value', response_split[2]);
					e_embed.html(response_split[3]);

					var e_embimg = jQuery("div#link-url-embed-content img");
					e_avimg_cur.attr("src", e_embimg.attr("src"));
					e_avimg_cur.attr("alt", e_embimg.attr("alt"));
					e_avimg_cur.removeAttr("width");
					e_avimg_cur.removeAttr("height");

					e_embed.slideDown(750, function() {
						bindEditTextClick();
						if (jQuery("div#link-url-embed-thpick").is(':visible')) {
							bindThumbPickerClick();
						}
						if (!response_split[2]) {
							jQuery("input#link-url-embed-edit-text").attr("checked", "checked");
							e_fields.fadeIn(750);
						}
					});

				} else {
					e_fields.fadeIn(750);
				}
			}
			e_loader.toggle();
		});
	});

	// toggle avatar options panel
	jQuery("form#link-details-form a#link-avatar-fields-toggle").click(
		function() {
			jQuery("div#link-avatar-fields").toggle(500, function(){
				var state = jQuery("input#link-avatar-fields-display");
				state.val((1 == state.val()) ? 0 : 1);
			});
		}
	);

	// toggle advanced settings panel
	jQuery("form#link-details-form a#link-settings-fields-toggle").click(
		function() {
			jQuery("div#link-settings-fields").toggle(500, function(){
				var state = jQuery("input#link-settings-fields-display");
				state.val((1 == state.val()) ? 0 : 1);
			});
		}
	);

	// disable right click for avatars that are based on embeded images to comply with their TOS
	jQuery("img.avatar-embed").bind("contextmenu",
		function(){
			return false;
		}
	);
	
});