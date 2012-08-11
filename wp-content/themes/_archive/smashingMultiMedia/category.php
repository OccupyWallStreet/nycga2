<?php get_header();
$catText 	= category_description($currentCat);?> 

<div id="floatswrap" class="smallftfl clearfix">
	<div class="container clearfix">
	
		<h2 class="cat-title"><?php single_cat_title(); ?><span><?php echo strip_tags($catText); ?></span></h2>
		
		<div id="main_col">
			<?php if (have_posts()) : 
				//set the counter 
				$counter = 3;      					
				while (have_posts()) : the_post();
					//set the class according to the image display selection from the theme options
					switch(get_option('wps_mediaPostDisplay_catOption')){
						case 'option1':
							$postClass = 'mediaPanes';
						break;
																
						case 'option2':
							$postClass = 'mediaPanesAlt';
						break;
					}
					//set the post class 
					$the_div_class = alternating_css_class($counter,3,' first-post'); ?> 
					<div <?php post_class("$postClass media3 $the_div_class"); ?> id="post-<?php the_ID(); ?>">
						<?php 
						// let's display now the post according to the image display selection from the theme options
						switch(get_option('wps_mediaPostDisplay_catOption')){
							case 'option1': ?>
								<div class="mediaWrap">
									
									<div class="teaser">
										<?php 
											// get the image 
											include (TEMPLATEPATH . '/posts/postSplashImg/col3_img.php'); 
										?>
											
										<h4><a href="<?php the_permalink(); ?>" title="<?php printf( __('Permalink to %s', 'smashingMultiMedia'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h4>
											
										<a href="#nogo" class="metaInfo">Info</a>
											
										<div class="meta tooltip">
											<p><?php _e('Published: ', 'smashingMultiMedia'); ?><?php the_time( get_option( 'date_format' ) ); ?> | <?php _e('By ', 'smashingMultiMedia'); ?><a href="<?php echo get_author_link( false, $authordata->ID, $authordata->user_nicename ); ?>" title="<?php printf( __( 'View all posts by %s', 'your-theme' ), $authordata->display_name ); ?>"><?php the_author(); ?></a></p>
											<?php if ($cats_meow = cats_meow(', ')) { // Returns categories other than the one queried ?>
												<p><?php printf( __( 'Also posted in %s', 'your-theme' ), $cats_meow ) ?></p>
											<?php } 
											the_tags( '<p>' . __('Tagged as: ', 'smashingMultiMedia' ), ',' , '</p>' ) ?>
										</div>
											
										<?php include (TEMPLATEPATH . '/posts/postTeaser/postTeaser.php'); ?>
											
									</div><!-- teaser -->
										
									<?php include (TEMPLATEPATH . '/posts/postFooter/postFootnotesAlt.php'); ?>
										
								</div><!-- mediaWrap -->
							<?php break;
				
							case 'option2': ?>
								<h4><a href="<?php the_permalink(); ?>" title="<?php printf( __('Permalink to %s', 'smashingMultiMedia'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h4>
								<div class="mediaWrap mediaWrapAlt">
									<?php 
										// get the image
										include (TEMPLATEPATH . '/posts/postSplashImg/col3_img.php'); 
										include (TEMPLATEPATH . '/posts/postFooter/postFootnotesAlt.php'); 
									?>
								</div><!-- mediaWrap -->
								<div class="teaser teaserAlt">
									<?php 
										$wordLimit 	= get_option('wps_multimediaWordLimit');
										include (TEMPLATEPATH . '/posts/postTeaser/postTeaser.php'); 
									?>
								</div><!-- teaser -->
							<?php  break;
						} ?>
					</div><!-- mediaPanes -->
					<?php 
					// clear for nicely displayed rows :)
					echo insert_clearfix($counter,3,' <div class="clear"></div>');
						
					$counter++;
				
				endwhile; 
				
				
					include('wp-pagenavi.php');
					if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
					?>
					
			<?php else : ?>
						
					<h2><?php _e('Not Found','smashingMultiMedia');?></h2>
					<p><?php _e('Sorry, but you are looking for something that is not here.','smashingMultiMedia');?></p>
					<div class="main_col_searchform">
						<?php include (TEMPLATEPATH . '/searchform.php'); ?>
					</div><!-- main_col_searchform -->
							
			<?php endif; ?> 
		
		</div><!-- main_col -->
			
		<?php get_sidebar(); ?>
	</div><!-- container -->
</div><!-- floatswrap-->
<?php get_footer(); ?>