<?php get_header(); ?>

	<div id="content">
			<?php get_template_part('includes/breadcrumbs'); ?>
	    <article id="main-article">
	    <?php $count = 1; ?>
		<?php if (have_posts()) : while ( have_posts() ) : the_post() ?>
			<?php get_template_part('includes/loop'); ?>
	        <?php
	            if($count%3 == 0){
	         ?>
	            <div class="clear"></div>
	        <?php
	            };
	            $count++;
	        ?>
		<?php endwhile; ?>
		<div class="clear"></div>
	    </article>
		<?php if (function_exists('wp_pagenavi')) wp_pagenavi(); else { ?>
			<div class="pagination">
		    	<div class="left"><?php previous_posts_link(__('&larr; Newer Entries', 'theme junkie')) ?></div>
		   		<div class="right"><?php next_posts_link(__('Older Entries &rarr;', 'theme junkie')) ?></div>
		   		<div class="clear"></div>
			</div><!-- .pagination -->
		<?php } ?>
		<?php else : ?>
			<?php get_template_part('includes/not-found'); ?>
		<?php endif; ?>
	</div><!-- #content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
