<?php
$slide = 1;
$home_featured_block_style = get_option('tn_buddyfun_home_featured_block_style');
$home_featured_block_cat = get_option('tn_buddyfun_home_featured_block_cat');
$home_featured_block_count = get_option('tn_buddyfun_home_featured_block_count');
$home_featured_block_custom_field = get_option('tn_buddyfun_home_featured_block_custom_field');
$home_featured_block_attach_type = get_option('tn_buddyfun_home_featured_block_attach_type');

if($home_featured_block_cat == 'Choose a category') { $home_featured_block_cat = ''; }

$category_id = get_cat_ID($home_featured_block_cat);
$cat_query = new WP_Query('cat='. $home_featured_block_cat . '&' . 'showposts=' . $home_featured_block_count); ?>

<div id="slider-wrapper">
<div id="slider" class="nivoSlider">

<?php
while ($cat_query->have_posts()) : $cat_query->the_post();
$the_post_ids = $post->ID;
$do_not_duplicate = $post->ID;
$values_img = get_post_meta($the_post_ids, $home_featured_block_custom_field, true);
?>

<a href="<?php the_permalink() ?>">

<?php if($home_featured_block_attach_type != "attachment") { ?>
<?php if ($home_featured_block_custom_field != '') : ?>
<?php if ($values_img != '') : ?>
<img class="nivo-featimg" title="<h1><?php the_title(); ?></h1>" src="<?php echo $values_img; ?>" alt="" /></a>
<?php else : ?>
<?php dez_get_attachment($the_post_id = $the_post_ids); ?>    
<?php endif; ?>
<?php endif; ?>

<?php } else { ?>
<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids, $with_wrap='no', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='large', $fetch_w='500', $fetch_h='auto',$alt_class='alignleft nivo-featimg feat-thumb'); ?>
<?php } ?>

</a>

<?php $slide++; endwhile; ?>

</div></div>

