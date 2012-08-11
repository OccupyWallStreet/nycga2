<script type="text/javascript">
  jQuery.noConflict();
jQuery(window).load(function() {
    jQuery('#slider').nivoSlider({
	effect:'fade'
});
});
</script>
<div id="slideshow">
	  <div id="slider-wrapper">
	   <div id="slider" class="nivoSlider">
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
		} ?>
        </div>
        </div>
</div>
