define(
		[
		 "jquery"
		 ],
		 function( $ ) {
	// *** Agenda view ***

	/**
	 * Callbacks for event expansion, collapse.
	 */
	var expand_event = function() {
		$( this )
			// Find the parent li.ai1ec-event that is not expanded and toggle it's class
			.closest( 'li.ai1ec-event:not(.ai1ec-expanded)' )
				.toggleClass( 'ai1ec-expanded' )
				// Find the event summary and slideToggle it
				.find( '.ai1ec-event-summary' )
					.slideToggle()
					.end()
				// find the .ai1ec-event-expand icon and toggle it
				.find( '.ai1ec-event-expand > i' )
				.toggleClass( 'icon-plus-sign icon-minus-sign');
	};
	var toggle_event = function() {
		$( this )
			// Find the parent li.ai1ec-event toggle it's class
			.closest( 'li.ai1ec-event' )
				.toggleClass( 'ai1ec-expanded' )
				// Find the event summary and slideToggle it
				.find( '.ai1ec-event-summary' )
					.slideToggle()
					.end()
				// find the .ai1ec-event-expand icon and toggle it
				.find( '.ai1ec-event-expand > i' )
				.toggleClass( 'icon-plus-sign icon-minus-sign' );
	};
	var collapse_all = function() {
		$( '.ai1ec-expanded .ai1ec-event-click')
			.click();
	};
	var expand_all = function() {
		$( 'li.ai1ec-event:not(.ai1ec-expanded) .ai1ec-event-click')
			.click();
	};
	return {
		expand_event   : expand_event,
		toggle_event   : toggle_event,
		collapse_all   : collapse_all,
		expand_all     : expand_all
	};
} );
