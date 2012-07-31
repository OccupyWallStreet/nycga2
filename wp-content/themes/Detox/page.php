<?php get_header(); ?>
<div id="content">
<div id="contentmiddle3">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="sl"></div>

<h2 id="post-<?php the_ID(); ?>"><?php the_title(); ?></h2>

<div class="meta">by <?php the_author() ?> | <?php edit_post_link('Edit','','<strong>|</strong>'); ?></div>
	
<div class="entry">
<?php the_content(__('Read more'));?><div class="clearfix"></div>
</div>
<div class="clearfix"></div>

<div class="postspace"></div>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
</div>

<?php get_template_part('bar'); ?>
</div>

<?php get_footer(); ?>