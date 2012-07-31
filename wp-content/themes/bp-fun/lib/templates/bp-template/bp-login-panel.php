<?php if (!is_user_logged_in()) : ?>
<form name="login-form" id="login-form" action="<?php echo site_url( 'wp-login.php' ) ?>" method="post">
<input type="text" name="log" id="user_login" value="<?php _e( 'Username', TEMPLATE_DOMAIN) ?>" onfocus="if (this.value == '<?php _e( 'Username', TEMPLATE_DOMAIN ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Username', TEMPLATE_DOMAIN ) ?>';}" />
<input type="password" name="pwd" id="user_pass" class="input" value="" />
<input type="checkbox" name="rememberme" id="rememberme" value="forever" title="<?php _e( 'Remember Me', TEMPLATE_DOMAIN ) ?>" />
<input type="submit" name="wp-submit" id="wp-submit" value="<?php _e( 'Log In', TEMPLATE_DOMAIN ) ?>"/>
<?php if ( bp_get_signup_allowed() ) : ?>
<input type="button" name="signup-submit" id="signup-submit" value="<?php _e( 'Sign Up', TEMPLATE_DOMAIN ) ?>" onclick="location.href='<?php echo bp_signup_page() ?>'" />
<?php endif; ?>
<input type="hidden" name="redirect_to" value="<?php echo bp_root_domain() ?>" />
<input type="hidden" name="testcookie" value="1" />
<?php do_action( 'bp_login_bar_logged_out' ) ?>
</form>
<?php else : ?>
<div id="logout-link">
<?php bp_loggedin_user_avatar( 'width=50&height=50' ) ?> &nbsp; <?php _e("Welcome back",TEMPLATE_DOMAIN); ?>, <a href="<?php bp_loggedinuser_link() ?>"><?php global $bp; echo $bp->loggedin_user->fullname; ?></a><br /><br />&nbsp;<span class="loglink"><a href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', TEMPLATE_DOMAIN ) ?></a></span>
<?php do_action( 'bp_login_bar_logged_in' ) ?>
</div>
<?php endif; ?>