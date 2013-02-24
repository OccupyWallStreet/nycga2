// rate posts jquery, called from the user rating click
function rfp_rate_js( post_id, direction, rater ) {

	if ( post_id != '' ) {
		jQuery( '#rfp-rate-'+post_id+' .counter' ).text( '...' );
		jQuery( '#rfp-rate-'+post_id+' b' ).hide();
		
		jQuery.post( ajaxurl, {
			action: 'rfp_rate',
			post_id: post_id, 
			direction: direction, 
			rater: rater 
		},
		function( data ){	
			datasplit = data.split( '|' );
			jQuery( '#rfp-rate-'+post_id+' .counter' ).text( datasplit[0] ); // the new (or old) rating
			jQuery( '#rfp-rate-'+post_id+' i' ).show().text( datasplit[1] ).animate({opacity:1},2000).fadeOut('slow'); //status message
		});
	}
}


// if a post is hidden, add a 'click to show' link
jQuery(document).ready( function() {
	jQuery( '.rfp-hide' ).append( '<div class="rfp-show">Click to show this hidden item</div>' ).click( function() {
		jQuery( this ).removeClass( 'rfp-hide' );
		jQuery( '.rfp-show', this ).hide();	  // using a nice way to select children of this
	});
});
