jQuery(function($) { // DOM READY WRAPPER
	wpmudev.init();
	
	$('.main-community-topics ul li:first p a span.ui-hide-triangle').addClass('ui-show-triangle');
  
	// if clicked outside updatesPanel when it's expanded, collapse it
	$('html').on('click', function() {
		wpmudev.updatesPanel.hide();
	});
	// updatesPanes show / hide button handler
	$('a.updates-fold').on('click', function(e) {
		e.stopPropagation();
		e.preventDefault();
		($('#updates-data').hasClass('updates-data-active')) ? wpmudev.updatesPanel.hide() : wpmudev.updatesPanel.show();
	});

	// ONLY ACTIVATE SUGGESTIVE SEARCH IF PLACAHOLDER VARS ARE DEFINED
	if ((typeof suggestedProjects) !== 'undefined') {
		$('#suggestive-dash-search')
			.attr("data-search", "plugin")
			.doubleSuggest({
				localSource  : suggestedProjects,
				remoteSource : false,
				selectValue  : "name",
				seekValue    : "name",
				minChars     : 2,
				onSelect: function (data) {
					if ("type" in data) $('#suggestive-dash-search').attr("data-search", data.type);
				},
				resultsComplete: function () {
					// Hide results if nothing to show
					if (!$(".ds-result-item").length) $("#ds-results-suggestive-dash-search").hide();
					else $("#ds-results-suggestive-dash-search").show();
				}
			})
			.on('keydown', function (e) {
				if (9 == e.keyCode || 39 == e.keyCode) {
					var $el = $(".ds-result-item:first");
					if ($el.length) {
						var data = $el.data();
						if ("type" in data) $('#suggestive-dash-search').attr("data-search", data.type);
						$("#suggestive-dash-search").val($el.text());
						return false;
					}
				} else if (13 == e.keyCode) {
					$("#project-search-go").click();
				}
			});
	}
	//handle forum search box
	$('#forum-search-go').click(function() {
		var searchUrl = 'http://premium.wpmudev.org/forums/search.php?q=' + $('#forum-search-q').val();
		window.open(searchUrl, '_blank');
		return false;
	});
	//catch the enter key
	$('#forum-search-q').keypress(function(e) {
			if(e.which == 13) {
				$(this).blur();
				$('#forum-search-go').focus().click();
			}
	});
	
	// Handle project search box
	$("#project-search-go").click(function () {
		var tmp = window.location;
		var scope = ("theme" == $('#suggestive-dash-search').attr("data-search")) ? '?page=wpmudev-themes' : '?page=wpmudev-plugins';
		tmp.hash = "#search=" + $("#suggestive-dash-search").val();
		tmp.search = scope || '?page=wpmudev-plugins';
		window.location = tmp;
	});
}); // END OF DOM WRAPPER


/* ===========================
   FUNCTION DECLARATIONS BELOW 
*/

var wpmudev = {
	tooltip: function(selector) {
		var tips  = selector,
			tipsl = tips.length,
			i;

		for (i = 0; i < tipsl; i++) {
			jQuery(tips[i]).on('mouseenter mouseleave', function() {
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
	},

	// need to fall-back on jQuery based animation if no CSS3 transition
	updatesPanel: {
		hide: function() {
			jQuery('#updates-data').removeClass('updates-data-active');
			jQuery('.overlay').animate({
				'opacity': '0.1'
			}, 500, function() {
				jQuery('.overlay').css('display', 'none')
			});
			jQuery('a.updates-fold').html('<span class="symbol">{</span>&nbsp;&nbsp;&nbsp;show');
		},

		show: function() {
			jQuery('#updates-data').addClass('updates-data-active');
			jQuery('.overlay').css('display', 'block').animate({
				'opacity': '0.8'
			}, 700);
			jQuery('a.updates-fold').html('<span class="symbol">}</span>&nbsp;&nbsp;hide');
		},

		init: function() {
			var that = wpmudev.updatesPanel;
			jQuery('#updates-data').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				that.show();
			});
		}
	},

	collapsableElements: function() {
		jQuery('.accordion-title p').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			var $_txtSpan  = jQuery(this).find('span.ui-hide-triangle').prev(),
				$_triangle = jQuery(this).find('span.ui-hide-triangle'),
				$_content  = jQuery(this).parent().find('ul');

			function show() {
				$_txtSpan.text('HIDE');
				$_content.slideDown( 'fast','swing' );
			}

			function hide() {
		      $_txtSpan.text('SHOW');
		      $_content.slideUp( 'fast','swing' );
			}

			if($_txtSpan.length){
			  //$_txtSpan.text() === 'SHOW' ? show() : hide();
			  $_content.is(":visible") ? hide() : show();
			  $_triangle.toggleClass('ui-show-triangle');
			}
		});
	},

	hoverToExpand: function() {
		jQuery('ul.hover-to-expand').on('mouseenter', 'li', function() {
			var $_productSearchField = jQuery('#suggestive-dash-search'),
				$_productSearchPanel = jQuery('div#ds-results-suggestive-dash-search');

			if($_productSearchPanel.filter(':visible').length || $_productSearchField.is(':focus')){
				} else {
					jQuery(this).find('div.expanded-content').css({
						'opacity': '1',
						'z-index': '3'
					}).animate({
						'top': '-50%'
					}, 'fast').find('ul').slideDown('fast');
			  }
		}).on('mouseleave', 'li', function() {
			jQuery(this).find('div.expanded-content').css('z-index', '1').animate({
				'top': '0%'
			}, 'fast').find('ul').slideUp('fast', function() {
				jQuery(this).parent().css('opacity', '0');
			});
		});
	},
  // panelContainerHeight prop. containing returned elem height. 
  // i might need it later on, for comparison
  panelContainerHeight : undefined,

	layoutCalculations: function() {

		var $_spacer   = jQuery('.spacer'),
			  $_base     = jQuery('#dash-main-content').height(),
			  $_gradient = jQuery('#right-section-gradient'),
			  that       = this;

		jQuery(window).load(function() {
			$_spacer.css( 'padding-bottom', ( 80 + $_base) );
			$_gradient.css( 'padding-bottom', ( 60 + $_base) );
			return (that.panelContainerHeight = $_base);
		});
	},

	init: function() {
		this.layoutCalculations();
		this.tooltip(jQuery('.tooltip'));
		this.expandOnHover(jQuery('table.hoverExpand'));
		this.updatesPanel.init();
		this.collapsableElements();
		this.hoverToExpand();
	}
}; // end WPMU DEV obj
