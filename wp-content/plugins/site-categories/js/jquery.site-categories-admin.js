/**
 *
 * Author: Derek Herman 
 * URL: http://valendesigns.com
 * Email: derek@valendesigns.com
 *
 */
 

/**
 *
 * Upload Option
 *
 * Allows window.send_to_editor to function properly using a private post_id
 * Dependencies: jQuery, Media Upload, Thickbox
 *
 */
(function ($) {
  uploadOptionBCAT = {
    init: function () {
		var formfield,
			postID = '',
			dest_field_number = '',
			wp_image_id = '',
			btnContent = '',
			tbframe_interval,
			formfieldName = '',
			qs_parms = '';
		
		// On Click
		$('input#bcat_image_upload').live("click", function () {
	
			if (!qs_parms)
				qs_parms = getUrlParams();
			
			if (qs_parms['post'])
				postID = qs_parms['post'];

			dest_field_number 	= $(this).attr('rel');		

			//dest_field_src 		= 'bcat_image_src';	// This is when the image <img src="" /> will be sent
		
			//http://inc331mu.com/wp-admin/media-upload.php?post_id=5&TB_iframe=1&width=640&height=698
			tb_show('', 'media-upload.php?type=image&amp;post_id=0&amp;TB_iframe=1&amp;width=640&amp;height=698');

	        tbframe_interval = setInterval(function() { jQuery('#TB_iframeContent').contents().find('.savesend .button').val('Use this image'); }, 2000);
	        return false;
		});

		$('input#bcat_image_remove').live("click", function () {
			$('input#bcat_image_upload').show();
			$('input#bcat_image_remove').hide();
			$('input#bcat_image_id').val('');
			
			var image_default_src = $('img#bcat_image_src').attr('rel');
			$('img#bcat_image_src').attr('src', image_default_src);
			
			return false;
		});

		window.original_send_to_editor = window.send_to_editor;
			window.send_to_editor = function(html) {

				var image = /(^.*\.jpg|jpeg|png|gif|ico*)/gi;
				var document = /(^.*\.pdf|doc|docx|ppt|pptx|odt*)/gi;
				var audio = /(^.*\.mp3|m4a|ogg|wav*)/gi;
         			var video = /(^.*\.mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2*)/gi;

				clearInterval(tbframe_interval);

				itemurl = $(html).attr('href');

				// The 'html' value provided has anchor (<a></a>) then inside the <img />
				// From the html img we get the class="" values and find the one starting with 'wp-image-' then extract the substring.
				var classes = $(html).find('img').attr('class').split(/\s+/);
				if (classes)
				{
					var wp_image_id = '';

					// wp-image-
					for(var i = 0; i < classes.length; i++) {
						var className = classes[i];

						if (className.length < 9) continue;
						else if (className.substring(0, 9) == "wp-image-")
						{
							wp_image_id = className.substring(9, className.length);
							$('input#bcat_image_id').val(wp_image_id);
							break;
						}
					}
				}
				
				if ( UrlExists(itemurl) ) {
					if (itemurl.match(image)) {
						$('img#bcat_image_src').attr('src', itemurl);
						$('img#bcat_image_src').show();
						$('input#bcat_image_upload').hide();
						$('input#bcat_image_remove'). show();
					} 
				}

				tb_remove();
			}
		}
	};
$(document).ready(function () {
	uploadOptionBCAT.init()
	
	// Show/Hide the Categories Grid layout selection
	$('select#site-categories-show-style').change(function() {
	  if ($(this).val() == "grid") {
		$('.site-categories-non-grid-options').hide();
		$('.site-categories-accordion-options').hide();
		$('.site-categories-grid-options').show();
	} else if ($(this).val() == "accordion") {
		$('.site-categories-non-accordion-options').hide();
		$('.site-categories-grid-options').hide();
		$('.site-categories-accordion-options').show();
	} else {
		$('.site-categories-grid-options').hide();		
		$('.site-categories-accordion-options').hide();
		$('.site-categories-non-grid-options').show();
	}
	});
})
})(jQuery);

function UrlExists(url) {
  var http = new XMLHttpRequest();
  http.open('HEAD', url, false);
  http.send();
  return http.status!=404;
}


function getUrlParams(url_string) {
	if (!url_string)
		url_string = window.location.search;
		

    var result = {};
    var params = (url_string.split('?')[1] || '').split('&');
    for(var param in params) {
        if (params.hasOwnProperty(param)) {
            paramParts = params[param].split('=');
            result[paramParts[0]] = decodeURIComponent(paramParts[1] || "");
        }
    }
    return result;
}