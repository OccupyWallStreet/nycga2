/**
 * Callback for map initialization, called by the Google Maps API script after
 * it has been loaded.
 */
function ai1ec_load_map()
{
	var options = {
		zoom: 14,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map( document.getElementById( 'ai1ec-gmap-canvas' ), options );
	var marker = new google.maps.Marker({ map: map });
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
}

// jQuery-less onready
var orig_onload = window.onload;
window.onload = function()
{
	if( typeof( orig_onload ) == 'function' )
		orig_onload();

	// Check if map container exists, and if so, load map into it
	if( document.getElementById( 'ai1ec-gmap-canvas' ) ) {
		// Include Google Maps API to display embedded map, triggering callback
		// when script has loaded.
		var script = document.createElement( 'script' );
		script.type = 'text/javascript';
		script.src = 'http://maps.google.com/maps/api/js?sensor=false&callback=ai1ec_load_map&language=' + ai1ec_event.language;
		document.body.appendChild( script );
	}
}

jQuery( function( $ ) {
	$( '.ai1ec-gmap-placeholder:first' ).click( function() {
		var map_el = $( '.ai1ec-gmap-container-hidden:first');
		// delete placeholder
		$( this ).remove();
		// hide map
		map_el.hide();
		map_el.removeClass( 'ai1ec-gmap-container-hidden' );
		map_el.fadeIn();
	});
});
