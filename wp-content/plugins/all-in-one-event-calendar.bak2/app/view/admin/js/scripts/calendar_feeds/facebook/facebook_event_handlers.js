define(
		[
		 "jquery",
		 "ai1ec_config",// Used for translations
		 "scripts/calendar_feeds/facebook/facebook_utility_functions",
		 'scripts/calendar_feeds/facebook/facebook_ajax_handlers',
		 "libs/utils",
		 "libs/jquery_cookie"
		 ],
		function( $, ai1ec_config, utility_functions, ajax_handlers, AI1EC_UTILS ) {
			var ajaxurl = AI1EC_UTILS.get_ajax_url();
			// Function that handles setting the cookie when the tab is clicked
			var handle_set_tab_cookie = function( e ) {
				var active = $( this ).attr( 'href' );
				$.cookie( 'feeds_active_tab', active );
			};
			// Verify that no more than 10 and more than 0 have been selected
			var do_controls_before_subscribing = function( e ) {
				var how_many_selected = $( 'select.ai1ec-facebook-multiselect option:selected' ).length;
				// If nothing is selected, show an alert and block execution.
				if( how_many_selected === 0 ) {
					alert( ai1ec_config.select_one_option );
					return false;
				}
				// If more than ten are selected, show an alert and block execution.
				if( how_many_selected > 10 ) {
					alert( ai1ec_config.no_more_than_ten );
					return false;
				}
				utility_functions.block_all_submit_and_ajax( this );
			};
			// When clicking on a submit button disable all other actions.
			var handle_click_on_submit_buttons = function( e ) {
				utility_functions.block_all_submit_and_ajax( this );
			};
			// When clicked, refresh the relative multiselect.
			var refresh_multiselect = function( e ) {
				e.preventDefault();
				$.ajaxSetup({
					"timeout": 2400000
				});
				// Find the spinner and show it
				$( this ).closest( '.ai1ec-facebook-multiselect-title-wrapper' )
				         .find( '.ajax-loading' )
				         .css( 'visibility', 'visible' );
				var type = $( this ).closest( '.ai1ec-facebook-multiselect-container' ).data( 'type' );
				var data = {
						"action"     : 'ai1ec_refresh_facebook_objects',
						"ai1ec_type" : type
					};
				$.post(
					ajaxurl,
					data,
					ajax_handlers.handle_refresh_multiselect,
					'json'
				);
			};
			var refresh_events = function( e ) {
				e.preventDefault();
				// Get the type so we know if we are refreshing a user, a page or a group.
				var type = $( this ).closest( '.ai1ec-facebook-items' ).data( 'type' );
				// Show the spinner
				$( this ).closest( '.ai1ec-facebook-subscriber' )
				         .find( '.ajax-loading-user' )
				         .show();

				// create the data to send
				var data = {
					"action"        : 'ai1ec_refresh_events',
					"ai1ec_post_id" : $( this ).data( 'id' ),
					"ai1ec_type"    : type
				};
				// make an ajax call to unsubscribe
				$.post(
					ajaxurl,
					data,
					ajax_handlers.handle_refresh_events,
					'json'
				);
			};
			var remove_subscription = function( e ) {
				// Prevent the default action.
				e.preventDefault();
				// Get the name of the user we are removing
				var user = $( this ).closest( '.ai1ec-facebook-subscriber' ).find( '.ai1ec-facebook-name' ).text();
				// Get the user id
				var user_id = $( this ).data( 'id' );
				// Inject the text into the header of the modal.
				$( '#ai1ec-facebook-modal #ai1ec-facebook-user-modal' ).text( user );
				// Save the user id, the current elements and if we are removing the logged on user.
				$( '#ai1ec-facebook-modal a.btn' )
					.data( 'user_id', user_id )
					.data( 'el', this )
					.data( 'logged', $( this ).hasClass( 'logged' ) );
				// Open the modal with a static background.
				$( '#ai1ec-facebook-modal' ).modal( {
					"show": true,
					"backdrop": true
				} );
			};
			var modal_remove_subscription = function( e ) {
				e.preventDefault();
				$( '#ai1ec-facebook-modal' ).modal( 'hide' );
				var remove_events = $( this ).hasClass( 'remove' ) ? true : false;
				// Get the element so that we can use it in the success handler
				var el = $( this ).data( 'el' );
				// Show the spinner
				$ajax_loading = $( el ).closest( '.ai1ec-facebook-subscriber' ).find( '.ajax-loading-user' );
				$ajax_loading.show();
				// Save if we are removing the logged on user
				var logged = $( this ).data( 'logged' );
				// Get the type that we have removed
				var type = $( el ).closest( '.ai1ec-facebook-items' ).data( 'type' );
				// create the data to send.
				var data = {
					"action"              : 'ai1ec_remove_subscribed',
					"ai1ec_post_id"       : $( this ).data( 'user_id' ),
					"ai1ec_remove_events" : remove_events,
					"ai1ec_logged_user"   : logged,
					"type"                : type
				};
				// make an ajax call to unsubscribe
				$.post(
					ajaxurl,
					data,
					ajax_handlers.handle_remove_events,
					'json'
				);
			};
			var click_on_facebook_connect_button_opens_modal_if_app_id_and_secret_are_not_set = function( e ) {
				if( $( this ).attr( 'href' ) === '#' ) {
					e.preventDefault();
					$( '#ai1ec_facebook_connect_modal' ).modal( {
						"show": true,
						"backdrop" : true
					} );
				}
			};
			var click_on_save_button_in_modal_trigger_submit = function( e ) {
				var both_fields_are_set = true;
				$( '#ai1ec_facebook_connect_modal input:text' ).each( function() {
					if( this.value === '' ) {
						both_fields_are_set = false;
					}
				} );
				if( both_fields_are_set === true ) {
					$( '#ai1ec_facebook_connect_modal' ).modal( 'hide' );
					$( '#ai1ec_facebook_modal_submit' ).click();
				} else {
					var $alert = AI1EC_UTILS.make_alert( ai1ec_config.app_id_and_secret_are_required, 'error' );
					$( '#ai1ec_facebook_connect_modal .modal-body' ).prepend( $alert );
				}
			};
			return {
				"handle_set_tab_cookie"                                                         : handle_set_tab_cookie,
				"do_controls_before_subscribing"                                                : do_controls_before_subscribing,
				"handle_click_on_submit_buttons"                                                : handle_click_on_submit_buttons,
				"refresh_multiselect"                                                           : refresh_multiselect,
				"refresh_events"                                                                : refresh_events,
				"remove_subscription"                                                           : remove_subscription,
				"modal_remove_subscription"                                                     : modal_remove_subscription,
				"click_on_facebook_connect_button_opens_modal_if_app_id_and_secret_are_not_set" : click_on_facebook_connect_button_opens_modal_if_app_id_and_secret_are_not_set,
				"click_on_save_button_in_modal_trigger_submit"                                  : click_on_save_button_in_modal_trigger_submit
			};
		}
);
