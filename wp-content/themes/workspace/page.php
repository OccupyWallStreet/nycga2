<?php get_header(); ?>
	<h1 class="page-title"><?php the_title(); ?></h1>
    <div id="content">
		<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-content">
					<?php the_content(''); ?>
					<?php edit_post_link('('.__('Edit', 'themejunkie').')', '<span class="entry-edit">', '</span>'); ?>
				</div><!-- .entry-content -->
				<?php if(get_option('workspace_show_page_comments') == 'on') { ?>
					<?php comments_template(); ?>
				<?php } ?>
			</div><!-- #post-<?php the_ID(); ?> -->		
		<?php endwhile; ?>
		<?php else : ?>
		<?php endif; ?>
    </div><!-- #content -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>