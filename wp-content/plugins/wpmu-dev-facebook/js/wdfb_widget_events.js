(function ($) {
$(function () {

function init_ui () {
	$(".wdfb_date_threshold").live('focus', function () {
		$(this).datepicker({
			dateFormat: 'yy-mm-dd'
		});
	});
}

function init () {
	if (typeof FB != 'object') return false; // Don't even bother
	FB.api({
		"method": "fql.query",
		"query": "SELECT create_event,rsvp_event,read_stream FROM permissions WHERE uid=me()"
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
			$('.wdfb_widget_events_home').html(
				'<div class="error below-h2">' + l10nWdfbEventsEditor.insuficient_perms + '<br />' + 
					'<a class="wdfb_grant_events_perms" href="#" >' + l10nWdfbEventsEditor.grant_perms + '</a>' +
				'</div>'
			);
			$(".wdfb_grant_events_perms").live("click", function () { 
				var $me = $(this);
				var locale = $me.attr("wdfb:locale");
				FB.login(function () {
					window.location.reload(true);
				}, {
					"scope": 'create_event,rsvp_event,read_stream'
				});
				/*
				FB.ui({ 
					"method": "permissions.request", 
					"perms": 'create_event,rsvp_event,read_stream',
					"display": "iframe"
				}, function () {
					window.location.reload(true);
				});
				*/ 
				return false; 
			}); 
		}
	});
}

if (typeof FB == 'object') {
	FB.getLoginStatus(function (resp) {
		init();
	});
}
	
});
})(jQuery);