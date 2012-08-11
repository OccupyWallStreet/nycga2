<div id="top-right-panel">
<?php
global $user_ID, $user_identity;
get_currentuserinfo();
if (!$user_ID):
?>

<form name="loginform" action="<?php echo site_url('/wp-login.php', 'login'); ?>" method="post">

      <h3><?php _e('Log in',TEMPLATE_DOMAIN); ?></h3>

      <label><?php _e('Username:',TEMPLATE_DOMAIN); ?></label>
      <p><input name="log" id="user_login" class="inbox" value=""/></p>

       <label><?php _e('Password:',TEMPLATE_DOMAIN); ?></label>
      <p><input name="pwd" id="user_pass" type="password" value="" class="inbox"/></p>

      <p><input name="submit" type="submit" value="<?php _e('Login',TEMPLATE_DOMAIN); ?>" class="submit-button"/>
      <input type="hidden" name="redirect_to" value="<?php echo site_url(); ?>"/>
      </p>

      <p class="chk"><input name="rememberme" id="rememberme" value="forever" type="checkbox" checked="checked" />&nbsp;<?php _e('remember me', TEMPLATE_DOMAIN); ?></p>
<p class="chk"><a href="<?php echo site_url(); ?>/wp-login.php?action=lostpassword" title="<?php _e('Get a new password sent to you',TEMPLATE_DOMAIN); ?>"><?php _e('Lost your password?',TEMPLATE_DOMAIN); ?></a></p><br />
<p class="chk"><a href="<?php echo site_url('/wp-signup.php', 'login'); ?>" title="<?php _e("Sign up for a new account",TEMPLATE_DOMAIN); ?>"><?php _e('Create a new account',TEMPLATE_DOMAIN); ?></a></p><br />
      </form>

<?php else: ?>

<div id="user-profile">
<h3><?php _e('Your Info.',TEMPLATE_DOMAIN); ?></h3>

<?php
global $wpdb;
$tmp_blog_id = $wpdb->get_var("SELECT meta_value FROM " . $wpdb->base_prefix . "usermeta WHERE meta_key = 'primary_blog' AND user_id = '" . $user_ID . "'");
$tmp_blog_domain = $wpdb->get_var("SELECT domain FROM " . $wpdb->base_prefix . "blogs WHERE blog_id = '" . $tmp_blog_id . "'");
$tmp_blog_path = $wpdb->get_var("SELECT path FROM " . $wpdb->base_prefix . "blogs WHERE blog_id = '" . $tmp_blog_id . "'");

if ($tmp_blog_domain == ''){
	$tmp_blog_domain = $current_site->domain;
}

if ($tmp_blog_path == ''){
	$tmp_blog_path = $current_site->path;

}
if ( is_ssl( )) {
$tmp_user_url =  'https://' . $tmp_blog_domain . $tmp_blog_path;
} else {
$tmp_user_url =  'http://' . $tmp_blog_domain . $tmp_blog_path;
}

?>

<a href="<?php echo $tmp_user_url; ?>" title="<?php _e("Go to your blog homepage",TEMPLATE_DOMAIN); ?>"><?php echo get_avatar($user_ID,'48',get_option('avatar_default')); ?></a>
&nbsp;<a href="<?php echo $tmp_user_url; ?>wp-admin/" title="<?php _e('Dashboard',TEMPLATE_DOMAIN) ?>">
<strong><?php _e('Your dashboard',TEMPLATE_DOMAIN); ?></strong></a>

<br />
&nbsp;<a href="<?php echo $tmp_user_url; ?>wp-admin/post-new.php" title="<?php _e('Posting Area',TEMPLATE_DOMAIN) ?>"><?php _e('Write a post',TEMPLATE_DOMAIN); ?></a>
<br />
&nbsp;<a href="<?php echo $tmp_user_url; ?>wp-admin/profile.php?page=user-avatar" title="<?php _e('Edit your avatar',TEMPLATE_DOMAIN) ?>"><?php _e('Upload new avatar',TEMPLATE_DOMAIN); ?></a>
<br /><br />
<?php _e("Welcome back,",TEMPLATE_DOMAIN); ?> <?php echo $user_identity; ?>, <?php _e("use the links above to get started or you can",TEMPLATE_DOMAIN); ?> <?php $mywp_version = get_bloginfo('version'); if ($mywp_version >= '2.7') { ?> <a href="<?php echo wp_logout_url( get_site_url() ); ?>"><?php _e('Log out &raquo;',TEMPLATE_DOMAIN); ?></a> <?php } else { ?> <a href="<?php echo site_url('/wp-login.php?action=logout', 'login'); ?>" title="<?php _e("Log out of this account",TEMPLATE_DOMAIN); ?>"><?php _e('Log out &raquo;',TEMPLATE_DOMAIN); ?></a> <?php } ?>.

<br /><br />
<?php _e("You can get help and support and chat to other users at the",TEMPLATE_DOMAIN); ?> <a href="<?php echo site_url(); ?>/forums" target="_blank" title="<?php _e("Search, post at and enjoy our discussion space",TEMPLATE_DOMAIN); ?>"><?php _e("Forums",TEMPLATE_DOMAIN); ?></a>.
</div>

<?php endif; ?>

</div>