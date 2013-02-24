<?php 

global $wpdb;
define( 'SL_NONCE_SECURITY', 'sexy-login-nonce' );
define( 'SL_LOGIN_TABLE',  $wpdb->prefix . 'sexy_login' );
define( 'SL_LOGIN_ATTEMPTS', 3 );
define( 'SL_LOGIN_ATTEMPTS_LAPSE', 10 );
define( 'SL_LOSTPWD_TIME_LAPSE', 10 );
define( 'SL_VERSION', '2.0' );

?>
