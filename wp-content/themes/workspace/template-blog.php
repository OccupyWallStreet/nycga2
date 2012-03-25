<?php
/*
Template Name: Blog
*/
?>

<?php get_header(); ?>
	
	<h1 class="page-title"><?php echo get_option('workspace_blog_page_title'); ?></h1>
	<div id="content">
		<?php 
			$temp = $wp_query;
			$wp_query= null;
			$wp_query = new WP_Query();
			$wp_query->query('paged='.$paged);
			while ($wp_query->have_posts()) : $wp_query->the_post();
		?>
		
			<?php get_template_part('includes/loop'); ?>
			
	    <?php endwhile; ?>
	    
	    <?php if (function_exists('wp_pagenavi')) wp_pagenavi(); else { ?>
			<div class="pagination">
		    	<div class="left"><?php previous_posts_link(__('Newer Entries', 'themejunkie')) ?></div>
		   		<div class="right"><?php next_posts_link(__('Older Entries', 'themejunkie')) ?></div>
		    	<div class="clear"></div>
			</div><!-- .pagination -->
	    <?php } ?>
		<?php $wp_query = null; $wp_query = $temp;?>
	</div><!-- #content -->
	
<?php get_sidebar(); ?>
<?php get_footer(); ?>