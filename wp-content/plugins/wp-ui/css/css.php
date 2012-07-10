<?php
header( 'Content-type: text/css' );
header( 'Cache-Control: must-revalidate' );
$offset = 72000;
header( "Expires: " . gmdate( "D, d M Y H:i:s", time() + 72000) . " GMT");

$load_styles = addslashes( $_GET['styles'] );

$styles_arr = explode( "|", $load_styles );

if ( $load_styles == 'all' ) {
	@readfile( 'wpui-all.css' );
	exit;
} else {
	if ( !is_array( $styles_arr ) ) exit;
	foreach( $styles_arr as $styles ) {
		@readfile( $styles . '.css' ) . "\n\n";
	}
}
exit; // Dont remove.
?>