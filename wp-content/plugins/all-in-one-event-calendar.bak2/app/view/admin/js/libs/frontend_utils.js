/**
 * This modules defines some common functions that are used by some other frontend modules
 */
define( function() {
	/**
	 * Used to ensure that entities used in L10N strings are correct.
	 */
	var ai1ec_convert_entities = function( o ) {
		var c, v;

		c = function( s ) {
			if( /&[^;]+;/.test( s ) ) {
				var e = document.createElement( 'div' );
				e.innerHTML = s;
				return ! e.firstChild ? s : e.firstChild.nodeValue;
			}
			return s;
		}

		if( typeof o === 'string' ) {
			return c( o );
		} else if( typeof o === 'object' ) {
			for( v in o ) {
				if( typeof o[v] === 'string' ) {
					o[v] = c( o[v] );
				}
			}
		}
		return o;
	};
	return {
		ai1ec_convert_entities : ai1ec_convert_entities
	};
} );