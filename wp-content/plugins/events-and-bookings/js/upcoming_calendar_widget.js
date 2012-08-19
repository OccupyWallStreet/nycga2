(function ($) {

$(function () {
	$(document).bind('eab-cuw-render_complete', function () {
		$("table.eab-upcoming_calendar_widget").each(function () {
			var $tbl = $(this);
			$tbl.find("tbody a:not(.eab-upcoming_calendar_widget-navigation-link)").click(function () {
				var $a = $(this);
				var $el = $a.parents('td').find(".wdpmudevevents-upcoming_calendar_widget-info_wrapper");
				if (!$el.length) return false;
				
				var $out = $("#wpmudevevents-upcoming_calendar_widget-shelf");
				if ($out.length) $out.remove();
				$tbl.after('<div id="wpmudevevents-upcoming_calendar_widget-shelf" style="display:none" />');
				$out = $("#wpmudevevents-upcoming_calendar_widget-shelf");
				if (!$out.length) return false;
				
				$out
					.html($el.html())
					.slideDown('slow')
				;
				
				return false;
			});
		});
	});
	$(".eab-upcoming_calendar_widget-navigation-link:not(.eab-cuw-calendar_date)").live('click', function () {
		var $me = $(this);
		var now = $me.parents("tr").find("input.eab-cuw-calendar_date").val();
		var direction = $me.is(".eab-navigation-prev") ? "prev" : "next";
		var unit = $me.is(".eab-time_unit-year") ? "year" : "month";
		
		// Start UI change
		var $out = $("#wpmudevevents-upcoming_calendar_widget-shelf");
		if ($out.length) $out.slideUp('slow');
		$me.parents("tr").find("a.eab-cuw-calendar_date").replaceWith(
			'<img src="' + _eab_data.root_url + 'waiting.gif" />'
		);
		
		$.post(_eab_data.ajax_url, {
			"action": "eab_cuw_get_calendar",
			"now": now,
			"direction": direction,
			"unit": unit
		}, function (data) {
			$me.parents("table").replaceWith(data);
			$(document).trigger('eab-cuw-render_complete');
		});
		return false;
	});
	
	// Init 
	$(document).trigger('eab-cuw-render_complete');
});
})(jQuery);
