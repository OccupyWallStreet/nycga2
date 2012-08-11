<?php
	$bp_text = get_option('dev_businessservices_bp_text');
?>
		<?php if ( is_user_logged_in() ) : ?>

			<?php do_action( 'bp_before_sidebar_me' ) ?>
			
			<?php
			
			if ($bp_text != ""){
			?>
			
			<div class="label-box">	<h3>
				<?php echo stripslashes($bp_text); ?>
			</h3></div>
			<?php	
			}
			
			?>

			<div id="sidebar-me">
				<a href="<?php echo bp_loggedin_user_domain() ?>">
					<?php bp_loggedin_user_avatar( 'type=thumb&width=40&height=40' ) ?>
				</a>

				<h4><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h4>
				<a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'business-services') ?></a>

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
									<?php _e( 'To start connecting please log in first.', 'business-services' ) ?>
									<?php if ( bp_get_signup_allowed() ) : ?>
										<?php printf( __( ' You can also <a href="%s" title="Create an account">create an account</a>.', 'business-services' ), site_url( BP_REGISTER_SLUG . '/' ) ) ?>
									<?php endif; ?></span></div>

					

					<form name="loginform" id="logs" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login' ) ?>" method="post">

		<label for="username"><?php _e( 'Username', 'business-services' ) ?></label>
			<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" />

		<label for="username"><?php _e( 'Password', 'business-services' ) ?></label>
			<input type="password" name="pwd" id="sidebar-user-pass" class="log" value="" tabindex="98" />

<input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" />

		<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Go'); ?>" tabindex="100" />
		<input type="hidden" name="testcookie" value="1" />
		</form>
			<?php do_action( 'bp_after_sidebar_login_form' ) ?>

		<?php endif; ?>
	<ul class="list">
		<li>
		<ul>
			<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>
				<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'business-services' ) ?>"><?php _e( 'Activity', 'business-services' ) ?></a>
				</li>
			<?php endif; ?>
			<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_member() ) : ?> class="selected"<?php endif; ?>>
				<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'business-services' ) ?>"><?php _e( 'Members', 'business-services' ) ?></a>
			</li>
			<?php if ( bp_is_active( 'groups' ) ) : ?>
				<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'business-services' ) ?>"><?php _e( 'Groups', 'business-services' ) ?></a>
				</li>
				<?php if ( bp_is_active( 'forums' ) && bp_is_active( 'groups' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
					<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
						<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'business-services' ) ?>"><?php _e( 'Forums', 'business-services' ) ?></a>
					</li>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( bp_is_active( 'blogs' ) && bp_core_is_multisite() ) : ?>
				<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'business-services' ) ?>"><?php _e( 'Blogs', 'business-services' ) ?></a>
				</li>
			<?php endif; ?>
			<?php do_action( 'bp_nav_items' ); ?>
			</ul>
			</li>
			</ul>