<?php
if ( !function_exists( 'pulse_press_returner' ) ) {
	function pulse_press_returner( $value ) {
        return create_function( '', 'return '.var_export( $value, true ).';' );
	}
}

if ( !function_exists( 'pulse_press_lambda' ) ) {
function pulse_press_lambda( $args, $expression, $locals = array() ) {
        $export_call = $locals? 'extract( '.var_export( $locals, true ).', EXTR_PREFIX_SAME, "ext");' : '';
        return create_function( $args, $export_call.' return '.$expression.';' );
}
}

add_action( 'init', array( 'PulsePress', 'init' ) );

class PulsePress {
	function init() {
	    load_theme_textdomain( 'pulse_press', get_template_directory() . '/languages' );

		add_filter( 'the_content', 'make_clickable' );

		if ( isset( $_REQUEST['pulse_pressajax'] ) ) {
			require_once( PULSEPRESS_INC_PATH . '/ajax.php' );
			PulsePressAjax::dispatch();
			die();
		}

		if ( function_exists( 'is_site_admin' ) && !is_site_admin() ) return;
	}



	/**
	 * Make sure the URL is loaded from the same domain as the frontend
	 */
	function url_filter( $url, $path = '' ) {
		$parsed = parse_url( $url );
		$host = ( isset( $parsed['host'] ) ) ? $parsed['host'] : '';
		if (!false === strpos( 'http', $url) )
			return preg_replace( '|https?://'.preg_quote( $host ).'|', home_url(), $url );
		return $url;
	}

	function admin_url( $path ) {
		return PulsePress::url_filter( admin_url( $path ) );
	}

	function make_media_urls( $string ) {
		// This line does not work in .org
		return str_replace( 'media-upload.php?', PulsePress::admin_url( 'media-upload.php?pulse_press-upload=true&' ), $string );
	}
}
