<?php
class Sexy_Login_Admin {
	
	public function __construct() {
		
		add_action( 'admin_init', array( $this, 'sl_options_init' ) );
		add_action( 'admin_menu', array( $this, 'sl_options_add_page' ) );
		
	}
	
	function sl_options_init(){
		register_setting( 'sl_options', 'sl_options', array( $this, 'sl_options_validate' ) );
	}

	function sl_options_add_page() {
		add_options_page( __( 'Sexy Login Options', 'sl-domain' ), __( 'Sexy Login Options', 'sl-domain' ), 'manage_options', 'sl_options', array( $this, 'sl_options_do_page' ) );
	}

	function sl_options_do_page() {

		$sl_options			= get_option( 'sl_options' );
		$captcha_enabled	= ( $sl_options['enable_captcha'] ) ? 'block' : 'none';
		$login_custom_url	= ( $sl_options['redirect_login'] == 'current' ) ? 'none;' : 'block;' ;
		$logout_custom_url	= ( $sl_options['redirect_logout'] == 'current' ) ? 'none;' : 'block;' ;
		?>
		
		<div class="wrap">
		
			<div id="icon-tools" class="icon32"></div>
			
			<p><h2>Sexy Login Options</h2></p>
			
			<form method="post" action="options.php">
			
				<?php settings_fields( 'sl_options' ); ?>
				
				<p>
					<table class="widefat" style="width:700px !important">
						<thead>
							<th colspan="2"><?php esc_html_e( 'ReCaptcha' ); ?></th>
						</thead>
						<tbody>
							<tr>
								<td width="150px" >
									<label for="enable-captcha"><?php esc_html_e( 'Enable Captcha', 'sl-domain' ); ?></label>
								</td>
								<td>
									<input type="checkbox" name="sl_options[enable_captcha]" id="enable-captcha" value="1" <?php checked( '1', $sl_options['enable_captcha'] ); ?> onClick="slShowCaptchaOptions( this.form );"/>
								</td>
							</tr>
							<tr class="captcha-options" style="display: <?php esc_attr_e( $captcha_enabled ); ?>;">
								<td colspan="2" >
									<p><?php esc_html_e( 'The next keys are required before you are able to use ReCaptcha. You can get the keys in', 'sl-domain' ); ?> <a href="<?php $uri = parse_url( get_option( 'siteurl' ) ); echo recaptcha_get_signup_url( $uri['host'], 'sexy-login' );?>" ><?php echo recaptcha_get_signup_url( $uri['host'], 'sexy-login' ); ?></a>
									<br /><?php esc_html_e( 'Note: the keys are not interchangeable.', 'sl-domain' ); ?></p>
								</td>
							</tr>
							<tr class="captcha-options" style="display: <?php esc_attr_e( $captcha_enabled ); ?>;">
								<td width="150px">
									<label for="recaptcha_public_key"><?php esc_html_e( 'Public Key' ); ?></label>
								</td>
								<td>
									<input type="text" name="sl_options[recaptcha_public_key]" id="recaptcha_public_key" size="90" value="<?php echo $sl_options['recaptcha_public_key']?>" />
								</td>
							</tr>
							<tr class="captcha-options" style="display: <?php esc_attr_e( $captcha_enabled ); ?>;">
								<td width="150px">
									<label for="recaptcha_private_key"><?php esc_html_e( 'Private Key' ); ?></label>
								</td>
								<td>
									<input type="text" name="sl_options[recaptcha_private_key]" id="recaptcha_private_key" size="90" value="<?php echo $sl_options['recaptcha_private_key']?>" />
								</td>
							</tr>
						</tbody>
					</table>
				</p>
				
				<p>
					<table class="widefat" style="width:700px!important">
						<thead>
							<th colspan="2"><?php esc_html_e( 'Sexy Login Wrap', 'sl-domain' ); ?></th>
						</thead>
						<tr>
							<td width="150px">
								<label for="wrap_width"><?php esc_html_e( 'Width' ); ?></label>
							</td>
							<td>
								<input type="number" min="140" max="240" name="sl_options[wrap_width]" id="wrap_width" value="<?php esc_attr_e( $sl_options['wrap_width'] );?>" />
							</td>
						</tr>
					</table>
				</p>
				
				<p>
					<table class="widefat" style="width:700px!important">
						<thead>
							<th colspan="2"><?php esc_html_e( 'Avatar' ); ?></th>	
						</thead>
						<tr>
							<td width="150px">
								<label for="show_avatar"><?php esc_html_e( 'Show Avatar', 'sl-domain' ); ?></label>
							</td>
							<td >
								<input type="checkbox" name="sl_options[show_avatar]" id="show_avatar" value="1" <?php checked( '1', $sl_options['show_avatar'] ); ?> />
							</td>
								
						</tr>
						<tr>
							<td width="150px">
								<label for="avatar_size"><?php esc_html_e( 'Avatar Size', 'sl-domain' ); ?></label>
							</td>
							<td>
								<input type="number" min="1" max="220" name="sl_options[avatar_size]" id="avatar_size" value="<?php esc_attr_e( $sl_options['avatar_size'] );?>" />
							</td>
						</tr>
					</table>
				</p>
				<p>
				
					<table class="widefat" style="width:700px!important">
						<thead>
							<th colspan="2"><?php esc_html_e( 'Buttons', 'sl-domain' ); ?></th>
						</thead>
						<tr>
							<td width="150px">
								<label for="show_dashboard"><?php esc_html_e( 'Show "Dashboard"', 'sl-domain' ); ?></label>
							</td>
							<td >
								<input type="checkbox" name="sl_options[show_dashboard]" id="show_dashboard" value="1" <?php checked( '1', $sl_options['show_dashboard'] ); ?> />
							</td>
						</tr>
						<tr>
							<td width="150px">
								<label for="show_profile"><?php esc_html_e( 'Show "Edit My Profile"', 'sl-domain' ); ?></label>
							</td>
							<td>
								<input type="checkbox" name="sl_options[show_profile]" value="1" id="show_profile" <?php checked( '1', $sl_options['show_profile'] ); ?> />
							</td>
						</tr>
					</table>
				</p>
				<p>
					<table class="widefat" style="width:700px!important">
						<thead>
							<th colspan="3"><?php esc_html_e( 'Redirect', 'sl-domain' ); ?></th>
						</thead>
						<tr>
							<td width="150px">
								<label for="sl-redirect-login"><?php esc_html_e( 'Login redirect to', 'sl-domain' ); ?></label>
							</td>
							<td>
								<select id="sl-redirect-login" name="sl_options[redirect_login]" onChange="slShowCustomUrl( this.id, this.form )">
									<option value="current" <?php selected( $sl_options['redirect_login'], 'current' ); ?> ><?php esc_html_e( 'Current URL', 'sl-domain' ); ?></option>
									<option value="custom" <?php selected( $sl_options['redirect_login'], 'custom' ); ?> ><?php esc_html_e( 'Custom URL', 'sl-domain' ); ?></option>
								</select>
							</td>
							<td>
								<input id="sl-redirect-login-url" style="display: <?php esc_attr_e( $login_custom_url ); ?>;" type="text" name="sl_options[redirect_login_url]" size="65" value="<?php echo $sl_options['redirect_login_url']?>" />
							</td>
						</tr>
						<tr>
							<td width="150px">
								<label for="sl-redirect-logout"><?php esc_html_e( 'Logout redirect to', 'sl-domain' ); ?></label>	
							</td>
							<td>
								<select id="sl-redirect-logout" name="sl_options[redirect_logout]" onChange="slShowCustomUrl( this.id, this.form )">
									<option value="current" <?php selected( $sl_options['redirect_logout'], 'current' ); ?> ><?php esc_html_e( 'Current URL', 'sl-domain' ); ?></option>
									<option value="custom" <?php selected( $sl_options['redirect_logout'], 'custom' ); ?> ><?php esc_html_e( 'Custom URL', 'sl-domain' ); ?></option>
								</select>
							</td>
							<td>
								<input id="sl-redirect-logout-url" style="display: <?php esc_attr_e( $logout_custom_url ); ?>;" type="text" name="sl_options[redirect_logout_url]" size="65" value="<?php echo $sl_options['redirect_logout_url']?>" />
							</td>
						</tr>
					</table>
				</p>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
				</p>
			</form>
		</div>
		<?php	
	}

	function sl_options_validate( $input ) {

		$input['enable_captcha']		= ( 1 == $input['enable_captcha'] ? 1 : 0 );
		$input['show_dashboard']		= ( 1 == $input['show_dashboard'] ? 1 : 0 );
		$input['show_profile']			= ( 1 == $input['show_profile'] ? 1 : 0 );
		$input['show_avatar']			= ( 1 == $input['show_avatar'] ? 1 : 0 );
		$input['redirect_login']		=  esc_html( $input['redirect_login'] );
		$input['redirect_logout']		=  esc_html( $input['redirect_logout'] );
		$input['redirect_login_url']	=  esc_url( $input['redirect_login_url'] );
		$input['redirect_logout_url']	=  esc_url( $input['redirect_logout_url'] );
		$input['recaptcha_public_key']	=  esc_html( $input['recaptcha_public_key'] );
		$input['recaptcha_private_key']	=  esc_html( $input['recaptcha_private_key'] );
		
		$input['avatar_size']			= ( (  $input['avatar_size'] <= 220 && is_numeric( $input['avatar_size'] ) )  ? esc_html( $input['avatar_size'] ) : 220 );
		$input['wrap_width']			= ( (  $input['wrap_width'] <= 240 && is_numeric( $input['wrap_width'] ) )  ? esc_html( $input['wrap_width'] ) : 240 );
		
		if ( $input['enable_captcha'] == 1  && ( empty( $input['recaptcha_public_key'] ) || empty( $input['recaptcha_private_key'] ) ) ){
		
			$input['enable_captcha']	= 0;
			$update_message				= __( 'ReCaptcha was disabled because you have not entered any key', 'sl-domain' );
			add_settings_error( 'general', 'settings_updated', $update_message, 'error');
			
		}

		return $input;
	}
	
	function upgrade() {
	
		$config				= get_option( 'sl_config' );
		$current_version	=  isset( $config['version'] ) ? $config['version'] : '1.0';

		if ( version_compare( $current_version, SL_VERSION, '==' ) )
			return;
			
		if ( version_compare( $current_version, '2.0', '<' ) ) {
		
			delete_option( 'widget_sexy_login_widget' );
			
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
			
			update_option( 'sl_options', $defaults );
			
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

		} // END < 2.0

		$config				= get_option( 'sl_config' );
		$config['version']	= SL_VERSION;
		update_option( 'sl_config', $config );

	}
	
}


?>