<script type="text/javascript">
  jQuery.noConflict();
jQuery(window).load(function() {
    jQuery('#slider-small').nivoSlider({
	effect:'fade'
});
});
</script>
<div id="homepage-content">
	<div id="gallery-catlist">
			<?php 	 	locate_template( array( '/library/components/navigation-gallery.php' ), true );  ?>	
	</div>
	<div id="slideshow-smaller">
	  <div id="slider-wrapper">
	   <div id="slider-small" class="nivoSlider">
		<?php
		global $blog_id;
		
		if ( !empty($wpdb->base_prefix) ) {
		  $db_prefix = $wpdb->base_prefix;
		} else {
		  $db_prefix = $wpdb->prefix;
		}
		
		$slides = $wpdb->get_results("SELECT * FROM {$db_prefix}gallery_slides WHERE slide_blog_id = ".intval($blog_id).";");
		
		$upload_dir = wp_upload_dir();
		$upload_path = $upload_dir['path'];
		$upload_url = $upload_dir['url'];
		
		if ($slides) {
		  foreach ($slides as $slide) { ?>
		  <?php
		    $slide_image = "{$slide->slide_file_name}";
		    $caption = $slide->slide_caption;
		    $link = $slide->slide_link;
		    
		    if (!empty($link)) {
		      ?>
		      <a href="<?php print $link; ?>"><img src="<?php echo $slide_image; ?>" title="<?php echo $caption; ?>"/></a>
		      <?php
		    } else {
		      ?>
		      <img src="<?php echo $slide_image; ?>" title="<?php echo $caption; ?>"/>
		      <?php
		    }
		  } 
		} else {
		?>
			<img src="<?php bloginfo('template_directory'); ?>/library/styles/images/defaultsetup/largephoto.jpg"/>
				<img src="<?php bloginfo('template_directory'); ?>/library/styles/images/defaultsetup/largephoto2.jpg"/>
					<img src="<?php bloginfo('template_directory'); ?>/library/styles/images/defaultsetup/largephoto3.jpg"/>
						<img src="<?php bloginfo('template_directory'); ?>/library/styles/images/defaultsetup/largephoto4.jpg"/>
							<img src="<?php bloginfo('template_directory'); ?>/library/styles/images/defaultsetup/largephoto5.jpg"/>
								<img src="<?php bloginfo('template_directory'); ?>/library/styles/images/defaultsetup/largephoto6.jpg"/>
		<?php
	}
		?>
        </div>
        </div>
	</div>
	<div class="clear"></div>
</div>
