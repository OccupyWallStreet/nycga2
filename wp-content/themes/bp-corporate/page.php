<?php get_header(); ?>

<?php
$facebook_like_status = get_option('tn_buddycorp_facebook_like_status');
$social_page_status = get_option('tn_buddycorp_social_page_status');
?>

<div id="post-entry" class="single-column">

<?php do_action( 'bp_before_blog_home' ) ?>

<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><?php the_title(); ?></h1>

<?php if ($social_page_status == 'no') { include( TEMPLATEPATH . '/lib/templates/wp-template/social.php'); } ?>

<div class="post-content">
<?php the_content(__('...click here to read more', TEMPLATE_DOMAIN)); ?>
<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
<?php edit_post_link(__('edit', TEMPLATE_DOMAIN), '<p>', '</p>'); ?>

<?php
if ($social_page_status == 'no') {
if ($facebook_like_status == 'enable') { ?>
<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:450px; height:30px"></iframe>
<?php } } ?>

</div>


<?php if ( comments_open() ) { ?>
<div class="post-tagged">
<p class="com">
<?php comments_popup_link(__('Leave Comments &rarr;', TEMPLATE_DOMAIN), __('One Comment &rarr;', TEMPLATE_DOMAIN), __('% Comments &rarr;', TEMPLATE_DOMAIN)); ?>
</p>
</div>
<?php } ?>


</div>

<?php endwhile; ?>

<?php do_action( 'bp_after_blog_home' ) ?>


<?php if ( comments_open() ) { ?><?php comments_template('',true); ?><?php } ?>


<?php else: ?>

<?php locate_template( array( 'lib/templates/wp-template/result.php'), true ); ?>

<?php endif; ?>

</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>