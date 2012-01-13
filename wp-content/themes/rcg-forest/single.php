<?php get_header(); ?>

	<div id="content" class="onecolumn">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="navigation">
			<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
			<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
		</div>

		<div class="post" id="post-<?php the_ID(); ?>">
                        <h2><?php the_title(); ?></h2>
                        <small><?php printf(__('%1$s at %2$s', 'rcg-forest'), get_the_time(__('F jS, Y', 'rcg-forest')), get_the_time('G:i')); ?></small>

			<div class="entry">
				<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'rcg-forest') . '</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>' . __('Pages:', 'rcg-forest') . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				<?php the_tags( '<p>' . __('Tags:', 'rcg-forest') . ' ', ', ', '</p>'); ?>

			</div>
                        <div class="postinfo">
                                <small>
                                        <?php _e('Posted by','rcg-forest');?> <strong><?php the_author() ?></strong>,
                                        <?php _e('in','rcg-forest'); echo ' '.get_the_category_list(', ');?>
                                        <?php edit_post_link(__('Edit', 'rcg-forest'), ' | ', ''); ?>
                                </small>
                        </div>
		</div>


	<?php comments_template(); ?>

	<?php endwhile; else: ?>

		<p><?php _e('Sorry, no posts matched your criteria.', 'rcg-forest'); ?></p>

        <?php endif; ?>

	</div>

<?php get_footer(); ?>
