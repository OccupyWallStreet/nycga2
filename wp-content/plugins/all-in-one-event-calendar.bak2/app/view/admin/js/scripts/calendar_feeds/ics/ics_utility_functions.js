define( [
         "jquery"
         ],
         function( $ ) {
	// Remove a feed from the DOM
	var remove_feed_from_dom = function( $el ) {
		// table row to delete
		var $feed_row = $el.closest( '.ai1ec-feed-container' );
		$feed_row.remove();
	};
	// Restore normal DOM function after a non succesful delete
	var restore_normal_state_after_unsuccesful_delete = function( $el ) {
		// Remove the disabled attribute from the delete button
		$el.siblings( '.ai1ec_delete_ics' ).removeAttr( 'disabled' );
		// Hide the spinner
		$el.siblings( '.ajax-loading' ).css( 'visibility', 'hidden' );
	};
	return {
		"remove_feed_from_dom"                          : remove_feed_from_dom,
		"restore_normal_state_after_unsuccesful_delete" : restore_normal_state_after_unsuccesful_delete
	};
} );