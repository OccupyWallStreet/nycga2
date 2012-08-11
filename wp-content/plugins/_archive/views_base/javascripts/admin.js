jQuery( document ).ready( function( $ ) {
	$('.thickbox').click( function() {
		img_loc = $(this).prev('input');
	});
	window.send_to_editor = function(html) {
		imgurl = $('img',html).attr('src');
		img_loc.val(imgurl);
		tb_remove();
	}
	$("[abbr]").each(function(index, value){
		$(this).html($('#'+$(this).attr('abbr')).html());
	});
	$("#div_custom_header").show();
	$("#div_custom_header ~ table:first").remove();
	$("#enable_facebook").closest('tr').remove();
	$("#enable_twitter").closest('tr').remove();
	$("#enable_linkedin").closest('tr').remove();
	$("#enable_google_plus").closest('tr').remove();
	$("#enable_flickr").closest('tr').remove();
	
});