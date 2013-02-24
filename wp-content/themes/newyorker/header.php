<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" />	

<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?> | <?php bloginfo('description'); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" title="default" media="screen" />
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/mobi.css" type="text/css" media="handheld" />
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/tab.css" type="text/css" media="only screen and (max-width: 1024px), only screen and (max-device-width: 1024px)" />
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/mobi.css" type="text/css" media="only screen and (max-width: 780px), only screen and (max-device-width: 780px)" />
<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT:bold' rel='stylesheet' type='text/css' />
<link rel="shortcut icon" type="image/ico" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico" />

<?php wp_head(); ?>
<?php do_action( 'bp_head' ); ?>
<?php if ( is_singular() ) wp_enqueue_script( "comment-reply" ); ?>
<script src="<?php echo get_template_directory_uri(); ?>/js/contentslider.js" type="text/javascript"></script>
</head>

<body <?php body_class() ?> id="newyorker_theme_by_milo317">
<div class="wrap">

<div id="header">
<div id="head">
<h1><a title="<?php _e( 'Get back to the frontpage', 'Detox') ?>" href="<?php home_url(); ?>/"><?php bloginfo('name'); ?></a></h1>
</div>

<div id="des">
<p>
<?php bloginfo('description'); ?>
</p>
</div>

<div id="tags">
<p>
<?php echo strftime("%a"); ?>,&nbsp;
<?php echo strftime("%b"); ?>&nbsp;
<?php echo strftime("%d"); ?>,&nbsp;
<?php echo strftime("%y"); ?>&nbsp;&nbsp;
<?php echo strftime("%T"); ?>
</p>
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
<?php get_template_part('searchform'); ?>
</div>