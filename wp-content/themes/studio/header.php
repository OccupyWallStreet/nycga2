<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
 		<?php include (get_template_directory() . '/library/options/options.php'); ?>
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>		
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_head' ) ?>
		<?php endif; ?>
		<?php font_show(); ?>
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
				<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
				<link rel="icon" href="<?php bloginfo('stylesheet_directory');?>/favicon.ico" type="images/x-icon" />
		<?php wp_head(); ?>
		<?php 
			$slideshow = get_option('dev_studio_slideshow');{
				if ($slideshow == "yes"){
					$slideheight = get_option('dev_studio_slideone_height');
					if ($slideheight == ""){
						$slideheight = 300;
						$pheight = 150;
					}	
					$wrapheight = ($slideheight + 40);
					?>
						<style type="text/css" media="screen">
							#loopedSlider .container { width:960px; height:<?php echo $slideheight; ?>px; overflow:hidden; position:relative; cursor:pointer; }
								#slideshow { height:<?php echo $wrapheight; ?>px;}
						</style>
					<?php
				}
				else{	
				}
			}		
		?>
	</head>
	<body <?php body_class() ?>>
			<div id="header-wrapper"><!-- start #header-wrapper -->
					<div id="header"><!-- start #header -->
							<?php if($bp_existed == 'true') : ?>
								<?php do_action( 'bp_before_header' ) ?>
							<?php endif; ?>
						<?php locate_template( array( '/library/components/branding-header.php' ), true ); ?>
						<?php locate_template( array( '/library/components/navigation.php' ), true ); ?>
						<?php if($bp_existed == 'true') : ?>
							<?php do_action( 'bp_header' ) ?>
						<?php endif; ?>
					</div><!-- end #header -->
			</div>	<!-- end #header-wrapper -->
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_header' ) ?>
			<?php do_action( 'bp_before_container' ) ?>
		<?php endif; ?>
			<?php if($bp_existed == 'true') : ?>
				<?php locate_template( array( '/library/components/panel.php' ), true ); ?>
			<?php endif; ?>
			<?php $customheader_on = get_option('dev_studio_customheader_on');
			if ($customheader_on == "yes"){
			?>
				<?php locate_template( array( '/library/components/customheader.php' ), true ); ?>
			<?php } ?>
			<?php locate_template( array( '/library/components/strapline.php' ), true ); ?>
			<div class="clear"></div>
		<?php if(!is_home()) { ?>
			<div id="site-wrapper"><!-- start #site-wrapper -->
			<?php } else { ?>
			<div id="site-wrapper-home"><!-- start #site-wrapper-home -->
				<?php } ?>
				<div id="container"><!-- start #container -->