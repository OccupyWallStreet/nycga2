<?php get_header(); ?>

<?php do_action( 'bp_before_blog_single_post' ) ?>

<div id="post-entry">

<?php if (have_posts()) : ?>

<?php locate_template ( array('lib/templates/wp-template/headline.php'), true ); ?>

<?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class('post-single'); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><?php the_title(); ?></h1>

<div class="post-author"><?php the_time('jS F Y') ?> <?php if( $bp_existed == 'true' ) { ?><?php printf( __( 'by %s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?>
<?php _e('by',TEMPLATE_DOMAIN); ?> <?php the_author_posts_link(); ?><?php } ?> <?php _e('under',TEMPLATE_DOMAIN); ?> <?php the_category(', ') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php edit_post_link(__('edit',TEMPLATE_DOMAIN)); ?></div>

<div class="post-content">
<?php $facebook_like_status = get_option('tn_buddyfun_facebook_like_status'); if ($facebook_like_status == 'enable') { ?>
<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:450px; height:30px"></iframe>
<?php } ?>
<?php the_content(); ?>
<?php wp_link_pages(array('before' => __( '<p><strong>Pages:</strong> ', TEMPLATE_DOMAIN ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
</div>

<div class="post-tagged">
<p class="tags">
<?php if(function_exists("the_tags")) : ?>
<?php the_tags(__('tags:&nbsp;', TEMPLATE_DOMAIN), ', ', ''); ?>
<?php endif; ?>
</p>
</div>
</div>


<?php endwhile; ?>

<?php if ( comments_open() ) { ?><?php comments_template('', true); ?><?php } ?>

<?php locate_template ( array('lib/templates/wp-template/paginate.php'), true ); ?>

<?php else: ?>

<?php locate_template ( array('lib/templates/wp-template/result.php'), true ); ?>

<?php endif; ?>

</div>

<?php do_action( 'bp_after_blog_single_post' ) ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>