<!-- start branding -->
<div id="site-logo"><!-- start #site-logo -->
	<?php 
		$logo_on = get_option('dev_studio_header_image');
		$logo_image = get_option('dev_studio_header_logo');
		$square_logo = get_option('dev_studio_header_image_square');
		$square_image = get_option('dev_studio_header_logo_square');
		$site_title = get_option('dev_studio_site_title');
	?>
	<?php

	if($logo_on == "no" && $square_logo == "yes"){
		?>
		<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'studio' ) ?>"><img src="<?php echo $square_image; ?>" alt="<?php bloginfo('name'); ?>" class="logo-square"/></a>
		<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'studio' ) ?>"><?php echo stripslashes($site_title); ?></a>
	<?php
	}
	else if($logo_on == "yes" && $square_logo == "no"){
		?>
		<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'studio' ) ?>"><img src="<?php echo $logo_image; ?>" alt="<?php bloginfo('name'); ?>" class="full-logo"/></a>
	<?php
	}
	else{
	?>		
	<?php
			if ($site_title == ""){
				$site_title = get_bloginfo('name');
			}
			?>
			<div class="largespacer"></div>
	<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'studio' ) ?>"><?php echo stripslashes($site_title); ?></a>
	<?php
	}
	?></div><!-- end #site-logo -->
	<!-- end branding -->