<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?php echo up_title(); ?></title>
<?php
  //meta description 
  echo up_description();
  // meta keywords
  echo up_keywords();
  // meta robots
  echo up_robots();
?>
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>">
  <?php custom_theme_css(); ?>   
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
  
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js"></script>
  <script>window.jQuery || document.write("<script src='<?php bloginfo('template_url'); ?>/js/libs/jquery-1.5.1.min.js'>\x3C/script>")</script>
  <script src="<?php bloginfo('template_url'); ?>/js/libs/modernizr-1.7.min.js"></script>
  <script src="<?php bloginfo('template_url'); ?>/js/easing.js" type="text/javascript"></script>
  <script src="<?php bloginfo('template_url'); ?>/js/jquery.ui.totop.js" type="text/javascript"></script>
  <script type="text/javascript">
		$(document).ready(function() {
			/*
			var defaults = {
	  			containerID: 'moccaUItoTop', // fading element id
				containerHoverClass: 'moccaUIhover', // fading element hover class
				scrollSpeed: 1200,
				easingType: 'linear' 
	 		};
			*/
			
			$().UItoTop({ easingType: 'easeOutQuart' });
			
		});
  </script> 
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="container">
<header id="top">
	<ul id="social">
	<?php social_media_icons(); ?>
	</ul>
	<?php get_feedburner_count(); ?>
	<p>	
	<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
	<?php custom_logo(); ?>
	</a></p>
</header>

<nav>
<div id="access" role="navigation">
		<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
</div><!-- #access -->

<form id="navsearchform" role="search" method="get" action="<?php echo home_url( '/' ); ?>">
	<input type="text" value="Search Here" onFocus="if (this.value == 'Search Here') {this.value = '';}" onBlur="if (this.value == '') {this.value = 'Search Here';}" name="s" id="navs" />
	<input type="submit" id="navsearchsubmit" value="" />
</form>
</nav>



<div id="main" role="main" class="clearfix">