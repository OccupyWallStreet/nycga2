define(
		[
		 "jquery",
		 "domReady"
		 ],
		 function( $, domReady ) {
	var remove_feeds_postbox_if_all_values_are_empty = function() {
		var remove = true;
		$( '#ai1ec-plugins-settings input:text' ).each( function() {
			if( this.value !== '' ) {
				remove = false;
			}
		} );
		if( remove === true ) {
			$( '#ai1ec-plugins-settings' ).remove();
		}
	};
	domReady( function() {
		remove_feeds_postbox_if_all_values_are_empty();
		// On clicking a .toggle-view
		$( window ).on( "click", '.ai1ec-admin-view-settings .toggle-view',  function () {
			// check to see if there are any siblings that are checked
			var is_one_box_checked = $( '.ai1ec-admin-view-settings .toggle-view:checked' ).length === 0;
			// check if this view is selected as the default via radio button
			var is_selected_default = $( this ).parents( 'tr' ).find( '.toggle-default-view:checked' ).length === 1;
			// if either is true, prevent :checked state change
			if ( is_one_box_checked === true || is_selected_default === true ) {
				return false;
			}
		});
		// When clicking a radio button to select a default view
		$( window ).on( "click", '.ai1ec-admin-view-settings .toggle-default-view', function () {
			// Automatically set the associated checkbox property to :checked
			$( this ).parents( 'tr' ).find( '.toggle-view' ).prop( 'checked', true );
		});
	} );
// end requirejs wrapper
} );
