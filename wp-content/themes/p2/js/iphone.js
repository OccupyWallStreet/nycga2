/**
 * Handles toggling the main navigation and new post form menu on iPhone.
 */
jQuery( document ).ready( function( $ ) {
	var $masthead   = $( '#header' ),
		$postToggle = $( '#mobile-post-button' ),
		$postbox    = $( '#postbox' ),
		$menu       = $masthead.find( '.menu' ),
	    timeout     = false;

	$postToggle.click( function( e ) {
		e.preventDefault();
		if ( $postbox.is( ':visible' ) )
			$postbox.slideUp( 'fast' );
		else
			$postbox.slideDown( 'fast' );
	} );

	$masthead.find( '.site-navigation' ).removeClass( 'main-navigation' ).addClass( 'main-small-navigation' );
	$masthead.find( '.site-navigation h1' ).removeClass( 'assistive-text' ).addClass( 'menu-toggle' );

	$( '.menu-toggle' ).click( function( e ) {
		e.preventDefault();
		if ( $menu.is( ':visible' ) )
			$menu.slideUp( 'fast' );
		else
			$menu.slideDown( 'fast' );
	} );
} );

