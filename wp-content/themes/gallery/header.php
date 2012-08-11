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
				<?php font_show(); ?>
				<?php wp_head(); ?>
	</head>
	<body <?php body_class() ?>>
		<div id="site-wrapper"><!-- start #site-wrapper -->
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_before_header' ) ?>
			<?php endif; ?>
				<div id="header">
					<?php $customheader_on = get_option('dev_gallery_customheader_on');
					if ($customheader_on == "yes"){
					?>
						<?php locate_template( array( '/library/components/customheader.php' ), true ); ?>
					<?php } ?>
					<?php locate_template( array( '/library/components/branding-header.php' ), true ); ?>
					<?php locate_template( array( '/library/components/navigation.php' ), true ); ?>
					<?php if($bp_existed == 'true') : ?>
					<?php locate_template( array( '/library/components/buddypress/buddypress-navigation.php' ), true ); ?>
					<?php endif; ?>
				<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_header' ) ?>
				<?php endif; ?>
				<div class="clear"></div>
				</div>
								<?php if($bp_existed == 'true') : ?>
									<?php do_action( 'bp_after_header' ) ?>
									<?php do_action( 'bp_before_container' ) ?>
								<?php endif; ?>
									<div id="container"><!-- start #container -->
										<?php if($bp_existed == 'true') : ?>
										<?php if ( function_exists( 'bp_message_get_notices' ) ) : ?>
									<?php bp_message_get_notices(); /* Site wide notices to all users */ ?>
										<?php endif; ?>
										<?php endif; ?>