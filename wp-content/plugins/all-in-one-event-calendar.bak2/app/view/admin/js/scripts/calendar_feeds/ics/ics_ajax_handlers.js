define( [
         "jquery",
         'scripts/calendar_feeds/ics/ics_utility_functions',
         "libs/utils"
         ], 
         function( $, utility_functions, AI1EC_UTILS ) {
	var handle_add_new_ics = function( response ) {
		var $button = $( '#ai1ec_add_new_ics' );
		var $url = $( '#ai1ec_feed_url' );
		// restore add button
		$button.removeAttr( 'disabled' );
		if( response.error ) {
			// tell the user there is an error
			// TODO: Use other method of notification
			alert( response.message );
		} else {
			$url.val( '' );
			// Add the feed to the settings screen
			$( '#ai1ec-feeds-after' ).after( response.message );
		}
	};
	var handle_delete_ics = function( response ) {
		var $hidden = $( 'input[value=' + response.ics_id + ']' );
		if( response.error ) {
			// Restore things
			utility_functions.restore_normal_state_after_unsuccesful_delete( $hidden );
		} else {
			// Remove the feed
			utility_functions.remove_feed_from_dom( $hidden );
		}
		var type = response.error === true ? 'error' : 'success';
		var $alert = AI1EC_UTILS.make_alert( response.message, type );
		$( '#ics-alerts' ).append( $alert );
	};
	var handle_update_ics = function( response ) {
		var $hidden = $( 'input[value=' + response.ics_id + ']' );
		if( response.error ) {
			// tell the user there is an error
			$alert = AI1EC_UTILS.make_alert( response.message, 'error' );
		} else {
			$alert = AI1EC_UTILS.make_alert( response.message, 'success' );
		}
		$( '#ics-alerts' ).append( $alert );
		var $button = $hidden.siblings( '.ai1ec_update_ics' );
		$button
			.removeAttr( 'disabled' )
			.siblings( '.ajax-loading' ).css( 'visibility', 'hidden' );
	};
	return {
		"handle_add_new_ics" : handle_add_new_ics,
		"handle_delete_ics"  : handle_delete_ics,
		"handle_update_ics"  : handle_update_ics
	};
} );