<?php

if ( defined( 'IS_LOCAL_ENV' ) && IS_LOCAL_ENV ) :
	/**
	 * Fixes Network Admin redirect issue when not using real DNS
	 */
	function nycga_redirect_network_admin_request( $v ) {
		return false;
	}
	add_filter( 'redirect_network_admin_request', 'nycga_redirect_network_admin_request' );
endif;

?>