<?php
/**
 * @package WordPress
 * @subpackage Magazeen_Theme
 */

get_header();
?>

	<div id="main-content" class="clearfix">
	
		<div class="container">
	
			<div class="col-580 left">
			
				<?php
					if (have_posts()) : 
						while (have_posts()) : the_post(); $category = get_the_category();
				?>
							
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
					<div class="post-meta clearfix">
				
						<h3 class="post-title left"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
						
						<p class="post-info right">
							<span>By <?php the_author_posts_link(); ?></span>
							<?php the_time( 'l F j, Y' ) ?>
						</p>
						
					</div><!-- End post-meta -->
					
					<div class="post-box">
					
						<div class="page-content clearfix">
						
							<div class="clearfix">
						
								<?php if( get_post_meta( $post->ID, "image_value", true ) ) : ?>
									
									<div class="post-image-inner right">
										<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "image_value", true ); ?>&amp;w=225&amp;h=246&amp;zc=1" alt="<?php the_title(); ?>" /></a>
									</div>
								
								<?php endif; ?>
						
								<?php the_content( '' ); ?>
								
								<?php wp_link_pages( array( 'before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number' ) ); ?>
															
									<br />
								
							</div>
																				
						</div><!-- End post-content -->
																	
					</div><!-- End post-box -->
					
				</div><!-- End post -->				

				<?php comments_template(); ?>
								
				<?php
						endwhile;
					endif;
				?>
				
			</div><!-- End col-580 (Left Column) -->
			
			<div class="col-340 right">
			
				<ul id="sidebar">
				
					<?php get_sidebar(); ?>
					
				</ul><!-- End sidebar -->   
								
			</div><!-- End col-340 (Right Column) -->
			
		</div><!-- End container -->
		
	</div><!-- End main-content -->

<?php get_footer(); ?>

