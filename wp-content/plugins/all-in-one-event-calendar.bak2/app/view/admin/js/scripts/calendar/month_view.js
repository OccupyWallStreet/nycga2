define(
		[
		 "jquery",
		 "libs/modernizr"
		 ],
		 function( $, Modernizr ) {
	// *** Month view ***

	/**
	 * Extends day bars for multiday events.
	 */
	var extend_multiday_events = function() {

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
	};

	/**
	 * Creates arrow for multiday bars.
	 *
	 * @param {int}    type  1 for ending arrow, 2 for starting arrow
	 * @param {string} color Color of the multiday event
	 */
	var create_multiday_arrow = function( type, color ) {
		var $arrow = $( '<div class="ai1ec-multiday-arrow' + type + '"></div>' );
		if ( type == 1 ) {
			$arrow.css({ borderLeftColor: color });
		} else {
			$arrow.css({ borderTopColor: color, borderRightColor: color, borderBottomColor: color });
		}
		return $arrow;
	};


	/**
	 * Reverts styling added for multiday bars. Needed to apply filters.
	 */
	var revert_multiday_events = function() {
		var $view = $( '.ai1ec-month-view' );
		$( '.ai1ec-date', $view ).each(function() {
			$(this).css({ marginBottom: "1px" });
		});

		$( '.ai1ec-multiday-clone', $view ).remove();

		$( '.ai1ec-multiday', $view ).each( function() {
			revert_multiday_bar( this );
		});
	};

	/**
	 * Reverts first multibar of a multiday event to its initial appearance.
	 */
	var revert_multiday_bar = function( bar ) {
		$( bar ).css({
			position: 'relative',
			left: 'auto',
			top: 'auto',
			width: 'auto'
		});

		var weekBarsSelector = '.' + bar.className.replace( /\s+/igm, '.' ) + '.ai1ec-multiday-clone';
		$( weekBarsSelector ).remove();

		$( '.ai1ec-multiday-arrow1, ai1ec-multiday-arrow2', bar ).remove();
	};

	/**
	 * Reverts dropdowns added to day cells. Needed to apply filters.
	 */
	var revert_dropdowns = function() {
		$(".ai1ec-month-view .ai1ec-event-dropdown").each( function() {
			var container = this.parentNode;
			$(this).find(".ai1ec-event-container").each( function () {
				container.appendChild(this);
			});
		});
		$(".ai1ec-month-view .ai1ec-event-dropdown").remove();
		$(".ai1ec-month-view .ai1ec-scroll-down").remove();
	};
	/**
	 * Trims date boxes for which there are too many listed events. ( NOT USED );
	 */
	var truncate_month_view = function()
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
						});
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
						});
						$( container ).append($scroll_up);
					} else {
						$( container ).bind( 'mouseleave' ,function() {
							$( this ).fadeOut( 100 );
						});
					}
				}
			});
		}
	};
	return {
		revert_multiday_events : revert_multiday_events,
		extend_multiday_events : extend_multiday_events
	};

} );
