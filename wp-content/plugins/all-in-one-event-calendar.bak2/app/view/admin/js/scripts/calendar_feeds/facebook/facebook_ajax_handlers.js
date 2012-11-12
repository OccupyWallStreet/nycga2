define( 
		[
		 "jquery",
		 "scripts/calendar_feeds/facebook/facebook_utility_functions",
		 "libs/utils"
		 ],
		 function( $, utility_functions, AI1EC_UTILS ) {

			var handle_refresh_multiselect = function( response ) {
				// Find the spinner and hide it
				var selector = '.ai1ec-facebook-multiselect-container[data-type=' + response.type + '] .ajax-loading';
				$( selector ).css( 'visibility', 'hidden' );
				if ( response.errors === true ) {
					var $alert = AI1EC_UTILS.make_alert( response.error_messages.join( '<br/>'), 'error' );
					$( '#alerts' ).append( $alert );
					return;
				}
				$( ' .ai1ec-facebook-multiselect-container[data-type=' + response.type + '] .ai1ec-facebook-multiselect' ).replaceWith( response.html );
				var $ok_alert = AI1EC_UTILS.make_alert( response.message, 'success' );
				$( '#alerts' ).append( $ok_alert );
			};
			var handle_refresh_events = function( response ) {
				var selector = '.ai1ec-facebook-refresh[data-id=' + response.id + ']';
				// Hide the spinner.
				$( selector ).closest( '.ai1ec-facebook-subscriber' )
				         .find( '.ajax-loading-user' )
				         .hide();
				// Handle exceptions
				if( response.exception !== undefined ) {
					$alert = AI1EC_UTILS.make_alert( response.message, 'error' );
				} else {
					var alert_class = response.errors ? 'warning' : 'success';
					$alert = AI1EC_UTILS.make_alert( response.message, alert_class );
				}
				$( '#alerts' ).append( $alert );
			};
			var handle_remove_events = function( response ) {
				var selector = '.ai1ec-facebook-remove[data-id=' + response.id + ']';
				var $el = $( selector );
				// Hide the spinner.
				$el.closest( '.ai1ec-facebook-subscriber' )
				         .find( '.ajax-loading-user' )
				         .hide();
				var $alert;
				if ( response.errors === true ) {
					$alert = AI1EC_UTILS.make_alert( response.error_message, 'error' );
				} else {
					// Create the alert
					$alert = AI1EC_UTILS.make_alert( response.message, 'success' );

					var logged = ( response.logged === 'true' ) ? true : false;
					if( logged ) {
						$( '#ai1ec_facebook_subscribe_yours' ).show();
						// First remove the category
						$el.closest( '#ai1ec-facebook' ).find( '.ai1ec-facebook-category-tag-wrapper' ).remove();
						// Then remove the buttons. Be careful to do this at the very end.
						$el.closest( '.ai1ec-facebook-subscriber' ).find( 'a.btn' ).remove();
					} else {
						// Remove the item  and reorder others
						utility_functions.cancel_element_and_reorder_other( $el );
						// Refresh the appropriate multiselect.
						$( '.ai1ec-facebook-multiselect-container[data-type=' + response.type + '] .ai1ec-facebook-multiselect' ).replaceWith( response.html );
					}
				}
				$( '#alerts' ).append( $alert );
			};

			return {
				"handle_refresh_multiselect"  : handle_refresh_multiselect,
				"handle_refresh_events"       : handle_refresh_events,
				"handle_remove_events"        : handle_remove_events
			};
		}
);