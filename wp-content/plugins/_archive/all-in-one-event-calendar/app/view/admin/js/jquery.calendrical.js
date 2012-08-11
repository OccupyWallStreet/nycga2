calendricalDateFormats = {
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

function formatDate(date, format)
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
}

function formatTime(hour, minute, iso)
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
}

function parseDate(date, format)
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
}

function parseTime(text)
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
}

(function($) {    

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
})(jQuery);
