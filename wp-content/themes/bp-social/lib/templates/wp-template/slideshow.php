<div id="Gallerybox">
<div id="myGallery">

<?php
global $wpdb, $post;
$home_featured_post = get_option('tn_buddysocial_featured_post_id');
$home_featured_cat = get_option('tn_buddysocial_featured_cat_id');
$home_featured_count = get_option('tn_buddysocial_featured_count');
$home_featured_custom_field = get_option('tn_buddysocial_featured_custom_field');

if($home_featured_post != '') {
$feat_query_check = "SELECT " . $wpdb->prefix . "posts.ID, post_title, post_name, post_status, post_content, post_date, post_author, post_excerpt FROM ". $wpdb->prefix . "posts WHERE ID IN ( " . $home_featured_post . " ) AND post_status = 'publish' ORDER by ID DESC";
$feat_query = $wpdb->get_results($feat_query_check, OBJECT);

if( $feat_query ) {
foreach( $feat_query as $post ) {
setup_postdata($post);
$the_post_ids = $post->ID;
$the_post_title = htmlspecialchars(stripslashes($post->post_title));
$the_post_title = short_text($the_post_title, 30);
$values_img = get_post_meta($the_post_ids,$home_featured_custom_field,true);
?>


<div class="imageElement post-<?php the_ID(); ?>">
<h3><a href="<?php echo the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php echo the_title(); ?></a></h3>

<?php if($home_featured_custom_field == '') {  ?>

<a href="#"><?php wp_custom_post_thumbnail($the_post_id=$the_post_ids , $with_wrap='', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='large', $fetch_w='630', $fetch_h='auto',$alt_class='full feat-thumb'); ?></a>

<?php } else { ?>

<?php if ($values_img) { ?>
<a href="#"><img src="<?php echo $values_img; ?>" class="full" /></a>
<?php } else { ?>
<?php dez_get_attachment($the_post_id = $the_post_ids, $open_class = '',  $close_class = ''); ?>
<?php } ?>

<?php } ?>

<p><?php the_excerpt_feature($excerpt_length = "30"); ?>...</p>
<a href="<?php the_permalink(); ?>" title="open image" class="open"></a>
</div>

<?php } ?>
<?php } ?>

<?php } else { // if in cat mode ?>

<?php
$cat_query = new WP_Query('cat='. $home_featured_cat . '&' . 'showposts=' . $home_featured_count);
while ($cat_query->have_posts()) : $cat_query->the_post();
$the_post_ids = $post->ID;
$do_not_duplicate = $post->ID;
$values_img = get_post_meta($the_post_ids, $home_featured_custom_field, true);
?>

<div class="imageElement post-<?php the_ID(); ?>">
<h3><a href="<?php echo the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php echo the_title(); ?></a></h3>

<?php if($home_featured_custom_field == '') {  ?>

<a href="#"><?php wp_custom_post_thumbnail($the_post_id=$the_post_ids , $with_wrap='', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='large', $fetch_w='630', $fetch_h='auto',$alt_class='full feat-thumb'); ?></a>

<?php } else { ?>

<?php if ($values_img) { ?>
<a href="#"><img src="<?php echo $values_img; ?>" class="full" /></a>
<?php } else { ?>
<?php dez_get_attachment($the_post_id = $the_post_ids, $open_class = '',  $close_class = ''); ?>
<?php } ?>

<?php } ?>

<p><?php the_excerpt_feature($excerpt_length = "30"); ?>...</p>
<a href="<?php the_permalink(); ?>" title="open image" class="open"></a>
</div>

<?php endwhile; ?>

<?php } ?>

</div> <!-- end mygallery -->
</div> <!-- end gallerybox -->