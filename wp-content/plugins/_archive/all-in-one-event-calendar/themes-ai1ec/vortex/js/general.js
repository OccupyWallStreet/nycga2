/**
 * @file
 *
 * General JavaScript to be loaded on all pages of a WordPress site on which
 * AI1EC is active.
 */

jQuery( function( $ ) {
  $( document ).delegate( '[rel="tooltip"]', 'hover.ai1ec', function() {
    // Don't add tooltips to category colour squares already contained in
    // descriptive category labels.
    if( $( this ).is( '.ai1ec-category .ai1ec-category-color' ) ) {
      return;
    }
    // Only register .tooltip() the first time it is hovered.
    if( ! $( this ).data( 'tooltipped.ai1ec' ) ) {
      $( this )
        .tooltip()
        .tooltip( 'show' )
        .data( 'tooltipped.ai1ec', true );
    }
  } );
} );
