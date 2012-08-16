<?php
$slide = 1;
$home_featured_block_style = get_option('tn_buddycorp_home_featured_block_style');
$home_featured_block_cat = get_option('tn_buddycorp_home_featured_block_cat');
$home_featured_block_count = get_option('tn_buddycorp_home_featured_block_count');
$home_featured_block_custom_field = get_option('tn_buddycorp_home_featured_block_custom_field');
$home_featured_block_attach_type = get_option('tn_buddycorp_home_featured_block_attach_type');

if($home_featured_block_cat == 'Choose a category' || $home_featured_block_cat == '') { $home_featured_block_cat = ''; }
if($home_featured_block_count == 'Select a number' || $home_featured_block_count == '') { $home_featured_block_count = '5'; }

?>

<div id="slider-wrapper">
<div id="slider" class="nivoSlider">

<?php
$cat_query = new WP_Query('cat='. $home_featured_block_cat . '&' . 'showposts=' . $home_featured_block_count);
while ($cat_query->have_posts()) : $cat_query->the_post();
$the_post_ids = $post->ID;
$do_not_duplicate = $post->ID;
$values_img = get_post_meta($the_post_ids, $home_featured_block_custom_field, true);
?>

<a title="<?php echo the_title(); ?>" href="<?php echo the_permalink(); ?>">

<?php if($home_featured_block_attach_type != "attachment") { ?>

<?php if ($values_img != '') : ?>
<img class="nivo-featimg" title="<?php the_title(); ?>" src="<?php echo $values_img; ?>" alt="" />
<?php else : ?>
<img class="nivo-featimg" title="<?php the_title(); ?>" src="<?php echo get_template_directory_uri(); ?>/_inc/images/no-images.jpg" alt="" />
<?php endif; ?>

<?php } else { ?>

<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids, $with_wrap='', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='large',$fetch_w='480', $fetch_h='auto', $alt_class='feat-thumb'); ?>

<?php } ?>

</a>

<?php $slide++; endwhile; ?>

</div></div>

