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
		<div id="site-wrapper">
			<div id="site-container">
				<?php
		$sitetitle = get_option('dev_businessblog_site_title');
		$rsstitle = get_option('dev_businessblog_rss_title');
				?>
					<?php
							if ($rsstitle == ""){
								$rsstitle = "Configure under theme options";
							}
							?>
				<div id="subscribes_rss"><a href="<?php bloginfo('rss2_url'); ?>"><?php echo stripslashes($rsstitle); ?></a></div>
				<div id="top-content">
					
				<div id="site-logo">
					<?php 
					$site_title = get_option('dev_businessblog_site_title');
						$logo_on = get_option('dev_businessblog_header_image');
						$logo_image = get_option('dev_businessblog_header_logo');
						$square_logo = get_option('dev_businessblog_header_image_square');
						$square_image = get_option('dev_businessblog_header_logo_square');
					?>
					<?php

					if($logo_on == "no" && $square_logo == "yes"){
						?>
						<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-blog' ) ?>"><img src="<?php echo $square_image; ?>" alt="<?php bloginfo('name'); ?>" class="logo-square"/></a>
						<h1 class="square-header"><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-blog' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
					<?php
					}
					else if($logo_on == "yes" && $square_logo == "no"){
						?>
						<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-blog' ) ?>"><img src="<?php echo $logo_image; ?>" alt="<?php bloginfo('name'); ?>" class="full-logo"/></a>
					<?php
					}
					else{
					?>		
					<?php
							if ($site_title == ""){
								$site_title = "Business Blog";
							}
							?>
						<h1><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-blog' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
					<?php
					}

					?></div>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_header' ) ?>
		<?php endif; ?>

					<?php locate_template( array( '/library/components/navigation.php' ), true ); ?>
		</div>
		<?php if(is_home()) { ?>
				<?php locate_template( array( '/library/components/featured-header.php' ), true ); ?>
		<?php } ?>
			<?php if( $bp_existed == 'true' ) : //check if bp existed ?>
				<?php locate_template( array( '/library/components/buddypress/buddypress-navigation.php' ), true ); ?>
			<?php endif // if not bp detected..let go normal ?>
		<div class="box">
		<div class="topbox">
		<div class="contentbox">
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_header' ) ?>
			<?php endif; ?>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_header' ) ?>
			<?php do_action( 'bp_before_container' ) ?>
		<?php endif; ?>
		<div id="container">