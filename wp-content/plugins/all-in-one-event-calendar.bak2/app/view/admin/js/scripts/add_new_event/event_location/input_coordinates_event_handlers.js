define( 
		[
		 "jquery",
		 'scripts/add_new_event/event_location/input_coordinates_utility_functions',
		 'scripts/add_new_event/event_location/gmaps_helper'
		 ],
function( $, input_utility_functions, gmaps_helper ) {
	// Toggle the visibility of google map on checkbox click
	var toggle_visibility_of_google_map_on_click = function( e ) {
		if( $( this ).is( ':checked' ) ) {
			// show the map
			$( '.ai1ec_box_map' )
				.addClass( 'ai1ec_box_map_visible')
				.hide()
				.slideDown( 'fast' );
		} else {
			// hide the map
			$( '.ai1ec_box_map' ).slideUp( 'fast' );
		}
	};
	// Hide / Show the coordinates table when clicking the checkbox
	var toggle_visibility_of_coordinate_fields_on_click = function( e ) {
		// If the checkbox is checked
		if( this.checked === true ) {
			$( '#ai1ec_table_coordinates' ).css( { visibility : 'visible' } );
		} else {
			// Hide the table
			$( '#ai1ec_table_coordinates' ).css( { visibility : 'hidden' } );
			// Erase the input fields
			$( '#ai1ec_table_coordinates input' ).val( '' );
			// Clean up error messages
			$( 'div.ai1ec-error' ).remove();
		}
	};
	//Validate the coordinates when clicking Publish / Update 
	var validate_coordinates_when_publishing = function( e ) {
		if ( input_utility_functions.ai1ec_check_lat_long_fields_filled_when_publishing_event( e ) === true ) {
			// Convert commas to dots
			input_utility_functions.ai1ec_convert_commas_to_dots_for_coordinates();
			// Check that fields are ok and there are no errors
			input_utility_functions.ai1ec_check_lat_long_ok_for_search( e );
		}
	};
	var update_map_from_coordinates_on_blur = function( e ) {
		// Convert commas to dots
		input_utility_functions.ai1ec_convert_commas_to_dots_for_coordinates();
		// Check if the coordinates are valid.
		var valid = input_utility_functions.ai1ec_check_lat_long_ok_for_search( e );
		// If they are valid, update the map.
		if( valid === true ) {
			gmaps_helper.ai1ec_update_map_from_coordinates();
		}
	};

	return {
		"toggle_visibility_of_google_map_on_click"        : toggle_visibility_of_google_map_on_click,
		"toggle_visibility_of_coordinate_fields_on_click" : toggle_visibility_of_coordinate_fields_on_click,
		"validate_coordinates_when_publishing"            : validate_coordinates_when_publishing,
		"update_map_from_coordinates_on_blur"             : update_map_from_coordinates_on_blur
	};
} );