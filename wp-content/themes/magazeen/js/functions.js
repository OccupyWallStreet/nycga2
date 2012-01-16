$( function() {
	$( '.category' ).click( function() {
		$( this ).toggleClass( 'active' )
		$( this ).siblings( '.dropdown' ).toggle();
		$( this ).find( '.indicator' ).toggleClass( 'indicator-active' );
		return false;
	} );
			
	$( '#dock > li' ).hover( function() {
		$( '.latest' ).fadeOut( 'fast' );
		$( this ).addClass( 'dock-active' );
		$( this ).children( 'span' ).fadeIn( 200 );
	}).bind( "mouseleave", function() {		
		$( this ).removeClass( 'dock-active' );	
		$( this ).children( 'span' ).fadeOut( 200 );
	} );
			
	$( '#dock' ).bind( "mouseleave", function() {
		$( '.latest' ).fadeIn( 1000 );
	} );
} );