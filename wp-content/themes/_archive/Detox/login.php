<?php 
	global $user_identity, $user_ID;	
	if (is_user_logged_in()) { 
?>
<div class="joins">
<div class="navbarf">
<h5><?php _e( 'Welcome back', 'Detox') ?> <?php echo $user_identity ?></h5> 
<ul>
<li><a href="<?php bloginfo('url') ?>/wp-admin/index.php"><?php _e( 'Go to Dashboard', 'Detox') ?></a></li>
<li><a href="<?php bloginfo('url') ?>/wp-admin/profile.php"><?php _e( 'Edit My Profile', 'Detox') ?></a></li>
<?php if ( current_user_can('level_1') ) : ?>
<li><a href="<?php bloginfo('url') ?>/wp-admin/edit-comments.php"><?php _e( 'Comments', 'Detox') ?></a></li>
<?php endif ?>
<li><a href="<?php echo wp_logout_url(get_permalink()); ?>" rel="nofollow" title="<?php _e('Log out', 'Detox') ?>"><?php _e('Log out', 'Detox') ?></a></li>
</ul>
<?php $aOptions = Detox::initOptions(false); ?>
</div>
</div>
<?php 
	} else {	
?>
<div class="joins">
<h1><a href="<?php echo get_settings('home'); ?>/wp-signup.php"><?php _e( 'It is free', 'Detox') ?></a></h1>
<p>
<?php _e( 'It takes less than 30 seconds.', 'Detox') ?> <a href="<?php echo get_settings('home'); ?>/wp-signup.php"><?php _e( 'Join Us', 'Detox') ?></a>
</p>
<h5><?php _e( 'Login', 'Detox') ?></h5>

<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
			<label><?php _e( 'Username', 'Detox' ) ?><br />
			<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>

			<label><?php _e( 'Password', 'Detox' ) ?><br />
			<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>

			<?php do_action( 'bp_sidebar_login_form' ) ?>
			<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" />
			<input type="hidden" name="testcookie" value="1" />
</form>
		
</div>
<?php } ?>