// Global Variables
var ai1ec_geocoder, ai1ec_default_location, ai1ec_myOptions, ai1ec_map, ai1ec_marker, ai1ec_marker_draggable;

/**
 *
 * converts commas to dots as in some regions (Europe for example) floating point numbers are defined with a comma instead of a dot
 *
 */
var ai1ec_convert_commas_to_dots_for_coordinates = function() {
	if ( jQuery( '#ai1ec_input_coordinates:checked' ).length > 0 ) {
		jQuery( '#ai1ec_table_coordinates input.coordinates' ).each( function() {
			this.value = AI1EC_UTILS.convert_comma_to_dot( this.value );
		});
	}
}
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
	var error = jQuery( '<div />',
			{
				text : error_message,
				class : "ai1ec-error"
			}
	);
	// Insert error message
	jQuery( el ).after( error );
}
/**
 *
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
	if ( e.target.id === 'publish' ) {
		// Prevent other events from firing
		e.stopImmediatePropagation();
		// Prevent the submit
		e.preventDefault();
	}
	// Focus on the first field that has an error
	jQuery( el ).focus();
}

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
	jQuery( '#ai1ec_address' ).val( result.formatted_address );

	var street_number = '',
				street_name = '',
				city = '',
				state = '',
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

	jQuery( '#ai1ec_city' ).val( city );
	jQuery( '#ai1ec_province' ).val( province );
	jQuery( '#ai1ec_postal_code' ).val( postal_code );
	jQuery( '#ai1ec_country' ).val( country );
};

/**
 * Show/hide elements that show selectors for ending until/after events
 */
var show_end_fields = function() {
	var selected = jQuery( '#ai1ec_end option:selected' ).val();
	switch( selected ) {
		// Never selected, hide end fields
		case '0':
			hide_all_end_fields();
			break;
		// After selected
		case '1':
			if( jQuery( '#ai1ec_count_holder' ).css( 'display' ) == 'none' ) {
				hide_all_end_fields();
				jQuery( '#ai1ec_count_holder' ).fadeIn();
			}
			break;
		// On date selected
		case '2':
			if( jQuery( '#ai1ec_until_holder' ).css( 'display' ) == 'none' ) {
				hide_all_end_fields();
				jQuery( '#ai1ec_until_holder' ).fadeIn();
			}
			break;
	}
};

/**
 * Show/hide elements that show selectors for repeating events
 */
var show_all_repeat_fields = function() {
	jQuery( '#ai1ec_end_holder' ).fadeIn();
	show_end_fields();
};

var hide_all_repeat_fields = function() {
	hide_all_end_fields();
	hide_custom_repeat_elements();
	hide_frequency();
	jQuery( '#ai1ec_end_holder' ).fadeOut();
};

var hide_all_end_fields = function() {
	jQuery( '#ai1ec_count_holder, #ai1ec_until_holder' ).hide();
};

var ai1ec_selector = function( selector ) {
	jQuery( selector + ' > li' ).live( 'click', function() {
		if( jQuery( this ).hasClass( 'ai1ec_selected' ) ) {
			jQuery( this ).removeClass( 'ai1ec_selected' );
		} else {
			jQuery( this ).addClass( 'ai1ec_selected' );
		}
		var data = new Array();
		jQuery( selector + ' > li' ).each( function() {
			if( jQuery( this ).hasClass( 'ai1ec_selected' ) ) {
				var value = jQuery( this ).children( 'input[type="hidden"]:first' ).val();
				data.push( value );
			}
		});
		jQuery( selector ).next().val( data.join() );
	});
};

var ai1ec_repeat_form_success = function( s1, s2, s3, rule, button, response ) {
	jQuery( s1 ).val( rule );
	jQuery.unblockUI();
	var txt = jQuery.trim( jQuery( s2 ).text() );
	if( txt.lastIndexOf( ':' ) == -1 ) {
		txt = txt.substring( 0, txt.length - 3 );
		jQuery( s2 ).text( txt + ':' );
	}
	jQuery(button).attr( 'disabled', false );
	jQuery( s3 ).fadeOut( 'fast', function() {
		jQuery( this ).text( response.message );
		jQuery( this ).fadeIn( 'fast' );
	});
};

var ai1ec_repeat_form_error = function( s1, s2, response, button ) {
	jQuery.growlUI( 'Error', response.message );
	jQuery( button ).attr( 'disabled', false );
	jQuery( s1 ).val( '' );
	var txt = jQuery.trim( jQuery( s2 ).text() );
	if( txt.lastIndexOf( '...' ) == -1 ) {
		txt = txt.substring( 0, txt.length - 1 );
		jQuery( s2 ).text( txt + '...' );
	}
};

var ai1ec_click_on_ics_rule_text = function( s1, s2, s3, data, fn ) {
	jQuery( s1 ).live( 'click', function() {
		if( ! jQuery( s2 ).is( ':checked' ) ) {
			jQuery( s2 ).attr( 'checked', true );
			var txt = jQuery.trim( jQuery( s3 ).text() );
			txt = txt.substring( 0, txt.length - 3 );
			jQuery( s3 ).text( txt + ':' );
		}
		ai1ec_show_repeat_tabs( data, fn );
		return false;
	});
};

var ai1ec_click_on_checkbox = function( s1, s2, s3, data, fn ) {
	jQuery( s1 ).click( function() {
		if( jQuery(this).is( ':checked' ) ) {
			ai1ec_show_repeat_tabs( data, fn );
		} else {
			jQuery( s2 ).text( '' );
			var txt = jQuery.trim( jQuery( s3 ).text() );
			txt = txt.substring( 0, txt.length - 1 );
			jQuery( s3 ).text( txt + '...' );
		}
	});
};

var ai1ec_click_on_modal_cancel = function( s1, s2, s3 ) {
	if( jQuery.trim( jQuery( s1 ).text() ) == '' ) {
		jQuery( s2 ).attr( 'checked', false );
		var txt = jQuery.trim( jQuery( s3 ).text() );
		if( txt.lastIndexOf( '...' ) == -1 ) {
			txt = txt.substring( 0, txt.length - 1 );
			jQuery( s3 ).text( txt + '...' );
		}
	}
};

// called after the repeat block is inserted in the DOM
var ai1ec_apply_js_on_repeat_block = function() {
	// Initialize count range slider
	jQuery( '#ai1ec_count, #ai1ec_daily_count, #ai1ec_weekly_count, #ai1ec_monthly_count, #ai1ec_yearly_count' ).rangeinput( {
		css: {
			input: 'ai1ec-range',
			slider: 'ai1ec-slider',
			progress: 'ai1ec-progress',
			handle: 'ai1ec-handle'
		}
	} );
	// Initialize inputdate plugin on our "until" date input.
	data = {
		start_date_input: 	'#ai1ec_until-date-input',
		start_time:       	'#ai1ec_until-time',
		date_format:				ai1ec_add_new_event.date_format,
		month_names:				ai1ec_add_new_event.month_names,
		day_names:					ai1ec_add_new_event.day_names,
		week_start_day:			ai1ec_add_new_event.week_start_day,
		twentyfour_hour:  	ai1ec_add_new_event.twentyfour_hour,
		now:              	new Date( ai1ec_add_new_event.now * 1000 )
	}
	jQuery.inputdate( data );
};

var ai1ec_show_repeat_tabs = function( data, post_ajax_func ) {
	jQuery.blockUI( {
		message: '<div class="ai1ec-repeat-box-loading"></div>',
		css: {
			width: '358px',
			border: '0',
			background: 'transparent',
			cursor: 'normal'
		}
	});
	jQuery.post(
		ajaxurl,
		data,
		function( response ) {
			if( response.error ) {
				// tell the user there is an error
				// TODO: Use other method of notification
				alert( response.message );
				jQuery.unblockUI();
			} else {
				// display the form
				jQuery.blockUI( {
					message: response.message,
					css: {
						width: '358px',
						border: '0',
						background: 'transparent',
						cursor: 'normal'
					}
				});
				var fn = window[post_ajax_func];
				if( typeof fn === 'function' ) {
					fn();
				}
			}
		},
		'json'
	);
};

/**
 * isUrl checks to see if the passed parameter is a valid url
 * and returns true on access and false on failure
 *
 * @param String s String to validate
 *
 * @return boolean True if the string is a valid url, false otherwise
 */
var isUrl = function( s ) {
	var regexp = /(http|https|webcal):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
	return regexp.test(s);
};

// ====================
// = jQuery DOM Ready =
// ====================
jQuery( function( $ ){

	/**
	 * Click event handler for Dismiss button
	 * that disables the data notification for admin users
	 */
	$( '.ai1ec-dismiss-notification' ).live( 'click', function() {
		var $button = $( this );
		var $parent = $( this ).parent().parent();
		// disable the update button
		$button.attr( 'disabled', true );

		// create the data to send
		var data = {
			action: 'ai1ec_disable_notification',
			note:   false
		};

		$.post( ajaxurl, data, function( response ) {
			if( response.error ) {
				// tell the user that there is an error
				alert( response.message );
			} else {
				// hide notification message
				$parent.remove();
			}
		});
	});

	/**
	 * Event post creation/edit form
	 */
	if( $('#ai1ec_event' ).length )
	{

		var now = new Date( ai1ec_add_new_event.now * 1000 );

		/**
		 * Timespan plugin setup
		 */

		// Initialize timespan plugin on our date/time inputs.
		var data = {
			allday: 						'#ai1ec_all_day_event',
			start_date_input: 	'#ai1ec_start-date-input',
			start_time_input: 	'#ai1ec_start-time-input',
			start_time: 				'#ai1ec_start-time',
			end_date_input: 		'#ai1ec_end-date-input',
			end_time_input: 		'#ai1ec_end-time-input',
			end_time: 					'#ai1ec_end-time',
			date_format:				ai1ec_add_new_event.date_format,
			month_names:				ai1ec_add_new_event.month_names,
			day_names:					ai1ec_add_new_event.day_names,
			week_start_day:			ai1ec_add_new_event.week_start_day,
			twentyfour_hour:  	ai1ec_add_new_event.twentyfour_hour,
			now:              	now
		}
		$.timespan( data );

		var exdate  = $( "#ai1ec_exdate" ).val();
		var dp_date = null;
		var _clear_dp = false;
		if( exdate.length >= 8 ) {
			dp_date = new Array();
			var _span_html = '';
			$.each( exdate.split( ',' ), function( i, v ) {
				_date = v.slice( 0, 8 );
				_year = _date.substr( 0, 4 );
				_month = _date.substr( 4, 2 );
				_day = _date.substr( 6, 2 );
				_span_html += _year + '-' + _month + '-' + _day + ',';
				_month = _month.charAt(0) == '0' ? ( '0' + ( parseInt( _month.charAt( 1 ) ) - 1 ) )
				                                 : ( parseInt( _month ) - 1 )

				dp_date.push( new Date( _year, _month, _day ) );
			});
			_span_html = _span_html.slice( 0, _span_html.length - 1 );
			$( '#widgetField span:first' ).html( _span_html );
		} else {
			dp_date = new Date( ai1ec_add_new_event.now * 1000 )
			_clear_dp = true;
		}

		$( '#widgetCalendar' ).DatePicker({
			flat: true,
			calendars: 3,
			mode: 'multiple',
			start: 1,
			date: dp_date,
			onChange: function( formated ) {
				$( '#widgetField span' ).get( 0 ).innerHTML = formated;
				formated = formated.toString();
				if( formated.length >= 8 ) {
					// save the date in your hidden field
					var exdate = '';
					$.each( formated.split( ',' ), function( i, v ) {
						exdate += v.replace( /-/g, '' ) + 'T000000Z,';
					});
					exdate = exdate.slice( 0, exdate.length - 1 );
					$( "#ai1ec_exdate" ).val( exdate );
				} else {
					$( "#ai1ec_exdate" ).val( '' );
				}
			}
		});

		if( _clear_dp ) {
			$( '#widgetCalendar' ).DatePickerClear();
		}

		var state = false;
		$( '#widgetField > a, #widgetField > span, #ai1ec_exclude_date_label' ).bind( 'click', function() {
			$('#widgetCalendar').stop().animate( { height: state ? 0 : $( '#widgetCalendar div.datepicker' ).get( 0 ).offsetHeight }, 500 );
			state = !state;
			return false;
		});
		$( '#widgetCalendar div.datepicker' ).css( 'position', 'absolute' )

		/**
		 * Google map setup
		 */

		// If the user is updating an event, initialize the map to the event
		// location, otherwise if the user is creating a new event initialize
		// the map to the whole world
		var ai1ec_geocoder = new google.maps.Geocoder();
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
		ai1ec_map = new google.maps.Map( $( '#ai1ec_map_canvas' ).get(0), ai1ec_myOptions );
		ai1ec_marker = new google.maps.Marker({ map: ai1ec_map });

		if( ! ai1ec_add_new_event.disable_autocompletion ) {
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
						region: ai1ec_add_new_event.region
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
									'region': ai1ec_add_new_event.region
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
		// If the coordinates checkbox is not checked
		if( $( '#ai1ec_input_coordinates:checked' ).length === 0 ) {
			// Hide the table (i hide things in js for progressive enhancement reasons)
			$( '#ai1ec_table_coordinates' ).css( { visibility : 'hidden' } );
			// Trigger the change event on the address to show the mep
			$( '#ai1ec_address' ).change();
		} else {
			// If the checkbox is checked, show the map using the coordinates
			ai1ec_update_map_from_coordinates();
		}
		// Toggle the visibility of google map on checkbox click
		$( '#ai1ec_google_map' ).click( function() {
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
		});

		ai1ec_selector( '#ai1ec_weekly_date_select' );
		ai1ec_selector( '#ai1ec_montly_date_select' );
		ai1ec_selector( '#ai1ec_yearly_date_select' );

		// ========================
		// = End dropdown clicked =
		// ========================
		$( '#ai1ec_end' ).live( 'change', show_end_fields );

		/**
		 * Bottom publish button click event handler
		 */
		if( $( '#ai1ec_bottom_publish' ).length > 0 ) {
			$( '#ai1ec_bottom_publish' ).click( function() {
				$( '#publish' ).trigger( 'click' );
			});
		}

		$( '.ai1ec_tab' ).live( 'click', function() {
			if( ! $( this ).hasClass( 'ai1ec_active' ) ) {
				var $active_tab = $( '.ai1ec_repeat_tabs > li > a.ai1ec_active' );
				var $active_content = $( $active_tab.attr( 'href' ) );

				var $becoming_active = $( $( this ).attr( 'href' ) );

				$active_tab.removeClass( 'ai1ec_active' );
				$active_content.hide();

				$( this ).addClass( 'ai1ec_active' );
				$becoming_active.append( $( '#ai1ec_repeat_tab_append' ) );
				$( '#ai1ec_ending_box' ).show();
				$becoming_active.show();
			}
			return false;
		});

		$( '.ai1ec_repeat_apply' ).live( 'click', function() {
			var $button = $( this );
			var rule = '';
			var $active_tab = $( $( '.ai1ec_active' ).attr( 'href' ) );
			var frequency = $active_tab.attr( 'title' );
			switch( frequency ) {
				case 'daily':
					rule += 'FREQ=DAILY;';
					var interval = $( '#ai1ec_daily_count' ).val();
					if( interval > 1 )
						rule += 'INTERVAL=' + interval + ';';
					break;
				case 'weekly':
					rule += 'FREQ=WEEKLY;';
					var interval = $( '#ai1ec_weekly_count' ).val();
					if( interval > 1 )
						rule += 'INTERVAL=' + interval + ';';
					var week_days = $( 'input[name="ai1ec_weekly_date_select"]:first' ).val();
					var wkst = $( '#ai1ec_weekly_date_select > li:first > input[type="hidden"]:first' ).val();
					if( week_days.length > 0 )
						rule += 'WKST=' + wkst + ';BYDAY=' + week_days + ';';
					break;
				case 'monthly':
					rule += 'FREQ=MONTHLY;';
					var interval  = $( '#ai1ec_monthly_count' ).val();
					var monthtype = $( 'input[name="ai1ec_monthly_type"]:checked' ).val();
					if( interval > 1 )
						rule += 'INTERVAL=' + interval + ';';
					var month_days = $( 'input[name="ai1ec_montly_date_select"]:first' ).val();
					if( month_days.length > 0 && monthtype == 'bymonthday' ) {
						rule += 'BYMONTHDAY=' + month_days + ';';
					} else if ( monthtype == 'byday' ) {
						byday_num     = $( '#ai1ec_monthly_byday_num' ).val();
						byday_weekday = $( '#ai1ec_monthly_byday_weekday' ).val();
						rule += 'BYDAY=' + byday_num + byday_weekday + ';';
					}
					break;
				case 'yearly':
					rule += 'FREQ=YEARLY;';
					var interval = $( '#ai1ec_yearly_count' ).val();
					if( interval > 1 )
						rule += 'INTERVAL=' + interval + ';';
					var months = $( 'input[name="ai1ec_yearly_date_select"]:first' ).val();
					if( months.length > 0 )
						rule += 'BYMONTH=' + months + ';';
					break;
			}

			var ending = $( '#ai1ec_end' ).val();
			// After
			if( ending == '1' ) {
				rule += 'COUNT=' + $( '#ai1ec_count' ).val() + ';';
			}
			// On Date
			if( ending == '2' ) {
				var until = parseDate( $( '#ai1ec_until-date-input' ).val(), ai1ec_add_new_event.date_format );
				var start = new Date( parseDate( $( '#ai1ec_start-time' ).val(), ai1ec_add_new_event.date_format ) );
				// Get UTC Day and UTC Month, and then add leading zeroes if required
				var d     = until.getUTCDate();
				var m     = until.getUTCMonth() + 1;
				var hh    = start.getUTCHours();
				var mm    = start.getUTCMinutes();
				var ss    = '00';
				// months
				m         = ( m < 10 )  ? '0' + m  : m;
				// days
				d         = ( d < 10 )  ? '0' + d  : d;
				// hours
				hh        = ( hh < 10 ) ? '0' + hh : hh;
				// minutes
				mm        = ( mm < 10 ) ? '0' + mm : mm;
				// Now, set the UTC friendly date string
				until     = until.getUTCFullYear() + '' + m + d + 'T235959Z';
				rule += 'UNTIL=' + until + ';';
			}

			var data = {
				action: 'ai1ec_rrule_to_text',
				rrule:  rule
			};
			$( this ).attr( 'disabled', true );
			$.post(
				ajaxurl,
				data,
				function( response ) {
					if( response.error ) {
						if( $( '#ai1ec_is_box_repeat' ).val() == '1' ) {
							ai1ec_repeat_form_error( '#ai1ec_rrule', '#ai1ec_repeat_label', response, $button );
						} else {
							ai1ec_repeat_form_error( '#ai1ec_exrule', '#ai1ec_exclude_label', response, $button );
						}
					} else {
						if( $( '#ai1ec_is_box_repeat' ).val() == '1' ) {
							ai1ec_repeat_form_success( '#ai1ec_rrule', '#ai1ec_repeat_label', '#ai1ec_repeat_text > a', rule, $button, response );
						} else {
							ai1ec_repeat_form_success( '#ai1ec_exrule', '#ai1ec_exclude_label', '#ai1ec_exclude_text > a', rule, $button, response );
						}
					}
				},
				'json'
			);
		});

		// handles click on rrule text
		ai1ec_click_on_ics_rule_text(
			'#ai1ec_repeat_text > a',
			'#ai1ec_repeat',
			'#ai1ec_repeat_label',
			{
				action: 'ai1ec_get_repeat_box',
				repeat: 1,
				post_id: $( '#post_ID' ).val()
			},
			'ai1ec_apply_js_on_repeat_block'
		);

		// handles click on exrule text
		ai1ec_click_on_ics_rule_text(
			'#ai1ec_exclude_text > a',
			'#ai1ec_exclude',
			'#ai1ec_exclude_label',
			{
				action: 'ai1ec_get_repeat_box',
				repeat: 0,
				post_id: $( '#post_ID' ).val()
			},
			'ai1ec_apply_js_on_repeat_block'
		);

		// handles click on repeat checkbox
		ai1ec_click_on_checkbox(
			'#ai1ec_repeat',
			'#ai1ec_repeat_text > a',
			'#ai1ec_repeat_label',
			{
				action: 'ai1ec_get_repeat_box',
				repeat: 1,
				post_id: $( '#post_ID' ).val()
			},
			'ai1ec_apply_js_on_repeat_block'
		);

		// handles click on exclude checkbox
		ai1ec_click_on_checkbox(
			'#ai1ec_exclude',
			'#ai1ec_exclude_text > a',
			'#ai1ec_exclude_label',
			{
				action: 'ai1ec_get_repeat_box',
				repeat: 0,
				post_id: $( '#post_ID' ).val()
			},
			'ai1ec_apply_js_on_repeat_block'
		);

		$( 'a.ai1ec_repeat_cancel' ).live( 'click', function() {
			if( $( '#ai1ec_is_box_repeat' ).val() == '1' ) {
				// handles click on cancel for RRULE
				ai1ec_click_on_modal_cancel( '#ai1ec_repeat_text > a', '#ai1ec_repeat', '#ai1ec_repeat_label' );
			} else {
				// handles click on cancel for EXRULE
				ai1ec_click_on_modal_cancel( '#ai1ec_exclude_text > a', '#ai1ec_exclude', '#ai1ec_exclude_label' );
			}
			$.unblockUI();
			return false;
		});

		$( '#ai1ec_monthly_type_bymonthday, #ai1ec_monthly_type_byday' ).live( 'change', function() {
			$( '#ai1c_repeat_monthly_bymonthday' ).toggle();
			$( '#ai1c_repeat_monthly_byday' ).toggle();
		})
	}

	/**
	 * ICS feeds
	 */

	if( $( '#ai1ec_add_new_ics' ).length )
	{
		/**
		 * Click event handler for + Add new subscription button
		 * checks to see if the feed url is valid url
		 * and makes an ajax call with the feed details
		 */
		$( '#ai1ec_add_new_ics' ).click( function() {
			var $button = $( this );
			var $url = $( '#ai1ec_feed_url' );
			var url = $url.val().replace( 'webcal://', 'http://' );
			var invalid = false;
			var error_message;

			// restore feed url border colors if it has been changed
			$('.ai1ec-feed-url, #ai1ec_feed_url').css( 'border-color', '#DFDFDF' );
			$('#ai1ec-feed-error').remove();

			// Check for duplicates
			$('.ai1ec-feed-url').each( function() {
				if( this.value == url ) {
					// This feed's already been added
					$(this).css( 'border-color', '#FF0000' );
					invalid = true;
					error_message = ai1ec_add_new_event.duplicate_feed_message;
				}
			} );
			// Check for valid URL
			if( ! isUrl( url ) ) {
				invalid = true;
				error_message = ai1ec_add_new_event.invalid_url_message;
			}

			if( invalid ) {
				// color the feed url input field in red and output error message
				$url
					.css( 'border-color', '#FF0000' )
					.focus()
					.before( '<div class="error" id="ai1ec-feed-error"><p>' + error_message + '</p></div>' );
			} else {
				// disable the add button for now
				$button.attr( 'disabled', true );
				// create the data to send
				var data = {
					action: 'ai1ec_add_ics',
					feed_url: url,
					feed_category: $( '#ai1ec_feed_category option:selected' ).val(),
					feed_tags: $( '#ai1ec_feed_tags' ).val()
				};
				// make an ajax call to save the new feed
				$.post(
					ajaxurl,
					data,
					function( response ) {
						// restore add button
						$button.removeAttr( 'disabled' );
						if( response.error ) {
							// tell the user there is an error
							// TODO: Use other method of notification
							alert( response.message );
						} else {
							$url.val( '' );
							// Add the feed to the settings screen
							$( '#ai1ec-feeds-after' ).after( response.message );
						}
					},
					'json'
				);
			}

		});

		/**
		 * Click event handler for X Delete button
		 * that deletes the feed by sending the feed_id via ajax
		 */
		$( '.ai1ec_delete_ics' ).live( 'click', function() {
			// store clicked button for later use
			var $button = $( this );
			// disable the delete button
			$button.attr( 'disabled', true );
			// table row to delete
			var $feed_row = $button.closest( '.ai1ec-feed-container' );
			// get the selected feed id
			var ics_id = $button.siblings( '.ai1ec_feed_id' ).val();
			// create the data to send
			var data = {
				action: 'ai1ec_delete_ics',
				ics_id: ics_id
			};
			// remove the feed from the database
			$.post( ajaxurl, data,
				function( response ) {
					// restore the delete button
					$button.removeAttr( 'disabled' );
					if( response.error ) {
						// tell the user there is an error
						alert( response.message );
					} else {
						// remove the feed from the settings screen
						$feed_row.remove();
					}
				},
				'json'
			);
		});

		/**
		 * Click event handler for Flush events button
		 * that deletes all event posts that came from that feed by sending the feed_id via ajax
		 */
		$( '.ai1ec_flush_ics' ).live( 'click', function() {
			// store clicked button for later use
			var $button = $( this );
			// disable the flush button
			$button.attr( 'disabled', true );
			// get the selected feed id
			var ics_id = $button.siblings( '.ai1ec_feed_id' ).val();
			$button.siblings( '.ajax-loading' ).css( 'visibility', 'visible' );
			// create the data to send
			var data = {
				action: 'ai1ec_flush_ics',
				ics_id: ics_id
			};
			// remove the feed from the database
			$.post( ajaxurl, data,
				function( response ) {
					if( response.error ) {
						// tell the user there is an error
						alert( response.message );
					} else {
						$button.fadeOut();
					}
					$button.siblings( '.ajax-loading' ).css( 'visibility', 'hidden' );
				},
				'json'
			);
		});

		/**
		 * Click event handler for Update events button
		 * that imports events from that feed by sending the feed_id via ajax
		 */
		$( '.ai1ec_update_ics' ).live( 'click', function() {
			// store clicked button for later use
			var $button = $( this );
			// disable the update button
			$button.attr( 'disabled', true );
			// get the selected feed id
			var ics_id = $button.siblings( '.ai1ec_feed_id' ).val();
			$button.siblings( '.ajax-loading' ).css( 'visibility', 'visible' );
			// create the data to send
			var data = {
				action: 'ai1ec_update_ics',
				ics_id: ics_id
			};
			// remove the feed from the database
			$.post( ajaxurl, data,
				function( response ) {
					if( response.error ) {
						// tell the user there is an error
						alert( response.message );
					} else {
						$button.siblings( '.ai1ec_flush_ics' ).remove();
						// If events were imported, create new flush button
						if( response.count )
							$button.after(
								'<input type="button" class="button ai1ec_flush_ics" value="' +
								response.flush_label + '" />' );
					}
					$button
						.attr( 'disabled', false )
						.siblings( '.ajax-loading' ).css( 'visibility', 'hidden' );
				},
				'json'
			);
		});
	}
});
