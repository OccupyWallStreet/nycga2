<div id="home-news">
<?php
global $wpdb, $post;
$home_featured_post = get_option('tn_buddysocial_featured_post_id');
$home_featured_cat = get_option('tn_buddysocial_featured_cat_id');
$home_featured_count = get_option('tn_buddysocial_featured_count');
$home_featured_custom_field = get_option('tn_buddysocial_featured_custom_field');

if($home_featured_post != '') {
$feat_query_check = "SELECT $wpdb->posts.ID, post_title, post_name, post_status, post_content, post_date FROM $wpdb->posts WHERE ID IN ( " . $home_featured_post . " ) AND post_status = 'publish' ORDER by ID DESC";
$feat_query = $wpdb->get_results($feat_query_check, OBJECT);

if( $feat_query ) {
foreach( $feat_query as $post ) {
setup_postdata($post);
$the_post_ids = $post->ID;
$the_post_title = htmlspecialchars(stripslashes($post->post_title));
$the_post_title = short_text($the_post_title, 30);
$values_img = get_post_meta($the_post_ids,$home_featured_custom_field,true);
$bcc == 0;
?>

<?php if ($bcc < 1) { ?>

<div class="leftbox">
<span><?php _e('Articles from the blog',TEMPLATE_DOMAIN); ?></span>
<h1><a href="<?php echo the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php echo the_title(); ?></a></h1>
<p class="date"><?php the_time( 'j F, Y' ); ?></p>

<?php if($home_featured_custom_field == '') {  ?>

<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids , $with_wrap='no', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='large',$fetch_w='400', $fetch_h='200',$alt_class=''); ?>

<?php } else { ?>

<?php if ($values_img) { ?>
<div class="feat-img"><a href="<?php echo the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><img src="<?php echo $values_img; ?>" class="full" alt="<?php the_title(); ?>" /></a></div>
<?php } else { ?>
<?php dez_get_attachment($the_post_id = $the_post_ids, $open_class = '<div class="feat-img">',  $close_class = '</div>'); ?>
<?php } ?>

<?php } ?>

<?php echo custom_the_content(120); ?>
</div><!-- end left box -->

<div id="rightbox">
<h3><?php _e('More Articles',TEMPLATE_DOMAIN); ?></h3>

<?php } else { ?>

<p>

<?php if($home_featured_custom_field == '') {  ?>

<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids , $with_wrap='no', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='thumbnail',$fetch_w='80', $fetch_h='80',$alt_class=''); ?>

<?php } else { ?>

<?php if ($values_img) { ?>
<span class="feat-img"><a href="<?php echo the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><img src="<?php echo $values_img; ?>" class="full" alt="<?php the_title(); ?>" /></a></span>
<?php } else { ?>
<?php dez_get_attachment($the_post_id = $the_post_ids, $open_class = '<span class="feat-img">',  $close_class = '</span>'); ?>
<?php } ?>

<?php } ?>


<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
</p>

<?php } ?>

<?php $bcc++; ?>

<?php } ?>

</div><!-- end right box -->

<?php } ?>

<?php } else { //if cat mode ?>

<?php
$cat_query = new WP_Query('cat='. $home_featured_cat . '&' . 'showposts=' . $home_featured_count);
while ($cat_query->have_posts()) : $cat_query->the_post();
$the_post_ids = $post->ID;
$do_not_duplicate = $post->ID;
$values_img = get_post_meta($the_post_ids, $home_featured_custom_field, true);
$bcc == 0;
?>

<?php if ($bcc < 1) { ?>

<div class="leftbox">
<span><?php _e('Articles from the blog',TEMPLATE_DOMAIN); ?></span>
<h1><a href="<?php echo the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php echo the_title(); ?></a></h1>
<p class="date"><?php the_time( 'j F, Y' ); ?></p>

<?php if($home_featured_custom_field == '') {  ?>

<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids , $with_wrap='no', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='large',$fetch_w='350', $fetch_h='200',$alt_class=''); ?>

<?php } else { ?>

<?php if ($values_img) { ?>
<div class="feat-img"><a href="<?php echo the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><img src="<?php echo $values_img; ?>" class="full" alt="<?php the_title(); ?>" /></a></div>
<?php } else { ?>
<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids , $with_wrap='no', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='large',$fetch_w='350', $fetch_h='200',$alt_class=''); ?>

<?php } ?>

<?php } ?>

<?php echo custom_the_content(120); ?>
</div><!-- end left box -->

<div id="rightbox">
<h3><?php _e('More Articles',TEMPLATE_DOMAIN); ?></h3>

<?php } else { ?>

<p>
<?php if($home_featured_custom_field == '') {  ?>

<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids , $with_wrap='no', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='thumbnail',$fetch_w='80', $fetch_h='80',$alt_class='alignleft'); ?>

<?php } else { ?>

<?php if ($values_img) { ?>
<span class="feat-img"><a href="<?php echo the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><img src="<?php echo $values_img; ?>" class="full" alt="<?php the_title(); ?>" /></a></span>
<?php } else { ?>
<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids , $with_wrap='no', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='thumbnail',$fetch_w='80', $fetch_h='80',$alt_class=''); ?>

<?php } ?>
<?php } ?>
<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
</p>

<?php } ?>

<?php $bcc++; endwhile; ?>

</div>

<?php } ?>

</div>