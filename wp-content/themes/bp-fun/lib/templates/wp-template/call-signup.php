<?php include (TEMPLATEPATH . '/options.php'); ?>

<div id="call-action">

<?php
$tn_buddyfun_call_signup_text = get_option('tn_buddyfun_call_signup_text');
$tn_buddyfun_call_signup_button_text = get_option('tn_buddyfun_call_signup_button_text');
$tn_buddyfun_call_signup_button_text_link = get_option('tn_buddyfun_call_signup_button_text_link');
?>

<div class="call-join">
<?php
if($tn_buddyfun_call_signup_text == ''){ ?>
<?php _e('Welcome to your BuddyPress Fun theme!', TEMPLATE_DOMAIN); ?><br />
<small><?php _e('Change or remove the text here using the theme options', TEMPLATE_DOMAIN); ?></small>
<?php } else {  ?>
<?php
if( function_exists('do_shortcode') ) {
echo stripslashes( do_shortcode($tn_buddyfun_call_signup_text) );
} else {
echo stripslashes($tn_buddyfun_call_signup_text);
}
?>
<?php } ?>
</div>

<?php if ( !is_user_logged_in() ) { ?>
<div class="call-button">
<p>
<?php if($tn_buddyfun_call_signup_button_text_link != '') { ?>
<a href="<?php echo stripslashes( do_shortcode($tn_buddyfun_call_signup_button_text_link) ); ?>">
<?php } else { ?>
<?php if($bp_existed == 'true') { ?> <a href="<?php echo bp_root_domain() . '/' . bp_get_root_slug( 'register' ) . '/'; ?>">
<?php } else { ?><a href="<?php echo site_url(); ?>/wp-login.php?action=register"><?php } ?>
<?php } ?>

<?php
if($tn_buddyfun_call_signup_button_text == ''){ ?>
<?php _e('Join Us Here', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php echo stripslashes($tn_buddyfun_call_signup_button_text); ?>
<?php } ?>
</a></p>
</div>
<?php } ?>
</div>