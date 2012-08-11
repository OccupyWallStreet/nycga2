<?php include (TEMPLATEPATH . '/options-var.php'); ?>

<div id="top-bar">
<div class="content-wrap">
<div id="userbar">

<div id="htop">
<div id="optionsbar">

<ul id="options-nav"> <!-- .bptopnav -->
<?php if (is_user_logged_in()) { ?>

<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
<li id="li-user"><?php _e('Welcome back', TEMPLATE_DOMAIN); ?>, <?php global $bp; echo $bp->loggedin_user->fullname; ?></li>
<?php bp_get_loggedin_user_nav() //bp_adminbar_account_menu() ?>
<?php } else { global $user_ID, $user_identity, $user_url, $user_email; get_currentuserinfo(); ?>
<li id="li-user"><?php _e('Welcome back', TEMPLATE_DOMAIN); ?>, <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><strong><?php echo $user_identity; ?></strong></a> / <?php $mywp_version = get_bloginfo('version'); if ($mywp_version >= '2.7') { ?> <a href="<?php echo wp_logout_url(get_bloginfo('url')); ?>"><?php _e('Log out', TEMPLATE_DOMAIN); ?></a> <?php } else { ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account', TEMPLATE_DOMAIN); ?>"><?php _e('Log out', TEMPLATE_DOMAIN); ?></a> <?php } ?></li>
<?php } ?>

<?php } ?>
</ul>



</div>

<div id="home-logo">
<?php
$tn_buddysocial_header_logo = get_option('tn_buddysocial_header_logo');
if($tn_buddysocial_header_logo == '') { ?>
<h1><a href="<?php echo site_url(); ?>"><?php bloginfo('name'); ?></a></h1>
<p><?php bloginfo('description'); ?></p>
<?php } else { ?>
<a href="<?php echo site_url(); ?>" title="<?php _e('Click here to go to the site homepage',TEMPLATE_DOMAIN); ?>">
<img src="<?php echo stripslashes($tn_buddysocial_header_logo); ?>" alt="<?php bloginfo('name'); ?> <?php _e('homepage', TEMPLATE_DOMAIN); ?>" /></a>
<?php } ?>
</div>
</div>



<?php if ( function_exists( 'wp_nav_menu' ) ) { // Added in 3.0 ?>
<ul id="nav">
<?php if (is_user_logged_in()) { ?>
<?php echo bp_wp_custom_nav_menu($get_custom_location='logged-in-nav', $get_default_menu='revert_wp_menu_page'); ?>
<?php } else { ?>
<?php echo bp_wp_custom_nav_menu($get_custom_location='not-logged-in-nav', $get_default_menu='revert_wp_menu_page'); ?>
<?php } ?>
</ul>
<?php } else { ?>
<ul id="nav">
<?php wp_list_pages('title_li=&depth=0'); ?>
</ul>
<?php } ?>



<div id="mobile-search">
<?php if (is_user_logged_in()) { ?>
<?php get_mobile_navigation( $type='top', $nav_name='logged-in-nav' ); ?>
<?php } else { ?>
<?php get_mobile_navigation( $type='top', $nav_name='not-logged-in-nav' ); ?>
<?php } ?>
</div>




</div>
</div>
</div>