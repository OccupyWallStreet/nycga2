jQuery(function($) { // DOM READY WRAPPER
	wpmudev.init();
});

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

	init: function() {
		this.tooltip(jQuery('.tooltip'));
	}
}; // end WPMU DEV obj