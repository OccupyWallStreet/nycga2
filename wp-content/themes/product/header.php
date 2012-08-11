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
		<script type="text/javascript">
		    jQuery(document).ready(function() {
			   jQuery.noConflict();

			     // Put all your code in your document ready area
			     jQuery(document).ready(function(){
			       // Do jQuery stuff using $
				 	jQuery(function(){
					 jQuery(".sf-menu").supersubs({ 
					            minWidth:    12,   // minimum width of sub-menus in em units 
					            maxWidth:    27,   // maximum width of sub-menus in em units 
					            extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
					                               // due to slight rounding differences and font-family 
					        }).superfish();  // call supersubs first, then superfish, so that subs are 
					                         // not display:none when measuring. Call before initialising 
					                         // containing tabs for same reason.
					});
			    });
		    });
		</script>
		
		<script type="text/javascript" charset="utf-8">
		   jQuery.noConflict();

		     // Put all your code in your document ready area
			jQuery(window).load(function(){
			jQuery(function(){
				jQuery('#loopedSlider').loopedSlider(
					{
						autoHeight: 500
					}
					);
			});
				    });
		</script>
		<?php
			$slideheight = get_option('dev_product_slideone_height');
			$pheight = ($slideheight/2);
			
			if ($slideheight == ""){
				$slideheight = 300;
				$pheight = 150;
			}
			
		?>
		<style type="text/css" media="screen">
			a.previous { position:absolute; top:<?php echo $pheight; ?>px; left:-22px; }
			a.next { position:absolute; top:<?php echo $pheight; ?>px; right:-24px; }
			#loopedSlider .container { width:940px; height:<?php echo $slideheight; ?>px; overflow:hidden; position:relative; cursor:pointer; }
		</style>
		<!--[if IE 6]>
		<style type="text/css">
				.shadow-spacer{
					background: none;
				}
		</style>
			<![endif]-->
	</head>
	<body <?php body_class() ?>>
		<div id="site-wrapper">
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_header' ) ?>
		<?php endif; ?>
		<div id="header">
				<div id="search-bar">
						<?php
							$sitedescription = get_option('dev_product_description');
							$sitetitle = get_option('dev_product_title');
							
									if ($sitedescription == ""){
										$sitedescription = "Enter a site description in options";
									}

									if ($sitetitle == ""){
										$sitetitle = "Product";
									}
						?>
					<h2><?php echo stripslashes($sitedescription); ?></h2>
					<?php locate_template( array( '/library/components/searchform.php' ), true ); ?>		
					<?php if($bp_existed == 'true') : ?>
						<?php //do_action( 'bp_search_login_bar' ) ?>
					<?php endif; ?>
				</div><!-- #search-bar -->
				<div id="logo">
				<?php 
					$logo_on = get_option('dev_product_header_image');
					$logo_image = get_option('dev_product_header_logo');
					$description_on = get_option('dev_product_header_description_on');
					$square_logo = get_option('dev_product_header_image_square');
					$square_image = get_option('dev_product_header_logo_square');
				?>
				<?php

				if($logo_on == "no" && $square_logo == "yes"){
					?>
					<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'product' ) ?>"><img src="<?php echo $square_image; ?>" alt="<?php bloginfo('name'); ?>" class="logo-square"/></a>
					<h1 class="square-header"><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'product' ) ?>"><?php echo stripslashes($sitetitle); ?></a></h1>
				<?php
				}
				else if($logo_on == "yes" && $square_logo == "no"){
					?>
					<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'product' ) ?>"><img src="<?php echo $logo_image; ?>" alt="<?php bloginfo('name'); ?>" class="full-logo"/></a>
				<?php
				}
				else{
				?>
					<h1><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'product' ) ?>"><?php echo stripslashes($sitetitle); ?></a></h1>
				<?php
				}

				?>
				</div>
			<div class="clear"></div>
		</div><!-- #header -->
				<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
					<?php locate_template( array( '/library/components/buddypress/buddypress-navigation.php' ), true ); ?>
				<?php } else { // if not bp detected..let go normal ?>
					<?php locate_template( array( '/library/components/navigation.php' ), true ); ?>
				<?php } ?>
				<?php
				
					locate_template( array( '/library/components/signup-box.php' ), true );
				
				?>
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_header' ) ?>
		<?php endif; ?>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_header' ) ?>
			<?php do_action( 'bp_before_container' ) ?>
		<?php endif; ?>
		<div id="container">