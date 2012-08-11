(function ($) {
	
/* ===========================
   FUNCTION DECLARATIONS BELOW 
*/

// Adding case-insensitive :contains, mapping as :CONTAINS
jQuery.expr[':'].CONTAINS = function(a, i, m) {
  return jQuery(a).text().toUpperCase()
      .indexOf(m[3].toUpperCase()) >= 0;
};
jQuery.expr[':'].NOT_CONTAINS = function(a, i, m) {
  return !jQuery(a).text().toUpperCase()
      .indexOf(m[3].toUpperCase()) >= 0;
};


var devlistings = {

	handleHover : function(){
		var $_listing = jQuery('div.listing'),
		gradient      = '<div class="hover-gradient"></div>',
		$_target      = $_listing.next();

		jQuery('div.listings')
		  .on('mouseenter', 'ul > li > div.listing' , function(){
		  	var $_e    = jQuery( this ).next(),
		  	$_parent   = jQuery( this ).parent(),
		  	$_listings = jQuery( ' div.listings ul li ' ),
		  	$_gradient = jQuery( this ).next().find('div.hover-gradient');
		  	if (!($_gradient.length)) {
		  		jQuery('div.hover-gradient').remove();
		  		$_e.append(gradient);
		  		$_e.find('div.hover-gradient').animate({ opacity : 1 },250 );
		  	}
		  })
		  .on('mouseleave', function(){
		  	var $_listings = jQuery( ' div.listings ul li ' );
		  		jQuery('div.hover-gradient').remove();
		  });
	},
	
	"get_stack": function () {
		var $stack = $("#stack");
		if (!$stack.length) {
			$("body").append('<ul id="stack" style="display:none" />');
			$stack = $("#stack");
		}
		return $stack;
	},
	
	"get_root": function () {
		return $(".listings ul").first();
	},
	
	"get_page_type": function () {
		var $root = devlistings.get_root();
		return $root.attr('data-page_type');
	},
	
	"project_details": function () {
		var $_details = $('#listing-details-container');
		var $me = $(this);
		var $item = $me.parents('li');
		var $title = $me.parents('li').find('div.listing h1').html();
		var $excerpt = $me.parents('li').find('div.listing span.full-excerpt').html();
		var $link = $me.parents('li').find('div.listing a').attr('href');
		var $button = $me.parents('li').find('div.install_wrap span.target');
		var $first = $item;
		var itempos = $item.offset();
	
		while ($first.prev().length) {
			var $prev = $first.prev();
			if (!$prev.is("li")) break;
			var prevpos = $prev.offset();
			if (prevpos.left >= itempos.left) break;
			$first = $prev;
		}

		$first.before($_details);
		var id = $item.attr("data-project_id");
		
		$(".listing-details-wrapper #listing-title").html($title);
		$(".listing-details-wrapper #listing-excerpt").html($excerpt);
		$(".listing-details-wrapper #listing-description").empty();
		$(".listing-details-wrapper #loading-details").show();
		$(".listing-details-wrapper #listing-readmore").attr('href', $link);
		$(".listing-details-wrapper #listing-install")
			.empty()
			.append($button.clone(true))
		;
		var screenshotHtml = '';
		var screenArray = project_screenshots[id];
		for (var i = 0; i < screenArray.length; i++) {
			screenshotHtml = screenshotHtml + '<li><a href="#"><img src="' + screenArray[i]['url'] + '" width="100%" height="100" alt="' + screenArray[i]['desc'] + '"	/></a><div></div></li>';
		}
		$(".listing-details-wrapper .listing-screens ul").html(screenshotHtml);
		$(".listing-details-wrapper .image-of").html( '1 / ' + screenArray.length );
			
		$_details.slideDown(500, 'swing');
		
		var pos = $_details.offset().top - 30;
		$("body,html").animate({
			'scrollTop': pos
		}, 300);
			 	
		$.post(ajaxurl, {
			"action": "wpmudev_get_project_details",
			"wdp_id": id
		}, function (data) {
			$(".listing-details-wrapper #listing-description").html(data);
			$(".listing-details-wrapper #loading-details").hide();
				
			$("div.overlay-details a.symbol").click();
		}, 'html');
		
		return false;
	},
	
	"sort_projects": function () {
		var sort = $("#sort_projects").val();
		if (sort == 'released') {
			$('li.listing-item').tsort('',{attr:'data-released', order:'desc'});
		} else if (sort == 'updated') {
			$('li.listing-item').tsort('',{attr:'data-updated', order:'desc'});
		} else if (sort == 'popularity') {
			$('li.listing-item').tsort('',{attr:'data-popularity', order:'desc'});
		} else if (sort == 'downloads') {
			$('li.listing-item').tsort('',{attr:'data-downloads', order:'desc'});
		} else if (sort == 'alphabetical') {
			$('li.listing-item').tsort('h1');
		}
	},
	
	"filter_projects": function () {
		var text = $("#filter_projects").val();
		var $root = devlistings.get_root();
		$root
			.find("li").addClass("search-hidden").end()
			.find("li:CONTAINS('" + text + "')").removeClass("search-hidden")
		;
		//$root.find("li:NOT_CONTAINS('" + text + "')").filter(":visible").hide();
		devlistings.update_results_count();
	},
	
	"search_filter_process": function (e) {
		if (9 == e.keyCode || 39 == e.keyCode) {
			if ($(".ds-result-item:first").length) {
				$("#filter_projects").val($(".ds-result-item:first").text());
				devlistings.filter_projects();
				return false;
			}
		} else if (13 == e.keyCode) {
			devlistings.filter_projects();
		}
	},

	"filter_tags": function () {
		var $root = devlistings.get_root();
		var tag_id = $("#filter_tags").val();
		if (!tag_id) {
			$root.find("li").show();
			devlistings.update_results_count();
			return true;
		}
		var tag = project_tags[tag_id];
		if (!tag) return false;
		
		$root.find("li").hide();
		$.each(tag.pids, function (idx, pid) {
			$root.find('li[data-project_id="' + pid + '"]').show();
		});
		devlistings.update_results_count();
	},
	
	"check_hash_request": function () {
		var hash = window.location.hash;
		if (!hash) return false;
		
		var pid = hash.match(/pid=(\d+)/); 
		if (pid && 2 == pid.length) {
			pid = pid[1];
			var $element = $('li[data-project_id="' + pid + '"]');
			if (!$element.length) return false;
			$(window).load(function () {
				$element.find("img").click();
			});
		}
		
		var search = hash.match(/search=(.*)/);
		if (search && 2 == search.length) {
			search = search[1];
			$(window).load(function () {
				$("#filter_projects")
					.val(search)
					.trigger('change')
				;
			});
		}		
		
	},
	
	"update_results_count": function () {
		var count = $('li.listing-item:visible').length;
		$('#results-count').html(count);
		if (!count) {
			$('div.listing-divider, div.listing-divider2').hide();
			$('#no-results').fadeIn('slow');
		} else {
			$('#no-results').hide();
			$('div.listing-divider, div.listing-divider2').show();
		}
	},
	
	"max_screenshot_height_adjust": function () {
		$(".listing-details-wrapper .listing-details-overlay .screenshot-container img").css("max-height", $(".listing-details-container.grid_container .listing-copy").height()-10);
	},
	
	"hide_install_message": function () {
		var $me = $("#_install_hide_msg");
		if (!$me.is(":checked")) return; // Nothing to do
		$.post(ajaxurl, {
			"action": "wpmudev_hide_install_message"
		}, function (data) {
			$("a.button.install_setup").removeClass("install_setup").off('click', devlistings.install_setup);
		});
	},
	
	"install_setup": function () {
		var $me = $(this);
		$("#_install_setup-wrapper")
			.after("<div id='_install_setup-background' />")
		;
		$("#_install_setup-background")
			.css({
				"position": "absolute",
				"height": $("#wpwrap").height(),
				"width": $("#wpwrap").width(),
				"background": "#000",
				"opacity": .7,
				"z-index": 9998
			})
			.offset({"top": 0, "left": 0})
			.show()
		;
		$("#_install_setup-wrapper")
			.css({
				"position": "fixed",
				"top": 100,
				"left": ($("#wpwrap").width() - 500) / 2,
				"width": 500,
				"z-index": 9999
			})
			.css("top", ($(window).height() - $("#_install_setup-wrapper").height()) /2)
			.find("a.install_plugin").attr("href", $me.attr("href")).end()
			.show()
		;
		$("#_install_setup-background").on('click', devlistings.install_setup_close);
		return false;
	},
	
	"install_instructions": function () {
		$("#_install_setup-wrapper").hide();
		$("#_install_setup-auto_install-wrapper")
			.css({
				"position": "fixed",
				"top": 100,
				"left": ($("#wpwrap").width() - 500) / 2,
				"width": 500,
				"z-index": 9999
			})
			.css("top", ($(window).height() - $("#_install_setup-auto_install-wrapper").height()) /2)
			.show()
		;
		return false;
	},
	
	"install_setup_close": function () {
		$("#_install_setup-background").remove();
		$("#_install_setup-wrapper").hide();
		$("#_install_setup-auto_install-wrapper").hide();
		return false;
	},
	
	"install_plugin": function () {
		var $me = $(this);
		if ($me.parents("#_install_setup-wrapper").length) return true; // Manual install
		
		var text = $me.text();
		var link = $me.attr("href");
		var $target = $me.parents('span.target');
		
		$me.html('<img src="' + loading_spinner + '" /> ' + $me.attr("data-downloading"));
		$('<div />').load(link + " #wpbody-content", function (download_html) {
			var $url = $(download_html).find('a[href*="action=activate"]:first');
			if (!$url.length) { // Something went wrong with the download
				$target.first().html($("#_install_error-placeholder").html());
				$target.find('.tooltip section').html($(download_html).find(".wrap p:not(:last)"));
				return false;		
			}
			$me.parents('span.target').first().html($("#_installed-placeholder").html());
		});
		return false;
	},
	
	"install_and_activate_plugin": function () {
		var $me = $(this);
		var text = $me.text();
		var link = $me.attr("href");
		var $target = $me.parents('span.target');
		
		$me.html('<img src="' + loading_spinner + '" /> ' + $me.attr("data-downloading"));
		$('<div />').load(link + " #wpbody-content", function (download_html) {
			var $url = $(download_html).find('a[href*="action=activate"]:first');
			if (!$url.length) { // Something went wrong with the download
				$target.first().html($("#_install_error-placeholder").html());
				$target.find('.tooltip section').html($(download_html).find(".wrap p:not(:last)"));
				return false;				
			}
			$me.html('<img src="' + loading_spinner + '" />' + $me.attr("data-installing"));
			$('<div />').load($url.attr("href"), function (activate_html, status, xhr) {
				var $page = $(activate_html);
				// Check if everything went well...
				var $error_frame = $page.find("div#message.error").length
					? $page.find("div#message.error") // Try easy match first
					: $page.find('iframe[src*="action=error_scrape"]') // Fall back to error iframe
				;
				if (status == "error") {
					$error_frame = $page.find("body");
					$error_frame = $error_frame.length ? $error_frame : 'Something went wrong with plugin activation';
				}
				if ($error_frame.length) { // Something went wrong with the install
					$target.first().html($("#_install_error-placeholder").html());
					$target.find('.tooltip section').html($error_frame);
					return false;			
				}
				
				// Replace button with "installed" message
				$me.parents('span.target').first().html($("#_installed-placeholder").html());
			});
		});
		return false;
	},
	
	"install_theme": function () {
		var $me = $(this);
		var text = $me.text();
		var link = $me.attr("href");
		
		$me.html('<img src="' + loading_spinner + '" /> ' + $me.attr("data-downloading"));
		$('<div />').load(link + " #wpbody-content", function (download_html) {
			$me.parents('span.target').first().html($("#_installed-placeholder").html());
		});
		return false;
	},
	
	tooltip: function(selector) {
		var tips  = selector,
			tipsl = tips.length,
			i;

		for (i = 0; i < tipsl; i++) {
			jQuery(tips[i]).live('mouseenter mouseleave', function() {
				jQuery(this).has('section').toggleClass('tooltipHover');
			});
		}
	},

	expandOnHover: function(tableSelector) {
		var arg = arguments,
			l   = arg.length,
			i   = 0;
		for (i; i < l; i++) {
			jQuery(arg[i]).on('mouseenter', 'tr', function() {
				var w = jQuery(this).width(),
					h = jQuery(this).height();
				jQuery(this).next().find('div.reason').css({
					'top'     : -1,
					'left'    : 0,
					'width'   : w,
					'display' : 'block'
				});

			}).on('mouseleave', 'tr', function() {
				jQuery(this).next().find('div.reason').css({
					'display': 'none'
				});
			});
		}
	}
}
	
$(document).ready(function() {
  devlistings.handleHover();
	devlistings.update_results_count();
	
  jQuery('a.close-plugin-details').live('click', function(){
  	jQuery('div.listing-details-wrapper').slideUp(300, 'swing');
  	return false;
  });
  jQuery('div.listing-details-wrapper').hide();
  	
  // Project details
	$('div.listings').on('click', 'ul > li > div.listing' , devlistings.project_details);
	// Sorting
	$("#sort_projects").on('change', devlistings.sort_projects);
	// Filtering
	$("#filter_tags").on('change', devlistings.filter_tags);
	// Searching
	$("#filter_projects")
		.on('keyup', devlistings.filter_projects)
		.on('change', devlistings.filter_projects)
		.on('keydown', devlistings.search_filter_process)
	;
	$("#clear_search").unbind('click').click(function(){
		$("#filter_projects").val('');
		return false;
	});
	$("#no-results a").click(function(){
		$("#filter_projects").val('').trigger('change');
		return false;
	});
	devlistings.tooltip($('.tooltip'));
	
	// ONLY ACTIVATE SUGGESTIVE SEARCH IF PLACAHOLDER VARS ARE DEFINED
	if ((typeof suggestedProjects) !== 'undefined') {
		$('#filter_projects').doubleSuggest({
			localSource  : suggestedProjects,
			remoteSource : false,
			selectValue  : "name",
			seekValue    : "name",
			minChars     : 2,
			resultsComplete: function () {
				// Hide results if nothing to show
				if (!$(".ds-result-item").length) $("#ds-results-filter_projects").hide();
				else $("#ds-results-filter_projects").show();
			}
		});
		$("#ds-container-filter_projects + .search-btn").on('click', devlistings.filter_projects);
		$(".ds-result-item").live('click', function () {
			setTimeout(devlistings.filter_projects, 200);
		});
	}

	jQuery('div.overlay-details a.symbol')
	  .live('click', function(){
	  	jQuery('div.listing-details-overlay').fadeOut(300, 'swing');
	  	$(window).unbind('resize', devlistings.max_screenshot_height_adjust);
	  	return false;
	  });

	$('.listing-details-wrapper div.listing-screens > ul').live('click', 'li', function (e) {
		var $me = $(e.target).is("li") ? $(e.target) : $(e.target).parents('li');
		var $img = $me.find('img');
		$(".listing-details-wrapper .listing-details-overlay .screenshot-container img").attr("src", $img.attr("src"));
		$(".listing-details-wrapper .listing-details-overlay .screenshot-description .screenshot-description").html($img.attr("alt"));
		$(".listing-details-wrapper .listing-details-overlay .screenshot-description .image-of").text(
			($me.prevAll('li').length + 1) + ' / ' + $me.parents('div.listing-screens ul').find('li').length 
		);
		
		// Setup nav
		$(".listing-details-wrapper .listing-details-overlay .screenshot-nav a")
			.first().unbind('click').bind('click', function () {
				var $prev = $me.prev(); 
				if ($prev.length && $prev.is("li")) {
					$prev.click();
				} else {
					$(".listing-details-wrapper .listing-details-overlay .screenshot-nav a").removeClass("faded");
					$(this).addClass("faded");
				}
				return false;
			}).end()
			.last().unbind('click').bind('click', function () {
				var $next = $me.next();
				if ($next.length && $next.is("li")) {
					$next.click();
				} else {
					$(".listing-details-wrapper .listing-details-overlay .screenshot-nav a").removeClass("faded");
					$(this).addClass("faded");
				}
				return false;
			}).end()
		;
		// Setup initial faded class
		if ($me.prev().length) $(".listing-details-wrapper .listing-details-overlay .screenshot-nav a").first().removeClass("faded");
		else $(".listing-details-wrapper .listing-details-overlay .screenshot-nav a").first().addClass("faded");
		if ($me.next().length) $(".listing-details-wrapper .listing-details-overlay .screenshot-nav a").last().removeClass("faded");
		else $(".listing-details-wrapper .listing-details-overlay .screenshot-nav a").last().addClass("faded");
		
		$('.listing-details-wrapper div.listing-details-overlay').fadeIn(400, 'swing');
		devlistings.max_screenshot_height_adjust();
		$(window).bind('resize', devlistings.max_screenshot_height_adjust);
		return false;
	});
	
	$("a.button.install_and_activate_plugin").on('click', devlistings.install_and_activate_plugin);
	$("a.button.install_plugin").on('click', devlistings.install_plugin);
	$("a.button.install_setup").on('click', devlistings.install_setup);
	$("a.button.install_instructions").on('click', devlistings.install_instructions);
	$("a.button.install_theme").on('click', devlistings.install_theme);
	$("a._install_setup-close").on('click', devlistings.install_setup_close);
	$("#_install_hide_msg").on('change', devlistings.hide_install_message);
	
	$(".target")
		.on("mouseover", ".tooltip", function () {
			var $sect = $(this).find('section');
			$sect.show();
			return false;
		})
		.on("mouseout", ".tooltip", function () {
			var $sect = $(this).find('section');
			$sect.hide();
			return false;
		})		
	;
	
	// Check hash-type requests
	devlistings.check_hash_request();
	
	//preload loading image
	if (document.images) {
		img1 = new Image();
		img1.src = loading_spinner;
	}
	
}); // END DOM READY
})(jQuery);