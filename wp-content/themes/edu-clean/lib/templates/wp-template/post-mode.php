<?php include (TEMPLATEPATH . '/options.php'); ?>

<?php
$post_mode_cat = get_option('tn_edus_feat_postmode');
$category_id = get_cat_ID($post_mode_cat);
//insert your category name
$my_query = new WP_Query('cat='. $category_id . '&' . 'showposts=' . 6);
while ($my_query->have_posts()) : $my_query->the_post();
$the_post_ids = get_the_ID();
$the_post_title = get_the_title();
$do_not_duplicate = $post->ID;

?>


<div id="post-id-<?php echo $the_post_ids; ?>" class="service-block">


<div style="width: 198px; height: 100px; overflow: hidden;" class="featimg">
<div class="featbox">
<a href="<?php the_permalink(); ?>">
<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids, $with_wrap='no', $wrap_w='0', $wrap_h='0', $title=get_the_title(), $fetch_size='medium',$fetch_w='198', $fetch_h='100',$alt_class='alignleft')?>
</a></div>
</div>

<h3><?php echo short_text($text=$the_post_title, $wordcount=35); ?></h3>

<p><?php the_excerpt_feature( $excerpt_length=15 )?></a></p>
</div>

<?php endwhile;?>