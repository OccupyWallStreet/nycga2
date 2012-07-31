/*	
(function ($) {
$(function () {

$("#post_as_page").change(function () {
	if ($("#post_as_page").is(":checked")) FB.getLoginStatus(function (resp) {
		FB.api({
			"method": "fql.query",
			"query": "SELECT offline_access FROM permissions WHERE uid=me()"
		}, function (resp) {
			var all_good = true;
			try {
				$.each(resp[0], function (idx, el) {
					if(el !== "1") all_good = false;
				});
			} catch (e) {
				all_good = false;
			}
			if (!all_good) {
				FB.login(function () {
					FB.Dialog.remove(FB.Dialog._active);
					window.location.reload(true);
				}, {
					"scope": 'offline_access'
				});
			}
		});
	});
	return true;
});

});
})(jQuery);
*/