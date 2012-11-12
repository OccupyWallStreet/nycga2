define( ['jquery',
		'domReady',
		'scripts/calendar_feeds/facebook/facebook_event_handlers',
		'scripts/calendar_feeds/facebook/facebook_utility_functions',
		'scripts/calendar_feeds/ics/ics_event_handlers',
		'libs/jquery_cookie',
		'libs/bootstrap_tab',
		'libs/bootstrap_alert',
		'libs/bootstrap_modal'
		], 
		function ( $, domReady, event_handlers, utility_functions, ics_event_handlers ) {

	var attach_event_handlers = function() {
		// Save the active tab in a cookie on click.
		$( 'ul.nav-tabs a' ).click( event_handlers.handle_set_tab_cookie );
		$( '#ai1ec_subscribe_users' ).click( event_handlers.do_controls_before_subscribing );
		$( 'input[type=submit]' ).not( '#ai1ec_subscribe_users' ).click( event_handlers.handle_click_on_submit_buttons );
		// Refresh the events for the clicked multiselect
		$( '.ai1ec-facebook-refresh-multiselect' ).click( event_handlers.refresh_multiselect );
		// Refreshes the events for the clicked facebook user with data from facebook.
		$( '.ai1ec-facebook-items' ).on( "click", '.ai1ec-facebook-refresh', event_handlers.refresh_events );
		// Open the modal that allows the user to choose whether to keep events or not. use delegate so that i have only 3 handlers instead of 5000 
		$( '.ai1ec-facebook-items' ).on( "click", '.ai1ec-facebook-remove', event_handlers.remove_subscription );
		// Handle the modal clickss
		$( '#ai1ec-facebook-modal' ).on( 'click', 'a.remove, a.keep', event_handlers.modal_remove_subscription );
		$( '#ai1ec-facebook-connect a' ).click( event_handlers.click_on_facebook_connect_button_opens_modal_if_app_id_and_secret_are_not_set );
		$( 'body' ).on( "click", "#ai1ec_facebook_connect_modal a.keep", event_handlers.click_on_save_button_in_modal_trigger_submit );
		// ============================ICS EVENT HANDLERs=======================
		$( '#ai1ec_add_new_ics' ).click( ics_event_handlers.add_new_ics_event_handler );
		// The modal handles the events when you click on the buttons.
		$( '#ai1ec-ics-modal' ).on( 'click', 'a.remove, a.keep', ics_event_handlers.delete_ics_modal_handler );
		// Handles opening the modal window for deleting the feeds
		$( 'div#ics' ).on( 'click', '.ai1ec_delete_ics', ics_event_handlers.handle_open_modal );
		// Handle updating the feeds events
		$( 'div#ics' ).on( 'click', '.ai1ec_update_ics', ics_event_handlers.update_ics_handler );
	};
	var start = function() {
		domReady( function(){
			// Set the active tab
			utility_functions.activate_saved_tab_on_page_load( $.cookie( 'feeds_active_tab' ) );
			// Attach the event handlers
			attach_event_handlers();
		} );
	};

	return {
		start: start
	};
} );