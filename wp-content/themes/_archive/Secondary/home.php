<?php get_header(); ?>
<div id="mid" class="fix">

<div id="block">
<?php get_template_part('featured'); ?>
</div>  

<div id="main" class="fix">
<div id="content">


<?php $temp = $wp_query; $wp_query= null; $wp_query = new WP_Query(); $wp_query->query('showposts=3&offset=8'.'&paged='.$paged); ?> 
<?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?> 

<div class="post">
<div class="postMeta">
<?php edit_post_link('Edit','',''); ?> <span class="date">[ <?php the_time('M j, y') ?> ]</span>
<span class="comments">( <?php comments_popup_link('0', '1', '%'); ?> )</span>
</div>
<h2><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title() ?></a></h2>

<h5><?php _e( 'Category:' ) ?> <?php the_category(', '); ?></h5>
		
<div class="entry">
<div class="walk">
<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?><?php echo $image; ?></a>

</div>
<?php the_content_rss('', FALSE, ' ', 40); ?>
<div class="read">
<a title="<?php _e( 'Read' ) ?> <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php _e( 'Read More' ) ?></a>
</div>

</div>
</div>

<?php endwhile; ?>
<div class="navigation">
<?php if(function_exists('wp_pagenavi')) { wp_pagenavi('', '', '', '', 3, false);} ?>
</div>
</div>


</div>

<?php get_template_part('lbar'); ?>
<?php get_template_part('rbar'); ?>
</div>

<?php get_footer(); ?>