<div id="feat-content">
<div class="feat-articles">
<div class="feat-post">

<?php
$home_featured_block_style = get_option('tn_buddycorp_home_featured_block_style');
$home_featured_block_cat = get_option('tn_buddycorp_home_featured_block_cat');
$home_featured_block_count = get_option('tn_buddycorp_home_featured_block_count');
$home_featured_block_custom_field = get_option('tn_buddycorp_home_featured_block_custom_field');
$home_featured_block_attach_type = get_option('tn_buddycorp_home_featured_block_attach_type');
$get_post_counter = get_the_current_blog_post_count();

if($home_featured_block_cat == 'Choose a category' || $home_featured_block_cat == '') { $home_featured_block_cat = ''; }
if($home_featured_block_count == 'Select a number' || $home_featured_block_count == '') { $home_featured_block_count = '5'; }

$featured_count = dev_multi_category_count($catslugs = $home_featured_block_cat);

$cat_query = new WP_Query('cat='. $home_featured_block_cat . '&' . 'showposts=' . $home_featured_block_count);
while ($cat_query->have_posts()) : $cat_query->the_post();
$the_post_ids = $post->ID;
$do_not_duplicate = $post->ID;
$bc == 0;
?>

<?php if($bc < 1) { ?>

<h4><span><a href="<?php echo site_url() . '/' . get_the_page_template_slug('template-blog.php'); ?>">
<?php _e('Blog', TEMPLATE_DOMAIN); ?></a> | <?php the_time('F jS') ?></span></h4>
<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
<div class="feat-tag"><?php the_tags(__('tagged in&nbsp;', TEMPLATE_DOMAIN), ', ', ''); ?></div>
<div class="feat-post-content">
<?php the_post_thumbnail(array(432,999), array('class' => 'feat-post-thumbnail')); ?>           
<?php echo custom_the_content(125); ?>
</div>

<?php if($featured_count != '1' && $get_post_counter > '2') { ?>
<h2><?php _e( 'More Article &raquo;', TEMPLATE_DOMAIN) ?></h2>
<ul class="more-article">
<?php } ?>

<?php } elseif($bc > 1 || $featured_count != '1') { ?>

<li><div class="alignleft"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></div><div class="alignright"><?php the_time('F jS') ?></div></li>

<?php } ?>

<?php $bc++; endwhile; ?>

<?php if($featured_count != '1') { ?></ul><?php } ?>

</div>
</div>
</div>