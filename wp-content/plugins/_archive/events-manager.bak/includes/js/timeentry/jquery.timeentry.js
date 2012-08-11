/* http://keith-wood.name/timeEntry.html
   Time entry for jQuery v1.4.2.
   Written by Keith Wood (kbwood@virginbroadband.com.au) June 2007.
   Dual licensed under the GPL (http://dev.jquery.com/browser/trunk/jquery/GPL-LICENSE.txt) and 
   MIT (http://dev.jquery.com/browser/trunk/jquery/MIT-LICENSE.txt) licenses. 
   Please attribute the author if you use it. */

/* Turn an input field into an entry point for a time value.
   The time can be entered via directly typing the value,
   via the arrow keys, or via spinner buttons.
   It is configurable to show 12 or 24-hour time, to show or hide seconds,
   to enforce a minimum and/or maximum time, to change the spinner image,
   and to constrain the time to steps, e.g. only on the quarter hours.
   Attach it with $('input selector').timeEntry(); for default settings,
   or configure it with options like:
   $('input selector').timeEntry(
      {spinnerImage: 'timeEntry2.png', spinnerSize: [20, 20, 0]}); */

(function($) { // Hide scope, no $ conflict

/* TimeEntry manager.
   Use the singleton instance of this class, $.timeEntry, to interact with the time entry
   functionality. Settings for (groups of) fields are maintained in an instance object
   (TimeEntryInstance), allowing multiple different settings on the same page. */
function TimeEntry() {
	this._disabledInputs = []; // List of time entry inputs that have been disabled
	this.regional = []; // Available regional settings, indexed by language code
	this.regional[''] = { // Default regional settings
		show24Hours: false, // True to use 24 hour time, false for 12 hour (AM/PM)
		separator: ':', // The separator between time fields
		ampmPrefix: '', // The separator before the AM/PM text
		ampmNames: ['AM', 'PM'], // Names of morning/evening markers
		spinnerTexts: ['Now', 'Previous field', 'Next field', 'Increment', 'Decrement']
		// The popup texts for the spinner image areas
	};
	this._defaults = {
		appendText: '', // Display text following the input box, e.g. showing the format
		showSeconds: false, // True to show seconds as well, false for hours/minutes only
		timeSteps: [1, 1, 1], // Steps for each of hours/minutes/seconds when incrementing/decrementing
		initialField: 0, // The field to highlight initially, 0 = hours, 1 = minutes, ...
		useMouseWheel: true, // True to use mouse wheel for increment/decrement if possible,
			// false to never use it
		defaultTime: null, // The time to use if none has been set, leave at null for now
		minTime: null, // The earliest selectable time, or null for no limit
		maxTime: null, // The latest selectable time, or null for no limit
		spinnerImage: 'timeEntry.png', // The URL of the images to use for the time spinner
			// Six images packed horizontally for normal and then each button pressed
		spinnerSize: [20, 20, 8], // The width and height of the spinner image,
			// and size of centre button for current time
		spinnerIncDecOnly: false, // True for increment/decrement buttons only, false for all
		spinnerRepeat: [500, 250], // Initial and subsequent waits in milliseconds
			// for repeats on the spinner buttons
		beforeShow: null, // Function that takes an input field and
			// returns a set of custom settings for the time entry
		beforeSetTime: null // Function that runs before updating the time,
			// takes the old and new times, and minimum and maximum times as parameters,
			// and returns an adjusted time if necessary
	};
	$.extend(this._defaults, this.regional['']);
}

var PROP_NAME = 'timeEntry';

$.extend(TimeEntry.prototype, {
	/* Class name added to elements to indicate already configured with time entry. */
	markerClassName: 'hasTimeEntry',

	/* Override the default settings for all instances of the time entry.
	   @param  options  (object) the new settings to use as defaults (anonymous object) */
	setDefaults: function(options) {
		extendRemove(this._defaults, options || {});
	},

	/* Attach the time entry handler to an input field.
	   @param  target   (element) the field to attach to
	   @param  options  (object) custom settings for this instance */
	_connectTimeEntry: function(target, options) {
		var input = $(target);
		if (input.hasClass(this.markerClassName)) {
			return;
		}
		var inst = {};
		inst.options = $.extend({}, options);
		inst._selectedHour = 0; // The currently selected hour
		inst._selectedMinute = 0; // The currently selected minute
		inst._selectedSecond = 0; // The currently selected second
		inst._field = 0; // The selected subfield
		inst.input = $(target); // The attached input field
		$.data(target, PROP_NAME, inst);
		var spinnerImage = this._get(inst, 'spinnerImage');
		var spinnerText = this._get(inst, 'spinnerText');
		var spinnerSize = this._get(inst, 'spinnerSize');
		var appendText = this._get(inst, 'appendText');
		var spinner = (!spinnerImage ? null : 
			$('<span class="timeEntry_control" style="display: inline-block; ' +
			'background: url(\'' + spinnerImage + '\') 0 0 no-repeat; ' +
			'width: ' + spinnerSize[0] + 'px; height: ' + spinnerSize[1] + 'px;' +
			($.browser.mozilla && $.browser.version < '1.9' ? // FF 2- (Win)
			' padding-left: ' + spinnerSize[0] + 'px; padding-bottom: ' +
			(spinnerSize[1] - 18) + 'px;' : '') + '"></span>'));
		input.wrap('<span class="timeEntry_wrap"></span>').
			after(appendText ? '<span class="timeEntry_append">' + appendText + '</span>' : '').
			after(spinner || '');
		input.addClass(this.markerClassName).bind('focus.timeEntry', this._doFocus).
			bind('blur.timeEntry', this._doBlur).bind('click.timeEntry', this._doClick).
			bind('keydown.timeEntry', this._doKeyDown).bind('keypress.timeEntry', this._doKeyPress);
		// check pastes
		if ($.browser.mozilla) {
			input.bind('input.timeEntry', function(event) { $.timeentry._parseTime(inst); });
		}
		if ($.browser.msie) {
			input.bind('paste.timeEntry', 
				function(event) { setTimeout(function() { $.timeentry._parseTime(inst); }, 1); });
		}
		// allow mouse wheel usage
		if (this._get(inst, 'useMouseWheel') && $.fn.mousewheel) {
			input.mousewheel(this._doMouseWheel);
		}
		if (spinner) {
			spinner.mousedown(this._handleSpinner).mouseup(this._endSpinner).
				mouseout(this._endSpinner).mousemove(this._describeSpinner);
		}
	},

	/* Enable a time entry input and any associated spinner.
	   @param  input  (element) single input field */
	_enableTimeEntry: function(input) {
		this._enableDisable(input, false);
	},

	/* Disable a time entry input and any associated spinner.
	   @param  input  (element) single input field */
	_disableTimeEntry: function(input) {
		this._enableDisable(input, true);
	},

	/* Enable or disable a time entry input and any associated spinner.
	   @param  input    (element) single input field
	   @param  disable  (boolean) true to disable, false to enable */
	_enableDisable: function(input, disable) {
		var inst = $.data(input, PROP_NAME);
		if (!inst) {
			return;
		}
		input.disabled = disable;
		if (input.nextSibling && input.nextSibling.nodeName.toLowerCase() == 'span') {
			$.timeEntry._changeSpinner(inst, input.nextSibling, (disable ? 5 : -1));
		}
		$.timeEntry._disabledInputs = $.map($.timeEntry._disabledInputs,
			function(value) { return (value == input ? null : value); }); // delete entry
		if (disable) {
			$.timeEntry._disabledInputs.push(input);
		}
	},

	/* Check whether an input field has been disabled.
	   @param  input  (element) input field to check
	   @return  (boolean) true if this field has been disabled, false if it is enabled */
	_isDisabledTimeEntry: function(input) {
		return $.inArray(input, this._disabledInputs) > -1;
	},

	/* Reconfigure the settings for a time entry field.
	   @param  input    (element) input field to change
	   @param  options  (object) new settings to add */
	_changeTimeEntry: function(input, options) {
		var inst = $.data(input, PROP_NAME);
		if (inst) {
			var currentTime = this._extractTime(inst);
			extendRemove(inst.options, options || {});
			if (currentTime) {
				this._setTime(inst, new Date(0, 0, 0,
					currentTime[0], currentTime[1], currentTime[2]));
			}
		}
		$.data(input, PROP_NAME, inst);
	},

	/* Remove the time entry functionality from an input.
	   @param  input  (element) input field to affect */
	_destroyTimeEntry: function(input) {
		$input = $(input);
		if (!$input.hasClass(this.markerClassName)) {
			return;
		}
		$input.removeClass(this.markerClassName).unbind('focus.timeEntry').
			unbind('blur.timeEntry').unbind('click.timeEntry').
			unbind('keydown.timeEntry').unbind('keypress.timeEntry');
		// check pastes
		if ($.browser.mozilla) {
			$input.unbind('input.timeEntry');
		}
		if ($.browser.msie) {
			$input.unbind('paste.timeEntry');
		}
		if ($.fn.mousewheel) {
			$input.unmousewheel();
		}
		this._disabledInputs = $.map(this._disabledInputs,
			function(value) { return (value == input ? null : value); }); // delete entry
		$input.parent().replaceWith($input);
		$.removeData(input, PROP_NAME);
	},

	/* Initialise the current time for a time entry input field.
	   @param  input  (element) input field to update
	   @param  time   (Date) the new time (year/month/day ignored) or null for now */
	_setTimeTimeEntry: function(input, time) {
		var inst = $.data(input, PROP_NAME);
		if (inst) {
			this._setTime(inst, time ? (typeof time == 'object' ?
				new Date(time.getTime()) : time) : null);
		}
	},

	/* Retrieve the current time for a time entry input field.
	   @param  input  (element) input field to update
	   @return  (Date) current time (year/month/day zero) or null if none */
	_getTimeTimeEntry: function(input) {
		var inst = $.data(input, PROP_NAME);
		var currentTime = (inst ? this._extractTime(inst) : null);
		return (!currentTime ? null :
			new Date(0, 0, 0, currentTime[0], currentTime[1], currentTime[2]));
	},

	/* Initialise time entry.
	   @param  target  (element) the input field or
	                   (event) the focus event */
	_doFocus: function(target) {
		var input = (target.nodeName && target.nodeName.toLowerCase() == 'input' ? target : this);
		if ($.timeEntry._lastInput == input) { // already here
			return;
		}
		if ($.timeEntry._isDisabledTimeEntry(input)) {
			return;
		}
		var inst = $.data(input, PROP_NAME);
		$.timeEntry._focussed = true;
		$.timeEntry._lastInput = input;
		$.timeEntry._blurredInput = null;
		var beforeShow = $.timeEntry._get(inst, 'beforeShow');
		extendRemove(inst.options, (beforeShow ? beforeShow.apply(input, [input]) : {}));
		$.data(input, PROP_NAME, inst);
		$.timeEntry._parseTime(inst);
	},

	/* Note that the field has been exited.
	   @param  event  (event) the blur event */
	_doBlur: function(event) {
		$.timeEntry._blurredInput = $.timeEntry._lastInput;
		$.timeEntry._lastInput = null;
	},

	/* Select appropriate field portion on click, if already in the field.
	   @param  event  (event) the click event */
	_doClick: function(event) {
		var input = event.target;
		var inst = $.data(input, PROP_NAME);
		if (!$.timeEntry._focussed) {
			var fieldSize = $.timeEntry._get(inst, 'separator').length + 2;
			inst._field = 0;
			if ($.browser.msie) { // check against bounding boxes
				var value = input.value;
				var offsetX = event.clientX + document.documentElement.scrollLeft -
					$(event.srcElement).offset().left;
				for (var field = 0; field <= Math.max(1, inst._secondField, inst._ampmField); field++) {
					var end = (field != inst._ampmField ? (field * fieldSize) + 2 :
						(inst._ampmField * fieldSize) + $.timeEntry._get(inst, 'ampmPrefix').length +
						$.timeEntry._get(inst, 'ampmNames')[0].length);
					input.value = value.substring(0, end); // trim to this size
					var range = input.createTextRange();
					if (offsetX < range.boundingWidth) { // and compare
						inst._field = field;
						break;
					}
				}
				input.value = value; // restore original value
			}
			else { // use input select range
				for (var field = 0; field <= Math.max(1, inst._secondField, inst._ampmField); field++) {
					var start = (field != inst._ampmField ? (field * fieldSize) + 2 :
						(inst._ampmField * fieldSize) + $.timeEntry._get(inst, 'ampmPrefix').length +
						$.timeEntry._get(inst, 'ampmNames')[0].length);
					if (input.selectionStart < start) {
						inst._field = field;
						break;
					}
				}
			}
		}
		$.data(input, PROP_NAME, inst);
		$.timeEntry._showField(inst);
		$.timeEntry._focussed = false;
	},

	/* Handle keystrokes in the field.
	   @param  event  (event) the keydown event
	   @return  (boolean) true to continue, false to stop processing */
	_doKeyDown: function(event) {
		if (event.keyCode >= 48) { // >= '0'
			return true;
		}
		var inst = $.data(event.target, PROP_NAME);
		switch (event.keyCode) {
			case 9: return (event.shiftKey ?
						// move to previous time field, or out if at the beginning
						$.timeEntry._previousField(inst, true) :
						// move to next time field, or out if at the end
						$.timeEntry._nextField(inst, true));
			case 35: if (event.ctrlKey) { // clear time on ctrl+end
						$.timeEntry._setValue(inst, '');
					}
					else { // last field on end
						inst._field = Math.max(1, inst._secondField, inst._ampmField);
						$.timeEntry._adjustField(inst, 0);
					}
					break;
			case 36: if (event.ctrlKey) { // current time on ctrl+home
						$.timeEntry._setTime(inst);
					}
					else { // first field on home
						inst._field = 0;
						$.timeEntry._adjustField(inst, 0);
					}
					break;
			case 37: $.timeEntry._previousField(inst, false); break; // previous field on left
			case 38: $.timeEntry._adjustField(inst, +1); break; // increment time field on up
			case 39: $.timeEntry._nextField(inst, false); break; // next field on right
			case 40: $.timeEntry._adjustField(inst, -1); break; // decrement time field on down
			case 46: $.timeEntry._setValue(inst, ''); break; // clear time on delete
		}
		return false;
	},

	/* Disallow unwanted characters.
	   @param  event  (event) the keypress event
	   @return  (boolean) true to continue, false to stop processing */
	_doKeyPress: function(event) {
		var chr = String.fromCharCode(event.charCode == undefined ? event.keyCode : event.charCode);
		if (chr < ' ') {
			return true;
		}
		var inst = $.data(event.target, PROP_NAME);
		$.timeEntry._handleKeyPress(inst, chr);
		return false;
	},

	/* Increment/decrement on mouse wheel activity.
	   @param  event  (event) the mouse wheel event
	   @param  delta  (number) the amount of change */
	_doMouseWheel: function(event, delta) {
		if ($.timeEntry._isDisabledTimeEntry(event.target)) {
			return;
		}
		delta = ($.browser.opera ? -delta / Math.abs(delta) :
			($.browser.safari ? delta / Math.abs(delta) : delta));
		var inst = $.data(event.target, PROP_NAME);
		$.timeEntry._adjustField(inst, delta);
		event.preventDefault();
	},

	/* Change the title based on position within the spinner.
	   @param  event  (event) the mouse move event */
	_describeSpinner: function(event) {
		var spinner = $.timeEntry._getSpinnerTarget(event);
		var inst = $.data(spinner.previousSibling, PROP_NAME);
		spinner.title = $.timeEntry._get(inst, 'spinnerTexts')[$.timeEntry._getSpinnerRegion(inst, event)];
	},

	/* Handle a click on the spinner.
	   @param  event  (event) the mouse click event */
	_handleSpinner: function(event) {
		var spinner = $.timeEntry._getSpinnerTarget(event);
		var input = spinner.previousSibling;
		if ($.timeEntry._isDisabledTimeEntry(input)) {
			return;
		}
		if (input == $.timeEntry._blurredInput) {
			$.timeEntry._lastInput = input;
			$.timeEntry._blurredInput = null;
		}
		var inst = $.data(input, PROP_NAME);
		$.timeEntry._doFocus(input);
		var region = $.timeEntry._getSpinnerRegion(inst, event);
		$.timeEntry._changeSpinner(inst, spinner, region);
		$.timeEntry._actionSpinner(inst, region);
		$.timeEntry._timer = null;
		var spinnerRepeat = $.timeEntry._get(inst, 'spinnerRepeat');
		if (region >= 3 && spinnerRepeat[0]) { // repeat increment/decrement
			$.timeEntry._timer = setTimeout(
				function() { $.timeEntry._repeatSpinner(inst, region); },
				spinnerRepeat[0]);
			$(spinner).one('mouseout', $.timeEntry._releaseSpinner).
				one('mouseup', $.timeEntry._releaseSpinner);
		}
	},

	/* Action a click on the spinner.
	   @param  inst    (object) the instance settings
	   @param  region  (number) the spinner "button" */
	_actionSpinner: function(inst, region) {
		switch (region) {
			case 0: this._setTime(inst); break;
			case 1: this._previousField(inst, false); break;
			case 2: this._nextField(inst, false); break;
			case 3: this._adjustField(inst, +1); break;
			case 4: this._adjustField(inst, -1); break;
		}
	},

	/* Repeat a click on the spinner.
	   @param  inst    (object) the instance settings
	   @param  region  (number) the spinner "button" */
	_repeatSpinner: function(inst, region) {
		if (!$.timeEntry._timer) {
			return;
		}
		$.timeEntry._lastInput = $.timeEntry._blurredInput;
		this._actionSpinner(inst, region);
		this._timer = setTimeout(
			function() { $.timeEntry._repeatSpinner(inst, region); },
			this._get(inst, 'spinnerRepeat')[1]);
	},

	/* Stop a spinner repeat.
	   @param  event  (event) the mouse event */
	_releaseSpinner: function(event) {
		clearTimeout($.timeEntry._timer);
		$.timeEntry._timer = null;
	},

	/* Tidy up after a spinner click.
	   @param  event  (event) the mouse event */
	_endSpinner: function(event) {
		$.timeEntry._timer = null;
		var spinner = $.timeEntry._getSpinnerTarget(event);
		var input = spinner.previousSibling;
		var inst = $.data(input, PROP_NAME);
		if (!$.timeEntry._isDisabledTimeEntry(input)) {
			$.timeEntry._changeSpinner(inst, spinner, -1);
		}
		if (!$.browser.opera) {
			$.timeEntry._lastInput = $.timeEntry._blurredInput;
		}
		if ($.timeEntry._lastInput) {
			$.timeEntry._showField(inst);
		}
	},

	/* Retrieve the spinner from the event.
	   @param  event  (event) the mouse click event
	   @return  (element) the target field */
	_getSpinnerTarget: function(event) {
		return event.target || event.srcElement;
	},

	/* Determine which "button" within the spinner was clicked.
	   @param  inst   (object) the instance settings
	   @param  event  (event) the mouse event
	   @return  (number) the spinner "button" number */
	_getSpinnerRegion: function(inst, event) {
		var spinner = this._getSpinnerTarget(event);
		var pos = ($.browser.opera || $.browser.safari ?
			$.timeEntry._findPos(spinner) : $(spinner).offset());
		var scrolled = ($.browser.safari ? $.timeEntry._findScroll(spinner) :
			[document.documentElement.scrollLeft || document.body.scrollLeft,
			document.documentElement.scrollTop || document.body.scrollTop]);
		var spinnerIncDecOnly = this._get(inst, 'spinnerIncDecOnly');
		var left = (spinnerIncDecOnly ? 99 :
			event.clientX + scrolled[0] - pos.left - ($.browser.msie ? 1 : 0));
		var top = event.clientY + scrolled[1] - pos.top - ($.browser.msie ? 1 : 0);
		var spinnerSize = this._get(inst, 'spinnerSize');
		var right = (spinnerIncDecOnly ? 99 : spinnerSize[0] - left);
		var bottom = spinnerSize[1] - top;
		if (spinnerSize[2] > 0 && Math.abs(left - right) <= spinnerSize[2] &&
				Math.abs(top - bottom) <= spinnerSize[2]) {
			return 0; // centre button
		}
		var min = Math.min(left, top, right, bottom);
		return (min == left ? 1 : (min == right ? 2 : (min == top ? 3 : 4))); // nearest edge
	},

	/* Change the spinner image depending on button clicked.
	   @param  inst     (object) the instance settings
	   @param  spinner  (element) the spinner control
	   @param  region   (number) the spinner "button" */
	_changeSpinner: function(inst, spinner, region) {
		$(spinner).css('background-position',
			'-' + ((region + 1) * this._get(inst, 'spinnerSize')[0]) + 'px 0px');
	},

	/* Find an object's position on the screen.
	   @param  obj  (element) the control
	   @return  (object) position as .left and .top */
	_findPos: function(obj) {
		var curLeft = curTop = 0;
		if (obj.offsetParent) {
			curLeft = obj.offsetLeft;
			curTop = obj.offsetTop;
			while (obj = obj.offsetParent) {
				var origCurLeft = curLeft;
				curLeft += obj.offsetLeft;
				if (curLeft < 0) {
					curLeft = origCurLeft;
				}
				curTop += obj.offsetTop;
			}
		}
		return {left: curLeft, top: curTop};
	},

	/* Find an object's scroll offset on the screen.
	   @param  obj  (element) the control
	   @return  (number[]) offset as [left, top] */
	_findScroll: function(obj) {
		var isFixed = false;
		$(obj).parents().each(function() {
			isFixed |= $(this).css('position') == 'fixed';
		});
		if (isFixed) {
			return [0, 0];
		}
		var scrollLeft = obj.scrollLeft;
		var scrollTop = obj.scrollTop;
		while (obj = obj.parentNode) {
			scrollLeft += obj.scrollLeft || 0;
			scrollTop += obj.scrollTop || 0;
		}
		return [scrollLeft, scrollTop];
	},

	/* Get a setting value, defaulting if necessary.
	   @param  inst  (object) the instance settings
	   @param  name  (string) the setting name
	   @return  (any) the setting value */
	_get: function(inst, name) {
		return (inst.options[name] != null ?
			inst.options[name] : $.timeEntry._defaults[name]);
	},

	/* Extract the time value from the input field, or default to now.
	   @param  inst  (object) the instance settings */
	_parseTime: function(inst) {
		var currentTime = this._extractTime(inst);
		var showSeconds = this._get(inst, 'showSeconds');
		if (currentTime) {
			inst._selectedHour = currentTime[0];
			inst._selectedMinute = currentTime[1];
			inst._selectedSecond = currentTime[2];
		}
		else {
			var now = this._constrainTime(inst);
			inst._selectedHour = now[0];
			inst._selectedMinute = now[1];
			inst._selectedSecond = (showSeconds ? now[2] : 0);
		}
		inst._secondField = (showSeconds ? 2 : -1);
		inst._ampmField = (this._get(inst, 'show24Hours') ? -1 : (showSeconds ? 3 : 2));
		inst._lastChr = '';
		inst._field = Math.max(0, Math.min(
			Math.max(1, inst._secondField, inst._ampmField), this._get(inst, 'initialField')));
		if (inst.input.val() != '') {
			this._showTime(inst);
		}
	},

	/* Extract the time value from the input field as an array of values, or default to null.
	   @param  inst  (object) the instance settings
	   @return  (number[3]) the time components (hours, minutes, seconds)
	            or null if no value */
	_extractTime: function(inst) {
		var value = inst.input.val();
		var separator = this._get(inst, 'separator');
		var currentTime = value.split(separator);
		if (separator == '' && value != '') {
			currentTime[0] = value.substring(0, 2);
			currentTime[1] = value.substring(2, 4);
			currentTime[2] = value.substring(4, 6);
		}
		var ampmNames = this._get(inst, 'ampmNames');
		var show24Hours = this._get(inst, 'show24Hours');
		if (currentTime.length >= 2) {
			var isAM = !show24Hours && (value.indexOf(ampmNames[0]) > -1);
			var isPM = !show24Hours && (value.indexOf(ampmNames[1]) > -1);
			var hour = parseInt(currentTime[0], 10);
			hour = (isNaN(hour) ? 0 : hour);
			hour = ((isAM || isPM) && hour == 12 ? 0 : hour) + (isPM ? 12 : 0);
			var minute = parseInt(currentTime[1], 10);
			minute = (isNaN(minute) ? 0 : minute);
			var second = (currentTime.length >= 3 ?
				parseInt(currentTime[2], 10) : 0);
			second = (isNaN(second) || !this._get(inst, 'showSeconds') ? 0 : second);
			return this._constrainTime(inst, [hour, minute, second]);
		} 
		return null;
	},

	/* Constrain the given/current time to the time steps.
	   @param  inst    (object) the instance settings
	   @param  fields  (number[3]) the current time components (hours, minutes, seconds)
	   @return  (number[3]) the constrained time components (hours, minutes, seconds) */
	_constrainTime: function(inst, fields) {
		var specified = (fields != null);
		if (!specified) {
			var now = this._determineTime(this._get(inst, 'defaultTime')) || new Date();
			fields = [now.getHours(), now.getMinutes(), now.getSeconds()];
		}
		var reset = false;
		var timeSteps = this._get(inst, 'timeSteps');
		for (var i = 0; i < timeSteps.length; i++) {
			if (reset) {
				fields[i] = 0;
			}
			else if (timeSteps[i] > 1) {
				fields[i] = Math.round(fields[i] / timeSteps[i]) * timeSteps[i];
				reset = true;
			}
		}
		return fields;
	},

	/* Set the selected time into the input field.
	   @param  inst  (object) the instance settings */
	_showTime: function(inst) {
		var show24Hours = this._get(inst, 'show24Hours');
		var separator = this._get(inst, 'separator');
		var currentTime = (this._formatNumber(show24Hours ? inst._selectedHour :
			((inst._selectedHour + 11) % 12) + 1) + separator +
			this._formatNumber(inst._selectedMinute) +
			(this._get(inst, 'showSeconds') ? separator +
			this._formatNumber(inst._selectedSecond) : '') +
			(show24Hours ?  '' : this._get(inst, 'ampmPrefix') +
			this._get(inst, 'ampmNames')[(inst._selectedHour < 12 ? 0 : 1)]));
		this._setValue(inst, currentTime);
		this._showField(inst);
	},

	/* Highlight the current time field.
	   @param  inst  (object) the instance settings */
	_showField: function(inst) {
		if (inst.input.is(':hidden')) {
			return;
		}
		var input = inst.input[0];
		var separator = this._get(inst, 'separator');
		var fieldSize = separator.length + 2;
		var start = (inst._field != inst._ampmField ? (inst._field * fieldSize) :
			(inst._ampmField * fieldSize) - separator.length + this._get(inst, 'ampmPrefix').length);
		var end = start + (inst._field != inst._ampmField ? 2 : this._get(inst, 'ampmNames')[0].length);
		if (input.setSelectionRange) { // Mozilla
			input.setSelectionRange(start, end);
		}
		else if (input.createTextRange) { // IE
			var range = input.createTextRange();
			range.moveStart('character', start);
			range.moveEnd('character', end - inst.input.val().length);
			range.select();
		}
		if (!input.disabled && $.timeEntry._lastInput == input) {
			input.focus();
		}
	},

	/* Ensure displayed single number has a leading zero.
	   @param  value  (number) current value
	   @return  (string) number with at least two digits */
	_formatNumber: function(value) {
		return (value < 10 ? '0' : '') + value;
	},

	/* Update the input field and notify listeners.
	   @param  inst   (object) the instance settings
	   @param  value  (string) the new value */
	_setValue: function(inst, value) {
		inst.input.val(value).trigger('change');
	},

	/* Move to previous field, or out of field altogether if appropriate.
	   @param  inst     (object) the instance settings
	   @param  moveOut  (boolean) true if can move out of the field
	   @return  (boolean) true if exitting the field, false if not */
	_previousField: function(inst, moveOut) {
		var atFirst = (inst.input.val() == '' || inst._field == 0);
		if (!atFirst) {
			inst._field--;
		}
		this._showField(inst);
		inst._lastChr = '';
		$.data(inst.input[0], PROP_NAME, inst);
		return (atFirst && moveOut);
	},

	/* Move to next field, or out of field altogether if appropriate.
	   @param  inst     (object) the instance settings
	   @param  moveOut  (boolean) true if can move out of the field
	   @return  (boolean) true if exitting the field, false if not */
	_nextField: function(inst, moveOut) {
		var atLast = (inst.input.val() == '' ||
			inst._field == Math.max(1, inst._secondField, inst._ampmField));
		if (!atLast) {
			inst._field++;
		}
		this._showField(inst);
		inst._lastChr = '';
		$.data(inst.input[0], PROP_NAME, inst);
		return (atLast && moveOut);
	},

	/* Update the current field in the direction indicated.
	   @param  inst    (object) the instance settings
	   @param  offset  (number) the amount to change by */
	_adjustField: function(inst, offset) {
		if (inst.input.val() == '') {
			offset = 0;
		}
		var timeSteps = this._get(inst, 'timeSteps');
		this._setTime(inst, new Date(0, 0, 0,
			inst._selectedHour + (inst._field == 0 ? offset * timeSteps[0] : 0) +
			(inst._field == inst._ampmField ? offset * 12 : 0),
			inst._selectedMinute + (inst._field == 1 ? offset * timeSteps[1] : 0),
			inst._selectedSecond + (inst._field == inst._secondField ? offset * timeSteps[2] : 0)));
	},

	/* Check against minimum/maximum and display time.
	   @param  inst  (object) the instance settings
	   @param  time  (Date) an actual time or
	                 (number) offset in seconds from now or
					 (string) units and periods of offsets from now */
	_setTime: function(inst, time) {
		time = this._determineTime(time);
		var fields = this._constrainTime(inst, time ?
			[time.getHours(), time.getMinutes(), time.getSeconds()] : null);
		time = new Date(0, 0, 0, fields[0], fields[1], fields[2]);
		// normalise to base date
		var time = this._normaliseTime(time);
		var minTime = this._normaliseTime(this._determineTime(this._get(inst, 'minTime')));
		var maxTime = this._normaliseTime(this._determineTime(this._get(inst, 'maxTime')));
		// ensure it is within the bounds set
		time = (minTime && time < minTime ? minTime :
			(maxTime && time > maxTime ? maxTime : time));
		var beforeSetTime = this._get(inst, 'beforeSetTime');
		// perform further restrictions if required
		if (beforeSetTime) {
			time = beforeSetTime.apply(inst.input[0],
				[this._getTimeTimeEntry(inst.input[0]), time, minTime, maxTime]);
		}
		inst._selectedHour = time.getHours();
		inst._selectedMinute = time.getMinutes();
		inst._selectedSecond = time.getSeconds();
		this._showTime(inst);
		$.data(inst.input[0], PROP_NAME, inst);
	},

	/* A time may be specified as an exact value or a relative one.
	   @param  setting  (Date) an actual time or
	                    (number) offset in seconds from now or
	                    (string) units and periods of offsets from now
	   @return  (Date) the calculated time */
	_determineTime: function(setting) {
		var offsetNumeric = function(offset) { // e.g. +300, -2
			var time = new Date();
			time.setTime(time.getTime() + offset * 1000);
			return time;
		};
		var offsetString = function(offset) { // e.g. '+2m', '-4h', '+3h +30m'
			var time = new Date();
			var hour = time.getHours();
			var minute = time.getMinutes();
			var second = time.getSeconds();
			var pattern = /([+-]?[0-9]+)\s*(s|S|m|M|h|H)?/g;
			var matches = pattern.exec(offset);
			while (matches) {
				switch (matches[2] || 's') {
					case 's' : case 'S' :
						second += parseInt(matches[1], 10); break;
					case 'm' : case 'M' :
						minute += parseInt(matches[1], 10); break;
					case 'h' : case 'H' :
						hour += parseInt(matches[1], 10); break;
				}
				matches = pattern.exec(offset);
			}
			time = new Date(0, 0, 10, hour, minute, second, 0);
			if (/^!/.test(offset)) { // no wrapping
				if (time.getDate() > 10) {
					time = new Date(0, 0, 10, 23, 59, 59);
				}
				else if (time.getDate() < 10) {
					time = new Date(0, 0, 10, 0, 0, 0);
				}
			}
			return time;
		};
		return (setting ? (typeof setting == 'string' ? offsetString(setting) :
			(typeof setting == 'number' ? offsetNumeric(setting) : setting)) : null);
	},

	/* Normalise time object to a common date.
	   @param  time  (Date) the original time
	   @return  (Date) the normalised time */
	_normaliseTime: function(time) {
		if (!time) {
			return null;
		}
		time.setFullYear(1900);
		time.setMonth(0);
		time.setDate(0);
		return time;
	},

	/* Update time based on keystroke entered.
	   @param  inst  (object) the instance settings
	   @param  chr   (ch) the new character */
	_handleKeyPress: function(inst, chr) {
		if (chr == this._get(inst, 'separator')) {
			this._nextField(inst, false);
		}
		else if (chr >= '0' && chr <= '9') { // allow direct entry of time
			var value = parseInt(inst._lastChr + chr, 10);
			var show24Hours = this._get(inst, 'show24Hours');
			var hour = (inst._field == 0 && ((show24Hours && value < 24) ||
				(value >= 1 && value <= 12)) ?
				value + (!show24Hours && inst._selectedHour >= 12 ? 12 : 0) : inst._selectedHour);
			var minute = (inst._field == 1 && value < 60 ? value : inst._selectedMinute);
			var second = (inst._field == inst._secondField && value < 60 ?
				value : inst._selectedSecond);
			var fields = this._constrainTime(inst, [hour, minute, second]);
			this._setTime(inst, new Date(0, 0, 0, fields[0], fields[1], fields[2]));
			inst._lastChr = chr;
		}
		else if (!this._get(inst, 'show24Hours')) { // set am/pm based on first char of names
			var ampmNames = this._get(inst, 'ampmNames');
			if ((chr == ampmNames[0].substring(0, 1).toLowerCase() &&
					inst._selectedHour >= 12) ||
					(chr == ampmNames[1].substring(0, 1).toLowerCase() &&
					inst._selectedHour < 12)) {
				var saveField = inst._field;
				inst._field = inst._ampmField;
				this._adjustField(inst, +1);
				inst._field = saveField;
				this._showField(inst);
			}
		}
	}
});

/* jQuery extend now ignores nulls!
   @param  target  (object) the object to update
   @param  props   (object) the new settings 
   @return  (object) the updated object */
function extendRemove(target, props) {
	$.extend(target, props);
	for (var name in props) {
		if (props[name] == null) {
			target[name] = null;
		}
	}
	return target;
}

/* Attach the time entry functionality to a jQuery selection.
   @param  command  (string) the command to run (optional, default 'attach')
   @param  options  (object) the new settings to use for these countdown instances (optional)
   @return  (jQuery) for chaining further calls */
$.fn.timeEntry = function(options) {
	var otherArgs = Array.prototype.slice.call(arguments, 1);
	if (typeof options == 'string' && (options == 'isDisabled' || options == 'getTime')) {
		return $.timeEntry['_' + options + 'TimeEntry'].apply($.timeEntry, [this[0]].concat(otherArgs));
	}
	return this.each(function() {
		var nodeName = this.nodeName.toLowerCase();
		if (nodeName == 'input') {
			if (typeof options == 'string') {
				$.timeEntry['_' + options + 'TimeEntry'].apply($.timeEntry, [this].concat(otherArgs));
			}
			else {
				// check for settings on the control itself - in namespace 'time:'
				var inlineSettings = {};
				for (attrName in $.timeEntry._defaults) {
					var attrValue = this.getAttribute('time:' + attrName);
					if (attrValue) {
						inlineSettings = inlineSettings || {};
						try {
							inlineSettings[attrName] = eval(attrValue);
						}
						catch (err) {
							inlineSettings[attrName] = attrValue;
						}
					}
				}
				$.timeEntry._connectTimeEntry(this, $.extend(inlineSettings, options));
			}
		} 
	});
};

/* Initialise the time entry functionality. */
$.timeEntry = new TimeEntry(); // singleton instance

})(jQuery);
