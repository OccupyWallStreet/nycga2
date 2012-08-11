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
<?php include (TEMPLATEPATH . '/options-var.php'); ?>

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
<?php if(function_exists('font_show')) { font_show(); } ?>

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


<?php if( is_user_logged_in() ) { ?>
<style type="text/css" media="all">
#commentform .labelcom { float: left; width: 100% !important; }
</style>
<?php } ?>


<?php if ( !function_exists( 'bp_exists' ) ) { ?>
<style type='text/css' media='screen'>
body { padding: 0px !important; margin: 0px !important; }
</style>
<?php } ?>


<!--[if IE 6]>
<style type="text/css">
<?php if($tn_buddysocial_blog_intro_header_color != "") { ?>
#custom #top-header {
behavior: url(<?php echo get_template_directory_uri(); ?>/_inc/js/iepngfix.htc);
background: <?php echo $tn_buddysocial_blog_intro_header_color; ?> none!important;
}
<?php } else { ?>
#custom #top-header {
background: #bcc7dd none !important;
}
<?php } ?>
div.item-avatar img { width: auto; height: auto; }
</style>
<![endif]-->


<?php
$layout_style = get_option('tn_buddysocial_blog_home_layout_style');
if($layout_style == '2-column') { ?>
<style type="text/css" media="all">
#custom #home-left #box-left {
width: 100% !important; padding: 0px !important; margin: 0px !important;
}
</style>
<?php } ?>

<?php
$index_style = get_option('tn_buddysocial_blog_index_layout_style');
if($index_style == '2-column') { ?>
<style type="text/css" media="all">
#custom #post-entry {
width: 655px;
padding-right: 15px !important;
border-right: 1px solid #ddd;
}
</style>
<?php } ?>


<?php if($bp_existed == 'true') { ?>
<?php if ( '1' == get_option( 'hide-loggedout-adminbar' ) && !is_user_logged_in() ) {  ?>
<style type="text/css" media="screen">
body { padding-top: 0px !important;
</style>
<?php } ?>

<?php if($bp_front_is_activity == 'true') { ?>
<style type="text/css" media="screen">
.home-page #content {width: 666px; float: left;} 
</style>
<?php } ?>

<style type="text/css" media="all">
<?php $member_page_layout = get_option('tn_buddysocial_member_page_layout_style');
if($member_page_layout == '2-column') { ?>
#custom #member-left { display: none; }
#custom #content { width: 650px; padding-right: 30px !important; }
#custom #item-header-avatar, #custom #item-actions { display: inline !important; }
<?php } else if($member_page_layout == '1-column') { ?>
<?php if( bp_current_component() && !bp_is_blog_page() ) { ?>
#custom #member-left, #custom #right-sidebar { display: none !important; }
<?php } ?>
.activity #right-sidebar { display: none !important; }
#custom #content { width: 100%; padding: 0px !important; border: 0 none; }
#custom #item-header-avatar, #custom #item-actions { display: inline !important; }
<?php } ?>
</style>
<?php }  ?>

<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->


<?php wp_head(); ?>


<?php
$featured_status = get_option('tn_buddysocial_blog_featured_style_status');
if($featured_status == "enable") {  ?>
<?php if( is_home() || is_front_page() ) { ?>
<script type="text/javascript">
function startGallery() {
var myGallery = new gallery($('myGallery'), {
timed: true,
showArrows: true,
showCarousel: false,
embedLinks: true
});
document.gallery = myGallery;
};
window.onDomReady(startGallery);
</script>
<?php } } ?>

<?php if (strstr($_SERVER['REQUEST_URI'], '/wp-signup.php')) { ?>
<style type='text/css' media='screen'>
#custom div#content {
  border: 0px none;
  float: left;
  padding: 0% !important;
  margin: 0;
  width: 100% !important;
}
.mu_register h2 {font-size: 20px;}
.mu_register { width: 96% !important; padding: 2% !important; background: #f8f8f8; border: 1px solid #ccc; float:left; }
.mu_register form {padding: 0px !important; background: transparent none !important;}
#setupform p label {
  display: inline !important;
}
#setupform input, #setupform textarea, #activateform input {
  font-size: 1.2em;
  width: auto;
  max-width: 400px;
}
#content .mu_register p { margin: 15px 0px 0px; }
</style>
<?php } ?>

<?php
$forum_root_slug = get_option('_bbp_forum_slug');
$topic_root_slug = get_option('_bbp_topic_slug');
$reply_root_slug = get_option('_bbp_reply_slug');
if( get_post_type() == 'forum' || get_post_type() == $forum_root_slug || get_post_type() == $topic_root_slug || get_post_type() == $reply_root_slug ) { ?>
<style type='text/css' media='screen'>

<?php if ( !is_active_sidebar( 'bbpress-sidebar' ) ) : ?>
#left-sidebar,#right-sidebar { display: none; }
#custom #post-entry { width: 100% !important; padding: 0 !important; border: 0 none !important; }
#custom div.post-content {float:left !important;}
<?php else: ?>
#container .bb-sidebar { display: inline !important; }
#left-sidebar, #right-sidebar { display: none !important; }
#container .post-content {
  float: left !important;
  width: 100%;
}
#post-entry {
width: 655px !important;
border-right: 1px solid #DDDDDD;
padding-right: 15px !important;
}
<?php endif; ?>

.bbp-forum-info {width: 40%;}
#content fieldset.bbp-form, #container fieldset.bbp-form, #wrapper fieldset.bbp-form { border: 1px solid #ccc;
  padding: 10px 20px;
}
.bbp-topic-title {font-size: 1.125em !important;}
p.bbp-topic-meta,.post-meta {margin: 0;}
table.bbp-topic tbody tr td, table.bbp-replies tbody tr td {
  background-color: transparent !important;
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
</style>
<?php } ?>


<!-- start theme options sync - using php to fetch theme option are deprecated and replace with style sync -->
<style type='text/css' media='screen'>
<?php include (TEMPLATEPATH . '/theme-options.php'); ?>
</style>
<!-- end theme options sync -->

</head>

<body <?php body_class() ?> id="custom">

<?php locate_template( array('lib/templates/wp-template/top-bar.php'), true );  ?>



<?php if (!strstr($_SERVER['REQUEST_URI'], '/wp-signup.php')) { ?>

<?php if($bp_existed == 'true') { ?>
<?php if ( !bp_is_register_page() && !bp_is_activation_page() ) { ?>
<?php if( !is_user_logged_in() || wpmudev_is_customize_preview() ) {  locate_template( array('lib/templates/wp-template/top-header.php'), true ); } ?>
<?php } ?>
<?php } else { ?>
<?php if( ( !is_user_logged_in() || wpmudev_is_customize_preview() ) && is_home() ) {  locate_template( array('lib/templates/wp-template/top-header.php'), true ); } ?>
<?php } ?>

<?php } ?>

<div id="wrapper">

<?php do_action( 'bp_before_container' ) ?>

<div id="container">



<?php
$tn_buddysocial_ads_code = get_option('tn_buddysocial_ads_code');
if($tn_buddysocial_ads_code != ''){ ?>
<div id="option-ads"><?php echo stripcslashes($tn_buddysocial_ads_code); ?></div>
<?php } ?>

<?php do_action( 'bp_before_header' ) ?>

<?php if( is_home() && !is_user_logged_in()) { ?>
<?php } else if( is_user_logged_in() ) { ?>
<?php
$tn_buddysocial_header_on = get_option('tn_buddysocial_header_on');
if('' != get_header_image() ) {
if($tn_buddysocial_header_on == 'enable') { ?>
<div id="custom-img-header">
<a href="<?php echo site_url(); ?>"><img src="<?php header_image(); ?>" alt="<?php bloginfo('name'); ?>" /></a>
</div>
<?php } else { ?>
<?php } ?>
<?php } } ?>

<?php do_action( 'bp_after_header' ) ?>

<?php if($bp_existed == 'true') { ?>

<?php if ( !bp_is_blog_page() && !bp_is_register_page() && !bp_is_activation_page() ) : ?>
<div class="content">

<?php do_action( 'bp_before_content' ) ?>

<?php if ( !bp_is_directory() ) : ?>
<?php get_sidebar('member-left'); ?>
<?php endif; ?>

<?php endif; ?>

<?php } else { ?>

<div class="content">
<?php do_action( 'bp_before_content' ) ?>
<?php } ?>