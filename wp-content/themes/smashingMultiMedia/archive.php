<?php get_header(); ?> 
<div id="floatswrap" class="smallftfl clearfix">
	
	<div class="container clearfix">
		<?php the_post();                    
                        
			if ( is_day() ) { ?>
                <h2 class="archive-title daily-title"><?php printf( __( 'Daily Archives: <span>%s</span>', 'smashingMultiMedia' ), get_the_time(get_option('date_format')) ) ?></h2>
			<?php } elseif ( is_month() ) { ?>
                <h2 class="archive-title monthly-title"><?php printf( __( 'Monthly Archives: <span>%s</span>', 'smashingMultiMedia' ), get_the_time('F Y') ) ?></h2>
			<?php } elseif ( is_year() ) { ?>
                <h2 class="archive-title yearly-title"><?php printf( __( 'Yearly Archives: <span>%s</span>', 'smashingMultiMedia' ), get_the_time('Y') ) ?></h2>
			<?php }	elseif (is_author()) { ?>
				<h2 class="archive-title author-title"><?php printf( __( 'Author Archives: <span>%s</span>', 'smashingMultiMedia' ), "$authordata->display_name" ) ?></h2>
			<?php }	elseif(is_tag()) { ?>
				 <h2 class="archive-title tag-title"><?php _e('Posts Tagged: ','smashingMultiMedia'); ?><?php single_tag_title(); ?></h2>
			<?php } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
                <h2 class="archive-title"><?php _e( 'Blog Archives', 'smashingMultiMedia' ) ?></h2>
			<?php } ?>

		<div id="main_col">
			<?php rewind_posts();
				while (have_posts()) : the_post(); ?>
					
					<div <?php post_class("clearfix archive_post"); ?> id="post-<?php the_ID(); ?>">
						<h3 class="entry-title archive-entry-title clearfix">
							<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a>
						</h3>
						
						
						<?php  $output = my_attachment_image(0, 'medium', 'alt="' . $post->post_title . '"','return');
						if (strlen($output[img_path])>0) { ?>
														
							<a class="thumb_img alignleft" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
								<?php my_attachment_image(0, 'medium', 'alt="' . $post->post_title . '"'); ?>
							</a> 
							
						<?php } elseif ((get_post_meta($post->ID, "3colmediaSplashImg_value", $single = true) != "") || (get_post_meta($post->ID, "3colmediaSplashImgAlt_value", $single = true) != "")){ ?>
						
							<a class="thumb_img alignleft" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
								<?php
								if (get_post_meta($post->ID, "3colmediaSplashImgAlt_value", $single = true) != "") {$img_src 	= get_post_meta($post->ID, "3colmediaSplashImgAlt_value", $single = true); } 
								else {$img_src 	= get_post_meta($post->ID, "3colmediaSplashImg_value", $single = true);}
								$des_src 	= 'wp-content/uploads/image-120/';	
								$img_file 	= mkthumb($img_src,$des_src,120,'height');    
								$imgURL 	= get_option('home').'/'.$des_src.''.$img_file; ?>
								<img src="<?php echo $imgURL; ?>" alt="<?php the_title(); ?>" />
							</a>
						<?php } else { } ?>
						
						<div class="meta">
							<p class="date"><?php the_time( get_option( 'date_format' ) ); ?></p>
							<p><?php _e( 'Posted in: ', 'smashingMultiMedia' ); ?><?php echo get_the_category_list(', '); ?></p> 
							<?php if (is_tag()) { 
								if ($tag_ur_it = tag_ur_it(', ')) { // Returns tags other than the one queried?>
									<p><?php printf( __( 'Also tagged %s', 'your-theme' ), $tag_ur_it ); ?></p>
								<?php } 
							} else { the_tags( '<p>' . __('Tagged as: ', 'smashingMultiMedia' ), ',' , '</p>' );} ?>
						</div>	
					</div><!-- post --> 
					<?php 
				endwhile; 
									
				include('wp-pagenavi.php');
				if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>
		</div><!-- main_col -->
			
		<?php get_sidebar(); ?>
	</div><!-- container -->
</div><!-- floatswrap-->
<?php get_footer(); ?>