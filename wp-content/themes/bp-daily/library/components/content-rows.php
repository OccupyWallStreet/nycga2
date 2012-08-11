<?php 
	$cat_row = get_option('dev_buddydaily_featurecat_rows');
	$cat_row_num = get_option('dev_buddydaily_featurecat_rows_num');
?>
		<?php query_posts('category_name='. $cat_row . '&posts_per_page='. $cat_row_num . ''); ?>
						  <?php while (have_posts()) : the_post(); ?>
			<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
				<div class="light-container">
						<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span>
				<?php the_excerpt(); ?>
	<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('permalink to', 'bp-daily');?>" class="button"><?php _e( 'more', 'bp-daily' ) ?></a>
			</div>
					<?php endwhile; ?>
				