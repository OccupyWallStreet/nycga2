<div id="slider-wrapper">
<div id="slider" class="nivoSlider">

<?php
global $wpdb, $post;
$home_featured_post = get_option('tn_blogsmu_featured_post_id');

$home_featured_custom_field = get_option('tn_blogsmu_featured_custom_field');

if($home_featured_post) {
$feat_query_check = "SELECT $wpdb->posts.ID, post_title, post_name, post_status, post_content, post_date, post_author FROM $wpdb->posts WHERE ID IN ( " . $home_featured_post . " ) AND post_status = 'publish' ORDER by ID DESC";
$feat_query = $wpdb->get_results($feat_query_check, OBJECT);
} else {
$feat_query_check = "SELECT $wpdb->posts.ID, post_title, post_name, post_status, post_content, post_date, post_author FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER by ID DESC LIMIT 10";
$feat_query = $wpdb->get_results($feat_query_check, OBJECT);
}

if( $feat_query ) {
foreach( $feat_query as $post ) {
setup_postdata($post);
$the_post_ids = $post->ID;
$the_post_title = htmlspecialchars(stripslashes($post->post_title));
$the_post_title = short_text($the_post_title, 30);
$values_img = get_post_meta($the_post_ids,$home_featured_custom_field,true);
?>


<?php if($home_featured_custom_field != '') {  ?>

<?php if($values_img != "") { ?>
<a href="<?php echo the_permalink(); ?>"><img class="nivo-featimg" title="<h1><?php the_title(); ?></h1>" src="<?php echo $values_img; ?>" alt="" /></a>
<?php } else { ?>

<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids , $with_wrap='no', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='large', $fetch_w='600', $fetch_h='200',$alt_class='full feat-thumb'); ?>

<?php } ?>

<?php } else { ?>

<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids , $with_wrap='no', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='large', $fetch_w='600', $fetch_h='200',$alt_class='full feat-thumb'); ?>

<?php } ?>

<?php } ?>

<?php
}

?>

</div>
</div>