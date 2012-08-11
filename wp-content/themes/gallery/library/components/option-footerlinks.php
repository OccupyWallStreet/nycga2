<!-- Footer Links : set in theme options -->
<?php
	$gallery_footerlinks = get_option('dev_gallery_footerlinks');
	if (trim($gallery_footerlinks) == ""){
		?>
		<div id="footer-links">
		<a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'WordPress Plugins, Themes and Support', 'gallery' )?>" ><?php _e( 'WPMU DEV - The WordPress Experts', 'gallery' ) ?></a><a href="<?php echo home_url(); ?>"><?php _e( 'Copyright', 'gallery' ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php bloginfo('name'); ?></a><a href="#site-wrapper"><?php _e('Go back to top &uarr;', 'gallery'); ?></a>
		</div>
		<?php
	}
	else{
		?>
		<div id="footer-links">
		<?php
		echo stripslashes($gallery_footerlinks);
		?>
		</div>
		<?php
	}
?>