/*
 * jQuery UI Autocomplete Multi Value Extension
 *
 * Author: Daryl Koopersmith
 *
 * Based on jQuery UI Autocomplete 1.8.11
 */
(function( $ ) {

var create = $.ui.autocomplete.prototype._create;

// Prevent TAB from changing the text field when the menu is open.
$.ui.autocomplete.prototype._create = function() {
	var self = this;
	// Must be bound before _create is called, otherwise the default keydown
	// function will close the menu before we can check menu.active
	this.element.bind('keydown.autocomplete.multiValue', function( event ) {
		if ( self.menu.active && event.keyCode === $.ui.keyCode.TAB )
			event.preventDefault();
	});
	create.apply( this, arguments );
};

// When an item is focused, prevent the contents of the field from being replaced.
$( '.ui-autocomplete-input' ).live( 'autocompletefocus.multiValue', function( event ) {
	if ( $( this ).autocomplete( 'option', 'multiValue' ) )
		event.preventDefault();
});

}( jQuery ));