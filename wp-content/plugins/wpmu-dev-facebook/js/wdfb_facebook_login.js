var  _wdfb_notifyAndRedirect;

(function ($) {
$(function () {
	
_redirecting = false;
	
function notifyAndRedirect () {
	if (_redirecting) return false;
	_redirecting = true;
	
	var redir = '';
	$('fb\\:login-button').each(function () {
		redir = $(this).attr('redirect-url');
		if (redir) return false;
	});
	if (!redir) redir = window.location;
	
	// Start UI change
	$('fb\\:login-button').each(function () {
		var $parent = $(this).parent('p');
		$parent.after('<img src="' + _wdfb_root_url + '/img/waiting.gif" class="' + $parent.attr("class") + '">');
		$parent.remove();
	});
	
	/**
	 * A workaround for FB JS SDK issue - auth.login is triggered
	 * *before* the cookie is ever set. Yay.
	 */
	function do_redirect_when_cookie_is_set () {
		if (document.cookie.match(/fbsr_\d+/)) {
			if (_wdfb_ajaxurl.match(/^https:/) && 'https:' != window.location.protocol) {
				$.getScript(_wdfb_ajaxurl + '?action=wdfb_perhaps_create_wp_user', function () {
					window.location = redir;
				});
			} else {
				$.post(_wdfb_ajaxurl, {"action": "wdfb_perhaps_create_wp_user"}, function () {
					window.location = redir;
				});
			}
		} else {
			setTimeout(do_redirect_when_cookie_is_set, 200);
		}
	}
	do_redirect_when_cookie_is_set();
}
_wdfb_notifyAndRedirect = notifyAndRedirect;

});
})(jQuery);