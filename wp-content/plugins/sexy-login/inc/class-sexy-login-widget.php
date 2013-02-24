<?php
class Sexy_Login_Widget extends WP_Widget {
	
	private static $instance;
	
	function Sexy_Login_Widget() {

		$this->WP_Widget( 'sexy_login_widget', 'Sexy Login', array( 'classname' => 'sexy_login_widget', 'description' => 'A simple widget for login in Wordpress.' ) );
		
	}
	
	function widget( $args, $instance ) {
	
		if ( empty( $this->instance ) )
			$this->instance = true;
		else
			return;
			
		extract( $args, EXTR_SKIP );
		
		$title			= empty( $instance['title'] ) ? ' ' : apply_filters( 'widget_title', $instance['title'] );
		$sl_options		= get_option( 'sl_options' );

		echo $before_widget;
				
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
		?>
		
		<div id="sexy-login-show-error" style="max-width: <?php esc_attr_e( $sl_options['wrap_width'] ); ?>px;"></div>
   
		<div id="sexy-login-wrap" style="max-width: <?php esc_attr_e( $sl_options['wrap_width'] ); ?>px;">
			
		<?php
		
		if ( is_user_logged_in() ) {
			
			global $current_user;
			get_currentuserinfo();
			$buttons	= array();
			
			if ( $sl_options['show_dashboard'] )
				$buttons['Dashboard']	= admin_url();

			if ( $sl_options['show_profile'] )
				$buttons['Edit My Profile']	=  admin_url( 'profile.php' ); 

			$buttons['Log Out']	= ( $sl_options['redirect_logout'] == 'custom' && $sl_options['redirect_logout_url'] != '' ) ? wp_logout_url( $sl_options['redirect_logout_url'] ) : wp_logout_url( $this->current_url() );			
			
			?> 
			<div id="sexy-login-user" > 
				<?php
				
				echo get_avatar( $current_user->ID, $sl_options['avatar_size'] );
				
				foreach ( $buttons as $text => $href ) {
					echo '<a href="' . esc_attr( $href ) . '">' . esc_html__( $text ) . '</a>';
				}
				
			?></div><?php
			
		} else {
		
			$attempts		= new Sexy_Login_Attempts();
			$login_captcha	= ( $sl_options['enable_captcha'] && $attempts->get_attempts() >= SL_LOGIN_ATTEMPTS ) ? 'block' : 'none';
			$redirect_to	= ( $sl_options['redirect_login'] == 'custom' ) ? $sl_options['redirect_login_url'] : $this->current_url();
			$can_register	= get_option('users_can_register');
			?>
			<div id="sexy-login-tabs">
			
				<a class="sexy-login-tab selected" name="sl-tab-login" href="#" <?php if ( ! $can_register ) echo 'style="width: 100%;"'; ?> ><?php esc_html_e( 'Log in' ) ?></a>

				<?php if ( $can_register ): ?> 
					<a class="sexy-login-tab" style="border-left: 1px solid #ccc; margin-left: -1px;" name="sl-tab-register" href="<?php echo esc_url( site_url( 'wp-login.php?action=register' ) ); ?>" ><?php esc_html_e( 'Register' ) ?></a>
				<?php endif;?>
				
			</div>
			
			<div id="sexy-login-content-tab">

				<div id="sexy-login-register-form" class="sexy-login-form-wrap">
					
					<form id="sl-register-form" action="<?php echo esc_url( site_url( 'wp-login.php?action=register', 'login_post' ) ); ?>" method="post">
						<p>
							<!--[if IE ]><label for="user_login"><?php esc_html_e( 'Username' ); ?>: </label><![endif]-->
							<input type="text" name="user_login" size="20" id="user_login" placeholder="<?php esc_attr_e( 'Username' ); ?>"  tabindex="1"/>
						</p>
						<p>
							<!--[if IE ]><label for="user_email"><?php esc_html_e( 'E-mail' ); ?>: </label><![endif]-->
							<input type="text" name="user_email" size="25" id="user_email" placeholder="<?php esc_attr_e( 'E-mail' ); ?>"  tabindex="2"/>
						</p>
						<p>
							<div id="sexy-register-recaptcha" class="sexy-div-captcha" style="display: <?php echo ( $sl_options['enable_captcha'] ) ? esc_attr( 'block' ) : esc_attr( 'none' );?>;"></div>
							<input type="submit" name="user-submit" value="<?php esc_html_e( 'Register' ); ?>" />
							<?php wp_nonce_field( 'nonce', SL_NONCE_SECURITY ); ?>
							<input type="hidden" name="url" value="<?php echo esc_url( site_url( 'wp-login.php?action=register', 'login_post' ) ); ?>" />
							<input type="hidden" name="redirect_to" value="" />
						</p>
					</form>
					
				</div>
				
				<div id="sexy-login-lostpwd-form" class="sexy-login-form-wrap">
				
					<h3><?php esc_html_e( 'Lost your password?' );?></h3>
					
					<p><?php esc_html_e( 'Please enter your username or email address. You will receive a link to create a new password via email.' );?></p>
					
					<form id="sl-lostpwd-form" method="post" action="<?php echo site_url( 'wp-login.php?action=lostpassword', 'login_post' ) ?>">
						<p>
							<input type="text" name="user_login" size="20" id="user_login" placeholder="<?php esc_attr_e( 'Username or E-mail' ); ?>" />
						</p>
						<p>
							<input type="submit" name="user-submit" value="<?php esc_attr_e( 'Get New Password' ); ?>"/>
							<?php wp_nonce_field( 'nonce', SL_NONCE_SECURITY ); ?>
							<input type="hidden" name="redirect_to" value="" />
						<p>
					</form>
					
				</div>
				
				<div id="sexy-login-login-form" class="sexy-login-form-wrap" style="display: block;">
				
					<form id="sl-login-form" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
						<p>
							<!--[if IE ]><label for="user_login"><?php esc_html_e( 'Username' ); ?>:</label><![endif]-->
							<input type="text" name="log" id="user_login" size="20" placeholder="<?php esc_attr_e( 'Username' ); ?>"  tabindex="1" />
						</p>
						<p>
							<!--[if IE ]><label for="user_pass"><?php esc_html_e( 'Password' ); ?>:</label><![endif]-->
							<input type="password" name="pwd" id="user_pass" size="20" placeholder="<?php esc_attr_e( 'Password' ); ?>" tabindex="2" />
						</p>
						
						<div id="sexy-login-recaptcha" class="sexy-div-captcha" style="display: <?php echo esc_attr( $login_captcha ); ?>;"></div>
						
						<input type="submit" name="wp-submit" id="wp-submit" class="submit-button" value="<?php esc_attr_e( 'Log In' ); ?>">
						
						<input type="hidden" name="rememberme" id="rememberme" value="forever" />
						<input type="hidden" name="redirect_to" value="<?php esc_attr_e( $redirect_to ); ?>" />
						<?php wp_nonce_field( 'nonce', SL_NONCE_SECURITY ); ?>
						
					</form>
				
					<a name="sl-tab-lostpwd" href="<?php echo esc_url( site_url( 'wp-login.php?action=lostpassword', 'login_post' ) ); ?>" title="<?php esc_attr_e( 'Lost your password?' ); ?>" ><?php esc_html_e( 'Lost your password?' ) ?></a>
					
				</div> 
			
			</div> <!-- END sexy-login-content-tab -->

			<?php
		}
		
		echo '</div>';
		echo $after_widget;
		
	}
	
	function update( $new_instance, $old_instance ) {
	
		$instance			= $old_instance;
		$instance['title']	= strip_tags( $new_instance['title'] );
		return $instance;
		
	}
	
	function form( $instance ) {
		
		$instance		= wp_parse_args( ( array ) $instance, array( 'title' => 'Sexy Login' ) );
		$title 			= strip_tags( $instance['title'] );
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title' ); ?>: 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php esc_attr_e( $title ); ?>" />
			</label>
		</p>
		<?php
		
	}
	
	private function current_url() {

		$current_url	= ( force_ssl_admin() || is_ssl() ) ? 'https://' : 'http://';
		$current_url	.= esc_attr( $_SERVER['HTTP_HOST'] );
		$current_url	.= esc_attr( $_SERVER['REQUEST_URI'] );
		return strip_tags( $current_url );
	
	}
	
}
?>