<?php 
	$video_code = get_option('dev_buddydaily_video_code');
	$video_title = get_option('dev_buddydaily_video_title');
	$video_description = get_option('dev_buddydaily_video_description');
?>
<div id="video-holder">
	<?php echo stripslashes($video_code); ?>
</div>
	<div class="dark-container">
		<h4><?php echo stripslashes($video_title); ?></h4>
		<p>
			<?php echo stripslashes($video_description); ?>
		</p>
	</div>