<?php
include (TEMPLATEPATH . '/options-var.php');

$tn_buddysocial_message_text = get_option('tn_buddysocial_message_text');
$tn_buddysocial_video_code = get_option('tn_buddysocial_blog_intro_header_video');
$tn_buddysocial_video_code_alt = get_option('tn_buddysocial_blog_intro_header_video_alt');
$tn_buddysocial_image_code = get_option('tn_buddysocial_blog_intro_header_image');
?>

<div id="top-header">
<div class="content-wrap">
<div class="home-featured-code">

<?php if( ($tn_buddysocial_image_code == "") && ($tn_buddysocial_video_code == "") && ($tn_buddysocial_video_code_alt == "") ) { ?>
<div id="img-code" class="feat-img"><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/header.jpg" alt="header" height="264" /></div>
<?php } ?>

<?php if($tn_buddysocial_image_code != "") { ?>
<div id="img-code" class="feat-img"><img src="<?php echo stripslashes($tn_buddysocial_image_code); ?>" alt="header" /></div>
<?php } ?>

<?php if($tn_buddysocial_video_code != "") { ?>
<div id="video-code" class="feat-img">
<object data="http://www.youtube.com/v/<?php echo stripslashes($tn_buddysocial_video_code); ?>" type="application/x-shockwave-flash" wmode="opaque" width="480" height="295">
<param name="movie" value="http://www.youtube.com/v/<?php echo stripslashes($tn_buddysocial_video_code); ?>" /><param name="wmode" value="transparent" /></object>
</div>
<?php } elseif($tn_buddysocial_video_code_alt != "") { ?>
<?php echo stripslashes($tn_buddysocial_video_code_alt); ?>
<?php } ?>

</div>


<div class="home-intro">
<div class="home-intro-text">
<?php if($tn_buddysocial_message_text == '') { ?>
<strong><?php _e("Welcome to Social Press, we connect you with your friends, family and co-worker",TEMPLATE_DOMAIN); ?></strong>
<p><?php _e("Start uploading picture, videos and write about your activity to share it with friends and family today.",TEMPLATE_DOMAIN); ?>
<a href="<?php echo site_url('wp-login.php?action=register'); ?>"><?php _e("Sign-up here &raquo;",TEMPLATE_DOMAIN); ?></a></p>
<?php } else { ?>
<?php echo do_shortcode( stripslashes($tn_buddysocial_message_text) ); ?>
<?php } ?>
</div>

<form name="login-form" id="login-form" action="<?php echo site_url( 'wp-login.php' ) ?>" method="post">
<h3><?php _e('Already a member?', TEMPLATE_DOMAIN); ?></h3>
<input type="text" name="log" id="user_login" value="<?php _e( 'Username', TEMPLATE_DOMAIN ) ?>" onfocus="if (this.value == '<?php _e( 'Username', TEMPLATE_DOMAIN ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Username', TEMPLATE_DOMAIN ) ?>';}" />
<input type="password" onblur="if (this.value == '') {this.value = 'password';}" onfocus="if (this.value == 'password') {this.value = '';}" value="password" class="input" id="user_pass" name="pwd"/>
<input type="submit" name="wp-submit" id="wp-submit" value="<?php _e( 'Log In', TEMPLATE_DOMAIN ) ?>"/>

<?php if($bp_existed == 'true') { ?>
<?php if ( bp_get_signup_allowed() ) : ?>
<input type="button" name="signup-submit" id="signup-submit" value="<?php _e( 'Sign Up', TEMPLATE_DOMAIN ) ?>" onclick="location.href='<?php echo bp_signup_page() ?>'" />
<?php endif; ?>
<input type="hidden" name="redirect_to" value="<?php echo bp_root_domain() ?>" />
<?php } else { ?>
<?php $check_active_signup = get_site_option( 'registration' ); if( $check_active_signup == 'none' ) { //if signup allowed ?>
<input type="button" name="signup-submit" id="signup-submit" value="<?php _e( 'Sign Up', TEMPLATE_DOMAIN ) ?>" onclick="location.href='<?php echo get_option('siteurl'); ?>/wp-login.php?action=register'" />
<input type="hidden" name="redirect_to" value="<?php echo get_option('siteurl'); ?>" />
<?php } ?>
<?php } ?>

<input type="hidden" name="testcookie" value="1" />

<p class="small"><input type="checkbox" checked="checked" name="rememberme" id="rememberme" value="forever" title="<?php _e( 'Remember Me', TEMPLATE_DOMAIN ) ?>" />
<?php _e( 'Remember Me', TEMPLATE_DOMAIN ) ?></p>
<?php do_action( 'bp_login_bar_logged_out' ) ?>
</form>


</div>
</div>
</div>