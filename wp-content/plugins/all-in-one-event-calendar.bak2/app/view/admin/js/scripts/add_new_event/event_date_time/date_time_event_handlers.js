define( 
		[
		 "jquery",
		 'ai1ec_config',
		 'scripts/add_new_event/event_date_time/date_time_utility_functions',
		 'libs/jquery.calendrical_timespan',
		 "libs/utils"
		 ],
		 function( $, ai1ec_config, date_time_utility_functions, calendrical_functions, AI1EC_UTILS ) {
	var ajaxurl = AI1EC_UTILS.get_ajax_url();
	/**
	 * Show/hide elements that show selectors for ending until/after events
	 */
	var show_end_fields = function() {
		var selected = $( '#ai1ec_end option:selected' ).val();
		switch( selected ) {
			// Never selected, hide end fields
			case '0':
				date_time_utility_functions.hide_all_end_fields();
				break;
			// After selected
			case '1':
				if( $( '#ai1ec_count_holder' ).css( 'display' ) == 'none' ) {
					date_time_utility_functions.hide_all_end_fields();
					$( '#ai1ec_count_holder' ).fadeIn();
				}
				break;
			// On date selected
			case '2':
				if( $( '#ai1ec_until_holder' ).css( 'display' ) == 'none' ) {
					date_time_utility_functions.hide_all_end_fields();
					$( '#ai1ec_until_holder' ).fadeIn();
				}
				break;
		}
	};
	var trigger_publish = function() {
		$( '#publish' ).trigger( 'click' );
	};
	// Handles clicks on the tabs when the modal is open
	var handle_click_on_tab_modal = function( e ) {
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
	};
	// Handle click on the Apply button
	var handle_click_on_apply_button = function( e ) {
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
			var until = calendrical_functions.parseDate( $( '#ai1ec_until-date-input' ).val(), ai1ec_config.date_format );
			// Take the starting date to set hour and minute
			var start = new Date( calendrical_functions.parseDate( $( '#ai1ec_start-time' ).val(), ai1ec_config.date_format ) );
			// Get UTC Day and UTC Month, and then add leading zeroes if required
			var d     = until.getUTCDate();
			var m     = until.getUTCMonth() + 1;
			var hh    = start.getUTCHours();
			var mm    = start.getUTCMinutes();

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
						date_time_utility_functions.ai1ec_repeat_form_error( '#ai1ec_rrule', '#ai1ec_repeat_label', response, $button );
					} else {
						date_time_utility_functions.ai1ec_repeat_form_error( '#ai1ec_exrule', '#ai1ec_exclude_label', response, $button );
					}
				} else {
					if( $( '#ai1ec_is_box_repeat' ).val() == '1' ) {
						date_time_utility_functions.ai1ec_repeat_form_success( '#ai1ec_rrule', '#ai1ec_repeat_label', '#ai1ec_repeat_text > a', rule, $button, response );
					} else {
						date_time_utility_functions.ai1ec_repeat_form_success( '#ai1ec_exrule', '#ai1ec_exclude_label', '#ai1ec_exclude_text > a', rule, $button, response );
					}
				}
			},
			'json'
		);
	};
	// Handle clicking on cancel button
	var handle_click_on_cancel_modal = function( e ) {
		if( $( '#ai1ec_is_box_repeat' ).val() == '1' ) {
			// handles click on cancel for RRULE
			date_time_utility_functions.ai1ec_click_on_modal_cancel( '#ai1ec_repeat_text > a', '#ai1ec_repeat', '#ai1ec_repeat_label' );
		} else {
			// handles click on cancel for EXRULE
			date_time_utility_functions.ai1ec_click_on_modal_cancel( '#ai1ec_exclude_text > a', '#ai1ec_exclude', '#ai1ec_exclude_label' );
		}
		$.unblockUI();
		return false;
	};
	// Handle clicking on the two checkboxes in the monthly tab
	var handle_checkbox_monthly_tab_modal = function( e ) {
		$( '#ai1c_repeat_monthly_bymonthday' ).toggle();
		$( '#ai1c_repeat_monthly_byday' ).toggle();
	};
	var handle_click_on_day_month_in_modal = function( e ) {
		var $this = $( e.target );
		if( $this.hasClass( 'ai1ec_selected' ) ) {
			$this.removeClass( 'ai1ec_selected' );
		} else {
			$this.addClass( 'ai1ec_selected' );
		}
		var data = [];
		var $ul = $this.closest( 'ul' ); 
		$( 'li', $ul ).each( function( i, el ) {
			if( $( el ).hasClass( 'ai1ec_selected' ) ) {
				var value = $( el ).children( 'input[type="hidden"]:first' ).val();
				data.push( value );
			}
		});
		$ul.next().val( data.join() );
	};
	// This are pseudo handlers, they might require a refactoring sooner or later
	var execute_pseudo_handlers = function() {
		// handles click on rrule text
		date_time_utility_functions.ai1ec_click_on_ics_rule_text(
			'#ai1ec_repeat_text > a',
			'#ai1ec_repeat',
			'#ai1ec_repeat_label',
			{
				action: 'ai1ec_get_repeat_box',
				repeat: 1,
				post_id: $( '#post_ID' ).val()
			},
			date_time_utility_functions.ai1ec_apply_js_on_repeat_block
		);
		// handles click on exrule text
		date_time_utility_functions.ai1ec_click_on_ics_rule_text(
			'#ai1ec_exclude_text > a',
			'#ai1ec_exclude',
			'#ai1ec_exclude_label',
			{
				action: 'ai1ec_get_repeat_box',
				repeat: 0,
				post_id: $( '#post_ID' ).val()
			},
			date_time_utility_functions.ai1ec_apply_js_on_repeat_block
		);

		// handles click on repeat checkbox
		date_time_utility_functions.ai1ec_click_on_checkbox(
			'#ai1ec_repeat',
			'#ai1ec_repeat_text > a',
			'#ai1ec_repeat_label',
			{
				action: 'ai1ec_get_repeat_box',
				repeat: 1,
				post_id: $( '#post_ID' ).val()
			},
			date_time_utility_functions.ai1ec_apply_js_on_repeat_block
		);

		// handles click on exclude checkbox
		date_time_utility_functions.ai1ec_click_on_checkbox(
			'#ai1ec_exclude',
			'#ai1ec_exclude_text > a',
			'#ai1ec_exclude_label',
			{
				action: 'ai1ec_get_repeat_box',
				repeat: 0,
				post_id: $( '#post_ID' ).val()
			},
			date_time_utility_functions.ai1ec_apply_js_on_repeat_block
		);
	};
	var handle_animation_of_calendar_widget = function( e ) {
		// On the first run it will be undefined, so we set it to false
		var state = $( this ).data( 'state' ) === undefined ? false : $( this ).data( 'state' );
		$('#widgetCalendar').stop().animate( { height: state ? 0 : $( '#widgetCalendar div.datepicker' ).get( 0 ).offsetHeight }, 500 );
		$( this ).data( 'state', ! state );
		return false;
	};
	return {
		show_end_fields                     : show_end_fields,
		trigger_publish                     : trigger_publish,
		handle_click_on_tab_modal           : handle_click_on_tab_modal,
		handle_click_on_apply_button        : handle_click_on_apply_button,
		handle_click_on_cancel_modal        : handle_click_on_cancel_modal,
		handle_checkbox_monthly_tab_modal   : handle_checkbox_monthly_tab_modal,
		execute_pseudo_handlers             : execute_pseudo_handlers,
		handle_animation_of_calendar_widget : handle_animation_of_calendar_widget,
		handle_click_on_day_month_in_modal  : handle_click_on_day_month_in_modal
	};
} );