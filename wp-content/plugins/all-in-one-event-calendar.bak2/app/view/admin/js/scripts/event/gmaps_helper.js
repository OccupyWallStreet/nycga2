define( 
		[
		 'jquery'
		 ],
		 function( $ ) {
	var init_gmaps = function() {
		var options = {
				zoom      : 14,
				mapTypeId : google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map( document.getElementById( 'ai1ec-gmap-canvas' ), options );
			var marker = new google.maps.Marker( { map: map } );
			var geocoder = new google.maps.Geocoder();

			geocoder.geocode(
				{
					'address': document.getElementById( 'ai1ec-gmap-address' ).value
				},
				function( results, status ) {
					if( status == google.maps.GeocoderStatus.OK ) {
						map.setCenter( results[0].geometry.location );
						marker.setPosition( results[0].geometry.location );
					}
				}
			);
	};
	var handle_show_map_when_clicking_on_placeholder = function() {
		var map_el = $( '.ai1ec-gmap-container-hidden:first');
		// delete placeholder
		$( this ).remove();
		// hide map
		map_el.hide();
		map_el.removeClass( 'ai1ec-gmap-container-hidden' );
		map_el.fadeIn();
	};
	return {
		handle_show_map_when_clicking_on_placeholder : handle_show_map_when_clicking_on_placeholder,
		init_gmaps                                   : init_gmaps
	};
} );