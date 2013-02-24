<?php
/*
Template Name: Homepage
*/
?>

<?php get_header(); ?>
			
			<div id="content">
			
				<div id="main" class="twelve columns" role="main">
					
					<div class="top-row">
						<div class="four columns">
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

								<section class="row post_content">
								
								<?php the_content(); ?>
															
								</section> <!-- end article header -->					
							
								<?php endwhile; ?>	
											
							<?php endif; ?>

						</div>
						<div class="main eight columns">

						<?php

						$orbit_slider = of_get_option('orbit_slider');
						if ($orbit_slider){

						?>
						
						<header>
						
							<div id="featured">

								<?php
									global $post;
									$tmp_post = $post;
									$args = array( 'numberposts' => 5 );
									$myposts = get_posts( $args );
									foreach( $myposts as $post ) :	setup_postdata($post); 
										$post_thumbnail_id = get_post_thumbnail_id();
										$featured_src = wp_get_attachment_image_src( $post_thumbnail_id, 'wpf-home-featured' );
								?>
								
								<div style="background-color: #F2F2F2;">
									<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<?php the_excerpt(); ?>
									<p><a href="<?php the_permalink(); ?>" class="button nice radius">Read more »</a></p>
								</div>
								
								<?php endforeach; ?>
								<?php $post = $tmp_post; ?>

							</div>
							
						</header>


						<script type="text/javascript">
						   $(window).load(function() {
						       $('#featured').orbit({ 
						       	fluid: '7x3'
						       });
						   });
						</script>

						<?php } ?>

						</div>
					</div>

					<div class="middle-row">
						<div class="five columns">

							<?php if ( is_active_sidebar( 'homecontent1' ) ) : ?>

							<?php dynamic_sidebar( 'homecontent1' ); ?>

							<?php endif; ?>

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

								<section class="post_content">
								
									<?php echo get_post_meta($post->ID, 'custom_tagline', true); ?>

								</section> <!-- end article header -->					
							
								<?php endwhile; ?>	
											
							<?php endif; ?>


						</div>
						<div class="sidebar three columns panel" role="complementary">

							<?php if ( is_active_sidebar( 'sidebar2' ) ) : ?>

							<?php dynamic_sidebar('sidebar2'); // sidebar 2 ?>

							<?php endif; ?>

						</div>
					</div>

					<div class="bottom-row">
						<?php if ( is_active_sidebar( 'homecontent2' ) ) : ?>

							<div class="twelve columns panel">

								<?php dynamic_sidebar( 'homecontent2' ); ?>

							</div>

						<?php endif; ?>

					</div>

				</div> <!-- end #main -->
    
				<?php //get_sidebar(); // sidebar 1 ?>
    
			</div> <!-- end #content -->

<?php get_footer(); ?>