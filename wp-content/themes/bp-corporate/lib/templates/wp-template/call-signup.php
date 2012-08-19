<?php
include( TEMPLATEPATH . '/options-var.php' );
$tn_buddycorp_call_signup_text = get_option('tn_buddycorp_call_signup_text');
$tn_buddycorp_call_signup_button_link = get_option('tn_buddycorp_call_signup_button_link');
$tn_buddycorp_call_signup_button_text = get_option('tn_buddycorp_call_signup_button_text');
?>


<div id="call-action">
<p>
<?php
if($tn_buddycorp_call_signup_text == ''){ ?>
<?php _e("Welcome to your BuddyPress Corporate theme!",TEMPLATE_DOMAIN); ?><br />
<span><?php _e("Change or remove the text here using the",TEMPLATE_DOMAIN); ?> <a href="<?php echo site_url(); ?>/wp-admin/themes.php?page=options-functions.php"><?php _e('theme options', TEMPLATE_DOMAIN); ?></a></span>
<?php } else {  ?>

<?php
if( function_exists('do_shortcode') ) {
echo stripslashes( do_shortcode($tn_buddycorp_call_signup_text) );
} else {
echo stripslashes($tn_buddycorp_call_signup_text);
}
?>
<?php } ?>
</p>


<?php if ( !is_user_logged_in() ) { ?>
<div class="bpc-button">

<?php if($tn_buddycorp_call_signup_button_link != '') { ?>
<a href="<?php echo stripslashes($tn_buddycorp_call_signup_button_link); ?>">
<?php } else { ?>
<?php if($bp_existed == 'true') { ?> <a href="<?php echo bp_root_domain() . '/' . bp_get_root_slug( 'register' ) . '/'; ?>">
<?php } else { ?><a href="<?php echo site_url(); ?>/wp-login.php?action=register"><?php } ?>
<?php } ?>

<?php
if($tn_buddycorp_call_signup_button_text == ''){ ?>
<?php _e('Join Us Here', TEMPLATE_DOMAIN); ?>
<?php } else { ?>
<?php echo stripslashes($tn_buddycorp_call_signup_button_text); ?>
<?php } ?>
</a>
</div>
<?php } ?>

</div>
