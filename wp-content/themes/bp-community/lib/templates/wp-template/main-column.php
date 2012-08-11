<div id="main-column">

<?php
global $bp_existed;
$home_featured_block_style = get_option('tn_buddycom_home_featured_block_style');
$home_featured_block_cat = get_option('tn_buddycom_home_featured_block_cat');
$home_featured_block_count = get_option('tn_buddycom_home_featured_block_count');
$home_featured_block_custom_field = get_option('tn_buddycom_home_featured_block_custom_field');
$home_featured_block_attach_type = get_option('tn_buddycom_home_featured_block_attach_type');
$get_post_counter = get_the_current_blog_post_count();

if($home_featured_block_cat == 'Choose a category') { $home_featured_block_cat = ''; }
if($home_featured_block_count != '' && $home_featured_block_count != 'Select a number') {
$home_featured_block_text_counter = $home_featured_block_count * 25;
} else {
$home_featured_block_text_counter = 120;
}

if($home_featured_block_count == 'Select a number' || $home_featured_block_count == '') {
$home_featured_block_count = '5';
}

$featured_count = dev_multi_category_count($catslugs = $home_featured_block_cat);

if($featured_count == '1') { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
#container #main-news {
    border-right: 0px none;
    padding: 0px;
    width: 100%;
}
<?php print "</style>"; ?>
<?php }


$bc == 0;
$cat_query = new WP_Query('cat='. $home_featured_block_cat . '&' . 'showposts=' . $home_featured_block_count);
while ($cat_query->have_posts()) : $cat_query->the_post();
$the_post_ids = $post->ID;

$do_not_duplicate = $post->ID;

$values_img = get_post_custom_values($home_featured_block_custom_field);
$values_vid = get_post_custom_values('video');

?>

<?php if ($bc < 1) { ?>
<div id="main-news">
<p class="normal-class"><?php _e('From The Blog', TEMPLATE_DOMAIN); ?></p>
<h1 style="margin-bottom: 10px;"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>


<?php
if (is_array($values_vid)) : ?>
<?php echo stripcslashes($values_vid); ?>
<?php else : ?>
<?php if($home_featured_block_attach_type != "attachment") { ?>
<?php if (is_array($values_img)) : ?>
<img title="<?php the_title(); ?>" src="<?php "$values_img[0]"; ?>" />
<?php else : ?>
<img title="<?php the_title(); ?>" src="<?php echo get_template_directory_uri(); ?>/_inc/images/no-images.jpg" />
<?php endif; ?>

<?php } else { ?>
<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids, $with_wrap='', $wrap_w='', $wrap_h='', $title=get_the_title(),$fetch_size='medium',$fetch_w='400', $fetch_h='auto',$alt_class=''); ?>
<?php } ?>

<?php endif; ?>

<div class="thenews"><?php echo custom_the_content($home_featured_block_text_counter); ?></div>

</div>

<?php if($featured_count != '1' && $get_post_counter > '2') { ?>
<div id="other-news">
<h2><?php _e('More News &raquo;', TEMPLATE_DOMAIN); ?></h2>
<ul>
<?php } ?>


<?php } elseif($bc > 1 || $featured_count != '1') { ?>


<?php if($home_featured_block_attach_type != "attachment") { ?>
<li>
<?php if(is_array($values_img)) : ?>
<div class="cthumb" style="width: 100%; height: 90px; overflow: hidden;">
<img style="width: 100%; height: auto;" title="<?php the_title(); ?>" src="<?php "$values_img[0]"; ?>" />
</div>
<?php else: ?>
<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids, $with_wrap='', $wrap_w='', $wrap_h='', $title=get_the_title(),$fetch_size='thumbnail',$fetch_w='64', $fetch_h='auto',$alt_class='feat-thumb alignleft'); ?>
<?php endif; ?>
<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a><br />
<small><?php the_time('jS F Y') ?> <?php if( $bp_existed == 'true' ) { ?><?php printf( __( 'by %s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?><?php _e('by',TEMPLATE_DOMAIN); ?> <?php the_author_posts_link(); ?><?php } ?></small>
</li>
<?php } else { ?>
<li>
<?php wp_custom_post_thumbnail($the_post_id=$the_post_ids, $with_wrap='yes', $wrap_w='190px', $wrap_h='90px', $title=get_the_title(),$fetch_size='thumbnail',$fetch_w='190', $fetch_h='auto',$alt_class='alignleft feat-thumb'); ?>
<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a><br />
<small><?php the_time('jS F Y') ?> <?php if( $bp_existed == 'true' ) { ?><?php printf( __( 'by %s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?><?php _e('by',TEMPLATE_DOMAIN); ?> <?php the_author_posts_link(); ?><?php } ?></small>
</li>
<?php } ?>

<?php } ?>

<?php $bc++; ?>

<?php endwhile; ?>

<?php if($featured_count != '1') { ?></ul></div><?php } ?>



</div>