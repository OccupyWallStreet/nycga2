<!-- start branding -->
<?php 
		$logo_on = get_option('dev_gallery_logo');
		$logo_image = get_option('dev_gallery_cropped_logo');
		$site_title = trim(get_option('dev_gallery_site_title'));
		
		$upload_dir = wp_upload_dir();
		$upload_url = $upload_dir['url'];
	?>
	<?php
	
	if(($logo_on == "") || ($logo_on == "yes")){
		if ($logo_image != ""){
				?>				
				<div id="branding">
						<a href="<?php echo home_url(); ?>" title="<?php _e( 'Home', 'gallery' ) ?>"><img src="<?php echo "{$logo_image}"; ?>" alt="<?php bloginfo('name'); ?>"/></a>
				</div>	
				<?php
		}
		if ($logo_image == ""){
				?>
					<div id="branding">
							<a href="<?php echo home_url(); ?>" title="<?php _e( 'Home', 'gallery' ) ?>">	<img src="<?php bloginfo('template_directory'); ?>/library/styles/images/defaultsetup/logo.png"/></a>
					</div>
				<?php
		}
	}
	
	if ($logo_on == "no"){
			if ($site_title != ""){
				?>
					<div id="branding">
							<a href="<?php echo home_url(); ?>" title="<?php _e( 'Home', 'gallery' ) ?>">	<?php echo stripslashes($site_title); ?></a>
					</div>
				<?php
			}
			else{
				?>
					<div id="branding">
						<?php _e( 'Gallery', 'gallery' ) ?>
					</div>
				<?php
			}
	}

?>
<!-- end branding -->
