<?php get_header(); ?>
<?php 
	$welcome_title = get_option('ne_buddyscholar_welcome_title');
	$welcome_message = get_option('ne_buddyscholar_welcome_message');
	$news_category = get_option('ne_buddyscholar_news_cat');
	$news_amount = get_option('ne_buddyscholar_news_number');
	$news_image_display = get_option('ne_buddyscholar_news_image_size');
	$spotlight_category = get_option('ne_buddyscholar_spotlight_cat');
	$spotlight_image_display = get_option('ne_buddyscholar_spotlight_image_size');
	$spotlight_amount = get_option('ne_buddyscholar_spotlight_number');
?>

			<div id="content">
					<?php 
						locate_template( array( '/slideshow.php' ), true );
					 ?>
					<div id="front-2col">
						<div id="front-maincolumn">
										<div class="content-box-outer">
								<div class="h3-background">
									<h3><?php echo stripslashes($welcome_title); ?></h3>
								</div>
								<div class="content-box-inner">
									<div class="entry">
									 <p><?php echo stripslashes($welcome_message); ?></p>
									</div>
								</div>
								</div>
										<div class="content-box-outer">
									<div class="h3-background">
										<h3><?php _e( 'Latest news', 'bp-scholar' ) ?></h3>
									</div>
									</div>
										
															<?php query_posts('category_name='. $news_category . '&showposts='. $news_amount . ''); ?>
																			  <?php while (have_posts()) : the_post(); ?>
																					<div class="content-box-outer">
										<div class="h3-background">
											<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('permalink to', 'bp-scholar');?><?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
										</div>
										<div class="content-box-inner">
											<div class="entry">
													<div class="entry-image">
														<span class="attach-post-image" style="height:100px;width:100px;display:block;background:url('<?php the_post_image_url($news_image_display); ?>') center center repeat">&nbsp;</span></div>
																	<?php the_excerpt(); ?>
																<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('permalink to', 'bp-scholar');?><?php the_title_attribute(); ?>"><?php _e( 'Read more', 'bp-scholar' ) ?></a>
																<div class="clear"></div>
											</div>
										</div>
										</div>
													<?php endwhile; ?>
									
									
						</div>
						<div id="front-sidebar">
								<div class="h3-background">
									<h3><?php _e( 'Spotlight', 'bp-scholar' ) ?></h3>
								</div>
								<?php query_posts('category_name='. $spotlight_category . '&showposts='. $spotlight_amount . ''); ?>
												  <?php while (have_posts()) : the_post(); ?>
							
								<div class="content-box-inner">
								
									<div class="entry">
									<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'bp-scholar' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
														<div class="entry-image-side">				<span class="attach-post-image" style="height:60px;width:170px;display:block;background:url('<?php the_post_image_url($spotlight_image_display); ?>') center center repeat">&nbsp;</span></div>																				<?php the_excerpt(); ?>
																								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('permalink to', 'bp-scholar');?><?php the_title_attribute(); ?>"><?php _e( 'Read more', 'bp-scholar' ) ?></a>
																								<hr/>								
									</div>
								</div>
									<?php endwhile; ?>
						</div>
				</div>
			</div>
	<?php get_sidebar('home'); ?>
<?php get_footer(); ?>
