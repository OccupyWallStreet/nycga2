<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();
	
require_once( 'sl-config.php' );

global $wpdb;

delete_option( 'sl_options' );

delete_option( 'sl_config' );

delete_option( 'widget_sexy_login_widget' );

$delete_metas	= '
	DELETE
	FROM ' . $wpdb->usermeta . ' 
	WHERE ' . $wpdb->usermeta . '.meta_key = "sl_lostpwd_time"
';

$wpdb->query( $delete_metas );

$wpdb->query( 'DROP TABLE IF EXISTS ' . SL_LOGIN_TABLE . ';' );

?>