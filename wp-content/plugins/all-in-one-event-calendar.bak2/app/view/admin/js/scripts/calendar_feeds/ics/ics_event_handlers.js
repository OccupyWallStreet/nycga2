define( [
         "jquery",
         'scripts/calendar_feeds/ics/ics_ajax_handlers',
         "libs/utils",
         "ai1ec_config"
         ],
         function( $, ajax_handlers, AI1EC_UTILS, ai1ec_config ) {
	var ajaxurl = AI1EC_UTILS.get_ajax_url();
	var add_new_ics_event_handler = function( e ) {
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
				error_message = ai1ec_config.duplicate_feed_message;
			}
		} );
		// Check for valid URL
		if( ! AI1EC_UTILS.isUrl( url ) ) {
			invalid = true;
			error_message = ai1ec_config.invalid_url_message;
		}

		if( invalid ) {
			// color the feed url input field in red and output error message
			$url
				.css( 'border-color', '#FF0000' )
				.focus()
				.before( '<div class="error" id="ai1ec-feed-error"><p>' + error_message + '</p></div>' );
		} else {
			// disable the add button for now
			$button.prop( 'disabled', true );
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
				ajax_handlers.handle_add_new_ics,
				'json'
			);
		}
	};
	var delete_ics_modal_handler = function( e ) {
		e.preventDefault();
		$( '#ai1ec-ics-modal' ).modal( 'hide' );
		var remove_events = $( this ).hasClass( 'remove' ) ? true : false;
		// Get the element so that we can use it in the success handler
		var el = $( this ).data( 'el' );
		var $button = $( el );
		var ics_id = $button.siblings( '.ai1ec_feed_id' ).val();
		$button.siblings( '.ajax-loading' ).css( 'visibility', 'visible' );
		// create the data to send
		var data = {
			"action" : 'ai1ec_delete_ics',
			"ics_id" : ics_id,
			"remove_events" : remove_events
		};
		// remove the feed from the database
		$.post( ajaxurl,
				data,
				ajax_handlers.handle_delete_ics,
				'json'
		);
	};
	var handle_open_modal = function( e ) {
		// Save the user id, the current elements and if we are removing the logged on user.
		$( '#ai1ec-ics-modal a.btn' ).data( 'el', this );
		// Open the modal with a static background.
		$( '#ai1ec-ics-modal' ).modal( {
			"show": true,
			"backdrop" : true
		} );
	};
	var update_ics_handler = function( e ) {
		// store clicked button for later use
		var $button = $( this );
		// disable the update button
		$button.prop( 'disabled', true );
		// get the selected feed id
		var ics_id = $button.siblings( '.ai1ec_feed_id' ).val();
		$button.siblings( '.ajax-loading' ).css( 'visibility', 'visible' );
		// create the data to send
		var data = {
			action: 'ai1ec_update_ics',
			ics_id: ics_id
		};
		// remove the feed from the database
		$.post(
				ajaxurl,
				data,
				ajax_handlers.handle_update_ics,
				'json'
		);
	};
	return {
		"add_new_ics_event_handler" : add_new_ics_event_handler,
		"delete_ics_modal_handler"  : delete_ics_modal_handler,
		"handle_open_modal"         : handle_open_modal,
		"update_ics_handler"        : update_ics_handler
	};

} );
