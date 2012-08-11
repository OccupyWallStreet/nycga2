<!DOCTYPE html>
<!--[if lt IE 7 ]>	<html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>		<html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>		<html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>		<html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php include ( TEMPLATEPATH . '/options-var.php' ); ?>

<title>
<?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', TEMPLATE_DOMAIN ), max( $paged, $page ) );

	?>
</title>

<?php do_action( 'bp_head' ) ?>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<?php if( function_exists('font_show')) { font_show(); } ?>

<?php if($bp_existed == 'true') { ?>
<?php if ( function_exists( 'bp_sitewide_activity_feed_link' ) ) : ?>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php _e('Site Wide Activity RSS Feed', 'buddypress' ) ?>" href="<?php bp_sitewide_activity_feed_link() ?>" />
<?php endif; ?>
<?php if ( function_exists( 'bp_member_activity_feed_link' ) && bp_is_user() ) : ?>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php bp_displayed_user_fullname() ?> | <?php _e( 'Activity RSS Feed', 'buddypress' ) ?>" href="<?php bp_member_activity_feed_link() ?>" />
<?php endif; ?>
<?php if ( function_exists( 'bp_group_activity_feed_link' ) && bp_is_group() ) : ?>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php bp_current_group_name() ?> | <?php _e( 'Group Activity RSS Feed', 'buddypress' ) ?>" href="<?php bp_group_activity_feed_link() ?>" />
<?php endif; ?>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> <?php _e( 'Blog Posts RSS Feed', 'buddypress' ) ?>" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> <?php _e( 'Blog Posts Atom Feed', 'buddypress' ) ?>" href="<?php bloginfo('atom_url'); ?>" />
<?php } ?>

<!-- automatic-feed-links in functions.php -->
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<!-- favicon.ico location -->
<?php if(file_exists( WP_CONTENT_DIR . '/favicon.ico')) { //put your favicon.ico inside wp-content/ ?>
<link rel="icon" href="<?php echo WP_CONTENT_URL; ?>/favicon.ico" type="images/x-icon" />
<?php } elseif(file_exists( TEMPLATEPATH . '/favicon.ico')) { ?>
<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" type="images/x-icon" />
<?php } ?>


<!--[if IE 6]>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/_inc/js/drop_down.js"></script>
<style type="text/css">
.item-list li .item-avatar { margin:0 6px 0 0; }
.linkbox { width: 20%; padding-right:3% !important; }
#nav li { behavior: url(<?php echo get_template_directory_uri(); ?>/_inc/js/hover.htc); }
#logo, #right-panel h4, #socials #the-mascot, #socials-single #the-mascot, .img-services, .start-free-small, #footer  {
behavior: url(<?php echo get_template_directory_uri(); ?>/_inc/js/iepngfix.htc); }
</style>
<![endif]-->


<?php if($bp_existed == 'true') {  ?>
<?php if ( '1' == get_option( 'hide-loggedout-adminbar' ) && !is_user_logged_in() ) {  ?>
<?php print "<style type='text/css' media='screen'>"; ?>
body { padding-top: 0px !important;
<?php print "</style>"; ?>
<?php } ?>

<?php if(bp_is_register_page() || bp_is_activation_page()) { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
#content {
border: 0px none !important;
}
#custom #content, #content .padder { width: 96% !important; }
#content .standard-form {
-moz-border-radius-bottomleft:6px;
-moz-border-radius-bottomright:6px;
-moz-border-radius-topleft:6px;
-moz-border-radius-topright:6px;
background:#EEEEEE none repeat scroll 0 0;
border-color:#CCCCCC #999999 #999999 #CCCCCC;
border-style:solid;
border-width:1px;
float:left;
margin:0;
padding:3%;
width:93%;
}
#sidebar { display: none; }
span.label { width: 100%; float: left; }
<?php print "</style>"; ?>
<?php } } ?>


<?php if( !function_exists( 'bp_exists' ) ) { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
body { padding: 0px !important; margin: 0px !important; }
<?php print "</style>"; ?>
<?php } ?>


<?php if($tn_blogsmu_section_one_headline == "" || strstr($_SERVER['REQUEST_URI'], '/wp-signup.php')) { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
#intro-content { background: transparent none !important; }
<?php print "</style>"; ?>
<?php } ?>

<?php if($tn_blogsmu_sidebar_position == "right") { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
#content, #container #post-entry {  width: 65% !important; float: left !important; border-right: 1px solid #ddd; border-left: 0px none !important; padding: 0px 30px 35px 0px !important;}
#container #sidebar { float: right !important; }
<?php print "</style>"; ?>
<?php } ?>


<?php wp_head(); ?>


<?php if(is_front_page() || is_home()) {
$feat_style = get_option('tn_blogsmu_featured_blk_option');
if(($feat_style == 'Featured Slider Posts') || ($feat_style == 'Featured Slider Categories') || ($feat_style == 'BP Album Rotate')) { ?>
<script type="text/javascript">
jQuery.noConflict();
var $je = jQuery;
 $je(window).load(function() {
        $je('#slider').nivoSlider();
    });
</script>
<?php } } ?>


<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<?php if (strstr($_SERVER['REQUEST_URI'], '/wp-signup.php')) { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
#content, #post-entry {
  border-left: 0px none;
  float: left;
  padding: 0;
  width: 100% !important;
}
.mu_register { width: 92%; padding: 4%; background: #eee; border: 1px solid #ccc; }
<?php print "</style>"; ?>
<?php } ?>


<?php
$forum_root_slug = get_option('_bbp_forum_slug');
$topic_root_slug = get_option('_bbp_topic_slug');
$reply_root_slug = get_option('_bbp_reply_slug');
if( get_post_type() == 'forum' || get_post_type() == $forum_root_slug || get_post_type() == $topic_root_slug || get_post_type() == $reply_root_slug ) { ?>
<?php print "<style type='text/css' media='screen'>"; ?>

<?php if ( !is_active_sidebar( 'bbpress-sidebar' ) ) : ?>
#sidebar, .post-meta { display: none; }
#custom #post-entry { width: 96% !important; padding: 2% !important; border: 0 none !important; }
<?php else: ?>
#container .bb-sidebar { display: inline !important; }
#sidebar, .post-meta { display: none !important; }
<?php endif; ?>



.bbp-forum-info {width: 40%;}
#content fieldset.bbp-form, #container fieldset.bbp-form, #wrapper fieldset.bbp-form { border: 1px solid #ccc;
  padding: 10px 20px;
}
.bbp-forums .even, .bbp-topics .even { background: #f8f8f8; }
#container .post-content {width: 100%;}
.bbp-breadcrumb {margin: 0 0 1em 0;}
#bbp_topic_title { width: 70%; }
.bbp-reply-author {width: 30%;}
.bbp-topic-meta {font-size: 0.875em;}
.bbp-reply-author img {margin: 0 1em 0 0;}
#container .bbp-reply-content,#container .bbp-reply-author {padding: 1.4em 1em;}
.bbp-topics td {padding: 1em;}
<?php print "</style>"; ?>
<?php } else { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
#container .bb-sidebar { display: none !important; }
<?php print "</style>"; ?>
<?php } ?>

<!-- start theme options sync - using php to fetch theme option are deprecated and replace with style sync -->
<?php print "<style type='text/css' media='screen'>"; ?>
<?php include (TEMPLATEPATH . '/theme-options.php'); ?>
<?php print "</style>"; ?>
<!-- end theme options sync -->

</head>


<body <?php body_class() ?> id="custom">

<div id="top-bg">
<div class="top-bg-inner">
<div class="alignleft">
<?php if($tn_blogsmu_header_logo != "") { ?>
<a href="<?php echo home_url(); ?>"><img src="<?php echo stripslashes($tn_blogsmu_header_logo); ?>" alt="logo" /></a>
<?php } else { ?>
<a href="<?php echo home_url(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/logo.png" alt="logo" /></a>
<?php } ?>
</div>


<?php
if($tn_blogsmu_home_login_block == 'disable') { ?>
<?php } else { ?>
<div class="alignright">
<?php if (is_user_logged_in()) { ?>
<?php if( $bp_existed == 'true' ) { global $bp; //check if bp existed ?>
<div id="li-user">
<div class="user-tab"><?php _e('Welcome back', TEMPLATE_DOMAIN); ?>, <a href="<?php bp_loggedin_user_link() ?>"><?php echo $bp->loggedin_user->fullname; ?></a></div>
</div>
<?php } else {
global $user_ID, $user_identity, $user_url, $user_email;
get_currentuserinfo(); ?>
<div id="li-user"><?php _e('Welcome back', TEMPLATE_DOMAIN); ?>, <a href="<?php echo site_url(); ?>/wp-admin/profile.php"><strong><?php echo $user_identity; ?></strong></a> / <?php $mywp_version = get_bloginfo('version'); if ($mywp_version >= '2.7') { ?> <a href="<?php echo wp_logout_url( get_site_url()); ?>"><?php _e('Log out', TEMPLATE_DOMAIN); ?></a> <?php } else { ?> <a href="<?php echo site_url(); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account', TEMPLATE_DOMAIN); ?>"><?php _e('Log out', TEMPLATE_DOMAIN); ?></a> <?php } ?></div>
<?php } ?>
<?php } else { ?>
<?php _e("Already a member?",TEMPLATE_DOMAIN); ?> <a href="<?php echo site_url(); ?>/<?php echo get_members_login_slug(); ?>/"><?php _e("Login here",TEMPLATE_DOMAIN); ?></a>
<?php } ?>
</div>
<?php } ?>
</div>
</div>


<div id="navigation">
<div id="page-navigation">
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
<li<?php if(is_front_page()) { echo " id='home'"; } ?>><a href="<?php echo site_url(); ?>" title="<?php _e('Go back to home', TEMPLATE_DOMAIN); ?>"><?php _e('Home', TEMPLATE_DOMAIN); ?></a></li>
<?php wp_list_pages('title_li=&depth=0'); ?>
</ul>
<?php } ?>
</div>

<div id="mobile-search">
<?php get_search_form(); ?>
<?php if ( is_user_logged_in() ) : ?>
<?php get_mobile_navigation( $type='top', $nav_name="logged-in-nav" ); ?>
<?php else: ?>
<?php get_mobile_navigation( $type='top', $nav_name="not-logged-in-nav" ); ?>
<?php endif; ?>
</div>

</div>

<?php do_action( 'bp_before_header' ) ?>
<div id="header">
<div id="header-gfx">
<div id="header-gfx-inner">
<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
<?php if ( $bp_front_is_activity == 'true' ) { ?>
<?php
if ( is_front_page() || !bp_current_component() ) {
locate_template ( array('lib/templates/wp-template/panel-home.php'), true );
} else {
locate_template ( array('lib/templates/wp-template/panel-index.php'), true );
custom_img_header_call();
}
?>

<?php } else { ?>

<?php
if(!is_front_page()) {
locate_template ( array('lib/templates/wp-template/panel-index.php'), true );
custom_img_header_call();
} else {
locate_template ( array('lib/templates/wp-template/panel-home.php'), true );
}
?>

<?php } ?>

<?php } else { //if bp not active ?>

<?php
if(is_front_page() && !strstr($_SERVER['REQUEST_URI'], '/wp-signup.php')) {
locate_template ( array('lib/templates/wp-template/panel-home.php'), true );
} else if (strstr($_SERVER['REQUEST_URI'], '/wp-signup.php')) {
locate_template ( array('lib/templates/wp-template/panel-index.php'), true );
} else {
locate_template ( array('lib/templates/wp-template/panel-index.php'), true );
custom_img_header_call();
}
?>

<?php } ?>



</div>
</div>

<?php do_action( 'bp_header' ) ?>

</div>

<?php do_action( 'bp_after_header' ) ?>


<?php if( is_front_page() && !strstr($_SERVER['REQUEST_URI'], '/wp-signup.php') ) {
  locate_template ( array('lib/templates/wp-template/sections.php'), true );
  } ?>

<div id="wrapper">

<?php do_action( 'bp_before_container' ) ?>

<div id="container">

<?php do_action( 'bp_before_content' ) ?>

<div class="content">

<!-- start content -->