define(
	[
	 "jquery",
	 "libs/jquery.masonry"
	],
	function( $, masonry ) {
		// *** Posterboard view ***

		var initialize_masonry = function() {
			$( '.ai1ec-posterboard-view' ).masonry({
				itemSelector: '.ai1ec-event:visible',
				isFitWidth: true,
				columnWidth: 240
			});
		}

		var reload_masonry = function() {
			$( '.ai1ec-posterboard-view' ).masonry( 'reload' );
		}

		return {
			initialize_masonry : initialize_masonry,
			reload_masonry     : reload_masonry
		};
} );
