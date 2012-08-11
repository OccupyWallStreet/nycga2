<?php include ( TEMPLATEPATH . '/options-var.php' ); ?>

<div id="services-content">

<?php
$post_mode_cat = get_option('tn_blogsmu_home_service_postmode_cat');
$post_mode_cat_count = get_option('tn_blogsmu_home_service_postmode_cat_count');

$category_id = get_cat_ID($post_mode_cat);

//insert your category name
$my_query = new WP_Query('cat='. $category_id . '&' . 'showposts=' . $post_mode_cat_count);
while ($my_query->have_posts()) : $my_query->the_post();
$the_post_ids = get_the_ID();
$the_post_title = get_the_title();
$do_not_duplicate = $post->ID;
?>


<div class="sbox">
<div class="simg">
<div class="img-services">
<a href="<?php the_permalink(); ?>"><?php wp_custom_post_thumbnail($the_post_id=$the_post_ids, $with_wrap='', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='medium',$fetch_w='260', $fetch_h='auto',$alt_class='feat-thumb'); ?></a>
</div>
</div>
<h3><?php echo the_title(); ?></h3>
<p>
<?php the_excerpt_feature( $excerpt_length=20 )?>...
<span class="learn-more"><a href="<?php the_permalink(); ?>"><?php _e("Find out more", TEMPLATE_DOMAIN); ?></a></span>
</p>
</div>


<?php endwhile;?>



</div>