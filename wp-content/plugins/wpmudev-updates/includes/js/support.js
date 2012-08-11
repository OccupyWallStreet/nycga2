jQuery(document).ready(function($) {
	
	$('#qa-submit').click(function() {
		$('#qa-form').submit();
		return false;
	});
	$('#qa-form').submit(function() {
		var formReturn = true;
		if (!$('#topic').val()) {
			$('#error_topic').slideDown();
			formReturn = false;
		}
		if (!$('#q-and-a').val() && !$('#q-and-a').find("option:selected").attr("forum_id")) {
			$('#error_project').slideDown();
			formReturn = false;
		}
		if (!$('#post_content').val()) {
			$('#error_content').slideDown();
			formReturn = false;
		}

		if (formReturn) {
			var form_data = $('#qa-form').serialize();
			$('#qa-table .error').hide();
			$('#qa-posting').show();
			$('#qa-form input, #qa-form select, #qa-form textarea').attr('disabled', 'disabled');
			$('#qa-submit').hide();
			$('#qa-form').css('opacity', '0.6');
			$.post(ajaxurl + '?action=wpmudev_support_post', form_data, function(json) {
				if (!json.response) {
					$('#qa-form input, #qa-form select, #qa-form textarea').removeAttr('disabled');
					$('#qa-form').css('opacity', '1');
					$('#qa-posting').hide();
					$('#qa-submit').show();
					$('#error_ajax span').html(json.data);
					$('#error_ajax').show();
				} else {
					$('#qa-form').hide();
					$('#success_ajax a').attr('href', json.data);
					$('#success_ajax').show();
				}
			}, 'json');
		}
		return false;
	});

	$('#q-and-a').change(function() {
		if ($(this).val())
			$('#forum_id').val( $(this).find("option:selected").parent().attr("forum_id") );
		else
			$('#forum_id').val( $(this).find("option:selected").attr("forum_id") );
	});

	//handle forum search box
	$('#forum-search-go').click(function() {
		var searchUrl = 'http://premium.wpmudev.org/forums/search.php?q=' + $('#search_projects').val();
		window.open(searchUrl, '_blank');
		return false;
	});
	//catch the enter key
	$('#search_projects').keypress(function(e) {
			if(e.which == 13) {
					$(this).blur();
					$('#forum-search-go').focus().click();
			}
	});
	
	$('.accordion-title p').on('click', function(e) {
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
});