<div id="csright">
<?php 
	global $user_identity, $user_ID;	
	if (is_user_logged_in()) { 
?>
<div class="joins">
<h2><?php bp_loggedin_user_avatar( 'width=20&height=20' ) ?> &nbsp; <?php bp_loggedinuser_link() ?></h2> 
<ul id="fr-nav">
<li<?php if (  bp_is_page( BP_MEMBERS_SLUG ) || bp_is_member() ) : ?> class="selected"<?php endif; ?>>
<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members' ) ?>"><?php _e( 'Members' ) ?></a></li>
<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity' ) ?>"><?php _e( 'Activity' ) ?></a>
</li>
<?php if ( function_exists( 'groups_install' ) ) : ?>
<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups' ) ?>"><?php _e( 'Groups' ) ?></a>
</li>
<?php endif; ?>
<?php if ( function_exists( 'groups_install' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) get_site_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums' ) ?>"><?php _e( 'Forums' ) ?></a>
</li>
<?php endif; ?>
<?php if ( function_exists( 'bp_blogs_install' ) && bp_core_is_multisite() ) : ?>
<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs' ) ?>"><?php _e( 'Blogs' ) ?></a>
</li>
<?php endif; ?>
</ul>


</form>
</div>
<?php 
	} else {	
?>
<div class="join">
<h1><a href="<?php echo get_settings('home'); ?>/wp-signup.php"><?php _e( 'It is free', 'Detox') ?></a></h1>
<p><?php _e( 'It takes less than 30 seconds.', 'Detox') ?></p>
<h2><a href="<?php echo get_settings('home'); ?>/wp-signup.php"><?php _e( 'Join', 'Detox') ?> <?php bloginfo('name'); ?></a></h2>
</div>

<div class="joins">
<h1><?php _e( 'Login', 'Detox') ?></h1>
<form name="login-form" id="login-form" action="<?php echo site_url( 'wp-login.php' ) ?>" method="post">
<input type="text" name="log" id="user_login" value="<?php _e( 'Username', 'Detox') ?>" onfocus="if (this.value == '<?php _e( 'Username' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Username', 'Detox') ?>';}" />
<input type="password" name="pwd" id="user_pass" class="input" value="" />
<input type="submit" name="wp-submit" id="wp-submit" value="<?php _e( 'Log In', 'Detox') ?>"/>
<input type="hidden" name="redirect_to" value="<?php echo bp_root_domain() ?>" />
<input type="hidden" name="testcookie" value="1" />
</form>
</div>
<?php } ?>
</div>