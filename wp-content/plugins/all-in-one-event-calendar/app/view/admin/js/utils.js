// Generate the namespace.
if( typeof AI1EC_UTILS === 'undefined' ) {
	AI1EC_UTILS = function()
	{
			// We just return an object. This is useful if we ever need to define some private variables.
			return {
				/**
				 * check if a number is float
				 * 
				 * @param the value to check
				 * 
				 * @return boolean true if the value is float, false if it's not
				 */
				is_float: function( n ) {
					return ! isNaN( parseFloat( n ) );
				},
				/**
				 * check if the value is a valid coordinate
				 * 
				 * @param mixed the value to check
				 * 
				 * @param boolean true if we are validating latitude
				 * 
				 * @return boolean true if the value is a valid coordinate
				 */
				is_valid_coordinate: function( n, is_latitude ) {
					// Longitude is valid between +180 and -180 while Latitude is valid between +90 an -90
					var max_value = is_latitude ? 90 : 180;
					return this.is_float( n ) && Math.abs( n ) < max_value;
				},
				/**
				 * Converts all the commas to dots so that the value can be used as a float
				 */
				convert_comma_to_dot: function( value ) {
					return value.replace( ',', '.' );
				}
			}
	}();
}
