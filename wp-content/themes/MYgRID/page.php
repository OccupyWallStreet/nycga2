<?php get_header(); ?>

	<div class="box">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div id="post-<?php the_ID(); ?>" class="post">
        
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			<div class="hrlineB"></div>
            
			<div class="entry bags">
            
				<div class="ger">
				By <?php the_author() ?>  &bull; <?php the_category(', ') ?> &bull; <?php the_time('j M Y') ?>
				</div>


				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
				<div style="clear: both;"></div>
				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>


			
			
			
				
	<?php comments_template(); ?>
    			</div>
		
		
		<?php endwhile; endif; ?>
	<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>

<div class="cell last bags">	
<?php get_sidebar(); ?>
</div>
</div>
<?php get_footer(); ?>