<?php
/*
Template Name: Default Index Page
*/
?>


<?php get_header() ?>

	<div id="content" class="container_24">
		<div class="padder">
	
			<!-- BEGIN: content columns -->
			<div class="content-columns container_19">
			
				<!-- BEGIN: sticky note -->
				

				<!-- //END: sticky note -->
				
				<!-- BEGIN: left content column -->
				<div class="left-content grid_5">
				<?php get_sidebar('left') ?>
				</div><!-- //END: left content column -->
				
				<!-- BEGIN: right content column -->
				<div class="right-content grid_14">
							

							
					<?php get_sidebar('right') ?>
					
					<div class="recent-posts">
					<h3 class="widgettitle">Recent Posts</h3>
					<?php query_posts( array( 'post__not_in' => get_option( 'sticky_posts' ), 'caller_get_posts' => 1, 'orderby' => ID, 'showposts' => 5  ) ); ?>
					<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
					
					<h4><?php the_title(); ?></h4>
					<div <?php post_class(); ?>>
						<?php the_excerpt(); ?>
					</div>
					<?php endwhile; endif; ?>
					</div>
					
				</div><!-- //END: right content column -->
			</div><!-- //END: content columns -->
			
		</div><!-- .padder -->
	</div><!-- #content -->
	
	<?php get_sidebar() ?>
	
<?php get_footer(); ?>

