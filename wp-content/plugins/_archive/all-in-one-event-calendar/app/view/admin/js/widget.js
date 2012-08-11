jQuery( function( $ ) {
	// Show/hide the multiselect containers when user clicks on "limit by" widget options
	$( '.ai1ec-limit-by-cat, .ai1ec-limit-by-tag, .ai1ec-limit-by-event' ).live( 'click', function() {
		$( this ).parent().next( '.ai1ec-limit-by-options-container' ).toggle();
	} );
} );