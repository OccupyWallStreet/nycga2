define( 
		[
		 "ai1ec_config"
		 ],
		 function( ai1ec_config ) {
	// Get the language
	var lang = ai1ec_config.gmaps_language;
	// Create the url
	var url = 'async!http://maps.google.com/maps/api/js?sensor=false&language=' + lang;
	// Return a wrapper function so that we have a callback. 
	// This is important because we load gMaps async and we don't want to wait for it to load and block other functions 
	return function( callback ) { 
		require( [ url ], callback ); 
	};
} );