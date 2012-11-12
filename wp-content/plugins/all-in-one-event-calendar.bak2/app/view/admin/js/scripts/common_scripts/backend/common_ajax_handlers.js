define( [
         "jquery"
         ],
         function( $ ) {
	var handle_dismiss_plugins = function( response ) {
		if ( response ) {
			if( typeof response.message !== 'undefined' ) {
				alert( response.message );
			} else {
				$( '.ai1ec-facebook-cron-dismiss-notification' ).closest( '.message' ).remove();
			}
		}
	};
	var handle_dismiss_notification = function( response ) {
		if( response.error ) {
			// tell the user that there is an error
			alert( response.message );
		} else {
			// hide notification message
			$( '.ai1ec-dismiss-notification' ).closest( '.message' ).remove();
		}
	};
	var handle_dismiss_intro_video = function( response ) {
		if( response.error ) {
			// Tell the user that there is an error.
			alert( response.message );
		} else {
			// Hide notification message.
			$( '.ai1ec-dismiss-intro-video' ).closest( '.message' ).remove();
		}
	};
	return {
		handle_dismiss_plugins      : handle_dismiss_plugins,
		handle_dismiss_notification : handle_dismiss_notification,
		handle_dismiss_intro_video  : handle_dismiss_intro_video
	};
} );
