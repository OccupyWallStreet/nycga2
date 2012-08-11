<?php include (TEMPLATEPATH . '/options.php'); ?>

<div id="header">

<?php
$tn_buddycom_call_signup_text = get_option('tn_buddycom_call_signup_text');
$tn_buddycom_call_signup_button_text = get_option('tn_buddycom_call_signup_button_text');
$tn_buddycom_call_signup_button_text_link = get_option('tn_buddycom_call_signup_button_text_link');
?>

<div id="intro-text">
<?php
if($tn_buddycom_call_signup_text == ''){ ?>
<h2><?php _e('Welcome to the BuddyPress Community Theme', TEMPLATE_DOMAIN); ?></h2>
<span><?php _e('Simply change this text in your theme options', TEMPLATE_DOMAIN); ?></span>
<?php } else {  ?>
<?php echo stripslashes($tn_buddycom_call_signup_text); ?>
<?php } ?>
</div>

<?php if (!is_user_logged_in() ) { ?>
<div id="signup-button">

<?php
if($tn_buddycom_call_signup_button_text_link == ''){ ?>
<?php if($bp_existed == 'true') { global $bp; ?> <a href="<?php echo $bp->root_domain . '/' . bp_get_root_slug( 'register' ) . '/'; ?>">
<?php } else { ?><a href="<?php echo site_url; ?>/wp-login.php?action=register"><?php } ?>
<?php } else { ?>
<?php
if( function_exists('do_shortcode') ) { ?>
<a href="<?php echo stripslashes( do_shortcode($tn_buddycom_call_signup_button_text_link) ); ?>">
<?php } else { ?>
<a href="<?php echo stripslashes($tn_buddycom_call_signup_button_text_link); ?>">
<?php } ?>
<?php } ?>

<?php
if($tn_buddycom_call_signup_button_text == ''){ ?>
<?php _e('Join Us Here', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php echo stripslashes($tn_buddycom_call_signup_button_text); ?>
<?php } ?>
</a></div>
<?php } else { ?>
<div id="login-p">
<?php if($bp_existed == 'true') { ?>
<?php } else { ?>
<?php locate_template( array( 'lib/templates/wp-template/login-panel.php'), true ); ?>
<?php } ?>
</div>
<?php } ?>

</div>