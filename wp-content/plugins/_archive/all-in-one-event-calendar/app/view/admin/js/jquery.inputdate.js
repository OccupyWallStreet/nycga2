(function( $ )
{
	/**
	 * Private functions
	 */

	// Helper function - reset contents of current field to stored original
	// value and alert user.
	function reset_invalid( field )
	{
		field
			.addClass( 'error' )
			.fadeOut( 'normal', function() {
				field
					.val( field.data( 'timespan.stored' ) )
					.removeClass( 'error' )
					.fadeIn( 'fast' );
			});
	}

	// Stores the value of the HTML element in context to its "stored" jQuery data.
	function store_value() {
		$(this).data( 'timespan.stored', this.value );
 	}

	/**
	 * Value initialization
	 */
	function reset( start_date_input, start_time, twentyfour_hour, date_format, now )
	{
		// Restore original values of fields when the page was loaded
		start_time.val( start_time.data( 'timespan.initial_value' ) );

		// Fill out input field with default date/time based on this original
		// value.

		var start = parseInt( start_time.val() );
		// If start_time field has a valid integer, use it, else use current time
		// rounded to nearest quarter-hour.
		if( ! isNaN( parseInt( start ) ) ) {
			start = new Date( parseInt( start ) * 1000 );
		} else {
			start = new Date( now );
		}
		start_date_input.val( formatDate( start, date_format ) );

		// Trigger function (defined above) to internally store values of each
		// input field (used in calculations later).
		start_date_input.each( store_value );
	}

	/**
	 * Private constants
	 */

	var default_options = {
		start_date_input: 'date-input',
		start_time: 'time',
		twentyfour_hour: false,
		date_format: 'def',
		now: new Date()
	};

	/**
	 * Public methods
	 */

	var methods = {

		/**
		 * Initialize settings.
		 */
		init: function( options )
		{
			var o = $.extend( {}, default_options, options );

			// Shortcut jQuery objects
			var start_date_input = $(o.start_date_input);
			var start_time = $(o.start_time);

			var date_inputs = start_date_input;
			var all_inputs = start_date_input;

			/**
			 * Event handlers
			 */

			// Save original (presumably valid) value of every input field upon focus.
			all_inputs.bind( 'focus.timespan', store_value );
			date_inputs.calendricalDate( {
				today: new Date( o.now.getFullYear(), o.now.getMonth(), o.now.getDate() ),
				dateFormat: o.date_format, monthNames: o.month_names, dayNames: o.day_names,
				weekStartDay: o.week_start_day
			} );

			// Validate and update saved value of DATE fields upon blur.
			date_inputs
				.bind( 'blur.timespan', function() {
					// Validate contents of this field.
					var date = parseDate( this.value, o.date_format );
					if( isNaN( date ) ) {
						// This field is invalid.
						reset_invalid( $(this) );
					} else {
						// Value is valid, so store it for later use (below).
						$(this).data( 'timespan.stored', this.value );
						// Re-format contents of field correctly (in case parsable but not
						// perfect).
						$(this).val( formatDate( date, o.date_format ) );
					}
				});

			// When start date/time are modified, update end date/time by shifting the
			// appropriate amount.
			start_date_input.bind( 'focus.timespan', function() {
					// Calculate the time difference between start & end and save it.
					var start_date_val = parseDate( start_date_input.val(), o.date_format ).getTime() / 1000;
				} )
				.bind( 'blur.timespan', function() {
					var start_date_val = parseDate( start_date_input.data( 'timespan.stored' ), o.date_format );
					// Shift end date/time as appropriate.
				} );

			// Validation upon form submission
			start_date_input.closest( 'form' )
				.bind( 'submit.timespan', function() {
					// Update hidden field value with chosen date/time.

					// Convert Date object into UNIX timestamp for form submission
					var unix_start_time = parseDate( start_date_input.val(), o.date_format ).getTime() / 1000;
					// If parsed incorrectly, entire calculation is invalid.
					if( isNaN( unix_start_time ) ) {
						unix_start_time = '';
					}
					// Set start date value to valid unix time, or empty string, depending
					// on above validation.
					start_time.val( unix_start_time );
				} );

			// Store original form value
			start_time.data( 'timespan.initial_value', start_time.val() );

			// Initialize input fields
			reset( start_date_input,
					start_time,
					o.twentyfour_hour,
					o.date_format,
					o.now )

			return this;
		},

		/**
		 * Reset values to defaults.
		 */
		reset: function( options )
		{
			var o = $.extend( {}, default_options, options );

			reset( $(o.start_date_input),
					$(o.start_time),
					o.twentyfour_hour,
					o.date_format,
					o.now );

			return this;
		},

		/**
		 * Destroy registered event handlers.
		 */
		destroy: function( options )
	 	{
			options = $.extend( {}, default_options, options );

			$.each( options, function( option_name, value ) {
				$(value).unbind( '.timespan' );
			} );
			$(options.start_date_input).closest('form').unbind( '.timespan' );

			return this;
		}
	}

	/**
	 * Main jQuery plugin definition
	 */

	$.inputdate = function( arg )
	{
		// Method calling logic
		if( methods[arg] ) {
			return methods[arg].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if( typeof arg === 'object' || ! arg ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' + arg + ' does not exist on jQuery.timespan' );
		}
	};
})( jQuery );
