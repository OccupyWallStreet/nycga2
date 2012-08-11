<?php
/*
Template Name: Full Width
*/
?>

<?php get_header(); ?>

<div class="full-width" id="post-entry">

<?php do_action( 'bp_before_blog_home' ) ?>

<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>

<div class="post-content">
<?php $facebook_like_status = get_option('tn_buddycorp_facebook_like_status'); if ($facebook_like_status == 'enable') { ?>
<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:900px; height:30px;"></iframe>
<?php } ?>
<?php the_content(); ?>
<?php wp_link_pages('before=<p>&after=</p>'); ?>
<?php edit_post_link(__('edit', TEMPLATE_DOMAIN), '<p>', '</p>'); ?>

</div>


<div class="post-tagged">
<?php if ( comments_open() ) { ?>
<p class="com">
<?php comments_popup_link(__('Leave Comments &rarr;', TEMPLATE_DOMAIN), __('One Comment &rarr;', TEMPLATE_DOMAIN), __('% Comments &rarr;', TEMPLATE_DOMAIN)); ?>
</p>
<?php } ?>
</div>

</div>


<?php endwhile; ?>

<?php if ( comments_open() ) { ?> <?php comments_template('',true); ?><?php } ?>

<?php do_action( 'bp_after_blog_home' ) ?>

<?php endif; ?>

</div>

<?php get_footer(); ?>