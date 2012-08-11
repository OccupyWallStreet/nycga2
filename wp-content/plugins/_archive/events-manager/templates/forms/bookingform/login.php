<?php
/* 
 * This file generates the default login form within the booking form (if enabled in options).
 */
?>
<div class="em-booking-login">
	<form class="em-booking-login-form" action="<?php echo site_url('wp-login.php', 'login_post'); ?>" method="post">
	<p><?php _e('Log in if you already have an account with us.','dbem'); ?></p>
    <p>
		<label><?php _e( 'Username','dbem' ) ?></label>
		<input type="text" name="log" class="input" value="" />
	</p>
	<p>
		<label><?php _e( 'Password','dbem' ) ?></label>
		<input type="password" name="pwd" class="input" value="" />
    </p>
    <?php do_action('login_form'); ?>
	<input type="submit" name="wp-submit" id="em_wp-submit" value="<?php _e('Log In', 'dbem'); ?>" tabindex="100" />
	<input name="rememberme" type="checkbox" id="em_rememberme" value="forever" /> <label><?php _e( 'Remember Me','dbem' ) ?></label>
	<input type="hidden" name="redirect_to" value="http://<?php echo $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ?>#em-booking" />
	<br />
	<?php
	//Signup Links
	if ( get_option('users_can_register') ) {
		echo "<br />";  
		if ( function_exists('bp_get_signup_page') ) { //Buddypress
			$register_link = bp_get_signup_page();
		}elseif ( file_exists( ABSPATH."/wp-signup.php" ) ) { //MU + WP3
			$register_link = site_url('wp-signup.php', 'login');
		} else {
			$register_link = site_url('wp-login.php?action=register', 'login');
		}
		?>
		<a href="<?php echo $register_link ?>"><?php _e('Sign Up','dbem') ?></a>&nbsp;&nbsp;|&nbsp;&nbsp; 
		<?php
	}
	?>	                    
	<a href="<?php echo site_url('wp-login.php?action=lostpassword', 'login') ?>" title="<?php _e('Password Lost and Found', 'dbem') ?>"><?php _e('Lost your password?', 'dbem') ?></a>                        
  </form>
</div>