<?php
/*
Plugin Name: Sexy Login
Plugin URI: http://wordpress.org/extend/plugins/sexy-login/
Description: The sexiest widget login for Wordpress!
Version: 2.0
Author: OptimalDevs
Author URI: http://optimaldevs.com/
*/

require_once( 'sl-config.php' );
require_once( 'inc/activation.php' );

register_activation_hook( __FILE__, 'sl_configure_database' );

add_action( 'widgets_init', 'sexy_login_init' );	
	
function sexy_login_init() {

	load_plugin_textdomain( 'sl-domain', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	
	require_once( 'inc/class-sexy-login-attempts.php' );
	require_once( 'inc/class-sexy-login-widget.php' );
			
	register_widget( 'Sexy_Login_Widget' );	
		
	if ( is_active_widget( false, false, 'sexy_login_widget', true ) ) {
		
		if ( is_admin() ) {
		
			require_once( 'inc/ajax.php' );
			
			add_action( 'wp_ajax_sexy_login_hook', 'sexy_login_ajax' );
			add_action( 'wp_ajax_nopriv_sexy_login_hook', 'sexy_login_ajax' );
			
			add_action( 'wp_ajax_sexy_register_hook', 'sexy_register_ajax' );
			add_action( 'wp_ajax_nopriv_sexy_register_hook', 'sexy_register_ajax' );
			
			add_action( 'wp_ajax_sexy_lostpwd_hook', 'sexy_lostpwd_ajax' );
			add_action( 'wp_ajax_nopriv_sexy_lostpwd_hook', 'sexy_lostpwd_ajax' );
			
		}
		
		if ( ! is_admin() ) {
			
			$sl_options	= get_option( 'sl_options' );
			
			if ( $sl_options['redirect_logout'] == 'custom' && $sl_options['redirect_logout_url'] != '' ) {
				
				function sl_allow_ms_parent_redirect( $allowed ) {
					
					$sl_options	= get_option( 'sl_options' );
					$uri		= parse_url( $sl_options['redirect_logout_url'] );
					$allowed[] 	= $uri['host'];
					return $allowed;
		
				}
				
				add_filter( 'allowed_redirect_hosts', 'sl_allow_ms_parent_redirect' );
				
			}
		
			if ( ! function_exists( 'recaptcha_get_html' ) )
				require_once( 'inc/lib/recaptchalib.php' );
		
			$ssl_plugins_url	= ( is_ssl() ) ? str_replace( 'http://', 'https://', plugin_dir_url( __FILE__ ) ) : plugin_dir_url( __FILE__ );
			$recaptcha_js		= ( is_ssl() ) ? 'https://www.google.com/recaptcha/api/js/recaptcha_ajax.js' : 'http://www.google.com/recaptcha/api/js/recaptcha_ajax.js';
			
			wp_register_script( 'blockui',  $ssl_plugins_url . 'js/jquery.blockUI.js', array( 'jquery' ), '2.53' );
			wp_register_script( 'sl-javascript', $ssl_plugins_url . 'js/sexy-login.js', array( 'jquery' ), '2.0' );
			wp_register_script( 'recaptcha', $recaptcha_js, array(), '1.0' );

			wp_enqueue_script( 'recaptcha' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'blockui' );
			wp_enqueue_script( 'sl-javascript' );
			
			wp_register_style( 'sl-style', $ssl_plugins_url . 'style.css', array(), '1.0', 'all' );
			
			wp_enqueue_style( 'sl-style' );
			
			wp_localize_script( 
				'sl-javascript', 
				'sexy_loginl_data', 
				array( 
					'ajaxurl'		=> admin_url( 'admin-ajax.php' ), 
					'loadingurl'	=> $ssl_plugins_url . 'img/ajax-loader.gif',
					'public_key'	=> $sl_options['recaptcha_public_key'],
					'captcha_enter'	=> __( 'Enter the two words above', 'sl-domain' ),
					'captcha_get'	=> __( 'Get another Captcha', 'sl-domain' )
				) 
			);
			
		} // END ! is_admin()
		
    } // END is_active_widget()
	
	if ( is_admin() ) {
	
		wp_register_script( 'sl-admin-js', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), '1.0' );
		wp_enqueue_script( 'sl-admin-js' );
		
		require_once( 'inc/admin/class-sexy-login-admin.php' );
		
		if ( ! function_exists( 'recaptcha_get_html' ) )
			require_once( 'inc/lib/recaptchalib.php' );
			
		$admin_init	= new Sexy_Login_Admin();
		$admin_init->upgrade();
		
	} // END is_admin()
	
}
?>