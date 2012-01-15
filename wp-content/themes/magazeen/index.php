<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>

	<div id="main-content" class="clearfix">
	
		<div class="container">
	
			<div class="col-580 left">
			
				<?php
					query_posts( 'showposts=2' );
					if (have_posts()) : 
						while (have_posts()) : the_post(); $category = get_the_category();
				?>
			
				<div <?php post_class(); ?>>
			
					<div class="post-meta clearfix">
				
						<h3 class="post-title left"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
						
						<p class="post-info right">
							<span>By <?php the_author_posts_link(); ?></span>
							<?php the_time( 'l F j, Y' ) ?>
						</p>
						
					</div><!-- End post-meta -->
					
					<div class="post-box">
					
						<div class="post-content">
					
							<div class="comment-count">
								<?php comments_popup_link(__( '0 Comments' ), __( '1 Comment' ), __( '% Comments' )); ?>
							</div>
							
							<?php if( get_post_meta( $post->ID, "image_value", true ) ) : ?>
							
							<div class="post-image">
								<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "image_value", true ); ?>&amp;w=521&amp;h=246&amp;zc=1" alt="<?php the_title(); ?>" /></a>
							</div>
							
							<?php endif; ?>
							
							<div class="post-intro">
							
								<?php the_content( '' ); ?>
								
							</div><!-- End post-intro -->
							
						</div><!-- End post-content -->
						
						<div class="post-footer clearfix">
						
							<div class="continue-reading">
								<a href="<?php the_permalink() ?>#more-<?php the_ID(); ?>" rel="bookmark" title="Continue Reading <?php the_title_attribute(); ?>">Continue Reading</a>
							</div>
							
							<div class="category-menu">
														
								<div class="category clearfix">
									<div><a href="#"><span class="indicator"></span> <?php echo $category[0]->cat_name; ?></a></div>
								</div>
																
								<div class="dropdown">
								
									<ul class="cat-posts">
										<?php
											$posted = get_posts( "category=" . $category[0]->cat_ID );
											if( $posted ) :
												foreach( $posted as $post ) : setup_postdata( $posted );
										?>
										<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a><span><?php the_time( ' F j, Y' ) ?></span></li>
										<?php
												endforeach;
											endif;
										?>
										<li class="view-more"><a href="<?php echo get_category_link( $category[0]->cat_ID ); ?>" class="view-more">View More &raquo;</a></li>
									</ul>
									
								</div><!-- End dropdown -->
							
							</div><!-- End category -->
													
						</div><!-- End post-footer -->
					
					</div><!-- End post-box -->
					
				</div><!-- End post -->
				
				<?php
						endwhile;
					endif;
				?>
				
				<?php
					query_posts( 'showposts=4&offset=2' );
					if (have_posts()) : $counter = 0;
						while (have_posts()) : the_post(); $category = get_the_category();
						
						if( $counter % 2 == 0 )
							$end = "";
						else
							$end = "last";
				?>
				
				<div <?php post_class( 'single ' . $end ); ?>>
			
					<div class="post-meta clearfix">
				
						<h3 class="post-title left"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
						
					</div><!-- End post-meta -->
					
					<div class="post-box">
					
						<div class="post-content">
					
							<div class="comment-count">
								<?php comments_popup_link(__( '0 Comments' ), __( '1 Comment' ), __( '% Comments' )); ?>
							</div>
							
							<?php if( get_post_meta( $post->ID, "image_value", true ) ) : ?>
							
							<div class="post-image">
								<img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "image_value", true ); ?>&amp;w=235&amp;h=109&amp;zc=1" alt="<?php the_title(); ?>" />
							</div>
							
							<?php endif; ?>
							
							<div class="post-intro">
							
								<?php the_excerpt( '' ); ?>
								
							</div><!-- End post-intro -->
							
						</div><!-- End post-content -->
						
						<div class="post-footer clearfix">
						
							<div class="continue-reading">
								<a href="<?php the_permalink() ?>#more-<?php the_ID(); ?>" rel="bookmark" title="Continue Reading <?php the_title_attribute(); ?>">Continue Reading</a>
							</div>
																				
						</div><!-- End post-footer -->
					
					</div><!-- End post-box -->
					
				</div><!-- End post -->
				
				<?php
					// Clear the left float to allow for different heights
					if( $counter % 2 != 0 )
						echo'<div style="clear:left;"> </div>';
				?>
				
				<?php
						$counter++;
						endwhile;
					endif;
				?>
					<br />
				<?php
					query_posts( 'showposts=6&offset=6' );
					if (have_posts()) :
						while (have_posts()) : the_post(); $category = get_the_category();
				?>
				
				<div <?php post_class( ); ?>>
			
					<div class="post-meta clearfix">
				
						<h3 class="post-title-small left"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
						
						<p class="post-info right">
							<span>By <?php the_author_posts_link(); ?></span>
							<?php the_time( 'l F j, Y' ) ?>
						</p>
						
					</div><!-- End post-meta -->
					
				</div><!-- End archive -->
				
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
