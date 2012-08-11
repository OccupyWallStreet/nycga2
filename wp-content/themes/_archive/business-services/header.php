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
		<?php wp_head(); ?>
	</head>
	<body <?php body_class() ?>>
		<div id="top-header">
		<div id="top-header-inner">
		<div id="top-header-content">
		<div class="sitelogo">
			<?php 
				$logo_on = get_option('dev_businessservices_header_image_on');
				$logo_image = get_option('dev_businessservices_header_logo');
				$square_logo = get_option('dev_businessservices_header_image_square');
				$square_image = get_option('dev_businessservices_header_logo_square');
				$site_title = get_option('dev_businessservices_site_title');
			?>
			<?php

			if($logo_on == "no" && $square_logo == "yes"){
				?>
				<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-services' ) ?>"><img src="<?php echo $square_image; ?>" alt="<?php bloginfo('name'); ?>" class="logo-square"/></a>
				<h1 class="square-header"><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-services' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
			<?php
			}
			else if($logo_on == "yes" && $square_logo == "no"){
				?>
				<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-services' ) ?>"><img src="<?php echo $logo_image; ?>" alt="<?php bloginfo('name'); ?>" class="full-logo"/></a>
			<?php
			}
			else{
			?>
			<?php

			if ($site_title == ""){
				$site_title = "Business Services";
			}

			?>
				<h1><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-services' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
			<?php
			}

			?>
			<div class="clear"></div>
		</div>
		</div>
		</div>
		</div>	
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_header' ) ?>
			<?php endif; ?>

				<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
					<?php locate_template( array( '/library/components/buddypress/buddypress-navigation.php' ), true ); ?>
				<?php } else { // if not bp detected..let go normal ?>
					<?php locate_template( array( '/library/components/navigation.php' ), true ); ?>
				<?php } ?>
					<?php locate_template( array( '/library/components/content-home.php' ), true ); ?>
						
						
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_header' ) ?>
				<?php endif; ?>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_header' ) ?>
			<?php do_action( 'bp_before_container' ) ?>
			<?php endif; ?>
		<div id="wrapper">
		<div id="container">
		<div id="head-content">