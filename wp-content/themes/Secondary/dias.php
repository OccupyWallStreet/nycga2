<?php do_action( 'bp_inside_before_sidebar' ) ?>
<?php if ( is_user_logged_in() ) : ?>
<?php do_action( 'bp_before_sidebar_me' ) ?>

<div class="joins">
<div id="sidebar-me">
<h3><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h3>
<a href="<?php echo bp_loggedin_user_domain() ?>"><?php bp_loggedin_user_avatar( 'type=thumb&width=40&height=40' ) ?></a>
<a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'buddypress' ) ?></a>
<?php do_action( 'bp_sidebar_me' ) ?>
</div>

<?php do_action( 'bp_after_sidebar_me' ) ?>

<div class="navbarf fix">
<ul class="fix">
<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_member() ) : ?> class="selected"<?php endif; ?>>
<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'buddypress' ) ?>"><?php _e( 'Members', 'buddypress' ) ?></a>
</li>
<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>
<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'buddypress' ) ?>"><?php _e( 'Activity', 'buddypress' ) ?></a>
</li>
<?php if ( bp_is_active( 'blogs' ) && bp_core_is_multisite() ) : ?>
<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'buddypress' ) ?>"><?php _e( 'Blogs', 'buddypress' ) ?></a>
</li>
<?php endif; ?>
<?php endif; ?>
<?php if ( bp_is_active( 'groups' ) ) : ?>
<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'buddypress' ) ?>"><?php _e( 'Groups', 'buddypress' ) ?></a>
</li>
<?php if ( bp_is_active( 'forums' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'buddypress' ) ?>"><?php _e( 'Forums', 'buddypress' ) ?></a>
</li>
<?php endif; ?>
<?php endif; ?>

<?php do_action( 'bp_nav_items' ); ?>
</ul>
</div>

</div>

<?php else : ?>
<?php do_action( 'bp_before_sidebar_login_form' ) ?>

<div class="joins">
<h3><a href="<?php echo get_settings('home'); ?>/wp-signup.php"><?php _e( 'It is free') ?></a></h3>
<p>
<?php _e( 'It takes less than 30 seconds.') ?> <a href="<?php echo get_settings('home'); ?>/wp-signup.php"><?php _e( 'Join Us') ?></a>
</p>
<h5><?php _e( 'Login') ?></h5>
<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
<label><?php _e( 'Username', 'buddypress' ) ?><br />
<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>
<label><?php _e( 'Password', 'buddypress' ) ?><br />
<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>
<?php do_action( 'bp_sidebar_login_form' ) ?>
<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" />
<input type="hidden" name="testcookie" value="1" />
</form>
<?php do_action( 'bp_after_sidebar_login_form' ) ?>
</div>

<?php endif; ?>
	
<?php dynamic_sidebar( 'sidebar' ) ?>
<?php do_action( 'bp_inside_after_sidebar' ) ?>

<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('bbar') ) : ?>

<h3><?php _e( 'Recent') ?> <span><?php _e( 'Posts') ?></span></h3>
<ul class="list columns">
<?php $my_query = new WP_Query('showposts=4'); ?>
<?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
<li><a href="<?php the_permalink() ?>"><?php the_title() ?> | <?php the_time('n.j') ?></a></li>
<?php endwhile; ?>
</ul>

<h3><?php _e( 'Social' ) ?></h3>
<div class="social">
<?php
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
?>
<a title="<?php _e( 'Tweet this' ) ?>" href="http://twitter.com/home?status=Currently reading <?php
  echo curPageURL();
?>"><?php _e( 'Tweet this' ) ?></a>
<a href="http://digg.com/submit?phase=2&amp;url=<?php
  echo curPageURL();
?>"><?php _e( 'Digg it' ) ?></a>
<a href="http://del.icio.us/post?url=<?php
  echo curPageURL();
?>" rel="nofollow"><?php _e( 'Bookmark it' ) ?></a>
<a href="http://www.stumbleupon.com/submit?url=<?php
  echo curPageURL();
?>" rel="nofollow"><?php _e( 'Stumble it' ) ?></a>
<a href="http://www.designfloat.com/submit.php?url=<?php
  echo curPageURL();
?>" rel="nofollow"><?php _e( 'Float it' ) ?></a>
</div> 

<?php endif; ?> 