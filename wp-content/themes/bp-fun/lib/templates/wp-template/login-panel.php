<?php
  global $user_ID, $user_identity, $user_url, $user_email;
  get_currentuserinfo();
  if (!$user_ID):
?>


<form name="loginform" id="login-form" action="<?php echo site_url(); ?>/wp-login.php" method="post">

<input type="text" name="log" id="user_login" value="<?php _e( 'Username', TEMPLATE_DOMAIN) ?>" onfocus="if (this.value == '<?php _e( 'Username', TEMPLATE_DOMAIN ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Username', TEMPLATE_DOMAIN ) ?>';}" />

<input type="password" name="pwd" id="user_pass" class="input" value="" />
<input style="display: none;" type="checkbox" checked='checked' name="rememberme" id="rememberme" value="forever" title="<?php _e( 'Remember Me', TEMPLATE_DOMAIN ) ?>" />
<input type="submit" name="wp-submit" id="wp-submit" value="<?php echo esc_attr(__('Login', TEMPLATE_DOMAIN)); ?>"/>
<input type="button" name="signup-submit" id="signup-submit" value="<?php _e( 'Sign Up', TEMPLATE_DOMAIN ) ?>" onclick="location.href='<?php echo site_url(); ?>/wp-login.php?action=register'" />
<input type="hidden" name="redirect_to" value="<?php echo site_url(); ?>"/>
</form>

<?php else: ?>

<?php
$pathtotheme = get_template_directory_uri();
$md5 = md5($user_email);
$default = urlencode("$pathtotheme/_inc/images/mygif.gif");
?>


<div id="logout-link">
<?php echo "<img style='width: 25px; height: 25px;' src='http://www.gravatar.com/avatar.php?gravatar_id=$md5&size=25&default=$default' alt='$user_identity' />"; ?>&nbsp;
<a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><strong><?php echo $user_identity; ?></strong></a> / <?php $mywp_version = get_bloginfo('version'); if ($mywp_version >= '2.7') { ?> <a href="<?php echo wp_logout_url( site_url() ); ?>"><?php _e('Log out', TEMPLATE_DOMAIN); ?></a> <?php } else { ?> <a href="<?php echo site_url(); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account',TEMPLATE_DOMAIN); ?>"><?php _e('Log out', TEMPLATE_DOMAIN); ?></a> <?php } ?>
</div>

<?php endif; ?>