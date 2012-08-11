<div id="top-header">
<div id="top-header-shade">
<div class="content-wrap">
<div class="content-content">
	<?php
		$site_title = get_option('dev_businessfeature_site_title');
		$site_description = get_option('dev_businessfeature_site_description');
		$strapline = get_option('dev_businessfeature_header_strap');
		$logo_on = get_option('dev_businessfeature_header_image_on');
		$logo_image = get_option('dev_businessfeature_header_logo');
		$square_logo = get_option('dev_businessfeature_header_image_square');
		$square_image = get_option('dev_businessfeature_header_logo_square');
	?>	
<div id="site-logo">
	<?php
	if($logo_on == "no" && $square_logo == "yes"){
		?>
		<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-feature' ) ?>"><img src="<?php echo $square_image; ?>" alt="<?php bloginfo('name'); ?>" class="logo-square"/></a>
		<h1 class="square-header"><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-feature' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
	<?php
	}
	else if($logo_on == "yes" && $square_logo == "no"){
		?>
		<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-feature' ) ?>"><img src="<?php echo $logo_image; ?>" alt="<?php bloginfo('name'); ?>" class="full-logo"/></a>
	<?php
	}
	else{
	?>
		<?php
		if ($site_title == ""){
			$site_title = "Business Feature";
		}
		?>
		<h1><a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', 'business-feature' ) ?>"><?php echo stripslashes($site_title); ?></a></h1>
	<?php
	}
	?>
	<?php
	if ($site_description == ""){
		$site_description = "Enter a site description in options";
	}
	?>
	<h2><?php echo stripslashes($site_description); ?></h2>
	</div>
<div id="site-panel">
<div class="top-panel">
	<?php echo stripslashes($strapline); ?>
</div>