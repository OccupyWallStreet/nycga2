<div id="slider-wrapper">
<div id="slider" class="nivoSlider">

<?php
global $wpdb, $post;

$home_featured_cat = get_option('tn_blogsmu_featured_cat_id');
$home_featured_cat_count = get_option('tn_blogsmu_featured_cat_id_count');

$cat_query = new WP_Query('cat='. $home_featured_cat . '&' . 'showposts=' . $home_featured_cat_count);
while ($cat_query->have_posts()) : $cat_query->the_post();
$the_post_ids = $post->ID;
$do_not_duplicate = $post->ID;
?>

<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids , $with_wrap='no', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='large', $fetch_w='600', $fetch_h='200',$alt_class='full feat-thumb'); ?> 


<?php endwhile; ?>

</div>
</div>