<!-- start buddypress login section -->
<div class="clear"></div>
		<?php if ( is_user_logged_in() ) : ?>
			<?php do_action( 'bp_before_sidebar_me' ) ?>
			<div id="sidebar-me">
				<a href="<?php echo bp_loggedin_user_domain() ?>">
							<?php bp_loggedin_user_avatar( 'type=full' ) ?>
	</a>
		<div class="clear"></div>
				<h2>		<?php _e( 'Hi', 'gallery' ) ?><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h2>
				<a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'gallery') ?></a>						<div class="spacer"></div>
<div class="clear"></div>
				<?php do_action( 'bp_sidebar_me' ) ?>

			<?php do_action( 'bp_after_sidebar_me' ) ?>
</div>
		<?php else : ?>
				<div id="sidebar-me">
						<?php do_action( 'bp_before_sidebar_login_form' ) ?>
						
						<div class="label-box">	<h3><?php _e( 'user Login', 'gallery' ) ?></h3>
								<p id="login-text">
									<?php _e( 'To start connecting please log in first.', 'gallery' ) ?>
									<?php if ( bp_get_signup_allowed() ) : ?>
										<?php printf( __( ' You can also <a href="%s" title="Create an account">create an account</a>.', 'gallery' ), site_url( BP_REGISTER_SLUG . '/' ) ) ?>
									<?php endif; ?>
								</p>
						</div>
							<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
								<label><?php _e( 'Username', 'gallery' ) ?><br />
								<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" /></label>
<br />
								<label><?php _e( 'Password', 'gallery' ) ?><br />
								<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" /></label>
<br />
								<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" /> <?php _e( 'Remember Me', 'gallery' ) ?></label></p>

								<?php do_action( 'bp_sidebar_login_form' ) ?>
								<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" />
								<input type="hidden" name="testcookie" value="1" />
							</form>
			<?php do_action( 'bp_after_sidebar_login_form' ) ?>
			<div class="clear"></div>
						</div>
		<?php endif; ?>
				<div id="sidebar-login">
					<br /><br />
			<?php if ( bp_search_form_enabled() ) : ?>

				<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
					<input type="text" id="search-terms" name="search-terms" value=""/>
					
							<div class="spacer"></div>
					<?php echo bp_search_form_type_select() ?>
					<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'gallery' ) ?>" />
					<?php wp_nonce_field( 'bp_search_form' ) ?>
				</form><!-- #search-form -->

			<?php endif; ?>

			<?php do_action( 'bp_search_login_bar' ) ?>
		</div>
			<div class="clear"></div>
<!-- end buddypress login section -->