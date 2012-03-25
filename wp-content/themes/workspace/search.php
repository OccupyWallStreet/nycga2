<?php get_header(); ?>
	<h1 class="page-title"><?php _e('Search Results for', 'themejunkie'); ?> "<?php the_search_query() ?>"</h1>
    <div id="content">
		<?php if (have_posts()) : while ( have_posts() ) : the_post() ?>
			<?php get_template_part('includes/loop'); ?>
		<?php endwhile; ?>
			<div class="clear"></div>
			<?php if (function_exists('wp_pagenavi')) wp_pagenavi(); else { ?>
				<div class="pagination">
					<div class="left"><?php previous_posts_link(__('Newer Entries', 'themejunkie')) ?></div>
					<div class="right"><?php next_posts_link(__('Older Entries', 'themejunkie')) ?></div>
					<div class="clear"></div>
				</div><!-- .pagination -->  
			<?php } ?> 
		<?php else : ?>
			<?php include(TEMPLATEPATH. '/includes/templates/not-found.php'); ?>
		<?php endif; ?>
    </div><!-- #content-->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
