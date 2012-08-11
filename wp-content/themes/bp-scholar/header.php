<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">

		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
   <?php include (get_template_directory() . '/options.php'); ?>
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
						 jQuery("ul.sf-menu").supersubs({ 
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
	</head>

	<body <?php body_class() ?>>
			<?php do_action( 'bp_before_header' ) ?>
			<div id="login-wrapper">
				<div id="login-bar">
						<div id="search-box">
			<?php 
			locate_template( array( '/includes/searchform.php' ), true ); ?>
				</div>
					<?php if($bp_existed == 'true') { ?>
					<div id="login-box">
			<?php 
			locate_template( array( '/includes/members-box.php' ), true );
			 ?>	
					</div>
						<?php do_action( 'bp_search_login_bar' ) ?>
					<?php } ?>
				<div class="clear"></div>
			</div>
			</div>
				<?php if($bp_existed == 'true') { ?>
			<?php do_action( 'bp_after_search_login_bar' ) ?>
				<?php } ?>
			
		<div id="header-wrapper">
		<div id="header">
				<?php 
					$advert = get_option('ne_buddyscholar_header_advert');		
					$advert_title = get_option('ne_buddyscholar_header_advert_title');
					$advert_link = get_option('ne_buddyscholar_header_advert_link');
				?>
				<?php
				if ($advert != "" && $advert_link == ""){
				?>
				<div id="advert">
					<img src="<?php echo $advert; ?>" alt="<?php bloginfo('name'); ?>"/>
				</div>
				<?php
				}
				else if ($advert != "" && $advert_link != ""){
					?>
						<div id="advert">
								<a href="<?php echo $advert_link ?>" title="<?php echo stripslashes($advert_title); ?>"><img src="<?php echo $advert; ?>" alt="<?php echo stripslashes($advert_title); ?>"></a>
						</div>	
					<?php
				}
				else {
					
				}
				?>
			<div id="logo">
			<?php 
				$logo_on = get_option('ne_buddyscholar_header_image');
				$logo_image = get_option('ne_buddyscholar_header_logo');
				$description_on = get_option('ne_buddyscholar_header_description_on');
				$description = get_option('ne_buddyscholar_header_description');
				$square_logo = get_option('ne_buddyscholar_header_image_square');
				$square_image = get_option('ne_buddyscholar_header_logo_square');
				$site_title = get_option('ne_buddyscholar_header_title');
			?>
			<?php

			if($logo_on == "no" && $square_logo == "yes"){
				?>
				<a href="<?php echo home_url(); ?>" title="<?php _e( 'Home', 'bp-scholar' ) ?>"><img src="<?php echo $square_image; ?>" alt="<?php bloginfo('name'); ?>" class="logo-square"/></a>
				<h1 class="square-header"><a href="<?php echo home_url(); ?>" title="<?php _e( 'Home', 'bp-scholar' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
			<?php
			}
			else if($logo_on == "yes" && $square_logo == "no"){
				?>
				<a href="<?php echo home_url(); ?>" title="<?php _e( 'Home', 'bp-scholar' ) ?>"><img src="<?php echo $logo_image; ?>" alt="<?php bloginfo('name'); ?>" class="full-logo"/></a>
			<?php
			}
			else{
			?>
				<h1><a href="<?php echo home_url(); ?>" title="<?php _e( 'Home', 'bp-scholar' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
			<?php
			}

			?>
			<?php

			if($description_on == "yes"){
				?>
		<div class="description"><?php echo stripslashes($description); ?></div>
			<?php
			}
			else{
			?>

			<?php
			}

			?>
			</div>
				<?php do_action( 'bp_header' ) ?>
				<div class="clear"></div>
			</div><!-- #header -->
		</div>
		<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
		<?php load_template (get_template_directory() . '/includes/main-navigation.php'); ?>
		<?php } else { // if not bp detected..let go normal ?>
			<div id="navigation-wrapper">
				<div id="navigation-bar">
					<div id="navcontainer">
					<ul class="sf-menu">
						<li<?php if ( is_front_page()) : ?> class="selected"<?php endif; ?>>
						<a href="<?php echo site_url() ?>" title="<?php _e( 'Home', 'bp-scholar' ) ?>"><?php _e( 'Home', 'bp-scholar' ) ?></a>
						</li>
							<?php wp_list_pages('title_li='); ?>
							<li><a href="#"><?php _e( 'Categories', 'bp-scholar' ) ?></a>
											<ul>
														<?php 
														wp_list_categories('orderby=id&show_count=0&title_li=');
														?>
											</ul>
							</li>
					</ul>
						</div>		<div class="clear"></div>
					</div>
				</div>
		<?php } ?>
	
			<div id="info-wrapper">
				<div id="info-bar">
						<?php 
							$site_message_show = get_option('ne_buddyscholar_site_message_on');
							$site_message = get_option('ne_buddyscholar_site_message');		
						?>
					<?php if ($site_message_show == "yes"){?>
					<h2><?php echo stripslashes($site_message); ?></h2>
					<?php }?>
					<div class="clear"></div>
				</div>
			</div>
		<?php if($bp_existed == 'true') { ?>
		<?php do_action( 'bp_after_header' ) ?>
		<?php do_action( 'bp_before_container' ) ?>
		<?php } ?>
<div id="container-wrapper">
		<div id="container">