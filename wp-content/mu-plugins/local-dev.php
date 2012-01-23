<?php

if ( defined( 'IS_LOCAL_ENV' ) && IS_LOCAL_ENV ) :
	/**
	 * Fixes Network Admin redirect issue when not using real DNS
	 */
	function nycga_redirect_network_admin_request( $v ) {
		return false;
	}
	add_filter( 'redirect_network_admin_request', 'nycga_redirect_network_admin_request' );
	
	/**
	 * Adds 'local environment' tab. This can be especially helpful when you manually modify
	 * your hosts file, and it's otherwise hard to tell the difference between the production
	 * site and your dev environment
	 */
	function nycga_local_env_flag() {
		?>

		<style type="text/css">
			#local-env-flag {
				position: fixed;
				right: 0;
				bottom: 50px;
				width: 200px;
				padding: 10px 15px;
				text-align: center;
				background: #f00;
				color: #fff;
				font-size: 1.5em;
				line-height: 1.8em;
				border: 2px solid #666;
				z-index: 1000;
			}
		</style>

		<div id="local-env-flag">
		<?php 
		if (defined('ENV_TAB')) 
			echo ENV_TAB;
		else
			echo 'DEV ENVIRONMENT';
		?>
		</div>

		<?php
	}
	add_action( 'wp_footer', 'nycga_local_env_flag' );
	add_action( 'admin_footer', 'nycga_local_env_flag' );
endif;

?>