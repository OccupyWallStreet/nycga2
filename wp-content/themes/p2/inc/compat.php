<?php
/**
 * Enable P2 to be compatible with prior versions of WordPress and PHP.
 *
 * @package P2
 */

if ( !function_exists( 'str_split' )):
function str_split($string,$string_length=1) {
	if (strlen($string)>$string_length || !$string_length) {
		do {
			$c = strlen($string);
			$parts[] = substr($string,0,$string_length);
			$string = substr($string,$string_length);
		} while($string !== false);
	} else {
		$parts    = array($string);
	}
	return $parts;
}
endif;

if ( !function_exists( 'str_ireplace' ) ) {
	function str_ireplace($name, $values, $replacement) {
		return str_replace($name, $values, $replacement);
	}
}

?>