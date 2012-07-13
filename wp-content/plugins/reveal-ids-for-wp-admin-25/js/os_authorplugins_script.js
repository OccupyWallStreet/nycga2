jQuery(document).ready(function() {
jQuery("#authorplugins-start").click(function() {
	jQuery("#authorplugins-wrap").hide();
	jQuery.ajax({ 
  		dataType: 'jsonp',
  		jsonp: 'jsonp_callback',
  		url: window.location.protocol + '//www.schloebe.de/api_portfolio.php?cat=wordpress',
  		success: function (j) {
			jQuery.each(j.plugins, function(i,plugin) {
				jQuery('#authorpluginsul').append( '<li><a href="' + plugin.os_script_info_url + '" target="_blank"><span class="post">' + plugin.os_script_title + '</span><span class="hidden"> - </span><cite>version ' + plugin.os_script_version + '</cite></a></li>' ).css("display", "none").fadeIn("slow");
    		});
		}
	});
});
});