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
			<?php wp_head(); ?>
	</head>
	<body <?php body_class() ?>>
		<div id="top-wrap">
		<div id="top-container">
		<div id="top-content">
		<div class="sitelogo">
			<?php 
				$logo_on = get_option('dev_businessportfolio_header_image');
				$logo_image = get_option('dev_businessportfolio_header_logo');
				$square_logo = get_option('dev_businessportfolio_header_image_square');
				$square_image = get_option('dev_businessportfolio_header_logo_square');
				$site_title = get_option('dev_businessportfolio_site_title');
			?>
					<?php
						if ($site_title == ""){
							$site_title = "Business Portfolio";
						}
					?>
			<?php
			if($logo_on == "no" && $square_logo == "yes"){
				?>
				<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-portfolio' ) ?>"><img src="<?php echo $square_image; ?>" alt="<?php bloginfo('name'); ?>" class="logo-square"/></a>
				<h1 class="square-header"><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-portfolio' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
			<?php
			}
			else if($logo_on == "yes" && $square_logo == "no"){
				?>
				<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-portfolio' ) ?>"><img src="<?php echo $logo_image; ?>" alt="<?php bloginfo('name'); ?>" class="full-logo"/></a>
			<?php
			}
			else{
			?>
				<h1><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-portfolio' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
			<?php
			}

			?>
		</div>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_header' ) ?>
		<?php endif; ?>
					<?php locate_template( array( '/library/components/navigation.php' ), true ); ?>
			</div>
			</div>
			</div>
			<?php
			if(is_home()) {
				?>
					<?php locate_template( array( '/library/components/home-panel.php' ), true ); ?>
				<?php
			} else {
				?>
				
					<?php locate_template( array( '/library/components/main-panel.php' ), true ); ?>
				<?php
			}
			?>
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_header' ) ?>
			<?php endif; ?>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_header' ) ?>
			<?php do_action( 'bp_before_container' ) ?>
		<?php endif; ?>
		<div id="wrapper">
		<div id="container">
		<div id="home-content">