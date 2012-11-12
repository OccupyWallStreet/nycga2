define(
		[
		 "jquery",
		 "domReady",
		 "libs/bootstrap_dropdown",
		 "libs/bootstrap_tooltip"
		 ],
		function( $, domReady ) {
			var start = function() {
				$( window ).on( 'hover.ai1ec', '[rel="tooltip"]', function() {
					// Don't add tooltips to category colour squares already contained in
					// descriptive category labels.
					if( $( this ).is( '.ai1ec-category .ai1ec-category-color' ) ) {
						return;
					}
					// Only register .tooltip() the first time it is hovered.
					if( ! $( this ).data( 'tooltipped.ai1ec' ) ) {
						$( this )
							.tooltip()
							.tooltip( 'show' )
							.data( 'tooltipped.ai1ec', true );
					}
				} );

			};
			return {
				start : start
			};
} );
