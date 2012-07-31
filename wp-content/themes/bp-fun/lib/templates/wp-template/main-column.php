<div id="main-entry">
<div class="main-post">

<?php
$home_featured_block_style = get_option('tn_buddyfun_home_featured_block_style');
$home_featured_block_cat = get_option('tn_buddyfun_home_featured_block_cat');
$home_featured_block_count = get_option('tn_buddyfun_home_featured_block_count');
$home_featured_block_custom_field = get_option('tn_buddyfun_home_featured_block_custom_field');
$home_featured_block_attach_type = get_option('tn_buddyfun_home_featured_block_attach_type');
$get_post_counter = get_the_current_blog_post_count();

if($home_featured_block_count != '' && $home_featured_block_count != '1' && $home_featured_block_count !='Select a number' ) {
$home_featured_block_text_counter = $home_featured_block_count * 25;
} elseif ($home_featured_block_count == '1' ) {
$home_featured_block_text_counter = 250;
} else {
$home_featured_block_count = 5;
$home_featured_block_text_counter = 150;
}

$featured_count = dev_multi_category_count($catslugs = $home_featured_block_cat);

if($featured_count == '1' || $home_featured_block_count == '1') { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
#container .main-post {
border-right: 0px none;
padding: 0px;
width: 100%;
}
<?php print "</style>"; ?>
<?php }


$category_id = get_cat_ID($home_featured_block_cat);

$cat_query = new WP_Query('cat='. $home_featured_block_cat . '&' . 'showposts=' . $home_featured_block_count);
while ($cat_query->have_posts()) : $cat_query->the_post();
$the_post_ids = $post->ID;
$do_not_duplicate = $post->ID;
$values_img = get_post_meta($the_post_ids,$home_featured_block_custom_field,true);
$bcc == 0; ?>

<?php if ($bcc < 1) { ?>

<?php $video_code = get_post_meta("video-code", $the_post_ids, true);
if ($video_code != '') { // if there is no a video custom meta existed ?>
<?php echo $video_code; ?>

<?php } else { ?>

<?php if($home_featured_block_attach_type == "custom-field") { ?>
<?php if ($home_featured_block_custom_field != '') : ?>
<?php if ($values_img != '') : ?>
<img class="custom_post_key_img" title="<?php the_title(); ?>" src="<?php echo $values_img; ?>" />
<?php else: ?>

<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids, $with_wrap='no', $wrap_w='100%', $wrap_h='auto', $title=get_the_title(), $fetch_size='large', $fetch_w='650', $fetch_h='auto',$alt_class='alignleft'); ?>

<?php endif; ?>
<?php endif; ?>

<?php } else { ?>

<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids, $with_wrap='no', $wrap_w='100%', $wrap_h='auto', $title=get_the_title(), $fetch_size='large', $fetch_w='650', $fetch_h='auto',$alt_class='alignleft'); ?>

<?php } ?>

<?php } // end check ?>


<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>

<div class="post-author"><?php if( $bp_existed == 'true' ) { ?><?php printf( __( 'by %s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?><?php _e('by',TEMPLATE_DOMAIN); ?> <?php the_author_posts_link(); ?><?php } ?> <?php _e('on', TEMPLATE_DOMAIN ); ?> <?php the_time('jS F Y') ?> </div>

<div class="post-content">
<?php echo custom_the_content($home_featured_block_text_counter); ?>
</div>

</div>

<?php if($featured_count != '1' && $home_featured_block_count != '1' && $get_post_counter > '2') { ?>
<div class="alt-post">
<h3><?php _e( 'More articles &raquo;', TEMPLATE_DOMAIN ) ?></h3>
<ul>
<?php } ?>

<?php } elseif($bcc > 1 || $featured_count != '1' || $home_featured_block_count != '1') { ?>

<li>


<?php if($home_featured_block_attach_type == "custom-field") { ?>
<?php if ($home_featured_block_custom_field != '') : ?>
<?php if ($values_img != '') : ?>
<div style="position: relative; float: left; text-align: center; width: 190px; height: 70px; overflow: hidden;" class="custom_post_img">
<img class="position-fix" style="width: 100%; height: auto;" title="<?php the_title(); ?>" src="<?php echo $values_img; ?>" />
</div>
<?php else: ?>
<?php custom_get_post_img ($the_post_id=$the_post_ids, $size='medium', $attributes=$the_post_title, $height='70'); ?>
<?php endif; ?>
<?php endif; ?>

<?php } else { ?>
<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids, $with_wrap='yes', $wrap_w='100%', $wrap_h='70px', $title=get_the_title(), $fetch_size='medium', $fetch_w='200', $fetch_h='auto',$alt_class='alignleft feat-thumb'); ?>
<?php } ?>
<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
<br /><small><?php _e('in', TEMPLATE_DOMAIN ); ?> <?php the_category(', '); ?></small>
</li>

<?php } ?>

<?php $bcc++; ?>

<?php endwhile; ?>

<?php if($featured_count != '1' && $home_featured_block_count != '1') { ?></ul></div><?php } ?>

</div>