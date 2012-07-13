jQuery(document).ready( function() {
	jQuery(".widget div#links-list-options a").live('click',
		function() {
			jQuery('#ajax-loader-links').toggle();

			jQuery(".widget div#links-list-options a").removeClass("selected");
			jQuery(this).addClass('selected');

			jQuery.post( ajaxurl, {
				action: 'widget_links_list',
				'cookie': encodeURIComponent(document.cookie),
				'_wpnonce': jQuery("input#_wpnonce-links").val(),
				'max_links': jQuery("input#links_widget_max").val(),
				'filter': jQuery(this).attr('id')
			},
			function(response)
			{
				jQuery('#ajax-loader-links').toggle();
				links_widget_response(response);
			});

			return false;
		}
	);
});

function links_widget_response(response) {
	response = response.substr(0, response.length-1);
	response = response.split('[[SPLIT]]');

	if ( response[0] != "-1" ) {
		jQuery(".widget ul#links-list").fadeOut(200,
			function() {
				jQuery(".widget ul#links-list").html(response[1]);
				jQuery(".widget ul#links-list").fadeIn(200);
			}
		);

	} else {
		jQuery(".widget ul#links-list").fadeOut(200,
			function() {
				var message = '<p>' + response[1] + '</p>';
				jQuery(".widget ul#links-list").html(message);
				jQuery(".widget ul#links-list").fadeIn(200);
			}
		);
	}
}