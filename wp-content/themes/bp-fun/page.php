<?php get_header(); ?>

<?php do_action( 'bp_before_blog_page' ) ?>

<div id="post-entry">

<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><?php the_title(); ?></h1>

<div class="post-content">
<?php $facebook_like_status = get_option('tn_buddyfun_facebook_like_status'); if ($facebook_like_status == 'enable') { ?>
<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:450px; height:30px"></iframe>
<?php } ?>
<?php the_content(); ?>
<?php wp_link_pages(array('before' => __( '<p><strong>Pages:</strong> ', TEMPLATE_DOMAIN ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
<p><?php edit_post_link(__('edit', TEMPLATE_DOMAIN)); ?></p>
</div>

<?php if ( comments_open() ) { ?>
<div class="post-tagged">
<p class="com">
<?php comments_popup_link(__('Leave Comments &rarr;',TEMPLATE_DOMAIN), __('One Comment &rarr;',TEMPLATE_DOMAIN), __('% Comments &rarr;',TEMPLATE_DOMAIN)); ?>
</p>
</div>
<?php } ?>


</div>


<?php endwhile; ?>

<?php if ( comments_open() ) { ?> <?php comments_template('', true); ?><?php } ?>

<?php else: ?>

<?php locate_template ( array('lib/templates/wp-template/result.php'), true ); ?>

<?php endif; ?>

</div>

<?php do_action( 'bp_after_blog_page' ) ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>