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

<?php include( TEMPLATEPATH . '/options-var.php' ); ?>

<title>
<?php if($bp_existed == 'true') { //check if bp existed ?>
<?php bp_page_title() ?>
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

<?php if( function_exists('font_show')) { font_show(); } ?>

<?php if($bp_existed == 'true') { ?>
<?php print "<style type=\"text/css\" media=\"all\">"; ?>

<?php if( bp_current_component() || bp_is_directory() ) { ?>
<?php $member_page_layout = get_option('tn_buddycorp_member_page_layout_style');

if($member_page_layout == '2-column') { ?>
#custom #userbar { display: none; }
#custom #content { width: 74%; }
<?php } else if($member_page_layout == '1-column') { ?>
#custom #userbar, #custom #profile-right { display: none; }
#custom .content #content { width: 96% !important; padding: 1.6% !important; max-width: 100% !important; }
<?php } else { ?>
<?php if( !bp_is_directory() ) { //we need this so member/profile did not break ?>
#custom #content { width: 53%; }
<?php } ?>
<?php } ?>

<?php }  ?>

<?php if( bp_is_activation_page() || bp_is_register_page() ) { ?>
#custom .content #content { width: 96% !important; padding: 1.6% !important; margin: 20px 0px 0px;  max-width: 100% !important; }
#custom #userbar, #custom #profile-right, #call-action { display: none; }
<?php } ?>

<?php print "</style>"; ?>
<?php }  ?>


<?php if($bp_existed == 'true') { //check if bp existed ?>
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
<?php } elseif(file_exists( WP_CONTENT_DIR . '/favicon.png')) { //put your favicon.png inside wp-content/ ?>
<link rel="icon" href="<?php echo WP_CONTENT_URL; ?>/favicon.png" type="images/x-icon" />
<?php } elseif(file_exists( TEMPLATEPATH . '/favicon.ico')) { ?>
<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" type="images/x-icon" />
<?php } elseif(file_exists( TEMPLATEPATH . '/favicon.png')) { ?>
<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png" type="images/x-icon" />
<?php } ?>


<!--[ if IE 6 ]>
<style type="text/css">
blockquote img { height: auto; width: 100%; }
#nav, #pnav { behavior: url(<?php echo get_template_directory_uri(); ?>/_inc/js/hover.htc); }
</style>
<![endif]-->

<!--[if gte IE 9]>
<style type="text/css">
.gradient, #cf .st, .form-submit #submit, #cf .st:hover, .form-submit #submit:hover, .reply a, .reply a:hover, .button, .button:hover {
filter: none;
}
</style>
<![endif]-->


<?php print "<style type='text/css' media='screen'>";
if($bp_existed == 'true') { //check if bp existed
if ( '1' == get_option( 'hide-loggedout-adminbar' ) && !is_user_logged_in() ) {  ?>
body { padding-top: 0px !important;
<?php } ?>
<?php } else { ?>body { padding: 0px !important; margin: 0px !important; }<?php } ?>
<?php print "</style>"; ?>


<?php if(function_exists('fbc_display_login_button')) { ?>
<!-- facebook plugin component css -->
<style type='text/css' media='screen'>
#searchbox #user_login, #searchbox #user_pass { width: 90px; }
</style>
<!-- end facenook plugin component css -->
<?php } ?>


<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<?php wp_head(); ?>

<?php if( is_front_page() || is_home() ) { ?>
<?php $home_featured_block_style = get_option('tn_buddycorp_home_featured_block_style'); if($home_featured_block_style == 'slideshow') { ?>

<script type="text/javascript">
jQuery.noConflict();
var $je = jQuery;
$je(window).load(function() {
    $je('#slider').nivoSlider({
        effect: 'fade', // Specify sets like: 'fold,fade,sliceDown'
        slices: 15, // For slice animations
        boxCols: 8, // For box animations
        boxRows: 4, // For box animations
        animSpeed: 500, // Slide transition speed
        pauseTime: 5000, // How long each slide will show
        startSlide: 0, // Set starting Slide (0 index)
        directionNav: true, // Next & Prev navigation
        directionNavHide: true, // Only show on hover
        controlNav: true, // 1,2,3... navigation
        controlNavThumbs: false, // Use thumbnails for Control Nav
        controlNavThumbsFromRel: false, // Use image rel for thumbs
        controlNavThumbsSearch: '.jpg', // Replace this with...
        controlNavThumbsReplace: '_thumb.jpg', // ...this in thumb Image src
        keyboardNav: true, // Use left & right arrows
        pauseOnHover: true, // Stop animation while hovering
        manualAdvance: false, // Force manual transitions
        captionOpacity: 0.8, // Universal caption opacity
        prevText: 'Prev', // Prev directionNav text
        nextText: 'Next', // Next directionNav text
        randomStart: false, // Start on a random slide
        beforeChange: function(){}, // Triggers before a slide transition
        afterChange: function(){}, // Triggers after a slide transition
        slideshowEnd: function(){}, // Triggers after all slides have been shown
        lastSlide: function(){}, // Triggers when last slide is shown
        afterLoad: function(){} // Triggers when slider has loaded
    });
});
</script>



<?php } } ?>

<?php if (strstr($_SERVER['REQUEST_URI'], '/wp-signup.php')) { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
#custom div#content {
  border: 0px none;
  float: left;
  padding: 5% !important;
  margin: 0;
  width: 90% !important;
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
<?php include( TEMPLATEPATH . '/theme-options.php' ); ?>
<?php print "</style>"; ?>
<!-- end theme options sync -->

<?php //load stuff
$tn_buddycorp_call_signup_on = get_option('tn_buddycorp_call_signup_on');
$tn_buddycorp_header_on = get_option('tn_buddycorp_header_on');
$get_current_scheme = get_option('tn_buddycorp_custom_style');
?>

<?php if(($get_current_scheme == '') || ($get_current_scheme == 'default.css')) { ?>
<?php } else { ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/_inc/preset-styles/<?php echo $get_current_scheme; ?>" type="text/css" media="screen" />
<?php } ?>

</head>

<body <?php body_class() ?> id="custom">

<div id="wrapper"<?php if ( $bp_front_is_activity == "true" )  { ?> class="activity_on_front"<?php } else { ?> class="activity_not_front"<?php } ?>>

<div id="container">

<?php do_action( 'bp_before_header' ) ?>

<div id="top-header">

<div id="pg-nav">
<?php if ( function_exists( 'wp_nav_menu' ) ) { // Added in 3.0 ?>
<?php wp_nav_menu( array('theme_location' => 'top-nav', 'menu_id' => 'pnav', 'container' => '', 'container_id' => '', 'fallback_cb' => ''));
?>
<?php } ?>
</div>

<div id="custom-logo">
<?php
$tn_buddycorp_header_logo = get_option('tn_buddycorp_header_logo');
if($tn_buddycorp_header_logo == '') { ?>
<h1><a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a></h1>
<p><?php bloginfo('description'); ?></p>
<?php } else { ?>
<a href="<?php echo home_url(); ?>" title="<?php _e('Click here to go to the site homepage', TEMPLATE_DOMAIN); ?>">
<img src="<?php echo stripslashes($tn_buddycorp_header_logo); ?>" alt="<?php bloginfo('name'); ?> <?php _e('homepage', TEMPLATE_DOMAIN); ?>" /></a>
<?php } ?>
</div>





</div>

<?php do_action( 'bp_after_header' ) ?>


<div id="navigation">
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





<?php
if( $tn_buddycorp_header_on == 'enable') { ?>
<?php if('' != get_header_image() ) { ?>
<div id="custom-img-header">
<div class="custom-img-header"><a href="<?php echo home_url(); ?>"><img src="<?php header_image(); ?>" alt="<?php bloginfo('name'); ?>" /></a></div>
</div>
<?php } } ?>



<?php do_action( 'bp_before_search_login_bar' ) ?>

<?php if($bp_existed == 'true') { //check if bp existed ?>

<?php if( !bp_is_register_page() ) { ?>

<div id="mobile-search">
<?php get_mobile_navigation( $type='top', $nav_name='main-nav' ); ?>
</div>

<div class="gradient" id="searchbox">

<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
<input type="text" id="search-terms" name="search-terms" value="" />
<?php echo bp_search_form_type_select() ?>
<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', TEMPLATE_DOMAIN ) ?>" />
<?php wp_nonce_field( 'bp_search_form' ) ?>
</form>
<?php do_action( 'bp_search_login_bar' ) ?>


<div id="fc_wrap">
<?php if ( !is_user_logged_in() ) : ?>

<?php do_action( 'bp_before_sidebar_login_form' ) ?>

<form name="login-form" class="mylogform" id="login-form" action="<?php echo site_url( '/wp-login.php', 'login' ) ?>" method="post">
<input type="text" name="log" id="user_login" value="<?php _e( 'Username', TEMPLATE_DOMAIN) ?>" onfocus="if (this.value == '<?php _e( 'Username', TEMPLATE_DOMAIN) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Username', TEMPLATE_DOMAIN) ?>';}" />
<input type="password" name="pwd" id="user_pass" class="input" value="" />

<input type="checkbox" checked="checked" name="rememberme" id="rememberme" value="forever" title="<?php _e( 'Remember Me', TEMPLATE_DOMAIN ) ?>" />

<input type="submit" name="wp-submit" id="wp-submit" value="<?php _e( 'Log In', TEMPLATE_DOMAIN) ?>"/>
<?php if ( bp_get_signup_allowed() ) : ?>
<input type="button" name="signup-submit" id="signup-submit" value="<?php _e( 'Sign Up', TEMPLATE_DOMAIN) ?>" onclick="location.href='<?php echo bp_signup_page() ?>'" />
<?php endif; ?>
<input type="hidden" name="testcookie" value="1" />
<?php do_action( 'bp_login_bar_logged_out' ) ?>
</form>

<?php do_action( 'bp_after_sidebar_login_form' ) ?>

<?php else : ?>

<div id="logout-link">
<?php global $bp; bp_loggedin_user_avatar( 'width=20&height=20' ) ?> &nbsp; <a href="<?php bp_loggedinuser_link() ?>"><?php echo $bp->loggedin_user->fullname; ?></a> / <a href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', TEMPLATE_DOMAIN ) ?></a>
<?php do_action( 'bp_login_bar_logged_in' ) ?>
</div>

<?php endif; ?>

</div>
</div>
<?php } //dont show in reg page ?>

<?php } else { ?>

<div id="searchbox">
<?php locate_template( array( 'lib/templates/wp-template/profile.php'), true ); ?>
</div>

<?php } ?>



<?php do_action( 'bp_after_search_login_bar' ) ?>


<?php if($tn_buddycorp_call_signup_on != ""){ ?>
<?php } else { ?>
<?php locate_template( array( 'lib/templates/wp-template/call-signup.php'), true ); ?>
<?php } ?>


<?php do_action( 'bp_before_container' ) ?>

<?php if($bp_existed == 'true') { //check if bp existed ?>
<div class="content">
<?php if( !bp_is_blog_page() && bp_current_component() && !bp_is_directory() ) { ?>
<?php locate_template( array( 'lib/templates/bp-template/userbar.php'), true ); ?>
<?php } ?>
<?php do_action( 'bp_before_content' ) ?>

<?php } else { ?>
<div class="content">
<?php do_action( 'bp_before_content' ) ?>
<?php } ?>