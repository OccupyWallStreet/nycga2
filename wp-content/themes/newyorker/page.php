<?php get_header(); ?>
<div id="content">
<div id="middle">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="sl"></div>
<div id="sfeatured">
<?php
if (function_exists('vp_get_thumb_url')) {
        $thumb=vp_get_thumb_url($post->post_content, 'bigg');
}
?>
<img src="<?php if ($thumb!='') echo $thumb; ?>" alt="<?php the_title(); ?>" />
<h1><?php the_title(); ?></h1>
</div>

<div class="meta">by <?php the_author() ?> | <?php edit_post_link('Edit','','<strong>|</strong>'); ?></div>
	
<div class="entry">
<?php the_content(__('Read more', 'Detox'));?>
<div class="clearfix"></div>
</div>
<div class="clearfix"></div>

<div class="postspace"></div>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.', 'Detox') ?></p>
<?php endif; ?>
</div>

<?php get_template_part('bar'); ?>
</div>

<?php get_footer(); ?>