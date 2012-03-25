<?php get_header(); ?>

	<h1 class="page-title">
		<?php if( is_tag() ) { ?>
			<?php _e('Posts Tagged &quot;','themejunkie') ?><?php single_tag_title(); echo('&quot;'); ?>
		<?php } elseif (is_day()) { ?>
			<?php _e('Posts made in','themejunkie') ?> <?php the_time('F jS, Y'); ?>
		<?php } elseif (is_month()) { ?>
			<?php _e('Posts made in','themejunkie') ?> <?php the_time('F, Y'); ?>
		<?php } elseif (is_year()) { ?>
			<?php _e('Posts made in','themejunkie') ?> <?php the_time('Y'); ?>
		<?php } elseif (is_category()) { ?>
			<?php single_cat_title(); ?>
		<?php }; ?>
	</h1>
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
		<?php endif; ?>
    </div><!-- #content -->
    
<?php get_sidebar(); ?>
<?php get_footer(); ?>
