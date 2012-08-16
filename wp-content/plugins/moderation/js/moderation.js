function moderation_submit() {
	jQuery('#moderation-report').load( moderation_ajaxurl, jQuery('form#moderation-report-form').serializeArray(), function() {
		jQuery('#moderation-report').append('<p>Press ESC or click anywhere outside this box to close it.</p>');
	} );
	return false;
}