<?php if ( is_user_logged_in() ) : ?>

	<?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?>
	<a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'bp-scholar' ) ?></a>
<?php else : ?>

		<?php do_action( 'bp_before_sidebar_login_form' ) ?>
		<form name="login-form" id="login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login' ) ?>" method="post">
			<label><?php _e( 'Username', 'bp-scholar' ) ?>
			<input type="text" name="log" id="userbar_user_login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="12" tabindex="97" /></label>

			<label><?php _e( 'Password', 'bp-scholar' ) ?>
			<input type="password" name="pwd" id="userbar_user_pass" class="input" value="" size="12" tabindex="98" /></label>

			<span class="forgetmenot"><label><input name="rememberme" type="checkbox" id="userbar_rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'bp-scholar' ) ?></label></span>

			<?php do_action( 'bp_sidebar_login_form' ) ?>

			<input type="submit" name="wp-submit" id="userbar_wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" />
			<input type="hidden" name="redirect_to" value="<?php bp_root_domain() ?>" />
			<input type="hidden" name="testcookie" value="1" />
					<?php if ( bp_get_signup_allowed() ) : ?>
						<?php printf( __( '<a href="%s" title="Create an account" class="button">Sign up</a>', 'bp-scholar' ), site_url( BP_REGISTER_SLUG . '/' ) ) ?>
					<?php endif; ?>
		</form>
		
		<?php do_action( 'bp_after_sidebar_login_form' ) ?>
<?php endif; ?>