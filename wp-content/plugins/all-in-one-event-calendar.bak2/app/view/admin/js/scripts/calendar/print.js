/**
 * This module handles the print button behaviour
 * 
 */
define( 
		[
		 "jquery"
		 ],
		 function( $ ) {
	/**
	 * Get the URL withouth the hash portion.
	 */
	var get_hashless_url = function() {
		// Get the full url.
		var url = window.location.href;
		// Get the hash.
		var hash = window.location.hash;
		// If the hash is present, use that, otherwise use the full length
		var index_of_hash = url.indexOf( hash ) || url.length;
		// Cut off the hash.
		var hashless_url = url.substr( 0, index_of_hash );
		return hashless_url;
	};
	// Add print=true to the url using ? or & as needed
	var add_print_true_as_a_get_variable = function( url ) {
		return url + ( ( url.indexOf( '?' ) == -1 ) ? '?print=true' : '&print=true' );
	};
	// Handle clicks on the print icon
	var handle_click_on_print_button = function( e ) {
		e.preventDefault();
		// Get the url withouth the hash.
		var hashless_url = get_hashless_url();
		// Add the extra parameter in the url, this will trigger the loading of the print CSS.
		var url = add_print_true_as_a_get_variable( hashless_url ) + window.location.hash;
		// Open a new page. http://stackoverflow.com/questions/9590282/script87-invalid-argument-in-ie-9-asp-net-c-sharp
		// window.open on IE doesn't like a lot of things in the page name
		window.open( url,'Print-page' );
	};

	/**
	 * Show print button in Agenda view, hide otherwise. This is called at the end
	 * of initialize_view();
	 */
	function toggle_print_button() {
		$('#ai1ec-print-button').toggle( $( '.ai1ec-agenda-view' ).length !== 0 );
	}
	return {
		toggle_print_button              : toggle_print_button,
		handle_click_on_print_button     : handle_click_on_print_button,
		// Expose for testing purpose
		add_print_true_as_a_get_variable : add_print_true_as_a_get_variable,
		// Expose for testing purpose
		get_hashless_url                 : get_hashless_url
	};
} );