<?php get_header(); ?> 
<div id="floatswrap" class="smallftfl clearfix">
	<div class="container clearfix">
	
		<div id="main_col">
			<?php if (have_posts()) : 
				while (have_posts()) : the_post(); ?>
					<div <?php post_class("clearfix search_post"); ?> id="post-<?php the_ID(); ?>">
						<h3 class="entry-title search-entry-title clearfix">
							<span class="alignright"><?php the_time( get_option( 'date_format' ) ); ?></span>
							<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a>
						</h3>
						
					</div><!-- search_post -->
							
				<?php 
				endwhile; 
					include('wp-pagenavi.php');
					if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
				?>
					
			<?php else : ?>
				<h4><?php _e('Nothing found','smashingMultiMedia');?></h4>
				<p><?php _e('Try a different search?','smashingMultiMedia');?></p>
				<div class="main_col_searchform">
					<?php include (TEMPLATEPATH . '/searchform.php'); ?>
				</div>
			<?php endif; ?>
				
		</div><!-- main_col -->
			
		<?php get_sidebar(); ?>
	</div><!-- container -->
</div><!-- floatswrap-->
<?php get_footer(); ?>