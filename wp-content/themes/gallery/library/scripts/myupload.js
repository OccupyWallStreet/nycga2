jQuery(document).ready(function() {
 
jQuery('#upload_image_button').click(function() {
											  window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#upload_image').val(imgurl);
 tb_remove();
 
 
}
 
 postid = jQuery('#post-id').val();
 tb_show('', 'media-upload.php?post_id=' + postid + '&type=image&TB_iframe=true');
 return false;
});
 
 
});
 
jQuery(document).ready(function() {
 
jQuery('#upload_image_button2').click(function() {
											   window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#upload_image2').val(imgurl);
 tb_remove();
 
 
}
 
postid = jQuery('#post-id').val();
tb_show('', 'media-upload.php?post_id=' + postid + '&type=image&TB_iframe=true');
 return false;
});
 
 
 
});

jQuery(document).ready(function() {
 
jQuery('#upload_image_button3').click(function() {
											   window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#upload_image3').val(imgurl);
 tb_remove();
 
 
}
 
postid = jQuery('#post-id').val();
tb_show('', 'media-upload.php?post_id=' + postid + '&type=image&TB_iframe=true');
 return false;
});
 
 
 
});

jQuery(document).ready(function() {
 
jQuery('#upload_image_button4').click(function() {
											   window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#upload_image4').val(imgurl);
 tb_remove();
 
 
}
 
 postid = jQuery('#post-id').val();
 tb_show('', 'media-upload.php?post_id=' + postid + '&type=image&TB_iframe=true');
 return false;
});
 
 
 
});