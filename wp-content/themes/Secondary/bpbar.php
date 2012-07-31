<?php do_action( 'bp_before_sidebar' ) ?>

<div class="joins">

	<?php do_action( 'bp_inside_before_sidebar' ) ?>

	<?php if ( is_user_logged_in() ) : ?>

		<?php do_action( 'bp_before_sidebar_me' ) ?>

     	<h3><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h3>
     	
		<div id="sidebar-me">
			<a href="<?php echo bp_loggedin_user_domain() ?>">
				<?php bp_loggedin_user_avatar( 'type=thumb&width=40&height=40' ) ?>
			</a>

			<a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'buddypress' ) ?></a>

			<?php do_action( 'bp_sidebar_me' ) ?>
		</div>

		<?php do_action( 'bp_after_sidebar_me' ) ?>

		<?php if ( bp_is_active( 'messages' ) ) : ?>
			<?php bp_message_get_notices(); /* Site wide notices to all users */ ?>
		<?php endif; ?>

</div>

	<?php else : ?>

		<?php do_action( 'bp_before_sidebar_login_form' ) ?>

<div class="joins">
			
<h1><a href="<?php echo get_settings('home'); ?>/wp-signup.php"><?php _e( 'It is free', 'Detox') ?></a></h1>
<p>
<?php _e( 'It takes less than 30 seconds.', 'Detox') ?> <a href="<?php echo get_settings('home'); ?>/wp-signup.php"><?php _e( 'Join Us', 'Detox') ?></a>
</p>
<h5><?php _e( 'Login', 'Detox') ?></h5>

		<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
			<label><?php _e( 'Username', 'buddypress' ) ?><br />
			<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php if ( isset( $user_login) ) echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>

			<label><?php _e( 'Password', 'buddypress' ) ?><br />
			<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>

			<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'buddypress' ) ?></label></p>

			<?php do_action( 'bp_sidebar_login_form' ) ?>
			<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e( 'Log In', 'buddypress' ); ?>" tabindex="100" />
			<input type="hidden" name="testcookie" value="1" />
		</form>

		<?php do_action( 'bp_after_sidebar_login_form' ) ?>

</div>

	<?php endif; ?>

	<?php /* Show forum tags on the forums directory */
	if ( bp_is_active( 'forums' ) && bp_is_current_component( 'forums' ) && bp_is_directory() ) : ?>
		<div id="forum-directory-tags" class="widget tags">
			<h3 class="widgettitle"><?php _e( 'Forum Topic Tags', 'buddypress' ) ?></h3>
			<div id="tag-text"><?php bp_forums_tag_heat_map(); ?></div>
		</div>
	<?php endif; ?>

	<?php dynamic_sidebar( 'sidebar-1' ) ?>

	<?php do_action( 'bp_inside_after_sidebar' ) ?>

<?php do_action( 'bp_after_sidebar' ) ?>