define(
		[
		 "jquery",
		 "scripts/calendar/print",
		 "scripts/calendar/posterboard_view",
		 "scripts/calendar/agenda_view",
		 "scripts/calendar/month_view",
		 "scripts/calendar/pop_up",
		 "libs/frontend_utils",
		 "ai1ec_calendar",
		 "libs/jquery.tablescroller",
		 "libs/jquery.scrollTo"
		],
		function( $, print_functions, posterboard_view, agenda_view, month_view, pop_up, frontend_utils, ai1ec_calendar ) {
	// Variable storing currently displayed view
	var current_hash = '';
	// An array caching the IDs of all event posts in the currently active view
	var post_ids;
	/**
	 * Function used to update view if user has clicked back/forward in the
	 * browser.
	 */
	var check_hash = function() {
		var live_hash = document.location.hash;
		var default_hash = frontend_utils.ai1ec_convert_entities( ai1ec_calendar.default_hash );
		// If current_hash doesn't match live hash, and the document's live hash
		// isn't empty, or if it is, the current_hash isn't equivalent to empty
		// (i.e., default hash), the page needs to be updated.
		if( current_hash != live_hash &&
		    ( live_hash != '' || current_hash != default_hash ) ) {
			// If hash is empty, resort to original requested action
			var hash = live_hash;
			if( ! hash )
				hash = default_hash;
			load_view( hash );
		}
	};

	// Monitor browser navigation between different URL #hash values
	var interval = setInterval( check_hash, 300 );
	var clear_interval = function() {
		clearInterval( interval );
	};
	/**
	 * function initialize_view
	 *
	 * General initialization function to execute whenever any view is loaded
	 * (this is also called at the end of load_view()).
	 */
	var initialize_view = function() {
		// Make current view actively selected in view dropdown button.
		var classes = $('body').attr( 'class' ).split( ' ' );
		for ( i in classes ) {
			// Extract current view from the body class.
			var matches = /ai1ec-action-([\w]+)/.exec( classes[i] );
			if ( matches != null ) break;
		}
		// Get the dropdown menu link of the active view.
		var $selected_view = $( '#ai1ec-view-' + matches[1] );
		// Replace contents of dropdown button with menu link, plus the caret.
		$( '#ai1ec-current-view' )
			.contents()
				.remove()
				.end()
			.prepend( $selected_view.contents().clone() )
			.append( '<span class="caret"></span>' );
		// Deactivate all dropdown menu items.
		$( '#ai1ec-view-dropdown .dropdown-menu li' ).removeClass( 'active' );
		// Activate only currently selected dropdown menu item.
		$selected_view.parent().addClass( 'active' );

		// Cache the current list of post IDs
		post_ids = new Array();
		$( '.ai1ec-post-id' ).each( function() {
			post_ids.push( this.value );
		} );
		post_ids = post_ids.join();	// Store IDs as comma-separated values

		// Make week view table scrollable
		$( 'table.ai1ec-week-view-original' ).tableScroll( { height: 400, containerClass: 'ai1ec-week-view' } );
		$( 'table.ai1ec-oneday-view-original' ).tableScroll( { height: 400, containerClass: 'ai1ec-oneday-view' } );

		// ===========================
		// = Pop up the active event =
		// ===========================
		if( $( '.ai1ec-active-event:first' ).length ) {
			// Pop up any active event in month/week view views
			$( '.ai1ec-month-view .ai1ec-active-event:first, .ai1ec-oneday-view .ai1ec-active-event:first, .ai1ec-week-view .ai1ec-active-event:first' )
				.each( function() {
				$(this)
					.each( pop_up.show_popup )
					.prev() // .ai1ec-popup
						.data( 'ai1ec_mouseinside', true ); // To keep pop-up from vanishing
			} );
			// Expand any active event in agenda view
			$( '.ai1ec-agenda-view .ai1ec-active-event:first > .ai1ec-event-summary' ).each( agenda_view.expand_event );
			// Bring the active event into focus
			$.scrollTo( '.ai1ec-active-event:first', 1000,
				{
					offset: {
						left: 0,
						top: -window.innerHeight / 2 + 100
					}
				}
			);
		}
		else if( $( '.ai1ec-week-view' ).length || $( '.ai1ec-oneday-view' ).length ) {
			// If no active event, then in week view, scroll down to 6am.
			$( '.ai1ec-oneday-view .tablescroll_wrapper, .ai1ec-week-view .tablescroll_wrapper' ).scrollTo( '.ai1ec-hour-marker:eq(6)' );
		}

		// Apply category/tag filters if any; hide all events by default, then fade
		// in filtered ones.
		if( $('.ai1ec-dropdown.active').length ) {
			$( '.ai1ec-month-view .ai1ec-event-container,' +
			   '.ai1ec-oneday-view .ai1ec-event-container,' +
			   '.ai1ec-week-view .ai1ec-event-container,' +
			   '.ai1ec-agenda-view .ai1ec-event,' +
			   '.ai1ec-posterboard-view .ai1ec-event' ).hide();
			apply_filters();
		}

		// If in month view, extend multiday events.
		if ( $( '.ai1ec-month-view .ai1ec-multiday' ).length ) {
			month_view.revert_multiday_events();
			month_view.extend_multiday_events();
			// Assign class to Month view popups that would overflow the container
			$( '.ai1ec-month-view .ai1ec-week td:first-of-type .ai1ec-popup-summary-wrap' ).addClass( 'ai1ec-popup-summary-right' );
		}

		// If in month view, center modal tooltips vertically
		$( '.ai1ec-popup-summary' ).each( function () {
			// margin offset is the height minus the width of margin / padding
			var margin_offset = -( $( this ).height() / 2 + 13 );
			$(this).css( 'margin-top', margin_offset );
		});

		// If in posterboard view, call masonry on events.
		if ( $( '.ai1ec-posterboard-view' ).length ) {
			posterboard_view.initialize_masonry();
		}

		// Hide the print button if the current view is not supported for printing.
		print_functions.toggle_print_button();
	};
	/**
	 * Checks if the date element in Agenda view contains visible events
	 */
	var has_visible_events = function( $el ) {
		var ret = false;
		$el.find( 'ol.ai1ec-date-events li.ai1ec-event' ).each( function() {
			if( $( this ).css( 'display' ) != 'none' ) ret = true;
		});
		return ret;
	};

	/**
	 * Applies the active category/tag filters to the current view.
	 * (Shows/hides events as necessary.)
	 */
	var apply_filters = function() {
		// Submit the selected term IDs via AJAX and filter the visible list of
		// post IDs. Only include filter selectors that have a selection.
		var selected_ids = new Array();

		var selected_cats =
			$('.ai1ec-filters-container .ai1ec-dropdown.active + #ai1ec-selected-categories').val();
		if( selected_cats ) {
			selected_ids.push( selected_cats );
			selected_cats = '&ai1ec_cat_ids=' + selected_cats;
		} else {
			selected_cats = '';
		}

		var selected_tags =
			$('.ai1ec-filters-container .ai1ec-dropdown.active + #ai1ec-selected-tags').val();
		if( selected_tags ) {
			selected_ids.push( selected_tags );
			selected_tags = '&ai1ec_tag_ids=' + selected_tags;
		} else {
			selected_tags = '';
		}

		selected_ids = selected_ids.join();

		// Modify export URL.
		var export_url = frontend_utils.ai1ec_convert_entities( ai1ec_calendar.export_url );
		if( selected_ids.length ) {
			export_url += selected_cats + selected_tags;
			$( '.ai1ec-subscribe-filtered' ).fadeIn( 'fast' );
		}
		else {
			$( '.ai1ec-subscribe-filtered' ).fadeOut( 'fast' );
		}
		$( '.ai1ec-subscribe' ).attr( 'href', export_url );
		$( '.ai1ec-subscribe-google' ).attr( 'href',
				'http://www.google.com/calendar/render?cid=' + escape( export_url.replace( 'webcal://', 'http://' ) ) );

		var query = {
			'action': 'ai1ec_term_filter',
			'ai1ec_post_ids': post_ids,
			'ai1ec_term_ids': selected_ids
		};

		// Delay loading animation so that it doesn't appear if the AJAX turnover
		// is quick enough
		var $loading = $('#ai1ec-calendar-view-loading')
			.delay( 500 )
			.fadeIn( 'fast' );
		var $view = $('#ai1ec-calendar-view')
			.delay( 500 )
			.fadeTo( 'fast', 0.3 );
		$.post( ai1ec_calendar.ajaxurl, query,
			function( data ) {
				// Cancel loading animation or fade out if faded in.
				$loading.clearQueue().fadeOut( 'fast' );
				$view.clearQueue().fadeTo( 'fast', 1.0 );

				// Show events that should be displayed (or leave them visible).
				var jq_selector = []; // Build our jQuery selector string.
				$.each( data.matching_ids, function( i, val ) {
					jq_selector.push( '#ai1ec-calendar-view .ai1ec-event-id-' + val );
				} );
				$( jq_selector.join() ).css( 'display', 'block' );

				// Hide events that should be hidden (or leave them hidden).
				jq_selector = []; // Build our jQuery selector string.
				$.each( data.unmatching_ids, function( i, val ) {
					jq_selector.push( '#ai1ec-calendar-view .ai1ec-event-id-' + val );
				} );
				$( jq_selector.join() ).css( 'display', 'none' );

				// Agenda view only: Slide up dates for which there are no events, and
				// slide down those for which there are
				$( 'ol.ai1ec-agenda-view > li.ai1ec-date' ).each( function() {
					if( has_visible_events( $(this) ) ) {
						$( this ).slideDown( 'fast' );
					}
					else {
						$( this ).slideUp( 'fast' );
					}
				} );

				// If there are multiday events in this view, revert them and recreate
				// them after filters have been applied.
				if ( $('.ai1ec-month-view .ai1ec-multiday').length ) {
					month_view.revert_multiday_events();
					month_view.extend_multiday_events();
				}

				// If in posterboard view, call masonry on events.
				if ( $( '.ai1ec-posterboard-view' ).length ) {
					posterboard_view.reload_masonry();
				}
			},
			'json'
		);
	};

	/**
	 * Updates the filter dropdowns and clear button based on whether filters
	 * are selected.
	 */
	var update_filter_selectors = function() {
		// Highlight this dropdown as "selected" if and only if any of its terms
		// have been selected.
		$( '.ai1ec-filter-selector-container' ).each( function() {
			if( $( 'li.ai1ec-selected', this ).length ) {
				$( '.ai1ec-dropdown', this ).addClass( 'active btn-info' );
			}
			else {
				$( '.ai1ec-dropdown', this ).removeClass( 'active btn-info' );
			}
		} );

		if( $('.ai1ec-filters-container .ai1ec-selected').length ) {
			$('.ai1ec-clear-filters').fadeIn( 'fast' );
		}
		else {
			$('.ai1ec-clear-filters').fadeOut( 'fast' );
		}

		apply_filters();
	};
	/**
	 * Load a calendar view represented by the given hash value.
	 *
	 * @param {string} hash The hash string requesting a calendar view
	 */
	var load_view = function( hash ) {
		// Reveal loader behind view
		$('#ai1ec-calendar-view-loading').fadeIn( 'fast' );
		$('#ai1ec-calendar-view').fadeTo( 'fast', 0.3,
			// After loader is visible, fetch new content
			function() {
				var query = hash.substring( 1 );
				// Fetch AJAX result
				$.post( ai1ec_calendar.ajaxurl, query, function( data )
					{
						// Replace action body class with new one
						var classes = $('body').attr( 'class' );
						classes = classes.replace( /\s?\bai1ec-[\w-]+\b/g, '' );
						classes += ' ' + data.body_class;
						$('body').attr( 'class', classes );

						// Animate vertical height of container between HTML replacement
						var $container = $('#ai1ec-calendar-view-container');
						$container.height( $container.height() );
						var new_height =
							$('#ai1ec-calendar-view')
								.html( data.html )
								.height();
						$container.animate( { height: new_height }, { complete: function() {
							// Restore height to automatic upon animation completion for
							// proper page layout.
							$container.height( 'auto' );
						} } );

						// Hide loader
						$('#ai1ec-calendar-view-loading').fadeOut( 'fast' );
						$('#ai1ec-calendar-view').fadeTo( 'fast', 1.0 );

						// Do any general view initialization after loading
						initialize_view();
					},
					'json'
				);
			} );

		// Update stored hash
		current_hash = hash;
	};
	// Handle loading the correct view when clicking on a link
	var handle_click_on_link_to_load_view = function() {
		// Load requested view
		load_view( $(this).attr( 'href' ) );
	};
	// Handle clearing filter
	var clear_filters = function() {
		$( '.ai1ec-filter-selector-container li' ).removeClass( 'ai1ec-selected' );
		update_filter_selectors();
	};
	// If there are preselected tag/cat IDs, update the filter UI.
	var initialize_filters = function() {
		// If there are preselected tag/cat IDs, update the filter UI.
		var selected_cats = $( '#ai1ec-selected-categories' ).val(),
		    selected_tags = $( '#ai1ec-selected-tags' ).val();
		if( typeof( selected_cats ) == 'string' && selected_cats != '' ||
		    typeof( selected_tags ) == 'string' && selected_tags != '' ) {
			update_filter_selectors();
		};
	};
	// Handle filtering per tag or category
	var handle_click_on_tag_category = function( e ) {
		var selected_class = 'ai1ec-selected';
		var $container = $( this ).closest( 'div' );
		// Let's see if we are handling tags or categories
		var hidden_input = $container.hasClass( 'ai1ec-category-filter-selector' ) ? '#ai1ec-selected-categories' : '#ai1ec-selected-tags';
		var hidden_data_el = $container.hasClass( 'ai1ec-category-filter-selector' ) ? 'ai1ec-categories' : 'ai1ec-tags';
		var data = [];
		if( $( this ).hasClass( selected_class ) ) {
			// Element deselected, remove class
			$( this ).removeClass( selected_class );
		} else {
			// Element selected, add class
			$( this ).addClass( selected_class );
		}
		$( 'li.' + selected_class, $container ).each( function() {
			var item_val = $( this ).find( 'input[name="' + hidden_data_el + '"]:first' ).val();
			data.push( item_val );
		} );
		$( hidden_input ).val( data.join() );
	};
	// Mark some category / filter as pre-selected if some filters are applied when the page is loaded
	var pre_select_tag_categories_if_set = function( selector, hidden_data_el, hidden_input ) {
		var selected_class = 'ai1ec-selected';
		// Check if hidden input has a preinitialized value
		var initial_val = $( hidden_input ).val();
		if( initial_val != undefined && initial_val != '' ) {
			var data = initial_val.split( ',' );
			// Turn each element of data into a jQuery selector
			$( data ).each( function( i, val ) {
				data[i] = 'input[name="' + hidden_data_el + '"][value="' + val + '"]';
			} );
			// Concatenate data into one long jQuery selector
			data = data.join();
			// Assign the selected_class to all matching elements
			$( selector ).has( data ).addClass( selected_class );
		}
	};
	return {
		initialize_view                   : initialize_view,
		handle_click_on_link_to_load_view : handle_click_on_link_to_load_view,
		update_filter_selectors           : update_filter_selectors,
		clear_filters                     : clear_filters,
		initialize_filters                : initialize_filters,
		handle_click_on_tag_category      : handle_click_on_tag_category,
		pre_select_tag_categories_if_set  : pre_select_tag_categories_if_set,
		// Expose for testin
		clear_interval                    : clear_interval
	};
});
