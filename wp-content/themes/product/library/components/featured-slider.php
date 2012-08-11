<?php 
	
		$slidenumber = get_option('dev_product_slide_number');
		
		$slideone_type = get_option('dev_product_slideone_type');
		$slidetwo_type = get_option('dev_product_slidetwo_type');
		$slidethree_type = get_option('dev_product_slidethree_type');
		$slidefour_type = get_option('dev_product_slidefour_type');
		
		$slideone_title = get_option('dev_product_slideone_title');
		$slidetwo_title = get_option('dev_product_slidetwo_title');
		$slidethree_title = get_option('dev_product_slidethree_title');
		$slidefour_title = get_option('dev_product_slidefour_title');
		
		$slideone_image = get_option('dev_product_slideone_image');
		$slidetwo_image = get_option('dev_product_slidetwo_image');
		$slidethree_image = get_option('dev_product_slidethree_image');
		$slidefour_image = get_option('dev_product_slidefour_image');
		
			$slideone_imagelink = get_option('dev_product_slideone_image_link');
			$slidetwo_imagelink = get_option('dev_product_slidetwo_image_link');
			$slidethree_imagelink = get_option('dev_product_slidethree_image_link');
			$slidefour_imagelink = get_option('dev_product_slidefour_image_link');
		
		$slideone_image_title = get_option('dev_product_slideone_image_title');
		$slidetwo_image_title = get_option('dev_product_slidetwo_image_title');
		$slidethree_image_title = get_option('dev_product_slidethree_image_title');
		$slidefour_image_title = get_option('dev_product_slidefour_image_title');
		
		$slideone_smallvideo = get_option('dev_product_slideone_small_video');
		$slidetwo_smallvideo = get_option('dev_product_slidetwo_small_video');
		$slidethree_smallvideo = get_option('dev_product_slidethree_small_video');
		$slidefour_smallvideo = get_option('dev_product_slidefour_small_video');
		
		$slideone_header = get_option('dev_product_slideone_header');
		$slidetwo_header = get_option('dev_product_slidetwo_header');
		$slidethree_header = get_option('dev_product_slidethree_header');
		$slidefour_header = get_option('dev_product_slidefour_header');
		
		$slideone_description = get_option('dev_product_slideone_description');
		$slidetwo_description = get_option('dev_product_slidetwo_description');
		$slidethree_description = get_option('dev_product_slidethree_description');
		$slidefour_description = get_option('dev_product_slidefour_description');
		
		$slideone_link = get_option('dev_product_slideone_link');
		$slidetwo_link = get_option('dev_product_slidetwo_link');
		$slidethree_link = get_option('dev_product_slidethree_link');
		$slidefour_link = get_option('dev_product_slidefour_link');
		
		$slideone_link_title = get_option('dev_product_slideone_link_title');
		$slidetwo_link_title = get_option('dev_product_slidetwo_link_title');
		$slidethree_link_title = get_option('dev_product_slidethree_link_title');
		$slidefour_link_title = get_option('dev_product_slidefour_link_title');
		
?>

<div id="loopedSlider">	
	<div class="container">
		<div class="slides">
			<?php
			if (($slidenumber == "1") || ($slidenumber == "2") || ($slidenumber == "3") || ($slidenumber == "4")){
				if ($slideone_type == "video and text"){
					?>
						<div class="slide">
							<div class="alignright">
					<?php echo stripslashes($slideone_smallvideo); ?>
							</div>
						<h2>
						<?php echo stripslashes($slideone_header); ?></h2>	
						<p>
					<?php echo stripslashes($slideone_description); ?>
							</p>
								<?php
								if ($slideone_link != ""){

								?>
								<a href="<?php echo $slideone_link; ?>" rel="bookmark" title="<?php echo $slideone_link_title; ?>" class="button">	<?php echo stripslashes($slideone_link_title); ?></a>
								
								<?php } 
								?>
						</div>
					<?php

				}
					else if ($slideone_type == "image and text"){
								?>
									<div class="slide">
										<div class="alignright">
											<a href="<?php echo $slideone_imagelink; ?>">
								<img src="<?php echo $slideone_image; ?>" alt="<?php echo $slideone_image_title; ?>"/></a>
										</div>
									<h2>
									<?php echo stripslashes($slideone_header); ?></h2>	
									<p>
								<?php echo stripslashes($slideone_description); ?>
										</p>
											<?php
											if ($slideone_link != ""){

											?>
											<a href="<?php echo $slideone_link; ?>" rel="bookmark" title="<?php echo $slideone_link_title; ?>" class="button"><?php echo stripslashes($slideone_link_title); ?></a>
											
											<?php } 
											?>
									</div>
								<?php
						}
							else if ($slideone_type == "image"){
									?>
										<div class="slide">
												<div class="slide-video">
													<a href="<?php echo $slideone_imagelink; ?>">
											<img src="<?php echo $slideone_image; ?>" alt="<?php echo $slideone_image_title; ?>" /></a>
											</div>
										</div>
									<?php
							}
									else if ($slideone_type == "video"){
											?>	
												<div class="slide">
													<div class="slide-video">
									<?php echo stripslashes($slideone_smallvideo); ?>
									</div>
									</div>
											<?php
									}
								else if ($slideone_type == "text"){?>
										<div class="slide">
												<h2>
												<?php echo stripslashes($slideone_header); ?></h2>	
												<p>
											<?php echo stripslashes($slideone_description); ?>
													</p>
													<?php
													if ($slideone_link != ""){

													?>
														<a href="<?php echo $slideone_link; ?>" rel="bookmark" title="<?php echo $slideone_link_title; ?>" class="button"><?php echo stripslashes($slideone_link_title); ?></a>
														<?php } 
														?>
										</div>
										<?php
								}		
									else {

									}	
			}
				?>
				<?php
				if (($slidenumber == "2") || ($slidenumber == "3") || ($slidenumber == "4")){
					if ($slidetwo_type == "video and text"){
						?>
							<div class="slide">
								<div class="alignright">
						<?php echo stripslashes($slidetwo_smallvideo); ?>
								</div>
							<h2>
							<?php echo stripslashes($slidetwo_header); ?></h2>	
							<p>
						<?php echo stripslashes($slidetwo_description); ?>
								</p>
									<?php
									if ($slidetwo_link != ""){

									?>
									<a href="<?php echo $slidetwo_link; ?>" rel="bookmark" title="<?php echo $slidetwo_link_title; ?>" class="button">	<?php echo stripslashes($slidetwo_link_title); ?></a>

									<?php } 
									?>
							</div>
						<?php

					}
						else if ($slidetwo_type == "image and text"){
									?>
										<div class="slide">
											<div class="alignright">
												<a href="<?php echo $slidetwo_imagelink; ?>">
									<img src="<?php echo $slidetwo_image; ?>" alt="<?php echo $slidetwo_image_title; ?>"/></a>
											</div>
										<h2>
										<?php echo stripslashes($slidetwo_header); ?></h2>	
										<p>
									<?php echo stripslashes($slidetwo_description); ?>
											</p>
												<?php
												if ($slidetwo_link != ""){

												?>
												<a href="<?php echo $slidetwo_link; ?>" rel="bookmark" title="<?php echo $slidetwo_link_title; ?>" class="button">	<?php echo stripslashes($slidetwo_link_title); ?></a>

												<?php } 
												?>
										</div>
									<?php
							}
								else if ($slidetwo_type == "image"){
										?>
											<div class="slide">
													<div class="slide-video">
																	<a href="<?php echo $slidetwo_imagelink; ?>">
												<img src="<?php echo $slidetwo_image; ?>" alt="<?php echo $slidetwo_image_title; ?>" /></a>
												</div>
											</div>
										<?php
								}
										else if ($slidetwo_type == "video"){
												?>	
													<div class="slide">
														<div class="slide-video">
										<?php echo stripslashes($slidetwo_smallvideo); ?>
										</div>
										</div>
												<?php
										}
									else if ($slidetwo_type == "text"){?>
											<div class="slide">
													<h2>
													<?php echo stripslashes($slidetwo_header); ?></h2>	
													<p>
												<?php echo stripslashes($slidetwo_description); ?>
														</p>
														<?php
														if ($slidetwo_link != ""){
														?>
															<a href="<?php echo $slidetwo_link; ?>" rel="bookmark" title="<?php echo $slidetwo_link_title; ?>" class="button"><?php echo stripslashes($slidetwo_link_title); ?></a>
															<?php } 
															?>
											</div>
											<?php
									}		
										else {

										}	
				}
					?>
					<?php
					if (($slidenumber == "3") || ($slidenumber == "4")){
						if ($slidethree_type == "video and text"){
							?>
								<div class="slide">
									<div class="alignright">
							<?php echo stripslashes($slidethree_smallvideo); ?>
									</div>
								<h2>
								<?php echo stripslashes($slidethree_header); ?></h2>	
								<p>
							<?php echo stripslashes($slidethree_description); ?>
									</p>
										<?php
										if ($slidethree_link != ""){

										?>
										<a href="<?php echo $slidethree_link; ?>" rel="bookmark" title="<?php echo $slidethree_link_title; ?>" class="button"><?php echo stripslashes($slidethree_link_title); ?></a>

										<?php } 
										?>
								</div>
							<?php

						}
							else if ($slidethree_type == "image and text"){
										?>
											<div class="slide">
												<div class="alignright">
																<a href="<?php echo $slidethree_imagelink; ?>">
										<img src="<?php echo $slidethree_image; ?>" alt="<?php echo $slidethree_image_title; ?>"/></a>
												</div>
											<h2>
											<?php echo stripslashes($slidethree_header); ?></h2>	
											<p>
										<?php echo stripslashes($slidethree_description); ?>
												</p>
													<?php
													if ($slidethree_link != ""){

													?>
													<a href="<?php echo $slidethree_link; ?>" rel="bookmark" title="<?php echo $slidethree_link_title; ?>" class="button"><?php echo stripslashes($slidethree_link_title); ?></a>

													<?php } 
													?>
											</div>
										<?php
								}
									else if ($slidethree_type == "image"){
											?>
												<div class="slide">
														<div class="slide-video">
																			<a href="<?php echo $slidethree_imagelink; ?>">
													<img src="<?php echo $slidethree_image; ?>" alt="<?php echo $slidethree_image_title; ?>" /></a>
													</div>
												</div>
											<?php
									}
											else if ($slidethree_type == "video"){
													?>	
														<div class="slide">
															<div class="slide-video">
											<?php echo stripslashes($slidethree_smallvideo); ?>
											</div>
											</div>
													<?php
											}
										else if ($slidethree_type == "text"){?>
												<div class="slide">
														<h2>
														<?php echo stripslashes($slidethree_header); ?></h2>	
														<p>
													<?php echo stripslashes($slidethree_description); ?>
															</p>
															<?php
															if ($slidethree_link != ""){

															?>
																<a href="<?php echo $slidethree_link; ?>" rel="bookmark" title="<?php echo $slidethree_link_title; ?>" class="button"><?php echo stripslashes($slidethree_link_title); ?></a>
																<?php } 
																?>
												</div>
												<?php
										}		
											else {

											}	
					}
						?>
						<?php
						if ($slidenumber == "4"){
							if ($slidefour_type == "video and text"){
								?>
									<div class="slide">
										<div class="alignright">
								<?php echo stripslashes($slidefour_smallvideo); ?>
										</div>
									<h2>
									<?php echo stripslashes($slidefour_header); ?></h2>	
									<p>
								<?php echo stripslashes($slidefour_description); ?>
										</p>
											<?php
											if ($slidefour_link != ""){

											?>
											<a href="<?php echo $slidefour_link; ?>" rel="bookmark" title="<?php echo $slidefour_link_title; ?>" class="button"><?php echo stripslashes($slidefour_link_title); ?></a>

											<?php } 
											?>
									</div>
								<?php

							}
								else if ($slidefour_type == "image and text"){
											?>
												<div class="slide">
													<div class="alignright">
																		<a href="<?php echo $slidefour_imagelink; ?>">
											<img src="<?php echo $slidefour_image; ?>" alt="<?php echo $slidefour_image_title; ?>"/></a>
													</div>
												<h2>
												<?php echo stripslashes($slidefour_header); ?></h2>	
												<p>
											<?php echo stripslashes($slidefour_description); ?>
													</p>
														<?php
														if ($slidefour_link != ""){

														?>
														<a href="<?php echo $slidefour_link; ?>" rel="bookmark" title="<?php echo $slidefour_link_title; ?>" class="button"><?php echo stripslashes($slidefour_link_title); ?></a>

														<?php } 
														?>
												</div>
											<?php
									}
										else if ($slidefour_type == "image"){
												?>
													<div class="slide">
															<div class="slide-video">
																			<a href="<?php echo $slidefour_imagelink; ?>">	<img src="<?php echo $slidefour_image; ?>" alt="<?php echo $slidefour_image_title; ?>" /></a>
														</div>
													</div>
												<?php
										}
												else if ($slidefour_type == "video"){
														?>	
															<div class="slide">
																<div class="slide-video">
												<?php echo stripslashes($slidefour_smallvideo); ?>
												</div>
												</div>
														<?php
												}
											else if ($slidefour_type == "text"){?>
													<div class="slide">
															<h2>
															<?php echo stripslashes($slidefour_header); ?></h2>	
															<p>
														<?php echo stripslashes($slidefour_description); ?>
																</p>
																<?php
																if ($slidefour_link != ""){

																?>
																	<a href="<?php echo $slidefour_link; ?>" rel="bookmark" title="<?php echo $slidefour_link_title; ?>" class="button"><?php echo stripslashes($slidefour_link_title); ?></a>
																	<?php } 
																	?>
													</div>
													<?php
											}		
												else {

												}	
						}
							?>
		</div>
		<div class="clear"></div>
	</div>
	
	<a href="#" class="previous"><img src="<?php bloginfo('template_directory'); ?>/library/styles/images/previous.png" width="22" height="22" alt="Previous" /></a>
	<a href="#" class="next"><img src="<?php bloginfo('template_directory'); ?>/library/styles/images/next.png" width="22" height="22" alt="Next" /></a>
	<ul class="pagination">
		<?php
		if (($slidenumber == "1") || ($slidenumber == "2") || ($slidenumber == "3") || ($slidenumber == "4")){
			?>
			<li><a href="#"><?php echo stripslashes($slideone_title); ?></a></li>	
			<?php
		}
		?>
			<?php
			if (($slidenumber == "2") || ($slidenumber == "3") || ($slidenumber == "4")){
				?>
				<li><a href="#"><?php echo stripslashes($slidetwo_title); ?></a></li>	
				<?php
			}
			?>
				<?php
				if (($slidenumber == "3") || ($slidenumber == "4")){
					?>
					<li><a href="#"><?php echo stripslashes($slidethree_title); ?></a></li>	
					<?php
				}
				?>
					<?php
					if ($slidenumber == "4"){
						?>
						<li><a href="#"><?php echo stripslashes($slidefour_title); ?></a></li>	
						<?php
					}
					?>
	</ul>	
</div>
			<div class="clear"></div>