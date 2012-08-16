<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<?php include (TEMPLATEPATH . '/includes/options.php'); ?>

<title><?php
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

	?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/nav.css" media="screen" type="text/css" />

<?php font_show(); ?>

<!-- start theme options sync - using php to fetch theme option are deprecated and replace with style sync -->
<?php print "<style type='text/css' media='all'>"; ?>
<?php include (TEMPLATEPATH . '/theme-options.php'); ?>
<?php print "</style>"; ?>
<!-- end theme options sync -->

<!-- automatic-feed-links in functions.php -->
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<!-- favicon.ico location -->
<?php if(file_exists( WP_CONTENT_DIR . '/favicon.ico')) { //put your favicon.ico inside wp-content/ ?>
<link rel="icon" href="<?php echo WP_CONTENT_URL; ?>/favicon.ico" type="images/x-icon" />
<?php } elseif(file_exists( TEMPLATEPATH . '/favicon.ico')) { ?>
<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" type="images/x-icon" />
<?php } ?>

<!--[if IE 6]>
<style type="text/css">
#container, #tops, #bottom-container, #site-title img  {
behavior: url(<?php echo get_template_directory_uri(); ?>/js/iepngfix.php);
}
#nav { behavior: url(<?php echo get_template_directory_uri(); ?>/js/hover.htc); }
</style>
<![endif]-->

<!-- lets use js from google -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/iepngfix_tilebg.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/dropmenu.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/modernizr.js"></script>

<?php
$forum_root_slug = get_option('_bbp_forum_slug');
$topic_root_slug = get_option('_bbp_topic_slug');
$reply_root_slug = get_option('_bbp_reply_slug');
if( get_post_type() == 'forum' || get_post_type() == $forum_root_slug || get_post_type() == $topic_root_slug || get_post_type() == $reply_root_slug ) { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
#sidebar { display: none; }
#custom #post-entry { width: 96% !important; padding: 2% !important; border: 0 none !important; }
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
<?php } ?>


<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>


</head>

<body <?php body_class() ?> id="custom">

<div id="wrap">
<div id="header">

<div id="site-top">
<div id="site-title">
<?php $header_logo = get_option('tn_edufaq_header_logo'); if( $header_logo == "" ) { ?>
<h1><a href="<?php echo home_url(); ?>"><?php if("" == get_option('tn_edufaq_site_custom_name')) { echo bloginfo('name'); } else { echo get_option('tn_edufaq_site_custom_name'); } ?></a></h1>
<p><?php if("" == get_option('tn_edufaq_site_custom_description')) { echo bloginfo('description'); } else { echo get_option('tn_edufaq_site_custom_description'); } ?></p>
<?php } else { ?>
<a href="<?php echo home_url(); ?>"><img src="<?php echo stripslashes( wp_filter_post_kses($header_logo) ); ?> " alt="<?php _e('go back to home','edu-knows'); ?>" /></a>
<?php } ?>
</div>

<div id="site-feeds">
<p class="rss-feeds"><a href="<?php if("" == get_option('tn_edufaq_feedburner_url')) { echo bloginfo('rss2_url'); } else { echo get_option('tn_edufaq_feedburner_url'); } ?>"><?php _e("Subscribe to Feeds",TEMPLATE_DOMAIN); ?></a></p>
<p class="tweets"><a href="<?php echo get_option('tn_edufaq_twitter_url'); ?>"><?php _e("Follow Updates!",TEMPLATE_DOMAIN); ?></a></p>
</div>

</div>

<div id="mobile-search">
<?php get_mobile_navigation( $type='top', $nav_name='main-nav' ); ?>
</div>

<div id="site-nv">
<?php if ( function_exists( 'wp_nav_menu' ) ) { // Added in 3.0 ?>
<?php wp_nav_menu( array('theme_location' => 'main-nav', 'menu_id' => 'nav', 'container' => '', 'container_id' => '', 'fallback_cb' => 'revert_wp_menu_page')); ?>
<?php } else { ?>
<ul id="nav">
<li class="<?php if (is_home() || is_single()) { ?>home<?php } else { ?>page_item<?php } ?>"><a href="<?php echo site_url(); ?>" title="<?php _e('Home','edu-knows'); ?>"><?php _e('Home','edu-knows'); ?></a></li>
<?php wp_list_pages('title_li=&depth=0'); ?>
</ul>
<?php } ?>
</div>


</div>



<div id="container">
<div id="top-container">
<div id="content">
<div id="entry">

<?php
// Check to see if the header image has been removed
$header_image = get_header_image();
if ( ! empty( $header_image ) ) :
?>
<div id="custom-img-header">
<a href="<?php echo home_url( '/' ); ?>">
<img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="" />
</a>
</div>
<?php endif; // end check for removed header image ?>