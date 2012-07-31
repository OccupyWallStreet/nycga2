<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?> | <?php bloginfo('description'); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" title="default" media="screen" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/mobi.css" type="text/css" media="handheld" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/mobi2.css" type="text/css" media="only screen and (max-width: 1020px), only screen and (max-device-width: 1020px)" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/mobi.css" type="text/css" media="only screen and (max-width: 480px), only screen and (max-device-width: 480px)" />
<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT:bold' rel='stylesheet' type='text/css' />

<?php $aOptions = Detox::initOptions(false); ?>
<style type="text/css">
/* [[CSS]]  */
body{background:transparent url(<?php echo($aOptions['featured1111-image']); ?>) repeat 0 0 !important;}
.hds{background:transparent url(<?php echo($aOptions['featured11-image']); ?>) no-repeat 0 0px !important;float:left;display:inline-block;width:auto;height:88px;}
.credits{background:#fff url(<?php echo($aOptions['featured11-image']); ?>) 50% 100% no-repeat !important;}
</style>

<link rel="shortcut icon" type="image/ico" href="<?php echo($aOptions['featured111-image']); ?>" />
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" />	

<!--[if lt IE 9]><script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js" type="text/javascript"></script><![endif]-->

<?php
			wp_enqueue_script('jquery');
            if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
		?>
<?php wp_head(); ?>
<script src="<?php bloginfo('template_url'); ?>/js/contentslider.js" type="text/javascript"></script>
</head>

<body <?php body_class() ?> id="detox_theme_by_milo317">
<div class="wrap">

<div id="header">

<div id="head">
<div class="hd"><a title="<?php _e( 'Get back to the frontpage', 'Detox') ?>" href="<?php echo get_settings('home'); ?>/"><img src="<?php echo($aOptions['featured11-image']); ?>" title="<?php bloginfo('name'); ?>" /></a></div>
<h1><a title="<?php _e( 'Get back to the frontpage', 'Detox') ?>" href="<?php echo get_settings('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
<div class="des"><?php bloginfo('description'); ?></div>
</div>

<div id="tags">
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('ad-column1') ) : ?>
<?php endif; ?>
</div>
</div>

<div id="navi">
<div id="pnavi">
<?php
if(function_exists('wp_nav_menu')) {
wp_nav_menu(array(
'theme_location' => 'topnav',
'container' => '',
'container_id' => 'top',
'menu_id' => 'pnav',
'fallback_cb' => 'topnav_fallback',
));
} else {
?>
<?php
}
?>
</div>
</div>

<div id="catnav">

<?php
if(function_exists('wp_nav_menu')) {
wp_nav_menu(array(
'theme_location' => 'secnav',
'container' => '',
'container_id' => 'secnav',
'menu_id' => 'catnavi',
'fallback_cb' => 'secnav_fallback',
));
} else {
?>
<?php
}
?>

</div>

<div id="heads">

<div class="hads">
<?php if ( !function_exists('dynamic_sidebar')
	        || !dynamic_sidebar('twitter') ) : ?>
<?php twitter_messages('milo317', 1, false, true, '&#187;', true, false, true); ?>
<?php endif; ?>
</div>

<div class="twt">
<?php $urltweet = get_option('Detox_urltweet'); ?>
<a href="<?php echo ($urltweet); ?>" rel="bookmark" title="<?php _e( 'Follow us on Twitter', 'Detox') ?>"><?php _e( 'Follow us on Twitter', 'Detox') ?></a>
</div>

<?php get_template_part('searchform'); ?>

<div id="feed">
<?php 
$url25 = get_option('Detox_url25'); 
?>
<a href="<?php echo ($url25); ?>" class="rss">RSS</a>
</div>

</div>