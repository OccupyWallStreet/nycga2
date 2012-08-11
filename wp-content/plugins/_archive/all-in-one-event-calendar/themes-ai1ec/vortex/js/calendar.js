/**
 * Used to ensure that entities used in L10N strings are correct.
 */
function ai1ec_convert_entities( o ) {
	var c, v;

	c = function( s ) {
		if( /&[^;]+;/.test( s ) ) {
			var e = document.createElement( 'div' );
			e.innerHTML = s;
			return ! e.firstChild ? s : e.firstChild.nodeValue;
		}
		return s;
	}

	if( typeof o === 'string' ) {
		return c( o );
	} else if( typeof o === 'object' ) {
		for( v in o ) {
			if( typeof o[v] === 'string' ) {
				o[v] = c( o[v] );
			}
		}
	}
	return o;
}

/**
 * Get the URL withouth the hash portion.
 */
function get_hashless_url() {
	// Get the full url.
	var url = window.location.href;
	// Get the hash.
	var hash = window.location.hash;
	// If the hash is present, use that, otherwise use the full length
	var index_of_hash = url.indexOf( hash ) || url.length;
	// Cut off the hash.
	var hashless_url = url.substr( 0, index_of_hash );
	return hashless_url;
}

jQuery( document ).ready( function( $ ) {

	// =====================================
	// = Calendar CSS selector replacement =
	// =====================================

	if( ai1ec_calendar.selector != undefined && ai1ec_calendar.selector != '' &&
	    $( ai1ec_calendar.selector ).length == 1 )
	{
		// Try to find an <h#> element containing the title
		var $title = $( ":header:contains(" + ai1ec_calendar.title + "):first" );
		// If none found, create one
		if( ! $title.length ) {
			$title = $( '<h1 class="page-title"></h1>' );
			$title.text( ai1ec_calendar.title ); // Do it this way to automatically generate HTML entities
		}

		var $calendar = $( '#ai1ec-container' )
			.detach()
			.before( $title );

		$( ai1ec_calendar.selector )
			.empty()
			.append( $calendar )
			.hide()
			.css( 'visibility', 'visible' )
			.fadeIn( 'fast' );
	}

	// =================================
	// = General script initialization =
	// =================================

	// Variable storing currently displayed view
	var current_hash = '';
	// An array caching the IDs of all event posts in the currently active view
	var post_ids;

	// Check whether appropriate classes have been added to <body> (some themes
	// don't respect the WP body_class() function). If not, add them, or our app
	// won't function properly.
	var classes = $('body').attr( 'class' );
	if( classes == undefined ) classes = '';
	if( classes.match( /\s?\bai1ec-[\w-]+\b/ ) == null ) {
		// Add action body class(es)
		classes += ' ' + ai1ec_calendar.body_class;
		$('body').attr( 'class', classes );
	}

	/**
	 * Function used to update view if user has clicked back/forward in the
	 * browser.
	 */
	function check_hash() {
		var live_hash = document.location.hash;
		var default_hash = ai1ec_convert_entities( ai1ec_calendar.default_hash );
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
	}

	// Monitor browser navigation between different URL #hash values
	setInterval( check_hash, 300 );

	/**
	 * Load a calendar view represented by the given hash value.
	 *
	 * @param {string} hash The hash string requesting a calendar view
	 */
	function load_view( hash ) {

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
	}

	/**
	 * Show print button in Agenda view, hide otherwise. This is called at the end
	 * of initialize_view();
	 */
	function toggle_print_button() {
		$('#ai1ec-print-button').toggle( $( '.ai1ec-agenda-view' ).length != 0 );
	}

	// Register navigation click handlers
	$('a.ai1ec-load-view').live( 'click', function() {
		// Load requested view
		load_view( $(this).attr( 'href' ) );
	} );
	// Handle the click on the print.
	$('#ai1ec-print-button').click( function( e ) {
		e.preventDefault();
		// Get the url withouth the hash.
		var hashless_url = get_hashless_url();
		// Add the extra parameter in the url, this will trigger the loading of the print CSS.
		var url = hashless_url + "&print=true" + window.location.hash;
		// Open a new page.
		window.open( url, 'Print page' );

	} )

	// *** Month/week views ***

	var popup_zIndex = 999;

	/**
	 * Callback for mouseenter event on .ai1ec-event element
	 */
	function show_popup() {
		var $popup = $(this).prev();
		$popup.removeClass( 'ai1ec-event-popup-allround' );

		if ( $( this.parentNode ).is( '.ai1ec-multiday' ) ) {
			var className = '.' + this.parentNode.className.replace( ' ai1ec-multiday-bar', '' ).replace( /\s+/igm, '.' );
			var bars = $( className );

			bars.each( function( i ) {
				var $p = $( '.ai1ec-event-popup', bars[i] );
				if ( $popup[0] != $p[0] ) {
					$p.addClass( "ai1ec-event-popup-allround" )
						.css( 'visibility', 'visible' )
						.fadeIn( 100);
				}
			});
		}

		// If not already done, position popup so that it does not exceed
		// right/left bounds of container.
		if( ! $popup.data( 'ai1ec_offset' ) ) {
			// Keep popup hidden but positionable
			$popup.css( 'visibility', 'hidden' ).show();

			var $container = $('#ai1ec-calendar-view-container');
			var popup_width = $popup.width();
			var popup_offset = $popup.offset();
			var container_offset = $container.offset();
			var container_x2 = container_offset.left + $container.width();
			var $summary = $( '.ai1ec-event-summary', $popup );

			// Respect right-side bounds
			if( popup_offset.left + popup_width > container_x2 )
				$popup.offset( { left: container_x2 - popup_width, top: popup_offset.top } );

			// Respect left-side bounds
			if( $summary.offset().left < container_offset.left )
				$popup.addClass( 'ai1ec-shifted-right' );

			/* From Alex - find out if we still need this:
			if ( $( this.parentNode ).hasClass("ai1ec-multiday") &&
				 $summary.offset().left + $summary.width() > container_x2) {
				$popup.removeClass( 'ai1ec-shifted-right' );
			}
			*/

			// Restore popup to 'display: none'.
			$popup.hide().css( 'visibility', 'visible' );
			// Flag the object so we don't calculate twice.
			$popup.data( 'ai1ec_offset', true );
		}

		// Display popup.
		$popup
			.css({ 'z-index': ++popup_zIndex })
			.fadeIn( 100,
				// Don't handle special case in touch environment (unneeded, interferes)
				Modernizr.touch
				?	null
				:	function() {
					// Special case - check if the mouse cursor is still in the pop-up.
					if( ! $(this).data( 'ai1ec_mouseinside' ) ) {
						$(this).each( hide_popup );
					}
				}
			);

		// If in touch environment, hide any previously popped up events (required
		// since we don't use mouseleave event for this in touch environments) and
		// then return false from "click" handler to prevent following of link.
		if( Modernizr.touch ) {
			$('.ai1ec-month-view .ai1ec-event-popup, .ai1ec-week-view .ai1ec-event-popup')
				.not( $popup )
				.fadeOut( 100, function() { $(this).parent().css( { zIndex: 'auto' } ); } );
			return false;
		}
	}
	function hide_popup() {
		var className = '.' + this.parentNode.className.replace( ' ai1ec-multiday-bar', '' ).replace( /\s+/igm, '.' );
		var bars = $( className + ' .ai1ec-event-popup' );
		$(bars)
			.fadeOut( 100, function() { $(this).parent().css( { zIndex: 'auto' } ); } )
			.data( 'ai1ec_mouseinside', false );
	}

	// Register popup click (for touch devices) or hover (for non-touch devices)
	// handlers for month/week views
	$('.ai1ec-month-view .ai1ec-event, .ai1ec-oneday-view .ai1ec-event, .ai1ec-week-view .ai1ec-event')
		.live( Modernizr.touch ? 'click' : 'mouseenter', show_popup );
	// Only hide popups on mouseleave for mouse-based devices (doesn't work
	// properly in touch-based devices for some reason).
	if( ! Modernizr.touch ) {
		$('.ai1ec-month-view .ai1ec-event-popup, .ai1ec-oneday-view .ai1ec-event-popup, .ai1ec-week-view .ai1ec-event-popup')
			.live( 'mouseleave', hide_popup )
			.live( 'mousemove', function() {
				// Track whether popup contains mouse cursor
				$(this).data( 'ai1ec_mouseinside', true );
			} );
	}
	// Hide any popups that were visible when the window lost focus
	if( $('.ai1ec-month-view, .ai1ec-oneday-view, .ai1ec-week-view').length ) {
		$(window).blur( function() {
			$('.ai1ec-event-popup:visible').each( hide_popup );
		} );
	}

	// ======================================
	// = Week / Day view hover-raise effect =
	// ======================================
	$( '.ai1ec-oneday-view .ai1ec-oneday a.ai1ec-event-container, .ai1ec-week-view .ai1ec-week a.ai1ec-event-container' )
		.live( 'mouseenter',
			function() {
				$(this).delay( 500 ).queue( function() { $(this).css( 'z-index', 5 ) } );
			} )
		.live( 'mouseleave',
			function() {
				$(this).clearQueue().css( 'z-index', 'auto' );
			} );

	/**
	 * Trims date boxes for which there are too many listed events.
	 */
	function truncate_month_view()
	{
		if( $( '.ai1ec-month-view' ).length )
		{
			// First undo any previous truncation
			revert_dropdowns();

			// Now set up truncation on any days with max visible events.
			$( '.ai1ec-month-view .ai1ec-day' ).each( function()
			{
				var max_visible = 5;
				var maxVisibleHeight = 5 * 16;
				var addDropdownContainer = -1;
				var $events = $( '.ai1ec-event-container:visible', this );

				$events.each( function( i ) {
					if ( this.offsetTop >= maxVisibleHeight && addDropdownContainer == -1 ) {
						addDropdownContainer = ( i > 1 ? i - 1 : 0 );
					 }

				});

				if ( addDropdownContainer != -1 ) {
					var container = document.createElement("div");
					container.className = "ai1ec-event-dropdown";

					$( container ).css({
						top: $events[addDropdownContainer].offsetTop,
						display: "none"
					});
					for ( var i = addDropdownContainer; i < $events.length; i++ ) {
						// Need to reset styles for events in dropdown.
						revert_multiday_bar( $events[i] );

						// Add an arrow for multiday events.
						if ( $( $events[i] ).hasClass( "ai1ec-multiday" ) ) {
							$( $events[i] ).append( create_multiday_arrow( 1, $events[i].style.backgroundColor ) );
						}
						$( container ).append( $events[i] );
					}

					// Scroll down button, and register mousedown.
					$scroll_down = $( '<a href="#" class="ai1ec-scroll-down"></a>' );
					$scroll_down.bind( 'hover click', function () {
						$( container ).fadeIn( 100 );
						return false;
					});

					var $date = $( this ).find( ".ai1ec-date" );
					if ( parseInt( $date.css( "marginBottom" ) ) > maxVisibleHeight ) {
						$date.css({ marginBottom: maxVisibleHeight - 15 + "px" });
						$( container ).css({
							top: maxVisibleHeight + "px"
						})
					}
					$( this ).append(container);
					$( this ).append($scroll_down);

					// Need additional button to close dropdown on touch devices
					if ( Modernizr.touch ) {
						// Scroll down button, and register mousedown
						$scroll_up = $( '<a href="#" class="ai1ec-scroll-up"></a>' );
						$scroll_up.bind("click", function () {
							$( container ).fadeOut( 100 );
							return false;
						})
						$( container ).append($scroll_up);
					} else {
						$( container ).bind( 'mouseleave' ,function() {
							$( this ).fadeOut( 100 );
						})
					}
				}
			});
		}
	}

	// *** Month view ***

	/**
	 * Extends day bars for multiday events.
	 */
	function extend_multiday_events() {

		// First undo any previous multiday bars
		revert_multiday_events();

		var $days = $('.ai1ec-day');
		$('.ai1ec-month-view .ai1ec-multiday:visible').each( function() {
			var container = this.parentNode;

			var elHeight = $(this).outerHeight( true );
			var elWidth = $(container).width() + 1;

			var $elTitle = $( '.ai1ec-event-title', this ).first();

			var endDay = $(this).data( 'endDay' );

			var $startEl = $( '.ai1ec-date', container );
			var startDay = $startEl.html();

			var nextMonthBar = $( this ).data( 'endTruncated' );
			if ( nextMonthBar ) {
				endDay = $( '.ai1ec-date', $days[$days.length - 1] ).html();
			}

			var $evtContainer = $(this);
			var bgColor = $( '.ai1ec-event', $evtContainer )[0].style.backgroundColor;
			var curLine = 0;
			var deltaDays = endDay - startDay + 1;
			var daysLeft = deltaDays;
			var lineWidth = 0;

			$days.each( function( i ) {
				var $dayEl = $( '.ai1ec-date', this );
				var $td = $( this.parentNode );
				cellNum = $td.index();

				var day = parseInt( $dayEl.html() );
				if ( day >= startDay && day <= endDay ) {

					if ( day == startDay ) {
						marginSize = parseInt( $dayEl.css( 'marginBottom' ) ) + 16;
					}
					if ( cellNum != 0 ) {
						// Extend initial event bar to the end of first (!) week.
						if ( curLine == 0 ) {
							lineWidth = $td.offset().left + $td.width() - $(container).offset().left;
						}
					}
					else if ( day > startDay && daysLeft != 0 ) {
						var $block = $evtContainer.clone(false);
						$dayEl.parent().append($block);

						// Create a new spanning multiday bar. "ai1ec-multiday-bar" is used
						// for proper styling, while "ai1ec-multiday-clone" identifies the
						// clones so they can be removed when required.
						$block.addClass( 'ai1ec-multiday-bar ai1ec-multiday-clone' );

						$block
							.css({
								position: "absolute",
								left: '1px',
								top: parseInt( $dayEl.css( 'marginBottom' ) ) + 13, // line height is 16px - 3px of initial margin
								backgroundColor: bgColor
							});

						var j = ( daysLeft > 7 ? i + 7 - 1 : i + daysLeft - 1 );
						var w = ( $( $days[j] ).offset().left + $( $days[j] ).width() - $($days[j].parentNode.parentNode).offset().left);

						$block.css( 'width', w - 3 );

						if ( daysLeft > 7 ) {
							$block.append( create_multiday_arrow( 1, bgColor ));
						}

						$block.append( create_multiday_arrow( 2, bgColor ));
						curLine++;
					}

					// Keep constant margin (number of bars) during the first row.
					if ( curLine == 0 ) {
						$dayEl.css({ 'marginBottom': marginSize + 'px' });
					}
					// But need to reset it and append margins from the begining for
					// subsequent weeks.
					else {
						$dayEl.css({ 'marginBottom': '+=16px' });
					}

					daysLeft--;
				}
			});
			// Adding "start arrow" to the end of multi-month bars.
			if ( nextMonthBar ) {
				var $lastBarPiece = $( '.' + $evtContainer[0].className.replace( /\s+/igm, '.' ) ).last();
				$lastBarPiece.append( create_multiday_arrow( 1, bgColor ));
			}

			$(this).css({
				position: 'absolute',
				top: $startEl.outerHeight( true ) - elHeight - 1 + 'px',
				left: '1px',
				width: lineWidth + 'px'
			});

			// Add an ending arrow to the initial event bar for multi-week events.
			if ( curLine > 0 ) {
				$(this).append( create_multiday_arrow( 1, bgColor ) );
			}
			// Add a starting arrow to the initial event bar for events starting in
			// previous month.
			if ( $(this).data( 'startTruncated' ) ) {
				$(this)
					.append( create_multiday_arrow( 2, bgColor ) )
					.addClass( 'ai1ec-multiday-bar' );
			}
		});
	}

	/**
	 * Creates arrow for multiday bars.
	 *
	 * @param {int}    type  1 for ending arrow, 2 for starting arrow
	 * @param {string} color Color of the multiday event
	 */
	function create_multiday_arrow( type, color ) {
		var $arrow = $( '<div class="ai1ec-multiday-arrow' + type + '"></div>' );
		if ( type == 1 ) {
			$arrow.css({ borderLeftColor: color });
		} else {
			$arrow.css({ borderTopColor: color, borderRightColor: color, borderBottomColor: color });
		}
		return $arrow;
	}


	/**
	 * Reverts styling added for multiday bars. Needed to apply filters.
	 */
	function revert_multiday_events() {
		var $view = $( '.ai1ec-month-view' );
		$( '.ai1ec-date', $view ).each(function() {
			$(this).css({ marginBottom: "1px" });
		});

		$( '.ai1ec-multiday-clone', $view ).remove();

		$( '.ai1ec-multiday', $view ).each( function() {
			revert_multiday_bar( this );
		});
	}

	/**
	 * Reverts first multibar of a multiday event to its initial appearance.
	 */
	function revert_multiday_bar( bar ) {
		$( bar ).css({
			position: 'relative',
			left: 'auto',
			top: 'auto',
			width: 'auto'
		});

		var weekBarsSelector = '.' + bar.className.replace( /\s+/igm, '.' ) + '.ai1ec-multiday-clone';
		$( weekBarsSelector ).remove();

		$( '.ai1ec-multiday-arrow1, ai1ec-multiday-arrow2', bar ).remove();
	}

	/**
	 * Reverts dropdowns added to day cells. Needed to apply filters.
	 */
	function revert_dropdowns() {
		$(".ai1ec-month-view .ai1ec-event-dropdown").each( function() {
			var container = this.parentNode;
			$(this).find(".ai1ec-event-container").each( function () {
				container.appendChild(this);
			})
		});
		$(".ai1ec-month-view .ai1ec-event-dropdown").remove();
		$(".ai1ec-month-view .ai1ec-scroll-down").remove();
	}

	// *** Agenda view ***

	/**
	 * Callbacks for event expansion, collapse.
	 */
	function expand_event() {
		$( this )	// ...-click block
			.hide()
			.parent() // event block
				.addClass( 'ai1ec-expanded' )
				.end()
			.prev()	// summary block
				.show()
				.find( '.ai1ec-event-description' ) // description block
					.hide()
					.slideDown( 'fast' );
	}
	function collapse_event() {
		$(this) // inner ...-click block
			.next() // description block
			.slideUp( 'fast', function() {
				$(this).parent() // summary block
					.parent() // event block
						.removeClass( 'ai1ec-expanded' )
						.end()
					.hide() // summary block again
						.next() // original ...-click block
						.show();
				}
			);
	}

	// Register click handlers for event title
	$('.ai1ec-agenda-view .ai1ec-event > .ai1ec-event-click')
		.live( 'click', expand_event );
	$('.ai1ec-agenda-view .ai1ec-event-summary > .ai1ec-event-click')
		.live( 'click', collapse_event );

	// Register click handlers for expand/collapse all buttons
	$('.ai1ec-action-agenda #ai1ec-expand-all').live( 'click', function() {
		$('.ai1ec-event > .ai1ec-event-click:visible').click();
	} );
	$('.ai1ec-action-agenda #ai1ec-collapse-all').live( 'click', function() {
		$('.ai1ec-event-summary > .ai1ec-event-click:visible').click();
	} );

	// *** All views ***

	// ========================
	// = Category/tag filters =
	// ========================

	element_selector(
			'.ai1ec-category-filter-selector li',
			'ai1ec-selected',
			'ai1ec-categories',
			'#ai1ec-selected-categories' );
	element_selector(
			'.ai1ec-tag-filter-selector li',
			'ai1ec-selected',
			'ai1ec-tags',
			'#ai1ec-selected-tags' );

	// ==================================
	// = Category/tag filtering actions =
	// ==================================

	/**
	 * Checks if the date element in Agenda view contains visible events
	 */
	function has_visible_events( $el ) {
		var ret = false;
		$el.find( 'ol.ai1ec-date-events li.ai1ec-event' ).each( function() {
			if( $( this ).css( 'display' ) != 'none' ) ret = true;
		});
		return ret;
	}

	/**
	 * Applies the active category/tag filters to the current view.
	 * (Shows/hides events as necessary.)
	 */
	function apply_filters() {
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
		var export_url = ai1ec_convert_entities( ai1ec_calendar.export_url );
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
					revert_multiday_events();
					extend_multiday_events();
				}
			},
			'json'
		);
	}

	/**
	 * Updates the filter dropdowns and clear button based on whether filters
	 * are selected.
	 */
	function update_filter_selectors() {
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
	}

	$('.ai1ec-filter-selector li').click( update_filter_selectors );

	$('.ai1ec-clear-filters').click( function() {
		$('.ai1ec-filter-selector-container li').removeClass( 'ai1ec-selected' );
		update_filter_selectors();
	} );

	/**
	 * function initialize_view
	 *
	 * General initialization function to execute whenever any view is loaded
	 * (this is also called at the end of load_view()).
	 */
	function initialize_view() {
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
					.each( show_popup )
					.prev() // .ai1ec-popup
						.data( 'ai1ec_mouseinside', true ); // To keep pop-up from vanishing
			} );
			// Expand any active event in agenda view
			$( '.ai1ec-agenda-view .ai1ec-active-event:first > .ai1ec-event-click' ).each( expand_event );
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
			$('.ai1ec-month-view .ai1ec-event-container, .ai1ec-oneday-view .ai1ec-event-container, .ai1ec-week-view .ai1ec-event-container, .ai1ec-agenda-view .ai1ec-event').hide();
			apply_filters();
		}

		// If in month view, extend multiday events.
		if ( $('.ai1ec-month-view .ai1ec-multiday').length ) {
			revert_multiday_events();
			extend_multiday_events();
		}

		// Hide the print button if the current view is not supported for printing.
		toggle_print_button();
	}

	// If there are preselected tag/cat IDs, update the filter UI.
	var selected_cats = $('#ai1ec-selected-categories').val(),
	    selected_tags = $('#ai1ec-selected-tags').val();
	if( typeof( selected_cats ) == 'string' && selected_cats != '' ||
	    typeof( selected_tags ) == 'string' && selected_tags != '' ) {
		update_filter_selectors();
	}

	initialize_view();
} );
