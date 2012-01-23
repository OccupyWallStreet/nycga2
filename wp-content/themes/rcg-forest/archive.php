<?php get_header(); ?>

	<div id="content" class="twocolumns">

<?php if (have_posts()) : ?>

 	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<h1 class="pagetitle"><?php printf(__('Archive for the &#8216;%s&#8217; Category', 'rcg-forest'), single_cat_title('', false)); ?></h1>
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h1 class="pagetitle"><?php printf(__('Posts Tagged &#8216;%s&#8217;', 'rcg-forest'), single_tag_title('', false) ); ?></h1>
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h1 class="pagetitle"><?php printf(_c('Archive for %s|Daily archive page', 'rcg-forest'), get_the_time(__('F jS, Y', 'rcg-forest'))); ?></h1>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h1 class="pagetitle"><?php printf(_c('Archive for %s|Monthly archive page', 'rcg-forest'), get_the_time(__('F, Y', 'rcg-forest'))); ?></h1>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h1 class="pagetitle"><?php printf(_c('Archive for %s|Yearly archive page', 'rcg-forest'), get_the_time(__('Y', 'rcg-forest'))); ?></h1>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h1 class="pagetitle"><?php _e('Author Archive', 'rcg-forest'); ?></h1>
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h1 class="pagetitle"><?php _e('Blog Archives', 'rcg-forest'); ?></h1>
 	  <?php } ?>

                <div class="navigation">
                        <div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries', 'rcg-forest')); ?></div>
                        <div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;', 'rcg-forest')); ?></div>
                </div>

		<?php while (have_posts()) : the_post(); ?>
		<div class="post">
				<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'rcg-forest'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
				<small><?php the_time(__('l, F jS, Y', 'rcg-forest')) ?></small>

				<div class="entry">
					<?php the_content() ?>
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
			<div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries', 'rcg-forest')); ?></div>
			<div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;', 'rcg-forest')); ?></div>
		</div>

	<?php else : ?>

		<h2 class="center"><?php _e('Not Found', 'rcg-forest'); ?></h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
