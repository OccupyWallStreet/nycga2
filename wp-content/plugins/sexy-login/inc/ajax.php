<?php
function sexy_login_ajax() {

	check_ajax_referer(  'nonce', SL_NONCE_SECURITY );
	
	$redirect_to	= $_REQUEST['redirect_to'];
	$sl_options		= get_option( 'sl_options' );
	$attempts		= new Sexy_Login_Attempts();
	$captcha_test	= false;
	$result			= array(
		'success'		=> 0,
		'redirect'		=> $redirect_to,
		'error'			=> esc_html( 'ERROR' ),
		'captcha'		=> false,
		'public_key'	=> $sl_options['recaptcha_public_key']
	);
	
	if ( $sl_options['enable_captcha'] && $attempts->get_attempts() >= SL_LOGIN_ATTEMPTS )
		$captcha_test	= recaptcha_check_answer( $sl_options['recaptcha_private_key'], $_SERVER['REMOTE_ADDR'], $_REQUEST['recaptcha_challenge_field'], $_REQUEST['recaptcha_response_field'] );
	
	if ( $captcha_test && ! $captcha_test->is_valid ) {

		$result['error']	= esc_html__( 'Invalid Captcha: Try again.', 'sl-domain' );
		$result['captcha'] 	= true;
		
	} else {

		$creds	= array();
		$creds['user_login']	= $_REQUEST['log'];
		$creds['user_password']	= $_REQUEST['pwd'];
		$creds['remember']		= ( isset( $_REQUEST['rememberme'] ) ) ? $_REQUEST['rememberme'] : false;
		$secure_cookie			= ( force_ssl_admin() ) ? true : false;
		
		if ( ! $secure_cookie ) {
			$user_name = sanitize_user( $_REQUEST['log'] );
			if ( $user = get_user_by('login',  $user_name ) ) {
				if ( get_user_option('use_ssl', $user->ID) ) {
					$secure_cookie = true;
					force_ssl_admin(true);
				}
			}
		}
		
		if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
			$secure_cookie = false;
		
		$login = wp_signon($creds, $secure_cookie);
			
		if ( ! is_wp_error( $login ) ){
		
			$result['success']		= 1;
			$result['redirect']		= $redirect_to;
			$attempts->delete_attempts();
			
		} else {
		
			$result['error']	= ( $login->errors ) ? $login->get_error_message() : '<strong>ERROR</strong>: ' . esc_html__( 'Please enter your username and password to login.', 'sl-domain' );
			$result['captcha']	= ( $sl_options['enable_captcha'] && $attempts->update_attempts() >= SL_LOGIN_ATTEMPTS ) ? true : false;
		
		}
		
	}
	
	header( 'content-type: application/json; charset=utf-8' );
	
	echo json_encode( $result );
	
	die();
	
}

function sexy_register_ajax() {
	
	check_ajax_referer(  'nonce', SL_NONCE_SECURITY );
	
	$result			= array();
	$sl_options		= get_option( 'sl_options' );
	$captcha_test	= false;
	
	if ( $sl_options['enable_captcha'] )
		$captcha_test	= recaptcha_check_answer( $sl_options['recaptcha_private_key'], $_SERVER['REMOTE_ADDR'], $_REQUEST['recaptcha_challenge_field'], $_REQUEST['recaptcha_response_field'] );
	
	if ( $captcha_test && ! $captcha_test->is_valid ) {
	
		$result['succes']	= 0;
		$result['error']	= esc_html__( 'Invalid Captcha: Try again.', 'sl-domain' );
		$result['captcha'] 	= true;
		
	} else {
	
		$user_login				= $_REQUEST['user_login'];
		$sanitized_user_login	= sanitize_user( $user_login );
		$user_email				= apply_filters( 'user_registration_email', $_REQUEST['user_email'] );
		$errors					= new WP_Error();
		
		// Check the username
		if ( $sanitized_user_login == '' ) {
			$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.' ) );
		} elseif ( ! validate_username( $user_login ) ) {
			$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
			$sanitized_user_login = '';
		} elseif ( username_exists( $sanitized_user_login ) ) {
			$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.' ) );
		}

		// Check the e-mail address
		if ( $user_email == '' ) {
			$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ) );
		} elseif ( ! is_email( $user_email ) ) {
			$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
			$user_email = '';
		} elseif ( email_exists( $user_email ) ) {
			$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
		}

		do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

		$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

		if ( $errors->get_error_code() ){
		
			$result['success']	= 0;
			$result['error'] 	= $errors->get_error_message();
			
		} else {
		
			$user_pass	= wp_generate_password( 12, false);
			$user_id	= wp_create_user( $sanitized_user_login, $user_pass, $user_email );
			
			if ( ! $user_id ) {
			
				$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
				$result['success']	= 0;
				$result['error'] 	= $errors->get_error_message();
				
			} else{

				update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

				wp_new_user_notification( $user_id, $user_pass );
				
				$result['success']	= 1;
				$result['message']	= esc_html__( 'Registration complete. Please check your e-mail.' );
				
			}
			
		}
	
	}

	header( 'content-type: application/json; charset=utf-8' );
	
	echo json_encode( $result );
	
	die();
	
}

function sexy_lostpwd_ajax() {

	check_ajax_referer( 'nonce', SL_NONCE_SECURITY );
	
	$result		= array();
	
	global $wpdb, $current_site;
	$user_login	= $_REQUEST['user_login'];
	$errors		= new WP_Error();

	if ( empty( $user_login ) ) {
		$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.'));
	} else if ( strpos( $user_login, '@' ) ) {
		$user_data = get_user_by( 'email', trim( $user_login ) );
		if ( empty( $user_data ) )
			$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
	} else {
		$login = trim( $user_login );
		$user_data = get_user_by('login', $login);
	}

	do_action('lostpassword_post');

	if ( $errors->get_error_code() ){
	
		$result['success']	= 0;
		$result['error'] 	= $errors->get_error_message();
		
	} else {
	
		if ( ! $user_data ) {
		
			$errors->add( 'invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.' ) );
			$result['success']	= 0;
			$result['error'] 	= $errors->get_error_message();
			
		} else {

			// redefining user_login ensures we return the right case in the email
			$user_login	= $user_data->user_login;
			$user_email	= $user_data->user_email;

			do_action('retreive_password', $user_login);  // Misspelled and deprecated
			do_action('retrieve_password', $user_login);

			$allow = apply_filters('allow_password_reset', true, $user_data->ID);

			if ( ! $allow ){
			
				$error = new WP_Error('no_password_reset', __('Password reset is not allowed for this user'));
				$result['error'] 	= $errors->get_error_message();
				$result['success']	= 0;
				
			} else if ( is_wp_error($allow) ){
			
				$result['error'] 	= $allow;
				$result['success']	= 0;
				
			} else {
				
				if ( ! empty( $user_data->sl_lostpwd_time ) && ( $user_data->sl_lostpwd_time >= ( intval( current_time( 'timestamp' ) ) - ( SL_LOSTPWD_TIME_LAPSE * 60 ) ) ) ) {
				
					$result['success']	= 0;
					$result['error']	= __( 'Before requesting a new password must wait', 'sl-domain' ) . ' <strong>' . SL_LOSTPWD_TIME_LAPSE . ' ' . __( 'minutes', 'sl-domain' ) . '</strong>';
				
				} else {
				
					$key	= $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
				
					if ( empty($key) ) {
						// Generate something random for a key...
						$key = wp_generate_password(20, false);
						do_action('retrieve_password_key', $user_login, $key);
						// Now insert the new md5 key into the db
						$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
					}
					
					$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
					$message .= network_home_url( '/' ) . "\r\n\r\n";
					$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
					$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
					$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
					$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

					if ( is_multisite() )
						$blogname = $GLOBALS['current_site']->site_name;
					else
						// The blogname option is escaped with esc_html on the way into the database in sanitize_option
						// we want to reverse this for the plain text arena of emails.
						$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

					$title = sprintf( __('[%s] Password Reset'), $blogname );

					$title = apply_filters('retrieve_password_title', $title);
					$message = apply_filters('retrieve_password_message', $message, $key);

					if ( $message && !wp_mail($user_email, $title, $message) ){
					
						$result['success']	= 0;
						$result['error']	= __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...');
					
					} else {
					
						$result['success']	= 1;
						$result['message']	= esc_html__( 'Check your e-mail for your new password.' );
						update_user_meta( $user_data->ID, 'sl_lostpwd_time', current_time( 'timestamp' ) );
						
					}
					
				} // END 10 minutes
				
			}
			
		}
		
	}
	
	header( 'content-type: application/json; charset=utf-8' );
	
	echo json_encode( $result );
	
	die();
}
?>