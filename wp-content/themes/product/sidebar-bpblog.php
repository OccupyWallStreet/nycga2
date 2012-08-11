
<div class="shadow-spacer"></div>
<div id="sidebar">
	<div class="padder">
			<div class="sidebar-box">
					<?php if ( is_active_sidebar( 'blogleft-sidebar' ) ) : ?>
							<?php dynamic_sidebar( 'blogleft-sidebar' ); ?>
								<?php else : ?>
								<div class="widget-error">
								<?php _e( 'Please log in and add widgets to this column.', 'product' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=blogleft-sidebar"><?php _e( 'Add Widgets', 'product' ) ?></a>
								</div>
							<?php endif; ?>
			</div>

				<div class="sidebar-box">
						<?php if ( is_active_sidebar( 'blogmiddle-sidebar' ) ) : ?>
								<?php dynamic_sidebar( 'blogmiddle-sidebar' ); ?>
							<?php else : ?>
									<div class="widget-error">
										<?php _e( 'Please log in and add widgets to this column.', 'product' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=blogmiddle-sidebar"><?php _e( 'Add Widgets', 'product' ) ?></a>
									</div>
						<?php endif; ?>
				</div>
				<div id="login-box">
					<?php do_action( 'bp_inside_before_sidebar' ) ?>

					<?php if ( is_user_logged_in() ) : ?>

						<?php do_action( 'bp_before_sidebar_me' ) ?>

						<div id="sidebar-me">
											<a href="<?php echo bp_loggedin_user_domain() ?>">
																			<?php bp_loggedin_user_avatar( 'type=thumb&width=40&height=40' ) ?>
																		</a>
								
																								<h4><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h4>
																								<a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'product') ?></a>
																	
																								<?php do_action( 'bp_sidebar_me' ) ?>						</div>

						<?php do_action( 'bp_after_sidebar_me' ) ?>

						<?php if ( function_exists( 'bp_message_get_notices' ) ) : ?>
							<?php bp_message_get_notices(); /* Site wide notices to all users */ ?>
						<?php endif; ?>

					<?php else : ?>

						<?php do_action( 'bp_before_sidebar_login_form' ) ?>

						<p id="login-text">
							<?php _e( 'To start connecting please log in first.', 'product' ) ?>
							<?php if ( bp_get_signup_allowed() ) : ?>
								<?php printf( __( ' You can also <a href="%s" title="Create an account">create an account</a>.', 'product' ), site_url( BP_REGISTER_SLUG . '/' ) ) ?>
							<?php endif; ?>
						</p>

						<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login' ) ?>" method="post">
							<label><?php _e( 'Username', 'product' ) ?><br />
							<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" /></label>

							<label><?php _e( 'Password', 'product' ) ?><br />
							<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" /></label>

							<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" /> <?php _e( 'Remember Me', 'product' ) ?></label></p>

							<?php do_action( 'bp_sidebar_login_form' ) ?>
							<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" />
							<input type="hidden" name="testcookie" value="1" />
						</form>

						<?php do_action( 'bp_after_sidebar_login_form' ) ?>

					<?php endif; ?>

						<hr />

							<?php if ( bp_search_form_enabled() ) : ?>

								<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
									<input type="text" id="search-terms" name="search-terms" value="" size="16"/>
									<?php echo bp_search_form_type_select() ?>

									<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'product' ) ?>" />
									<?php wp_nonce_field( 'bp_search_form' ) ?>
								</form><!-- #search-form -->

							<?php endif; ?>

					</div>
					
					<div class="clear"></div>
	</div><!-- .padder -->
</div><!-- #sidebar -->

<div class="shadow-spacer"></div>