<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
 		<?php include (get_template_directory() . '/library/options/options.php'); ?>
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>		
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_head' ) ?>
		<?php endif; ?>
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		<link rel="icon" href="<?php bloginfo('stylesheet_directory');?>/favicon.ico" type="images/x-icon" />
			<!-- font_show start -->
			<?php font_show(); ?>
			<!-- font_show end -->
			<?php wp_head(); ?>
	</head>
	<body <?php body_class() ?>>
		<div id="top-navigation-bar">
			<?php locate_template( array( '/library/components/navigation-header.php' ), true ); ?>
				<div class="clear"></div>
		</div>
				<div id="site-wrapper">		
		<div id="headerContainer">
			<?php locate_template( array( '/library/components/branding-header.php' ), true ); ?>
			<?php if ( get_header_image() ) : ?>				
				<div id="top-header-graphic">
					<img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="" />
				</div>
			<?php endif; ?>
			<?php locate_template( array( '/library/components/discover-header.php' ), true ); ?>
		</div>
		<div id="container"><!-- start #container -->
		
		
		

