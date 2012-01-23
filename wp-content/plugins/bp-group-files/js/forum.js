jQuery(document).ready(function($) {

	// Set multipart form encoding so we can upload documents
	$('form#forum-topic-form')
		.attr((this.encoding ? 'encoding' : 'enctype'), 'multipart/form-data')
	;

    // Hide the document upload form fields
	$('div#bp_group_documents_forum_upload').slideUp("fast");

    // Toggle the document upload form fields on click
	$('a#bp_group_documents_forum_upload_toggle').click(function() {
		var div = $('div#bp_group_documents_forum_upload');

		if ( div.css('display') == 'none' ) {
		div.slideDown("fast", function() {
			$('a#bp_group_documents_forum_upload_toggle').html("Upload document (-)");
			});
		} else {
		div.slideUp("fast", function() {
			$('a#bp_group_documents_forum_upload_toggle').html("Upload document (+)");
			});
		}
		return false;
	});

});
