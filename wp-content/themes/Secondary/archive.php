<?php get_header(); ?>
<div id="mid" class="fix">
<div id="single2" class="fix"><a name="main"></a>

<div id="content">
<?php if (have_posts()) : ?>

<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
<?php /* If this is a category archive */ if (is_category()) { ?>
<h2 class="pagetitle"><?php echo single_cat_title(); ?> <?php _e('Posts'); ?></h2>
<div class="alignright"><?php echo category_description($category); ?></div> 
<?php if(is_category()) : ?>
<div class="alignleft"><?php
$this_category = get_category($cat);// This line just gets the active category information
print '<a href="'.get_category_feed_link($this_category->cat_ID, '').'">RSS</a>';
?>  <?php _e('for the'); ?> <?php single_cat_title(); ?> <?php _e('Posts'); ?></div>
<?php endif; ?>
<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
<h2 class="pagetitle"><?php _e('Archive for'); ?> <?php the_time('F jS, Y'); ?></h2>
<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
<h2 class="pagetitle"><?php _e('Archive for'); ?> <?php the_time('F, Y'); ?></h2>
<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
<h2 class="pagetitle"><?php _e('Archive for'); ?> <?php the_time('Y'); ?></h2>
<?php /* If this is an author archive */ } elseif (is_author()) { ?>
<h2 class="pagetitle"><?php _e('Author Archive'); ?></h2>
<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
<h2 class="pagetitle"><?php _e('Blog Archives'); ?></h2>
<?php } ?>
<?php while (have_posts()) : the_post(); ?>


<div class="post clear" id="post-<?php the_ID(); ?>">
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