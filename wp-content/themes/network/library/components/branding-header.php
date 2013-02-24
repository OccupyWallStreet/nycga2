<!-- start branding -->
<div id="site-logo"><!-- start #site-logo -->
	<?php 
		$logo_on = get_option('dev_network_header_image');
		$logo_image = get_option('dev_network_header_logo');
		$square_logo = get_option('dev_network_header_image_square');
		$square_image = get_option('dev_network_header_logo_square'); 
		$site_title = get_bloginfo('name');
		$site_title = isset($site_title)?$site_title:get_option('dev_network_site_title');
		$site_slogan = get_bloginfo('description');
		$site_slogan = isset($site_slogan)?$site_slogan:get_option('dev_network_site_slogan');
		$signup_link = get_option('dev_network_site_slogan');
		$signup_featuretext = get_option('dev_network_signupfeat_text');
	?>
<div id="header">
	<div class="logo">
		<?php if($logo_on == "no" && $square_logo == "yes"){
			?>
			<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'network' ) ?>"><img src="<?php echo $square_image; ?>" alt="<?php bloginfo('name'); ?>" class="logo-square"/></a>
			<h1><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'network' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
		<?php
		}
		else if($logo_on == "yes" && $square_logo == "no"){
			?>
			<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'network' ) ?>"><img src="<?php echo $logo_image; ?>" alt="<?php bloginfo('name'); ?>" class="full-logo"/></a>
		<?php
		}
		else{
		?>		
		<?php
				if ($site_title == ""){
					$site_title = "Network";
				}
				?><h1>
		<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'network' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
		<?php
		}
		?>
	</div>
	<div class="description">
		<?php if ($site_slogan) { ?>
		<h4><?php echo stripslashes($site_slogan); ?>  
		</h4>
		<?php } ?>
				<?php if ( (!is_user_logged_in()) && ($signup_featuretext != "")) { ?>
					<h4>	<?php echo stripslashes($signup_featuretext); ?>	</h4>
				<?php } ?>
		<?php if ( !is_user_logged_in() ) { ?>
			<?php signup_button(); ?>
		<?php } ?>
	</div>
	<div class="clear"></div>
</div>
</div>