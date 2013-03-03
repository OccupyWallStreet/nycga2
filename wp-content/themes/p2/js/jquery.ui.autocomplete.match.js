/*
 * jQuery UI Autocomplete Match Extension
 *
 * Author: Daryl Koopersmith
 *
 * Based on jQuery UI Autocomplete 1.8.11
 *
 * Adds a match parameter that takes the following:
 *     RegExp   - Matches and creates a term based on text before the caret.
 *                Will use the first capture group for the term if it exists.
 *
 *     Callback - function( request, response )
 *         request  - An object containing a term and an event.
 *         matched  - A callback. Pass a term to continue searching,
 *                    or false to cancel.
 */

(function( $ ) {

var proto       = $.ui.autocomplete.prototype,
	create      = proto._create,
	setOption   = proto._setOption,
	search      = proto.search;

// Build the autocomplete methods.
$.extend( proto, {
	_create: function() {
		create.call( this );
		this._initMatch();
	},

	_setOption: function( key, value ) {
		setOption.apply( this, arguments );

		if ( key === 'match' )
			this._initMatch();
	},

	_initMatch: function() {
		var self = this, re;

		if ( this.options.match instanceof RegExp ) {
			re = this.options.match;
			this.match = function( request, matched ) {
				var match = Caret( self.element[0] ).before().match( re ),
					value;

				if ( ! match )
					return matched( false );

				// If the regex contains a capture group, use the first capture group.
				// Otherwise, use the full match.
				value = ( typeof match[1] === 'undefined' ) ? match[0] : match[1];
				return matched( value );
			};

		} else if ( $.isFunction( this.options.match ) ) {
			this.match = this.options.match;

		} else {
			// Nothing to match, waltz right through.
			this.match = function( request, matched ) {
				return matched( request.term );
			};
		}
	},

	search: function( value, event ) {
		var self = this;

		value = value != null ? value : this.element.val();

		// Run the default autocomplete search function in matched.
		this.match({ term: value, event: event }, function( match ) {
			return self.matched( match, event );
		});
	},

	matched: function( value, event ) {
		this._trigger( "matched", event, { term: value } );

		if ( value === false ) {
			return this.close( event );
		}

		// Now we trigger search.
		search.call( this, value, event );
	}
});

})( jQuery );