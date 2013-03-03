<?php
/**
 * Utility functions.
 *
 * @package P2
 * @since unknown
 */

function p2_maybe_define( $constant, $value, $filter = '' ) {
	if ( defined( $constant ) )
		return;

	if ( !empty( $filter ) )
		$value = apply_filters( $filter, $value );

	define( $constant, $value );
}

?>