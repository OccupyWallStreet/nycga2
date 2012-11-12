define( 
		[
		 "jquery"
		 ],
		 function( $ ) {
// I merged two plugins into one because they had a lot of dependencies.
var calendricalDateFormats = {
		us  : { //US date format (eg. 12/1/2011)
			pattern : /([\d]{1,2})\/([\d]{1,2})\/([\d]{4}|[\d]{2})/,
			format  : 'm/d/y',
			order   : 'middleEndian',
			zeroPad : false },
		iso : { //ISO 8601 (eg. 2011-12-01)
			pattern : /([\d]{4}|[\d]{2})-([\d]{1,2})-([\d]{1,2})/,
			format  : 'y-m-d',
			order   : 'bigEndian',
			zeroPad : true },
		dot : { //Little endian with dots (eg. 1.12.2011)
			pattern : /([\d]{1,2}).([\d]{1,2}).([\d]{4}|[\d]{2})/,
			format  : 'd.m.y',
			order   : 'littleEndian',
			zeroPad : false },
		def : { //Default (eg. 1/12/2011)
			pattern : /([\d]{1,2})\/([\d]{1,2})\/([\d]{4}|[\d]{2})/,
			format  : 'd/m/y',
			order   : 'littleEndian',
			zeroPad : false }
	};

	var formatDate = function(date, format)
	{
		if( typeof calendricalDateFormats[format] === 'undefined' )
			format = 'def';
			
		var y = ( date.getUTCFullYear() ).toString();
		var m = ( date.getUTCMonth() + 1 ).toString();
		var d = ( date.getUTCDate() ).toString();
		if( calendricalDateFormats[format].zeroPad ) {
			if( m.length == 1 ) m = '0' + m;
			if( d.length == 1 ) d = '0' + d;
		}
		var dt = calendricalDateFormats[format].format;
		dt = dt.replace('d', d);
		dt = dt.replace('m', m);
		dt = dt.replace('y', y);
		return dt;
	};

	var formatTime = function(hour, minute, iso)
	{
		var printMinute = minute;
		if( minute < 10 ) printMinute = '0' + minute;

		if( iso ) {
			var printHour = hour
			if( printHour < 10 ) printHour = '0' + hour;
			return printHour + ':' + printMinute;
		} else {
			var printHour = hour % 12;
			if( printHour == 0 ) printHour = 12;
			var half = ( hour < 12 ) ? 'am' : 'pm';
			return printHour + ':' + printMinute + half;
		}
	};

	var parseDate = function(date, format)
	{
		if( typeof calendricalDateFormats[format] === 'undefined' )
			format = 'def';
		
		var matches = date.match(calendricalDateFormats[format].pattern);
		if( !matches || matches.length != 4 ) {
			// Return an "invalid date" date instance like the original parseDate
			return Date( 'invalid' );
		}
		
		switch( calendricalDateFormats[format].order ) {
			case 'bigEndian' :
				var d = matches[3];	var m = matches[2];	var y = matches[1];
				break;
			case 'littleEndian' :
				var d = matches[1];	var m = matches[2];	var y = matches[3];
				break;
			case 'middleEndian' :
				var d = matches[2];	var m = matches[1];	var y = matches[3];
				break;
			default : //Default to little endian
				var d = matches[1];	var m = matches[2];	var y = matches[3];
				break;
		}
			
		// Add century to a two digit year
		if( y.length == 2 ) {
			y = new Date().getUTCFullYear().toString().substr(0, 2) + y;
		}
	 	
		// This is how the original parseDate does it
		return new Date( m + '/' + d + '/' + y + ' GMT' );
	};

	var parseTime = function(text)
	{
		var match = match = /(\d+)\s*[:\-\.,]\s*(\d+)\s*(am|pm)?/i.exec(text);
		if( match && match.length >= 3 ) {
			var hour = Number(match[1]);
			var minute = Number(match[2]);
			if( hour == 12 && match[3] ) hour -= 12;
			if( match[3] && match[3].toLowerCase() == 'pm' ) hour += 12;
			return {
				hour:   hour,
				minute: minute
			};
		} else {
			return null;
		}
	};

    function getToday()
    {
        var date = new Date();
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }
    
    function areDatesEqual(date1, date2)
    {
			if( typeof date1 === 'string' )
				date1 = new Date( date1 );
				
			if( typeof date2 === 'string' )
				date2 = new Date(date2);
			
			if( date1.getUTCDate() === date2.getUTCDate() ) {
				if( date1.getUTCMonth() === date2.getUTCMonth() ) {
					if( date1.getUTCFullYear() === date2.getUTCFullYear() ) {
						return true;
					}
				}
			}
			return false;
    }
    
    function daysInMonth(year, month)
    {
        if (year instanceof Date) return daysInMonth(year.getUTCFullYear(), year.getUTCMonth());
        if (month == 1) {
            var leapYear = (year % 4 == 0) &&
                (!(year % 100 == 0) || (year % 400 == 0));
            return leapYear ? 29 : 28;
        } else if (month == 3 || month == 5 || month == 8 || month == 10) {
            return 30;
        } else {
            return 31;
        }
    }
    
		function dayAfter(date)
    {
				// + 1 day
        return new Date( date.getTime() + (1*24*60*60*1000) );
    }
    
    function dayBefore(date)
    {
				// - 1 day
        return new Date( date.getTime() - (1*24*60*60*1000) );
    }
    
    function monthAfter(year, month)
    {
        return (month == 11) ?
            new Date(year + 1, 0, 1) :
            new Date(year, month + 1, 1);
    }
    
    /**
     * Generates calendar header, with month name, << and >> controls, and
     * initials for days of the week.
     */
    function renderCalendarHeader(element, year, month, options)
    {
				var monthNames = options.monthNames.split(',');
        //Prepare thead element
        var thead = $('<thead />');
        var titleRow = $('<tr />').appendTo(thead);
        
        //Generate << (back a month) link
        $('<th />').addClass('monthCell').append(
          $('<a href="javascript:;">&laquo;</a>')
                  .addClass('prevMonth')
                  .mousedown(function(e) {
                      renderCalendarPage(element,
                          month == 0 ? (year - 1) : year,
                          month == 0 ? 11 : (month - 1), options
                      );
                      e.preventDefault();
                  })
        ).appendTo(titleRow);
        
        //Generate month title
        $('<th />').addClass('monthCell').attr('colSpan', 5).append(
            $('<a href="javascript:;">' + monthNames[month] + ' ' +
                year + '</a>').addClass('monthName')
        ).appendTo(titleRow);
        
        //Generate >> (forward a month) link
        $('<th />').addClass('monthCell').append(
            $('<a href="javascript:;">&raquo;</a>')
                .addClass('nextMonth')
                .mousedown(function() {
                    renderCalendarPage(element,
                        month == 11 ? (year + 1) : year,
                        month == 11 ? 0 : (month + 1), options
                    );
                })
        ).appendTo(titleRow);
        
        // Generate weekday initials row. Adjust for week start day 
				var names = options.dayNames.split(','); 
				var startDay = parseInt(options.weekStartDay); 
				var adjustedNames = []; 
				for( var i = 0, len = names.length; i < len; i++ ) { 
					adjustedNames[i] = names[(i + startDay) % len]; 
				}
        var dayNames = $('<tr />').appendTo(thead);
        $.each( adjustedNames, function( k, v ) {
            $('<td />').addClass('dayName').append(v).appendTo(dayNames);
        });
        
        return thead;
    }
    
    function renderCalendarPage(element, year, month, options)
    {
        options = options || {};
        
				var startDay = parseInt(options.weekStartDay);
        var today = options.today ? options.today : getToday();
        // Normalize
				today.setHours(0);
				today.setMinutes(0);
				
        var date = new Date(year, month, 1);
				var endDate = monthAfter(year, month);
        
        //Adjust dates for current timezone. This is a workaround to get
				//date comparison to work properly.
				var tzOffset = Math.abs(today.getTimezoneOffset());
				if (tzOffset != 0) {
					today.setHours(today.getHours() + tzOffset / 60);
					today.setMinutes(today.getMinutes() + tzOffset % 60);
					date.setHours(date.getHours() + tzOffset / 60);
					date.setMinutes(date.getMinutes() + tzOffset % 60);
					endDate.setHours(endDate.getHours() + tzOffset / 60);
					endDate.setMinutes(endDate.getMinutes() + tzOffset % 60);
				}
				
				//Wind end date forward to last day of week
				var ff = endDate.getUTCDay() - startDay;
				if (ff < 0) {
					ff = Math.abs(ff) - 1;
				} else {
					ff = 6 - ff;
				}
        for (var i = 0; i < ff; i++) endDate = dayAfter(endDate);
        
        var table = $('<table />');
        renderCalendarHeader(element, year, month, options).appendTo(table);
        
        var tbody = $('<tbody />').appendTo(table);
        var row = $('<tr />');

				//Rewind date to first day of week
				var rewind = date.getUTCDay() - startDay;
				if (rewind < 0) rewind = 7 + rewind;
        for (var i = 0; i < rewind; i++) date = dayBefore(date);
        
        while (date <= endDate) {
            var td = $('<td />')
                .addClass('day')
                .append(
                    $('<a href="javascript:;">' +
                        date.getUTCDate() + '</a>'
                    ).click((function() {
                        var thisDate = date;
                        
                        return function() {
                            if (options && options.selectDate) {
                                options.selectDate(thisDate);
                            }
                        }
                    }()))
                )
                .appendTo(row);
            
            var isToday     = areDatesEqual(date, today);
            var isSelected  = options.selected &&
                                areDatesEqual(options.selected, date);
            
            if (isToday)                    td.addClass('today');
            if (isSelected)                 td.addClass('selected');
            if (isToday && isSelected)      td.addClass('today_selected');
            if (date.getUTCMonth() != month)   td.addClass('nonMonth');
            
           	var dow = date.getUTCDay();
						if (((dow + 1) % 7) == startDay) {
                tbody.append(row);
                row = $('<tr />');
            }
            date = dayAfter(date);
        }
        if (row.children().length) {
            tbody.append(row);
        } else {
            row.remove();
        }
        
        element.empty().append(table);
    }
    
    function renderTimeSelect(element, options)
    {
        var selection = options.selection && parseTime(options.selection);
        if (selection) {
            selection.minute = Math.floor(selection.minute / 15.0) * 15;
        }
        var startTime = options.startTime &&
            (options.startTime.hour * 60 + options.startTime.minute);
        
        var scrollTo;   //Element to scroll the dropdown box to when shown
        var ul = $('<ul />');
        for (var hour = 0; hour < 24; hour++) {
            for (var minute = 0; minute < 60; minute += 15) {
                if (startTime && startTime > (hour * 60 + minute)) continue;
                
                (function() {
                    var timeText = formatTime(hour, minute, options.isoTime);
                    var fullText = timeText;
                    if (startTime != null) {
                        var duration = (hour * 60 + minute) - startTime;
                        if (duration < 60) {
                            fullText += ' (' + duration + ' min)';
                        } else if (duration == 60) {
                            fullText += ' (1 hr)';
                        } else {
                            fullText += ' (' + Math.floor( duration / 60.0 ) + ' hr ' + ( duration % 60 ) + ' min)';
                        }
                    }
                    var li = $('<li />').append(
                        $('<a href="javascript:;">' + fullText + '</a>')
                        .click(function() {
                            if (options && options.selectTime) {
                                options.selectTime(timeText);
                            }
                        }).mousemove(function() {
                            $('li.selected', ul).removeClass('selected');
                        })
                    ).appendTo(ul);
                    
                    //Set to scroll to the default hour, unless already set
                    if (!scrollTo && hour == options.defaultHour) {
                        scrollTo = li;
                    }
                    
                    if (selection &&
                        selection.hour == hour &&
                        selection.minute == minute)
                    {
                        //Highlight selected item
                        li.addClass('selected');
                        
                        //Set to scroll to the selected hour
                        //
                        //This is set even if scrollTo is already set, since
                        //scrolling to selected hour is more important than
                        //scrolling to default hour
                        scrollTo = li;
                    }
                })();
            }
        }
        if (scrollTo) {
            //Set timeout of zero so code runs immediately after any calling
            //functions are finished (this is needed, since box hasn't been
            //added to the DOM yet)
            setTimeout(function() {
                //Scroll the dropdown box so that scrollTo item is in
                //the middle
                element[0].scrollTop =
                    scrollTo[0].offsetTop - scrollTo.height() * 2;
            }, 0);
        }
        element.empty().append(ul);
    }
    
    $.fn.calendricalDate = function(options)
    {
        options = options || {};
        options.padding = options.padding || 4;
				options.monthNames = options.monthNames ||
														 'January,February,March,April,May,June,July,August,September,October,November,December';
				options.dayNames = options.dayNames || 'S,M,T,W,T,F,S';
				options.weekStartDay = options.weekStartDay || 0;
        
        return this.each(function() {
            var element = $(this);
            var div;
            var within = false;
            
            element.bind('focus', function() {
                if (div) return;
                var offset = element.position();
                var padding = element.css('padding-left');
                div = $('<div />')
                    .addClass('calendricalDatePopup')
                    .mouseenter(function() { within = true; })
                    .mouseleave(function() { within = false; })
                    .mousedown(function(e) {
                        e.preventDefault();
                    })
                    .css({
                        position: 'absolute',
                        left: offset.left,
                        top: offset.top + element.height() +
                            options.padding * 2
                    });
                element.after(div); 
                
                var selected = parseDate(element.val(), options.dateFormat);
                if (!selected.getUTCFullYear()) selected = options.today ? options.today : getToday();
                
                renderCalendarPage(
                    div,
                    selected.getUTCFullYear(),
                    selected.getUTCMonth(), {
												today: options.today,
                        selected: selected,
												monthNames: options.monthNames,
												dayNames: options.dayNames,
												weekStartDay: options.weekStartDay,
                        selectDate: function(date) {
                            within = false;
                            element.val(formatDate(date, options.dateFormat));
                            div.remove();
                            div = null;
                            if (options.endDate) {
                                var endDate = parseDate(
                                    options.endDate.val(), options.dateFormat
                                );
                                if (endDate >= selected) {
                                    options.endDate.val(formatDate(
                                        new Date(
                                            date.getTime() +
                                            endDate.getTime() -
                                            selected.getTime()
                                        ),
                                        options.dateFormat
                                    ));
                                }
                            }
                        }
                    }
                );
            }).blur(function() {
                if (within){
                    if (div) element.focus();
                    return;
                }
                if (!div) return;
                div.remove();
                div = null;
            });
        });
    };
    
    $.fn.calendricalDateRange = function(options)
    {
        if (this.length >= 2) {
            $(this[0]).calendricalDate($.extend({
                endDate:   $(this[1])
            }, options));
            $(this[1]).calendricalDate(options);
        }
        return this;
    };

		$.fn.calendricalDateRangeSingle = function(options)
    {
        if (this.length == 1) {
            $(this).calendricalDate(options);
        }
        return this;
    };
    
    $.fn.calendricalTime = function(options)
    {
        options = options || {};
        options.padding = options.padding || 4;
        
        return this.each(function() {
            var element = $(this);
            var div;
            var within = false;
            
            element.bind('focus click', function() {
                if (div) return;

                var useStartTime = options.startTime;
                if (useStartTime) {
                    if (options.startDate && options.endDate &&
                        !areDatesEqual(parseDate(options.startDate.val()),
                            parseDate(options.endDate.val())))
                        useStartTime = false;
                }

                var offset = element.position();
                div = $('<div />')
                    .addClass('calendricalTimePopup')
                    .mouseenter(function() { within = true; })
                    .mouseleave(function() { within = false; })
                    .mousedown(function(e) {
                        e.preventDefault();
                    })
                    .css({
                        position: 'absolute',
                        left: offset.left,
                        top: offset.top + element.height() +
                            options.padding * 2
                    });
                if (useStartTime) {
                    div.addClass('calendricalEndTimePopup');
                }

                element.after(div); 
                
                var opts = {
                    selection: element.val(),
                    selectTime: function(time) {
                        within = false;
                        element.val(time);
                        div.remove();
                        div = null;
                    },
                    isoTime: options.isoTime || false,
                    defaultHour: (options.defaultHour != null) ?
                                    options.defaultHour : 8
                };
                
                if (useStartTime) {
                    opts.startTime = parseTime(options.startTime.val());
                }
                
                renderTimeSelect(div, opts);
            }).blur(function() {
                if (within){
                    if (div) element.focus();
                    return;
                }
                if (!div) return;
                div.remove();
                div = null;
            });
        });
    },
    
    $.fn.calendricalTimeRange = function(options)
    {
        if (this.length >= 2) {
            $(this[0]).calendricalTime(options);
            $(this[1]).calendricalTime($.extend({
                startTime: $(this[0])
            }, options));
        }
        return this;
    };

    $.fn.calendricalDateTimeRange = function(options)
    {
        if (this.length >= 4) {
            $(this[0]).calendricalDate($.extend({
                endDate:   $(this[2])
            }, options));
            $(this[1]).calendricalTime(options);
            $(this[2]).calendricalDate(options);
            $(this[3]).calendricalTime($.extend({
                startTime: $(this[1]),
                startDate: $(this[0]),
                endDate:   $(this[2])
            }, options));
        }
        return this;
    };
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
    return {
    	formatDate : formatDate,
    	parseDate  : parseDate
    };
} );