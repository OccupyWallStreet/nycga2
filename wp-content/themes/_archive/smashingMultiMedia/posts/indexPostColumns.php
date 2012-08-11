<?php 
	$counter = 3;      					
	$sticky		= get_option('sticky_posts');
	$showPosts 	= get_option('wps_nonSticky_showposts');
	
	$args2	= array(
		'caller_get_posts' 	=> 1,
		'post__not_in' 		=> $sticky,
		'showposts'			=> $showPosts,
	); 

	//set the class according to the image display selection from the theme options
	$mySecondQuery = new WP_Query($args2);
	while ($mySecondQuery->have_posts()) : $mySecondQuery->the_post();
	
		switch(get_option('wps_mediaPostDisplay_frPgOption')){
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
		switch(get_option('wps_mediaPostDisplay_frPgOption')){
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
							<p><?php _e('Published: ', 'smashingMultiMedia'); ?><?php the_time( get_option( 'date_format' ) ); ?></p>
							<p><?php _e( 'Posted in: ', 'smashingMultiMedia' ); ?><?php echo get_the_category_list(', '); ?></p>
						</div>
								
						<?php 
							$wordLimit 	= get_option('wps_multimediaWordLimit');
							include (TEMPLATEPATH . '/posts/postTeaser/postTeaser.php'); 
						?>
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
		?>