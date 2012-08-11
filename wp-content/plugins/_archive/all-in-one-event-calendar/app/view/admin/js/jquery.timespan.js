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
	function reset( start_date_input, start_time_input, start_time,
			end_date_input, end_time_input, end_time, allday, twentyfour_hour,
			date_format, now )
	{
		// Restore original values of fields when the page was loaded
		start_time.val( start_time.data( 'timespan.initial_value' ) );
		end_time.val( end_time.data( 'timespan.initial_value' ) );
		allday.get(0).checked = allday.data( 'timespan.initial_value' );

		// Fill out input fields with default date/time based on these original
		// values.

		var start = parseInt( start_time.val() );
		// If start_time field has a valid integer, use it, else use current time
		// rounded to nearest quarter-hour.
		if( ! isNaN( parseInt( start ) ) ) {
			start = new Date( parseInt( start ) * 1000 );
			start_time_input.val( formatTime( start.getUTCHours(), start.getUTCMinutes(), twentyfour_hour ) );
		} else {
			start = new Date( now );
			// Round minutes to nearest quarter-hour.
			start_time_input.val(
				formatTime( start.getUTCHours(), start.getUTCMinutes() - start.getUTCMinutes() % 15, twentyfour_hour ) );
		}
		start_date_input.val( formatDate( start, date_format ) );

		var end = parseInt( end_time.val() );
		// If end_time field has a valid integer, use it, else use start time plus
		// one hour.
		if( ! isNaN( parseInt( end ) ) ) {
			end = new Date( parseInt( end ) * 1000 );
			end_time_input.val( formatTime( end.getUTCHours(), end.getUTCMinutes(), twentyfour_hour ) );
		} else {
			end = new Date( start.getTime() + 3600000 );
			// Round minutes to nearest quarter-hour.
			end_time_input.val(
				formatTime( end.getUTCHours(), end.getUTCMinutes() - end.getUTCMinutes() % 15, twentyfour_hour ) );
		}
		// If all-day is checked, end date one day *before* last day of the span,
		// provided we were given an iCalendar-spec all-day timespan.
		if( allday.get(0).checked )
			end.setUTCDate( end.getUTCDate() - 1 );
		end_date_input.val( formatDate( end, date_format ) );

		// Trigger function (defined above) to internally store values of each
		// input field (used in calculations later).
		start_date_input.each( store_value );
		start_time_input.each( store_value );
		end_date_input.each( store_value );
		end_time_input.each( store_value );

		// Set up visibility of controls and Calendrical activation based on
		// original "checked" status of "All day" box.
		allday.trigger( 'change.timespan' );
	}

	/**
	 * Private constants
	 */

	var default_options = {
		allday: '#allday',
		start_date_input: '#start-date-input',
		start_time_input: '#start-time-input',
		start_time: '#start-time',
		end_date_input: '#end-date-input',
		end_time_input: '#end-time-input',
		end_time: '#end-time',
		twentyfour_hour: false,
		date_format: 'def',
		now: new Date(),
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
			var allday = $(o.allday);
			var start_date_input = $(o.start_date_input);
			var start_time_input = $(o.start_time_input);
			var start_time = $(o.start_time);
			var end_date_input = $(o.end_date_input);
			var end_time_input = $(o.end_time_input);
			var end_time = $(o.end_time);

			var date_inputs = start_date_input.add( o.end_date_input );
			var time_inputs = start_time_input.add( o.end_time_input );
			var all_inputs = start_date_input.add( o.start_time_input )
				.add( o.end_date_input ).add( o.end_time_input );

			/**
			 * Event handlers
			 */

			// Save original (presumably valid) value of every input field upon focus.
			all_inputs.bind( 'focus.timespan', store_value );

			// When "All day" is toggled, show/hide time.
			var today = new Date( o.now.getFullYear(), o.now.getMonth(), o.now.getDate() );
			allday
				.bind( 'change.timespan', function() {
					if( this.checked ) {
						time_inputs.fadeOut();
						date_inputs.calendricalDateRange( {
							today: today, dateFormat: o.date_format, monthNames: o.month_names,
							dayNames: o.day_names, weekStartDay: o.week_start_day
						} );
					} else {
						time_inputs.fadeIn();
						all_inputs.calendricalDateTimeRange( { 
							today: today, dateFormat: o.date_format, isoTime: o.twentyfour_hour,
							monthNames: o.month_names, dayNames: o.day_names, weekStartDay: o.week_start_day
						} );
					}
				} )
				.get().checked = false;

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

			// Validate and update saved value of TIME fields upon blur.
			time_inputs
				.bind( 'blur.timespan', function() {
					// Validate contents of this field.
					var time = parseTime( this.value );
					if( ! time ) {
						// This field is invalid.
						reset_invalid( $(this) );
					} else {
						// Value is valid, so store it for later use (below).
						$(this).data( 'timespan.stored', this.value );
						// Re-format contents of field correctly (in case parsable but not
						// perfect).
						$(this).val( formatTime( time.hour, time.minute, o.twentyfour_hour ) );
					}
				});

			// Gets the time difference between start and end dates
			function get_startend_time_difference() {
				var start_date_val = parseDate( start_date_input.val(), o.date_format ).getTime() / 1000;
				var start_time_val = parseTime( start_time_input.val() );
				start_date_val += start_time_val.hour * 3600 + start_time_val.minute * 60;
				var end_date_val = parseDate( end_date_input.val(), o.date_format ).getTime() / 1000;
				var end_time_val = parseTime( end_time_input.val() );
				end_date_val += end_time_val.hour * 3600 + end_time_val.minute * 60;

				return end_date_val - start_date_val;
			}

			function shift_jqts_enddate() {
				var start_date_val = parseDate( start_date_input.data( 'timespan.stored' ), o.date_format );
				var start_time_val = parseTime( start_time_input.data( 'timespan.stored' ) );
				var end_time_val = start_date_val.getTime() / 1000
					+ start_time_val.hour * 3600 + start_time_val.minute * 60
					+ start_date_input.data( 'time_diff' );
				end_time_val = new Date( end_time_val * 1000 );
				end_date_input.val( formatDate( end_time_val, o.date_format ) );
				end_time_input.val( formatTime( end_time_val.getUTCHours(), end_time_val.getUTCMinutes(), o.twentyfour_hour ) );

				return true;
			}

			// When start date/time are modified, update end date/time by shifting the
			// appropriate amount.
			start_date_input.add( o.start_time_input )
				.bind( 'focus.timespan', function() {
					// Calculate the time difference between start & end and save it.
					start_date_input.data( 'time_diff', get_startend_time_difference() );
				} )
				.bind( 'blur.timespan', function() {
					// If End date is earlier than StartDate, reset it to 15 mins after startDate
					if ( start_date_input.data( 'time_diff' ) < 0 ) {
						start_date_input.data( 'time_diff', 15 * 60 );
					}
					// Shift end date/time as appropriate.
					var shift_jqts = shift_jqts_enddate();
				} );
			
			// When end date/time is modified, check if it is earlier than start date/time and shift it if needed
			end_date_input.add( o.start_time_input )
				.bind( 'blur.timespan', function() {
					// If End date is earlier than StartDate, reset it to 15 mins after startDate
					if ( get_startend_time_difference() < 0 ) {
						start_date_input.data( 'time_diff', 15 * 60 );
						// Shift end date/time as appropriate.
						var shift_jqts = shift_jqts_enddate();
					}
				} );

			// Validation upon form submission
			start_date_input.closest( 'form' )
				.bind( 'submit.timespan', function() {
					// Update hidden field values with chosen date/time.
					//
					// 1. Start date/time

					// Convert Date object into UNIX timestamp for form submission
					var unix_start_time = parseDate( start_date_input.val(), o.date_format ).getTime() / 1000;
					// If parsed correctly, proceed to add the time.
					if( ! isNaN( unix_start_time ) ) {
						// Add time quantity to date, unless "All day" is checked.
						if( ! allday.get(0).checked ) {
							var time = parseTime( start_time_input.val() );
							// If parsed correctly, proceed add its value.
							if( time ) {
								unix_start_time += time.hour * 3600 + time.minute * 60;
							} else {
								// Else entire calculation is invalid.
								unix_start_time = '';
							}
						}
					} else {
						// Else entire calculation is invalid.
						unix_start_time = '';
					}
					// Set start date value to valid unix time, or empty string, depending
					// on above validation.
					start_time.val( unix_start_time );

					// 2. End date/time

					// Convert Date object into UNIX timestamp for form submission
					var unix_end_time = parseDate( end_date_input.val(), o.date_format ).getTime() / 1000;
					// If parsed correctly, proceed to add the time.
					if( ! isNaN( unix_end_time ) ) {
						// If "All day" is checked, store an end date that is one day
						// *after* the start date (following iCalendar spec).
						if( allday.get(0).checked ) {
							unix_end_time += 24 * 60 * 60;
						// Else add time quantity to date.
						} else {
							var time = parseTime( end_time_input.val() );
							// If parsed correctly, proceed add its value.
							if( time ) {
								unix_end_time += time.hour * 3600 + time.minute * 60;
							} else {
								// Else entire calculation is invalid.
								unix_end_time = '';
							}
						}
					} else {
						// Else entire calculation is invalid.
						unix_end_time = '';
					}
					// Set end date value to valid unix time, or empty string, depending
					// on above validation.
					end_time.val( unix_end_time );
				} );

			// Store original form values
			start_time.data( 'timespan.initial_value', start_time.val() );
			end_time.data( 'timespan.initial_value', end_time.val() );
			allday.data( 'timespan.initial_value', allday.get(0).checked );

			// Initialize input fields
			reset( start_date_input,
					start_time_input,
					start_time,
					end_date_input,
					end_time_input,
					end_time,
					allday,
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
					$(o.start_time_input),
					$(o.start_time),
					$(o.end_date_input),
					$(o.end_time_input),
					$(o.end_time),
					$(o.allday),
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

	$.timespan = function( arg )
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
