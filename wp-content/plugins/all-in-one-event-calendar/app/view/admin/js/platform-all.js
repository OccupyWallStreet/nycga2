jQuery( function( $ ) {
  // Disable the "Front page" option in WP General Settings.
  if( $( 'body.options-reading-php' ).length ) {
    function disable_front_page_option() {
      $( '#page_on_front' ).attr( 'disabled', 'disabled' );
    }
    disable_front_page_option();
    $( '#front-static-pages input:radio' ).change(disable_front_page_option);
    $( '#page_on_front' ).after( '<span class="description">' + ai1ec_platform_all.page_on_front_description + '</span>' );
  }
});
