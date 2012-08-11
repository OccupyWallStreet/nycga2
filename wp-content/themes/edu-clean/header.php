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

<?php include (TEMPLATEPATH . '/options.php'); ?>

<title>
<?php
if($bp_existed == 'true') { ?>
<?php bp_page_title(); ?>
<?php } else { ?>

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

<?php } ?>
</title>

<?php do_action( 'bp_head' ) ?>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<?php if(function_exists('font_show')) { font_show(); } ?>

<?php if($bp_existed == 'true') { ?>
<?php if ( function_exists( 'bp_sitewide_activity_feed_link' ) ) : ?>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php _e('Site Wide Activity RSS Feed', 'buddypress' ) ?>" href="<?php bp_sitewide_activity_feed_link() ?>" />
<?php endif; ?>

<?php if ( function_exists( 'bp_member_activity_feed_link' ) && bp_is_member() ) : ?>
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


<?php if($bp_existed != 'true') { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
body { padding: 0px !important; margin: 0px !important; }
<?php print "</style>"; ?>
<?php } ?>


<!--[if IE 6]>
<style type="text/css">
#list-benefits ul li, .site-logo img {behavior: url(<?php echo get_template_directory_uri(); ?>/_inc/js/iepngfix.htc);}
#nav { behavior: url(<?php echo get_template_directory_uri(); ?>/_inc/js/hover.htc); }
</style>
<![endif]-->


<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->


<?php wp_head(); ?>

<?php $tab_status = get_option('tn_edus_rss_network_status'); if($tab_status == 'yes') { ?>
<?php if( is_home() || is_front_page() ) { ?>
<script type="text/javascript">document.write('<style type="text/css">.tabber{display:none;}<\/style>');</script>
<?php } ?>
<?php } ?>

<?php if (strstr($_SERVER['REQUEST_URI'], '/wp-signup.php')) { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
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
#custom #post-entry { width: 100% !important; padding: 0% !important; border: 0 none !important; }
#custom div.post-blog-content {float:left !important;}
<?php else: ?>
#sidebar, .post-meta { display: none; } 
#container .bb-sidebar { display: inline !important; }
<?php endif; ?>


.bbp-forum-info {width: 40%;}
#content fieldset.bbp-form, #container fieldset.bbp-form, #wrapper fieldset.bbp-form { border: 1px solid #ccc;
  padding: 10px 20px;
}
table.bbp-topic tbody tr td, table.bbp-replies tbody tr td {
  background-color: transparent !important;
}
.bbp-forums .even, .bbp-topics .even { background: #f8f8f8; }
#container .post-blog-content {width: 100%;}
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

<?php do_action( 'bp_before_header' ) ?>

<div class="top-header-wrap">
<div id="top-header">
<div class="h-content">

<div class="top-h-content">
<div class="site-logo">

<?php if($tn_edus_header_logo == ""){ ?>
<h1><a href="<?php echo home_url(); ?>" title="<?php _e("Click here to go to the site homepage",TEMPLATE_DOMAIN); ?>"><?php bloginfo('name'); ?></a></h1>
<?php } else { ?>
<a href="<?php echo home_url(); ?>" title="<?php _e("Click here to go to the site homepage", TEMPLATE_DOMAIN); ?>">
<img src="<?php echo stripslashes($tn_edus_header_logo); ?>" alt="<?php _e("homepage", TEMPLATE_DOMAIN); ?>" /></a>
<?php } ?>



</div>


<div id="mobile-search">
<?php get_mobile_navigation( $type='top', $nav_name='main-nav' ); ?>
</div>


<?php if( is_multisite() ){
if( function_exists('get_sitestats')) { ?>
<div class="site-stats"><span><?php _e("Currently powering", TEMPLATE_DOMAIN); ?> <?php
$stats = get_sitestats();
$tmp_user_count = number_format ($stats[ 'users' ] );
$tmp_blog_count = number_format ($stats[ 'blogs' ] );
print "<strong>" . $tmp_blog_count . __(" blogs", TEMPLATE_DOMAIN) . "</strong>";
;?></span></div>
<?php } } ?>

</div>

<div class="bottom-h-content">
<div class="navigation">
<?php if ( function_exists( 'wp_nav_menu' ) ) { // Added in 3.0 ?>
<ul id="nav">
<?php echo bp_wp_custom_nav_menu($get_custom_location='main-nav', $get_default_menu='revert_wp_menu_page'); ?>
</ul>
<?php } else { ?>
<ul id="nav">
<li<?php if(is_front_page()) { echo " id='home'"; } ?>><a href="<?php echo home_url(); ?>" title="<?php _e('Go back to home', TEMPLATE_DOMAIN); ?>"><?php _e('Home', TEMPLATE_DOMAIN); ?></a></li>
<?php wp_list_pages('title_li=&depth=0'); ?>
</ul>
<?php } ?>
</div>
</div>

</div>

</div>

<?php do_action( 'bp_header' ) ?>

</div>





<?php if ( !is_user_logged_in() ) { ?>
<div id="main-header">
<div id="main-header-content">
<div id="main-header-inner">

<?php if( is_home() && !strstr($_SERVER['REQUEST_URI'], '/wp-signup.php') ) { ?>

<div id="main-header-inner-content">
<h4><?php echo stripslashes($tn_edus_header_text); ?></h4>
<br /><br />
<div id="intro-log">
<div id="list-benefits">
<?php echo stripslashes($tn_edus_header_listing); ?>
</div>

<?php if ( !strstr($_SERVER['REQUEST_URI'], '/wp-signup.php') ) { ?>
<div id="intro-package">
<div id="edublog-free"><h3><?php _e("Get started in seconds for free",TEMPLATE_DOMAIN); ?></h3><p><a href="<?php echo home_url(); ?>/<?php if( $bp_existed == 'true' ) { //check if bp existed ?><?php echo bp_get_root_slug( 'register' ) . '/'; ?><?php } else { ?>wp-signup.php<?php } ?>" title="<?php _e("Get yourself a free blog",TEMPLATE_DOMAIN); ?>"><?php _e("Join Here!",TEMPLATE_DOMAIN); ?></a></p></div>
</div>
<?php } ?>

</div>
</div>
<?php } else { ?>
<div id="main-header-inner-content">
<h4><?php echo stripslashes($tn_edus_header_text); ?></h4>
</div>
<?php } ?>

</div>
</div>
</div>

<?php } else if( is_user_logged_in() ) { ?>
<div id="main-header">
<div id="main-header-content">
<div id="main-header-inner">
<div id="main-header-inner-content">
<h4><?php echo stripslashes($tn_edus_header_logged_text); ?></h4>
</div>
</div>
</div>
</div>

<?php } ?>

<?php do_action( 'bp_after_header' ) ?>
<div id="wraps">

<?php do_action( 'bp_before_container' ) ?>
<div id="container">

<div class="content">
<?php do_action( 'bp_before_content' ) ?>