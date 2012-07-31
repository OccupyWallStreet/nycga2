(function ($) {
$(function () {
	
	
function selectBoxFromTarget ($me) {
	var id = $me.attr('href');
	var $target = $(id);
	if (!$target.length) return false;
	$(".wdfb_connect_target").css('display', 'none');
	$target.css('display', 'block');
	$(".wdfb_connect_widget_container ul.wdfb_connect_widget_action_links li a").removeClass('wdfb_active');
	$me.addClass('wdfb_active');
}

$(".wdfb_connect_widget_container ul.wdfb_connect_widget_action_links li a")
	.unbind('click')
	.click(function (e) {
		e.stopPropagation();
		selectBoxFromTarget($(this));
		return false;
	})
;

selectBoxFromTarget( $(".wdfb_connect_widget_container ul.wdfb_connect_widget_action_links li:first a") );
	
});
})(jQuery);