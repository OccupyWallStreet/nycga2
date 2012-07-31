<?php
/*
Template Name: Widgetized Page
*/
?>


<?php get_header() ?>

	<div id="content">
		<div class="padder">
	
			<!-- BEGIN: content columns -->
			<div class="content-columns">
			
				<!-- BEGIN: sticky note -->
				

				<!-- //END: sticky note -->
				
				<!-- BEGIN: left content column -->
				<div class="left-content">
				<?php locate_template( array( 'sidebar-home-1.php' ), true ) ?>
				</div><!-- //END: left content column -->
				
				<!-- BEGIN: right content column -->
				<div class="right-content">
				
					<?php locate_template( array( 'sidebar-home-2.php' ), true ) ?>
					
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

