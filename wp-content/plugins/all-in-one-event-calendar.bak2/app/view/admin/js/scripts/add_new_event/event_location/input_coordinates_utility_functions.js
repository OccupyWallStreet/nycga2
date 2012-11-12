/**
 * 
 */
define( 
		[
		 "jquery",
		 "ai1ec_config",
		 "libs/utils"
		 ],
		 function( $, ai1ec_config, AI1EC_UTILS ) {
			/**
			 *
			 * converts commas to dots as in some regions (Europe for example) floating point numbers are defined with a comma instead of a dot
			 *
			 */
			var ai1ec_convert_commas_to_dots_for_coordinates = function() {
				if ( $( '#ai1ec_input_coordinates:checked' ).length > 0 ) {
					$( '#ai1ec_table_coordinates input.coordinates' ).each( function() {
						this.value = AI1EC_UTILS.convert_comma_to_dot( this.value );
					} );
				}
			};
			/**
			 * Shows the error message after the field
			 *
			 * @param Object the dom element after which we put the error
			 *
			 * @param the error message
			 *
			 */
			var ai1ec_show_error_message_after_element = function( el, error_message ) {
				// Create the element to append in case of error
				var error = $( '<div />',
						{
							"text" : error_message,
							"class" : "ai1ec-error"
						}
				);
				// Insert error message
				$( el ).after( error );
			};
			/**
			 * INTERNAL FUNCTION (not exported)
			 * prevent default actions and stop immediate propagation if the publish button was clicked and
			 * gives focus to the passed element 
			 *
			 * @param Object the event object
			 *
			 * @param Object the element to focus
			 *
			 */
			var ai1ec_prevent_actions_and_focus_on_errors = function( e, el ) {
				// If the validation was triggered  by clicking publish
				if ( e.target.id === 'publish' || e.target.id === 'ai1ec_bottom_publish' ) {
					// Prevent other events from firing
					e.stopImmediatePropagation();
					// Prevent the submit
					e.preventDefault();
					// Just in case, hide the ajax spinner and remove the disabled status
					$( e.target ).removeClass( 'button-primary-disabled' );
					$( e.target ).siblings( '#ajax-loading' ).css( 'visibility', 'hidden' );
					
				}
				// Focus on the first field that has an error
				$( el ).focus();
			};
			/**
			 * Check if either the coordinates or the address are set
			 * 
			 * @returns boolean true if at least one is set between the address and both coordinates
			 */
			var check_if_address_or_coordinates_are_set = function() {
				var address_set = AI1EC_UTILS.field_has_value( 'ai1ec_address' );
				var lat_long_set = true;
				$( '.coordinates' ).each( function() {
					var is_set = AI1EC_UTILS.field_has_value( this.id );
					if ( ! is_set ) {
						lat_long_set = false;
					}
				} );
				return address_set || lat_long_set;
			};
			/**
			 * check that both latitude and longitude are not empty when publishing an event if the "Input coordinates" check-box
			 * is checked
			 *
			 * @param Object the event object
			 *
			 * @returns boolean true if the check is ok, false otherwise
			 *
			 */
			var ai1ec_check_lat_long_fields_filled_when_publishing_event = function( e ) {
				var valid = true;
				// We will save the first non valid field in this variable so whe can focus
				var first_not_valid = false;
				if ( $( '#ai1ec_input_coordinates:checked' ).length > 0 ) {
					// Clean up old error messages
					$( 'div.ai1ec-error' ).remove();
					$( '#ai1ec_table_coordinates input.coordinates' ).each( function() {
						// Check if we are validating latitude or longitude
						var latitude = $( this ).hasClass( 'latitude' );
						// Get the correct error message
						var error_message = latitude ? ai1ec_config.error_message_not_entered_lat : ai1ec_config.error_message_not_entered_long;
						if ( this.value === '' ) {
							valid = false;
							if( first_not_valid === false ) {
								first_not_valid = this;
							}
							ai1ec_show_error_message_after_element( this, error_message );
						}
					});
				}
				if ( valid === false ) {
					ai1ec_prevent_actions_and_focus_on_errors( e, first_not_valid );
				}
				return valid;
			};
			/**
			 * checks if latitude and longitude fields are valid and a search can be performed
			 *
			 * @param Object the event object that is passed to the handler function
			 *
			 * @return boolean true if the values are valid and both fields have a value, false otherwise;
			 */
			var ai1ec_check_lat_long_ok_for_search = function( e ) {
				// If the coordinates checkbox is checked
				if ( $( '#ai1ec_input_coordinates:checked' ).length === 1 ) {
					// Clean up old error messages
					$( 'div.ai1ec-error' ).remove();
					var valid = true;
					// We will save the first non valid field in this variable so whe can focus
					var first_not_valid = false;
					// If a field is empty, we will return false so that the map is not updated.
					var at_least_one_field_empty = false;
					// Let's iterate over the coordinates.
					$( '#ai1ec_table_coordinates input.coordinates' ).each( function() {
						if ( this.value === '' ) {
							at_least_one_field_empty = true;
							return;
						}
						// Check if we are validating latitude or longitude
						var latitude = $( this ).hasClass( 'latitude' );
						// Get the correct error message
						var error_message = latitude ? ai1ec_config.error_message_not_valid_lat : ai1ec_config.error_message_not_valid_long;
						// Check if the coordinate is valid.
						if( ! AI1EC_UTILS.is_valid_coordinate( this.value, latitude ) ) {
							valid = false;
							// Save the elements so that we can focus later
							if ( first_not_valid === false ) {
								first_not_valid = this;
							}
							ai1ec_show_error_message_after_element( this, error_message );
						};
					});
					// Check if there are errors
					if ( valid === false ) {
						ai1ec_prevent_actions_and_focus_on_errors( e, first_not_valid );
					}
					if ( at_least_one_field_empty === true ) {
						valid = false;
					}
					return valid;
				}
			};
			return {
				ai1ec_convert_commas_to_dots_for_coordinates             : ai1ec_convert_commas_to_dots_for_coordinates,
				ai1ec_show_error_message_after_element                   : ai1ec_show_error_message_after_element,
				check_if_address_or_coordinates_are_set                  : check_if_address_or_coordinates_are_set,
				ai1ec_check_lat_long_fields_filled_when_publishing_event : ai1ec_check_lat_long_fields_filled_when_publishing_event,
				ai1ec_check_lat_long_ok_for_search                       : ai1ec_check_lat_long_ok_for_search
			};
} );