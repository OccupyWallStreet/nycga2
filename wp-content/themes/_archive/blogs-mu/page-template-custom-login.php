<?php
/*
Template Name: Custom Login
*/
?>

<?php
include ( TEMPLATEPATH . '/options-var.php' );
if( is_user_logged_in() ) {
ob_start();
if($bp_existed == 'true') {
bp_core_redirect( bp_get_root_domain()  );
} else {
wp_redirect( get_home_url() );
}
ob_end_flush();
}
?>

<?php get_header(); ?>

<div class="full-width members-login" id="post-entry">

<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="page"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><?php the_title(); ?></h1>


<?php if (!is_user_logged_in()) { ?>

<div class="post-content">

<?php the_content(); ?>

<?php if($bp_existed == 'true') { ?>

<?php do_action( 'bp_before_sidebar_login_form' ) ?>
<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo esc_url(site_url( 'wp-login.php', 'login' )); ?>" method="post">
<label><?php _e( 'Username', TEMPLATE_DOMAIN) ?><br />
<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" /></label>
<label><?php _e( 'Password', TEMPLATE_DOMAIN) ?><br />
<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" /></label>
<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" /> <?php _e( 'Remember Me', TEMPLATE_DOMAIN ) ?></label></p>

<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In', TEMPLATE_DOMAIN); ?>" tabindex="100" /> <br />

<input type="hidden" name="redirect_to" value="<?php echo bp_root_domain() ?>"/>
<input type="hidden" name="testcookie" value="1" />

<small><a href="<?php echo bp_root_domain() ?>/wp-login.php?action=lostpassword"><?php _e('Lost your password?',TEMPLATE_DOMAIN); ?></a>&nbsp;|&nbsp;
<a href="<?php echo bp_root_domain() . '/' . bp_get_root_slug( 'register' ) . '/'; ?>"><?php _e('Create a new account', TEMPLATE_DOMAIN); ?></a></small>
<?php do_action( 'bp_sidebar_login_form' ) ?>
</form>
<?php do_action( 'bp_after_sidebar_login_form' ) ?>


<?php } else { ?>


<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo esc_url(site_url( 'wp-login.php', 'login' )); ?>" method="post">
<label><?php _e( 'Username', TEMPLATE_DOMAIN) ?><br />
<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" /></label>
<label><?php _e( 'Password', TEMPLATE_DOMAIN) ?><br />
<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" /></label>

<input type="hidden" name="redirect_to" value="<?php echo site_url(); ?>"/>
<input type="hidden" name="testcookie" value="1" />

<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" /> <?php _e( 'Remember Me', TEMPLATE_DOMAIN ) ?></label></p>
<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In', TEMPLATE_DOMAIN); ?>" tabindex="100" /><br />
<small><a href="<?php echo site_url(); ?>/wp-login.php?action=lostpassword"><?php _e('Lost your password?',TEMPLATE_DOMAIN); ?></a>&nbsp;|&nbsp;<?php if(function_exists('get_current_site')) { ?><a href="<?php echo site_url(); ?>/wp-signup.php"><?php } else { ?><a href="<?php echo site_url(); ?>/wp-login.php?action=register"><?php } ?><?php _e('Create a new account', TEMPLATE_DOMAIN); ?> </a></small>

</form>

<?php } ?>


</div>

<?php } else { ?>

<div class="post-content">
<p class="verify"><?php _e("You are already logged in. Please continue to member page.", TEMPLATE_DOMAIN); ?></p>
</div>

<?php } ?>


</div>

<?php endwhile; ?>

<?php endif; ?>

</div>

<?php get_footer(); ?>