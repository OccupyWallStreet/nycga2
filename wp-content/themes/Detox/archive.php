<?php get_header(); ?>
<div id="content">

<div class="sl"></div>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div class="slate">
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a> </h2>

<div class="entry">
<div class="arlead">
<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
<?php $image = get_the_post_thumbnail($post->ID, 'slider'); ?>
<?php echo $image; ?>
</a>
</div>
<div class="meta">by <?php the_author() ?> | on <?php the_time('M'); ?> <?php the_time('j'); ?></div>
<?php the_content_rss('', FALSE, ' ', 20); ?>
<div class="read"><a title="Read more here" href="<?php the_permalink() ?>">Read on </a></div>
</div>
</div>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php endif; ?>

<div class="navigation">
<?php if(function_exists('wp_pagenavi')) { wp_pagenavi('', '', '', '', 3, false);} ?>
</div>

</div>


<?php get_footer(); ?>