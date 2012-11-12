/**
 * This module defines the funcitonality to show / hide pop_ups and handle all edge cases
 */
define(
		[
		 "jquery",
		 "libs/modernizr"
		 ],
		 function( $, Modernizr ) {
	// *** Month/week views ***

	var popup_zIndex = 999;

	/**
	 * Callback for mouseenter event on .ai1ec-event element
	 */
	var show_popup = function() {
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
	};
	var hide_popup = function() {
		var className = '.' + this.parentNode.className.replace( ' ai1ec-multiday-bar', '' ).replace( /\s+/igm, '.' );
		var bars = $( className + ' .ai1ec-event-popup' );
		$(bars)
			.fadeOut( 100, function() { $(this).parent().css( { zIndex: 'auto' } ); } )
			.data( 'ai1ec_mouseinside', false );
	};
	// Hide any popups that were visible when the window lost focus
	var hide_popup_on_windows_blur_event = function() {
		if( $('.ai1ec-month-view, .ai1ec-oneday-view, .ai1ec-week-view').length ) {
			$( '.ai1ec-event-popup:visible' ).each( hide_popup );
		}
	};
	// Set data so we can track it later
	var set_data_on_active_popup = function() {
		// Track whether popup contains mouse cursor
		$( this ).data( 'ai1ec_mouseinside', true );
	};
	return {
		hide_popup                       : hide_popup,
		show_popup                       : show_popup,
		hide_popup_on_windows_blur_event : hide_popup_on_windows_blur_event,
		set_data_on_active_popup         : set_data_on_active_popup
	};
} );
