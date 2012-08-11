<?php
	$bp_text = get_option('dev_businessfeature_bp_text');
?>
		<div id="memberpanel">
		<?php if ( is_user_logged_in() ) : ?>

			<?php do_action( 'bp_before_sidebar_me' ) ?>
			
			<div class="label-box">	<h3>
				<?php echo stripslashes($bp_text); ?>
			</h3></div>

			<div id="sidebar-me">
				<a href="<?php echo bp_loggedin_user_domain() ?>">
					<?php bp_loggedin_user_avatar( 'type=thumb&width=40&height=40' ) ?>
				</a>

			<h4><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h4>
			<a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'business-feature') ?></a>

				<?php do_action( 'bp_sidebar_me' ) ?>
			</div>

			<?php do_action( 'bp_after_sidebar_me' ) ?>

			<?php if ( function_exists( 'bp_message_get_notices' ) ) : ?>
				<?php bp_message_get_notices(); /* Site wide notices to all users */ ?>
			<?php endif; ?>

		<?php else : ?>
			
						<?php do_action( 'bp_before_sidebar_login_form' ) ?>
						
						<div class="label-box">	<h3>User Login:</h3>
							<span>
									<?php _e( 'To start connecting please log in first.', 'business-feature' ) ?>
									<?php if ( bp_get_signup_allowed() ) : ?>
										<?php printf( __( ' You can also <a href="%s" title="Create an account">create an account</a>.', 'business-feature' ), site_url( BP_REGISTER_SLUG . '/' ) ) ?>
									<?php endif; ?></span></div>

					<form name="loginform" id="logs" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login' ) ?>" method="post">

		<label for="username"><?php _e( 'Username', 'business-feature' ) ?></label>
			<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" />

		<label for="username"><?php _e( 'Password', 'business-feature' ) ?></label>
			<input type="password" name="pwd" id="sidebar-user-pass" class="log" value="" tabindex="98" />

<input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" />

		<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Go'); ?>" tabindex="100" />
		<input type="hidden" name="testcookie" value="1" />
		</form>
			<?php do_action( 'bp_after_sidebar_login_form' ) ?>

		<?php endif; ?>
	</div>