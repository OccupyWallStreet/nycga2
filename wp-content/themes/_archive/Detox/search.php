<?php get_header(); ?>
<div id="content">
<div id="contentmiddle2">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="sl"></div>
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a> <span class="date">on <?php the_time('M'); ?><span class="bigdate"><?php the_time('j'); ?></span> <?php the_time('Y'); ?></span></h2>

<div class="meta">by <?php the_author() ?> | <?php edit_post_link('Edit','','<strong>|</strong>'); ?>  &#732; <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></div>
	
<div class="entry">
<div class="alignright">
<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
<?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?>
<?php echo $image; ?>
</a>
</div>
<?php the_content_rss('', FALSE, '', 24); ?>
<div class="read"><a title="Read more here" href="<?php the_permalink() ?>">Read on </a></div>
</div>
<div class="postspace">
</div>
	
	<?php endwhile; else: ?>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p><?php endif; ?>
	<div class="navigation">
<?php if(function_exists('wp_pagenavi')) { wp_pagenavi('', '', '', '', 3, false);} ?>
</div>
</div>

<?php get_template_part('sbar'); ?>
<?php get_template_part('bar'); ?>

</div>

<?php get_footer(); ?>