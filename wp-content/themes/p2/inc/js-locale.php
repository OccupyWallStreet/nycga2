<?php

/**
 * Transforms the WP_Locale translations for the wp.locale JavaScript class.
 *
 * Used by P2 and WordPress.com support forums.
 *
 * @param $locale WP_Locale - A locale object.
 * @param $json_encode bool - Whether to encode the result. Default true.
 * @return string|array     - The translations object.
 */
function get_js_locale( $locale, $json_encode = true ) {
	$js_locale = array(
		'month'         => array_values( $locale->month ),
		'monthabbrev'   => array_values( $locale->month_abbrev ),
		'weekday'       => array_values( $locale->weekday ),
		'weekdayabbrev' => array_values( $locale->weekday_abbrev ),
	);

	if ( $json_encode )
		return json_encode( $js_locale );
	else
		return $js_locale;
}

?>