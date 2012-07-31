<?php get_header() ?>

	<div id="content" class="grid_24">
		<div class="padder">
	
			<!-- BEGIN: content columns -->
			<div class="content-columns grid_19">
			
				<!-- BEGIN: page content -->
				
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
					<div id="post-<?php the_ID(); ?>">
	
						<h3 class="page-content"><?php the_content( __( '<p class="serif">Read the rest of this page &rarr;</p>', 'buddypress' ) ); ?></h3>
	
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
						<?php edit_post_link( __( 'Edit this page.', 'buddypress' ), '<p class="button">', '</p>'); ?>
	
					</div>
	
				<?php endwhile; endif; ?>
					
				<!-- //END: page content -->
				
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
	
<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>

