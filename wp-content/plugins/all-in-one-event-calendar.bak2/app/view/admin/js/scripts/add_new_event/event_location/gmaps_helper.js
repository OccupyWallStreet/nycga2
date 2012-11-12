define( 
		[
		 'jquery',
		 'domReady',
		 'ai1ec_config',
		 'scripts/add_new_event/event_location/input_coordinates_utility_functions',
		 'libs/jquery.autocomplete_geomod',
		 'libs/geo_autocomplete'
		 ],
		function( $, domReady, ai1ec_config, input_utility_functions ) {
	// Local Variables (killing those would be even better)
	var ai1ec_geocoder, 
	    ai1ec_default_location, 
	    ai1ec_myOptions, 
	    ai1ec_map, 
	    ai1ec_marker,
	    ai1ec_position;
	
	var gmap_event_listener = function( e ) {
		$( 'input.longitude' ).val( e.latLng.lng() );
		$( 'input.latitude' ).val( e.latLng.lat() );
		// If the checkbox to input coordinates is not checked, trigger the click event on it.
		if( $( '#ai1ec_input_coordinates:checked' ).length === 0 ) {
			$( '#ai1ec_input_coordinates' ).trigger( 'click' );
		}
	};
	var set_position_with_geolocator_if_available = function() {
		// Check if browser supports W3C Geolocation API. Use !! to have a boolean that reflect the truthiness of the original value.
		if ( !! navigator.geolocation ) {
			// Ask the user for his position. If the User denies it or if anything else goes wrong, we just fail silently and keep using our default.
			navigator.geolocation.getCurrentPosition( function( position ) {
				// The callback takes some time bofore it's called, we need to be sure to set the starting position only when no previous position was set.
				// So we check if the coordinates or the address have been set.
				var address_or_coordinates_set = input_utility_functions.check_if_address_or_coordinates_are_set();
				// If they have not been set, we use geolocation data.
				if ( address_or_coordinates_set === false ) {
					var lat = position.coords.latitude;
					var long = position.coords.longitude;
					// Update default location.
					ai1ec_default_location = new google.maps.LatLng( lat, long );
					// Set the marker position.
					ai1ec_marker.setPosition( ai1ec_default_location );
					// Center the Map and adjust the zoom level.
					ai1ec_map.setCenter( ai1ec_default_location );
					ai1ec_map.setZoom( 15 );
					ai1ec_position = position;
				}
			} );
		}
	};
	var set_autocomplete_if_needed = function() {
		if( ! ai1ec_config.disable_autocompletion ) {
			// This is the only way to stop the autocomplete from firing when the
			// coordinates checkbox is checked. The new jQuery UI autocomplete
			// supports the method .autocomplete( "disable" ) but not this version.
			$( '#ai1ec_address' )
				.bind( "keypress keyup keydown change", function( e ) {
					if( $( '#ai1ec_input_coordinates:checked' ).length ) {
						e.stopImmediatePropagation();
					}
				})
				// Initialize geo_autocomplete plugin
				.geo_autocomplete(
					new google.maps.Geocoder,
					{
						selectFirst: false,
						minChars: 3,
						cacheLength: 50,
						width: 300,
						scroll: true,
						scrollHeight: 330,
						region: ai1ec_config.region
					}
				)
				.result(
					function( _event, _data ) {
						if( _data ) {
							ai1ec_update_address( _data );
						}
					}
				)
				// Each time user changes address field, reformat field and update map.
				.change(
					function() {
						// Position map based on provided address value
						if( $( this ).val().length > 0 ) {
							var address = $( this ).val();

							ai1ec_geocoder.geocode(
								{
									'address': address,
									'region': ai1ec_config.region
								},
								function( results, status ) {
									if( status == google.maps.GeocoderStatus.OK ) {
										ai1ec_update_address( results[0] );
									}
								}
							);
						}
					}
				);
		}
	};
	var init_gmaps = function() {
		/**
		 * Google map setup
		 */
		// If the user is updating an event, initialize the map to the event
		// location, otherwise if the user is creating a new event initialize
		// the map to the whole world
		ai1ec_geocoder = new google.maps.Geocoder();
		//world = map.setCenter(new GLatLng(9.965, -83.327), 1);
		//africa = map.setCenter(new GLatLng(-3, 27), 3);
		//europe = map.setCenter(new GLatLng(47, 19), 3);
		//asia = map.setCenter(new GLatLng(32, 130), 3);
		//south pacific = map.setCenter(new GLatLng(-24, 134), 3);
		//north america = map.setCenter(new GLatLng(50, -114), 3);
		//latin america = map.setCenter(new GLatLng(-20, -70), 3);
		ai1ec_default_location = new google.maps.LatLng( 9.965, -83.327 );
		ai1ec_myOptions = {
			zoom: 0,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: ai1ec_default_location
		};
		domReady( function() {
			// This is mainly for testing purpose but it makes sense in any case, start the work only if there is a container
			if( $( '#ai1ec_map_canvas' ).length > 0 ) {
				// initialize map
				ai1ec_map = new google.maps.Map( $( '#ai1ec_map_canvas' ).get(0), ai1ec_myOptions );
				// Initialize Marker
				ai1ec_marker = new google.maps.Marker({
					map: ai1ec_map,
					draggable: true
				});
				// When the marker is dropped, update the latitude and longitude fields.
				google.maps.event.addListener( ai1ec_marker, 'dragend', gmap_event_listener );
				ai1ec_marker.setPosition( ai1ec_default_location );
				// If the browser supports geolocation, use it
				set_position_with_geolocator_if_available();
				// Start the autocompleter if the user decided to use it
				set_autocomplete_if_needed();
				// Set the map location and show / hide the coordinates
				hide_coordinates_on_load_if_checbox_not_checked();
			}
		} );

	};
	/**
	 * Given a location, update the address field with a reformatted version,
	 * update hidden location fields with address data, and center map on
	 * new location.
	 *
	 * @param object result  single result of a Google geocode() call
	 */
	var ai1ec_update_address = function( result ) {
		ai1ec_map.setCenter( result.geometry.location );
		ai1ec_map.setZoom( 15 );
		ai1ec_marker.setPosition( result.geometry.location );
		$( '#ai1ec_address' ).val( result.formatted_address );

		var street_number = '',
					street_name = '',
					city = '',
					postal_code = 0,
					country = 0,
					province = '';

		for( var i = 0; i < result.address_components.length; i++ ) {
			switch( result.address_components[i].types[0] ) {
				case 'street_number':
					street_number = result.address_components[i].long_name;
					break;
				case 'route':
					street_name = result.address_components[i].long_name;
					break;
				case 'locality':
					city = result.address_components[i].long_name;
					break;
				case 'administrative_area_level_1':
					province = result.address_components[i].long_name;
					break;
				case 'postal_code':
					postal_code = result.address_components[i].long_name;
					break;
				case 'country':
					country = result.address_components[i].long_name;
					break;
			}
		}
		// Combine street number with street address
		var address = street_number.length > 0 ? street_number + ' ' : '';
		address += street_name.length > 0 ? street_name : '';
		// Clean up postal code if necessary
		postal_code = postal_code != 0 ? postal_code : '';

		$( '#ai1ec_city' ).val( city );
		$( '#ai1ec_province' ).val( province );
		$( '#ai1ec_postal_code' ).val( postal_code );
		$( '#ai1ec_country' ).val( country );
	};
	/**
	 * Updates the map taking the coordinates from the input fields
	 */
	var ai1ec_update_map_from_coordinates = function() {
		var lat = parseFloat( $( 'input.latitude' ).val() );
		var long = parseFloat( $( 'input.longitude' ).val() );
		var LatLong = new google.maps.LatLng( lat, long );

		ai1ec_map.setCenter( LatLong );
		ai1ec_map.setZoom( 15 );
		ai1ec_marker.setPosition( LatLong );
	};
	var hide_coordinates_on_load_if_checbox_not_checked = function() {
		// If the coordinates checkbox is not checked
		if( $( '#ai1ec_input_coordinates:checked' ).length === 0 ) {
			// Hide the table (i hide things in js for progressive enhancement reasons)
			$( '#ai1ec_table_coordinates' ).css( { visibility : 'hidden' } );
			// Trigger the change event on the address to show the map
			$( '#ai1ec_address' ).change();
		} else {
			// If the checkbox is checked, show the map using the coordinates
			ai1ec_update_map_from_coordinates();
		}
	};
	// This allows another function to access the marker ( if the marker is set ). I mainly use this for testing.
	var get_marker = function() {
		return ai1ec_marker;
	}
	// This allows another function to access the position ( if the position is set ). I mainly use this for testing.
	var get_position = function() {
		return ai1ec_position;
	}
	return {
		init_gmaps                        : init_gmaps,
		ai1ec_update_map_from_coordinates : ai1ec_update_map_from_coordinates,
		get_marker                        : get_marker,
		get_position                      : get_position
	};
} );