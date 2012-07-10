<?php
/**
 * Slide show functionality,
 */



$wpui_options = get_option( 'wpUI_options' );

add_shortcode( 'wpui_slides', 'wpui_slides' );

function wpui_slides( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'engine'	=>	'wp-ui'		
	), $atts ));
	
	static $wpslnum = 0;
	$wpslnum++;
	
	if ( isset( $wpui_options[ 'bleeding' ] ) && $wpui_options[ 'bleeding' ] != 'on' )
		return false;

	$output = '';
	
	$output .= '<div id="wpui-slideshow-' . $wpslnum . '" class="wpui_slideshow">' . do_shortcode( $content ) . '</div>';
	
	$output .= '<script type="text/javascript">';
	$output .= 'jQuery( function() {';
	$output .= 'jQuery( "#wpui-slideshow-' . $wpslnum . '" ).kSlides();';
	$output .= '});';
	$output .= '</script>';

	return $output;
}


add_shortcode( 'wpui_slide', 'wpui_slide_shortcode' );
// Shortcode wpui_slide
function wpui_slide_shortcode( $atts, $content=null ) {	
	extract( shortcode_atts( array(
		'image'			=>	false,
		'image_title'	=>	false
	), $atts ) );
	
	if ( $image_title )
		$imagea = wpui_get_media_item( $image_title );
		$image = $imagea['image'];
	
	if ( ! $image || ! function_exists( 'getimagesize' ) ) return false;
	
	if ( is_array( $imagea ) )
		$img_title = $imagea[ 'title' ];
	else {
		$filename = substr(strrchr( $image, '/' ), 1 );
		$filename = str_ireplace( strrchr( $filename, '.' ), '', $filename );
		$img_title = $filename;
	}
	
	$samp = getimagesize( $image );
	if ( ! is_array( $samp ) ) return "Not a valid image.";		
	
	$output = '';
	
	$output .= '<h3 class="wp-tab-title">' . $imagea[ 'title' ] . '</h3>';
	$output .= '<div class="wpui-slide wp-tab-content">';
	
	$output .= '<img src="' . $image . '" />';
	
	$output .= '<div class="wpui_image_description">' . $content . '</div>';
	
	$output .= '</div><!-- end .wpui-slide -->';
		
	return $output;
} // end function wpui_slide_shortcode





?>