<?php get_header(); ?>

	<div id="content" class="twocolumns">

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'rcg-forest'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
				<small><?php the_time(__('F jS, Y', 'rcg-forest')) ?></small>

				<div class="entry">
					<?php the_content(__('Read the rest of this entry &raquo;', 'rcg-forest')); ?>
				</div>

                                <div class="postinfocom">
                                <small>
                                        <span class="alignleft">
                                                <?php _e('Posted by','rcg-forest');?> <strong><?php the_author() ?></strong>,
                                                <?php _e('in','rcg-forest'); echo ' '.get_the_category_list(', ');?>
                                                <?php edit_post_link(__('Edit', 'rcg-forest'), ' | ', ''); ?>
                                        </span>
                                        <span class="alignright com"><?php comments_popup_link(__('No Comments &#187;', 'rcg-forest'), __('1 Comment &#187;', 'rcg-forest'), __('% Comments &#187;', 'rcg-forest')); ?></span>
                                </small>
                                </div>

			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries', 'rcg-forest')) ?></div>
			<div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;', 'rcg-forest')) ?></div>
		</div>

	<?php else : ?>

		<h2 class="center"><?php _e('Not Found', 'rcg-forest'); ?></h2>
		<p class="center"><?php _e('Sorry, but you are looking for something that isn&#8217;t here.', 'rcg-forest'); ?></p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
