<!-- start buddypress login section -->
		<?php if ( is_user_logged_in() ) : ?>
			<?php do_action( 'bp_before_sidebar_me' ) ?>
			<div id="sidebar-me">
					<?php bp_loggedin_user_avatar( 'type=thumb&width=100&height=100' ) ?>
				</a>
		<div class="spacer"></div>
				<h4><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h4>
				<div class="spacer"></div>
				<a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'studio' ) ?></a>
		
<div class="clear"></div>
				<?php do_action( 'bp_sidebar_me' ) ?>
			</div>

			<?php do_action( 'bp_after_sidebar_me' ) ?>
		<?php else : ?>
						<?php do_action( 'bp_before_sidebar_login_form' ) ?>
						
						<div class="label-box">	<h3><?php _e( 'user Login', 'studio' ) ?></h3>
								<p id="login-text">
									<?php _e( 'To start connecting please log in first.', 'studio' ) ?>
									<?php if ( bp_get_signup_allowed() ) : ?>
										<?php printf( __( ' You can also <a href="%s" title="Create an account">create an account</a>.', 'studio' ), site_url( BP_REGISTER_SLUG . '/' ) ) ?>
									<?php endif; ?>
								</p>
						</div>
							<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
								<label><?php _e( 'Username', 'studio' ) ?><br />
											<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>
							

								<label><?php _e( 'Password', 'studio' ) ?><br />
								<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>

								<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'studio' ) ?></label></p>

								<?php do_action( 'bp_sidebar_login_form' ) ?>
								<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" />
								<input type="hidden" name="testcookie" value="1" />
							</form>
			<?php do_action( 'bp_after_sidebar_login_form' ) ?>
		<?php endif; ?>
		<div class="spacer"></div>
			<?php if ( bp_search_form_enabled() ) : ?>

				<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
					<input type="text" id="search-terms" name="search-terms" value=""/>
					<?php echo bp_search_form_type_select() ?>
<div class="spacer"></div>
					<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'studio' ) ?>" />
					<?php wp_nonce_field( 'bp_search_form' ) ?>
				</form><!-- #search-form -->

			<?php endif; ?>

			<?php do_action( 'bp_search_login_bar' ) ?>
		
<!-- end buddypress login section -->