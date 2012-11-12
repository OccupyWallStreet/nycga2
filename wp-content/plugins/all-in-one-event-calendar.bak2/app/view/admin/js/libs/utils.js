define( 
		[
		 "jquery"
		 ],
		 function( $ ) {
	var AI1EC_UTILS = function()
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
				"is_float": function( n ) {
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
				"is_valid_coordinate": function( n, is_latitude ) {
					// Longitude is valid between +180 and -180 while Latitude is valid between +90 an -90
					var max_value = is_latitude ? 90 : 180;
					return this.is_float( n ) && Math.abs( n ) < max_value;
				},
				/**
				 * Converts all the commas to dots so that the value can be used as a float
				 */
				"convert_comma_to_dot": function( value ) {
					return value.replace( ',', '.' );
				},
				/**
				 * Check if a field has a value.
				 * 
				 * @param string id, the id of the element to check
				 * 
				 * @return boolean Whether the fields has a value or not
				 */
				"field_has_value": function( id ) {
					var selector = '#' + id;
					var $field = $( selector );
					var has_value = false;
					// Check if the field was found. If it's not found we treat it as having no value.
					if( $field.length === 1 ) {
						has_value = $.trim( $field.val() ) !== '';
					}
					return has_value;
				},
				/**
				 * Create a twitter bootstrap aler
				 * 
				 * @param text the text of the message
				 * 
				 * @param type the type of the message
				 * 
				 * @return the alert, ready to be inserted in the DOM
				 * 
				 */
				"make_alert": function( text, type ) {
					var alert_class = '';
					switch (type) {
						case 'error'  : alert_class = 'alert alert-error';
							break;
						case 'success': alert_class = 'alert alert-success';
							break;
						default: alert_class = 'alert';
							break;
					}
					// Create the alert
					$alert = $( '<div />', { 
						"class" : alert_class,
						"html"  : text
					} );
					// Create the close button
					$close = $( '<a />', {
						"class"        : "close",
						"data-dismiss" : "alert",
						"href"         : "#",
						"text"         : "x"
					} );
					// Prepend the close button to the alert.
					$alert.prepend( $close );
					return $alert;
				},
				/**
				 * Define the ajax url. If undefined we hardcode a value. This is needed for testing purpose only because in the testing environment the variable ajaxurl is undefined.
				 */
				"get_ajax_url": function() {
					if( typeof window.ajaxurl === "undefined" ) {
						return "http://localhost/wordpress/wp-admin/admin-ajax.php";
					} else {
						return window.ajaxurl;
					}
				},
				/**
				 * isUrl checks to see if the passed parameter is a valid url
				 * and returns true on access and false on failure
				 *
				 * @param String s String to validate
				 *
				 * @return boolean True if the string is a valid url, false otherwise
				 */
				 "isUrl" : function( s ) {
					var regexp = /(http|https|webcal):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
					return regexp.test(s);
				}
				
			};
	}();
	return AI1EC_UTILS;
} );
