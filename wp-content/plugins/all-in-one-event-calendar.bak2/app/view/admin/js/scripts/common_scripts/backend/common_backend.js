define(
		[
		 "jquery",
		 "domReady",
		 "ai1ec_config",
		 "scripts/common_scripts/backend/common_event_handlers",
		 "libs/bootstrap_modal"
		 ],
		 function( $, domReady, ai1ec_config, event_handlers ) {
	var add_export_to_facebook = function() {
		// When we have select the "Show only events that can be exported to facebook" filter and when there are rows in the table
		if( $( '#ai1ec-facebook-filter option[value=exportable]:selected' ).length > 0 && $( 'table.wp-list-table tr.no-items' ).length === 0 && ai1ec_config.facebook_logged_in === "1" ) {
			// Add the bulk action to the selects
			$( '<option>' ).val( 'export-facebook' ).text( "Export to facebook" ).appendTo( "select[name='action']" );
			$( '<option>' ).val( 'export-facebook' ).text( "Export to facebook" ).appendTo( "select[name='action2']" );
		}
	};
	var handle_platform_mode = function() {
		if( ai1ec_config.platform_active === "1" ) {
			if( $( 'body.options-reading-php' ).length ) {
				var disable_front_page_option = function() {
					$( '#page_on_front' ).attr( 'disabled', 'disabled' );
				};
				disable_front_page_option();
				$( '#front-static-pages input:radio' ).change( disable_front_page_option );
				$( '#page_on_front' ).after( '<span class="description">' + ai1ec_config.page_on_front_description + '</span>' );
			}
			if( ai1ec_config.strict_mode === "1" ) {
				$( '#dashboard-widgets .postbox' )
					.not( '#ai1ec-calendar-tasks, #dashboard_right_now' )
					.remove();
				$( '#adminmenu > li' )
					.not( '.wp-menu-separator, #menu-dashboard, #menu-posts-ai1ec_event, #menu-media, #menu-appearance, #menu-users, #menu-settings' )
					.remove();
				$( '#menu-appearance > .wp-submenu li, #menu-settings > .wp-submenu li' )
					.not( ':has(a[href*="all-in-one-event-calendar"])' )
					.remove();
			}
		}
	};
	var initialize_modal_video = function() {
		if ( $( '#ai1ec-video' ).length ) {
			// TODO: Load YouTube IFrame Player API async using requirejs (right?)
			// TODO: Separate event handlers into common_event_handlers.js. Tried this
			// already and had difficulties; maybe the Bootstrap modal code wasn't
			// initialized yet? Weird error messages.

			// Load the YouTube IFrame Player API code asynchronously.
			$( '<script type="text/javascript" src="//www.youtube.com/iframe_api"></script>' )
				.prependTo( 'head' );

			// Create an <iframe> (and YouTube player) after the API code downloads.
			window.onYouTubeIframeAPIReady = function() {
				var player = new YT.Player( 'ai1ec-video', {
					height: '368',
					width: '600',
					videoId: window.ai1ecVideo.youtubeId
				});
				$( '#ai1ec-video' ).css( 'display', 'block' );

				$( '#ai1ec-video-modal' ).on( 'hide', function() {
					player.stopVideo();
				} );
			}
		}
	}
	var attach_event_handlers_backend = function() {
		$( window ).on( 'click', '.ai1ec-facebook-cron-dismiss-notification',  event_handlers.dismiss_plugins_messages_handler );
		$( window ).on( 'click', '.ai1ec-dismiss-notification', event_handlers.dismiss_notification_handler );
		$( window ).on( 'click', '.ai1ec-dismiss-intro-video', event_handlers.dismiss_intro_video_handler );
		$( window ).on( 'click', '.ai1ec-limit-by-cat, .ai1ec-limit-by-tag, .ai1ec-limit-by-event', event_handlers.handle_multiselect_containers_widget_page );
	};
	// If it was set in the backend, run the script
	if( ai1ec_config.page !== '' ) {
		$( '.if-js-closed' ).removeClass( 'if-js-closed' ).addClass( 'closed' );
		postboxes.add_postbox_toggles( ai1ec_config.page  );
	};
	var start = function() {
		domReady( function() {
			// Attach the export to Facebook functionality.
			add_export_to_facebook();
			// Initialize modal video if present.
			initialize_modal_video();
			// Attach the event handlers.
			attach_event_handlers_backend();
			// Handle event platform mode.
			handle_platform_mode();
		} );
	};
	return {
		start : start
	};
} );
