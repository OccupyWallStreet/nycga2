define( 
		[
		 "jquery",
		 "ai1ec_config",
		 "libs/utils"
		 ],
		 function( $, ai1ec_config, AI1EC_UTILS ) {
	var ajaxurl = AI1EC_UTILS.get_ajax_url();
	var hide_all_repeat_fields = function() {
		hide_all_end_fields();
		hide_custom_repeat_elements();
		hide_frequency();
		$( '#ai1ec_end_holder' ).fadeOut();
	};

	var hide_all_end_fields = function() {
		$( '#ai1ec_count_holder, #ai1ec_until_holder' ).hide();
	};

	var ai1ec_repeat_form_success = function( s1, s2, s3, rule, button, response ) {
		$( s1 ).val( rule );
		$.unblockUI();
		var txt = $.trim( $( s2 ).text() );
		if( txt.lastIndexOf( ':' ) == -1 ) {
			txt = txt.substring( 0, txt.length - 3 );
			$( s2 ).text( txt + ':' );
		}
		$(button).attr( 'disabled', false );
		$( s3 ).fadeOut( 'fast', function() {
			$( this ).text( response.message );
			$( this ).fadeIn( 'fast' );
		});
	};

	var ai1ec_repeat_form_error = function( s1, s2, response, button ) {
		$.growlUI( 'Error', response.message );
		$( button ).attr( 'disabled', false );
		$( s1 ).val( '' );
		var txt = $.trim( $( s2 ).text() );
		if( txt.lastIndexOf( '...' ) == -1 ) {
			txt = txt.substring( 0, txt.length - 1 );
			$( s2 ).text( txt + '...' );
		}
	};

	var ai1ec_click_on_ics_rule_text = function( s1, s2, s3, data, fn ) {
		$( window ).on( 'click', s1, function() {
			if( ! $( s2 ).is( ':checked' ) ) {
				$( s2 ).attr( 'checked', true );
				var txt = $.trim( $( s3 ).text() );
				txt = txt.substring( 0, txt.length - 3 );
				$( s3 ).text( txt + ':' );
			}
			ai1ec_show_repeat_tabs( data, fn );
			return false;
		});
	};

	var ai1ec_click_on_checkbox = function( s1, s2, s3, data, fn ) {
		$( s1 ).click( function() {
			if( $(this).is( ':checked' ) ) {
				ai1ec_show_repeat_tabs( data, fn );
			} else {
				$( s2 ).text( '' );
				var txt = $.trim( $( s3 ).text() );
				txt = txt.substring( 0, txt.length - 1 );
				$( s3 ).text( txt + '...' );
			}
		});
	};

	var ai1ec_click_on_modal_cancel = function( s1, s2, s3 ) {
		if( $.trim( $( s1 ).text() ) == '' ) {
			$( s2 ).removeAttr( 'checked' );
			var txt = $.trim( $( s3 ).text() );
			if( txt.lastIndexOf( '...' ) == -1 ) {
				txt = txt.substring( 0, txt.length - 1 );
				$( s3 ).text( txt + '...' );
			}
		}
	};

	// called after the repeat block is inserted in the DOM
	var ai1ec_apply_js_on_repeat_block = function() {
		// Initialize count range slider
		$( '#ai1ec_count, #ai1ec_daily_count, #ai1ec_weekly_count, #ai1ec_monthly_count, #ai1ec_yearly_count' ).rangeinput( {
			css: {
				input: 'ai1ec-range',
				slider: 'ai1ec-slider',
				progress: 'ai1ec-progress',
				handle: 'ai1ec-handle'
			}
		} );
		// Initialize inputdate plugin on our "until" date input.
		data = {
			start_date_input : '#ai1ec_until-date-input',
			start_time       : '#ai1ec_until-time',
			date_format      : ai1ec_config.date_format,
			month_names      : ai1ec_config.month_names,
			day_names        : ai1ec_config.day_names,
			week_start_day   : ai1ec_config.week_start_day,
			twentyfour_hour  : ai1ec_config.twentyfour_hour,
			now              : new Date( ai1ec_config.now * 1000 )
		};
		$.inputdate( data );
	};

	var ai1ec_show_repeat_tabs = function( data, post_ajax_func ) {
		$.blockUI( {
			message: '<div class="ai1ec-repeat-box-loading"></div>',
			css: {
				width: '358px',
				border: '0',
				background: 'transparent',
				cursor: 'normal'
			}
		} );
		$.post(
			ajaxurl,
			data,
			function( response ) {
				if( response.error ) {
					// tell the user there is an error
					// TODO: Use other method of notification
					alert( response.message );
					$.unblockUI();
				} else {
					// display the form
					$.blockUI( {
						message: response.message,
						css: {
							width: '358px',
							border: '0',
							background: 'transparent',
							cursor: 'normal'
						}
					});
					if( typeof post_ajax_func === 'function' ) {
						post_ajax_func();
					}
				}
			},
			'json'
		);
	};
	return {
		ai1ec_show_repeat_tabs         : ai1ec_show_repeat_tabs,
		ai1ec_apply_js_on_repeat_block : ai1ec_apply_js_on_repeat_block,
		ai1ec_click_on_modal_cancel    : ai1ec_click_on_modal_cancel,
		ai1ec_click_on_checkbox        : ai1ec_click_on_checkbox,
		ai1ec_click_on_ics_rule_text   : ai1ec_click_on_ics_rule_text,
		ai1ec_repeat_form_error        : ai1ec_repeat_form_error,
		ai1ec_repeat_form_success      : ai1ec_repeat_form_success,
		hide_all_end_fields            : hide_all_end_fields,
		hide_all_repeat_fields         : hide_all_repeat_fields
	};
		} );