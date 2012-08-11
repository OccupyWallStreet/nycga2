<div id="top-right-panel">

<?php if ( is_user_logged_in() ) : ?>

<?php do_action( 'bp_before_sidebar_me' ) ?>

<div id="user-profile">
<h3><?php _e('Your Info.', TEMPLATE_DOMAIN); ?></h3>

<div id="sidebar-me">

<div class="p-avatar">
<a href="<?php echo bp_loggedin_user_domain() ?>">
<?php bp_loggedin_user_avatar( 'type=full&width=64&height=64' ) ?>
</a>
</div>

<div class="p-user-meta">
<h4><?php _e('Welcome back', TEMPLATE_DOMAIN); ?><br /><a href="<?php bp_loggedinuser_link() ?>"><?php global $bp; echo $bp->loggedin_user->fullname; ?></a></h4>
<a class="button logout" id="bplogout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out' , TEMPLATE_DOMAIN) ?></a>
</div>

<p><?php _e("You can get help and support and chat to other users at the",TEMPLATE_DOMAIN); ?> <a href="<?php echo site_url(); ?>/forums" target="_blank" title="<?php _e('Search, post at and enjoy our discussion space', TEMPLATE_DOMAIN); ?>"><?php _e("Forums",TEMPLATE_DOMAIN); ?></a>.</p>

</div>

<?php do_action( 'bp_sidebar_me' ) ?>

</div>

<?php do_action( 'bp_after_sidebar_me' ) ?>

<?php else : ?>

<?php do_action( 'bp_before_sidebar_login_form' ) ?>

<p id="login-text">
<?php _e( 'To start connecting please log in first.', TEMPLATE_DOMAIN ) ?>
<?php if ( bp_get_signup_allowed() ) : ?>
<?php printf( __( ' You can also <a href="%s" title="Create an account">create an account</a>.', TEMPLATE_DOMAIN ), site_url( BP_REGISTER_SLUG . '/' ) ) ?>
<?php endif; ?>
</p>

<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url('/wp-login.php') ?>" method="post">
<label><?php _e( 'Username', TEMPLATE_DOMAIN ) ?><br />

<input type="text" name="log" id="sidebar-user-login" class="inbox" value="<?php echo esc_attr(stripslashes($user_login)); ?>" /></label>
<label><?php _e( 'Password', TEMPLATE_DOMAIN ) ?><br />

<input type="password" name="pwd" id="sidebar-user-pass" class="inbox" value="" /></label>
<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" /> <?php _e( 'Remember Me', TEMPLATE_DOMAIN ) ?></label></p>

<?php do_action( 'bp_sidebar_login_form' ) ?>
<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In', TEMPLATE_DOMAIN); ?>" tabindex="100" />
<input type="hidden" name="testcookie" value="1" />

<?php do_action( 'bp_login_bar_logged_out' ) ?>
</form>

<?php do_action( 'bp_after_sidebar_login_form' ) ?>

<?php endif; ?>

</div>