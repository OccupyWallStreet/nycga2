define(
		[
		 "jquery",
		 "libs/bootstrap_modal"
		 ],
		 function( $ ) {
	var open_modal_when_user_chooses_to_unpublish_event = function( e ) {
		if( ! $( this ).is( ':checked' ) && $( '#ai1ec-facebook-export-modal' ).length ) {
			$( '#ai1ec-facebook-export-modal' ).modal( {
				"show": true,
				"backdrop" : 'static' 
			} );
		} else {
			// Remove th hidden input if present
			$( '#ai1ec-remove-event-hidden' ).remove();
		}
	};
	var add_hidden_field_when_user_click_remove_in_modal = function() {
		$( '#ai1ec-facebook-export-modal' ).modal( 'hide' );
		if( $( this ).hasClass( 'remove' ) ) {
			var $input = $( '<input />', {
				type  : "hidden",
				name  : "ai1ec-remove-event",
				value : 1,
				id    : "ai1ec-remove-event-hidden"
			} );
			$( '#ai1ec-facebook-publish' ).append( $input );
		}
	};
	return {
		open_modal_when_user_chooses_to_unpublish_event  : open_modal_when_user_chooses_to_unpublish_event,
		add_hidden_field_when_user_click_remove_in_modal : add_hidden_field_when_user_click_remove_in_modal
	};
} );