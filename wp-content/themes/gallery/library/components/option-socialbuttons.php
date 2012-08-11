<!-- Social icons : set in theme options -->
<?php
	$gallery_socialbuttons = get_option('dev_gallery_socialbuttons');
	$gallery_flickr = get_option('dev_gallery_flickr');
	$gallery_delicious = get_option('dev_gallery_delicious');
	$gallery_rss = get_option('dev_gallery_rss');
	$gallery_facebook = get_option('dev_gallery_facebook');
	$gallery_vimeo = get_option('dev_gallery_vimeo');
	$gallery_twitter = get_option('dev_gallery_twitter');
	
	if ($gallery_socialbuttons == "yes"){
		?>
		<div id="social-section">
		<?php
	if (trim($gallery_flickr) == ""){
	}
	else{
		?>
		<a href="<?php echo $gallery_flickr; ?>" title="Flickr"><img src="<?php bloginfo('template_directory');?>/library/styles/images/flickr.png" alt="<?php _e( 'Flickr', 'gallery' ) ?>" /></a>
		<?php
	}
	
	if (trim($gallery_delicious) == ""){
	}
	else{
		?>
		<a href="<?php echo $gallery_delicious; ?>" title="Delicious"><img src="<?php bloginfo('template_directory');?>/library/styles/images/delicious.png" alt="<?php _e( 'Delicious', 'gallery' ) ?>" /></a>
		<?php
	}
	
	if (trim($gallery_rss) == ""){
	}
	else{
		?>
		<a href="<?php echo $gallery_rss; ?>" title="RSS"><img src="<?php bloginfo('template_directory');?>/library/styles/images/rss.png" alt="<?php _e( 'RSS Feed', 'gallery' ) ?>" /></a>
		<?php
	}
	
	if (trim($gallery_facebook) == ""){
	}
	else{
		?>
		<a href="<?php echo $gallery_facebook; ?>" title="Facebook"><img src="<?php bloginfo('template_directory');?>/library/styles/images/facebook.png" alt="<?php _e( 'Facebook', 'gallery' ) ?>" /></a>
		<?php
	}
	
	if (trim($gallery_vimeo) == ""){
	}
	else{
		?>
		<a href="<?php echo $gallery_vimeo; ?>" title="Vimeo"><img src="<?php bloginfo('template_directory');?>/library/styles/images/vimeo.png" alt="<?php _e( 'Vimeo', 'gallery' ) ?>" /></a>
		<?php
	}
	
	if (trim($gallery_twitter) == ""){
	}
	else{
		?>
		<a href="<?php echo $gallery_twitter; ?>" title="Twitter"><img src="<?php bloginfo('template_directory');?>/library/styles/images/twitter.png" alt="<?php _e( 'Twitter', 'gallery' ) ?>" /></a>
		<?php
	}
?>		<div class="clear"></div>
</div>
<?php } ?>