define( 
		[
		 'jquery',
		 'domReady',
		 'scripts/event/gmaps_helper'
		 ],
		 function( $, domReady, gmaps_helper ) {
	// Perform all initialization functions required on the page.
	var init = function() {
		if( $( '#ai1ec-gmap-canvas' ).length > 0 ) {
			require( ['libs/gmaps' ], function( gMapsLoader ) {
				gMapsLoader( gmaps_helper.init_gmaps );
			} );
		}
	};
	var attach_event_handlers = function() {
		// handle showing the maps when clicking on the placeholder
		$( '.ai1ec-gmap-placeholder:first' ).click( gmaps_helper.handle_show_map_when_clicking_on_placeholder );
	};
	var start = function() {
		domReady( function() {
			// Initialize the page. 
			// We wait for the DOM to be loaded so we load gMaps only when needed
			init();
			attach_event_handlers();
		} );
	};
	return {
		start: start
	};
			
} );