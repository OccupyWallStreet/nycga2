<?php get_header(); ?>
<div id="content">

<div class="sl"></div>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div class="slate">

<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a> </h2>

<div class="entry">

<div class="hg">
<a href="<?php the_permalink() ?>">
<?php
if (function_exists('vp_get_thumb_url')) {
        $thumb=vp_get_thumb_url($post->post_content, 'browse');
}
?>
<img src="<?php if ($thumb!='') echo $thumb; ?>" alt="<?php the_title(); ?>" />
</a>
</div>

<div class="meta"><?php _e('by', 'Detox') ?> <?php the_author() ?> | <?php _e('on', 'Detox') ?> <?php the_time('M'); ?> <?php the_time('j'); ?></div>
<?php the_excerpt(); ?>
<div class="read"><a title="<?php _e('Read more here', 'Detox') ?>" href="<?php the_permalink() ?>"><?php _e('Read on', 'Detox') ?> </a></div>
</div>
</div>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.', 'Detox') ?></p>

<?php endif; ?>

<div class="navigation">
<?php if(function_exists('wp_pagenavi')) { wp_pagenavi('', '', '', '', 3, false);} ?>
</div>

</div>


<?php get_footer(); ?>