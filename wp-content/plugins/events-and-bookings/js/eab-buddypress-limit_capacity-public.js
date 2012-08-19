(function ($) {

$(function () {
	var $inputs = $('input[name="quantity"]');
	$inputs.each(function () {
		var $me = $(this);
		var max = parseInt($me.attr("max"));
		if (!max) return true;
		
		$me.on('keyup', function (e) {
			var current = parseInt($me.val());
			if (current > max) {
				$me.val('');
				return false;
			}
			return true;
		});
	});
});

})(jQuery);