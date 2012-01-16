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

				<?php if (have_posts()) : ?>
				
					<div <?php post_class(); ?>>
			
						<div class="post-meta clearfix">
					
							<h3 class="post-title">
							
								 <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
								  <?php /* If this is a category archive */ if (is_category()) { ?>
									Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category
								  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
									Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;
								  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
									Archive for <?php the_time('F jS, Y'); ?>
								  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
									Archive for <?php the_time('F, Y'); ?>
								  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
									Archive for <?php the_time('Y'); ?>
								  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
									Author Archive
								  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
									Blog Archives
								  <?php } ?>
								
							</h3>
							
							<p class="post-info right">
								<?php bloginfo( 'name' ); ?> Archives
							</p>
							
						</div><!-- End post-meta -->
						
						<div class="post-box">
						
							<div class="post-content">
							
								<p>If you can't find what you are looking for, try searching for it below:</p>
							
								<p><?php echo get_search_form(); ?></p>
								
								<br />
							
							</div>
							
						</div>

				 	</div>

					<?php while (have_posts()) : the_post(); ?>
					
					<div <?php post_class( ); ?>>
			
						<div class="post-meta clearfix">
					
							<h3 class="post-title-small left"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
							
							<p class="post-info right">
								<span>By <?php the_author_posts_link(); ?></span>
								<?php the_time( 'l F j, Y' ) ?>
							</p>
							
						</div><!-- End post-meta -->
						
					</div><!-- End archive -->
			
					<?php endwhile; ?>

						<div class="navigation clearfix">
							<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
							<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
						</div>
				
				<?php
					 else : 
				?>
				
				
	
				<?php
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
