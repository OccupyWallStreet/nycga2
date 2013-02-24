<?php
function sl_configure_database() {
	
	// Sexy Login Config
	$exist_sexy_login	= get_option( 'widget_sexy_login_widget' );
	$old_config			= get_option( 'sl_config' );
	$defaults			= array( 'version' => SL_VERSION );
	$new_config			= wp_parse_args( $old_config, $defaults );
	
	if ( $exist_sexy_login && !$old_config )
		delete_option( 'widget_sexy_login_widget' );
	
	update_option( 'sl_config', $new_config );
	
	// Sexy Login Options
	$defaults	= array(
		'enable_captcha'		=> FALSE,
		'recaptcha_public_key'	=> '',
		'recaptcha_private_key'	=> '',
		'show_dashboard'		=> TRUE,
		'show_profile'			=> TRUE,
		'show_avatar'			=> TRUE,
		'avatar_size'			=> 220,
		'wrap_width'			=> 240,
		'redirect_login'		=> 'current',
		'redirect_login_url'	=> '',
		'redirect_logout'		=> 'current',
		'redirect_logout_url'	=> ''
	);
	$old_options	= get_option( 'sl_options' );
	$new_options	= wp_parse_args( $old_options, $defaults );
	
	update_option( 'sl_options', $new_options );
	
	global $wpdb;
	
	if( $wpdb->get_var( 'SHOW TABLES LIKE "' . SL_LOGIN_TABLE . '"' ) != SL_LOGIN_TABLE ) {
	
		$create_table	= 'CREATE TABLE ' . SL_LOGIN_TABLE . ' (
			ip varchar(255) NOT NULL,
			last_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			login_attempts tinyint DEFAULT 1,
			PRIMARY KEY  (ip)
		);';
		$wpdb->query( $create_table );
		
	}
	
}
?>