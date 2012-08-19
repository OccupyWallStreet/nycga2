jQuery(document).ready(function($) {


	jQuery('.most-popular-topics h1').parent().each(function () {
		var $me = $(this);
		if (!$me.is("li")) return true;
		$me.on('click', function (e) {
			if ($(e.target).is('a[href^="http://"]')) return true; // Allow external links to do their thing.
			var $_content  = $me.parent().find('ul');

			$_content.is(":visible")
				? $_content.find("table").hide().end().slideUp('fast')
				: $_content.slideDown('fast').find("table").show()
			;
			return false;
		});
	});

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

});