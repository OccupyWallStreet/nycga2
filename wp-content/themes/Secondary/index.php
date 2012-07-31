<?php get_header(); ?>
<div id="mid" class="fix">
<div id="single2" class="fix"><a name="main"></a>

<div id="content">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">
<div class="postMeta"><?php edit_post_link('Edit','',''); ?> <span class="date">[ <?php the_time('M j, y') ?> ]</span>
<span class="comments">( <?php comments_popup_link('0', '1', '%'); ?> )</span></div>
<h2><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title() ?></a></h2>

<h5><?php _e( 'Category:' ) ?> <?php the_category(', '); ?></h5>
		
<div class="entry">
<div class="walk">
<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?><?php echo $image; ?></a>
</div>
<?php the_excerpt_rss(40, 0); ?>
<div class="read">
<a title="<?php _e( 'Read' ) ?> <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php _e( 'Read More' ) ?></a>
</div>
</div>
</div>

<?php endwhile; ?>
<?php get_template_part('paginate'); ?>
<?php else: ?>
<?php get_template_part('result'); ?>
<?php endif; ?>

</div>
</div>
<?php get_template_part('rbar'); ?>
</div>
<?php get_footer(); ?>