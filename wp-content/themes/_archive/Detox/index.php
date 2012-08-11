<?php get_header(); ?>
<div id="content">
<div id="contentmiddle2">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="sl"></div>
<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a> <span class="date">on <?php the_time('M'); ?><span class="bigdate"><?php the_time('j'); ?></span> <?php the_time('Y'); ?></span></h2>

<div class="meta">by <?php the_author() ?> | <?php edit_post_link('Edit','','<strong>|</strong>'); ?>  &#732; <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></div>
	
<div class="entry">
<div class="alignright">
<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
<?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?>
<?php echo $image; ?>
</a>
</div>
<?php the_content(__('Read more'));?>
<div class="postspace"></div>
</div>

<div class="meta"> 
Topic: <?php the_category(', ') ?> | <?php edit_post_link('Edit','','<strong>|</strong>'); ?><?php if(function_exists('the_tags')) {$my_tags = get_the_tags();if ( $my_tags != "" ){ the_tags('Tags: ', ', ', '<br />'); } else {echo "Tags: None";} }?><?php if(function_exists('UTW_ShowTagsForCurrentPost')) { echo 'Tags: ';UTW_ShowTagsForCurrentPost("commalist");echo '<br />'; } ?> | <a title="Stumble it" href="http://www.stumbleupon.com/submit&#38;url=<?php bloginfo('url'); ?>">
<img src="<?php bloginfo('stylesheet_directory'); ?>/images/su.png" alt="stumble" /></a> | <a title="Submit it to Digg" href="http://digg.com/submit?phase=2&#38;url=<?php bloginfo('url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/digg.png" alt="digg" /></a> | <a title="Add to del.icio.us" href="http://del.icio.us/post&#38;url=<?php bloginfo('url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/del.png" alt="del"/></a> | <a title="Links in Technorati" href="http://technorati.com/search/<?php bloginfo('url'); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/tecno.png" alt="tech"/></a>
</div>
	
<div class="postspace"></div>
	<?php endwhile; else: ?>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
	<?php comments_template(); // Get wp-comments.php template ?>

<div class="navigation">
<?php $wp_query->is_single = true; previous_post ('&laquo; %', '', 'yes'); $wp_query->is_single = false; ?>
</div>
<div class="postspace"></div>

</div>

<?php get_template_part('sbar'); ?>
<?php get_template_part('bar'); ?>

</div>

<?php get_footer(); ?>