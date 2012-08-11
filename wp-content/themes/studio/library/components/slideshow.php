<?php
	$slideshow_speed = get_option('dev_studio_slideshow_speed');
	if ($slideshow_speed == ""){
		$slideshow_speed = "3000";
	}
?>
<script type="text/javascript" charset="utf-8">
   jQuery.noConflict();
	jQuery(window).load(function(){
	jQuery(function(){
		jQuery('#loopedSlider').loopedSlider(
			{
				autoHeight: 500,
				autoStart: <?php print $slideshow_speed; ?>,
				restart: 5000,
				addPagination: true
			}
			);
	});
		    });
</script>
<!-- start image only slideshow -->
<?php
	$slidenumber = get_option('dev_studio_slide_number');
	$slideone_image = get_option('dev_studio_slideone_image');
	$slidetwo_image = get_option('dev_studio_slidetwo_image');
	$slidethree_image = get_option('dev_studio_slidethree_image');
	$slidefour_image = get_option('dev_studio_slidefour_image');
	$slidefive_image = get_option('dev_studio_slidefive_image');
	$slidesix_image = get_option('dev_studio_slidesix_image');
	
		$slideone_imagelink = get_option('dev_studio_slideone_image_link');
		$slidetwo_imagelink = get_option('dev_studio_slidetwo_image_link');
		$slidethree_imagelink = get_option('dev_studio_slidethree_image_link');
		$slidefour_imagelink = get_option('dev_studio_slidefour_image_link');
		$slidefive_imagelink = get_option('dev_studio_slidefive_image_link');
		$slidesix_imagelink = get_option('dev_studio_slidesix_image_link');
	
	$slideone_image_title = get_option('dev_studio_slideone_image_title');
	$slidetwo_image_title = get_option('dev_studio_slidetwo_image_title');
	$slidethree_image_title = get_option('dev_studio_slidethree_image_title');
	$slidefour_image_title = get_option('dev_studio_slidefour_image_title');
	$slidefive_image_title = get_option('dev_studio_slidefive_image_title');
	$slidesix_image_title = get_option('dev_studio_slidesix_image_title');
	
?>
<div id="slideshow"><!-- start #slideshow -->
	<div id="loopedSlider">	<!-- start #loopedSlider -->
		<div class="container"><!-- start slides loop -->
			<div class="slides">
				<?php
				if (($slidenumber == "1") || ($slidenumber == "2") || ($slidenumber == "3") || ($slidenumber == "4") || ($slidenumber == "5") || ($slidenumber == "6")){
										?>
											<div class="slide">
													<div class="slide-video">
														<a href="<?php echo $slideone_imagelink; ?>">
												<img src="<?php echo $slideone_image; ?>" alt="<?php echo $slideone_image_title; ?>" /></a>
												</div>
											</div>
										<?php
									}
					if (($slidenumber == "2") || ($slidenumber == "3") || ($slidenumber == "4") || ($slidenumber == "5") || ($slidenumber == "6")){
											?>
												<div class="slide">
														<div class="slide-video">
																		<a href="<?php echo $slidetwo_imagelink; ?>">
													<img src="<?php echo $slidetwo_image; ?>" alt="<?php echo $slidetwo_image_title; ?>" /></a>
													</div>
												</div>
											<?php
										}
						if (($slidenumber == "3") || ($slidenumber == "4") || ($slidenumber == "5") || ($slidenumber == "6")){
												?>
													<div class="slide">
															<div class="slide-video">
																				<a href="<?php echo $slidethree_imagelink; ?>">
														<img src="<?php echo $slidethree_image; ?>" alt="<?php echo $slidethree_image_title; ?>" /></a>
														</div>
													</div>
												<?php
										}
							if (($slidenumber == "4") || ($slidenumber == "5") || ($slidenumber == "6")){
?>
														<div class="slide">
																<div class="slide-video">
																				<a href="<?php echo $slidefour_imagelink; ?>">	<img src="<?php echo $slidefour_image; ?>" alt="<?php echo $slidefour_image_title; ?>" /></a>
															</div>
														</div>
													<?php
											}
												if (($slidenumber == "5") || ($slidenumber == "6")){
					?>
																			<div class="slide">
																					<div class="slide-video">
																									<a href="<?php echo $slidefive_imagelink; ?>">	<img src="<?php echo $slidefive_image; ?>" alt="<?php echo $slidefive_image_title; ?>" /></a>
																				</div>
																			</div>
																		<?php
																}
																	if ($slidenumber == "6"){
										?>
																								<div class="slide">
																										<div class="slide-video">
																														<a href="<?php echo $slidesix_imagelink; ?>">	<img src="<?php echo $slidesix_image; ?>" alt="<?php echo $slidesix_image_title; ?>" /></a>
																									</div>
																								</div>
																							<?php
																					}
?>
			</div>
		</div><!-- ends slides loop -->
	<div class="clear"></div>
</div><!-- end #loopedSlider -->
</div><!-- end #slideshow -->
<div class="clear"></div>
<div class="largespacer"></div>
<!-- end image only slideshow -->