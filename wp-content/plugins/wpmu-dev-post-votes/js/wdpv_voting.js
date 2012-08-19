(function ($) {
$(function () {
	
function getPostId ($el) {
	return parseInt($el.find('input:hidden:not(.wpdv_blog_id)').val());
}
function getBlogId ($el) {
	return parseInt($el.find('input:hidden.wdpv_blog_id').val());
}

function updateResultBoxes (bid, pid) {
	$.post(_wdpv_ajax_url, {"action": "wdpv_vote_results", "blog_id": bid, "post_id": pid}, function (result) {
		$('.wdpv_vote_result').each(function () {
			var $me = $(this);
			var mpid = getPostId($me);
			var mbid = getBlogId($me);
			if (pid == mpid && bid == mbid) { // If we have what we need, use that 
				$me.find('.wdpv_vote_result_output')
					.html(result.data)
					.removeClass('wdpv_waiting')
				;
			}
			// If we got here, we're on multiple posts page. Request update.
			$.post(_wdpv_ajax_url, {"action": "wdpv_vote_results", "blog_id": mbid, "post_id": mpid}, function (result) {
				$me.find('.wdpv_vote_result_output')
					.html(result.data)
					.removeClass('wdpv_waiting')
				;
			});
		});
	});
}

function doVote ($me, vote) {
	var pid = getPostId($me);
	var blog_id = getBlogId($me);
	var oldBg = $me.css('background-image');

	// Disable all voting buttons for this post
	$('.wdpv_vote_up, .wdpv_vote_down').each(function () {
		var $obj = $(this);
		if (getPostId($obj) == pid && getBlogId($obj) == blog_id) {
			$obj
				.unbind('click')
				.addClass('wdpv_disabled')
			;
		}
	});
	
	// Show loader on all result containers while we load the response
	$('.wdpv_vote_result').each(function () {
		var $me = $(this);
		var mpid = getPostId($me);
		var mbid = getBlogId($me);
		if (pid == mpid && blog_id == mbid) { // If we have what we need, use that 
			$me.find('.wdpv_vote_result_output')
				.empty()
				.addClass('wdpv_waiting')
			;
		}
	});
	
	// Update the post votes
	$.post(_wdpv_ajax_url, {"action": "wdpv_record_vote", "wdpv_vote": vote, "blog_id": blog_id, "post_id": pid}, function (resp) {
		updateResultBoxes(blog_id, pid);		
	});	
}

function voteUp () {
	doVote($(this), "+1");
	return false;	
}
function voteDown () {
	doVote($(this), "-1");
	return false;	
}

$('.wdpv_vote_up').click(voteUp);
$('.wdpv_vote_down').click(voteDown);

});
})(jQuery);