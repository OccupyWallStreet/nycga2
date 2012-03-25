jQuery(document).ready(function() {
	var fileInput = '';

	jQuery('.upload_image_button').click(function() {
		fileInput = jQuery(this).parent().prev('input.uploadfield');
		//console.log(fileInput);
		formfield = jQuery('#upload_image').attr('name');
		post_id = jQuery('#post_ID').val();
		tb_show('', 'media-upload.php?post_id='+post_id+'&amp;type=image&amp;TB_iframe=true');
		return false;
	});

	jQuery('.upload_image_reset').click(function() {
		jQuery(this).parent().prev('input.uploadfield').val('');
	});

	// user inserts file into post. only run custom if user started process using the above process
	// window.send_to_editor(html) is how wp would normally handle the received data

	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html){

		if (fileInput) {
			fileurl = jQuery('img',html).attr('src');

			fileInput.val(fileurl);

			tb_remove();

		} else {
			window.original_send_to_editor(html);
		}
	};

});