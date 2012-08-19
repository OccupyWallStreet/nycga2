/**
 * This function will assign a click event on the passed jQuery selector.
 * On each click, the function will toggle the CSS class selected_class. Then
 * the function will go over each element contained by the DOM selector and look
 * for an <input> field with name=hidden_data_el in each element that has the
 * selected_class. Of these matching <input>s, their values will be joined by
 * "," and the joined value stored in hidden_input (another hidden input field).
 *
 * If when this function is called, the hidden_input's value is not empty, this
 * function will attempt to preselect any elements matching that value.
 *
 * @param string selector        Container that has the <input name=hidden_data_el /> to toggle class
 * @param string selected_class  Selected element class
 * @param string hidden_data_el  Name of input element storing selected element's value
 * @param string hidden_input    jQuery selector for input that stores all selected values
 */
function element_selector( selector, selected_class, hidden_data_el, hidden_input )
{
	var $ = jQuery;

	// Register click event
	$( selector ).click( function() {
		var data = new Array();
		if( $( this ).hasClass( selected_class ) ) {
			// Element deselected, remove class
			$( this ).removeClass( selected_class );
		} else {
			// Element selected, add class
			$( this ).addClass( selected_class );
		}
		$( selector + '.' + selected_class ).each( function() {
			var item_val = $( this ).find( 'input[name="' + hidden_data_el + '"]:first' ).val();
			data.push( item_val );
		} );
		$( hidden_input ).val( data.join() );
	} );

	// Check if hidden input has a preinitialized value
	var initial_val = $( hidden_input ).val();
	if( initial_val != undefined && initial_val != '' ) {
		var data = initial_val.split( ',' );
		// Turn each element of data into a jQuery selector
		$( data ).each( function( i, val ) {
			data[i] = 'input[name="' + hidden_data_el + '"][value="' + val + '"]';
		} );
		// Concatenate data into one long jQuery selector
		data = data.join();
		// Assign the selected_class to all matching elements
		$( selector ).has( data ).addClass( selected_class );
	}
}
