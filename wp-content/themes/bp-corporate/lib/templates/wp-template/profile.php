<form id="search-form" method="get" action="<?php bloginfo ('url'); ?>">
<input name="s" id="search-terms" type="text" value="<?php _e('Search here', TEMPLATE_DOMAIN); ?>" onfocus="if (this.value == '<?php _e('Search here', TEMPLATE_DOMAIN); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Search here', TEMPLATE_DOMAIN); ?>';}" size="10" tabindex="1" />
<input type="submit" id="search-submit" value="<?php echo esc_attr(__('Search', TEMPLATE_DOMAIN)); ?>" />
</form>

<?php
  global $user_ID, $user_identity, $user_url, $user_email;
  get_currentuserinfo();
  if (!$user_ID):
?>


<form name="loginform" class="mylogform" id="login-form" action="<?php echo get_option('siteurl'); ?>/wp-login.php" method="post">

<input type="text" name="log" id="user_login" value="<?php _e('Username', TEMPLATE_DOMAIN); ?>" onfocus="if (this.value == '<?php _e('Username', TEMPLATE_DOMAIN); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Username', TEMPLATE_DOMAIN); ?>';}" />

<input type="password" name="pwd" id="user_pass" class="input" value="" />

<input type="checkbox" checked="checked" name="rememberme" id="rememberme" value="forever" title="<?php _e('Remember Me', TEMPLATE_DOMAIN); ?>" />
<input type="submit" name="wp-submit" id="wp-submit" value="<?php echo esc_attr(__('Login', TEMPLATE_DOMAIN)); ?>"/>
<input type="button" name="signup-submit" id="signup-submit" value="<?php _e('Sign Up', TEMPLATE_DOMAIN); ?>" onclick="location.href='<?php echo get_option('siteurl'); ?>/wp-login.php?action=register'" />
<input type="hidden" name="redirect_to" value="<?php echo home_url(); ?>"/>
</form>

<?php else: ?>

<?php
$pathtotheme = get_template_directory_uri();
$md5 = md5($user_email);
$default = urlencode("$pathtotheme/_inc/images/mygif.gif");
?>


<div id="logout-link">
<?php echo "<img style='width: 20px; height: 20px;' src='http://www.gravatar.com/avatar.php?gravatar_id=$md5&size=20&default=$default' alt='$user_identity' />"; ?>&nbsp;
<a href="<?php echo home_url(); ?>/wp-admin/profile.php"><strong><?php echo $user_identity; ?></strong></a> / <?php $mywp_version = get_bloginfo('version'); if ($mywp_version >= '2.7') { ?> <a href="<?php echo wp_logout_url(get_bloginfo('url')); ?>"><?php _e('Log out', TEMPLATE_DOMAIN); ?></a> <?php } else { ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account', TEMPLATE_DOMAIN); ?>"><?php _e('Log out', TEMPLATE_DOMAIN); ?></a> <?php } ?>
</div>

<?php endif; ?>
