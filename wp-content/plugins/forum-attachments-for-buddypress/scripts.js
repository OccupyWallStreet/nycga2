jQuery(document).ready( function() {
	var j = jQuery;
	
	/* Enctype for topic.php and edit.php */
	var a = j("div#item-body form").attr('enctype');
	if ( a != 'multipart/form-data' ) {
		j("div#item-body form").attr('enctype', 'multipart/form-data');
	}
	
	/* Enctype for /forums/ */
	var c = j("div#new-topic-post form").attr('enctype');
	if ( c != 'multipart/form-data' ) {
		j("div#new-topic-post form").attr('enctype', 'multipart/form-data');
	}

	j("div#bp-forum-attachments-allowed").slideUp("fast");
	
	j("#bp-forum-attachments-allowed-toggle").click(function() {
		var b = j("#bp-forum-attachments-allowed");
		
		if ( j(b).css('display') == 'none' ) {
			j(b).slideDown("fast", function() {
				j("#bp-forum-attachments-allowed-toggle").html("Allowed file types (-)");
			});
		} else {
			j(b).slideUp("fast", function() {
				j("#bp-forum-attachments-allowed-toggle").html("Allowed file types (+)");
			});
		}
	});
	


});
		